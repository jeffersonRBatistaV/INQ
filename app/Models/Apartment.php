<?php

namespace App\Models;

use App\Models\Concerns\BelongsToLandlord;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Apartment extends Model
{
    use BelongsToLandlord;

    protected $fillable = [
        'landlord_id', 'building_id', 'code', 'label', 'address',
        'base_rent', 'status', 'notes',
    ];

    protected function casts(): array
    {
        return ['base_rent' => 'decimal:2'];
    }

    public function landlord(): BelongsTo
    {
        return $this->belongsTo(User::class, 'landlord_id');
    }

    public function building(): BelongsTo
    {
        return $this->belongsTo(Building::class);
    }

    public function leases(): HasMany
    {
        return $this->hasMany(Lease::class);
    }

    public function activeLease(): ?Lease
    {
        return $this->leases()->where('status', 'active')->latest('start_date')->first();
    }

    public function displayName(): string
    {
        return $this->label ? "{$this->code} - {$this->label}" : $this->code;
    }
}
