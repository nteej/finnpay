@extends('layouts.app')
@section('title', 'Payment References')

@section('content')
<div class="flex items-center justify-between mb-6">
    <div>
        <p class="text-sm text-slate-500">Generate unique references for your clients to make payments</p>
    </div>
    <a href="{{ route('references.create') }}"
       class="flex items-center gap-2 bg-[#003580] hover:bg-[#002868] text-white text-sm font-medium px-4 py-2 rounded-lg transition-colors">
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
        </svg>
        New Reference
    </a>
</div>

<div class="bg-white rounded-xl border border-slate-200 overflow-hidden">
    @if($references->isEmpty())
        <div class="text-center py-16">
            <div class="w-14 h-14 bg-[#EEF4FF] rounded-full flex items-center justify-center mx-auto mb-4">
                <svg class="w-7 h-7 text-[#4477AA]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 20l4-16m2 16l4-16M6 9h14M4 15h14"/>
                </svg>
            </div>
            <h3 class="text-slate-700 font-semibold mb-1">No payment references yet</h3>
            <p class="text-slate-500 text-sm mb-4">Create a reference and share it with your clients</p>
            <a href="{{ route('references.create') }}" class="bg-[#003580] text-white text-sm px-5 py-2 rounded-lg hover:bg-[#002868] transition-colors">
                Create first reference
            </a>
        </div>
    @else
        <table class="w-full text-sm">
            <thead>
                <tr class="bg-slate-50 border-b border-slate-200">
                    <th class="text-left px-5 py-3 text-xs font-semibold text-slate-500 uppercase tracking-wider">Reference</th>
                    <th class="text-left px-5 py-3 text-xs font-semibold text-slate-500 uppercase tracking-wider">Title</th>
                    <th class="text-left px-5 py-3 text-xs font-semibold text-slate-500 uppercase tracking-wider hidden sm:table-cell">Requested</th>
                    <th class="text-left px-5 py-3 text-xs font-semibold text-slate-500 uppercase tracking-wider hidden md:table-cell">Payments</th>
                    <th class="text-left px-5 py-3 text-xs font-semibold text-slate-500 uppercase tracking-wider">Status</th>
                    <th class="text-left px-5 py-3 text-xs font-semibold text-slate-500 uppercase tracking-wider hidden lg:table-cell">Created</th>
                    <th class="px-5 py-3"></th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-100">
                @foreach($references as $ref)
                    <tr class="hover:bg-slate-50 transition-colors">
                        <td class="px-5 py-3.5">
                            <span class="font-mono text-[#003580] font-semibold text-xs">{{ $ref->reference_number }}</span>
                        </td>
                        <td class="px-5 py-3.5">
                            <span class="text-slate-800 font-medium">{{ $ref->title }}</span>
                        </td>
                        <td class="px-5 py-3.5 hidden sm:table-cell text-slate-600">
                            @if($ref->amount_requested)
                                {{ $ref->currency === 'EUR' ? '€' : '$' }}{{ number_format($ref->amount_requested, 2) }}
                            @else
                                <span class="text-slate-400">—</span>
                            @endif
                        </td>
                        <td class="px-5 py-3.5 hidden md:table-cell text-slate-600">
                            {{ $ref->transactions_count }}
                        </td>
                        <td class="px-5 py-3.5">
                            @php
                                $badge = match($ref->status) {
                                    'active'    => 'bg-green-100 text-green-700',
                                    'paid'      => 'bg-blue-100 text-blue-700',
                                    'expired'   => 'bg-slate-100 text-slate-600',
                                    'cancelled' => 'bg-red-100 text-red-700',
                                    default     => 'bg-slate-100 text-slate-600',
                                };
                            @endphp
                            <span class="inline-flex px-2 py-0.5 rounded-full text-xs font-medium {{ $badge }}">
                                {{ ucfirst($ref->status) }}
                            </span>
                        </td>
                        <td class="px-5 py-3.5 hidden lg:table-cell text-slate-500 text-xs">
                            {{ $ref->created_at->format('d M Y') }}
                        </td>
                        <td class="px-5 py-3.5 text-right">
                            <a href="{{ route('references.show', $ref) }}" class="text-[#003580] hover:text-[#003580] text-xs font-medium">View</a>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>

        @if($references->hasPages())
            <div class="px-5 py-4 border-t border-slate-100">
                {{ $references->links() }}
            </div>
        @endif
    @endif
</div>
@endsection
