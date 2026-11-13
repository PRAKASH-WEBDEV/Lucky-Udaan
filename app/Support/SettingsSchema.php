<?php

namespace App\Support;

/**
 * Every editable site setting, grouped into tabs for the Website Settings screen.
 * The seeder fills defaults from here; templates read values with setting('key').
 */
class SettingsSchema
{
    public static function groups(): array
    {
        return [
            'general' => ['General', 'info-circle', [
                ['site_name', 'Organisation name', 'text', 'required|string|max:120', 'Lucky Udaan Foundation'],
                ['tagline', 'Tagline', 'text', 'nullable|string|max:150', 'Together for a Better Tomorrow'],
                ['legal_name', 'Legal entity line', 'text', 'nullable|string|max:255', 'A Section 8 company registered under the Companies Act, 2013'],
                ['contact_person', 'Contact person', 'text', 'nullable|string|max:120', 'Pawan Kumar'],
                ['founded_year', 'Year founded', 'text', 'nullable|string|max:10', ''],
                ['logo', 'Logo', 'image', 'nullable', 'seed/brand/logo.webp'],
                ['logo_light', 'Logo for dark backgrounds (optional)', 'image', 'nullable', ''],
                ['favicon', 'Favicon (square, 512×512)', 'image', 'nullable', 'seed/brand/favicon.png'],
                ['footer_about', 'Footer “about” text', 'textarea', 'nullable|string|max:600', 'Lucky Udaan Foundation is a not-for-profit organisation in Gonda, Uttar Pradesh, working for education, healthcare, women empowerment and the dignity of every life — together for a better tomorrow.'],
                ['footer_note', 'Footer bottom note', 'text', 'nullable|string|max:255', 'Donations are used only for charitable purposes. The organisation works without any profit motive.'],
            ]],
            'contact' => ['Contact', 'telephone', [
                ['email', 'Email', 'email', 'required|email|max:150', 'luckyudaanfoundation@gmail.com'],
                ['phone', 'Phone', 'text', 'required|string|max:20', '9214748702'],
                ['whatsapp', 'WhatsApp number', 'text', 'nullable|string|max:20', '9214748702'],
                ['address', 'Street address', 'text', 'required|string|max:255', 'Civil Line, Mandenala, Ambedkar Chauraha'],
                ['city', 'City', 'text', 'required|string|max:80', 'Gonda'],
                ['state', 'State', 'text', 'required|string|max:80', 'Uttar Pradesh'],
                ['pincode', 'PIN code', 'text', 'required|string|max:10', '271001'],
                ['office_hours', 'Office hours', 'text', 'nullable|string|max:120', 'Mon – Sat, 10:00 AM – 6:00 PM'],
                ['map_embed', 'Google Map embed URL', 'url', 'nullable|url|max:1000', 'https://maps.google.com/maps?q=Ambedkar%20Chauraha%2C%20Civil%20Lines%2C%20Gonda%2C%20Uttar%20Pradesh%20271001&z=15&output=embed'],
                ['latitude', 'Latitude', 'text', 'nullable|numeric', '27.1339'],
                ['longitude', 'Longitude', 'text', 'nullable|numeric', '81.9619'],
            ]],
            'social' => ['Social links', 'share', [
                ['facebook', 'Facebook', 'url', 'nullable|url|max:255', ''],
                ['instagram', 'Instagram', 'url', 'nullable|url|max:255', ''],
                ['youtube', 'YouTube', 'url', 'nullable|url|max:255', ''],
                ['twitter', 'X (Twitter)', 'url', 'nullable|url|max:255', ''],
                ['linkedin', 'LinkedIn', 'url', 'nullable|url|max:255', ''],
            ]],
            'donation' => ['Donation & legal', 'bank', [
                ['bank_name', 'Bank name', 'text', 'nullable|string|max:120', ''],
                ['bank_account_name', 'Account name', 'text', 'nullable|string|max:150', 'Lucky Udaan Foundation'],
                ['bank_account_number', 'Account number', 'text', 'nullable|string|max:40', ''],
                ['bank_ifsc', 'IFSC code', 'text', 'nullable|string|max:20', ''],
                ['bank_branch', 'Branch', 'text', 'nullable|string|max:150', ''],
                ['upi_id', 'UPI ID', 'text', 'nullable|string|max:100', ''],
                ['upi_qr', 'UPI QR code image', 'image', 'nullable', ''],
                ['cin', 'CIN (Company Identification Number)', 'text', 'nullable|string|max:40', ''],
                ['pan', 'Organisation PAN', 'text', 'nullable|string|max:20', ''],
                ['reg_12a', '12A registration number', 'text', 'nullable|string|max:80', ''],
                ['reg_80g', '80G registration number', 'text', 'nullable|string|max:80', ''],
                ['csr1', 'CSR-1 registration number', 'text', 'nullable|string|max:80', ''],
                ['darpan_id', 'NGO Darpan ID', 'text', 'nullable|string|max:80', ''],
                ['donation_min', 'Minimum online pledge (₹)', 'number', 'nullable|integer|min:1', '100'],
                ['donation_amounts', 'Suggested amounts (comma separated)', 'text', 'nullable|string|max:120', '500,1000,2500,5000,10000'],
                ['signatory_name', 'Receipt signatory name', 'text', 'nullable|string|max:120', 'Pawan Kumar'],
                ['signatory_title', 'Receipt signatory title', 'text', 'nullable|string|max:120', 'Authorised Signatory'],
            ]],
            'home' => ['Homepage', 'house', [
                ['stat1_value', 'Impact counter 1 – number', 'number', 'nullable|integer|min:0', '5000'],
                ['stat1_suffix', 'Counter 1 – suffix', 'text', 'nullable|string|max:5', '+'],
                ['stat1_label', 'Counter 1 – label', 'text', 'nullable|string|max:60', 'Lives touched'],
                ['stat2_value', 'Impact counter 2 – number', 'number', 'nullable|integer|min:0', '120'],
                ['stat2_suffix', 'Counter 2 – suffix', 'text', 'nullable|string|max:5', '+'],
                ['stat2_label', 'Counter 2 – label', 'text', 'nullable|string|max:60', 'Camps & drives held'],
                ['stat3_value', 'Impact counter 3 – number', 'number', 'nullable|integer|min:0', '300'],
                ['stat3_suffix', 'Counter 3 – suffix', 'text', 'nullable|string|max:5', '+'],
                ['stat3_label', 'Counter 3 – label', 'text', 'nullable|string|max:60', 'Volunteers'],
                ['stat4_value', 'Impact counter 4 – number', 'number', 'nullable|integer|min:0', '14'],
                ['stat4_suffix', 'Counter 4 – suffix', 'text', 'nullable|string|max:5', ''],
                ['stat4_label', 'Counter 4 – label', 'text', 'nullable|string|max:60', 'Focus areas'],
                ['about_heading', 'About section heading', 'text', 'nullable|string|max:150', 'Small acts of kindness, lifting lives across Gonda'],
                ['about_text', 'About section text', 'textarea', 'nullable|string|max:1500', 'Lucky Udaan Foundation was born from a simple belief: every child deserves to learn, every family deserves good health, and every woman deserves the chance to stand on her own feet. From classrooms and health camps to tailoring centres and tree plantation drives, we work side by side with communities in Gonda and across Uttar Pradesh.'],
                ['about_image', 'About section image', 'image', 'nullable', 'seed/photos/photo8.webp'],
                ['cta_heading', 'Donate banner heading', 'text', 'nullable|string|max:150', 'Your small contribution can create a big smile'],
                ['cta_text', 'Donate banner text', 'textarea', 'nullable|string|max:400', 'Every rupee you give reaches a child’s classroom, a patient’s medicine or a woman’s first sewing machine.'],
            ]],
            'seo' => ['SEO & analytics', 'search', [
                ['seo_title', 'Default page title', 'text', 'nullable|string|max:70', 'Lucky Udaan Foundation | NGO in Gonda, Uttar Pradesh'],
                ['seo_description', 'Default meta description', 'textarea', 'nullable|string|max:300', 'Lucky Udaan Foundation is a Section 8 non-profit in Gonda, Uttar Pradesh, working for education, healthcare, women empowerment, environment and community welfare. Donate, volunteer or partner with us.'],
                ['seo_keywords', 'Keywords (optional)', 'text', 'nullable|string|max:255', 'NGO in Gonda, charity Gonda Uttar Pradesh, Lucky Udaan Foundation, donate NGO UP, volunteer Gonda'],
                ['og_image', 'Default social share image (1200×630)', 'image', 'nullable', 'seed/brand/og-image.webp'],
                ['google_verification', 'Google Search Console verification code', 'text', 'nullable|string|max:120', ''],
                ['ga_id', 'Google Analytics 4 ID (G-XXXX)', 'text', ['nullable', 'regex:/^G-[A-Z0-9]+$/'], ''],
            ]],
            'theme' => ['Brand colours', 'palette', [
                ['color_primary', 'Primary colour', 'color', ['nullable', 'regex:/^#[0-9a-fA-F]{6}$/'], '#1f7a4d'],
                ['color_primary_dark', 'Dark colour', 'color', ['nullable', 'regex:/^#[0-9a-fA-F]{6}$/'], '#0f3d2e'],
                ['color_accent', 'Accent colour', 'color', ['nullable', 'regex:/^#[0-9a-fA-F]{6}$/'], '#e2a93b'],
            ]],
        ];
    }

    /** Flat list: key => [label, type, rules, default]. */
    public static function fields(): array
    {
        $out = [];
        foreach (self::groups() as $group) {
            foreach ($group[2] as [$key, $label, $type, $rules, $default]) {
                $out[$key] = compact('label', 'type', 'rules', 'default');
            }
        }

        return $out;
    }

    public static function defaults(): array
    {
        return array_map(fn ($f) => $f['default'], self::fields());
    }
}
