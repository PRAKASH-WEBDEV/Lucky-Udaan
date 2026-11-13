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
        'settings' => 'Website Settings',
        'admins' => 'Admin Users & Roles',
    ],

    // Sidebar: [label, icon, route, module] or a section heading string.
    'menu' => [
        ['Dashboard', 'grid-1x2', 'admin.dashboard', null],
        'Fundraising',
        ['Donations', 'cash-coin', 'admin.donations.index', 'donations'],
        ['Donors', 'person-hearts', 'admin.donors.index', 'donors'],
        ['Campaigns', 'megaphone', 'admin.campaigns.index', 'campaigns'],
        'Website',
        ['Homepage Banners', 'images', 'admin.banners.index', 'banners'],
        ['Programs', 'diagram-3', 'admin.programs.index', 'programs'],
        ['Blog & News', 'newspaper', 'admin.posts.index', 'posts'],
        ['Events', 'calendar-event', 'admin.events.index', 'events'],
        ['Gallery', 'camera', 'admin.gallery.index', 'gallery'],
        ['Team', 'people', 'admin.team.index', 'team'],
        ['Content', 'file-earmark-richtext', null, 'content', [
            ['Pages', 'admin.pages.index'],
            ['Testimonials', 'admin.testimonials.index'],
            ['Success Stories', 'admin.stories.index'],
            ['FAQs', 'admin.faqs.index'],
            ['Certifications', 'admin.certifications.index'],
            ['Annual Reports', 'admin.reports.index'],
            ['Partners', 'admin.partners.index'],
            ['Jobs & Internships', 'admin.openings.index'],
        ]],
        'People',
        ['Volunteers', 'hand-thumbs-up', 'admin.volunteers.index', 'volunteers'],
        ['Members', 'person-badge', 'admin.members.index', 'members'],
        ['Enquiries', 'envelope-paper', 'admin.enquiries.index', 'enquiries'],
        'Administration',
        ['Reports & Analytics', 'bar-chart-line', 'admin.analytics.index', 'analytics'],
        ['Website Settings', 'gear', 'admin.settings.edit', 'settings'],
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
