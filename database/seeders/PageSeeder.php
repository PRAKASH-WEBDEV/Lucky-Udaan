<?php

namespace Database\Seeders;

use App\Models\Page;
use Illuminate\Database\Seeder;

/**
 * Hero text, body copy and SEO for every public page. Editable under Dashboard → Content → Pages.
 * Existing pages are left untouched so re-seeding never overwrites edits.
 */
class PageSeeder extends Seeder
{
    public function run(): void
    {
        foreach ($this->pages() as $slug => [$title, $subtitle, $metaTitle, $metaDescription, $body]) {
            Page::firstOrCreate(['slug' => $slug], [
                'title' => $title,
                'subtitle' => $subtitle,
                'meta_title' => $metaTitle,
                'meta_description' => $metaDescription,
                'body' => $body,
                'is_active' => true,
            ]);
        }
    }

    private function pages(): array
    {
        return [
            'home' => ['Home', null, null, null, null],

            'about-us' => [
                'About Lucky Udaan Foundation',
                'A not-for-profit organisation from Gonda, Uttar Pradesh — working so that every child, woman, elder and living being can rise.',
                'About Us – Section 8 NGO in Gonda, Uttar Pradesh',
                'Learn about Lucky Udaan Foundation, a Section 8 not-for-profit company in Gonda, UP, working for education, healthcare, women empowerment, environment and community welfare.',
                <<<'HTML'
<h2>Our story</h2>
<p><em>Udaan</em> means flight. Lucky Udaan Foundation was started in Gonda, Uttar Pradesh, with one simple belief — that every person deserves the chance to rise, whatever their circumstances. A child who cannot afford books, a mother who wants to earn with dignity, an elderly neighbour living alone, a stray animal in need of care: each of them is a reason we exist.</p>
<p>We are registered as a <strong>Section 8 company under the Companies Act, 2013</strong>, which means we operate entirely without a profit motive. Every rupee and every hour given to us is used to further our charitable objectives.</p>
<h2>What drives us</h2>
<p>Our work spans education and skills, healthcare, women empowerment, care homes, animal welfare, food and relief, environment, civic development, road safety, disaster relief, agriculture, sports and the arts. These are not separate projects — they are connected parts of a single goal: strong, healthy and self-reliant communities.</p>
<p>We work hand in hand with local authorities, schools, colleges, trusts, companies and fellow NGOs, because lasting change is always a shared effort.</p>
<h2>How we work</h2>
<ul>
<li><strong>Community first</strong> — we listen before we act, and design programmes with the people they serve.</li>
<li><strong>Transparency</strong> — we publish our registrations and reports, and acknowledge every donation.</li>
<li><strong>Dignity</strong> — help should empower, never embarrass.</li>
<li><strong>Measurable impact</strong> — we track outcomes and document what works.</li>
</ul>
HTML,
            ],

            'programs' => [
                'Our Programs',
                'Fourteen focus areas, one promise — a better tomorrow for Gonda and beyond.',
                'Our Programs – Education, Health, Women Empowerment & More',
                'Explore Lucky Udaan Foundation’s programs in Gonda, UP: education and skills, healthcare, women empowerment, care homes, animal welfare, food relief, environment and more.',
                '<p>Each program grows out of a real need we see on the ground. Choose an area to learn what we do and how you can help.</p>',
            ],

            'campaigns' => [
                'Campaigns',
                'Time-bound drives with clear goals — pick a cause and help us reach it.',
                'Donation Campaigns – Support a Cause in Gonda',
                'Support active fundraising campaigns by Lucky Udaan Foundation for education, health camps, women’s livelihoods and winter relief in Gonda, Uttar Pradesh.',
                null,
            ],

            'events' => [
                'Events',
                'Health camps, plantation drives, workshops and celebrations — come and be part of them.',
                'Upcoming Events – Health Camps, Drives & Workshops in Gonda',
                'See upcoming and past events by Lucky Udaan Foundation in Gonda, Uttar Pradesh — health camps, awareness drives, plantation drives and workshops.',
                null,
            ],

            'blog' => [
                'Blog & News',
                'Stories, updates and ideas from our work in the community.',
                'Blog & News – Lucky Udaan Foundation',
                'Read the latest news, stories and updates from Lucky Udaan Foundation’s programs in Gonda, Uttar Pradesh.',
                null,
            ],

            'announcements' => [
                'Announcements',
                'Important notices, registrations and updates from the foundation.',
                'Announcements – Lucky Udaan Foundation',
                'Official announcements from Lucky Udaan Foundation, Gonda — registrations, notices and programme updates.',
                null,
            ],

            'press-releases' => [
                'Press Releases',
                'Official statements and releases for the media.',
                'Press Releases – Lucky Udaan Foundation',
                'Official press releases from Lucky Udaan Foundation, a not-for-profit organisation in Gonda, Uttar Pradesh.',
                null,
            ],

            'transparency' => [
                'Transparency & Accountability',
                'We believe every supporter has the right to know how their contribution is used.',
                'Transparency – Registrations, Reports & Accountability',
                'Lucky Udaan Foundation’s legal registrations, annual reports and financial practices. A Section 8 not-for-profit company in Gonda, UP.',
                <<<'HTML'
<h2>Our commitment</h2>
<p>As a Section 8 company, Lucky Udaan Foundation is prohibited by law from distributing any profit or dividend. All income is applied solely to promote our charitable objectives.</p>
<ul>
<li>Every donation is recorded and acknowledged with a receipt.</li>
<li>Accounts are maintained as required under the Companies Act, 2013 and audited annually by a chartered accountant.</li>
<li>Annual reports and audited financial statements are published on this website once finalised.</li>
<li>Funds received for a specific campaign are used for that purpose.</li>
<li>Our board reviews programme spending and outcomes regularly.</li>
</ul>
<p>If you have any question about how funds are used, please write to us — we are happy to answer.</p>
HTML,
            ],

            'contact-us' => [
                'Contact Us',
                'Questions, ideas or want to help? We’d love to hear from you.',
                'Contact Lucky Udaan Foundation – NGO in Gonda, UP',
                'Contact Lucky Udaan Foundation at Civil Line, Mandenala, Ambedkar Chauraha, Gonda, Uttar Pradesh 271001. Call 9214748702 or email luckyudaanfoundation@gmail.com.',
                '<p>Visit our office in Gonda, call or WhatsApp us, or send a message using the form. We usually reply within two working days.</p>',
            ],

            'volunteer' => [
                'Volunteer With Us',
                'Give a few hours, change a life — and discover what you’re capable of.',
                'Volunteer in Gonda – Join Lucky Udaan Foundation',
                'Volunteer with Lucky Udaan Foundation in Gonda, UP. Teach children, support health camps, plant trees, help in relief work or lend your professional skills.',
                <<<'HTML'
<p>Volunteers are the heart of everything we do. Whether you are a student, a working professional, a homemaker or retired, there is meaningful work waiting for you.</p>
<p>Teach a class, help at a health camp, join a plantation drive, design a poster, take photographs, or share your professional expertise in law, finance or technology. We will match you with work that fits your time and skills.</p>
HTML,
            ],

            'membership' => [
                'Become a Member',
                'Join the Lucky Udaan family and help shape the work we do.',
                'Membership – Join Lucky Udaan Foundation',
                'Become a member of Lucky Udaan Foundation, Gonda. General, student, life and patron memberships for people who want to support our mission.',
                '<p>Members are part of our extended family. They support our mission, take part in our activities and help spread the word about our work. Choose the membership that suits you and fill in the application — our team will confirm by email.</p>',
            ],

            'careers' => [
                'Careers',
                'Build a career with purpose — work where it matters most.',
                'Careers at Lucky Udaan Foundation – NGO Jobs in Gonda',
                'Explore job opportunities at Lucky Udaan Foundation in Gonda, Uttar Pradesh. Work in education, health, community development and programme management.',
                '<p>We are a growing team of people who care deeply about their community. We engage professionals across programme management, field coordination, teaching, healthcare, communications and finance. If you want your work to make a visible difference, we would love to hear from you.</p>',
            ],

            'internships' => [
                'Internships',
                'Real-world experience for students who want to learn by doing.',
                'Internships at Lucky Udaan Foundation – Social Work, Media & More',
                'Apply for internships at Lucky Udaan Foundation, Gonda — social work, education, research, social media and content. Certificate on completion.',
                '<p>Our internships give students hands-on exposure to community work — from field surveys and awareness campaigns to research, documentation and digital outreach. Interns work alongside our team and receive a certificate on successful completion.</p>',
            ],

            'gallery' => [
                'Gallery',
                'Moments from our classrooms, camps, drives and celebrations.',
                'Photo Gallery – Lucky Udaan Foundation in Action',
                'Photos and videos of Lucky Udaan Foundation’s work in Gonda, Uttar Pradesh — education, health camps, women empowerment, environment and relief.',
                null,
            ],

            'our-team' => [
                'Our Team',
                'The people who give their time, skill and heart to the foundation.',
                'Our Team – Lucky Udaan Foundation',
                'Meet the directors, team members and advisors of Lucky Udaan Foundation, Gonda, Uttar Pradesh.',
                null,
            ],

            'success-stories' => [
                'Success Stories',
                'Real change, one life at a time.',
                'Success Stories – Lives Changed by Lucky Udaan Foundation',
                'Read stories of children, women and families whose lives have changed through Lucky Udaan Foundation’s programs in Gonda.',
                null,
            ],

            'faq' => [
                'Frequently Asked Questions',
                'Quick answers about donating, volunteering and our work.',
                'FAQ – Donations, Volunteering & More',
                'Answers to common questions about donating to, volunteering with and partnering with Lucky Udaan Foundation, Gonda.',
                null,
            ],

            'certifications' => [
                'Certifications & Registrations',
                'Our legal registrations and approvals.',
                'Certifications – Section 8, 12A & 80G Registration',
                'Registration and certification details of Lucky Udaan Foundation — Section 8 company incorporation, 12A and 80G registrations.',
                null,
            ],

            'media' => [
                'Media',
                'Lucky Udaan Foundation in the news, plus videos from the field.',
                'Media Coverage & Videos – Lucky Udaan Foundation',
                'News coverage, videos and media resources about Lucky Udaan Foundation, Gonda, Uttar Pradesh.',
                null,
            ],

            'partnership' => [
                'Partner With Us',
                'Together we can reach further — CSR, institutional and community partnerships.',
                'CSR & Partnerships – Partner with an NGO in Gonda',
                'Partner with Lucky Udaan Foundation for CSR projects, government collaborations, educational partnerships and employee volunteering in Gonda, UP.',
                '<p>We collaborate with government bodies, educational institutions, trusts, companies and fellow NGOs to deliver programmes at scale. As a Section 8 company, we can work with corporate partners on CSR projects with clear goals, regular reporting and on-ground visibility.</p>',
            ],

            'annual-reports' => [
                'Annual Reports',
                'Our activities, outcomes and finances, year by year.',
                'Annual Reports & Financial Statements',
                'Download annual reports and audited financial statements of Lucky Udaan Foundation, Gonda.',
                null,
            ],

            'testimonials' => [
                'Testimonials',
                'In the words of the people we work with.',
                'Testimonials – What People Say About Lucky Udaan Foundation',
                'Read what beneficiaries, volunteers and supporters say about Lucky Udaan Foundation’s work in Gonda.',
                null,
            ],

            'donate' => [
                'Donate Now',
                'Your gift brings books to a child, medicine to a patient and a livelihood to a family.',
                'Donate to an NGO in Gonda – Support Lucky Udaan Foundation',
                'Donate to Lucky Udaan Foundation by UPI or bank transfer and support education, healthcare, women empowerment and relief work in Gonda, Uttar Pradesh.',
                null,
            ],

            'privacy-policy' => [
                'Privacy Policy',
                'How we collect, use and protect your personal information.',
                'Privacy Policy',
                'Privacy policy of Lucky Udaan Foundation — how we collect, use and protect personal information shared on our website.',
                <<<'HTML'
<p>Lucky Udaan Foundation (“we”, “us”) respects your privacy. This policy explains what information we collect through this website and how we use it.</p>
<h2>Information we collect</h2>
<ul>
<li>Details you submit through our forms — such as your name, email, phone number, address, PAN (for tax receipts), and messages.</li>
<li>Documents you upload, such as a CV when applying for a job or internship.</li>
<li>Basic technical information such as your IP address and browser type, used for security and to prevent spam.</li>
</ul>
<h2>How we use it</h2>
<ul>
<li>To respond to your enquiries and applications.</li>
<li>To record donations and issue receipts, including 80G receipts where applicable.</li>
<li>To coordinate volunteering, membership and partnership activities.</li>
<li>To meet our legal and statutory obligations.</li>
</ul>
<h2>Sharing</h2>
<p>We do not sell or rent your personal information. We share it only where required by law (for example, with tax authorities for donation reporting) or with service providers who help us run this website, under confidentiality obligations.</p>
<h2>Security and retention</h2>
<p>We use reasonable technical and organisational measures to protect your data. Personal information is kept only for as long as needed for the purposes above or as required by law.</p>
<h2>Your choices</h2>
<p>You may ask us to access, correct or delete your personal information, or to stop contacting you, by writing to us at the email address below.</p>
<h2>Cookies</h2>
<p>This website uses only essential cookies needed for security and form submissions. If analytics is enabled, it is used in aggregate to understand how visitors use the site.</p>
<h2>Changes</h2>
<p>We may update this policy from time to time. The latest version will always be available on this page.</p>
HTML,
            ],

            'terms-and-conditions' => [
                'Terms & Conditions',
                'The terms that apply when you use this website.',
                'Terms & Conditions',
                'Terms and conditions for using the Lucky Udaan Foundation website and making donations.',
                <<<'HTML'
<p>By using this website you agree to these terms. If you do not agree, please do not use the site.</p>
<h2>About us</h2>
<p>Lucky Udaan Foundation is a not-for-profit company registered under Section 8 of the Companies Act, 2013, with its office in Gonda, Uttar Pradesh.</p>
<h2>Use of the website</h2>
<ul>
<li>Content on this website is provided for general information about our work.</li>
<li>You agree not to misuse the website, attempt unauthorised access, or submit false or misleading information.</li>
<li>Text, photographs and logos on this website belong to Lucky Udaan Foundation or are used with permission, and may not be reused without written consent.</li>
</ul>
<h2>Donations</h2>
<ul>
<li>Donations are voluntary and are used for the charitable objectives of the foundation.</li>
<li>Funds given for a specific campaign are applied to that campaign; if a campaign is over-subscribed or cannot be completed, funds are used for a closely related purpose.</li>
<li>Receipts are issued after the payment is confirmed in our bank account. Tax-exemption receipts are subject to our registration status and applicable law.</li>
<li>Please see our Refund Policy for information on refunds.</li>
</ul>
<h2>Volunteering and membership</h2>
<p>Submitting an application does not guarantee selection. We may accept or decline applications at our discretion.</p>
<h2>Liability</h2>
<p>We make every effort to keep information accurate, but we do not guarantee that the website is error-free or always available, and we are not liable for any loss arising from its use.</p>
<h2>Governing law</h2>
<p>These terms are governed by the laws of India. Courts at Gonda, Uttar Pradesh shall have jurisdiction.</p>
HTML,
            ],

            'refund-policy' => [
                'Refund Policy',
                'Our policy on refunds of donations made in error.',
                'Donation Refund Policy',
                'Refund and cancellation policy for donations made to Lucky Udaan Foundation.',
                <<<'HTML'
<p>We are grateful for every contribution. Donations are generally non-refundable because they are applied to our charitable work soon after they are received. We understand, however, that mistakes can happen.</p>
<h2>When we can refund</h2>
<ul>
<li>A duplicate payment was made by mistake.</li>
<li>An incorrect amount was transferred (for example, an extra zero).</li>
<li>A payment was made without the account holder’s authorisation.</li>
</ul>
<h2>How to request a refund</h2>
<p>Write to us within <strong>15 days</strong> of the donation, with your name, the date, amount, transaction / UTR reference and the reason for the request. We will review the request and respond within 7 working days.</p>
<h2>How refunds are made</h2>
<p>Approved refunds are returned to the original payment source (bank account or UPI ID). Any receipt issued for the refunded amount will be cancelled, and no tax deduction may be claimed for it.</p>
<p>Refunds cannot be made once a tax-exemption receipt has been used in a tax return, or after funds have been utilised for a specific campaign.</p>
HTML,
            ],
        ];
    }
}
