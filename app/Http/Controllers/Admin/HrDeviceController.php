<?php

namespace App\Http\Controllers\Admin;

use App\Models\HrDevice;
use Illuminate\Database\Eloquent\Model;

/** Attendance devices (biometric / RFID / face machines). */
class HrDeviceController extends ResourceController
{
    protected string $model = HrDevice::class;

    protected string $route = 'admin.devices';

    protected string $title = 'Attendance Devices';

    protected string $singular = 'Device';

    protected string $icon = 'fingerprint';

    protected array $searchable = ['name', 'serial_no', 'location'];

    protected string $sort = 'name';

    protected string $direction = 'asc';

    protected function fields(?Model $item): array
    {
        return [
            ['name' => 'name', 'label' => 'Device name', 'col' => 6, 'rules' => 'required|string|max:120', 'placeholder' => 'Head office – main gate'],
            ['name' => 'device_type', 'label' => 'Type', 'type' => 'select', 'col' => 6, 'options' => HrDevice::TYPES, 'rules' => 'required|in:'.implode(',', array_keys(HrDevice::TYPES)), 'default' => 'biometric'],
            ['name' => 'serial_no', 'label' => 'Serial number', 'col' => 4, 'rules' => 'nullable|string|max:80'],
            ['name' => 'ip_address', 'label' => 'IP address', 'col' => 4, 'rules' => 'nullable|ip'],
            ['name' => 'location', 'label' => 'Location', 'col' => 4, 'rules' => 'nullable|string|max:150'],
            ['name' => 'last_sync_at', 'label' => 'Last synced', 'type' => 'datetime', 'col' => 6, 'rules' => 'nullable|date'],
            ['name' => 'notes', 'label' => 'Notes', 'type' => 'textarea', 'rows' => 2, 'rules' => 'nullable|string|max:1000'],
            ['name' => 'is_active', 'label' => 'In use', 'type' => 'switch', 'side' => true, 'default' => true],
        ];
    }

    protected function columns(): array
    {
        return [
            ['Device', fn ($d) => static::titleCell($d->name, $d->location), 'name'],
            ['Type', fn ($d) => HrDevice::TYPES[$d->device_type] ?? $d->device_type],
            ['Serial / IP', fn ($d) => trim($d->serial_no.' '.$d->ip_address) ?: '—'],
            ['Last sync', fn ($d) => $d->last_sync_at?->diffForHumans() ?? 'Never', 'last_sync_at'],
        ];
    }
}
