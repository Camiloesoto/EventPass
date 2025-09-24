<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\{BelongsTo, HasMany};
use Carbon\Carbon;

class TicketType extends Model
{
    protected $table = 'ticket_types';
    protected $fillable = ['event_id','name','quantity','price'];
    protected $casts = [
        'price' => 'decimal:2',
    ];
    protected $dates = ['deleted_at'];

    public function event(): BelongsTo
    {
        return $this->belongsTo(Event::class);
    }

    public function orderItems(): HasMany
    {
        return $this->hasMany(OrderItem::class);
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

    public function getEventId(): int
    {
        return (int) $this->attributes['event_id'];
    }

    public function setEventId(int $value): void
    {
        $this->attributes['event_id'] = $value;
    }

    public function getName(): string
    {
        return (string) $this->attributes['name'];
    }

    public function setName(string $value): void
    {
        $this->attributes['name'] = $value;
    }

    public function getQuantity(): int
    {
        return (int) $this->attributes['quantity'];
    }

    public function setQuantity(int $value): void
    {
        $this->attributes['quantity'] = $value;
    }

    public function getPrice(): float
    {
        return (float) $this->attributes['price'];
    }

    public function setPrice(float $value): void
    {
        $this->attributes['price'] = $value;
    }

    public function getCreatedAt(): Carbon
    {
        return Carbon::parse($this->attributes['created_at']);
    }
    
    public function setCreatedAt($value): void
    {
        $this->attributes['created_at'] = $value;
    }
    
    public function getUpdatedAt(): Carbon
    {
        return Carbon::parse($this->attributes['updated_at']);
    }
    
    public function setUpdatedAt($value): void
    {
        $this->attributes['updated_at'] = $value;
    }
}