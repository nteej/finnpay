<div class="space-y-4 p-1">
    <div class="flex items-center gap-3 text-sm text-gray-600 mb-4 pb-3 border-b border-gray-200">
        <span>Completed: <strong>{{ $wizard->completed_at->format('d M Y, H:i') }}</strong></span>
        <span>&middot;</span>
        <span>{{ $wizard->responses->count() }} answer{{ $wizard->responses->count() !== 1 ? 's' : '' }}</span>
    </div>

    @php
        $bySection = $wizard->responses->load('question')->groupBy('question.section');
    @endphp

    @foreach($bySection as $section => $responses)
        <div>
            <p class="text-xs font-bold uppercase tracking-widest text-gray-400 mb-2">{{ $section }}</p>
            <div class="space-y-3">
                @foreach($responses->sortBy('question.sort_order') as $response)
                    <div class="bg-gray-50 rounded-lg px-4 py-3">
                        <p class="text-xs font-semibold text-gray-500 mb-1">{{ $response->question->question_text }}</p>
                        <p class="text-sm text-gray-800">
                            {{ $response->answer ?: '—' }}
                        </p>
                    </div>
                @endforeach
            </div>
        </div>
    @endforeach

    @if($wizard->responses->isEmpty())
        <p class="text-sm text-gray-400 text-center py-4">No responses recorded.</p>
    @endif
</div>
