<?php

namespace App\Http\Controllers\Site;

use App\Http\Controllers\Controller;
use App\Models\NewsletterSubscriber;
use App\Models\Survey;
use App\Models\SurveyResponse;
use Illuminate\Http\Request;

/** Newsletter sign-up and public surveys. */
class EngageController extends Controller
{
    public function subscribe(Request $request)
    {
        $data = $request->validate([
            'newsletter_email' => ['required', 'email:rfc', 'max:150'],
            'newsletter_name' => ['nullable', 'string', 'max:120'],
        ], [], ['newsletter_email' => 'email']);

        $sub = NewsletterSubscriber::firstOrNew(['email' => strtolower($data['newsletter_email'])]);
        $isNew = ! $sub->exists || $sub->status !== 'subscribed';
        $sub->fill(['name' => $data['newsletter_name'] ?? $sub->name, 'status' => 'subscribed', 'source' => $sub->source ?: 'website', 'ip' => $request->ip()])->save();

        return back()->with('newsletter', $isNew ? 'Thank you! You’re subscribed to our updates.' : 'You’re already subscribed — thank you!');
    }

    public function survey(Survey $survey)
    {
        abort_unless($survey->is_active, 404);

        return view('site.survey', ['survey' => $survey, 'questions' => $survey->questionList()]);
    }

    public function submitSurvey(Request $request, Survey $survey)
    {
        abort_unless($survey->isOpen(), 404);
        $questions = $survey->questionList();
        $data = $request->validate([
            'name' => ['nullable', 'string', 'max:120'],
            'phone' => ['nullable', 'string', 'max:20'],
            'answers' => ['required', 'array'],
            'answers.*' => ['nullable', 'string', 'max:2000'],
        ]);
        $answers = array_map(fn ($i) => trim((string) ($data['answers'][$i] ?? '')), array_keys($questions));
        if (! array_filter($answers)) {
            return back()->withInput()->withErrors(['answers' => 'Please answer at least one question.']);
        }

        SurveyResponse::create([
            'survey_id' => $survey->id, 'name' => $data['name'] ?? null, 'phone' => $data['phone'] ?? null,
            'answers' => $answers, 'ip' => $request->ip(),
        ]);

        return back()->with('success', 'Thank you for sharing your views. Your response has been recorded.');
    }
}
