<?php

namespace Database\Seeders;

use App\Models\Banner;
use App\Models\Campaign;
use App\Models\Certification;
use App\Models\Event;
use App\Models\Faq;
use App\Models\GalleryItem;
use App\Models\Opening;
use App\Models\Post;
use App\Models\Program;
use App\Models\SuccessStory;
use App\Models\TeamMember;
use App\Models\Testimonial;
use Illuminate\Database\Seeder;

/**
 * Sample website content so the site looks complete on day one.
 * Items marked "sample" in the README should be replaced with real content before launch.
 */
class ContentSeeder extends Seeder
{
    public function run(): void
    {
        $program = fn (string $slug) => Program::where('slug', $slug)->value('id');

        // ---------------------------------------------------------- banners
        if (Banner::count() === 0) {
            foreach ([
                ['Education for every child', 'Every child deserves a classroom, a book and a dream', 'We help children in Gonda stay in school with learning support, books and caring mentors.', 'seed/photos/photo1.webp', 'Sponsor a child’s education', '/donate', 'Our programs', '/programs'],
                ['Health for all', 'Health camp today, healthy Gonda tomorrow', 'Free medical check-ups, medicines and health awareness — right at the village doorstep.', 'seed/photos/photo2.webp', 'Support a health camp', '/donate', 'Upcoming events', '/events'],
                ['Atmanirbhar Nari, Atmanirbhar Bharat', 'Self-reliant women build a self-reliant India', 'Self-help groups, tailoring and skill training that help women earn with dignity.', 'seed/photos/photo8.webp', 'Empower a woman', '/donate', 'Volunteer with us', '/volunteer'],
            ] as $i => [$eyebrow, $title, $sub, $img, $b1, $l1, $b2, $l2]) {
                Banner::create(['eyebrow' => $eyebrow, 'title' => $title, 'subtitle' => $sub, 'image' => $img, 'button_text' => $b1, 'button_link' => $l1,
                    'button2_text' => $b2, 'button2_link' => $l2, 'sort_order' => $i + 1, 'is_active' => true]);
            }
        }

        // ---------------------------------------------------------- campaigns
        foreach ([
            ['Books & bags for 500 children', 'education-and-skills', 'seed/photos/photo6.webp', 250000, 120,
                'Help us give 500 children from low-income families in Gonda a school bag, notebooks, stationery and story books for the new session.',
                ['For many families, the cost of a bag and notebooks decides whether a child goes to school. ₹500 equips one child for the whole year.', 'Kits are distributed at community events, and every distribution is documented with photos on our website.']],
            ['Free health camps in rural Gonda', 'healthcare', 'seed/photos/photo2.webp', 150000, 90,
                'Fund a series of free medical camps with doctors, basic tests and medicines for villages far from a hospital.',
                ['Each camp serves 150–250 people with general check-ups, BP and sugar screening, eye check-ups and free medicines.', '₹15,000 covers the cost of one full camp, including medicines and referral support.']],
            ['Sewing machines for 50 women', 'women-empowerment', 'seed/photos/photo8.webp', 300000, 150,
                'Give trained women a sewing machine of their own — and the chance to earn a steady income from home.',
                ['Women who complete our tailoring course are ready to work but cannot afford a machine. ₹6,000 gives one woman her own sewing machine and starter kit.', 'Many of our trainees go on to stitch school uniforms and garments for their neighbourhood.']],
            ['Warm blankets this winter', 'food-and-relief', 'seed/photos/photo5.webp', 100000, 45,
                'Protect elderly people and homeless families from the bitter winter cold with blankets and warm clothing.',
                ['Winter nights in Gonda can drop close to freezing. ₹400 provides one thick blanket to someone sleeping out in the cold.', 'Our volunteers identify those most in need and distribute blankets personally.']],
        ] as $i => [$title, $prog, $img, $goal, $days, $excerpt, $paras]) {
            Campaign::firstOrCreate(['title' => $title], [
                'program_id' => $program($prog), 'image' => $img, 'goal_amount' => $goal, 'raised_amount' => 0,
                'start_date' => now()->subDays(30), 'end_date' => now()->addDays($days),
                'excerpt' => $excerpt, 'description' => '<p>'.implode('</p><p>', $paras).'</p>',
                'is_featured' => $i < 3, 'is_active' => true,
            ]);
        }

        // ---------------------------------------------------------- posts
        foreach ([
            ['blog', 'Why a school bag can change a child’s future', 'seed/photos/photo6.webp', 12,
                'For a child from a low-income family, the right school supplies can be the difference between attending school and dropping out.',
                ['When we began visiting homes in and around Gonda, one reason for children missing school came up again and again: they simply did not have a bag, notebooks or a pencil box.', 'A complete kit costs about ₹500. For a daily-wage family, that is several days of income. By providing these basics, we remove one of the quiet barriers that keep children out of the classroom.', '<strong>How you can help:</strong> sponsor a kit through our Books & Bags campaign, or volunteer at a distribution day.']],
            ['blog', 'Five simple hygiene habits that prevent disease', 'seed/photos/photo2.webp', 26,
                'Small daily habits can prevent many common illnesses. Here are five we share at every health camp.',
                ['At our health camps we see many cases of stomach infections, skin problems and seasonal illnesses that can be prevented with simple habits.', '<strong>1.</strong> Wash hands with soap before eating and after using the toilet. <strong>2.</strong> Drink boiled or filtered water. <strong>3.</strong> Keep food covered. <strong>4.</strong> Trim nails and bathe daily. <strong>5.</strong> Keep surroundings free of stagnant water.', 'Teaching these habits to children is one of the most effective ways to keep the whole family healthy.']],
            ['blog', 'Learning a skill, earning with dignity', 'seed/photos/photo8.webp', 40,
                'How tailoring and vocational training help women in Gonda build confidence and income.',
                ['Skill training is about much more than a certificate. For many women, it is the first time they have earned money of their own.', 'Our tailoring course covers measurement, cutting, stitching and finishing, followed by guidance on finding customers and pricing work fairly.']],
            ['news', 'Digital literacy classes begin for youth', 'seed/photos/photo7.webp', 8,
                'A new batch of young people has started learning computer basics, internet safety and online services.',
                ['Our digital literacy classes cover typing, documents, email, safe internet use and accessing government services online.', 'Classes run in small batches so that every student gets hands-on practice.']],
            ['news', 'Tree plantation drive with local youth', 'seed/photos/photo4.webp', 20,
                'Volunteers came together to plant saplings and pledge to care for them through the year.',
                ['Planting is only the first step — every sapling is adopted by a volunteer who waters and protects it.', 'We thank all the young volunteers who joined the drive.']],
            ['announcement', 'Volunteer registrations are now open', null, 3,
                'We are welcoming new volunteers for teaching, health camps, environment drives and social media.',
                ['If you would like to give a few hours a week to your community, register on our Volunteer page. Our team will contact you to discuss your interests and availability.']],
            ['announcement', 'Internship applications invited', null, 15,
                'Students of social work, education, mass communication and allied fields can apply for our internship programme.',
                ['Interns work on field surveys, awareness campaigns, documentation and digital outreach. A certificate is provided on successful completion. Apply on the Internships page.']],
            ['press', 'Lucky Udaan Foundation launches its website and online volunteer registration', null, 1,
                'The Gonda-based not-for-profit organisation has launched its official website to make it easier for people to volunteer, donate and partner.',
                ['Gonda, Uttar Pradesh — Lucky Udaan Foundation, a Section 8 not-for-profit company working in education, healthcare, women empowerment and community welfare, has launched its official website.', 'The website allows citizens to register as volunteers, apply for membership and internships, learn about the foundation’s 14 focus areas and support active campaigns.', 'For media enquiries, contact the foundation at the details given on the Contact page.']],
        ] as [$type, $title, $img, $daysAgo, $excerpt, $paras]) {
            Post::firstOrCreate(['title' => $title], [
                'type' => $type, 'image' => $img, 'excerpt' => $excerpt, 'author' => 'Team Lucky Udaan',
                'body' => '<p>'.implode('</p><p>', $paras).'</p>', 'published_at' => now()->subDays($daysAgo)->setTime(10, 0),
                'is_active' => true, 'is_featured' => $type === 'blog',
            ]);
        }

        // ---------------------------------------------------------- events
        foreach ([
            ['Free health check-up camp', 'seed/photos/photo2.webp', 16, 6, 'Community hall, Gonda', 'Free general check-up, BP and sugar screening and medicines. Open to all.'],
            ['Tree plantation drive', 'seed/photos/photo4.webp', 30, 4, 'Gonda', 'Join us to plant and adopt saplings. Gloves and saplings provided — just bring your enthusiasm.'],
            ['Winter blanket distribution', 'seed/photos/photo5.webp', 45, 5, 'Civil Line, Gonda', 'Distribution of blankets and warm clothes to elderly people and families in need.'],
            ['Road safety awareness workshop', null, 60, 3, 'Gonda', 'Traffic rules, helmet awareness and first aid for school and college students.'],
            ['Women’s self-help group meet', 'seed/photos/photo3.webp', -20, 3, 'Gonda', 'Members shared progress, savings updates and plans for new livelihood activities.'],
        ] as [$title, $img, $inDays, $hours, $venue, $excerpt]) {
            $start = now()->addDays($inDays)->setTime(10, 0);
            Event::firstOrCreate(['title' => $title], [
                'image' => $img, 'starts_at' => $start, 'ends_at' => $start->copy()->addHours($hours), 'venue' => $venue,
                'excerpt' => $excerpt, 'description' => '<p>'.$excerpt.'</p><p>To volunteer at this event or for more details, please contact us.</p>', 'is_active' => true,
            ]);
        }

        // ---------------------------------------------------------- gallery
        if (GalleryItem::count() === 0) {
            foreach ([
                ['Children learning in a support class', 'Education', 1], ['Free health check-up camp', 'Health', 2],
                ['Women’s self-help group meeting', 'Women Empowerment', 3], ['Volunteers planting saplings', 'Environment', 4],
                ['Blankets for elders in winter', 'Relief', 5], ['Books for school children', 'Education', 6],
                ['Digital literacy class', 'Education', 7], ['Tailoring and skill training', 'Women Empowerment', 8],
            ] as $i => [$title, $cat, $n]) {
                GalleryItem::create(['title' => $title, 'category' => $cat, 'type' => 'photo', 'image' => "seed/photos/photo$n.webp", 'sort_order' => $i + 1, 'is_active' => true]);
            }
        }

        // ---------------------------------------------------------- team
        TeamMember::firstOrCreate(['name' => 'Pawan Kumar'], [
            'designation' => 'Director', 'group' => 'board', 'sort_order' => 1, 'is_active' => true,
            'bio' => 'Leads the foundation’s work across Gonda and is the first point of contact for partners and supporters.',
        ]);

        // ---------------------------------------------------------- testimonials (sample — replace with real quotes)
        if (Testimonial::count() === 0) {
            foreach ([
                ['Parent of a student', 'Education programme, Gonda', 'My son was about to leave school because we could not buy his books. The foundation not only gave him a bag and books but also helps him with studies in the evening. Today he wants to become a teacher.'],
                ['Self-help group member', 'Women empowerment, Gonda', 'I learnt tailoring at the centre and now I stitch clothes from home. For the first time I can contribute to my children’s fees with my own earnings.'],
                ['Volunteer', 'Health camps', 'Volunteering at the health camps showed me how much a few hours can mean to someone. The team is sincere, organised and truly cares for every person who walks in.'],
            ] as $i => [$name, $designation, $message]) {
                Testimonial::create(compact('name', 'designation', 'message') + ['rating' => 5, 'sort_order' => $i + 1, 'is_active' => true]);
            }
        }

        // ---------------------------------------------------------- success stories (sample)
        foreach ([
            ['From the sewing class to her own small business', 'A self-help group member', 'Gonda', 'seed/photos/photo8.webp',
                'After completing our tailoring course, a mother of three began taking stitching orders from her neighbourhood.',
                ['She joined the tailoring course with no experience and a lot of hesitation. Three months later, she was stitching blouses and school uniforms for her neighbours.', 'With a sewing machine from our campaign, she now works from home and earns a steady income — and has started teaching two other women in her lane.']],
            ['Back to school, and top of the class', 'A Class 6 student', 'Gonda', 'seed/photos/photo1.webp',
                'A boy who had nearly dropped out returned to school with our support — and is now among the best in his class.',
                ['When our volunteers met him, he had stopped attending school to help at a roadside stall.', 'With a school kit, evening support classes and regular conversations with his parents, he returned to school. His teachers say his confidence has grown as much as his marks.']],
        ] as [$title, $person, $loc, $img, $excerpt, $paras]) {
            SuccessStory::firstOrCreate(['title' => $title], [
                'person_name' => $person, 'location' => $loc, 'image' => $img, 'excerpt' => $excerpt,
                'body' => '<p>'.implode('</p><p>', $paras).'</p><p><em>Names are withheld to protect privacy.</em></p>', 'is_featured' => true, 'is_active' => true,
            ]);
        }

        // ---------------------------------------------------------- FAQs
        if (Faq::count() === 0) {
            foreach ([
                ['General', 'What is Lucky Udaan Foundation?', 'Lucky Udaan Foundation is a not-for-profit organisation registered as a Section 8 company under the Companies Act, 2013, based in Gonda, Uttar Pradesh. We work in education, healthcare, women empowerment, care homes, animal welfare, food and relief, environment, civic development, road safety, disaster relief, agriculture, sports and culture.'],
                ['General', 'Where do you work?', 'Our office is at Civil Line, Mandenala, Ambedkar Chauraha, Gonda. We work mainly in Gonda district and nearby areas of Uttar Pradesh, and partner with others to reach further.'],
                ['Donations', 'How can I donate?', 'You can donate by UPI or bank transfer using the details on our Donate page. After paying, please fill in the donation form so we can match your payment and send you a receipt.'],
                ['Donations', 'Will I get a receipt?', 'Yes. Every donation is recorded and a receipt is issued once the payment is confirmed in our bank account.'],
                ['Donations', 'Are donations eligible for tax exemption under 80G?', 'Tax-exemption details depend on our 80G registration status, which is shown on our Transparency and Certifications pages. If you need an 80G receipt, please provide your PAN and address in the donation form.'],
                ['Donations', 'Can I donate for a specific cause?', 'Yes. Choose a campaign on the Donate page and your contribution will be used for that purpose.'],
                ['Volunteering', 'Who can volunteer?', 'Anyone above 16 years of age who wants to contribute their time and skills. Students, professionals, homemakers and retired persons are all welcome.'],
                ['Volunteering', 'Do I need to commit a fixed number of hours?', 'No. You can choose weekends, weekdays, evenings, specific events or remote work, depending on your availability.'],
                ['Volunteering', 'Will I receive a certificate?', 'Yes, volunteers and interns receive a certificate recognising their contribution.'],
                ['Membership', 'How do I become a member?', 'Fill in the application on our Membership page. Our team reviews every application and confirms membership by email along with your membership number.'],
            ] as $i => [$category, $question, $answer]) {
                Faq::create(compact('category', 'question', 'answer') + ['sort_order' => $i + 1, 'is_active' => true]);
            }
        }

        // ---------------------------------------------------------- certifications (numbers to be added by the client)
        foreach ([
            ['Certificate of Incorporation (Section 8 Company)', 'Ministry of Corporate Affairs, Government of India', 'Registered as a not-for-profit company under Section 8 of the Companies Act, 2013.'],
            ['12A Registration', 'Income Tax Department', 'Registration under Section 12A of the Income Tax Act, which exempts the organisation’s income used for charitable purposes.'],
            ['80G Registration', 'Income Tax Department', 'Allows eligible donors to claim a tax deduction on their donations under Section 80G of the Income Tax Act.'],
        ] as $i => [$title, $issuer, $desc]) {
            Certification::firstOrCreate(['title' => $title], ['issued_by' => $issuer, 'description' => $desc, 'sort_order' => $i + 1, 'is_active' => true]);
        }

        // ---------------------------------------------------------- openings (sample)
        Opening::firstOrCreate(['title' => 'Social Media & Content Intern'], [
            'type' => 'internship', 'location' => 'Gonda / Remote', 'employment_type' => 'Unpaid, certificate provided', 'duration' => '8 weeks',
            'summary' => 'Help tell our story — write posts, design creatives and document field activities for our website and social media.',
            'description' => '<ul><li>Create posts and short videos for social media</li><li>Write blog posts and event updates</li><li>Photograph and document field activities</li></ul><p><strong>Who can apply:</strong> students of mass communication, journalism, design or any discipline with a flair for writing and design.</p>',
            'deadline' => now()->addDays(45), 'is_active' => true,
        ]);
        Opening::firstOrCreate(['title' => 'Field Research Intern'], [
            'type' => 'internship', 'location' => 'Gonda', 'employment_type' => 'Stipend-based', 'duration' => '6–8 weeks',
            'summary' => 'Support community surveys and impact assessment for our education and health programmes.',
            'description' => '<ul><li>Conduct household surveys</li><li>Compile and analyse data</li><li>Prepare short reports and case studies</li></ul><p><strong>Who can apply:</strong> students of social work, sociology, economics or public health.</p>',
            'deadline' => now()->addDays(60), 'is_active' => true,
        ]);
        Opening::firstOrCreate(['title' => 'Programme Coordinator – Education'], [
            'type' => 'job', 'location' => 'Gonda', 'employment_type' => 'Full-time',
            'summary' => 'Plan and run our learning-support centres, coordinate volunteers and track student progress.',
            'description' => '<ul><li>Manage day-to-day operations of learning centres</li><li>Recruit, train and coordinate volunteer teachers</li><li>Maintain records and prepare monthly reports</li></ul><p><strong>Requirements:</strong> graduate (B.Ed./MSW preferred), 2+ years in education or community work, fluency in Hindi.</p>',
            'deadline' => now()->addDays(30), 'is_active' => true,
        ]);
    }
}
