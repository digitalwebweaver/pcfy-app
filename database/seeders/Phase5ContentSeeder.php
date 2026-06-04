<?php

namespace Database\Seeders;

use App\Models\Menu;
use App\Models\MenuItem;
use App\Models\Section;
use App\Models\TrustItem;
use App\Models\BlogCategory;
use Illuminate\Database\Seeder;

class Phase5ContentSeeder extends Seeder
{
    public function run(): void
    {
        $this->seedMenus();
        $this->seedSections();
        $this->seedTrustItems();
        $this->seedBlogCategories();

        $this->command->info('✓ Phase 5 default content seeded.');
    }

    private function seedMenus(): void
    {
        // Primary navigation
        $primary = Menu::firstOrCreate(['location' => 'primary'], ['name' => 'Primary Navigation']);

        $primaryItems = [
            ['label' => 'Home',       'type' => 'url', 'url' => '/',       'sort_order' => 1],
            ['label' => 'Shop',       'type' => 'url', 'url' => '/shop',   'sort_order' => 2],
            ['label' => 'Blog',       'type' => 'url', 'url' => '/blog',   'sort_order' => 3],
            ['label' => 'Contact',    'type' => 'url', 'url' => '/contact','sort_order' => 4],
        ];
        foreach ($primaryItems as $item) {
            MenuItem::firstOrCreate(
                ['menu_id' => $primary->id, 'label' => $item['label']],
                array_merge($item, ['menu_id' => $primary->id, 'is_visible' => true])
            );
        }

        // Footer navigation
        $footer = Menu::firstOrCreate(['location' => 'footer'], ['name' => 'Footer Links']);

        $footerItems = [
            ['label' => 'About Us',      'type' => 'url', 'url' => '/page/about',       'sort_order' => 1],
            ['label' => 'Privacy Policy','type' => 'url', 'url' => '/page/privacy',     'sort_order' => 2],
            ['label' => 'Terms of Use',  'type' => 'url', 'url' => '/page/terms',       'sort_order' => 3],
            ['label' => 'Warranty',      'type' => 'url', 'url' => '/page/warranty',    'sort_order' => 4],
            ['label' => 'Blog',          'type' => 'url', 'url' => '/blog',             'sort_order' => 5],
        ];
        foreach ($footerItems as $item) {
            MenuItem::firstOrCreate(
                ['menu_id' => $footer->id, 'label' => $item['label']],
                array_merge($item, ['menu_id' => $footer->id, 'is_visible' => true])
            );
        }
    }

    private function seedSections(): void
    {
        $sections = [
            [
                'page' => 'home', 'section_key' => 'hero', 'label' => 'Hero Banner',
                'sort_order' => 1, 'is_visible' => true,
                'content' => [
                    'heading'    => 'Better computers. Half the price. Zero compromise.',
                    'subheading' => 'Certified refurbished · 30-point tested',
                    'body'       => 'Laptops, desktops, servers & spare parts — professionally restored and backed by a 6-month warranty.',
                    'btn_text'   => 'Shop all refurbished',
                    'btn_url'    => '/shop',
                ],
            ],
            [
                'page' => 'home', 'section_key' => 'features', 'label' => 'Why Choose Us',
                'sort_order' => 2, 'is_visible' => true,
                'content' => [
                    'heading'    => 'Why thousands of businesses choose pcfy.in',
                    'subheading' => 'Quality you can trust',
                ],
            ],
            [
                'page' => 'home', 'section_key' => 'testimonials', 'label' => 'Testimonials',
                'sort_order' => 3, 'is_visible' => true,
                'content' => ['heading' => 'What our customers say'],
            ],
            [
                'page' => 'home', 'section_key' => 'cta', 'label' => 'Bottom CTA',
                'sort_order' => 4, 'is_visible' => true,
                'content' => [
                    'heading'  => "Ready to upgrade your team's hardware?",
                    'body'     => 'Get a quote in minutes — no commitment required.',
                    'btn_text' => 'Browse products',
                    'btn_url'  => '/shop',
                ],
            ],
        ];

        foreach ($sections as $s) {
            Section::firstOrCreate(
                ['page' => $s['page'], 'section_key' => $s['section_key']],
                $s
            );
        }
    }

    private function seedTrustItems(): void
    {
        $items = [
            ['title' => '30-Point Tested',   'subtitle' => 'Every device fully inspected', 'sort_order' => 1,
             'icon_svg' => '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M22 11.08V12a10 10 0 11-5.93-9.14"/><polyline points="22 4 12 14.01 9 11.01"/></svg>'],
            ['title' => '6-Month Warranty',  'subtitle' => 'On every product',             'sort_order' => 2,
             'icon_svg' => '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/></svg>'],
            ['title' => 'Free Delivery',     'subtitle' => 'On orders above ₹999',         'sort_order' => 3,
             'icon_svg' => '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="1" y="3" width="15" height="13"/><polygon points="16 8 20 8 23 11 23 16 16 16 16 8"/><circle cx="5.5" cy="18.5" r="2.5"/><circle cx="18.5" cy="18.5" r="2.5"/></svg>'],
            ['title' => 'Honest Grading',    'subtitle' => 'A, B, C grades explained',     'sort_order' => 4,
             'icon_svg' => '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/></svg>'],
        ];

        foreach ($items as $item) {
            TrustItem::firstOrCreate(['title' => $item['title']], array_merge($item, ['is_visible' => true]));
        }
    }

    private function seedBlogCategories(): void
    {
        $cats = [
            ['name' => 'Buying Guides',      'description' => 'Tips for choosing the right refurbished hardware'],
            ['name' => 'Tech News',           'description' => 'Latest in refurbished and enterprise tech'],
            ['name' => 'How-To',             'description' => 'Setup guides and tutorials'],
            ['name' => 'Company Updates',    'description' => 'News from the pcfy.in team'],
        ];

        foreach ($cats as $cat) {
            BlogCategory::firstOrCreate(['name' => $cat['name']], array_merge($cat, ['is_active' => true]));
        }
    }
}
