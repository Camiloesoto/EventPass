<?php

namespace App\Models;

use App\Enums\RefundStatus;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Carbon\Carbon;

class Refund extends Model
{
    protected $table = 'refunds';
    protected $fillable = ['payment_id','provider_refund_id','amount','status','refunded_at'];
    protected $casts = [
        'amount' => 'decimal:2',
        'status' => RefundStatus::class,
        'refunded_at' => 'datetime',
    ];

    public function payment(): BelongsTo
    {
        return $this->belongsTo(Payment::class);
    }

    /* ===================== Explicit Getters and Setters ===================== */

    public function getId(): int
    {
        return (int) $this->attributes['id'];
    }

    public function setId(int $value): void
    {
        $this->attributes['id'] = $value;
    }

    public function getPaymentId(): int
    {
        return (int) $this->attributes['payment_id'];
    }

    public function setPaymentId(int $value): void
    {
        $this->attributes['payment_id'] = $value;
    }

    public function getProviderRefundId(): string
    {
        return (string) $this->attributes['provider_refund_id'];
    }

    public function setProviderRefundId(string $value): void
    {
        $this->attributes['provider_refund_id'] = $value;
    }

    public function getAmount(): float
    {
        return (float) $this->attributes['amount'];
    }

    public function setAmount(float $value): void
    {
        $this->attributes['amount'] = $value;
    }

    public function getStatus(): RefundStatus
    {
        return RefundStatus::from($this->attributes['status']);
    }

    public function setStatus(RefundStatus $value): void
    {
        $this->attributes['status'] = $value->value;
    }

    public function getRefundedAt(): ?Carbon
    {
        return $this->attributes['refunded_at'] ? Carbon::parse($this->attributes['refunded_at']) : null;
    }

    public function setRefundedAt(?Carbon $value): void
    {
        $this->attributes['refunded_at'] = $value;
    }

    public function getCreatedAt(): Carbon
    {
        return Carbon::parse($this->attributes['created_at']);
    }

    public function getUpdatedAt(): Carbon
    {
        return Carbon::parse($this->attributes['updated_at']);
    }
}