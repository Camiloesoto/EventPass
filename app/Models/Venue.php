<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Carbon\Carbon;

class Venue extends Model
{
    protected $table = 'venues';
    protected $fillable = ['name','address','timezone'];

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
    
    public function getAddress(): string
    {
        return (string) $this->attributes['address'];
    }
    
    public function setAddress(string $value): void
    {
        $this->attributes['address'] = $value;
    }
    
    public function getTimezone(): string
    {
        return (string) $this->attributes['timezone'];
    }
    
    public function setTimezone(string $value): void
    {
        $this->attributes['timezone'] = $value;
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
    
    public function events(): HasMany
    {
        return $this->hasMany(Event::class);
    }
}
