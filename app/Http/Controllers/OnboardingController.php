<?php

namespace App\Http\Controllers;

use App\Models\OnboardingWizard;
use App\Models\WizardQuestion;
use Illuminate\Http\Request;

class OnboardingController extends Controller
{
    public function show(string $token)
    {
        $wizard = OnboardingWizard::where('token', $token)
            ->with(['user', 'responses'])
            ->firstOrFail();

        if ($wizard->isCompleted()) {
            return view('onboarding.complete', compact('wizard'));
        }

        $questions = WizardQuestion::where('is_active', true)
            ->orderBy('sort_order')
            ->orderBy('id')
            ->get();

        // Group by section for multi-step display
        $sections = $questions->groupBy('section');

        // Pre-fill existing answers (if resuming)
        $existing = $wizard->responses->keyBy('wizard_question_id');

        return view('onboarding.wizard', compact('wizard', 'sections', 'existing'));
    }

    public function submit(Request $request, string $token)
    {
        $wizard = OnboardingWizard::where('token', $token)
            ->whereNull('completed_at')
            ->firstOrFail();

        $questions = WizardQuestion::where('is_active', true)->get();

        // Build validation rules dynamically
        $rules = [];
        foreach ($questions as $q) {
            $key = 'answers.' . $q->id;
            $rules[$key] = $q->is_required ? 'required' : 'nullable';
            if ($q->type === 'number') $rules[$key] .= '|numeric';
            if ($q->type === 'date')   $rules[$key] .= '|date';
        }

        $validated = $request->validate($rules);
        $answers   = $validated['answers'] ?? [];

        foreach ($questions as $q) {
            $answer = $answers[$q->id] ?? null;
            // Checkbox answers arrive as arrays — encode to JSON string
            if (is_array($answer)) $answer = implode(', ', $answer);

            $wizard->responses()->updateOrCreate(
                ['wizard_question_id' => $q->id],
                ['answer' => $answer]
            );
        }

        $wizard->update(['completed_at' => now()]);

        return redirect()->route('onboarding.show', $token);
    }
}
