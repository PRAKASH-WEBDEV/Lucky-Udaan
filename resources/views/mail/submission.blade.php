<x-mail::message>
# {{ $heading }}

A new submission was received on the website.

<x-mail::table>
| Field | Details |
|:------|:--------|
@foreach ($fields as $label => $value)
@if (filled($value))
| **{{ $label }}** | {{ str_replace(["\r", "\n", '|'], [' ', ' ', '/'], is_array($value) ? implode(', ', $value) : $value) }} |
@endif
@endforeach
</x-mail::table>

@if ($adminUrl)
<x-mail::button :url="$adminUrl">
Open in dashboard
</x-mail::button>
@endif

{{ setting('site_name') }}
</x-mail::message>
