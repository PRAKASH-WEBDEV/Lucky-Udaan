<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Campaign;
use App\Models\Event;
use App\Models\Page;
use App\Models\Post;
use App\Models\Program;
use App\Models\Survey;
use App\Support\Uploader;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Str;

/** Image tools, the list of all website links, and the media library. */
class ToolController extends Controller
{
    // ------------------------------------------------------------ website links

    public function links()
    {
        $static = collect(Route::getRoutes()->getRoutes())
            ->filter(fn ($r) => in_array('GET', $r->methods(), true) && $r->getName()
                && ! Str::startsWith($r->getName(), ['admin.', 'ignition.', 'sanctum.', 'storage.'])
                && ! str_contains($r->uri(), '{') && ! Str::startsWith($r->uri(), ['up', '_']))
            ->map(fn ($r) => [Str::headline(str_replace(['.index', '.'], ['', ' '], $r->getName())), url($r->uri())])
            ->sortBy(0)->values();

        $groups = [
            'Main pages' => $static->all(),
            'Programs' => Program::active()->ordered()->get()->map(fn ($p) => [$p->title, route('programs.show', $p)])->all(),
            'Campaigns' => Campaign::active()->latest('id')->get()->map(fn ($c) => [$c->title, route('campaigns.show', $c)])->all(),
            'Events' => Event::active()->latest('starts_at')->limit(50)->get()->map(fn ($e) => [$e->title, route('events.show', $e)])->all(),
            'Posts & news' => Post::published()->limit(100)->get()->map(fn ($p) => [$p->title.' ('.$p->typeLabel().')', $p->url()])->all(),
            'Surveys' => Survey::active()->get()->map(fn ($s) => [$s->title, route('surveys.show', $s)])->all(),
            'Custom pages (content)' => Page::orderBy('title')->get()->map(fn ($p) => [$p->title.($p->is_active ? '' : ' — hidden'), null, route('admin.pages.edit', $p->id)])->all(),
        ];

        return view('admin.tools.links', ['groups' => array_filter($groups)]);
    }

    // ------------------------------------------------------------ image tools

    public function images()
    {
        return view('admin.tools.images', ['gd' => function_exists('imagewebp')]);
    }

    /** Resize / convert / compress an image and send it straight back as a download (nothing is stored). */
    public function processImage(Request $request)
    {
        $data = $request->validate([
            'image' => ['required', 'image', 'mimes:jpg,jpeg,png,webp', 'max:10240'],
            'width' => ['nullable', 'integer', 'min:16', 'max:6000'],
            'format' => ['required', 'in:webp,jpg,png'],
            'quality' => ['required', 'integer', 'min:30', 'max:100'],
        ]);
        abort_unless(function_exists('imagecreatetruecolor'), 503, 'The GD image extension is not available on this server.');

        $file = $request->file('image');
        $src = match ($file->getMimeType()) {
            'image/png' => @imagecreatefrompng($file->getRealPath()),
            'image/webp' => @imagecreatefromwebp($file->getRealPath()),
            default => @imagecreatefromjpeg($file->getRealPath()),
        };
        abort_unless($src, 422, 'Could not read this image.');

        [$w, $h] = [imagesx($src), imagesy($src)];
        $tw = min((int) ($data['width'] ?: $w), $w);
        $th = (int) round($h * $tw / $w);
        $dst = imagecreatetruecolor($tw, $th);
        if ($data['format'] !== 'jpg') {
            imagealphablending($dst, false);
            imagesavealpha($dst, true);
        } else {
            imagefill($dst, 0, 0, imagecolorallocate($dst, 255, 255, 255));
        }
        imagecopyresampled($dst, $src, 0, 0, 0, 0, $tw, $th, $w, $h);

        ob_start();
        match ($data['format']) {
            'webp' => imagewebp($dst, null, (int) $data['quality']),
            'png' => imagepng($dst, null, (int) round(9 - ($data['quality'] - 30) / 70 * 9)),
            default => imagejpeg($dst, null, (int) $data['quality']),
        };
        $bytes = ob_get_clean();
        imagedestroy($src);
        imagedestroy($dst);

        $name = Str::slug(pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME)) ?: 'image';

        return response($bytes, 200, [
            'Content-Type' => ['webp' => 'image/webp', 'png' => 'image/png', 'jpg' => 'image/jpeg'][$data['format']],
            'Content-Disposition' => 'attachment; filename="'.$name.'-'.$tw.'w.'.$data['format'].'"',
        ]);
    }

    // ------------------------------------------------------------ media library

    public function media(Request $request)
    {
        $root = public_path('uploads');
        $files = collect(File::isDirectory($root) ? File::allFiles($root) : [])
            ->reject(fn ($f) => str_contains($f->getFilename(), '-sm.'))
            ->map(fn ($f) => [
                'path' => str_replace('\\', '/', $f->getRelativePathname()),
                'size' => $f->getSize(),
                'time' => $f->getMTime(),
                'image' => in_array(strtolower($f->getExtension()), ['jpg', 'jpeg', 'png', 'webp', 'gif', 'svg'], true),
            ])
            ->when($q = trim((string) $request->get('q')), fn ($c) => $c->filter(fn ($f) => str_contains(strtolower($f['path']), strtolower($q))))
            ->sortByDesc('time')->values();

        return view('admin.tools.media', ['files' => $files, 'writable' => is_writable($root) || (! File::exists($root) && is_writable(public_path()))]);
    }

    public function uploadMedia(Request $request)
    {
        $request->validate(['files' => 'required|array|max:20', 'files.*' => 'file|mimes:jpg,jpeg,png,webp,pdf|max:10240']);
        try {
            foreach ($request->file('files') as $file) {
                Uploader::store($file, 'library');
            }
        } catch (\Throwable $e) {
            return back()->with('error', 'Upload failed: this server cannot save files (on Vercel the disk is read-only). '.$e->getMessage());
        }

        return back()->with('success', count($request->file('files')).' file(s) uploaded.');
    }
}
