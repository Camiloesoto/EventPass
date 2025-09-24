<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class TicketCheckin extends Model
{
    protected $table = 'ticket_checkins';
    protected $fillable = ['ticket_id','scanned_by_user_id','scanned_at','device','location'];
    protected $casts = ['scanned_at' => 'datetime'];

    public function ticket(): BelongsTo
    {
        return $this->belongsTo(Ticket::class);
    }

    public function scannedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'scanned_by_user_id');
    }
}
