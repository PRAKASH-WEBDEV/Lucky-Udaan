{{-- Spam trap: hidden from people and screen readers; bots fill it in. --}}
<div class="hp-field" aria-hidden="true">
    <label for="website-{{ $id = \Illuminate\Support\Str::random(6) }}">Leave this field empty</label>
    <input type="text" name="website" id="website-{{ $id }}" tabindex="-1" autocomplete="off">
</div>
<input type="hidden" name="_hp_t" value="{{ \Illuminate\Support\Facades\Crypt::encryptString((string) time()) }}">
