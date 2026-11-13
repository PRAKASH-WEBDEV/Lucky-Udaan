<?php

namespace App\Support;

/** Shared option lists used by public forms and the dashboard. */
class Options
{
    public static function interests(): array
    {
        $list = ['Education & Skills', 'Healthcare', 'Women Empowerment', 'Care Homes', 'Animal Welfare', 'Food & Relief',
            'Environment', 'Community Development', 'Road Safety', 'Disaster Relief', 'Agriculture', 'Sports', 'Arts & Culture',
            'Fundraising', 'Social Media & Design', 'Photography & Video'];

        return array_combine($list, $list);
    }

    public static function availability(): array
    {
        $list = ['Weekends', 'Weekdays', 'Evenings', 'Flexible', 'Events only', 'Remote / online'];

        return array_combine($list, $list);
    }

    public static function partnershipTypes(): array
    {
        $list = ['CSR Partnership', 'Government Collaboration', 'Educational Institution', 'NGO Collaboration', 'Corporate Volunteering', 'In-kind Support', 'Other'];

        return array_combine($list, $list);
    }
}
