<?php

namespace App\Http\Controllers\Admin;

use App\Models\Campaign;
use App\Models\Program;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\HtmlString;

class CampaignController extends ResourceController
{
    protected string $model = Campaign::class;

    protected string $route = 'admin.campaigns';

    protected string $title = 'Campaigns';

    protected string $singular = 'Campaign';

    protected string $icon = 'megaphone';

    protected string $uploadDir = 'campaigns';

    protected array $searchable = ['title', 'excerpt'];

    protected array $with = ['program'];

    protected function fields(?Model $item): array
    {
        return [
            ['name' => 'title', 'label' => 'Campaign title', 'rules' => 'required|string|max:150'],
            $this->slugField($item, 'campaigns'),
            ['name' => 'program_id', 'label' => 'Related program', 'type' => 'select', 'col' => 6, 'empty' => '— None —',
                'options' => Program::orderBy('title')->pluck('title', 'id')->all(), 'rules' => 'nullable|exists:programs,id'],
            ['name' => 'goal_amount', 'label' => 'Goal amount (₹)', 'type' => 'number', 'col' => 3, 'rules' => 'nullable|numeric|min:0|max:9999999999', 'default' => 0],
            ['name' => 'raised_amount', 'label' => 'Raised so far (₹)', 'type' => 'number', 'col' => 3, 'rules' => 'nullable|numeric|min:0|max:9999999999', 'default' => 0,
                'help' => 'Updates automatically when donations for this campaign are confirmed.'],
            ['name' => 'start_date', 'label' => 'Start date', 'type' => 'date', 'col' => 6, 'rules' => 'nullable|date'],
            ['name' => 'end_date', 'label' => 'End date', 'type' => 'date', 'col' => 6, 'rules' => 'nullable|date|after_or_equal:start_date'],
            ['name' => 'excerpt', 'label' => 'Short description', 'type' => 'textarea', 'rows' => 3, 'rules' => 'required|string|max:500', 'counter' => 200],
            ['name' => 'description', 'label' => 'Full story', 'type' => 'editor'],
            ...$this->seoFields(),
            $this->imageField(),
            $this->featuredField(),
            $this->activeField(),
        ];
    }

    protected function columns(): array
    {
        return [
            ['', fn ($c) => static::thumb($c->image, $c->title)],
            ['Campaign', fn ($c) => static::titleCell($c->title, $c->program?->title), 'title'],
            ['Progress', fn ($c) => new HtmlString('<div class="small mb-1">'.e(inr($c->raised_amount)).' <span class="text-muted">of '.e(inr($c->goal_amount)).'</span></div>'
                .'<div class="progress" style="height:6px"><div class="progress-bar" style="width:'.$c->progress().'%"></div></div>'), 'raised_amount'],
            ['Ends', fn ($c) => $c->end_date?->format('d M Y') ?? '—', 'end_date'],
        ];
    }

    protected function rowActions(Model $item): array
    {
        return [['View on site', route('campaigns.show', $item), 'box-arrow-up-right']];
    }
}
