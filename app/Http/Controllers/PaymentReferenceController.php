<?php

namespace App\Http\Controllers;

use App\Models\PaymentReference;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class PaymentReferenceController extends Controller
{
    public function index()
    {
        $references = Auth::user()->paymentReferences()
            ->withCount('transactions')
            ->orderByDesc('created_at')
            ->paginate(15);

        return view('references.index', compact('references'));
    }

    public function create()
    {
        if (! auth()->user()->isFullyOnboarded()) {
            return redirect()->route('dashboard')->with('error', 'Your account setup is not complete. Please wait for your package to be assigned.');
        }
        return view('references.create');
    }

    public function store(Request $request)
    {
        if (! auth()->user()->isFullyOnboarded()) {
            return redirect()->route('dashboard')->with('error', 'Your account setup is not complete. Please wait for your package to be assigned.');
        }
        $data = $request->validate([
            'title'            => 'required|string|max:255',
            'notes'            => 'nullable|string|max:1000',
            'amount_requested' => 'nullable|numeric|min:0',
            'currency'         => 'required|in:USD,EUR',
            'expires_at'       => 'nullable|date|after:today',
        ]);

        $reference = Auth::user()->paymentReferences()->create([
            ...$data,
            'reference_number' => PaymentReference::generateReference(),
        ]);

        return redirect()->route('references.show', $reference)
            ->with('success', 'Payment reference created successfully.');
    }

    public function show(PaymentReference $reference)
    {
        $this->authorize('view', $reference);
        $reference->load('transactions');
        return view('references.show', compact('reference'));
    }

    public function destroy(PaymentReference $reference)
    {
        $this->authorize('delete', $reference);
        $reference->update(['status' => 'cancelled']);
        return redirect()->route('references.index')
            ->with('success', 'Payment reference cancelled.');
    }
}
