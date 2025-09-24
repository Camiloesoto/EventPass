<?php
namespace App\Models;

use App\Enums\OrderStatus;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\{BelongsTo, HasMany};
use Carbon\Carbon;

class Order extends Model
{
    protected $table = 'orders';
    protected $fillable = ['user_id','order_date','status','subtotal_amount','discount_amount','total_amount'];
    protected $casts = [
        'order_date' => 'datetime',
        'status' => OrderStatus::class,
        'subtotal_amount' => 'decimal:2',
        'discount_amount' => 'decimal:2',
        'total_amount' => 'decimal:2',
    ];
    protected $dates = ['deleted_at'];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function items(): HasMany
    {
        return $this->hasMany(OrderItem::class);
    }

    public function payments(): HasMany
    {
        return $this->hasMany(Payment::class);
    }

    public function calculateTotals(): float
    {
        $subtotal = (float) ($this->items()->selectRaw('SUM(quantity * unit_price) as s')->value('s') ?? 0.0);
        $this->setSubtotalAmount($subtotal);
        $this->setTotalAmount(max(0, $subtotal - $this->getDiscountAmount()));

        return $this->getTotalAmount();
    }

    public function generateTickets(): array
    {
        $tickets = [];

        foreach ($this->items as $item) {
            for ($i = 0; $i < $item->quantity; $i++) {
                $tickets[] = $item->tickets()->create([
                    'user_id' => $this->user_id,
                    'qr_code_hash' => hash('sha256', uniqid((string) $this->id, true)),
                    'pdf_url' => '#',
                    'status' => \App\Enums\TicketStatus::issued->value,
                ]);
            }
        }

        return $tickets;
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
    
    public function getUserId(): int
    {
        return (int) $this->attributes['user_id'];
    }
    
    public function setUserId(int $value): void
    {
        $this->attributes['user_id'] = $value;
    }
    
    public function getOrderDate(): Carbon
    {
        return Carbon::parse($this->attributes['order_date']);
    }
    
    public function setOrderDate(Carbon $value): void
    {
        $this->attributes['order_date'] = $value;
    }
    
    public function getStatus(): OrderStatus
    {
        return OrderStatus::from($this->attributes['status']);
    }
    
    public function setStatus(OrderStatus $value): void
    {
        $this->attributes['status'] = $value->value;
    }
    
    public function getSubtotalAmount(): float
    {
        return (float) $this->attributes['subtotal_amount'];
    }
    
    public function setSubtotalAmount(float $value): void
    {
        $this->attributes['subtotal_amount'] = $value;
    }
    
    public function getDiscountAmount(): float
    {
        return (float) $this->attributes['discount_amount'];
    }
    
    public function setDiscountAmount(float $value): void
    {
        $this->attributes['discount_amount'] = $value;
    }
    
    public function getTotalAmount(): float
    {
        return (float) $this->attributes['total_amount'];
    }
    
    public function setTotalAmount(float $value): void
    {
        $this->attributes['total_amount'] = $value;
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
