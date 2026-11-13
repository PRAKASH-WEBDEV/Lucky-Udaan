@if (session('success'))
    <div class="alert alert-success d-flex gap-2 align-items-start" role="status" tabindex="-1" id="form-status">
        <i class="bi bi-check-circle-fill fs-5"></i><div>{{ session('success') }}</div>
    </div>
@endif
@if ($errors->any())
    <div class="alert alert-danger d-flex gap-2 align-items-start" role="alert" tabindex="-1" id="form-status">
        <i class="bi bi-exclamation-triangle-fill fs-5"></i>
        <div>Please correct the {{ $errors->count() === 1 ? 'highlighted field' : $errors->count().' highlighted fields' }} and try again.</div>
    </div>
@endif
@if (session('success') || $errors->any())
    @push('scripts')<script>document.getElementById('form-status')?.scrollIntoView({block:'center'});</script>@endpush
@endif
