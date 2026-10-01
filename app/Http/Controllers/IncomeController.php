<?php

namespace App\Http\Controllers;

use App\Models\Income;
use App\Models\Driver;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class IncomeController extends Controller
{
    private function isOwner()
    {
        return Auth::check()
            && strtoupper(Auth::user()->role ?? '') === 'OWNER';
    }

    public function index(Request $request)
    {
        $query = Income::orderBy('income_date', 'desc')
            ->orderBy('id', 'desc');

        if ($request->filled('search')) {
            $search = trim($request->search);

            $query->where(function ($q) use ($search) {

                $q->where('driver_id', 'like', "%{$search}%")
                    ->orWhere('description', 'like', "%{$search}%")
                    ->orWhere('fees_amount', 'like', "%{$search}%")
                    ->orWhere('gst_amount', 'like', "%{$search}%")
                    ->orWhere('total_amount', 'like', "%{$search}%")
                    ->orWhere('amount', 'like', "%{$search}%");

                if (is_numeric($search)) {
                    $q->orWhere('id', (int) $search);
                }

                $q->orWhereDate('income_date', $search);
            });
        }

        $incomes = $query->get();

        return view('income.index', compact('incomes'));
    }

    public function create()
    {
        $drivers = Driver::orderBy('name')->get();

        return view('income.create', compact('drivers'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'driver_id' => 'required',
            'fees_amount' => 'required|numeric|min:0',
            'gst_amount' => 'nullable|numeric|min:0',
            'fees_payment_mode' => 'required|in:cash,account',
            'gst_payment_mode' => 'required|in:cash,account',
            'income_date' => 'required|date',
            'description' => 'nullable|string|max:255',
        ]);

        $feesAmount = (float) $validated['fees_amount'];
        $gstAmount = (float) ($validated['gst_amount'] ?? 0);
        $totalAmount = $feesAmount + $gstAmount;

        Income::create([
            'driver_id' => $validated['driver_id'],
            'fees_amount' => $feesAmount,
            'gst_amount' => $gstAmount,
            'amount' => $totalAmount,
            'total_amount' => $totalAmount,
            'fees_payment_mode' => $validated['fees_payment_mode'],
            'gst_payment_mode' => $validated['gst_payment_mode'],
            'income_date' => $validated['income_date'],
            'description' => $validated['description'] ?? null,
        ]);

        return redirect()
            ->route('income.index')
            ->with('success', 'Income added successfully.');
    }

    public function edit($id)
    {
        if (!$this->isOwner()) {
            abort(403);
        }

        $income = Income::findOrFail($id);
        $drivers = Driver::orderBy('name')->get();

        return view('income.edit', compact('income', 'drivers'));
    }

    public function update(Request $request, $id)
    {
        if (!$this->isOwner()) {
            abort(403);
        }

        $income = Income::findOrFail($id);

        $validated = $request->validate([
            'driver_id' => 'required',
            'fees_amount' => 'required|numeric|min:0',
            'gst_amount' => 'nullable|numeric|min:0',
            'fees_payment_mode' => 'required|in:cash,account',
            'gst_payment_mode' => 'required|in:cash,account',
            'income_date' => 'required|date',
            'description' => 'nullable|string|max:255',
        ]);

        $feesAmount = (float) $validated['fees_amount'];
        $gstAmount = (float) ($validated['gst_amount'] ?? 0);
        $totalAmount = $feesAmount + $gstAmount;

        $income->update([
            'driver_id' => $validated['driver_id'],
            'fees_amount' => $feesAmount,
            'gst_amount' => $gstAmount,
            'amount' => $totalAmount,
            'total_amount' => $totalAmount,
            'fees_payment_mode' => $validated['fees_payment_mode'],
            'gst_payment_mode' => $validated['gst_payment_mode'],
            'income_date' => $validated['income_date'],
            'description' => $validated['description'] ?? null,
        ]);

        return redirect()
            ->route('income.index')
            ->with('success', 'Income updated successfully.');
    }

    public function destroy($id)
    {
        if (!$this->isOwner()) {
            abort(403);
        }

        $income = Income::findOrFail($id);
        $income->delete();

        return redirect()
            ->route('income.index')
            ->with('success', 'Income deleted successfully.');
    }
}
