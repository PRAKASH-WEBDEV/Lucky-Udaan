<?php

namespace App\Http\Controllers\Admin;

use App\Models\Testimonial;
use Illuminate\Database\Eloquent\Model;

class TestimonialController extends ResourceController
{
    protected string $model = Testimonial::class;

    protected string $route = 'admin.testimonials';

    protected string $title = 'Testimonials';

    protected string $singular = 'Testimonial';

    protected string $icon = 'chat-quote';

    protected string $uploadDir = 'testimonials';

    protected array $searchable = ['name', 'message'];

    protected string $sort = 'sort_order';

    protected string $direction = 'asc';

    protected function fields(?Model $item): array
    {
        return [
            ['name' => 'name', 'label' => 'Name', 'col' => 6, 'rules' => 'required|string|max:120'],
            ['name' => 'designation', 'label' => 'Role / place', 'col' => 6, 'rules' => 'nullable|string|max:120', 'placeholder' => 'e.g. Volunteer, Gonda'],
            ['name' => 'message', 'label' => 'Testimonial', 'type' => 'textarea', 'rows' => 5, 'rules' => 'required|string|max:1200'],
            ['name' => 'rating', 'label' => 'Rating', 'type' => 'select', 'col' => 4, 'options' => [5 => '★★★★★', 4 => '★★★★', 3 => '★★★'], 'rules' => 'required|integer|between:1,5'],
            $this->imageField('photo', 'Photo'),
            $this->sortField(),
            $this->activeField(),
        ];
    }

    protected function columns(): array
    {
        return [
            ['', fn ($t) => static::thumb($t->photo, $t->name)],
            ['Name', fn ($t) => static::titleCell($t->name, $t->message), 'name'],
            ['Rating', fn ($t) => str_repeat('★', $t->rating), 'rating'],
        ];
    }
}
