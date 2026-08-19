<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Concerns\HasUlids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[Fillable(['ticket_id', 'message', 'responded_by'])]
class TicketResponse extends Model
{
    use HasFactory, HasUlids;

    /**
     * Get the ticket that owns the response.
     */
    public function ticket(): BelongsTo
    {
        return $this->belongsTo(Ticket::class, 'ticket_id');
    }

    /**
     * Get the user that created the response.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'responded_by');
    }
}
