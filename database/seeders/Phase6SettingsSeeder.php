<?php

namespace Database\Seeders;

use App\Models\Setting;
use Illuminate\Database\Seeder;

class Phase6SettingsSeeder extends Seeder
{
    public function run(): void
    {
        $defaults = [
            // Brand
            ['key' => 'site_name',      'group' => 'brand',   'value' => 'pcfy.in',                          'label' => 'Site name'],
            ['key' => 'site_tagline',   'group' => 'brand',   'value' => 'Better computers. Half the price.','label' => 'Tagline'],
            ['key' => 'site_logo',      'group' => 'brand',   'value' => '',                                 'label' => 'Logo URL'],
            ['key' => 'site_favicon',   'group' => 'brand',   'value' => '',                                 'label' => 'Favicon URL'],
            ['key' => 'accent_color',   'group' => 'brand',   'value' => '#f59e0b',                          'label' => 'Accent colour'],

            // Contact
            ['key' => 'contact_phone',     'group' => 'contact', 'value' => '+91 98765 43210',               'label' => 'Phone'],
            ['key' => 'contact_email',     'group' => 'contact', 'value' => 'hello@pcfy.in',                 'label' => 'Email'],
            ['key' => 'contact_address',   'group' => 'contact', 'value' => 'Vadodara, Gujarat, India',      'label' => 'Address'],
            ['key' => 'whatsapp_number',   'group' => 'contact', 'value' => '919876543210',                  'label' => 'WhatsApp (digits only)'],
            ['key' => 'whatsapp_message',  'group' => 'contact', 'value' => 'Hi, I have a query about pcfy.in', 'label' => 'WhatsApp default message'],

            // Social
            ['key' => 'social_instagram', 'group' => 'social', 'value' => '', 'label' => 'Instagram URL'],
            ['key' => 'social_linkedin',  'group' => 'social', 'value' => '', 'label' => 'LinkedIn URL'],
            ['key' => 'social_facebook',  'group' => 'social', 'value' => '', 'label' => 'Facebook URL'],
            ['key' => 'social_twitter',   'group' => 'social', 'value' => '', 'label' => 'Twitter/X URL'],
            ['key' => 'social_youtube',   'group' => 'social', 'value' => '', 'label' => 'YouTube URL'],

            // SEO defaults
            ['key' => 'seo_title',       'group' => 'seo', 'value' => 'pcfy.in — Better computers, half the price', 'label' => 'Default meta title'],
            ['key' => 'seo_description', 'group' => 'seo', 'value' => "India's trusted marketplace for certified refurbished laptops, desktops, servers & spare parts.", 'label' => 'Default meta description'],
            ['key' => 'seo_og_image',    'group' => 'seo', 'value' => '', 'label' => 'Default OG image URL'],
            ['key' => 'robots_txt',      'group' => 'seo', 'value' => "User-agent: *\nAllow: /\nDisallow: /adminhu/\nSitemap: /sitemap.xml", 'label' => 'robots.txt content'],

            // SMTP
            ['key' => 'mail_driver',    'group' => 'smtp', 'value' => 'log',            'label' => 'Mail driver'],
            ['key' => 'mail_host',      'group' => 'smtp', 'value' => 'smtp.mailtrap.io','label' => 'SMTP host'],
            ['key' => 'mail_port',      'group' => 'smtp', 'value' => '2525',            'label' => 'SMTP port'],
            ['key' => 'mail_username',  'group' => 'smtp', 'value' => '',               'label' => 'SMTP username'],
            ['key' => 'mail_password',  'group' => 'smtp', 'value' => '',               'label' => 'SMTP password'],
            ['key' => 'mail_from_name', 'group' => 'smtp', 'value' => 'pcfy.in',        'label' => 'From name'],
            ['key' => 'mail_from_email','group' => 'smtp', 'value' => 'noreply@pcfy.in','label' => 'From email'],

            // Analytics
            ['key' => 'ga_id',    'group' => 'analytics', 'value' => '', 'label' => 'Google Analytics ID (G-XXXXXX)'],
            ['key' => 'gtm_id',   'group' => 'analytics', 'value' => '', 'label' => 'Google Tag Manager ID (GTM-XXXXXX)'],

            // Announcement bar
            ['key' => 'announcement_enabled', 'group' => 'announcement', 'value' => '1',                'label' => 'Show announcement bar'],
            ['key' => 'announcement_items',   'group' => 'announcement', 'value' => json_encode([
                '🚚 Free delivery above ₹999',
                '📞 +91 98765 43210',
                '🛡 6-month warranty on every product',
            ]), 'label' => 'Announcement items (JSON)'],

            // Footer
            ['key' => 'footer_copyright', 'group' => 'footer', 'value' => '© 2026 pcfy.in — All rights reserved.', 'label' => 'Copyright text'],
            ['key' => 'footer_col1_title','group' => 'footer', 'value' => 'Quick links',   'label' => 'Footer col 1 title'],
            ['key' => 'footer_col2_title','group' => 'footer', 'value' => 'Company',        'label' => 'Footer col 2 title'],
            ['key' => 'footer_col3_title','group' => 'footer', 'value' => 'Support',        'label' => 'Footer col 3 title'],
        ];

        foreach ($defaults as $row) {
            Setting::firstOrCreate(['key' => $row['key']], $row);
        }

        $this->command->info('✓ Phase 6 default settings seeded.');
    }
}
