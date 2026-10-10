<?php

namespace App\Http\Controllers;

use App\Models\Apartment;
use App\Models\Expense;
use Illuminate\Http\Request;

class ExpenseController extends Controller
{
    public function index()
    {
        $expenses = Expense::with('apartment')->orderByDesc('expense_date')->paginate(25);
        $total = (float) Expense::sum('amount');

        return view('expenses.index', compact('expenses', 'total'));
    }

    public function create()
    {
        $apartments = Apartment::orderBy('code')->get();

        return view('expenses.create', compact('apartments'));
    }

    public function store(Request $request)
    {
        Expense::create($this->validated($request));

        return redirect()->route('expenses.index')->with('status', 'Gasto registrado.');
    }

    public function edit(Expense $expense)
    {
        $apartments = Apartment::orderBy('code')->get();

        return view('expenses.edit', compact('expense', 'apartments'));
    }

    public function update(Request $request, Expense $expense)
    {
        $expense->update($this->validated($request));

        return redirect()->route('expenses.index')->with('status', 'Gasto actualizado.');
    }

    public function destroy(Expense $expense)
    {
        $expense->delete();

        return redirect()->route('expenses.index')->with('status', 'Gasto eliminado.');
    }

    private function validated(Request $request): array
    {
        return $request->validate([
            'apartment_id' => ['nullable', 'exists:apartments,id'],
            'concept' => ['required', 'string', 'max:160'],
            'category' => ['nullable', 'string', 'max:80'],
            'amount' => ['required', 'numeric', 'min:0'],
            'expense_date' => ['required', 'date'],
            'notes' => ['nullable', 'string'],
        ]);
    }
}
