<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class TicketCheckin extends Model
{
    use HasFactory;

    protected $fillable = ['ticket_id','scanned_by_user_id','scanned_at','device','location'];
    protected $casts = ['scanned_at'=>'datetime'];

    public function ticket()  { return $this->belongsTo(Ticket::class); }
    public function scanner() { return $this->belongsTo(User::class,'scanned_by_user_id'); }
}
