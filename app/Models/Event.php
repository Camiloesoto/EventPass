<?php

namespace App\Models;

use App\Enums\EventStatus;
use App\Enums\OrderStatus;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Relations\{BelongsTo, HasMany};
use Carbon\Carbon;

class Event extends Model
{
    protected $table = 'events';
    protected $fillable = ['venue_id','name','description','start_time','end_time','capacity','status'];
    protected $casts = ['start_time' => 'datetime', 'end_time' => 'datetime', 'status' => EventStatus::class];
    protected $dates = ['deleted_at'];

    /* =====================
       Relaciones (UML)
       ===================== */
    public function venue(): BelongsTo
    {
        return $this->belongsTo(Venue::class);
    }

    public function ticketTypes(): HasMany
    {
        return $this->hasMany(TicketType::class);
    }

    public function waitlistEntries(): HasMany
    {
        return $this->hasMany(WaitlistEntry::class);
    }

    /* =====================
       Métodos de negocio (UML)
       ===================== */
    public function availableCapacity(): int
    {
        $total = (int) $this->ticketTypes()->sum('quantity');
        $sold = (int) DB::table('order_items')
            ->join('orders', 'orders.id', '=', 'order_items.order_id')
            ->join('ticket_types', 'ticket_types.id', '=', 'order_items.ticket_type_id')
            ->where('ticket_types.event_id', $this->id)
            ->where('orders.status', OrderStatus::paid->value)
            ->sum('order_items.quantity');

        return max(0, $total - $sold);
    }

    public function getAvailableCapacityAttribute(): int
    {
        return $this->availableCapacity();
    }

    public function isSoldOut(): bool
    {
        return $this->availableCapacity() <= 0;
    }

    public function salesReport()
    {
        return (object)[
            'event_id' => $this->id,
            'sold'     => (int) $this->ticketTypes()->sum('quantity'),
            'revenue'  => (float) $this->ticketTypes()->sum('price'),
        ];
    }

    protected function availableCapacityText(): Attribute
    {
        return Attribute::make(get: fn() => $this->availableCapacity().' seats');
    }

    /* =====================
       Getters/Setters explícitos (para la rúbrica)
       ===================== */
    public function getName(): string
    {
        return (string) $this->attributes['name'];
    }

    public function setName(string $value): void
    {
        $this->attributes['name'] = $value;
    }

    public function getDescription(): string
    {
        return (string) ($this->attributes['description'] ?? '');
    }

    public function setDescription(string $value): void
    {
        $this->attributes['description'] = $value;
    }

    public function getStartTime(): Carbon
    {
        return Carbon::parse($this->attributes['start_time']);
    }

    public function setStartTime(Carbon $value): void
    {
        $this->attributes['start_time'] = $value;
    }

    public function getEndTime(): Carbon
    {
        return Carbon::parse($this->attributes['end_time']);
    }

    public function setEndTime(Carbon $value): void
    {
        $this->attributes['end_time'] = $value;
    }

    public function getCapacity(): int
    {
        return (int) $this->attributes['capacity'];
    }

    public function setCapacity(int $value): void
    {
        $this->attributes['capacity'] = $value;
    }

    public function getStatus()
    {
        return $this->attributes['status'];
    }

    public function setStatus($value): void
    {
        $this->attributes['status'] = $value;
    }

    public function getAvailableCapacityText(): string
    {
        return $this->availableCapacity().' seats';
    }
}
