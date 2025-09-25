<?php

namespace App\Models;

use App\Enums\WaitlistStatus;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Notifications\Notifiable;
use Carbon\Carbon;

class WaitlistEntry extends Model
{
    use Notifiable;

    protected $table = 'waitlist_entries';
    protected $fillable = ['user_id','event_id','status','notified_at'];
    protected $casts = ['status' => WaitlistStatus::class, 'notified_at' => 'datetime'];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function event(): BelongsTo
    {
        return $this->belongsTo(Event::class);
    }

    public function notifyUser(): void
    {
        $this->update(['notified_at' => now()]);
    }

    public function getId(): int
    {
        return (int) $this->attributes['id'];
    }

    public function setId(int $value): void
    {
        $this->attributes['id'] = $value;
    }

    public function getStatus(): WaitlistStatus
    {
        return WaitlistStatus::from($this->attributes['status']);
    }

    public function setStatus(WaitlistStatus $value): void
    {
        $this->attributes['status'] = $value->value;
    }

    public function getNotifiedAt(): ?Carbon
    {
        return $this->attributes['notified_at'] ? Carbon::parse($this->attributes['notified_at']) : null;
    }

    public function setNotifiedAt(?Carbon $value): void
    {
        $this->attributes['notified_at'] = $value;
    }

    public function getUserId(): int
    {
        return (int) $this->attributes['user_id'];
    }

    public function setUserId(int $value): void
    {
        $this->attributes['user_id'] = $value;
    }

    public function getEventId(): int
    {
        return (int) $this->attributes['event_id'];
    }

    public function setEventId(int $value): void
    {
        $this->attributes['event_id'] = $value;
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
