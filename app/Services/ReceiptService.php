<?php

namespace App\Services;

use App\Models\Payment;
use App\Models\Receipt;
use App\Models\ReceiptSequence;
use Illuminate\Support\Facades\DB;

class ReceiptService
{
    /**
     * Create the next sequential receipt for a payment (numbered per landlord).
     */
    public function issue(Payment $payment): Receipt
    {
        return DB::transaction(function () use ($payment) {
            $existing = Receipt::where('payment_id', $payment->id)->first();

            if ($existing) {
                return $existing;
            }

            $sequence = ReceiptSequence::withoutGlobalScopes()
                ->where('landlord_id', $payment->landlord_id)
                ->lockForUpdate()
                ->first();

            if (! $sequence) {
                $sequence = ReceiptSequence::withoutGlobalScopes()->create([
                    'landlord_id' => $payment->landlord_id,
                    'last_number' => 0,
                ]);
                $sequence = ReceiptSequence::withoutGlobalScopes()
                    ->where('id', $sequence->id)
                    ->lockForUpdate()
                    ->first();
            }

            $sequence->last_number = (int) $sequence->last_number + 1;
            $sequence->save();

            return Receipt::create([
                'landlord_id' => $payment->landlord_id,
                'payment_id' => $payment->id,
                'number' => $sequence->last_number,
                'total' => $payment->amount,
                'issued_at' => now(),
            ]);
        });
    }
}
