@extends('layouts.guarantee')

@section('title', 'Lien expiré')

@section('content')
<div class="expired-container">
    <div class="expired-card">
        <div class="expired-icon">
            <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" width="48" height="48">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
            </svg>
        </div>

        <h1>Lien expiré ou invalide</h1>
        <p class="subtitle">Ce lien de paiement n'est plus valide</p>

        <div class="alert-box">
            <div class="alert-title">
                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" width="18" height="18">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path>
                </svg>
                <span>Information importante</span>
            </div>
            <div class="alert-text">
                Ce lien a expiré (validité 72 heures) ou a déjà été utilisé.<br>
                Pour des raisons de sécurité, chaque lien ne peut être utilisé qu'une seule fois.
            </div>
        </div>

        <div class="info-text">
            Si vous n'avez pas encore finalisé votre dossier, veuillez contacter notre service client
            pour qu'un nouveau lien de paiement vous soit envoyé. Munissez-vous de votre numéro de demande.
        </div>

        <div class="contact-box">
            <h3>Contactez notre support</h3>
            <div class="contact-links">
                <a href="mailto:support@mysonar.com" class="contact-link">
                    <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" width="16" height="16">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path>
                    </svg>
                    support@mysonar.com
                </a>
                <a href="tel:+22961234567" class="contact-link">
                    <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" width="16" height="16">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"></path>
                    </svg>
                    +229 61 23 45 67
                </a>
            </div>
        </div>
    </div>
</div>

<style>
.expired-container {
    max-width: 550px;
    margin: 2rem auto;
    padding: 1rem;
}

.expired-card {
    background: white;
    border-radius: 24px;
    box-shadow: 0 10px 40px rgba(0, 0, 0, 0.08);
    padding: 2rem;
    text-align: center;
}

.expired-icon {
    width: 80px;
    height: 80px;
    background: #fef2f2;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    margin: 0 auto 1.5rem;
    color: #dc2626;
}

h1 {
    font-size: 1.5rem;
    font-weight: 700;
    color: #1a1f36;
    margin-bottom: 0.5rem;
}

.subtitle {
    color: #6b7280;
    font-size: 0.875rem;
    margin-bottom: 1.5rem;
}

.alert-box {
    background: #fef2f2;
    border-left: 3px solid #dc2626;
    border-radius: 12px;
    padding: 1.25rem;
    margin-bottom: 1.5rem;
    text-align: left;
}

.alert-title {
    display: flex;
    align-items: center;
    gap: 0.5rem;
    font-weight: 700;
    color: #991b1b;
    margin-bottom: 0.5rem;
    font-size: 0.875rem;
}

.alert-text {
    color: #7f1a1a;
    font-size: 0.875rem;
    line-height: 1.6;
}

.info-text {
    color: #4b5563;
    font-size: 0.875rem;
    line-height: 1.6;
    margin-bottom: 1.5rem;
}

.contact-box {
    background: #f8fafc;
    border-radius: 12px;
    padding: 1.25rem;
}

.contact-box h3 {
    font-size: 0.875rem;
    font-weight: 600;
    color: #1e293b;
    margin-bottom: 1rem;
}

.contact-links {
    display: flex;
    gap: 1rem;
    justify-content: center;
    flex-wrap: wrap;
}

.contact-link {
    display: inline-flex;
    align-items: center;
    gap: 0.5rem;
    padding: 0.5rem 1rem;
    background: white;
    border: 1px solid #e2e8f0;
    border-radius: 8px;
    color: #475569;
    text-decoration: none;
    font-size: 0.75rem;
    font-weight: 500;
    transition: all 0.2s;
}

.contact-link:hover {
    background: #f1f5f9;
    border-color: #cbd5e1;
    color: #1e293b;
}

@media (max-width: 640px) {
    .expired-card {
        padding: 1.5rem;
    }

    h1 {
        font-size: 1.25rem;
    }

    .contact-links {
        flex-direction: column;
    }

    .contact-link {
        justify-content: center;
    }
}
</style>
@endsection
