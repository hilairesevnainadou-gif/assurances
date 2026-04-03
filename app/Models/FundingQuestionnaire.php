<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class FundingQuestionnaire extends Model
{
    use HasFactory;

    protected $table = 'funding_questionnaires';

    protected $fillable = [
        'funding_request_id',
        'objective',
        'other_objective',
        'usage_details',
        'repayment_plan',
        'benefits',
        'experience',
        'has_previous_funding',
        'previous_funding_details',
        'risk_management',
        'declaration_accepted',
        'submitted_at',
    ];

    protected $casts = [
        'benefits' => 'array',
        'has_previous_funding' => 'boolean',
        'declaration_accepted' => 'boolean',
        'submitted_at' => 'datetime',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    // Relations
    public function fundingRequest(): BelongsTo
    {
        return $this->belongsTo(FundingRequest::class);
    }

    // Accesseurs
    public function getObjectiveLabelAttribute(): string
    {
        $labels = [
            'business_creation' => 'Création d\'entreprise',
            'business_expansion' => 'Expansion d\'activité',
            'equipment_purchase' => 'Achat d\'équipement',
            'inventory' => 'Réapprovisionnement de stock',
            'property_renovation' => 'Rénovation de locaux',
            'education' => 'Formation / Éducation',
            'emergency' => 'Situation d\'urgence',
            'other' => 'Autre',
        ];

        $objective = $this->objective;
        $label = $labels[$objective] ?? $objective;

        if ($objective === 'other' && $this->other_objective) {
            $label .= " : {$this->other_objective}";
        }

        return $label;
    }

    public function getExperienceLabelAttribute(): string
    {
        $labels = [
            'none' => 'Aucune expérience',
            'less_1year' => 'Moins d\'1 an',
            '1_3years' => '1 à 3 ans',
            '3_5years' => '3 à 5 ans',
            'more_5years' => 'Plus de 5 ans',
        ];

        return $labels[$this->experience] ?? $this->experience;
    }

    public function getBenefitsListAttribute(): string
    {
        $labels = [
            'revenue_increase' => 'Augmentation du chiffre d\'affaires',
            'job_creation' => 'Création d\'emplois',
            'productivity' => 'Augmentation de la productivité',
            'market_expansion' => 'Expansion du marché',
            'cost_reduction' => 'Réduction des coûts',
            'quality_improvement' => 'Amélioration de la qualité',
            'innovation' => 'Innovation',
            'other' => 'Autre',
        ];

        $benefits = is_array($this->benefits) ? $this->benefits : json_decode($this->benefits, true);

        $list = [];
        foreach ($benefits as $benefit) {
            $list[] = $labels[$benefit] ?? $benefit;
        }

        return implode(', ', $list);
    }

    public function getHasPreviousFundingLabelAttribute(): string
    {
        return $this->has_previous_funding ? 'Oui' : 'Non';
    }
}
