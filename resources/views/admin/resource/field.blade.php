@php
    $type = $field['type'] ?? 'text';
    $name = $field['name'] ?? null;
    $label = $field['label'] ?? '';
    $col = $field['col'] ?? 12;
    $id = $name ? 'field-'.$name : null;
    $rules = $field['rules'] ?? [];
    $rulesStr = is_array($rules) ? implode('|', array_filter($rules, 'is_string')) : $rules;
    $required = str_contains($rulesStr, 'required') && ! str_contains($rulesStr, 'required_');
    $error = $name ? $errors->first($name) : null;
    $raw = $name ? $item->{$name} : null;
    if ($raw instanceof \Carbon\CarbonInterface) {
        $raw = $type === 'datetime' ? $raw->format('Y-m-d\TH:i') : $raw->format('Y-m-d');
    }
    if (! $item->exists && $raw === null && array_key_exists('default', $field)) {
        $raw = $field['default'];
    }
    $value = $name && ! in_array($type, ['image', 'file', 'password'], true) ? old($name, $raw) : null;
@endphp

@if ($type === 'section')
    <div class="col-12"><div class="form-section-title">{{ $label }}</div>@if (! empty($field['help']))<p class="form-text mt-n2 mb-3">{{ $field['help'] }}</p>@endif</div>
@elseif (! empty($field['hide_on_edit']) && $item->exists)
    {{-- not shown when editing --}}
@elseif ($type === 'static')
    <div class="col-12 mb-3">
        <div class="form-label">{{ $label }}</div>
        <div>{{ $field['value']($item) }}</div>
    </div>
@elseif ($type === 'switch')
    <div class="col-{{ $col }} mb-3">
        <div class="form-check form-switch">
            <input type="hidden" name="{{ $name }}" value="0">
            <input class="form-check-input" type="checkbox" role="switch" name="{{ $name }}" id="{{ $id }}" value="1" @checked(old($name, $raw ?? ($field['default'] ?? false)))>
            <label class="form-check-label" for="{{ $id }}">{{ $label }}</label>
        </div>
    </div>
@else
    <div class="col-md-{{ $col }} mb-3">
        <label class="form-label" for="{{ $id }}">{{ $label }} @if ($required)<span class="text-danger">*</span>@endif</label>

        @switch($type)
            @case('textarea')
                <textarea class="form-control @if ($error) is-invalid @endif" name="{{ $name }}" id="{{ $id }}" rows="{{ $field['rows'] ?? 4 }}"
                          @if (! empty($field['counter'])) data-counter="{{ $field['counter'] }}" @endif
                          @if (! empty($field['placeholder'])) placeholder="{{ $field['placeholder'] }}" @endif>{{ $value }}</textarea>
                @break

            @case('editor')
                <input type="hidden" name="{{ $name }}" id="{{ $id }}" value="{{ $value }}">
                <div class="editor-wrap @if ($error) is-invalid @endif"><div data-editor="{{ $id }}"></div></div>
                @break

            @case('select')
                <select class="form-select @if ($error) is-invalid @endif" name="{{ $name }}" id="{{ $id }}">
                    @if (array_key_exists('empty', $field))<option value="">{{ $field['empty'] }}</option>@endif
                    @foreach ($field['options'] as $k => $v)
                        <option value="{{ $k }}" @selected((string) $value === (string) $k)>{{ $v }}</option>
                    @endforeach
                </select>
                @break

            @case('checkboxes')
                @php $checked = (array) old($name, $raw ?? []); @endphp
                <div class="check-columns border rounded-3 p-3 @if ($error) border-danger @endif">
                    @foreach ($field['options'] as $k => $v)
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" name="{{ $name }}[]" value="{{ $k }}" id="{{ $id }}-{{ $loop->index }}" @checked(in_array($k, $checked, true))>
                            <label class="form-check-label" for="{{ $id }}-{{ $loop->index }}">{{ $v }}</label>
                        </div>
                    @endforeach
                </div>
                @break

            @case('icon')
                <div class="icon-grid border rounded-3" role="radiogroup" aria-label="{{ $label }}">
                    @foreach ($field['options'] as $k => $v)
                        <label title="{{ $k }}"><input type="radio" name="{{ $name }}" value="{{ $k }}" @checked($value === $k)><span><i class="bi bi-{{ $k }}"></i></span></label>
                    @endforeach
                </div>
                @break

            @case('image')
                <div class="image-input" data-image-input>
                    <div class="preview">
                        @if ($raw)<img src="{{ upload_url($raw) }}" alt="Current {{ strtolower($label) }}">@else<i class="bi bi-image fs-2"></i>@endif
                    </div>
                    <input type="file" class="form-control form-control-sm @if ($error) is-invalid @endif" name="{{ $name }}" id="{{ $id }}" accept="image/jpeg,image/png,image/webp">
                    @if ($raw && empty($field['required']))
                        <div class="form-check mt-2">
                            <input class="form-check-input" type="checkbox" name="remove_{{ $name }}" value="1" id="remove-{{ $name }}">
                            <label class="form-check-label small" for="remove-{{ $name }}">Remove current image</label>
                        </div>
                    @endif
                </div>
                @break

            @case('file')
                @if ($raw)
                    <div class="d-flex align-items-center gap-2 mb-2 small">
                        <i class="bi bi-file-earmark-pdf text-danger fs-5"></i><a href="{{ upload_url($raw) }}" target="_blank" rel="noopener">View current file</a>
                        <span class="form-check ms-auto mb-0"><input class="form-check-input" type="checkbox" name="remove_{{ $name }}" value="1" id="remove-{{ $name }}"><label class="form-check-label" for="remove-{{ $name }}">Remove</label></span>
                    </div>
                @endif
                <input type="file" class="form-control @if ($error) is-invalid @endif" name="{{ $name }}" id="{{ $id }}" accept="{{ collect(explode(',', $field['mimes'] ?? 'pdf'))->map(fn ($m) => '.'.$m)->implode(',') }}">
                @break

            @default
                @php
                    $htmlType = match ($type) { 'datetime' => 'datetime-local', 'number', 'email', 'url', 'date', 'color', 'password' => $type, default => 'text' };
                @endphp
                <input type="{{ $htmlType }}" class="form-control @if ($type === 'color') form-control-color w-100 @endif @if ($error) is-invalid @endif"
                       name="{{ $name }}" id="{{ $id }}" value="{{ $value }}"
                       @if ($type === 'number') step="{{ $field['step'] ?? 'any' }}" @endif
                       @if ($type === 'password') autocomplete="new-password" @endif
                       @if (! empty($field['counter'])) data-counter="{{ $field['counter'] }}" @endif
                       @if (! empty($field['datalist'])) list="{{ $id }}-list" @endif
                       @if (! empty($field['placeholder'])) placeholder="{{ $field['placeholder'] }}" @endif>
                @if (! empty($field['datalist']))
                    <datalist id="{{ $id }}-list">@foreach ($field['datalist'] as $opt)<option value="{{ $opt }}">@endforeach</datalist>
                @endif
        @endswitch

        @if ($error)<div class="invalid-feedback d-block">{{ $error }}</div>@endif
        @if (! empty($field['help']))<div class="form-text">{{ $field['help'] }}</div>@endif
    </div>
@endif
