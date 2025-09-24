<?php

namespace App\Models;

use App\Enums\PaymentStatus;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\{BelongsTo, HasMany};
use Carbon\Carbon;

class Payment extends Model
{
    protected $table = 'payments';
    protected $fillable = ['order_id','provider','provider_payment_id','currency','amount','status','paid_at'];
    protected $casts = [
        'amount' => 'decimal:2',
        'status' => PaymentStatus::class,
        'paid_at' => 'datetime',
    ];

    public function order(): BelongsTo
    {
        return $this->belongsTo(Order::class);
    }

    public function refunds(): HasMany
    {
        return $this->hasMany(Refund::class);
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

    public function getOrderId(): int
    {
        return (int) $this->attributes['order_id'];
    }

    public function setOrderId(int $value): void
    {
        $this->attributes['order_id'] = $value;
    }

    public function getProvider(): string
    {
        return (string) $this->attributes['provider'];
    }

    public function setProvider(string $value): void
    {
        $this->attributes['provider'] = $value;
    }

    public function getProviderPaymentId(): string
    {
        return (string) $this->attributes['provider_payment_id'];
    }

    public function setProviderPaymentId(string $value): void
    {
        $this->attributes['provider_payment_id'] = $value;
    }

    public function getCurrency(): string
    {
        return (string) $this->attributes['currency'];
    }

    public function setCurrency(string $value): void
    {
        $this->attributes['currency'] = $value;
    }

    public function getAmount(): float
    {
        return (float) $this->attributes['amount'];
    }

    public function setAmount(float $value): void
    {
        $this->attributes['amount'] = $value;
    }

    public function getStatus(): PaymentStatus
    {
        return PaymentStatus::from($this->attributes['status']);
    }

    public function setStatus(PaymentStatus $value): void
    {
        $this->attributes['status'] = $value->value;
    }

    public function getPaidAt(): ?Carbon
    {
        return $this->attributes['paid_at'] ? Carbon::parse($this->attributes['paid_at']) : null;
    }

    public function setPaidAt(?Carbon $value): void
    {
        $this->attributes['paid_at'] = $value;
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