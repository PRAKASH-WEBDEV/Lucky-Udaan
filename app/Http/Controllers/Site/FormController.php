<?php

namespace App\Http\Controllers\Site;

use App\Http\Controllers\Controller;
use App\Http\Requests\Site\ApplicationRequest;
use App\Http\Requests\Site\ContactRequest;
use App\Http\Requests\Site\DonationRequest;
use App\Http\Requests\Site\MembershipRequest;
use App\Http\Requests\Site\PartnershipRequest;
use App\Http\Requests\Site\VolunteerRequest;
use App\Mail\SubmissionReceived;
use App\Models\Campaign;
use App\Models\Donation;
use App\Models\Donor;
use App\Models\Enquiry;
use App\Models\Member;
use App\Models\Opening;
use App\Models\Volunteer;
use App\Support\Uploader;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\URL;

/** Pages with forms, and their submissions (validated, stored, then shown in the dashboard). */
class FormController extends Controller
{
    // ------------------------------------------------------------ pages

    public function contact()
    {
        seo()->schema(['@context' => 'https://schema.org', '@type' => 'ContactPage', 'name' => 'Contact '.setting('site_name'), 'url' => route('contact')]);

        return view('site.forms.contact', ['page' => PageController::page('contact-us', 'Contact Us')]);
    }

    public function volunteer()
    {
        return view('site.forms.volunteer', ['page' => PageController::page('volunteer', 'Volunteer With Us')]);
    }

    public function membership()
    {
        return view('site.forms.membership', ['page' => PageController::page('membership', 'Become a Member')]);
    }

    public function careers()
    {
        return view('site.forms.careers', [
            'page' => PageController::page('careers', 'Careers'),
            'kind' => 'career',
            'openings' => Opening::open()->where('type', 'job')->latest()->get(),
        ]);
    }

    public function internships()
    {
        return view('site.forms.careers', [
            'page' => PageController::page('internships', 'Internships'),
            'kind' => 'internship',
            'openings' => Opening::open()->where('type', 'internship')->latest()->get(),
        ]);
    }

    public function partnership()
    {
        return view('site.forms.partnership', ['page' => PageController::page('partnership', 'Partner With Us')]);
    }

    public function donate(Request $request)
    {
        $campaigns = Campaign::active()->latest()->get(['id', 'title', 'slug']);

        return view('site.forms.donate', [
            'page' => PageController::page('donate', 'Donate Now'),
            'campaigns' => $campaigns,
            'selectedCampaign' => $campaigns->firstWhere('slug', $request->get('campaign'))?->id,
            'amounts' => array_values(array_filter(array_map('intval', explode(',', (string) setting('donation_amounts', '500,1000,2500,5000'))))),
        ]);
    }

    public function donateThanks(Request $request, string $receipt)
    {
        abort_unless($request->hasValidSignature(), 403);
        $donation = Donation::with('campaign')->where('receipt_no', $receipt)->firstOrFail();
        seo()->set('Thank you for your donation')->noindex = true;

        return view('site.forms.donate-thanks', compact('donation'));
    }

    // ------------------------------------------------------------ submissions

    public function submitContact(ContactRequest $request)
    {
        $data = $request->validated();
        $enquiry = Enquiry::create($data + ['type' => 'contact', 'ip' => $request->ip()]);
        $this->notify('New contact message', $data, $enquiry);

        return back()->with('success', 'Thank you for reaching out, '.e($data['name']).'! Our team will get back to you within 2 working days.');
    }

    public function submitVolunteer(VolunteerRequest $request)
    {
        $data = collect($request->validated())->except('consent')->all();
        $volunteer = Volunteer::create($data);
        $this->notify('New volunteer application', $data, null, route('admin.volunteers.edit', $volunteer->id));

        return back()->with('success', 'Welcome aboard, '.e($data['name']).'! We have received your volunteer application and will contact you soon.');
    }

    public function submitMembership(MembershipRequest $request)
    {
        $data = collect($request->validated())->except('consent')->all();
        $member = Member::create($data + ['status' => 'pending']);
        $this->notify('New membership application', $data, null, route('admin.members.edit', $member->id));

        return back()->with('success', 'Thank you, '.e($data['name']).'! Your membership application is under review. We will confirm by email.');
    }

