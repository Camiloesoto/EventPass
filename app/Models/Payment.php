<?php
namespace App\Models;

use App\Enums\PaymentStatus;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\{BelongsTo, HasMany};

class Payment extends Model
{
    protected $table = 'payments';
    protected $fillable = ['order_id','provider','provider_payment_id','amount','currency','status','paid_at'];
    protected $casts = ['amount' => 'decimal:2', 'status' => PaymentStatus::class, 'paid_at' => 'datetime'];

    public function order(): BelongsTo
    {
        return $this->belongsTo(Order::class);
    }

    public function refunds(): HasMany
    {
        return $this->hasMany(Refund::class);
    }
}
