<?php

namespace App\Models;

use App\Models\Concerns\BelongsToLandlord;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Charge extends Model
{
    use BelongsToLandlord;

    public const TYPE_RENT = 'rent';
    public const TYPE_SERVICE = 'service';
    public const TYPE_MORA = 'mora';
    public const TYPE_OTHER = 'other';

    public const STATUS_PENDING = 'pending';
    public const STATUS_PARTIAL = 'partial';
    public const STATUS_PAID = 'paid';

    protected $fillable = [
        'landlord_id', 'lease_id', 'apartment_id', 'tenant_id',
        'period', 'type', 'concept', 'amount', 'paid_amount',
        'due_date', 'status', 'notes',
    ];

    protected function casts(): array
    {
        return [
            'amount' => 'decimal:2',
            'paid_amount' => 'decimal:2',
            'due_date' => 'date',
        ];
    }

    public function landlord(): BelongsTo
    {
        return $this->belongsTo(User::class, 'landlord_id');
    }

    public function lease(): BelongsTo
    {
        return $this->belongsTo(Lease::class);
    }

    public function apartment(): BelongsTo
    {
        return $this->belongsTo(Apartment::class);
    }

    public function tenant(): BelongsTo
    {
        return $this->belongsTo(Tenant::class);
    }

    public function allocations(): HasMany
    {
        return $this->hasMany(PaymentAllocation::class);
    }

    public function balance(): float
    {
        return round((float) $this->amount - (float) $this->paid_amount, 2);
    }

    public function isOverdue(): bool
    {
        return $this->status !== self::STATUS_PAID
            && $this->due_date->isPast()
            && $this->balance() > 0;
    }

    public function refreshStatus(): void
    {
        $paid = (float) $this->paid_amount;

        $this->status = match (true) {
            $paid <= 0 => self::STATUS_PENDING,
            $paid >= (float) $this->amount => self::STATUS_PAID,
            default => self::STATUS_PARTIAL,
        };

        $this->save();
    }
}
