<?php

namespace App\Http\Controllers\Admin;

use App\Models\Translation;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

/** English / Hindi text pairs, used in templates via tr('key', 'Fallback'). */
class TranslationController extends ResourceController
{
    protected string $model = Translation::class;

    protected string $route = 'admin.translations';

    protected string $title = 'Translations';

    protected string $singular = 'Translation';

    protected string $icon = 'translate';

    protected array $searchable = ['key', 'en', 'hi'];

    protected string $sort = 'key';

    protected string $direction = 'asc';

    protected int $perPage = 40;

    protected function fields(?Model $item): array
    {
        return [
            ['name' => 'key', 'label' => 'Key', 'col' => 8, 'rules' => ['required', 'string', 'max:190', 'regex:/^[a-z0-9_.\-]+$/', Rule::unique('translations', 'key')->ignore($item?->getKey())], 'placeholder' => 'home.donate_button',
                'help' => 'Lowercase letters, numbers, dots and dashes.'],
            ['name' => 'group', 'label' => 'Group', 'col' => 4, 'rules' => 'nullable|string|max:60', 'default' => 'site', 'datalist' => ['site', 'home', 'forms', 'footer', 'menu']],
            ['name' => 'en', 'label' => 'English', 'type' => 'textarea', 'rows' => 3, 'rules' => 'nullable|string|max:5000'],
            ['name' => 'hi', 'label' => 'हिन्दी (Hindi)', 'type' => 'textarea', 'rows' => 3, 'rules' => 'nullable|string|max:5000'],
        ];
    }

    protected function prepare(array $data, ?Model $item, Request $request): array
    {
        $data['group'] = $data['group'] ?: 'site';

        return $data;
    }

    protected function afterSave(Model $item, Request $request, bool $created): void
    {
        cache()->forget('translations');
    }

    protected function filters(): array
    {
        return ['group' => ['Group', Translation::distinct()->orderBy('group')->pluck('group', 'group')->all()]];
    }

    protected function columns(): array
    {
        return [
            ['Key', fn ($t) => static::titleCell($t->key, $t->group), 'key'],
            ['English', fn ($t) => \Illuminate\Support\Str::limit((string) $t->en, 70)],
            ['Hindi', fn ($t) => \Illuminate\Support\Str::limit((string) $t->hi, 70)],
        ];
    }

    protected function exportColumns(): array
    {
        return ['Key' => 'key', 'Group' => 'group', 'English' => 'en', 'Hindi' => 'hi'];
    }
}
