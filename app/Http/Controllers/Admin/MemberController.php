<?php

namespace App\Http\Controllers\Admin;

use App\Models\Member;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;

class MemberController extends ResourceController
{
    protected string $model = Member::class;

    protected string $route = 'admin.members';

    protected string $title = 'Members';

    protected string $singular = 'Member';

    protected string $icon = 'person-badge';

    protected string $uploadDir = 'members';

    protected array $searchable = ['name', 'email', 'phone', 'membership_no', 'city'];

    protected ?string $dateColumn = 'created_at';

    protected const STATUSES = ['pending' => 'Pending', 'approved' => 'Approved', 'rejected' => 'Rejected'];

    protected function fields(?Model $item): array
    {
        return [
            ['name' => 'name', 'label' => 'Full name', 'col' => 6, 'rules' => 'required|string|max:120'],
            ['name' => 'email', 'label' => 'Email', 'type' => 'email', 'col' => 6, 'rules' => 'required|email|max:150'],
            ['name' => 'phone', 'label' => 'Phone', 'col' => 6, 'rules' => 'required|string|max:20'],
            ['name' => 'gender', 'label' => 'Gender', 'type' => 'select', 'col' => 3, 'empty' => '—', 'options' => ['Female' => 'Female', 'Male' => 'Male', 'Other' => 'Other'], 'rules' => 'nullable|in:Female,Male,Other'],
            ['name' => 'dob', 'label' => 'Date of birth', 'type' => 'date', 'col' => 3, 'rules' => 'nullable|date|before:today'],
            ['name' => 'occupation', 'label' => 'Occupation', 'col' => 6, 'rules' => 'nullable|string|max:120'],
            ['name' => 'city', 'label' => 'City', 'col' => 6, 'rules' => 'nullable|string|max:80'],
            ['name' => 'address', 'label' => 'Address', 'type' => 'textarea', 'rows' => 2, 'rules' => 'nullable|string|max:500'],
            ['name' => 'message', 'label' => 'Why they want to join', 'type' => 'textarea', 'rows' => 3, 'rules' => 'nullable|string|max:2000'],
            ['name' => 'membership_type', 'label' => 'Membership type', 'type' => 'select', 'side' => true, 'options' => Member::TYPES, 'rules' => 'required|in:'.implode(',', array_keys(Member::TYPES))],
            ['name' => 'status', 'label' => 'Status', 'type' => 'select', 'side' => true, 'options' => self::STATUSES, 'rules' => 'required|in:pending,approved,rejected',
                'help' => 'A membership number is issued automatically on approval.'],
            ['name' => 'valid_till', 'label' => 'Valid till', 'type' => 'date', 'side' => true, 'rules' => 'nullable|date'],
            $this->imageField('photo', 'Photo'),
            $this->activeField(),
        ];
    }

    protected function prepare(array $data, ?Model $item, Request $request): array
    {
        if (($data['status'] ?? null) === 'approved' && ! $item?->membership_no) {
            $next = (int) Member::whereNotNull('membership_no')->count() + 1;
            do {
                $no = 'LUF-M-'.str_pad((string) $next++, 4, '0', STR_PAD_LEFT);
            } while (Member::where('membership_no', $no)->exists());
            $data['membership_no'] = $no;
            if (empty($data['valid_till']) && ($data['membership_type'] ?? '') !== 'life') {
                $data['valid_till'] = now()->addYear()->toDateString();
            }
        }

        return $data;
    }

    protected function filters(): array
    {
        return ['status' => ['Status', self::STATUSES], 'membership_type' => ['Type', Member::TYPES]];
    }

    protected function columns(): array
    {
        return [
            ['Member', fn ($m) => static::titleCell($m->name, $m->membership_no ?: $m->email), 'name'],
            ['Phone', fn ($m) => $m->phone],
            ['Type', fn ($m) => Member::TYPES[$m->membership_type] ?? $m->membership_type, 'membership_type'],
            ['Status', fn ($m) => static::badge(ucfirst($m->status), static::statusTone($m->status)), 'status'],
            ['Applied', fn ($m) => $m->created_at->format('d M Y'), 'created_at'],
        ];
    }

    protected function exportColumns(): array
    {
        return [
            'Membership No' => 'membership_no', 'Name' => 'name', 'Email' => 'email', 'Phone' => 'phone', 'Gender' => 'gender',
            'DOB' => fn ($m) => $m->dob?->format('Y-m-d'), 'Occupation' => 'occupation', 'City' => 'city', 'Address' => 'address',
            'Type' => 'membership_type', 'Status' => 'status', 'Valid till' => fn ($m) => $m->valid_till?->format('Y-m-d'),
            'Applied on' => fn ($m) => $m->created_at->format('Y-m-d'),
        ];
    }
}
