<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Setting;
use App\Support\SettingsSchema;
use App\Support\Uploader;
use Illuminate\Http\Request;

class SettingController extends Controller
{
    public function edit()
    {
        return view('admin.settings', [
            'groups' => SettingsSchema::groups(),
            'values' => Setting::allCached(),
        ]);
    }

    public function update(Request $request)
    {
        $fields = SettingsSchema::fields();
        $rules = [];
        $attributes = [];
        foreach ($fields as $key => $f) {
            $rules[$key] = $f['type'] === 'image'
                ? ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:5120']
                : (is_array($f['rules']) ? $f['rules'] : explode('|', $f['rules']));
            $attributes[$key] = strtolower($f['label']);
        }
        $validated = $request->validate($rules, [], $attributes);

        $current = Setting::allCached();
        $save = [];
        foreach ($fields as $key => $f) {
            if ($f['type'] === 'image') {
                if ($request->hasFile($key)) {
                    Uploader::delete($current[$key] ?? null);
                    $save[$key] = Uploader::store($request->file($key), 'brand');
                } elseif ($request->boolean('remove_'.$key)) {
                    Uploader::delete($current[$key] ?? null);
                    $save[$key] = null;
                }

                continue;
            }
            $save[$key] = $validated[$key] ?? null;
        }
        Setting::put($save);

        return redirect()->route('admin.settings.edit', ['tab' => $request->input('_tab')])->with('success', 'Settings saved.');
    }
}
