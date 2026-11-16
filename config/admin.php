<?php

/*
| Dashboard navigation and permission keys.
| Each sidebar item belongs to a permission "module"; roles store the list of modules they may access.
*/

return [
    // Permission modules shown on the Roles screen.
    'modules' => [
        'content' => 'Website Content (pages, FAQs, testimonials, stories, reports, certifications, partners, openings)',
        'banners' => 'Homepage Banners',
        'programs' => 'Programs',
        'campaigns' => 'Campaigns',
        'posts' => 'Blog, News & Press',
        'events' => 'Events',
        'gallery' => 'Gallery',
        'team' => 'Team',
        'donations' => 'Donations',
        'donors' => 'Donors',
        'volunteers' => 'Volunteers',
        'members' => 'Members',
        'enquiries' => 'Enquiries & Applications',
        'analytics' => 'Reports & Analytics',
        'settings' => 'Website Settings, Subscription & Billing',
        'admins' => 'Admin Users & Roles',
        'hr' => 'Staff: employees, attendance, leave, letters, claims, violations, holidays',
        'payroll' => 'Payroll, salary slips & salary structures',
        'beneficiaries' => 'Beneficiary details',
        'finance' => 'Expenses & membership plans',
        'surveys' => 'Surveys',
        'newsletter' => 'Newsletter subscribers',
        'audit' => 'Audit logs',
    ],

    /*
    | Sidebar: a section heading string, or [label, icon, route, module, children?].
    | Children: [label, route, params = [], module = parent's module].
    | A group with a null module shows if any of its children is allowed.
    */
    'menu' => [
        ['Dashboard', 'grid-1x2', 'admin.dashboard', null],

        'Staff Management',
        ['HR Overview', 'speedometer2', 'admin.hr.overview', 'hr'],
        ['Employees', 'person-vcard', 'admin.employees.index', 'hr'],
        ['Attendance', 'calendar-check', null, null, [
            ['Attendance Register', 'admin.attendance.index', [], 'hr'],
            ['Manual Punch', 'admin.attendance.create', [], 'hr'],
            ['Attendance Settings', 'admin.settings.edit', ['tab' => 'hr'], 'settings'],
        ]],
        ['Leave Management', 'calendar-x', null, 'hr', [
            ['Leave Requests', 'admin.leaves.index'],
            ['Leave Quotas', 'admin.leave-types.index'],
        ]],
        ['Payroll', 'wallet2', null, 'payroll', [
            ['Payroll', 'admin.payroll.index'],
            ['Salary Slips', 'admin.salary-slips.index'],
            ['Salary Structures', 'admin.salary-structures.index'],
        ]],
        ['Offer Letters', 'envelope-paper-heart', 'admin.offer-letters.index', 'hr'],
        ['Expense Claims', 'receipt', 'admin.expense-claims.index', 'hr'],
        ['Violations & Devices', 'fingerprint', null, 'hr', [
            ['Violations', 'admin.violations.index'],
            ['Attendance Devices', 'admin.devices.index'],
        ]],
        ['Holiday Calendar', 'calendar-heart', 'admin.holidays.index', 'hr'],
        ['Letterhead', 'file-earmark-richtext', null, null, [
            ['View / print letterhead', 'admin.hr.letterhead', [], 'hr'],
            ['Letterhead settings', 'admin.settings.edit', ['tab' => 'letterhead'], 'settings'],
        ]],

        'Beneficiaries',
        ['Beneficiaries Details', 'person-heart', 'admin.beneficiaries.index', 'beneficiaries'],

        'Content',
        ['Blog & News', 'newspaper', 'admin.posts.index', 'posts'],
        ['Press Releases', 'broadcast', 'admin.press.index', 'posts'],
        ['Announcements', 'megaphone', 'admin.announcements.index', 'posts'],
        ['Trust Wall', 'patch-check', 'admin.trust-wall.index', 'content'],
        ['Image Tools', 'magic', 'admin.tools.images', 'content'],
        ['Categories', 'folder2', 'admin.categories.index', 'posts'],
        ['Tags', 'tags', 'admin.tags.index', 'posts'],
        ['Authors', 'pen', 'admin.authors.index', 'posts'],
        ['Newsletter Subscribers', 'envelope-at', 'admin.newsletter.index', 'newsletter'],

        'Website',
        ['All Website Links', 'link-45deg', 'admin.tools.links', 'content'],
        ['Custom Pages', 'file-earmark-text', 'admin.pages.index', 'content'],
        ['Hero Slider', 'images', 'admin.banners.index', 'banners'],
        ['About Page', 'info-square', 'admin.about-page', 'content'],
        ['Testimonials', 'chat-quote', 'admin.testimonials.index', 'content'],
        ['Impact Stats', 'graph-up-arrow', 'admin.settings.edit', 'settings', [], ['tab' => 'home']],
        ['Our Values', 'gem', 'admin.values.index', 'content'],
        ['Our Journey', 'signpost-split', 'admin.journey.index', 'content'],
        ['Awards', 'trophy', 'admin.awards.index', 'content'],
        ['Support Leaders', 'person-raised-hand', 'admin.leaders.index', 'content'],
        ['Media Library', 'collection', 'admin.tools.media', 'content'],
        ['Transparency', 'shield-check', null, null, [
            ['Annual Reports', 'admin.reports.index', [], 'content'],
            ['Certifications', 'admin.certifications.index', [], 'content'],
            ['Registration details', 'admin.settings.edit', ['tab' => 'donation'], 'settings'],
        ]],
        ['More Content', 'grid-3x3-gap', null, null, [
            ['Gallery', 'admin.gallery.index', [], 'gallery'],
            ['Team', 'admin.team.index', [], 'team'],
            ['Success Stories', 'admin.stories.index', [], 'content'],
            ['FAQs', 'admin.faqs.index', [], 'content'],
            ['Partners', 'admin.partners.index', [], 'content'],
        ]],
        ['Website Settings', 'gear', 'admin.settings.edit', 'settings', [], ['tab' => 'general']],
        ['Social Embeds', 'share', 'admin.social-embeds.index', 'content'],
        ['Translations', 'translate', 'admin.translations.index', 'content'],
        ['SEO & Analytics', 'search', 'admin.settings.edit', 'settings', [], ['tab' => 'seo']],

        'Finance',
        ['Donations & Receipts', 'cash-coin', 'admin.donations.index', 'donations'],
        ['Donors', 'person-hearts', 'admin.donors.index', 'donors'],
        ['Expenses', 'cash-stack', 'admin.expenses.index', 'finance'],
        ['Membership & Plans', 'card-checklist', null, null, [
            ['Members', 'admin.members.index', [], 'members'],
            ['Membership Plans', 'admin.membership-plans.index', [], 'finance'],
        ]],
        ['Reports', 'bar-chart-line', 'admin.analytics.index', 'analytics'],

        'Programs & Outreach',
        ['Campaigns', 'megaphone', 'admin.campaigns.index', 'campaigns'],
        ['Events', 'calendar-event', 'admin.events.index', 'events'],
        ['Surveys', 'ui-checks', 'admin.surveys.index', 'surveys'],
        ['Programs', 'diagram-3', 'admin.programs.index', 'programs'],
        ['Volunteers', 'hand-thumbs-up', 'admin.volunteers.index', 'volunteers'],
        ['Careers', 'briefcase', 'admin.careers.index', 'content'],
        ['Internships', 'mortarboard', 'admin.internships.index', 'content'],
        ['Enquiries & Applications', 'envelope-paper', 'admin.enquiries.index', 'enquiries'],

        'Settings',
        ['NGO Settings', 'building-gear', 'admin.settings.edit', 'settings', [], ['tab' => 'donation']],
        ['Subscription & Billing', 'credit-card-2-front', 'admin.subscriptions.index', 'settings'],
        ['Notifications', 'bell', 'admin.notifications.index', null],
        ['Audit Logs', 'clock-history', 'admin.audit-logs.index', 'audit'],
        ['Admin Users', 'shield-lock', 'admin.admins.index', 'admins'],
        ['Roles & Permissions', 'key', 'admin.roles.index', 'admins'],
    ],

    // Icons offered when choosing a Program icon (Bootstrap Icons names).
    'icons' => [
        'book', 'mortarboard', 'laptop', 'heart-pulse', 'hospital', 'droplet', 'gender-female', 'people', 'house-heart',
        'house', 'person-wheelchair', 'emoji-smile', 'egg-fried', 'basket', 'cup-hot', 'tree', 'recycle', 'water',
        'sun', 'globe2', 'buildings', 'sign-stop', 'cone-striped', 'bandaid', 'life-preserver', 'shield-check',
        'flower1', 'trophy', 'dribbble', 'bicycle', 'palette', 'music-note-beamed', 'brush', 'handshake',
        'briefcase', 'lightbulb', 'megaphone', 'heart', 'star', 'award', 'bug', 'feather',
    ],
];
