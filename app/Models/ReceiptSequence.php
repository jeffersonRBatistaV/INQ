<?php

namespace App\Models;

use App\Models\Concerns\BelongsToLandlord;
use Illuminate\Database\Eloquent\Model;

class ReceiptSequence extends Model
{
    use BelongsToLandlord;

    protected $fillable = ['landlord_id', 'last_number'];
}
