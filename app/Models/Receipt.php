<?php

namespace App\Models;

use App\Models\Concerns\BelongsToLandlord;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Receipt extends Model
{
    use BelongsToLandlord;

    protected $fillable = ['landlord_id', 'payment_id', 'number', 'total', 'issued_at'];

    protected function casts(): array
    {
        return [
            'total' => 'decimal:2',
            'issued_at' => 'datetime',
        ];
    }

    public function payment(): BelongsTo
    {
        return $this->belongsTo(Payment::class);
    }

    public function formattedNumber(): string
    {
        return str_pad((string) $this->number, 6, '0', STR_PAD_LEFT);
    }
}
