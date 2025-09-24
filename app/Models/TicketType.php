<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class TicketType extends Model
{
    protected $table = 'ticket_types';
    protected $fillable = ['event_id','name','price','quantity'];
    protected $casts = ['price' => 'decimal:2'];

    public function event(): BelongsTo
    {
        return $this->belongsTo(Event::class);
    }
}
