<?php

namespace App\Models;

use App\Enums\TicketStatus;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\{BelongsTo, HasMany};
use Carbon\Carbon;

class Ticket extends Model
{
    protected $table = 'tickets';
    protected $fillable = ['order_item_id','user_id','code','qr_code_hash','pdf_url','status','redeemed_at'];
    protected $casts = ['status' => TicketStatus::class, 'redeemed_at' => 'datetime'];
    protected $dates = ['deleted_at'];

    public function orderItem(): BelongsTo
    {
        return $this->belongsTo(OrderItem::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function checkins(): HasMany
    {
        return $this->hasMany(TicketCheckin::class);
    }
    
    public function getId(): int
    {
        return (int) $this->attributes['id'];
    }
    
    public function setId(int $value): void
    {
        $this->attributes['id'] = $value;
    }
    
    public function getCode(): string
    {
        return (string) $this->attributes['code'];
    }

    public function setCode(string $value): void
    {
        $this->attributes['code'] = $value;
    }
    
    public function getOrderItemId(): int
    {
        return (int) $this->attributes['order_item_id'];
    }
    
    public function setOrderItemId(int $value): void
    {
        $this->attributes['order_item_id'] = $value;
    }
    
    public function getUserId(): int
    {
        return (int) $this->attributes['user_id'];
    }
    
    public function setUserId(int $value): void
    {
        $this->attributes['user_id'] = $value;
    }
    
    public function getQrCodeHash(): string
    {
        return (string) $this->attributes['qr_code_hash'];
    }
    
    public function setQrCodeHash(string $value): void
    {
        $this->attributes['qr_code_hash'] = $value;
    }
    
    public function getPdfUrl(): string
    {
        return (string) $this->attributes['pdf_url'];
    }
    
    public function setPdfUrl(string $value): void
    {
        $this->attributes['pdf_url'] = $value;
    }
    
    public function getStatus(): TicketStatus
    {
        return TicketStatus::from($this->attributes['status']);
    }
    
    public function setStatus(TicketStatus $value): void
    {
        $this->attributes['status'] = $value->value;
    }
    
    public function getRedeemedAt(): ?Carbon
    {
        return $this->attributes['redeemed_at'] ? Carbon::parse($this->attributes['redeemed_at']) : null;
    }
    
    public function setRedeemedAt(?Carbon $value): void
    {
        $this->attributes['redeemed_at'] = $value;
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