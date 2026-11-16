@extends('layouts.admin')
@section('title', 'Image Tools')

@section('content')
<div class="page-head">
    <div>
        <h1>Image Tools</h1>
        <p class="sub">Resize, compress or convert a photo before using it on the website, WhatsApp or print. The result downloads straight to your computer — nothing is saved on the server.</p>
    </div>
</div>

@if (! $gd)<div class="alert alert-warning">The GD image extension is not enabled on this server, so image processing is unavailable.</div>@endif
@if ($errors->any())<div class="alert alert-danger">{{ $errors->first() }}</div>@endif

<div class="row g-4">
    <div class="col-xl-7">
        <form class="card" method="post" action="{{ route('admin.tools.images.process') }}" enctype="multipart/form-data">
            @csrf
            <div class="card-body row g-3">
                <div class="col-12">
                    <label class="form-label" for="img">Image <span class="text-danger">*</span></label>
                    <input type="file" class="form-control" id="img" name="image" accept="image/jpeg,image/png,image/webp" required
                           onchange="const f=this.files[0];if(!f)return;const r=new FileReader();r.onload=e=>{const p=document.getElementById('preview');p.src=e.target.result;p.onload=()=>document.getElementById('dims').textContent=p.naturalWidth+' × '+p.naturalHeight+' px · '+(f.size/1024).toFixed(0)+' KB'};r.readAsDataURL(f)">
                    <div class="form-text">JPG, PNG or WebP up to 10 MB.</div>
                </div>
                <div class="col-sm-4">
                    <label class="form-label" for="w">Width (px)</label>
                    <input type="number" class="form-control" id="w" name="width" min="16" max="6000" placeholder="Original" list="widths">
                    <datalist id="widths"><option value="1920"><option value="1200"><option value="800"><option value="640"><option value="400"></datalist>
                    <div class="form-text">Height scales automatically.</div>
                </div>
                <div class="col-sm-4">
                    <label class="form-label" for="fmt">Format</label>
                    <select class="form-select" id="fmt" name="format">
                        <option value="webp">WebP (smallest, for website)</option>
                        <option value="jpg">JPG (photos, print)</option>
                        <option value="png">PNG (logos, transparency)</option>
                    </select>
                </div>
                <div class="col-sm-4">
                    <label class="form-label" for="q">Quality: <span id="qv">80</span></label>
                    <input type="range" class="form-range" id="q" name="quality" min="30" max="100" value="80" oninput="document.getElementById('qv').textContent=this.value">
                </div>
            </div>
            <div class="card-footer"><button class="btn btn-primary" type="submit" @disabled(! $gd)><i class="bi bi-download"></i> Process &amp; download</button></div>
        </form>
    </div>
    <div class="col-xl-5">
        <div class="card h-100">
            <div class="card-header">Preview</div>
            <div class="card-body text-center">
                <img id="preview" alt="" style="max-width:100%;max-height:320px;border-radius:8px">
                <div id="dims" class="small text-muted mt-2">Choose an image to see its size.</div>
            </div>
            <div class="card-footer small text-muted">Tips: website banners 1920 px · content images 1200 px · thumbnails 640 px · WebP quality 75–85.</div>
        </div>
    </div>
</div>
@endsection
