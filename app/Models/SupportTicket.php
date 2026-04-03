<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class SupportTicket extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'ticket_number',
        'subject',
        'description',
        'category',
        'priority',
        'status',
        'assigned_to',
        'resolved_at',
        'closed_at',
        'metadata'
    ];

    protected $casts = [
        'resolved_at' => 'datetime',
        'closed_at' => 'datetime',
        'metadata' => 'array'
    ];

    protected $attributes = [
        'category' => 'general',
        'priority' => 'medium',
        'status' => 'open'
    ];

    // ============================================
    // RELATIONS
    // ============================================

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function assignee(): BelongsTo
    {
        return $this->belongsTo(User::class, 'assigned_to');
    }

    public function messages(): HasMany
    {
        return $this->hasMany(SupportMessage::class, 'ticket_id');
    }

    // ============================================
    // ACCESSORS
    // ============================================

    public function getStatusBadgeAttribute(): string
    {
        $badges = [
            'open' => '<span class="badge bg-primary">Ouvert</span>',
            'in_progress' => '<span class="badge bg-warning">En cours</span>',
            'resolved' => '<span class="badge bg-success">Résolu</span>',
            'closed' => '<span class="badge bg-secondary">Fermé</span>'
        ];

        return $badges[$this->status] ?? '<span class="badge bg-secondary">Inconnu</span>';
    }

    public function getPriorityBadgeAttribute(): string
    {
        $badges = [
            'low' => '<span class="badge bg-info">Basse</span>',
            'medium' => '<span class="badge bg-warning">Moyenne</span>',
            'high' => '<span class="badge bg-danger">Haute</span>',
            'urgent' => '<span class="badge bg-danger">Urgent</span>'
        ];

        return $badges[$this->priority] ?? '<span class="badge bg-secondary">Inconnu</span>';
    }

    public function getCategoryLabelAttribute(): string
    {
        $categories = [
            'general' => 'Général',
            'technical' => 'Technique',
            'billing' => 'Facturation',
            'account' => 'Compte',
            'training' => 'Formation',
            'funding' => 'Financement',
            'document' => 'Document',
            'other' => 'Autre'
        ];

        return $categories[$this->category] ?? $this->category;
    }

    /**
     * Nombre de messages non lus par l'utilisateur
     */
    public function getUnreadCountAttribute(): int
    {
        return $this->messages()
            ->where('is_admin', true) // Messages des admins uniquement
            ->where('read', false)
            ->count();
    }

    // ============================================
    // SCOPES (déplacés de SupportMessage)
    // ============================================

    public function scopeOpen($query)
    {
        return $query->where('status', 'open');
    }

    public function scopeInProgress($query)
    {
        return $query->where('status', 'in_progress');
    }

    public function scopeResolved($query)
    {
        return $query->where('status', 'resolved');
    }

    public function scopeClosed($query)
    {
        return $query->where('status', 'closed');
    }

    public function scopeByUser($query, $userId)
    {
        return $query->where('user_id', $userId);
    }

    public function scopeByPriority($query, $priority)
    {
        return $query->where('priority', $priority);
    }

    public function scopeByCategory($query, $category)
    {
        return $query->where('category', $category);
    }

    // ============================================
    // MÉTHODES UTILITAIRES
    // ============================================

    public function isOpen(): bool
    {
        return $this->status === 'open';
    }

    public function isInProgress(): bool
    {
        return $this->status === 'in_progress';
    }

    public function isResolved(): bool
    {
        return $this->status === 'resolved';
    }

    public function isClosed(): bool
    {
        return $this->status === 'closed';
    }

    public function canBeReplied(): bool
    {
        return !$this->isClosed() && !$this->isResolved();
    }

    public function addMessage(string $message, int $userId, bool $isAdmin = false, array $attachments = []): SupportMessage
    {
        return $this->messages()->create([
            'message' => $message,
            'user_id' => $userId,
            'is_admin' => $isAdmin,
            'attachments' => $attachments ?: null,
        ]);
    }

    public function markAsInProgress(?int $assigneeId = null): void
    {
        $this->update([
            'status' => 'in_progress',
            'assigned_to' => $assigneeId
        ]);
    }

    public function markAsResolved(): void
    {
        $this->update([
            'status' => 'resolved',
            'resolved_at' => now()
        ]);
    }

    public function markAsClosed(): void
    {
        $this->update([
            'status' => 'closed',
            'closed_at' => now()
        ]);
    }

    public function reopen(): void
    {
        $this->update([
            'status' => 'open',
            'resolved_at' => null,
            'closed_at' => null
        ]);
    }

    public function hasUnreadMessages(int $userId): bool
    {
        return $this->messages()
            ->where('user_id', '!=', $userId)
            ->where('is_admin', true)
            ->where('read', false)
            ->exists();
    }

    public function markMessagesAsRead(int $userId): void
    {
        $this->messages()
            ->where('user_id', '!=', $userId)
            ->where('is_admin', true)
            ->where('read', false)
            ->update([
                'read' => true,
                'read_at' => now()
            ]);
    }

    /**
     * Générer un numéro de ticket unique
     */
    public static function generateTicketNumber(): string
    {
        $year = date('Y');
        $month = date('m');
        $count = self::whereYear('created_at', $year)->count() + 1;

        return 'BHDM-' . $year . $month . '-' . str_pad($count, 4, '0', STR_PAD_LEFT);
    }

    // Dans SupportTicket.php
    public function getStatusLabelAttribute(): string
    {
        $labels = [
            'open' => 'Ouvert',
            'in_progress' => 'En cours',
            'resolved' => 'Résolu',
            'closed' => 'Fermé',
        ];
        return $labels[$this->status] ?? $this->status;
    }

    // Optionnel : pourcentage de progression basé sur les messages
    public function getProgressPercentageAttribute(): int
    {
        // Exemple : 100% si résolu, sinon en fonction du nombre de messages
        if ($this->isResolved() || $this->isClosed()) {
            return 100;
        }
        // Sinon, progression arbitraire : 50% si en cours, 25% si ouvert
        return $this->isInProgress() ? 50 : 25;
    }
}
