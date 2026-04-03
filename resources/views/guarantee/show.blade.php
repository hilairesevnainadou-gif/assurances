@extends('layouts.guarantee')

@section('title', 'Paiement des frais')

@section('content')
<div class="grid-2">
    <!-- Colonne gauche - Récapitulatif -->
    <div class="card">
        <div class="card-header">
            <h2>Récapitulatif du dossier</h2>
        </div>
        <div class="card-body">
            <div class="info-row">
                <span class="info-label">Numéro de dossier</span>
                <span class="info-value">{{ $fundingRequest->request_number }}</span>
            </div>
            <div class="info-row">
                <span class="info-label">Bénéficiaire</span>
                <span class="info-value">{{ $fundingRequest->first_name }} {{ $fundingRequest->last_name }}</span>
            </div>
            <div class="info-row">
                <span class="info-label">Type de financement</span>
                <span class="info-value">{{ $fundingRequest->type_name }}</span>
            </div>
            <div class="info-row">
                <span class="info-label">Montant approuvé</span>
                <span class="info-value amount">{{ number_format($amountApproved, 0, ',', ' ') }} FCFA</span>
            </div>
            
            <div class="fee-box">
                <div class="fee-label">Frais de dossier</div>
                <div class="fee-amount">{{ number_format($finalFee, 0, ',', ' ') }} FCFA</div>
                <div class="fee-sub">À régler pour finaliser votre dossier</div>
            </div>
        </div>
    </div>
    
    <!-- Colonne droite - Paiement -->
    <div class="card">
        <div class="card-header">
            <h2>Paiement sécurisé</h2>
        </div>
        <div class="card-body">
            <div class="notice">
                Le versement de votre financement sera effectué immédiatement après validation de votre paiement.
            </div>
            
            <div id="errorAlert" class="alert alert-error hidden"></div>
            
            <form id="paymentForm" action="{{ route('guarantee.pay', $token) }}" method="POST" class="hidden">
                @csrf
                <input type="hidden" name="kkiapay_transaction_id" id="kkiapayTxnId">
            </form>
            
            <button class="btn" id="payBtn" onclick="openKkiapay()">
                Payer {{ number_format($finalFee, 0, ',', ' ') }} FCFA
            </button>
            
            <div style="margin-top: 1rem; text-align: center; font-size: 0.75rem; color: #9ca3af;">
                Paiement sécurisé par KKiaPay
            </div>
        </div>
    </div>
</div>

<!-- Témoignages - Versements effectués -->
<div class="card">
    <div class="card-header">
        <h2>Versements récents</h2>
    </div>
    <div class="card-body">
        <div class="testimonial-grid">
            <div class="testimonial">
                <div class="testimonial-text">
                    "Mon financement a été versé dans les 24h après mon paiement. Processus fiable."
                </div>
                <div class="testimonial-author">Amara Koné</div>
                <div class="testimonial-date">Versement reçu le 15/03/2026</div>
                <div class="payment-received">✓ 500 000 FCFA reçus</div>
            </div>
            
            <div class="testimonial">
                <div class="testimonial-text">
                    "Service sérieux et rapide. Je recommande pour les projets professionnels."
                </div>
                <div class="testimonial-author">Fatima Diallo</div>
                <div class="testimonial-date">Versement reçu le 10/03/2026</div>
                <div class="payment-received">✓ 750 000 FCFA reçus</div>
            </div>
            
            <div class="testimonial">
                <div class="testimonial-text">
                    "Très satisfait du service. Le versement est arrivé comme convenu."
                </div>
                <div class="testimonial-author">Jean-Marc B.</div>
                <div class="testimonial-date">Versement reçu le 05/03/2026</div>
                <div class="payment-received">✓ 1 200 000 FCFA reçus</div>
            </div>
        </div>
        
        <div class="stats">
            <div>
                <div class="stat-number">500+</div>
                <div class="stat-label">Dossiers financés</div>
            </div>
            <div>
                <div class="stat-number">2.5M</div>
                <div class="stat-label">FCFA versés</div>
            </div>
            <div>
                <div class="stat-number">24h</div>
                <div class="stat-label">Délai de versement</div>
            </div>
            <div>
                <div class="stat-number">98%</div>
                <div class="stat-label">Clients satisfaits</div>
            </div>
        </div>
    </div>
</div>

<!-- Contact -->
<div class="card">
    <div class="card-header">
        <h2>Besoin d'assistance ?</h2>
    </div>
    <div class="card-body">
        <div class="contact-grid">
            <div class="contact-item">
                <i class="fas fa-phone"></i>
                <h4>Téléphone</h4>
                <p>+229 61 23 45 67</p>
            </div>
            <div class="contact-item">
                <i class="fas fa-envelope"></i>
                <h4>Email</h4>
                <p>support@mysonar.com</p>
            </div>
            <div class="contact-item">
                <i class="fab fa-whatsapp"></i>
                <h4>WhatsApp</h4>
                <p>+229 61 23 45 68</p>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
    const KKIAPAY_PUBLIC_KEY = "{{ config('services.kkiapay.public_key') }}";
    const AMOUNT = {{ (int) $finalFee }};
    const IS_SANDBOX = {{ config('services.kkiapay.sandbox') ? 'true' : 'false' }};
    
    function openKkiapay() {
        const btn = document.getElementById('payBtn');
        btn.disabled = true;
        btn.innerHTML = '<div class="spinner"></div> Connexion en cours...';
        
        try {
            openKkiapayWidget({
                amount: AMOUNT,
                api_key: KKIAPAY_PUBLIC_KEY,
                sandbox: IS_SANDBOX,
                name: "{{ addslashes($fundingRequest->first_name . ' ' . $fundingRequest->last_name) }}",
                email: "{{ $fundingRequest->email }}",
                theme: "#1a56db"
            });
        } catch (error) {
            showError('Erreur de connexion au service de paiement');
            resetBtn();
        }
    }
    
    if (typeof addSuccessListener === 'function') {
        addSuccessListener(function(response) {
            if (response.transactionId) {
                document.getElementById('kkiapayTxnId').value = response.transactionId;
                document.getElementById('paymentForm').submit();
            } else {
                showError('Transaction invalide');
                resetBtn();
            }
        });
    }
    
    if (typeof addFailedListener === 'function') {
        addFailedListener(function() {
            showError('Le paiement a échoué. Veuillez réessayer.');
            resetBtn();
        });
    }
    
    function resetBtn() {
        const btn = document.getElementById('payBtn');
        btn.disabled = false;
        btn.innerHTML = 'Payer {{ number_format($finalFee, 0, ',', ' ') }} FCFA';
    }
    
    function showError(message) {
        const alert = document.getElementById('errorAlert');
        alert.textContent = message;
        alert.classList.remove('hidden');
        setTimeout(() => alert.classList.add('hidden'), 5000);
    }
</script>
@endpush
@endsection