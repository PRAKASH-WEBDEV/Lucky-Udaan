{{-- Accessible form field with label, validation feedback and help text. --}}
@props(['name', 'label', 'type' => 'text', 'required' => false, 'options' => [], 'help' => null, 'value' => null, 'placeholder' => null, 'rows' => 4, 'autocomplete' => null, 'empty' => 'Select…'])
@php
    $id = 'f-'.str_replace(['[', ']'], ['-', ''], $name);
    $error = $errors->first($name);
    $val = old($name, $value);
    $describedBy = trim(($help ? $id.'-help ' : '').($error ? $id.'-error' : ''));
@endphp
<div {{ $attributes->class(['mb-3']) }}>
    <label for="{{ $id }}" class="form-label">{{ $label }} @if ($required)<span class="req" aria-hidden="true">*</span>@endif</label>
    @if ($type === 'textarea')
        <textarea name="{{ $name }}" id="{{ $id }}" rows="{{ $rows }}" @class(['form-control', 'is-invalid' => $error]) @required($required)
                  @if ($placeholder) placeholder="{{ $placeholder }}" @endif @if ($describedBy) aria-describedby="{{ $describedBy }}" @endif>{{ $val }}</textarea>
    @elseif ($type === 'select')
        <select name="{{ $name }}" id="{{ $id }}" @class(['form-select', 'is-invalid' => $error]) @required($required) @if ($describedBy) aria-describedby="{{ $describedBy }}" @endif>
            <option value="">{{ $empty }}</option>
            @foreach ($options as $k => $v)
                <option value="{{ $k }}" @selected((string) $val === (string) $k)>{{ $v }}</option>
            @endforeach
        </select>
    @else
        <input type="{{ $type }}" name="{{ $name }}" id="{{ $id }}" value="{{ $type === 'file' ? '' : $val }}" @class(['form-control', 'is-invalid' => $error]) @required($required)
               @if ($placeholder) placeholder="{{ $placeholder }}" @endif @if ($autocomplete) autocomplete="{{ $autocomplete }}" @endif
               @if ($describedBy) aria-describedby="{{ $describedBy }}" @endif {{ $attributes->only(['accept', 'min', 'max', 'step', 'inputmode', 'pattern', 'maxlength']) }}>
    @endif
    @if ($error)<div class="invalid-feedback" id="{{ $id }}-error">{{ $error }}</div>@endif
    @if ($help)<div class="form-text" id="{{ $id }}-help">{{ $help }}</div>@endif
</div>
