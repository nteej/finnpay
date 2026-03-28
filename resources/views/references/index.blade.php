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
                            <div class="flex items-center justify-end gap-3">
                                @if($ref->status === 'active')
                                    <a href="{{ $ref->paypalUrl() }}" target="_blank" rel="noopener"
                                       title="Open PayPal payment link"
                                       class="inline-flex items-center gap-1 text-xs font-semibold text-[#009CDE] hover:text-[#003087] transition-colors">
                                        <svg class="w-3.5 h-3.5" viewBox="0 0 24 24" fill="currentColor">
                                            <path d="M7.076 21.337H2.47a.641.641 0 0 1-.633-.74L4.944.901C5.026.382 5.474 0 5.998 0h7.46c2.57 0 4.578.543 5.69 1.81 1.01 1.15 1.304 2.42 1.012 4.287-.023.143-.047.288-.077.437-.983 5.05-4.349 6.797-8.647 6.797h-2.19c-.524 0-.968.382-1.05.9l-1.12 7.106zm14.146-14.42a3.35 3.35 0 0 0-.607-.541c-.013.076-.026.175-.041.254-.93 4.778-4.005 7.201-9.138 7.201h-2.19a.563.563 0 0 0-.556.479l-1.187 7.527h-.506l-.24 1.516a.56.56 0 0 0 .554.647h3.882c.46 0 .85-.334.922-.788.06-.26.76-4.852.816-5.09a.932.932 0 0 1 .921-.788h.58c3.76 0 6.705-1.528 7.565-5.946.36-1.847.174-3.388-.775-4.471z"/>
                                        </svg>
                                        Pay
                                    </a>
                                    <a href="{{ route('references.edit', $ref) }}"
                                       class="text-slate-400 hover:text-[#003580] transition-colors" title="Edit">
                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                                        </svg>
                                    </a>
                                @endif
                                <a href="{{ route('references.show', $ref) }}" class="text-[#003580] hover:text-[#003580] text-xs font-medium">View</a>
                            </div>
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