    public function submitApplication(ApplicationRequest $request, string $kind)
    {
        $data = $request->validated();
        $opening = isset($data['opening_id']) ? Opening::find($data['opening_id']) : null;

        $enquiry = Enquiry::create([
            'type' => $kind,
            'opening_id' => $opening?->id,
            'name' => $data['name'],
            'email' => $data['email'],
            'phone' => $data['phone'],
            'subject' => $opening?->title ?? ($kind === 'internship' ? 'Internship application' : 'General job application'),
            'message' => $data['message'],
            'data' => array_filter([
                'qualification' => $data['qualification'],
                'institution' => $data['institution'] ?? null,
                'city' => $data['city'] ?? null,
                'portfolio' => $data['portfolio'] ?? null,
            ]),
            'attachment' => $request->hasFile('cv') ? Uploader::storePrivate($request->file('cv'), 'applications') : null,
            'ip' => $request->ip(),
        ]);
        $this->notify($kind === 'internship' ? 'New internship application' : 'New job application', collect($data)->except(['cv', 'consent', 'opening_id'])->all() + ['position' => $enquiry->subject], $enquiry);

        return back()->with('success', 'Thank you for applying, '.e($data['name']).'! We review every application and will contact shortlisted candidates.');
    }

    public function submitPartnership(PartnershipRequest $request)
    {
        $data = $request->validated();
        $enquiry = Enquiry::create([
            'type' => 'partnership',
            'name' => $data['name'],
            'email' => $data['email'],
            'phone' => $data['phone'],
            'organisation' => $data['organisation'],
            'subject' => $data['partnership_type'],
            'message' => $data['message'],
            'data' => array_filter(['designation' => $data['designation'] ?? null, 'website' => $data['website_url'] ?? null]),
            'ip' => $request->ip(),
        ]);
        $this->notify('New partnership enquiry', $data, $enquiry);

        return back()->with('success', 'Thank you! Our partnerships team will reach out to '.e($data['organisation']).' shortly.');
    }

    public function submitDonation(DonationRequest $request)
    {
        $data = $request->validated();

        $donation = DB::transaction(function () use ($data) {
            $donor = Donor::query()
                ->where(fn ($q) => $q->where('email', $data['email'])->orWhere('phone', $data['phone']))
                ->first() ?? new Donor;
            $donor->fill(array_filter([
                'name' => $data['name'],
                'email' => $data['email'],
                'phone' => $data['phone'],
                'pan' => isset($data['pan']) ? strtoupper($data['pan']) : null,
                'address' => $data['address'] ?? null,
                'city' => $data['city'] ?? null,
            ]))->save();

            return Donation::create([
                'donor_id' => $donor->id,
                'campaign_id' => $data['campaign_id'] ?? null,
                'amount' => $data['amount'],
                'method' => $data['method'],
                'transaction_ref' => $data['transaction_ref'] ?? null,
                'wants_80g' => (bool) ($data['wants_80g'] ?? false),
                'message' => $data['message'] ?? null,
                'status' => 'pending',
                'source' => 'website',
            ]);
        });

        $this->notify('New donation pledge '.inr($donation->amount), collect($data)->except('consent')->all() + ['receipt_no' => $donation->receipt_no],
            null, route('admin.donations.edit', $donation->id));

        return redirect()->to(URL::temporarySignedRoute('donate.thanks', now()->addDays(7), ['receipt' => $donation->receipt_no]));
    }

    /** Email the NGO inbox. Failures are logged, never shown to the visitor. */
    private function notify(string $heading, array $fields, ?Enquiry $enquiry = null, ?string $adminUrl = null): void
    {
        $to = setting('email');
        if (! $to) {
            return;
        }
        $labelled = [];
        foreach ($fields as $k => $v) {
            $labelled[ucwords(str_replace('_', ' ', $k))] = $v;
        }
        try {
            Mail::to($to)->send(new SubmissionReceived($heading, $labelled,
                $adminUrl ?? ($enquiry ? route('admin.enquiries.edit', $enquiry->id) : null), $fields['email'] ?? null));
        } catch (\Throwable $e) {
            Log::warning('Submission email failed: '.$e->getMessage());
        }
    }
}
