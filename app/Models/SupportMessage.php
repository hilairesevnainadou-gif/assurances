<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SupportMessage extends Model
{
    use HasFactory;

    protected $fillable = [
        'ticket_id',
        'user_id',
        'message',
        'is_admin',
        'attachments',
        'read',
        'read_at'
    ];

    protected $casts = [
        'is_admin' => 'boolean',
        'read' => 'boolean',
        'attachments' => 'array',
        'read_at' => 'datetime'
    ];

    protected $attributes = [
        'is_admin' => false,
        'read' => false
    ];

    // ============================================
    // RELATIONS
    // ============================================

    public function ticket(): BelongsTo
    {
        return $this->belongsTo(SupportTicket::class, 'ticket_id');
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    // ============================================
    // ACCESSORS
    // ============================================

    public function getFormattedMessageAttribute(): string
    {
        return nl2br(e($this->message));
    }

    public function getAttachmentCountAttribute(): int
    {
        return $this->attachments ? count($this->attachments) : 0;
    }

    // ============================================
    // MÉTHODES UTILITAIRES
    // ============================================

    public function markAsRead(): void
    {
        $this->update([
            'read' => true,
            'read_at' => now()
        ]);
    }

    public function markAsUnread(): void
    {
        $this->update([
            'read' => false,
            'read_at' => null
        ]);
    }

    public function hasAttachments(): bool
    {
        return !empty($this->attachments);
    }

    public function getAttachmentsList(): array
    {
        return $this->attachments ?? [];
    }

    public function addAttachment(array $attachment): void
    {
        $attachments = $this->attachments ?? [];
        $attachments[] = $attachment;

        $this->update(['attachments' => $attachments]);
    }

    public function removeAttachment(int $index): bool
    {
        $attachments = $this->attachments ?? [];

        if (isset($attachments[$index])) {
            unset($attachments[$index]);
            $attachments = array_values($attachments); // Réindexer le tableau

            $this->update(['attachments' => $attachments]);
            return true;
        }

        return false;
    }

    /**
     * Vérifier si le message est un message admin
     */
    public function isFromAdmin(): bool
    {
        return $this->is_admin === true;
    }

    /**
     * Vérifier si le message est lu
     */
    public function isRead(): bool
    {
        return $this->read === true;
    }
}
