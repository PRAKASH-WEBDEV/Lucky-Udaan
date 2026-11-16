<?php

namespace App\Http\Controllers\Admin;

use App\Models\Survey;
use App\Models\SurveyResponse;
use Illuminate\Database\Eloquent\Model;
use Symfony\Component\HttpFoundation\StreamedResponse;

class SurveyController extends ResourceController
{
    protected string $model = Survey::class;

    protected string $route = 'admin.surveys';

    protected string $title = 'Surveys';

    protected string $singular = 'Survey';

    protected string $icon = 'ui-checks';

    protected array $searchable = ['title', 'description'];

    protected function fields(?Model $item): array
    {
        return [
            ['name' => 'title', 'label' => 'Survey title', 'rules' => 'required|string|max:200'],
            $this->slugField($item, 'surveys'),
            ['name' => 'description', 'label' => 'Introduction', 'type' => 'textarea', 'rows' => 3, 'rules' => 'nullable|string|max:2000'],
            ['name' => 'questions', 'label' => 'Questions (one per line)', 'type' => 'textarea', 'rows' => 8, 'rules' => 'required|string|max:5000',
                'placeholder' => "How did you hear about us?\nWhich of our programs helped you?\nWhat should we do more of?",
                'help' => 'Each line becomes a question with a text answer. Respondents can also leave their name and phone.'],
            ['name' => 'ends_on', 'label' => 'Closes on', 'type' => 'date', 'side' => true, 'rules' => 'nullable|date'],
            ['name' => 'is_active', 'label' => 'Open for responses', 'type' => 'switch', 'side' => true, 'default' => true],
        ];
    }

    protected function columns(): array
    {
        return [
            ['Survey', fn ($s) => static::titleCell($s->title, count($s->questionList()).' questions'), 'title'],
            ['Responses', fn ($s) => $s->responses()->count()],
            ['Closes', fn ($s) => $s->ends_on?->format('d M Y') ?? 'No end date', 'ends_on'],
            ['Status', fn ($s) => static::badge($s->isOpen() ? 'Open' : 'Closed', $s->isOpen() ? 'success' : 'secondary')],
        ];
    }

    protected function rowActions(Model $item): array
    {
        return [
            ['Public link', route('surveys.show', $item), 'box-arrow-up-right'],
            ['Download responses (CSV)', route('admin.surveys.responses', $item->getKey()), 'download'],
        ];
    }

    public function responses(int $id): StreamedResponse
    {
        $survey = Survey::findOrFail($id);
        $questions = $survey->questionList();

        return response()->streamDownload(function () use ($survey, $questions) {
            $out = fopen('php://output', 'w');
            fwrite($out, "\xEF\xBB\xBF");
            fputcsv($out, array_merge(['Submitted', 'Name', 'Phone'], $questions));
            SurveyResponse::where('survey_id', $survey->id)->orderBy('id')->chunk(500, function ($rows) use ($out, $questions) {
                foreach ($rows as $r) {
                    $answers = (array) $r->answers;
                    fputcsv($out, array_merge([$r->created_at?->format('Y-m-d H:i'), $r->name, $r->phone], array_map(fn ($i) => $answers[$i] ?? '', array_keys($questions))));
                }
            });
            fclose($out);
        }, $survey->slug.'-responses.csv', ['Content-Type' => 'text/csv; charset=UTF-8']);
    }
}
