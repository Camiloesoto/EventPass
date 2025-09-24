<?php

namespace App\Models;

use App\Enums\WaitlistStatus;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Notifications\Notifiable;

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

    /* UML: notifyUser(): void */
    public function notifyUser(): void
    {
        // Implementa tu notificación real (Notification, Mail, etc.)
        // $this->notify(new WaitlistSpotsAvailableNotification($this->event));
        $this->update(['notified_at' => now()]);
    }

    /* =====================
       Getters/Setters explícitos (para la rúbrica)
       ===================== */
    public function getStatus()
    {
        return $this->attributes['status'] ?? null;
    }

    public function setStatus($value): void
    {
        $this->attributes['status'] = $value;
    }

    public function getNotifiedAt()
    {
        return $this->attributes['notified_at'];
    }

    public function setNotifiedAt($value): void
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
}
