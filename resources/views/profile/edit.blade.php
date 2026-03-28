@extends('layouts.app')
@section('title', 'Profile')

@section('content')
<div class="max-w-xl space-y-5">

    {{-- Personal Info --}}
    <div class="bg-white rounded-xl border border-slate-200 p-6">
        <h3 class="font-semibold text-slate-800 text-sm mb-1">Personal Information</h3>
        <p class="text-xs text-slate-500 mb-5">Your freelancer ID: <span class="font-mono font-semibold text-[#003580]">{{ $user->freelancer_id }}</span></p>

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
                              focus:outline-none focus:ring-2 focus:ring-[#003580] focus:border-transparent">
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
                              focus:outline-none focus:ring-2 focus:ring-[#003580] focus:border-transparent"
                       placeholder="+94 77 123 4567">
            </div>

            <button type="submit"
                    class="w-full bg-[#003580] hover:bg-[#002868] text-white font-medium py-2.5 px-4 rounded-lg transition-colors text-sm">
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
                    <div class="flex items-start gap-3 p-4 rounded-lg border {{ $account->is_default ? 'border-[#003580] bg-[#EEF4FF]' : 'border-slate-200 bg-slate-50' }}">
                        <div class="flex-1 min-w-0">
                            <div class="flex items-center gap-2 mb-0.5">
                                <span class="text-sm font-semibold text-slate-800">{{ $account->bank_name }}</span>
                                <span class="text-xs font-medium px-1.5 py-0.5 rounded
                                    {{ $account->currency === 'LKR' ? 'bg-emerald-100 text-emerald-700' :
                                       ($account->currency === 'USD' ? 'bg-blue-100 text-blue-700' : 'bg-purple-100 text-purple-700') }}">
                                    {{ $account->currency }}
                                </span>
                                @if($account->is_default)
                                    <span class="text-xs font-medium px-1.5 py-0.5 rounded bg-[#DDEEFF] text-[#002868]">Default</span>
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
                                    <button type="submit" class="text-xs text-[#003580] hover:text-[#003580] font-medium">Set default</button>
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
                               focus:outline-none focus:ring-2 focus:ring-[#003580] focus:border-transparent">
                    <option value="LKR" {{ old('currency') === 'LKR' ? 'selected' : '' }}>LKR — Sri Lankan Rupee</option>
                    <option value="USD" {{ old('currency') === 'USD' ? 'selected' : '' }}>USD — US Dollar</option>
                    <option value="EUR" {{ old('currency') === 'EUR' ? 'selected' : '' }}>EUR — Euro</option>
                </select>
            </div>

            <div>
                <label class="block text-sm font-medium text-slate-700 mb-1.5">Account Holder Name</label>
                <input type="text" name="bank_account_holder" value="{{ old('bank_account_holder') }}" required
                       class="w-full border border-slate-300 rounded-lg px-3.5 py-2.5 text-sm text-slate-900
                              focus:outline-none focus:ring-2 focus:ring-[#003580] focus:border-transparent"
                       placeholder="Name as on bank account">
            </div>

            {{-- Bank dropdown (from banks.json) --}}
            <div>
                <label class="block text-sm font-medium text-slate-700 mb-1.5">Bank <span class="text-red-500">*</span></label>
                <select id="bankSelect" name="bank_name" required
                        class="w-full border border-slate-300 rounded-lg px-3.5 py-2.5 text-sm text-slate-900
                               focus:outline-none focus:ring-2 focus:ring-[#003580] focus:border-transparent bg-white">
                    <option value="">Select a bank...</option>
                </select>
                <input type="hidden" id="bankCodeInput" name="bank_code" value="{{ old('bank_code') }}">
            </div>

            {{-- Branch dropdown (populated dynamically) --}}
            <div>
                <label class="block text-sm font-medium text-slate-700 mb-1.5">Branch <span class="text-red-500">*</span></label>
                <select id="branchSelect" name="bank_branch" required disabled
                        class="w-full border border-slate-300 rounded-lg px-3.5 py-2.5 text-sm text-slate-900
                               focus:outline-none focus:ring-2 focus:ring-[#003580] focus:border-transparent bg-white
                               disabled:bg-slate-50 disabled:text-slate-400 disabled:cursor-not-allowed">
                    <option value="">Select bank first...</option>
                </select>
                <input type="hidden" id="branchCodeInput" name="branch_code" value="{{ old('branch_code') }}">
            </div>

            <div>
                <label class="block text-sm font-medium text-slate-700 mb-1.5">Account Number</label>
                <input type="text" name="bank_account_number" value="{{ old('bank_account_number') }}" required
                       class="w-full border border-slate-300 rounded-lg px-3.5 py-2.5 text-sm text-slate-900
                              focus:outline-none focus:ring-2 focus:ring-[#003580] focus:border-transparent"
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
                              focus:outline-none focus:ring-2 focus:ring-[#003580] focus:border-transparent">
            </div>

            <div>
                <label class="block text-sm font-medium text-slate-700 mb-1.5">New Password</label>
                <input type="password" name="password" required
                       class="w-full border border-slate-300 rounded-lg px-3.5 py-2.5 text-sm text-slate-900
                              focus:outline-none focus:ring-2 focus:ring-[#003580] focus:border-transparent">
            </div>

            <div>
                <label class="block text-sm font-medium text-slate-700 mb-1.5">Confirm New Password</label>
                <input type="password" name="password_confirmation" required
                       class="w-full border border-slate-300 rounded-lg px-3.5 py-2.5 text-sm text-slate-900
                              focus:outline-none focus:ring-2 focus:ring-[#003580] focus:border-transparent">
            </div>

            <button type="submit"
                    class="w-full bg-slate-700 hover:bg-slate-800 text-white font-medium py-2.5 px-4 rounded-lg transition-colors text-sm">
                Update Password
            </button>
        </form>
    </div>
</div>
@push('scripts')
<script>
(function () {
    const BANKS = @json(json_decode(file_get_contents(public_path('banks.json'))));
    const oldBankCode   = {{ old('bank_code')   ? (int) old('bank_code')   : 'null' }};
    const oldBranchCode = {{ old('branch_code') ? (int) old('branch_code') : 'null' }};

    const bankSelect   = document.getElementById('bankSelect');
    const branchSelect = document.getElementById('branchSelect');
    const bankCodeInput   = document.getElementById('bankCodeInput');
    const branchCodeInput = document.getElementById('branchCodeInput');

    let allBranches = null; // lazy-loaded

    // ── Populate bank dropdown ───────────────────────────────────────────────
    BANKS.forEach(bank => {
        const opt = new Option(bank.name, bank.name);
        opt.dataset.id = bank.ID;
        if (bank.ID === oldBankCode) opt.selected = true;
        bankSelect.appendChild(opt);
    });

    // ── On bank change: load & filter branches ───────────────────────────────
    bankSelect.addEventListener('change', () => {
        const selectedOpt = bankSelect.options[bankSelect.selectedIndex];
        const bankId = selectedOpt ? parseInt(selectedOpt.dataset.id) : null;

        bankCodeInput.value = bankId || '';

        // Reset branch
        branchSelect.innerHTML = '<option value="">Loading branches...</option>';
        branchSelect.disabled = true;
        branchCodeInput.value = '';

        if (!bankId) {
            branchSelect.innerHTML = '<option value="">Select bank first...</option>';
            return;
        }

        loadBranches().then(branches => {
            populateBranches(branches[bankId] || [], oldBranchCode);
        });
    });

    // ── On branch change: write branch code ──────────────────────────────────
    branchSelect.addEventListener('change', () => {
        const opt = branchSelect.options[branchSelect.selectedIndex];
        branchCodeInput.value = opt ? (opt.dataset.id || '') : '';
    });

    // ── Lazy-load branches.json ───────────────────────────────────────────────
    function loadBranches() {
        if (allBranches) return Promise.resolve(allBranches);
        return fetch('/branches.json')
            .then(r => r.json())
            .then(data => { allBranches = data; return data; });
    }

    function populateBranches(list, preselect) {
        branchSelect.innerHTML = '<option value="">Select a branch...</option>';
        list.forEach(branch => {
            const opt = new Option(branch.name, branch.name);
            opt.dataset.id = branch.ID;
            if (branch.ID === preselect) opt.selected = true;
            branchSelect.appendChild(opt);
        });
        branchSelect.disabled = list.length === 0;
        if (list.length === 0) {
            branchSelect.innerHTML = '<option value="">No branches found</option>';
        }
        // Restore branch code if pre-selected
        const selected = branchSelect.options[branchSelect.selectedIndex];
        branchCodeInput.value = selected ? (selected.dataset.id || '') : '';
    }

    // ── Restore state after validation error ─────────────────────────────────
    if (oldBankCode) {
        // bank dropdown already pre-selected above via BANKS loop;
        // trigger branch load
        const evt = new Event('change');
        bankSelect.dispatchEvent(evt);
    }
})();
</script>
@endpush

@endsection
