<?php

namespace App\Http\Controllers;

use App\Models\BankAccount;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class BankAccountController extends Controller
{
    public function store(Request $request)
    {
        $data = $request->validate([
            'bank_name'           => 'required|string|max:255',
            'bank_branch'         => 'nullable|string|max:255',
            'bank_account_number' => 'required|string|max:50',
            'bank_account_holder' => 'required|string|max:255',
            'currency'            => 'required|in:LKR,USD,EUR',
        ]);

        $user = Auth::user();
        $isFirst = ! $user->bankAccounts()->exists();

        $user->bankAccounts()->create([
            ...$data,
            'is_default' => $isFirst,
        ]);

        return back()->with('success', 'Bank account added successfully.');
    }

    public function destroy(BankAccount $bankAccount)
    {
        abort_unless($bankAccount->user_id === Auth::id(), 403);

        $wasDefault = $bankAccount->is_default;
        $bankAccount->update(['is_active' => false]);

        // Promote the next account to default if the deleted one was default
        if ($wasDefault) {
            Auth::user()->bankAccounts()->first()?->update(['is_default' => true]);
        }

        return back()->with('success', 'Bank account removed.');
    }

    public function setDefault(BankAccount $bankAccount)
    {
        abort_unless($bankAccount->user_id === Auth::id(), 403);

        Auth::user()->bankAccounts()->update(['is_default' => false]);
        $bankAccount->update(['is_default' => true]);

        return back()->with('success', 'Default bank account updated.');
    }
}
