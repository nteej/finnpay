@extends('layouts.app')
@section('title', 'Profile')

@section('content')
<div class="max-w-xl space-y-5">

    {{-- Personal Info --}}
    <div class="bg-white rounded-xl border border-slate-200 p-6">
        <h3 class="font-semibold text-slate-800 text-sm mb-1">Personal Information</h3>
        <p class="text-xs text-slate-500 mb-5">Your freelancer ID: <span class="font-mono font-semibold text-indigo-600">{{ $user->freelancer_id }}</span></p>

        @if(session('success'))
            <div class="bg-emerald-50 border border-emerald-200 text-emerald-800 rounded-lg px-4 py-3 text-sm mb-5">
                {{ session('success') }}
            </div>
        @endif

        <form method="POST" action="{{ route('profile.update') }}" class="space-y-4">
            @csrf
            @method('PATCH')

            <div>
                <label class="block text-sm font-medium text-slate-700 mb-1.5">Full Name</label>
                <input type="text" name="name" value="{{ old('name', $user->name) }}" required
                       class="w-full border border-slate-300 rounded-lg px-3.5 py-2.5 text-sm text-slate-900
                              focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-transparent">
            </div>

            <div>
                <label class="block text-sm font-medium text-slate-700 mb-1.5">Email Address</label>
                <input type="email" value="{{ $user->email }}" disabled
                       class="w-full border border-slate-200 rounded-lg px-3.5 py-2.5 text-sm text-slate-500 bg-slate-50 cursor-not-allowed">
            </div>

            <div>
                <label class="block text-sm font-medium text-slate-700 mb-1.5">Phone</label>
                <input type="tel" name="phone" value="{{ old('phone', $user->phone) }}"
                       class="w-full border border-slate-300 rounded-lg px-3.5 py-2.5 text-sm text-slate-900
                              focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-transparent"
                       placeholder="+94 77 123 4567">
            </div>

            <button type="submit"
                    class="w-full bg-indigo-600 hover:bg-indigo-700 text-white font-medium py-2.5 px-4 rounded-lg transition-colors text-sm">
                Save Changes
            </button>
        </form>
    </div>

    {{-- Bank Accounts --}}
    <div class="bg-white rounded-xl border border-slate-200 p-6">
        <h3 class="font-semibold text-slate-800 text-sm mb-1">Bank Accounts</h3>
        <p class="text-xs text-slate-500 mb-5">Add one or more bank accounts. Payments will be sent to the default account.</p>

        @if($bankAccounts->isNotEmpty())
            <div class="space-y-3 mb-6">
                @foreach($bankAccounts as $account)
                    <div class="flex items-start gap-3 p-4 rounded-lg border {{ $account->is_default ? 'border-indigo-300 bg-indigo-50' : 'border-slate-200 bg-slate-50' }}">
                        <div class="flex-1 min-w-0">
                            <div class="flex items-center gap-2 mb-0.5">
                                <span class="text-sm font-semibold text-slate-800">{{ $account->bank_name }}</span>
                                <span class="text-xs font-medium px-1.5 py-0.5 rounded
                                    {{ $account->currency === 'LKR' ? 'bg-emerald-100 text-emerald-700' :
                                       ($account->currency === 'USD' ? 'bg-blue-100 text-blue-700' : 'bg-purple-100 text-purple-700') }}">
                                    {{ $account->currency }}
                                </span>
                                @if($account->is_default)
                                    <span class="text-xs font-medium px-1.5 py-0.5 rounded bg-indigo-100 text-indigo-700">Default</span>
                                @endif
                            </div>
                            <p class="text-xs text-slate-500">{{ $account->bank_account_holder }} &middot; {{ $account->bank_account_number }}</p>
                            @if($account->bank_branch)
                                <p class="text-xs text-slate-400">{{ $account->bank_branch }}</p>
                            @endif
                        </div>
                        <div class="flex items-center gap-2 flex-shrink-0">
                            @if(! $account->is_default)
                                <form method="POST" action="{{ route('bank-accounts.setDefault', $account) }}">
                                    @csrf @method('PATCH')
                                    <button type="submit" class="text-xs text-indigo-600 hover:text-indigo-800 font-medium">Set default</button>
                                </form>
                            @endif
                            <form method="POST" action="{{ route('bank-accounts.destroy', $account) }}"
                                  onsubmit="return confirm('Remove this bank account?')">
                                @csrf @method('DELETE')
                                <button type="submit" class="text-xs text-red-500 hover:text-red-700 font-medium">Remove</button>
                            </form>
                        </div>
                    </div>
                @endforeach
            </div>
        @endif

        {{-- Add new bank account --}}
        <h4 class="font-medium text-slate-700 text-sm mb-3 {{ $bankAccounts->isNotEmpty() ? 'border-t border-slate-100 pt-4' : '' }}">Add Bank Account</h4>
        <form method="POST" action="{{ route('bank-accounts.store') }}" class="space-y-3">
            @csrf

            <div>
                <label class="block text-sm font-medium text-slate-700 mb-1.5">Currency</label>
                <select name="currency"
                        class="w-full border border-slate-300 rounded-lg px-3.5 py-2.5 text-sm text-slate-900
                               focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-transparent">
                    <option value="LKR" {{ old('currency') === 'LKR' ? 'selected' : '' }}>LKR — Sri Lankan Rupee</option>
                    <option value="USD" {{ old('currency') === 'USD' ? 'selected' : '' }}>USD — US Dollar</option>
                    <option value="EUR" {{ old('currency') === 'EUR' ? 'selected' : '' }}>EUR — Euro</option>
                </select>
            </div>

            <div>
                <label class="block text-sm font-medium text-slate-700 mb-1.5">Account Holder Name</label>
                <input type="text" name="bank_account_holder" value="{{ old('bank_account_holder') }}" required
                       class="w-full border border-slate-300 rounded-lg px-3.5 py-2.5 text-sm text-slate-900
                              focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-transparent"
                       placeholder="Name as on bank account">
            </div>

            <div>
                <label class="block text-sm font-medium text-slate-700 mb-1.5">Bank Name</label>
                <input type="text" name="bank_name" value="{{ old('bank_name') }}" required
                       class="w-full border border-slate-300 rounded-lg px-3.5 py-2.5 text-sm text-slate-900
                              focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-transparent"
                       placeholder="e.g. Commercial Bank of Ceylon">
            </div>

            <div>
                <label class="block text-sm font-medium text-slate-700 mb-1.5">Bank Branch</label>
                <input type="text" name="bank_branch" value="{{ old('bank_branch') }}"
                       class="w-full border border-slate-300 rounded-lg px-3.5 py-2.5 text-sm text-slate-900
                              focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-transparent"
                       placeholder="e.g. Colombo Main Branch">
            </div>

            <div>
                <label class="block text-sm font-medium text-slate-700 mb-1.5">Account Number</label>
                <input type="text" name="bank_account_number" value="{{ old('bank_account_number') }}" required
                       class="w-full border border-slate-300 rounded-lg px-3.5 py-2.5 text-sm text-slate-900
                              focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-transparent"
                       placeholder="Your bank account number">
            </div>

            <button type="submit"
                    class="w-full bg-slate-700 hover:bg-slate-800 text-white font-medium py-2.5 px-4 rounded-lg transition-colors text-sm">
                Add Bank Account
            </button>
        </form>
    </div>

    {{-- Change Password --}}
    <div class="bg-white rounded-xl border border-slate-200 p-6">
        <h3 class="font-semibold text-slate-800 text-sm mb-5">Change Password</h3>

        <form method="POST" action="{{ route('profile.password') }}" class="space-y-4">
            @csrf
            @method('PATCH')

            @error('current_password')
                <div class="bg-red-50 border border-red-200 text-red-700 rounded-lg px-4 py-3 text-sm">{{ $message }}</div>
            @enderror

            <div>
                <label class="block text-sm font-medium text-slate-700 mb-1.5">Current Password</label>
                <input type="password" name="current_password" required
                       class="w-full border border-slate-300 rounded-lg px-3.5 py-2.5 text-sm text-slate-900
                              focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-transparent">
            </div>

            <div>
                <label class="block text-sm font-medium text-slate-700 mb-1.5">New Password</label>
                <input type="password" name="password" required
                       class="w-full border border-slate-300 rounded-lg px-3.5 py-2.5 text-sm text-slate-900
                              focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-transparent">
            </div>

            <div>
                <label class="block text-sm font-medium text-slate-700 mb-1.5">Confirm New Password</label>
                <input type="password" name="password_confirmation" required
                       class="w-full border border-slate-300 rounded-lg px-3.5 py-2.5 text-sm text-slate-900
                              focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-transparent">
            </div>

            <button type="submit"
                    class="w-full bg-slate-700 hover:bg-slate-800 text-white font-medium py-2.5 px-4 rounded-lg transition-colors text-sm">
                Update Password
            </button>
        </form>
    </div>
</div>
@endsection
