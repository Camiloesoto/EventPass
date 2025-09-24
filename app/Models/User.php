<?php
namespace App\Models;

use Illuminate\Auth\Authenticatable;
use Illuminate\Contracts\Auth\Authenticatable as AuthenticatableContract;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Relations\{HasMany, MorphMany};
use Carbon\Carbon;

class User extends Model implements AuthenticatableContract
{
    use Authenticatable;

    protected $table = 'users';
    protected $fillable = ['name','email','password','is_admin'];
    protected $hidden = ['password','remember_token'];
    protected $casts = [
        'email_verified_at' => 'datetime',
        'is_admin' => 'boolean'
    ];
    protected $dates = ['deleted_at'];

    protected function firstName(): Attribute
    {
        return Attribute::make(get: fn() => explode(' ', $this->name)[0] ?? $this->name);
    }

    public function orders(): HasMany
    {
        return $this->hasMany(Order::class);
    }

    public function tickets(): HasMany
    {
        return $this->hasMany(Ticket::class);
    }

    public function waitlistEntries(): HasMany
    {
        return $this->hasMany(WaitlistEntry::class);
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
    
    public function getName(): string
    {
        return (string) $this->attributes['name'];
    }
    
    public function setName(string $value): void
    {
        $this->attributes['name'] = $value;
    }
    
    public function getEmail(): string
    {
        return (string) $this->attributes['email'];
    }
    
    public function setEmail(string $value): void
    {
        $this->attributes['email'] = $value;
    }
    
    public function getEmailVerifiedAt(): ?Carbon
    {
        return $this->attributes['email_verified_at'] ? Carbon::parse($this->attributes['email_verified_at']) : null;
    }
    
    public function setEmailVerifiedAt(?Carbon $value): void
    {
        $this->attributes['email_verified_at'] = $value;
    }
    
    public function getPassword(): string
    {
        return (string) $this->attributes['password'];
    }
    
    public function setPassword(string $value): void
    {
        $this->attributes['password'] = $value;
    }
    
    public function getRememberToken(): ?string
    {
        return $this->attributes['remember_token'];
    }
    
    public function getDeletedAt(): ?Carbon
    {
        return $this->attributes['deleted_at'] ? Carbon::parse($this->attributes['deleted_at']) : null;
    }
    
    public function setDeletedAt(?Carbon $value): void
    {
        $this->attributes['deleted_at'] = $value;
    }
    
    public function getIsAdmin(): bool
    {
        return (bool) $this->attributes['is_admin'];
    }
    
    public function setIsAdmin(bool $value): void
    {
        $this->attributes['is_admin'] = $value;
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
