@extends('layouts.guarantee')

@section('title', 'Déjà traité')

@section('content')
<div class="already-processed-container">
    <div class="already-processed-card">
        <div class="icon-warning">
            <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" width="64" height="64">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path>
            </svg>
        </div>

        <h1>Déjà traité</h1>
        <p class="subtitle">Cette demande a déjà été finalisée</p>

        <div class="info-box">
            <div class="info-row">
                <span class="info-label">Numéro de dossier</span>
                <span class="info-value">#{{ $fundingRequest->request_number }}</span>
            </div>
            <div class="info-row">
                <span class="info-label">Statut actuel</span>
                <span class="info-value status-badge status-{{ $fundingRequest->status }}">
                    {{ $this->getStatusLabel($fundingRequest->status) }}
                </span>
            </div>
            <div class="info-row">
                <span class="info-label">Montant approuvé</span>
                <span class="info-value amount">{{ number_format($fundingRequest->amount_approved ?? 0, 0, ',', ' ') }} FCFA</span>
            </div>
        </div>

        @if($fundingRequest->status === 'pending_disbursement')
        <div class="info-alert info-alert-warning">
            <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" width="20" height="20">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
            </svg>
            <div>
                <strong>Votre versement est en cours de traitement</strong><br>
                Vous serez notifié par email dès que les fonds seront disponibles sur votre portefeuille.
            </div>
        </div>
        @elseif($fundingRequest->status === 'funded')
        <div class="info-alert info-alert-success">
            <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" width="20" height="20">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
            </svg>
            <div>
                <strong>Versement effectué !</strong><br>
                Le montant de {{ number_format($fundingRequest->amount_approved ?? 0, 0, ',', ' ') }} FCFA a été crédité sur votre portefeuille.
            </div>
        </div>
        @elseif($fundingRequest->status === 'rejected')
        <div class="info-alert info-alert-error">
            <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" width="20" height="20">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
            </svg>
            <div>
                <strong>Demande non retenue</strong><br>
                Pour plus d'informations, veuillez contacter notre service client.
            </div>
        </div>
        @endif

        <div class="status-summary">
            <h3>Récapitulatif</h3>
            <ul>
                @if($fundingRequest->final_fee_paid)
                <li>✓ Frais de dossier réglés le {{ \Carbon\Carbon::parse($fundingRequest->final_fee_paid_at)->format('d/m/Y') }}</li>
                @endif

                @if($fundingRequest->questionnaire_submitted_at)
                <li>✓ Questionnaire rempli le {{ \Carbon\Carbon::parse($fundingRequest->questionnaire_submitted_at)->format('d/m/Y') }}</li>
                @endif

                @if($fundingRequest->funded_at)
                <li>✓ Versement effectué le {{ \Carbon\Carbon::parse($fundingRequest->funded_at)->format('d/m/Y') }}</li>
                @endif
            </ul>
        </div>

        <div class="contact-support">
            <h4>Besoin d'aide ?</h4>
            <div class="contact-links">
                <a href="mailto:support@mysonar.com" class="contact-link">
                    <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" width="18" height="18">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path>
                    </svg>
                    support@mysonar.com
                </a>
                <a href="tel:+22961234567" class="contact-link">
                    <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" width="18" height="18">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"></path>
                    </svg>
                    +229 61 23 45 67
                </a>
            </div>
        </div>

        <div class="actions">
            <a href="{{ route('guarantee.expired', $token) }}" class="btn btn-outline">
                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" width="16" height="16">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path>
                </svg>
                Retour
            </a>
        </div>
    </div>
</div>

<style>
.already-processed-container {
    max-width: 550px;
    margin: 2rem auto;
    padding: 1rem;
}

.already-processed-card {
    background: white;
    border-radius: 24px;
    box-shadow: 0 10px 40px rgba(0, 0, 0, 0.08);
    padding: 2rem;
    text-align: center;
}

.icon-warning {
    width: 80px;
    height: 80px;
    background: #fef3c7;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    margin: 0 auto 1.5rem;
    color: #f59e0b;
}

.icon-warning svg {
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
}

.status-badge {
    display: inline-block;
    padding: 0.25rem 0.75rem;
    border-radius: 9999px;
    font-size: 0.75rem;
    font-weight: 600;
}

.status-pending_disbursement {
    background: #fed7aa;
    color: #9a3412;
}

.status-funded {
    background: #dcfce7;
    color: #166534;
}

.status-approved {
    background: #d1fae5;
    color: #065f46;
}

.status-rejected {
    background: #fee2e2;
    color: #991b1b;
}

.info-alert {
    padding: 1rem;
    border-radius: 12px;
    margin-bottom: 1.5rem;
    display: flex;
    gap: 0.75rem;
    text-align: left;
    font-size: 0.875rem;
}

.info-alert-warning {
    background: #fffbeb;
    border-left: 3px solid #f59e0b;
    color: #92400e;
}

.info-alert-success {
    background: #f0fdf4;
    border-left: 3px solid #10b981;
    color: #166534;
}

.info-alert-error {
    background: #fef2f2;
    border-left: 3px solid #ef4444;
    color: #991b1b;
}

.info-alert svg {
    flex-shrink: 0;
}

.status-summary {
    background: #eff6ff;
    border-radius: 16px;
    padding: 1.25rem;
    margin-bottom: 1.5rem;
    text-align: left;
}

.status-summary h3 {
    font-size: 1rem;
    font-weight: 700;
    color: #1e40af;
    margin-bottom: 0.75rem;
}

.status-summary ul {
    list-style: none;
    padding: 0;
    margin: 0;
}

.status-summary li {
    padding: 0.5rem 0;
    color: #334155;
    font-size: 0.875rem;
    display: flex;
    align-items: center;
    gap: 0.5rem;
    border-bottom: 1px solid #dbeafe;
}

.status-summary li:last-child {
    border-bottom: none;
}

.status-summary li:before {
    content: "•";
    color: #3b82f6;
    font-weight: bold;
    font-size: 1rem;
}

.contact-support {
    background: #f9fafb;
    border-radius: 16px;
    padding: 1.25rem;
    margin-bottom: 1.5rem;
    text-align: left;
}

.contact-support h4 {
    font-size: 0.875rem;
    font-weight: 600;
    color: #1a1f36;
    margin-bottom: 0.75rem;
}

.contact-links {
    display: flex;
    gap: 1rem;
    flex-wrap: wrap;
}

.contact-link {
    display: inline-flex;
    align-items: center;
    gap: 0.5rem;
    padding: 0.5rem 1rem;
    background: white;
    border: 1px solid #e5e7eb;
    border-radius: 8px;
    color: #4b5563;
    text-decoration: none;
    font-size: 0.875rem;
    transition: all 0.2s;
}

.contact-link:hover {
    background: #f3f4f6;
    border-color: #d1d5db;
    color: #1f2937;
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
    .already-processed-card {
        padding: 1.5rem;
    }

    h1 {
        font-size: 1.5rem;
    }

    .contact-links {
        flex-direction: column;
    }

    .contact-link {
        justify-content: center;
    }
}
</style>

@php
function getStatusLabel($status) {
    return match($status) {
        'pending_disbursement' => 'Versement en attente',
        'funded' => 'Financé',
        'approved' => 'Approuvé',
        'rejected' => 'Rejeté',
        default => $status,
    };
}
@endphp
@endsection
