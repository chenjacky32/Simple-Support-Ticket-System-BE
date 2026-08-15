<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Concerns\HasUlids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

#[Fillable(['ticket_code', 'title', 'description', 'attachment_path', 'status', 'user_id', 'resolved_at'])]
class Ticket extends Model
{
    use HasFactory, HasUlids;

    /**
     * Ticket Status Enums
     */
    public const STATUS_OPENED = "OPENED";
    public const STATUS_INPROGRESS = "INPROGRESS";
    public const STATUS_RESOLVED = "RESOLVED";

    protected $casts = [
        'resolved_at' => 'datetime',
    ];

    /**
     * Get the user that owns the ticket.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    /**
     * Get the responses for the ticket.
     */
    public function responses(): HasMany
    {
        return $this->hasMany(TicketResponse::class, 'ticket_id');
    }

    /**
     * Get the status logs for the ticket.
     */
    public function statusLogs(): HasMany
    {
        return $this->hasMany(TicketStatusLog::class, 'ticket_id');
    }
}
