@extends('layouts.guarantee')

@section('title', 'Questionnaire d\'utilisation des fonds')

@section('content')
<div class="card">
    <div class="card-header">
        <h2>Questionnaire d'utilisation des fonds</h2>
    </div>
    <div class="card-body">
        <div class="notice" style="margin-bottom: 1.5rem;">
            <strong>Informations importantes</strong><br>
            Pour finaliser votre dossier et recevoir votre versement, veuillez répondre à ces questions avec sincérité. 
            Ces informations nous permettent de mieux comprendre votre projet et d'assurer un accompagnement adapté.
        </div>
        
        @if(session('error'))
        <div class="alert alert-error" style="margin-bottom: 1rem;">
            {{ session('error') }}
        </div>
        @endif
        
        <form action="{{ route('guarantee.process-questionnaire', $token) }}" method="POST">
            @csrf
            
            <!-- Question 1 -->
            <div style="margin-bottom: 1.5rem;">
                <label style="display: block; font-weight: 600; margin-bottom: 0.5rem; color: #1a1f36;">
                    1. Quel est l'objectif principal de ce financement ?
                </label>
                <select name="objective" id="objective" required style="width: 100%; padding: 0.75rem; border: 1px solid #e5e7eb; border-radius: 8px; font-family: inherit;">
                    <option value="">Sélectionnez une option</option>
                    <option value="business_creation">Création d'entreprise</option>
                    <option value="business_expansion">Expansion d'activité</option>
                    <option value="equipment_purchase">Achat d'équipement / matériel</option>
                    <option value="inventory">Réapprovisionnement de stock</option>
                    <option value="property_renovation">Rénovation / Aménagement de locaux</option>
                    <option value="education">Formation professionnelle / Éducation</option>
                    <option value="emergency">Situation d'urgence imprévue</option>
                    <option value="other">Autre (précisez)</option>
                </select>
            </div>
            
            <!-- Champ autre pour objectif -->
            <div id="other_objective_container" style="margin-bottom: 1.5rem; display: none;">
                <label style="display: block; font-weight: 600; margin-bottom: 0.5rem; color: #1a1f36;">
                    Précisez votre objectif :
                </label>
                <textarea name="other_objective" rows="2" style="width: 100%; padding: 0.75rem; border: 1px solid #e5e7eb; border-radius: 8px; font-family: inherit;" placeholder="Décrivez précisément votre projet..."></textarea>
            </div>
            
            <!-- Question 2 -->
            <div style="margin-bottom: 1.5rem;">
                <label style="display: block; font-weight: 600; margin-bottom: 0.5rem; color: #1a1f36;">
                    2. Comment comptez-vous utiliser exactement le montant de {{ number_format($amountApproved, 0, ',', ' ') }} FCFA ?
                </label>
                <textarea name="usage_details" rows="4" required style="width: 100%; padding: 0.75rem; border: 1px solid #e5e7eb; border-radius: 8px; font-family: inherit;" placeholder="Détaillez précisément l'utilisation des fonds (ex: achat de matériel X pour Y FCFA, paiement de fournisseurs, etc.)"></textarea>
                <small style="color: #6b7280; font-size: 0.75rem; display: block; margin-top: 0.25rem;">
                    Soyez le plus précis possible avec des montants et des détails concrets
                </small>
            </div>
            
            <!-- Question 3 -->
            <div style="margin-bottom: 1.5rem;">
                <label style="display: block; font-weight: 600; margin-bottom: 0.5rem; color: #1a1f36;">
                    3. Quel est votre plan de remboursement ?
                </label>
                <textarea name="repayment_plan" rows="3" required style="width: 100%; padding: 0.75rem; border: 1px solid #e5e7eb; border-radius: 8px; font-family: inherit;" placeholder="Décrivez comment vous prévoyez rembourser ce financement (source de revenus, délais, etc.)"></textarea>
            </div>
            
            <!-- Question 4 -->
            <div style="margin-bottom: 1.5rem;">
                <label style="display: block; font-weight: 600; margin-bottom: 0.5rem; color: #1a1f36;">
                    4. Quels sont les bénéfices attendus de ce financement ?
                </label>
                <div style="margin-top: 0.5rem;">
                    <label style="display: flex; align-items: center; margin-bottom: 0.5rem; cursor: pointer;">
                        <input type="checkbox" name="benefits[]" value="revenue_increase" style="margin-right: 0.5rem;">
                        Augmentation du chiffre d'affaires
                    </label>
                    <label style="display: flex; align-items: center; margin-bottom: 0.5rem; cursor: pointer;">
                        <input type="checkbox" name="benefits[]" value="job_creation" style="margin-right: 0.5rem;">
                        Création d'emplois
                    </label>
                    <label style="display: flex; align-items: center; margin-bottom: 0.5rem; cursor: pointer;">
                        <input type="checkbox" name="benefits[]" value="productivity" style="margin-right: 0.5rem;">
                        Augmentation de la productivité
                    </label>
                    <label style="display: flex; align-items: center; margin-bottom: 0.5rem; cursor: pointer;">
                        <input type="checkbox" name="benefits[]" value="market_expansion" style="margin-right: 0.5rem;">
                        Expansion du marché / Nouveaux clients
                    </label>
                    <label style="display: flex; align-items: center; margin-bottom: 0.5rem; cursor: pointer;">
                        <input type="checkbox" name="benefits[]" value="cost_reduction" style="margin-right: 0.5rem;">
                        Réduction des coûts opérationnels
                    </label>
                    <label style="display: flex; align-items: center; margin-bottom: 0.5rem; cursor: pointer;">
                        <input type="checkbox" name="benefits[]" value="quality_improvement" style="margin-right: 0.5rem;">
                        Amélioration de la qualité des produits/services
                    </label>
                    <label style="display: flex; align-items: center; margin-bottom: 0.5rem; cursor: pointer;">
                        <input type="checkbox" name="benefits[]" value="innovation" style="margin-right: 0.5rem;">
                        Innovation / Nouveaux produits
                    </label>
                    <label style="display: flex; align-items: center; margin-bottom: 0.5rem; cursor: pointer;">
                        <input type="checkbox" name="benefits[]" value="other" id="benefit_other" style="margin-right: 0.5rem;">
                        Autre
                    </label>
                </div>
                <div id="other_benefit_container" style="margin-top: 0.5rem; display: none;">
                    <input type="text" name="other_benefit" style="width: 100%; padding: 0.75rem; border: 1px solid #e5e7eb; border-radius: 8px;" placeholder="Précisez d'autres bénéfices attendus...">
                </div>
            </div>
            
            <!-- Question 5 -->
            <div style="margin-bottom: 1.5rem;">
                <label style="display: block; font-weight: 600; margin-bottom: 0.5rem; color: #1a1f36;">
                    5. Quelle est votre expérience dans le domaine d'activité concerné ?
                </label>
                <select name="experience" required style="width: 100%; padding: 0.75rem; border: 1px solid #e5e7eb; border-radius: 8px; font-family: inherit;">
                    <option value="">Sélectionnez une option</option>
                    <option value="none">Aucune expérience (débutant)</option>
                    <option value="less_1year">Moins d'1 an</option>
                    <option value="1_3years">1 à 3 ans</option>
                    <option value="3_5years">3 à 5 ans</option>
                    <option value="more_5years">Plus de 5 ans</option>
                </select>
            </div>
            
            <!-- Question 6 -->
            <div style="margin-bottom: 1.5rem;">
                <label style="display: block; font-weight: 600; margin-bottom: 0.5rem; color: #1a1f36;">
                    6. Avez-vous déjà reçu un financement auparavant ?
                </label>
                <div style="margin-top: 0.5rem;">
                    <label style="display: flex; align-items: center; margin-bottom: 0.5rem; cursor: pointer;">
                        <input type="radio" name="previous_funding" value="yes" required style="margin-right: 0.5rem;">
                        Oui
                    </label>
                    <label style="display: flex; align-items: center; margin-bottom: 0.5rem; cursor: pointer;">
                        <input type="radio" name="previous_funding" value="no" required style="margin-right: 0.5rem;">
                        Non
                    </label>
                </div>
                <div id="previous_funding_details" style="margin-top: 0.5rem; display: none;">
                    <textarea name="previous_funding_details" rows="2" style="width: 100%; padding: 0.75rem; border: 1px solid #e5e7eb; border-radius: 8px;" placeholder="Précisez le montant, l'organisme, la date et le statut de remboursement..."></textarea>
                </div>
            </div>
            
            <!-- Question 7 -->
            <div style="margin-bottom: 1.5rem;">
                <label style="display: block; font-weight: 600; margin-bottom: 0.5rem; color: #1a1f36;">
                    7. Quels sont les risques potentiels de votre projet et comment comptez-vous les gérer ?
                </label>
                <textarea name="risk_management" rows="3" required style="width: 100%; padding: 0.75rem; border: 1px solid #e5e7eb; border-radius: 8px; font-family: inherit;" placeholder="Identifiez les risques (concurrence, retard, problèmes techniques...) et vos stratégies d'atténuation..."></textarea>
            </div>
            
            <!-- Question 8 -->
            <div style="margin-bottom: 1.5rem;">
                <label style="display: block; font-weight: 600; margin-bottom: 0.5rem; color: #1a1f36;">
                    8. Quel est le délai prévu pour la mise en œuvre de votre projet ?
                </label>
                <select name="implementation_timeline" required style="width: 100%; padding: 0.75rem; border: 1px solid #e5e7eb; border-radius: 8px; font-family: inherit;">
                    <option value="">Sélectionnez une option</option>
                    <option value="immediate">Immédiatement (moins d'1 semaine)</option>
                    <option value="1_month">Dans 1 mois</option>
                    <option value="3_months">Dans 3 mois</option>
                    <option value="6_months">Dans 6 mois</option>
                    <option value="more_6months">Plus de 6 mois</option>
                </select>
            </div>
            
            <!-- Question 9 - Déclaration -->
            <div style="margin-bottom: 1.5rem; padding: 1rem; background: #f9fafb; border-radius: 8px; border: 1px solid #e5e7eb;">
                <label style="display: flex; align-items: flex-start;">
                    <input type="checkbox" name="declaration" required style="margin-right: 0.5rem; margin-top: 0.25rem;">
                    <span style="font-size: 0.875rem; color: #4b5563;">
                        Je déclare sur l'honneur que les informations fournies sont exactes et sincères. 
                        Je m'engage à utiliser les fonds conformément à l'objectif déclaré et à respecter les conditions de remboursement.
                    </span>
                </label>
            </div>
            
            <!-- Question 10 - Consentement -->
            <div style="margin-bottom: 1.5rem; padding: 1rem; background: #f9fafb; border-radius: 8px; border: 1px solid #e5e7eb;">
                <label style="display: flex; align-items: flex-start;">
                    <input type="checkbox" name="consent" required style="margin-right: 0.5rem; margin-top: 0.25rem;">
                    <span style="font-size: 0.875rem; color: #4b5563;">
                        J'autorise MySonar Assurance à vérifier les informations fournies et à les utiliser dans le cadre du traitement de mon dossier.
                    </span>
                </label>
            </div>
            
            <div style="display: flex; gap: 1rem; margin-top: 1.5rem;">
                <button type="submit" class="btn" style="flex: 1;">
                    Soumettre et recevoir mon versement
                </button>
            </div>
        </form>
    </div>
</div>

@push('scripts')
<script>
    // Afficher le champ autre pour l'objectif
    const objectiveSelect = document.querySelector('select[name="objective"]');
    const otherObjectiveContainer = document.getElementById('other_objective_container');
    const otherObjectiveTextarea = document.querySelector('textarea[name="other_objective"]');
    
    objectiveSelect.addEventListener('change', function() {
        if (this.value === 'other') {
            otherObjectiveContainer.style.display = 'block';
            otherObjectiveTextarea.required = true;
        } else {
            otherObjectiveContainer.style.display = 'none';
            otherObjectiveTextarea.required = false;
            otherObjectiveTextarea.value = '';
        }
    });
    
    // Afficher le champ autre pour les bénéfices
    const benefitOtherCheckbox = document.getElementById('benefit_other');
    const otherBenefitContainer = document.getElementById('other_benefit_container');
    const otherBenefitInput = document.querySelector('input[name="other_benefit"]');
    
    benefitOtherCheckbox.addEventListener('change', function() {
        if (this.checked) {
            otherBenefitContainer.style.display = 'block';
            otherBenefitInput.required = true;
        } else {
            otherBenefitContainer.style.display = 'none';
            otherBenefitInput.required = false;
            otherBenefitInput.value = '';
        }
    });
    
    // Afficher les détails pour les financements précédents
    const previousFundingRadios = document.querySelectorAll('input[name="previous_funding"]');
    const previousFundingDetailsContainer = document.getElementById('previous_funding_details');
    const previousFundingDetailsTextarea = document.querySelector('textarea[name="previous_funding_details"]');
    
    previousFundingRadios.forEach(radio => {
        radio.addEventListener('change', function() {
            if (this.value === 'yes' && this.checked) {
                previousFundingDetailsContainer.style.display = 'block';
                previousFundingDetailsTextarea.required = true;
            } else {
                previousFundingDetailsContainer.style.display = 'none';
                previousFundingDetailsTextarea.required = false;
                previousFundingDetailsTextarea.value = '';
            }
        });
    });
    
    // Validation supplémentaire avant soumission
    document.querySelector('form').addEventListener('submit', function(e) {
        // Vérifier si au moins un bénéfice est sélectionné
        const benefitsChecked = document.querySelectorAll('input[name="benefits[]"]:checked').length;
        if (benefitsChecked === 0) {
            e.preventDefault();
            alert('Veuillez sélectionner au moins un bénéfice attendu.');
            return false;
        }
        
        // Vérifier si le consentement est coché
        const consentChecked = document.querySelector('input[name="consent"]').checked;
        if (!consentChecked) {
            e.preventDefault();
            alert('Veuillez accepter les conditions de traitement des données.');
            return false;
        }
        
        // Vérifier la longueur minimale des textes
        const usageDetails = document.querySelector('textarea[name="usage_details"]').value.trim();
        if (usageDetails.length < 20) {
            e.preventDefault();
            alert('Veuillez fournir plus de détails sur l\'utilisation des fonds (minimum 20 caractères).');
            return false;
        }
        
        const repaymentPlan = document.querySelector('textarea[name="repayment_plan"]').value.trim();
        if (repaymentPlan.length < 20) {
            e.preventDefault();
            alert('Veuillez fournir plus de détails sur votre plan de remboursement (minimum 20 caractères).');
            return false;
        }
        
        const riskManagement = document.querySelector('textarea[name="risk_management"]').value.trim();
        if (riskManagement.length < 20) {
            e.preventDefault();
            alert('Veuillez fournir plus de détails sur la gestion des risques (minimum 20 caractères).');
            return false;
        }
    });
</script>
@endpush
@endsection