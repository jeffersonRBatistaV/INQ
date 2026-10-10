<?php

namespace App\Models;

use Database\Factories\UserFactory;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable implements MustVerifyEmail
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, Notifiable;

    /**
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'role',
        'phone',
        'mora_type',
        'mora_value',
        'grace_days',
        'currency',
    ];

    /**
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'suspended_at' => 'datetime',
            'password' => 'hashed',
            'mora_value' => 'decimal:2',
        ];
    }

    public function isSuperadmin(): bool
    {
        return $this->role === 'superadmin';
    }

    public function isSuspended(): bool
    {
        return $this->suspended_at !== null;
    }

    public function buildings(): HasMany
    {
        return $this->hasMany(Building::class, 'landlord_id');
    }

    public function apartments(): HasMany
    {
        return $this->hasMany(Apartment::class, 'landlord_id');
    }

    public function tenants(): HasMany
    {
        return $this->hasMany(Tenant::class, 'landlord_id');
    }

    public function terms(): HasMany
    {
        return $this->hasMany(Lease::class, 'landlord_id');
    }
}
