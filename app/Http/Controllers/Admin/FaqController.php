<?php

namespace App\Http\Controllers\Admin;

use App\Models\Faq;
use Illuminate\Database\Eloquent\Model;

class FaqController extends ResourceController
{
    protected string $model = Faq::class;

    protected string $route = 'admin.faqs';

    protected string $title = 'FAQs';

    protected string $singular = 'FAQ';

    protected string $icon = 'question-circle';

    protected array $searchable = ['question', 'answer'];

    protected string $sort = 'sort_order';

    protected string $direction = 'asc';

    protected function categories(): array
    {
        $list = array_unique(array_merge(['General', 'Donations', 'Volunteering', 'Membership'], Faq::distinct()->pluck('category')->all()));

        return array_combine($list, $list);
    }

    protected function fields(?Model $item): array
    {
        return [
            ['name' => 'question', 'label' => 'Question', 'rules' => 'required|string|max:255'],
            ['name' => 'answer', 'label' => 'Answer', 'type' => 'textarea', 'rows' => 6, 'rules' => 'required|string|max:3000'],
            ['name' => 'category', 'label' => 'Category', 'side' => true, 'rules' => 'required|string|max:60', 'datalist' => array_values($this->categories()), 'default' => 'General'],
            $this->sortField(),
            $this->activeField(),
        ];
    }

    protected function filters(): array
    {
        return ['category' => ['Category', $this->categories()]];
    }

    protected function columns(): array
    {
        return [
            ['Question', fn ($f) => static::titleCell($f->question, $f->answer), 'question'],
            ['Category', fn ($f) => static::badge($f->category, 'info'), 'category'],
            ['Order', fn ($f) => $f->sort_order, 'sort_order'],
        ];
    }
}
