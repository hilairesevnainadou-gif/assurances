@extends('layouts.guarantee')

@section('title', 'Félicitations - Questionnaire envoyé')

@section('content')
<div class="success-container">
    <div class="success-card">
        <div class="success-icon">
            <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" width="64" height="64">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
            </svg>
        </div>

        <h1>Félicitations !</h1>
        <p class="subtitle">Votre questionnaire a été soumis avec succès</p>

        <div class="info-box">
            <div class="info-row">
                <span class="info-label">Numéro de dossier</span>
                <span class="info-value">#{{ $fundingRequest->request_number }}</span>
            </div>
            <div class="info-row">
                <span class="info-label">Montant approuvé</span>
                <span class="info-value amount">{{ number_format($fundingRequest->amount_approved ?? 0, 0, ',', ' ') }} FCFA</span>
            </div>
            <div class="info-row">
                <span class="info-label">Date de soumission</span>
                <span class="info-value">{{ now()->format('d/m/Y à H:i') }}</span>
            </div>
        </div>

        <div class="status-banner">
            <div class="status-step completed">
                <div class="step-icon">✓</div>
                <div class="step-text">Paiement des frais</div>
            </div>
            <div class="status-line completed"></div>
            <div class="status-step completed">
                <div class="step-icon">✓</div>
                <div class="step-text">Questionnaire rempli</div>
            </div>
            <div class="status-line"></div>
            <div class="status-step pending">
                <div class="step-icon">⏳</div>
                <div class="step-text">Versement en cours</div>
            </div>
        </div>

        <div class="message-box">
            <h3>Prochaines étapes</h3>
            <ul>
                <li>✓ Notre équipe examine votre questionnaire</li>
                <li>✓ Le versement sera effectué sous <strong>24 à 48 heures ouvrables</strong></li>
                <li>✓ Vous recevrez une confirmation par email dès que le transfert est initié</li>
                <li>✓ Les fonds seront crédités directement sur votre portefeuille MySonar</li>
            </ul>
        </div>

        <div class="info-alert">
            <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" width="20" height="20">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
            </svg>
            <div>
                <strong>Important</strong><br>
                Vérifiez votre boîte email (y compris les spams) pour le suivi de votre dossier.
            </div>
        </div>

        <div class="actions">
            <a href="{{ route('guarantee.show', $token) }}" class="btn btn-outline">
                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" width="16" height="16">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path>
                </svg>
                Retour au dossier
            </a>
        </div>
    </div>
</div>

<style>
.success-container {
    max-width: 600px;
    margin: 2rem auto;
    padding: 1rem;
}

.success-card {
    background: white;
    border-radius: 24px;
    box-shadow: 0 10px 40px rgba(0, 0, 0, 0.08);
    padding: 2rem;
    text-align: center;
}

.success-icon {
    width: 80px;
    height: 80px;
    background: linear-gradient(135deg, #10b981 0%, #059669 100%);
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    margin: 0 auto 1.5rem;
    color: white;
}

.success-icon svg {
    width: 40px;
    height: 40px;
}

h1 {
    font-size: 1.75rem;
    font-weight: 700;
    color: #1a1f36;
    margin-bottom: 0.5rem;
}

.subtitle {
    color: #6b7280;
    font-size: 0.95rem;
    margin-bottom: 2rem;
}

.info-box {
    background: #f9fafb;
    border-radius: 16px;
    padding: 1.25rem;
    margin-bottom: 1.5rem;
    text-align: left;
}

.info-row {
    display: flex;
    justify-content: space-between;
    padding: 0.5rem 0;
    border-bottom: 1px solid #e5e7eb;
}

.info-row:last-child {
    border-bottom: none;
}

.info-label {
    color: #6b7280;
    font-size: 0.875rem;
}

.info-value {
    font-weight: 600;
    color: #1a1f36;
}

.info-value.amount {
    color: #10b981;
    font-size: 1.1rem;
}

.status-banner {
    display: flex;
    align-items: center;
    justify-content: space-between;
    margin: 1.5rem 0;
    padding: 1rem;
    background: #f9fafb;
    border-radius: 16px;
}

.status-step {
    text-align: center;
    flex: 1;
}

.status-step .step-icon {
    width: 32px;
    height: 32px;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    margin: 0 auto 0.5rem;
    font-weight: 700;
    font-size: 0.875rem;
}

.status-step.completed .step-icon {
    background: #10b981;
    color: white;
}

.status-step.pending .step-icon {
    background: #fef3c7;
    color: #f59e0b;
}

.status-step .step-text {
    font-size: 0.75rem;
    color: #6b7280;
    font-weight: 500;
}

.status-line {
    width: 50px;
    height: 2px;
    background: #e5e7eb;
}

.status-line.completed {
    background: #10b981;
}

.message-box {
    background: #eff6ff;
    border-radius: 16px;
    padding: 1.25rem;
    margin-bottom: 1.5rem;
    text-align: left;
}

.message-box h3 {
    font-size: 1rem;
    font-weight: 700;
    color: #1e40af;
    margin-bottom: 0.75rem;
}

.message-box ul {
    list-style: none;
    padding: 0;
    margin: 0;
}

.message-box li {
    padding: 0.5rem 0;
    color: #334155;
    font-size: 0.875rem;
    display: flex;
    align-items: center;
    gap: 0.5rem;
}

.message-box li:before {
    content: "•";
    color: #3b82f6;
    font-weight: bold;
    font-size: 1.2rem;
}

.info-alert {
    background: #fffbeb;
    border-left: 3px solid #f59e0b;
    padding: 1rem;
    border-radius: 12px;
    margin-bottom: 1.5rem;
    display: flex;
    gap: 0.75rem;
    text-align: left;
    font-size: 0.875rem;
    color: #92400e;
}

.info-alert svg {
    flex-shrink: 0;
    color: #f59e0b;
}

.actions {
    display: flex;
    gap: 1rem;
    justify-content: center;
}

.btn-outline {
    display: inline-flex;
    align-items: center;
    gap: 0.5rem;
    padding: 0.75rem 1.5rem;
    background: white;
    border: 1px solid #e5e7eb;
    border-radius: 12px;
    color: #4b5563;
    text-decoration: none;
    font-weight: 500;
    font-size: 0.875rem;
    transition: all 0.2s;
}

.btn-outline:hover {
    background: #f9fafb;
    border-color: #d1d5db;
}

@media (max-width: 640px) {
    .success-card {
        padding: 1.5rem;
    }

    h1 {
        font-size: 1.5rem;
    }

    .status-step .step-text {
        font-size: 0.65rem;
    }

    .status-line {
        width: 30px;
    }

    .message-box li {
        font-size: 0.8rem;
    }
}
</style>
@endsection
