<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Carbon\Carbon;

class TicketCheckin extends Model
{
    protected $table = 'ticket_checkins';
    protected $fillable = ['ticket_id','scanned_by_user_id','scanned_at','device','location'];
    protected $casts = [
        'scanned_at' => 'datetime',
    ];

    public function ticket(): BelongsTo
    {
        return $this->belongsTo(Ticket::class);
    }

    public function scannedByUser(): BelongsTo
    {
        return $this->belongsTo(User::class, 'scanned_by_user_id');
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

    public function getTicketId(): int
    {
        return (int) $this->attributes['ticket_id'];
    }

    public function setTicketId(int $value): void
    {
        $this->attributes['ticket_id'] = $value;
    }

    public function getScannedByUserId(): ?int
    {
        return $this->attributes['scanned_by_user_id'] ? (int) $this->attributes['scanned_by_user_id'] : null;
    }

    public function setScannedByUserId(?int $value): void
    {
        $this->attributes['scanned_by_user_id'] = $value;
    }

    public function getScannedAt(): Carbon
    {
        return Carbon::parse($this->attributes['scanned_at']);
    }

    public function setScannedAt(Carbon $value): void
    {
        $this->attributes['scanned_at'] = $value;
    }

    public function getDevice(): ?string
    {
        return $this->attributes['device'];
    }

    public function setDevice(?string $value): void
    {
        $this->attributes['device'] = $value;
    }

    public function getLocation(): ?string
    {
        return $this->attributes['location'];
    }

    public function setLocation(?string $value): void
    {
        $this->attributes['location'] = $value;
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