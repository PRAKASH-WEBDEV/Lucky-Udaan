<?php

namespace Database\Seeders;

use App\Models\Campaign;
use App\Models\Donation;
use App\Models\Donor;
use App\Models\Enquiry;
use App\Models\Member;
use App\Models\Volunteer;
use Illuminate\Database\Seeder;

/**
 * Sample dashboard activity (donors, donations, volunteers, members, enquiries) for local testing.
 * Skipped automatically when APP_ENV=production. All people below are fictional.
 */
class DemoActivitySeeder extends Seeder
{
    public function run(): void
    {
        if (Donation::count() > 0) {
            return;
        }

        $names = ['Aarav Sharma', 'Priya Verma', 'Rohit Singh', 'Neha Gupta', 'Vikas Yadav', 'Anjali Mishra', 'Suresh Tiwari', 'Kavita Pandey', 'Amit Srivastava', 'Pooja Dubey'];
        $donors = collect($names)->map(fn ($n, $i) => Donor::create([
            'name' => $n,
            'email' => strtolower(str_replace(' ', '.', $n)).'@example.com',
            'phone' => '98'.str_pad((string) (10000000 + $i * 7919), 8, '0', STR_PAD_LEFT),
            'city' => ['Gonda', 'Lucknow', 'Ayodhya', 'Bahraich', 'Basti'][$i % 5],
            'state' => 'Uttar Pradesh',
        ]));

        $campaigns = Campaign::pluck('id')->all();
        $methods = ['upi', 'upi', 'bank_transfer', 'cash', 'upi', 'cheque'];
        $amounts = [500, 1000, 2100, 5000, 1100, 251, 10000, 1500, 3000, 501];

        for ($i = 0; $i < 34; $i++) {
            $status = $i < 28 ? 'confirmed' : ($i < 32 ? 'pending' : 'failed');
            Donation::create([
                'donor_id' => $donors[$i % $donors->count()]->id,
                'campaign_id' => $i % 3 === 0 ? null : $campaigns[$i % max(1, count($campaigns))] ?? null,
                'amount' => $amounts[$i % count($amounts)],
                'method' => $methods[$i % count($methods)],
                'transaction_ref' => $status === 'confirmed' ? 'UTR'.(400000000 + $i * 1237) : null,
                'status' => $status,
                'source' => $i % 2 ? 'website' : 'offline',
                'wants_80g' => $i % 4 === 0,
                'donated_at' => now()->subDays(($i * 11) % 330)->setTime(9 + $i % 8, ($i * 7) % 60),
            ]);
        }

        foreach (array_slice($names, 0, 6) as $i => $n) {
            Volunteer::create([
                'name' => $n, 'email' => 'vol'.$i.'@example.com', 'phone' => '97'.str_pad((string) (2000000 + $i * 31), 8, '0', STR_PAD_LEFT),
                'city' => 'Gonda', 'occupation' => ['Student', 'Teacher', 'Engineer', 'Homemaker', 'Doctor', 'Retired'][$i],
                'availability' => ['Weekends', 'Evenings', 'Flexible', 'Events only', 'Weekdays', 'Remote / online'][$i],
                'interests' => [['Education & Skills', 'Sports'], ['Healthcare'], ['Environment', 'Photography & Video'], ['Women Empowerment'], ['Healthcare', 'Disaster Relief'], ['Arts & Culture']][$i],
                'status' => $i < 3 ? 'approved' : 'new',
                'created_at' => now()->subDays($i * 9),
            ]);
        }

        foreach (array_slice($names, 4, 4) as $i => $n) {
            Member::create([
                'name' => $n, 'email' => 'member'.$i.'@example.com', 'phone' => '96'.str_pad((string) (3000000 + $i * 17), 8, '0', STR_PAD_LEFT),
                'city' => 'Gonda', 'address' => 'Gonda, Uttar Pradesh', 'membership_type' => ['general', 'life', 'student', 'general'][$i],
                'status' => $i < 2 ? 'approved' : 'pending', 'membership_no' => $i < 2 ? 'LUF-M-000'.($i + 1) : null,
                'valid_till' => $i === 0 ? now()->addYear() : null, 'created_at' => now()->subDays($i * 13),
            ]);
        }

        foreach ([
            ['contact', 'Ritu Saxena', 'Want to sponsor a health camp', 'Hello, our family would like to sponsor a health camp in memory of my father. Please share the details.'],
            ['partnership', 'CSR Desk, Sample Industries Pvt Ltd', 'CSR Partnership', 'We are exploring education projects in eastern UP for our CSR budget this year and would like to discuss a partnership.'],
            ['internship', 'Shivani Patel', 'Social Media & Content Intern', 'I am a second-year BJMC student and would love to help with your social media and documentation.'],
            ['contact', 'Mohit Agarwal', 'Donating old books', 'I have around 200 school books in good condition. Can I donate them to your learning centre?'],
            ['career', 'Deepak Kumar', 'Programme Coordinator – Education', 'I have three years of experience running community learning centres and would like to apply.'],
        ] as $i => [$type, $name, $subject, $message]) {
            Enquiry::create([
                'type' => $type, 'name' => $name, 'email' => 'enquiry'.$i.'@example.com', 'phone' => '95'.str_pad((string) (4000000 + $i), 8, '0', STR_PAD_LEFT),
                'subject' => $subject, 'message' => $message, 'status' => $i < 3 ? 'new' : 'replied', 'created_at' => now()->subDays($i * 2),
                'organisation' => $type === 'partnership' ? 'Sample Industries Pvt Ltd' : null,
            ]);
        }
    }
}
