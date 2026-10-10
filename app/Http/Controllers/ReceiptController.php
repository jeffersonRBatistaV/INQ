<?php

namespace App\Http\Controllers;

use App\Models\Payment;
use Barryvdh\DomPDF\Facade\Pdf;

class ReceiptController extends Controller
{
    public function download(Payment $payment)
    {
        $payment->load(['tenant', 'lease.apartment', 'allocations.charge', 'receipt', 'landlord']);

        abort_if($payment->receipt === null, 404);

        $pdf = Pdf::loadView('receipts.pdf', [
            'payment' => $payment,
            'receipt' => $payment->receipt,
            'landlord' => $payment->landlord,
        ]);

        return $pdf->download('recibo-'.$payment->receipt->formattedNumber().'.pdf');
    }
}
