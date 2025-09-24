<?php

namespace App\Models;

use App\Enums\OrderStatus;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\{BelongsTo, HasMany};
use Carbon\Carbon;

class OrderItem extends Model
{
    protected $table = 'order_items';
    protected $fillable = ['order_id','ticket_type_id','quantity','unit_price'];
    protected $casts = [
        'unit_price' => 'decimal:2',
    ];

    public function order(): BelongsTo
    {
        return $this->belongsTo(Order::class);
    }

    public function ticketType(): BelongsTo
    {
        return $this->belongsTo(TicketType::class);
    }

    public function tickets(): HasMany
    {
        return $this->hasMany(Ticket::class);
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

    public function getTicketTypeId(): int
    {
        return (int) $this->attributes['ticket_type_id'];
    }

    public function setTicketTypeId(int $value): void
    {
        $this->attributes['ticket_type_id'] = $value;
    }

    public function getQuantity(): int
    {
        return (int) $this->attributes['quantity'];
    }

    public function setQuantity(int $value): void
    {
        $this->attributes['quantity'] = $value;
    }

    public function getUnitPrice(): float
    {
        return (float) $this->attributes['unit_price'];
    }

    public function setUnitPrice(float $value): void
    {
        $this->attributes['unit_price'] = $value;
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