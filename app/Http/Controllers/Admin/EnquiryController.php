<?php

namespace App\Http\Controllers\Admin;

use App\Models\Enquiry;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\HtmlString;

/** Contact, partnership, career and internship submissions from the website. */
class EnquiryController extends ResourceController
{
    protected string $model = Enquiry::class;

    protected string $route = 'admin.enquiries';

    protected string $title = 'Enquiries';

    protected string $singular = 'Enquiry';

    protected string $icon = 'envelope-paper';

    protected array $searchable = ['name', 'email', 'phone', 'subject', 'organisation'];

    protected array $with = ['opening'];

    protected ?string $dateColumn = 'created_at';

    protected bool $canCreate = false;

    protected const STATUSES = ['new' => 'New', 'read' => 'Read', 'replied' => 'Replied', 'closed' => 'Closed'];

    public function edit(int $id)
    {
        $item = Enquiry::findOrFail($id);
        if ($item->status === 'new') {
            $item->update(['status' => 'read']);
        }

        return parent::edit($id);
    }

    protected function fields(?Model $item): array
    {
        return [
            ['type' => 'static', 'label' => 'Submission', 'value' => fn ($e) => $this->details($e)],
            ['name' => 'status', 'label' => 'Status', 'type' => 'select', 'side' => true, 'options' => self::STATUSES, 'rules' => 'required|in:new,read,replied,closed'],
            ['type' => 'static', 'side' => true, 'label' => 'Quick reply', 'value' => fn ($e) => new HtmlString(
                '<a class="btn btn-outline-primary btn-sm w-100 mb-2" href="mailto:'.e($e->email).'?subject='.rawurlencode('Re: '.($e->subject ?: 'Your enquiry to '.setting('site_name'))).'"><i class="bi bi-reply me-1"></i>Reply by email</a>'
                .($e->phone ? '<a class="btn btn-outline-success btn-sm w-100" target="_blank" rel="noopener" href="'.e(whatsapp_link($e->phone)).'"><i class="bi bi-whatsapp me-1"></i>WhatsApp</a>' : ''))],
        ];
    }

    private function details(Enquiry $e): HtmlString
    {
        $rows = [
            'Type' => Enquiry::TYPES[$e->type] ?? $e->type,
            'Name' => $e->name,
            'Email' => $e->email,
            'Phone' => $e->phone,
            'Organisation' => $e->organisation,
            'Position' => $e->opening?->title,
            'Subject' => $e->subject,
        ];
        foreach ((array) $e->data as $k => $v) {
            $rows[ucwords(str_replace('_', ' ', $k))] = is_array($v) ? implode(', ', $v) : $v;
        }
        $rows['Received'] = $e->created_at->format('d M Y, h:i A');

        $html = '<dl class="row mb-3 detail-list">';
        foreach (array_filter($rows, fn ($v) => $v !== null && $v !== '') as $k => $v) {
            $html .= '<dt class="col-sm-4">'.e($k).'</dt><dd class="col-sm-8">'.e($v).'</dd>';
        }
        $html .= '</dl>';
        if ($e->message) {
            $html .= '<div class="message-box">'.nl2br(e($e->message)).'</div>';
        }
        if ($e->attachment) {
            $html .= '<a class="btn btn-light border mt-3" href="'.e(route('admin.enquiries.attachment', $e->id)).'"><i class="bi bi-paperclip me-1"></i>Download attached CV</a>';
        }

        return new HtmlString($html);
    }

    public function attachment(int $id)
    {
        $e = Enquiry::findOrFail($id);
        abort_unless($e->attachment && Storage::disk('local')->exists($e->attachment), 404);

        return Storage::disk('local')->download($e->attachment, \Illuminate\Support\Str::slug($e->name).'-cv.'.pathinfo($e->attachment, PATHINFO_EXTENSION));
    }

    protected function beforeDelete(Model $item): void
    {
        if ($item->attachment) {
            Storage::disk('local')->delete($item->attachment);
        }
    }

    protected function filters(): array
    {
        return ['type' => ['Type', Enquiry::TYPES], 'status' => ['Status', self::STATUSES]];
    }

    protected function columns(): array
    {
        return [
            ['From', fn ($e) => new HtmlString(($e->status === 'new' ? '<span class="dot-new" title="New"></span>' : '').static::titleCell($e->name, $e->email)), 'name'],
            ['Type', fn ($e) => static::badge(Enquiry::TYPES[$e->type] ?? $e->type, 'info'), 'type'],
            ['Subject', fn ($e) => \Illuminate\Support\Str::limit($e->subject ?: $e->opening?->title ?: $e->message, 50)],
            ['Status', fn ($e) => static::badge(ucfirst($e->status), static::statusTone($e->status)), 'status'],
            ['Received', fn ($e) => $e->created_at->format('d M Y'), 'created_at'],
        ];
    }

    protected function exportColumns(): array
    {
        return [
            'Type' => 'type', 'Name' => 'name', 'Email' => 'email', 'Phone' => 'phone', 'Organisation' => 'organisation',
            'Subject' => 'subject', 'Message' => 'message', 'Position' => 'opening.title', 'Status' => 'status',
            'Received' => fn ($e) => $e->created_at->format('Y-m-d H:i'),
        ];
    }
}
