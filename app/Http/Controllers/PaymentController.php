<?php

namespace App\Http\Controllers;

use App\Models\Payment;
use App\Models\Tenant;
use App\Services\PaymentRecorder;
use App\Services\ReceiptService;
use Illuminate\Http\Request;

class PaymentController extends Controller
{
    public function __construct(
        private PaymentRecorder $recorder,
        private ReceiptService $receipts,
    ) {
    }

    public function index(Request $request)
    {
        $payments = Payment::with(['tenant', 'lease.apartment', 'receipt'])
            ->orderByDesc('paid_at')
            ->orderByDesc('id')
            ->paginate(25);

        return view('payments.index', compact('payments'));
    }

    public function create(Request $request)
    {
        $tenants = Tenant::with(['leases' => fn ($q) => $q->where('status', 'active')])->orderBy('name')->get();

        return view('payments.create', [
            'tenants' => $tenants,
            'selectedTenant' => $request->query('tenant_id'),
        ]);
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'tenant_id' => ['required', 'exists:tenants,id'],
            'lease_id' => ['nullable', 'exists:leases,id'],
            'amount' => ['required', 'numeric', 'min:0.01'],
            'method' => ['required', 'in:cash,transfer,other'],
            'reference' => ['nullable', 'string', 'max:120'],
            'paid_at' => ['required', 'date'],
            'notes' => ['nullable', 'string'],
        ]);

        $data['landlord_id'] = $request->user()->id;

        $payment = $this->recorder->record($data);
        $this->receipts->issue($payment);

        return redirect()->route('payments.show', $payment)->with('status', 'Pago registrado y recibo emitido.');
    }

    public function show(Payment $payment)
    {
        $payment->load(['tenant', 'lease.apartment', 'allocations.charge', 'receipt']);

        return view('payments.show', compact('payment'));
    }
}
