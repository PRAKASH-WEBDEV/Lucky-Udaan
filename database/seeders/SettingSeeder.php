<?php

namespace Database\Seeders;

use App\Models\Setting;
use App\Support\SettingsSchema;
use Illuminate\Database\Seeder;

class SettingSeeder extends Seeder
{
    /** Fills every setting with its default, without overwriting values already edited in the dashboard. */
    public function run(): void
    {
        foreach (SettingsSchema::defaults() as $key => $value) {
            Setting::firstOrCreate(['key' => $key], ['value' => $value === '' ? null : $value]);
        }
        cache()->forget(Setting::CACHE_KEY);
    }
}
