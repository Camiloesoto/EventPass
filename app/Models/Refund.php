<?php
namespace App\Models;

use App\Enums\RefundStatus;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Refund extends Model
{
    protected $table = 'refunds';
    protected $fillable = ['payment_id','provider_refund_id','amount','status','refunded_at'];
    protected $casts = ['amount' => 'decimal:2', 'status' => RefundStatus::class, 'refunded_at' => 'datetime'];

    public function payment(): BelongsTo
    {
        return $this->belongsTo(Payment::class);
    }
}
