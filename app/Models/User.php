<?php

namespace App\Models;

use App\Models\DocumentUser;
use App\Models\FundingRequest;
use App\Models\Notification;
use App\Models\SupportMessage;
use App\Models\SupportTicket;
use App\Models\Transaction;
use App\Models\Wallet;
use App\Models\Referral;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
// Supprimer cette ligne si Sanctum n'est pas installé
// use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    // Supprimer HasApiTokens si non installé
    use HasFactory, Notifiable;

    protected $fillable = [
        // Identité
        'first_name',
        'last_name',
        'name',
        'email',
        'phone',
        'email_verified_at',
        'password',

        // Photo & documents
        'profile_photo',
        'id_number',
        'id_document_path',

        // Informations personnelles
        'birth_date',
        'gender',
        'bio',

        // Adresse complète
        'address',
        'city',
        'postal_code',
        'country',

        // Informations entreprise (legacy - dans table users)
        'company_name',
        'company_type',
        'sector',
        'job_title',
        'employees_count',
        'annual_turnover',

        // Membre
        'member_id',
        'member_since',
        'member_status',
        'member_type',

        // Statuts
        'is_active',
        'is_verified',
        'is_admin',
        'is_moderator',

        // Préférences
        'locale',
        'timezone',
        'preferences',

        // Connexion
        'last_login_at',
        'last_login_ip',
        'last_login_device',

        // Parrainage
        'referred_by',
        'referral_code',
        'referral_earnings',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
        'birth_date' => 'date',
        'member_since' => 'date',
        'is_active' => 'boolean',
        'is_verified' => 'boolean',
        'is_admin' => 'boolean',
        'is_moderator' => 'boolean',
        'last_login_at' => 'datetime',
        'preferences' => 'json',
        'referral_earnings' => 'decimal:2',
    ];

    // ============================================
    // RELATIONS
    // ============================================

    public function supportTickets(): HasMany
    {
        return $this->hasMany(SupportTicket::class);
    }

    public function supportMessages(): HasMany
    {
        return $this->hasMany(SupportMessage::class);
    }

    public function assignedTickets(): HasMany
    {
        return $this->hasMany(SupportTicket::class, 'assigned_to');
    }

    public function companies(): HasMany
    {
        return $this->hasMany(Company::class);
    }

    public function primaryCompany(): HasOne
    {
        return $this->hasOne(Company::class)->where('is_primary', true);
    }

    public function company(): ?Company
    {
        return $this->primaryCompany ?? $this->companies()->first();
    }

    public function wallet(): HasOne
    {
        return $this->hasOne(Wallet::class);
    }

    public function fundingRequests(): HasMany
    {
        return $this->hasMany(FundingRequest::class);
    }

    public function documentUsers(): HasMany
    {
        return $this->hasMany(DocumentUser::class);
    }

    public function notifications(): HasMany
    {
        return $this->hasMany(Notification::class);
    }

    public function transactions()
    {
        return $this->hasManyThrough(Transaction::class, Wallet::class);
    }

    // ============================================
    // RELATIONS PARRAINAGE
    // ============================================

    public function referrer(): BelongsTo
    {
        return $this->belongsTo(User::class, 'referred_by');
    }

    public function referrals(): HasMany
    {
        return $this->hasMany(User::class, 'referred_by');
    }

    public function referralTransactions(): HasMany
    {
        return $this->hasMany(Referral::class, 'referrer_id');
    }

    // ============================================
    // MÉTHODES PARRAINAGE
    // ============================================

    public function generateReferralCode(): string
    {
        $baseCode = strtoupper(substr($this->first_name, 0, 3) . substr($this->last_name, 0, 3));
        $baseCode = preg_replace('/[^A-Z0-9]/', '', $baseCode);

        if (strlen($baseCode) < 6) {
            $baseCode = str_pad($baseCode, 6, 'X');
        }

        $code = $baseCode . rand(100, 999);

        while (User::where('referral_code', $code)->exists()) {
            $code = $baseCode . rand(100, 999);
        }

        return $code;
    }

    public function getReferralLinkAttribute(): string
    {
        if (!$this->referral_code) {
            return route('register');
        }
        return route('register.with.referral', ['ref' => $this->referral_code]);
    }

    // ============================================
    // MÉTHODES UTILITAIRES
    // ============================================

    public function isEntreprise(): bool
    {
        return $this->member_type === 'entreprise';
    }

    public function isAdmin(): bool
    {
        return $this->is_admin === true;
    }

    public function getFullNameAttribute(): string
    {
        return trim("{$this->first_name} {$this->last_name}");
    }

    public function hasPrimaryCompany(): bool
    {
        return $this->companies()->where('is_primary', true)->exists();
    }
}
