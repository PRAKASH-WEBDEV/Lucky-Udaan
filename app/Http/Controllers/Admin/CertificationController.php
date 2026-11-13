<?php

namespace App\Http\Controllers\Admin;

use App\Models\Certification;
use Illuminate\Database\Eloquent\Model;

class CertificationController extends ResourceController
{
    protected string $model = Certification::class;

    protected string $route = 'admin.certifications';

    protected string $title = 'Certifications';

    protected string $singular = 'Certification';

    protected string $icon = 'patch-check';

    protected string $uploadDir = 'certifications';

    protected array $searchable = ['title', 'number', 'issued_by'];

    protected string $sort = 'sort_order';

    protected string $direction = 'asc';

    protected function fields(?Model $item): array
    {
        return [
            ['name' => 'title', 'label' => 'Certificate', 'rules' => 'required|string|max:200', 'placeholder' => 'e.g. 80G Registration'],
            ['name' => 'number', 'label' => 'Registration / certificate number', 'col' => 6, 'rules' => 'nullable|string|max:120'],
            ['name' => 'issued_by', 'label' => 'Issued by', 'col' => 6, 'rules' => 'nullable|string|max:150'],
            ['name' => 'issued_on', 'label' => 'Issued on', 'type' => 'date', 'col' => 6, 'rules' => 'nullable|date'],
            ['name' => 'valid_till', 'label' => 'Valid till', 'type' => 'date', 'col' => 6, 'rules' => 'nullable|date'],
            ['name' => 'description', 'label' => 'What it means', 'type' => 'textarea', 'rows' => 3, 'rules' => 'nullable|string|max:1000'],
            $this->imageField('image', 'Certificate image'),
            ['name' => 'file', 'label' => 'Certificate PDF', 'type' => 'file', 'side' => true, 'mimes' => 'pdf', 'help' => 'PDF up to 10 MB.'],
            $this->sortField(),
            $this->activeField(),
        ];
    }

    protected function columns(): array
    {
        return [
            ['', fn ($c) => static::thumb($c->image, $c->title)],
            ['Certificate', fn ($c) => static::titleCell($c->title, $c->issued_by), 'title'],
            ['Number', fn ($c) => $c->number ?: '—', 'number'],
            ['Issued', fn ($c) => $c->issued_on?->format('d M Y') ?? '—', 'issued_on'],
        ];
    }
}
