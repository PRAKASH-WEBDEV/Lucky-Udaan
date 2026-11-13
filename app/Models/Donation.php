<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Donation extends Model
{
    protected $guarded = ['id'];

    public const METHODS = [
        'upi' => 'UPI',
        'bank_transfer' => 'Bank Transfer / NEFT / IMPS',
        'cash' => 'Cash',
        'cheque' => 'Cheque / DD',
        'other' => 'Other',
    ];

    public const STATUSES = ['pending' => 'Pending', 'confirmed' => 'Confirmed', 'failed' => 'Failed'];

    protected function casts(): array
    {
        return [
            'amount' => 'decimal:2',
            'wants_80g' => 'boolean',
            'donated_at' => 'datetime',
        ];
    }

    protected static function booted(): void
    {
        static::creating(function (Donation $donation) {
            $donation->donated_at ??= now();
            if (! $donation->receipt_no) {
                $prefix = 'LUF-'.now()->format('ym').'-';
                $last = static::where('receipt_no', 'like', $prefix.'%')->orderByDesc('receipt_no')->value('receipt_no');
                $next = $last ? ((int) substr($last, -4)) + 1 : 1;
                $donation->receipt_no = $prefix.str_pad((string) $next, 4, '0', STR_PAD_LEFT);
            }
        });

        // Keep a campaign's raised total in sync with its confirmed donations.
        static::saved(fn (Donation $d) => $d->syncCampaignTotals());
        static::deleted(fn (Donation $d) => $d->syncCampaignTotals());
    }

    public function syncCampaignTotals(): void
    {
        foreach (array_filter([$this->campaign_id, $this->getOriginal('campaign_id')]) as $id) {
            $campaign = Campaign::find($id);
            $campaign?->update(['raised_amount' => $campaign->donations()->where('status', 'confirmed')->sum('amount')]);
        }
    }

    public function donor()
    {
        return $this->belongsTo(Donor::class);
    }

    public function campaign()
    {
        return $this->belongsTo(Campaign::class);
    }

    public function methodLabel(): string
    {
        return self::METHODS[$this->method] ?? ucfirst($this->method);
    }
}
