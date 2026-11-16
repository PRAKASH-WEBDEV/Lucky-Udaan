<?php

namespace App\Http\Controllers\Admin;

use App\Models\Beneficiary;
use App\Models\Program;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class BeneficiaryController extends ResourceController
{
    protected string $model = Beneficiary::class;

    protected string $route = 'admin.beneficiaries';

    protected string $title = 'Beneficiaries';

    protected string $singular = 'Beneficiary';

    protected string $icon = 'person-heart';

    protected string $uploadDir = 'beneficiaries';

    protected array $searchable = ['name', 'reg_no', 'phone', 'village', 'district', 'guardian_name'];

    protected array $with = ['program'];

    protected ?string $dateColumn = 'enrolled_on';

    protected function fields(?Model $item): array
    {
        return [
            ['type' => 'section', 'label' => 'Person'],
            ['name' => 'name', 'label' => 'Full name', 'col' => 6, 'rules' => 'required|string|max:150'],
            ['name' => 'reg_no', 'label' => 'Registration no.', 'col' => 3, 'rules' => ['nullable', 'string', 'max:30', Rule::unique('beneficiaries', 'reg_no')->ignore($item?->getKey())], 'help' => 'Blank = auto (BEN-0001).'],
            ['name' => 'category', 'label' => 'Category', 'type' => 'select', 'col' => 3, 'empty' => '—', 'options' => Beneficiary::CATEGORIES, 'rules' => 'nullable|in:'.implode(',', array_keys(Beneficiary::CATEGORIES))],
            ['name' => 'guardian_name', 'label' => 'Father / husband / guardian', 'col' => 6, 'rules' => 'nullable|string|max:150'],
            ['name' => 'gender', 'label' => 'Gender', 'type' => 'select', 'col' => 2, 'empty' => '—', 'options' => ['female' => 'Female', 'male' => 'Male', 'other' => 'Other'], 'rules' => 'nullable|in:female,male,other'],
            ['name' => 'dob', 'label' => 'Date of birth', 'type' => 'date', 'col' => 2, 'rules' => 'nullable|date|before:today'],
            ['name' => 'age', 'label' => 'Age', 'type' => 'number', 'col' => 2, 'step' => 1, 'rules' => 'nullable|integer|min:0|max:120', 'help' => 'If DOB unknown.'],
            ['name' => 'phone', 'label' => 'Phone', 'col' => 4, 'rules' => 'nullable|string|max:30'],
            ['name' => 'id_type', 'label' => 'ID proof', 'type' => 'select', 'col' => 4, 'empty' => '—', 'options' => ['aadhaar' => 'Aadhaar', 'voter' => 'Voter ID', 'ration' => 'Ration card', 'other' => 'Other'], 'rules' => 'nullable|in:aadhaar,voter,ration,other'],
            ['name' => 'id_number', 'label' => 'ID number', 'col' => 4, 'rules' => 'nullable|string|max:40', 'help' => 'For Aadhaar, store only the last 4 digits.'],
            ['type' => 'section', 'label' => 'Address'],
            ['name' => 'address', 'label' => 'House / street', 'rules' => 'nullable|string|max:500'],
            ['name' => 'village', 'label' => 'Village / ward', 'col' => 4, 'rules' => 'nullable|string|max:120'],
            ['name' => 'block', 'label' => 'Block / tehsil', 'col' => 4, 'rules' => 'nullable|string|max:120'],
            ['name' => 'district', 'label' => 'District', 'col' => 4, 'rules' => 'nullable|string|max:120', 'default' => setting('city')],
            ['type' => 'section', 'label' => 'Household & support'],
            ['name' => 'family_size', 'label' => 'Family members', 'type' => 'number', 'col' => 3, 'step' => 1, 'rules' => 'nullable|integer|min:1|max:50'],
            ['name' => 'annual_income', 'label' => 'Annual family income (₹)', 'type' => 'number', 'col' => 3, 'rules' => 'nullable|numeric|min:0|max:99999999'],
            ['name' => 'program_id', 'label' => 'Program', 'type' => 'select', 'col' => 6, 'empty' => '— None —', 'options' => Program::orderBy('title')->pluck('title', 'id')->all(), 'rules' => 'nullable|exists:programs,id'],
            ['name' => 'support_type', 'label' => 'Support given', 'col' => 6, 'rules' => 'nullable|string|max:150', 'datalist' => ['Education kit', 'Scholarship', 'Medical aid', 'Ration kit', 'Skill training', 'Sewing machine', 'Shelter', 'Counselling']],
            ['name' => 'enrolled_on', 'label' => 'Enrolled on', 'type' => 'date', 'col' => 6, 'rules' => 'nullable|date', 'default' => today()->format('Y-m-d')],
            ['name' => 'support_details', 'label' => 'Support history / details', 'type' => 'textarea', 'rows' => 3, 'rules' => 'nullable|string|max:3000'],
            ['name' => 'notes', 'label' => 'Internal notes', 'type' => 'textarea', 'rows' => 2, 'rules' => 'nullable|string|max:2000'],
            $this->imageField('photo', 'Photo', 'Only with the person’s consent.'),
            ['name' => 'is_active', 'label' => 'Currently supported', 'type' => 'switch', 'side' => true, 'default' => true],
        ];
    }

    protected function afterSave(Model $item, Request $request, bool $created): void
    {
        if (! $item->reg_no) {
            $item->forceFill(['reg_no' => 'BEN-'.str_pad($item->id, 4, '0', STR_PAD_LEFT)])->saveQuietly();
        }
    }

    protected function filters(): array
    {
        return [
            'category' => ['Category', Beneficiary::CATEGORIES],
            'program_id' => ['Program', Program::orderBy('title')->pluck('title', 'id')->all()],
        ];
    }

    protected function columns(): array
    {
        return [
            ['', fn ($b) => static::thumb($b->photo, $b->name)],
            ['Beneficiary', fn ($b) => static::titleCell($b->name, trim($b->reg_no.' · '.($b->guardian_name ? 'c/o '.$b->guardian_name : ''), ' ·')), 'name'],
            ['Category', fn ($b) => static::badge(Beneficiary::CATEGORIES[$b->category] ?? null, 'info')],
            ['Village / district', fn ($b) => trim($b->village.', '.$b->district, ', ') ?: '—'],
            ['Program', fn ($b) => $b->program?->title ?? '—'],
            ['Enrolled', fn ($b) => $b->enrolled_on?->format('d M Y') ?? '—', 'enrolled_on'],
        ];
    }

    protected function exportColumns(): array
    {
        return [
            'Reg no' => 'reg_no', 'Name' => 'name', 'Guardian' => 'guardian_name', 'Gender' => 'gender',
            'Age' => fn ($b) => $b->dob ? $b->dob->age : $b->age, 'Phone' => 'phone',
            'Category' => fn ($b) => Beneficiary::CATEGORIES[$b->category] ?? '', 'Village' => 'village', 'Block' => 'block', 'District' => 'district',
            'Family size' => 'family_size', 'Annual income' => 'annual_income', 'Program' => fn ($b) => $b->program?->title,
            'Support' => 'support_type', 'Enrolled on' => fn ($b) => $b->enrolled_on?->format('Y-m-d'), 'Active' => fn ($b) => $b->is_active ? 'Yes' : 'No',
        ];
    }
}
