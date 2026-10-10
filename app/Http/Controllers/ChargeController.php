<?php

namespace App\Http\Controllers;

use App\Models\Charge;
use App\Models\Lease;
use App\Services\ChargeGenerator;
use Illuminate\Http\Request;

class ChargeController extends Controller
{
    public function __construct(private ChargeGenerator $generator)
    {
    }

    public function index(Request $request)
    {
        $period = $request->query('period', now()->format('Y-m'));

        $charges = Charge::with(['apartment', 'tenant'])
            ->where('period', $period)
            ->orderBy('apartment_id')
            ->orderByDesc('due_date')
            ->paginate(30)
            ->withQueryString();

        $activeLeases = Lease::with(['apartment', 'tenant'])->where('status', 'active')->orderBy('apartment_id')->get();

        return view('charges.index', compact('charges', 'period', 'activeLeases'));
    }

    public function generate(Request $request)
    {
        $data = $request->validate([
            'period' => ['required', 'date_format:Y-m'],
            'lease_id' => ['required', 'exists:leases,id'],
            'services' => ['nullable', 'array'],
            'services.*.concept' => ['nullable', 'string', 'max:120'],
            'services.*.amount' => ['nullable', 'numeric', 'min:0'],
        ]);

        $lease = Lease::findOrFail($data['lease_id']);

        $services = collect($data['services'] ?? [])
            ->filter(fn ($s) => ! empty($s['concept']) && ($s['amount'] ?? 0) > 0)
            ->values()
            ->all();

        $result = $this->generator->generateForLease($lease, $data['period'], $services);

        $msg = 'Cargos generados: renta '.($result['rent'] ? 'sí' : 'ya existía')
            .', servicios '.$result['services'].', mora '.$result['mora'].'.';

        return redirect()->route('charges.index', ['period' => $data['period']])->with('status', $msg);
    }

    public function destroy(Charge $charge)
    {
        $period = $charge->period;
        $charge->delete();

        return redirect()->route('charges.index', ['period' => $period])->with('status', 'Cargo eliminado.');
    }
}
