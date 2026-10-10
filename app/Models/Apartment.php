<?php

namespace App\Models;

use App\Models\Concerns\BelongsToLandlord;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Apartment extends Model
{
    use BelongsToLandlord;

    public const CODE_PREFIX = 'APT-';

    public const CODE_WIDTH = 4;

    protected $fillable = [
        'landlord_id', 'building_id', 'code', 'label', 'address',
        'base_rent', 'status', 'notes',
    ];

    protected static function booted(): void
    {
        static::creating(function (Apartment $apartment) {
            if (empty($apartment->code)) {
                $landlordId = $apartment->landlord_id ?? auth()->id();

                if ($landlordId) {
                    $apartment->code = static::generateCode($landlordId);
                }
            }
        });
    }

    public static function generateCode(int $landlordId): string
    {
        $last = static::withoutGlobalScopes()
            ->where('landlord_id', $landlordId)
            ->where('code', 'like', self::CODE_PREFIX.'%')
            ->orderByDesc('id')
            ->value('code');

        $next = $last && preg_match('/(\d+)$/', $last, $matches) ? (int) $matches[1] + 1 : 1;

        do {
            $code = self::CODE_PREFIX.str_pad((string) $next, self::CODE_WIDTH, '0', STR_PAD_LEFT);
            $next++;
        } while (static::withoutGlobalScopes()
            ->where('landlord_id', $landlordId)
            ->where('code', $code)
            ->exists());

        return $code;
    }

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

    public function activeLease(): HasOne
    {
        return $this->hasOne(Lease::class)->where('status', 'active')->latestOfMany();
    }

    public function isOccupied(): bool
    {
        return $this->status === 'occupied';
    }

    public function syncStatus(): void
    {
        $status = $this->leases()->where('status', 'active')->exists() ? 'occupied' : 'available';

        if ($this->status !== $status) {
            $this->update(['status' => $status]);
        }
    }

    public function displayName(): string
    {
        return $this->label ? "{$this->code} - {$this->label}" : $this->code;
    }
}
