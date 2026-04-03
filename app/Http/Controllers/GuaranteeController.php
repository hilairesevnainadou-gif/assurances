<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\FundingRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\View\View;

class GuaranteeController extends Controller
{
    // ═══════════════════════════════════════════════════════════════════════
    // Actions publiques
    // ═══════════════════════════════════════════════════════════════════════

    /**
     * Afficher la page de paiement des frais de dossier.
     */
    public function show(string $token): View|RedirectResponse
    {
        try {
            $data = $this->validateToken($token);

            if (! $data) {
                return redirect()->route('guarantee.expired', $token);
            }

            $fundingRequest = $this->getFundingRequest($data['frid']);

            if (! $fundingRequest) {
                abort(404, 'Demande de financement introuvable.');
            }

            // Vérifier si déjà traité
            if ($fundingRequest->status !== 'approved') {
                return view('guarantee.already-processed', compact('fundingRequest'));
            }

            // Vérifier si le paiement a déjà été effectué
            if ($fundingRequest->final_fee_paid) {
                return redirect()->route('guarantee.questionnaire', $token)
                    ->with('info', 'Paiement déjà effectué. Veuillez compléter le questionnaire.');
            }

            $finalFee = $fundingRequest->registration_final_fee ?? 0;
            $amountApproved = $fundingRequest->amount_approved ?? 0;

            // Vérifier si les frais sont à zéro
            if ($finalFee == 0) {
                return redirect()->route('guarantee.questionnaire', $token)
                    ->with('info', 'Aucun frais à payer. Veuillez compléter le questionnaire.');
            }

            // Utiliser la bonne vue (show.blade.php ou payment.blade.php)
            // Si votre vue s'appelle show.blade.php, utilisez 'guarantee.show'
            return view('guarantee.show', compact(
                'fundingRequest',
                'token',
                'finalFee',
                'amountApproved'
            ));

        } catch (\Exception $e) {
            Log::error('Erreur affichage page paiement', [
                'token' => $token,
                'error' => $e->getMessage()
            ]);
            return redirect()->route('guarantee.expired', $token);
        }
    }

/**
 * Traiter le paiement KKiaPay.
 */
public function pay(Request $request, string $token): RedirectResponse
{
    try {
        $data = $this->validateToken($token);

        if (! $data) {
            return redirect()->route('guarantee.expired', $token);
        }

        $request->validate([
            'kkiapay_transaction_id' => ['required', 'string', 'max:255'],
        ]);

        $fundingRequest = $this->getFundingRequest($data['frid']);

        if (! $fundingRequest) {
            abort(404, 'Demande introuvable.');
        }

        if ($fundingRequest->status !== 'approved') {
            return redirect()->route('guarantee.already-processed', $token);
        }

        if ($fundingRequest->final_fee_paid) {
            return redirect()->route('guarantee.questionnaire', $token)
                ->with('info', 'Paiement déjà effectué.');
        }

        DB::beginTransaction();

        try {
            $finalFee = $fundingRequest->registration_final_fee ?? 0;

            // 1. Mettre à jour la demande de financement
            DB::table('funding_requests')
                ->where('id', $data['frid'])
                ->where('status', 'approved')
                ->update([
                    'status' => 'pending_disbursement',
                    'final_fee_paid' => true,
                    'final_fee_paid_at' => now(),
                    'updated_at' => now(),
                ]);

            // 2. Récupérer ou créer un wallet pour l'utilisateur
            $wallet = DB::table('wallets')
                ->where('user_id', $data['uid'])
                ->first();

            $walletId = null;

            if ($wallet) {
                $walletId = $wallet->id;
            } else {
                // Créer un wallet par défaut
                $walletId = DB::table('wallets')->insertGetId([
                    'user_id' => $data['uid'],
                    'type' => 'user',
                    'wallet_number' => 'BHDM-WALLET-' . strtoupper(\Illuminate\Support\Str::random(8)),
                    'balance' => 0,
                    'currency' => 'XOF',
                    'status' => 'active',
                    'activated_at' => now(),
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }

            // 3. Enregistrer la transaction de paiement des frais
            // Utiliser une valeur valide pour 'type' (credit, debit, etc.)
            DB::table('transactions')->insert([
                'wallet_id' => $walletId,
                'funding_request_id' => $data['frid'],
                'transaction_id' => 'TXN-FEE-' . $request->kkiapay_transaction_id,
                'type' => 'debit', // Changer de 'fee_payment' à 'credit' ou 'debit'
                'amount' => $finalFee,
                'fee' => 0,
                'total_amount' => $finalFee,
                'payment_method' => 'kkiapay',
                'status' => 'completed',
                'completed_at' => now(),
                'reference' => 'FEE-' . $fundingRequest->request_number,
                'description' => "Frais de dossier — demande #{$fundingRequest->request_number}",
                'metadata' => json_encode([
                    'type' => 'final_fee_payment',
                    'funding_request_id' => $data['frid'],
                    'kkiapay_transaction_id' => $request->kkiapay_transaction_id,
                    'paid_at' => now()->toIso8601String(),
                ]),
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            // 4. Marquer le token comme utilisé
            DB::table('guarantee_tokens')
                ->where('token', $token)
                ->whereNull('used_at')
                ->update(['used_at' => now()]);

            // 5. Notifier le client
            DB::table('notifications')->insert([
                'user_id' => $data['uid'],
                'type' => 'final_fee_paid',
                'title' => 'Frais de dossier réglés avec succès',
                'message' => "Vos frais de dossier pour la demande #{$fundingRequest->request_number} ont été reçus. Veuillez maintenant compléter le questionnaire pour finaliser votre dossier.",
                'data' => json_encode([
                    'funding_request_id' => $data['frid'],
                    'kkiapay_transaction_id' => $request->kkiapay_transaction_id,
                ]),
                'read_at' => null,
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            DB::commit();

            return redirect()->route('guarantee.questionnaire', $token)
                ->with('success', 'Paiement effectué avec succès ! Veuillez maintenant compléter le questionnaire.');

        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }

    } catch (\Exception $e) {
        Log::error('Échec paiement frais', [
            'funding_request_id' => $data['frid'] ?? null,
            'kkiapay_transaction_id' => $request->kkiapay_transaction_id ?? null,
            'error' => $e->getMessage(),
            'trace' => $e->getTraceAsString()
        ]);

        return back()->with('error', 'Une erreur est survenue lors du paiement. Veuillez réessayer ou contacter le support.');
    }
}

    /**
     * Afficher le questionnaire après paiement
     */
    public function questionnaire(string $token): View|RedirectResponse
    {
        try {
            $data = $this->validateToken($token, checkUsed: false);

            if (! $data) {
                return redirect()->route('guarantee.expired', $token);
            }

            $fundingRequest = $this->getFundingRequest($data['frid']);

            if (! $fundingRequest) {
                abort(404, 'Demande introuvable.');
            }

            // Vérifier si le paiement a bien été effectué
            if (!$fundingRequest->final_fee_paid && $fundingRequest->status !== 'pending_disbursement') {
                return redirect()->route('guarantee.show', $token)
                    ->with('error', 'Veuillez d\'abord procéder au paiement des frais.');
            }

            // Vérifier si le questionnaire a déjà été rempli
            $existingQuestionnaire = DB::table('funding_questionnaires')
                ->where('funding_request_id', $fundingRequest->id)
                ->first();

            if ($existingQuestionnaire) {
                return redirect()->route('guarantee.success', $token)
                    ->with('info', 'Questionnaire déjà complété. Votre dossier est en cours de traitement.');
            }

            $amountApproved = $fundingRequest->amount_approved ?? 0;

            return view('guarantee.questionnaire', compact(
                'fundingRequest',
                'token',
                'amountApproved'
            ));

        } catch (\Exception $e) {
            Log::error('Erreur affichage questionnaire', [
                'token' => $token,
                'error' => $e->getMessage()
            ]);
            return redirect()->route('guarantee.expired', $token);
        }
    }

    /**
 * Traiter le questionnaire
 */
public function processQuestionnaire(Request $request, string $token): RedirectResponse
{
    try {
        $data = $this->validateToken($token, checkUsed: false);

        if (! $data) {
            return redirect()->route('guarantee.expired', $token);
        }

        $validated = $request->validate([
            'objective' => 'required|string',
            'other_objective' => 'nullable|string',
            'usage_details' => 'required|string|min:20',
            'repayment_plan' => 'required|string|min:20',
            'benefits' => 'required|array',
            'experience' => 'required|string',
            'previous_funding' => 'required|in:yes,no',
            'previous_funding_details' => 'nullable|string',
            'risk_management' => 'required|string|min:20',
            'declaration' => 'required|accepted',
            'consent' => 'required|accepted',
        ]);

        $fundingRequest = $this->getFundingRequest($data['frid']);

        if (! $fundingRequest) {
            abort(404, 'Demande introuvable.');
        }

        // Vérifier si le questionnaire existe déjà
        $existing = DB::table('funding_questionnaires')
            ->where('funding_request_id', $fundingRequest->id)
            ->first();

        if ($existing) {
            return redirect()->route('guarantee.success', $token)
                ->with('info', 'Questionnaire déjà soumis.');
        }

        DB::beginTransaction();

        try {
            // 1. Enregistrer les réponses du questionnaire
            DB::table('funding_questionnaires')->insert([
                'funding_request_id' => $data['frid'],
                'objective' => $validated['objective'],
                'other_objective' => $validated['other_objective'] ?? null,
                'usage_details' => $validated['usage_details'],
                'repayment_plan' => $validated['repayment_plan'],
                'benefits' => json_encode($validated['benefits']),
                'experience' => $validated['experience'],
                'has_previous_funding' => $validated['previous_funding'] === 'yes',
                'previous_funding_details' => $validated['previous_funding_details'] ?? null,
                'risk_management' => $validated['risk_management'],
                'declaration_accepted' => true,
                'submitted_at' => now(),
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            // 2. Mettre à jour la demande avec les informations supplémentaires
            DB::table('funding_requests')
                ->where('id', $data['frid'])
                ->update([
                    'questionnaire_submitted_at' => now(),
                    'questionnaire_data' => json_encode([
                        'objective' => $validated['objective'],
                        'usage_details' => $validated['usage_details'],
                        'repayment_plan' => $validated['repayment_plan'],
                        'benefits' => $validated['benefits'],
                        'experience' => $validated['experience'],
                    ]),
                    'updated_at' => now(),
                ]);

            // 3. Notifier les administrateurs (récupérer les admins)
            $admins = DB::table('users')
                ->where('is_admin', true)
                ->orWhere('is_moderator', true)
                ->get();

            foreach ($admins as $admin) {
                DB::table('notifications')->insert([
                    'user_id' => $admin->id,
                    'type' => 'questionnaire_submitted',
                    'title' => 'Nouveau questionnaire rempli',
                    'message' => "Le client {$fundingRequest->first_name} {$fundingRequest->last_name} a rempli le questionnaire pour la demande #{$fundingRequest->request_number}",
                    'data' => json_encode([
                        'funding_request_id' => $data['frid'],
                        'submitted_at' => now()->toIso8601String(),
                    ]),
                    'read_at' => null,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }

            // 4. Notifier le client
            DB::table('notifications')->insert([
                'user_id' => $data['uid'],
                'type' => 'questionnaire_completed',
                'title' => 'Questionnaire reçu avec succès',
                'message' => "Votre questionnaire a été reçu. Votre versement sera traité dans les 24 à 48 heures ouvrables.",
                'data' => json_encode([
                    'funding_request_id' => $data['frid'],
                ]),
                'read_at' => null,
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            DB::commit();

            return redirect()->route('guarantee.success', $token)
                ->with('success', 'Questionnaire soumis avec succès ! Votre versement sera traité sous 24-48h.');

        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }

    } catch (\Exception $e) {
        Log::error('Échec soumission questionnaire', [
            'funding_request_id' => $data['frid'] ?? null,
            'error' => $e->getMessage(),
            'trace' => $e->getTraceAsString()
        ]);

        return back()->with('error', 'Une erreur est survenue. Veuillez réessayer ou contacter le support.');
    }
}

    /**
 * Page de succès après questionnaire
 */
public function success(string $token): View|RedirectResponse
{
    try {
        $data = $this->validateToken($token, checkUsed: false);

        if (! $data) {
            return redirect()->route('guarantee.expired', $token);
        }

        $fundingRequest = $this->getFundingRequest($data['frid']);

        if (! $fundingRequest) {
            abort(404, 'Demande introuvable.');
        }

        // Passer le token à la vue
        return view('guarantee.success', compact('fundingRequest', 'token'));

    } catch (\Exception $e) {
        Log::error('Erreur page succès', [
            'token' => $token,
            'error' => $e->getMessage()
        ]);
        return redirect()->route('guarantee.expired', $token);
    }
}


/**
 * Page déjà traité
 */
public function alreadyProcessed(string $token): View|RedirectResponse
{
    try {
        $data = $this->validateToken($token, checkUsed: false);

        if (! $data) {
            return redirect()->route('guarantee.expired', $token);
        }

        $fundingRequest = $this->getFundingRequest($data['frid']);

        if (! $fundingRequest) {
            abort(404, 'Demande introuvable.');
        }

        $statusLabel = match($fundingRequest->status) {
            'pending_disbursement' => 'Versement en attente',
            'funded' => 'Financé',
            'approved' => 'Approuvé',
            'rejected' => 'Rejeté',
            default => $fundingRequest->status,
        };

        // Passer le token à la vue
        return view('guarantee.already-processed', compact('fundingRequest', 'statusLabel', 'token'));

    } catch (\Exception $e) {
        Log::error('Erreur page already-processed', [
            'token' => $token,
            'error' => $e->getMessage()
        ]);
        return redirect()->route('guarantee.expired', $token);
    }
}

    /**
     * Page lien expiré ou invalide
     */
    public function expired(string $token): View
    {
        return view('guarantee.expired');
    }



    // ═══════════════════════════════════════════════════════════════════════
    // Méthodes privées
    // ═══════════════════════════════════════════════════════════════════════

    /**
     * Valide le token signé HMAC-SHA256.
     */
    private function validateToken(string $token, bool $checkUsed = true): ?array
    {
        $parts = explode('.', $token, 2);
        if (count($parts) !== 2) {
            Log::warning('Token invalide - format incorrect', ['token' => substr($token, 0, 50)]);
            return null;
        }

        [$payload, $signature] = $parts;

        $secret = config('services.main_project.secret');
        if (!$secret) {
            Log::error('Clé secrète non configurée');
            return null;
        }

        $expectedSig = hash_hmac('sha256', $payload, $secret);
        if (! hash_equals($expectedSig, $signature)) {
            Log::warning('Token invalide - signature incorrecte');
            return null;
        }

        $decoded = json_decode(base64_decode($payload), true);
        if (! is_array($decoded) || empty($decoded['frid']) || empty($decoded['uid']) || empty($decoded['exp'])) {
            Log::warning('Token invalide - payload corrompu');
            return null;
        }

        if ($decoded['exp'] < now()->timestamp) {
            Log::info('Token expiré', [
                'frid' => $decoded['frid'],
                'expired_at' => date('Y-m-d H:i:s', $decoded['exp'])
            ]);
            return null;
        }

        if ($checkUsed) {
            try {
                $record = DB::table('guarantee_tokens')
                    ->where('token', $token)
                    ->where('funding_request_id', $decoded['frid'])
                    ->where('expires_at', '>', now())
                    ->whereNull('used_at')
                    ->first();

                if (! $record) {
                    Log::info('Token non trouvé en base ou déjà utilisé', [
                        'frid' => $decoded['frid']
                    ]);
                    return null;
                }
            } catch (\Exception $e) {
                Log::error('Erreur vérification token base', [
                    'error' => $e->getMessage()
                ]);
                return null;
            }
        }

        return $decoded;
    }

    /**
     * Récupère la demande de financement avec les infos nécessaires
     */
    private function getFundingRequest(int $id): ?object
    {
        try {
            $result = DB::table('funding_requests')
                ->join('users', 'funding_requests.user_id', '=', 'users.id')
                ->join('typefinanciements', 'funding_requests.typefinancement_id', '=', 'typefinanciements.id')
                ->where('funding_requests.id', $id)
                ->select(
                    'funding_requests.id',
                    'funding_requests.request_number',
                    'funding_requests.status',
                    'funding_requests.amount_requested',
                    'funding_requests.amount_approved',
                    'funding_requests.final_fee_paid',
                    'funding_requests.final_fee_paid_at',
                    'funding_requests.approved_at',
                    'funding_requests.questionnaire_submitted_at',
                    'funding_requests.questionnaire_data',
                    'users.id as user_id',
                    'users.first_name',
                    'users.last_name',
                    'users.email',
                    'typefinanciements.name as type_name',
                    'typefinanciements.registration_final_fee',
                    'typefinanciements.registration_fee'
                )
                ->first();

            if (!$result) {
                Log::warning('Demande de financement non trouvée', ['id' => $id]);
            }

            return $result;

        } catch (\Exception $e) {
            Log::error('Erreur récupération demande', [
                'id' => $id,
                'error' => $e->getMessage()
            ]);
            return null;
        }
    }
}
