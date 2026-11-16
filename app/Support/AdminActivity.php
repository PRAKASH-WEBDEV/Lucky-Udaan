<?php

namespace App\Support;

use App\Models\Admin;
use App\Models\AdminNotification;
use App\Models\AuditLog;
use App\Models\Donation;
use App\Models\Enquiry;
use App\Models\Member;
use App\Models\NewsletterSubscriber;
use App\Models\SurveyResponse;
use App\Models\Volunteer;
use Illuminate\Auth\Events\Login;
use Illuminate\Auth\Events\Logout;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;

/**
 * Audit log (what admins change) and dashboard notifications (what visitors submit).
 */
class AdminActivity
{
    /** Models never written to the audit log. */
    private const SKIP = [AuditLog::class, AdminNotification::class];

    /** Fields that change on their own and aren't worth logging. */
    private const NOISE = ['updated_at', 'created_at', 'remember_token', 'last_login_at', 'last_login_ip'];

    /** Fields whose values must never be stored in the log. */
    private const SECRET = ['password', 'remember_token'];

    public static function register(): void
    {
        foreach (['created', 'updated', 'deleted'] as $action) {
            Event::listen("eloquent.{$action}: *", function (string $event, array $payload) use ($action) {
                self::audit($action, $payload[0] ?? null);
            });
        }

        Event::listen(Login::class, fn (Login $e) => $e->guard === 'admin' ? self::write('login', null, 'Signed in', null, $e->user) : null);
        Event::listen(Logout::class, fn (Logout $e) => $e->guard === 'admin' && $e->user ? self::write('logout', null, 'Signed out', null, $e->user) : null);

        // Visitor submissions → dashboard notification (+ optional email).
        $watch = [
            Donation::class => ['donation', fn ($m) => 'New donation pledge '.inr($m->amount).($m->donor ? ' from '.$m->donor->name : ''), fn ($m) => route('admin.donations.edit', $m->id)],
            Enquiry::class => ['enquiry', fn ($m) => 'New '.Str::headline($m->type).' enquiry from '.$m->name, fn ($m) => route('admin.enquiries.edit', $m->id)],
            Volunteer::class => ['volunteer', fn ($m) => 'New volunteer: '.$m->name, fn ($m) => route('admin.volunteers.edit', $m->id)],
            Member::class => ['member', fn ($m) => 'New membership application: '.$m->name, fn ($m) => route('admin.members.edit', $m->id)],
            NewsletterSubscriber::class => ['newsletter', fn ($m) => 'New newsletter subscriber: '.$m->email, fn () => route('admin.newsletter.index')],
            SurveyResponse::class => ['survey', fn ($m) => 'New response to “'.$m->survey?->title.'”', fn () => route('admin.surveys.index')],
        ];
        foreach ($watch as $class => [$type, $title, $url]) {
            $class::created(function ($model) use ($type, $title, $url) {
                if (! request()->route() || auth('admin')->check()) {
                    return; // seeders/console, or entered by staff — not a visitor submission
                }
                $t = $title($model);
                AdminNotification::notify($type, $t, $model->message ?? null, $url($model));
                self::email($type, $t, $url($model));
            });
        }
    }

    private static function audit(string $action, mixed $model): void
    {
        if (! $model instanceof Model || in_array($model::class, self::SKIP, true) || ! ($admin = auth('admin')->user())) {
            return;
        }

        $changes = null;
        if ($action === 'updated') {
            $dirty = array_diff_key($model->getChanges(), array_flip(self::NOISE));
            if (! $dirty) {
                return;
            }
            $changes = [];
            foreach ($dirty as $key => $new) {
                $changes[$key] = in_array($key, self::SECRET, true)
                    ? ['from' => '•••', 'to' => '•••']
                    : ['from' => self::short($model->getOriginal($key)), 'to' => self::short($new)];
            }
        }

        $label = $model->title ?? $model->name ?? $model->candidate_name ?? $model->service_name ?? $model->email ?? $model->key ?? $model->receipt_no ?? null;
        $description = Str::headline(class_basename($model)).($label ? ': '.Str::limit((string) $label, 120) : ' #'.$model->getKey());

        self::write($action, $model, $description, $changes, $admin);
    }

    private static function write(string $action, ?Model $model, string $description, ?array $changes, $admin): void
    {
        try {
            AuditLog::create([
                'admin_id' => $admin instanceof Admin ? $admin->id : null,
                'action' => $action,
                'subject_type' => $model ? $model::class : null,
                'subject_id' => $model?->getKey(),
                'description' => $description,
                'changes' => $changes,
                'ip' => request()?->ip(),
            ]);
        } catch (\Throwable) {
            // Never let logging break the action itself (e.g. before migrations ran).
        }
    }

    private static function short(mixed $value): mixed
    {
        if ($value instanceof \DateTimeInterface) {
            return $value->format('Y-m-d H:i');
        }
        if (is_array($value)) {
            $value = json_encode($value, JSON_UNESCAPED_UNICODE);
        }

        return is_string($value) ? Str::limit(strip_tags($value), 200) : $value;
    }

    private static function email(string $type, string $title, string $url): void
    {
        $to = setting('notify_email');
        $events = array_map('trim', explode(',', strtolower((string) setting('notify_events'))));
        if (! $to || ! in_array($type, $events, true)) {
            return;
        }
        try {
            Mail::raw($title."\n\nOpen in dashboard: ".$url, fn ($m) => $m->to($to)->subject('['.setting('site_name').'] '.$title));
        } catch (\Throwable $e) {
            logger()->warning('Notification email failed: '.$e->getMessage());
        }
    }
}
