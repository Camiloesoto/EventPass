<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Support\Str;

class Ticket extends Model
{
    use HasFactory;

    // statuses
    public const STATUS_ISSUED      = 'issued';
    public const STATUS_TRANSFERRED  = 'transferred';
    public const STATUS_REDEEMED     = 'redeemed';
    public const STATUS_CANCELLED    = 'cancelled';

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    protected $fillable = [
        'user_id','code','qr_hash','status','redeemed_at','revoked_at','meta'
    ];

    protected $casts = [
        'meta' => 'array',
        'redeemed_at' => 'datetime',
        'revoked_at'  => 'datetime',
    ];

    protected static function booted(): void
    {
        static::creating(function (Ticket $t) {
            $t->code   ??= Str::ulid()->toBase32();
            $t->qr_hash??= hash('sha256', Str::uuid()->toString().random_bytes(16));
            $t->status ??= self::STATUS_ISSUED;
        });
    }

    public function owner()    { return $this->belongsTo(User::class, 'user_id'); }
    public function checkins() { return $this->hasMany(TicketCheckin::class); }

    public function isRedeemed(): bool { return $this->redeemed_at !== null; }
    public function isActive(): bool   { return $this->status !== self::STATUS_CANCELLED; }
}
