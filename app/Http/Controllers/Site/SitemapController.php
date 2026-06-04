<?php

namespace App\Http\Controllers\Site;

use App\Http\Controllers\Controller;
use App\Models\BlogPost;
use App\Models\Category;
use App\Models\Page;
use App\Models\Product;
use App\Models\Setting;
use Spatie\Sitemap\Sitemap;
use Spatie\Sitemap\Tags\Url;

class SitemapController extends Controller
{
    public function index()
    {
        $sitemap = Sitemap::create();

        // Homepage
        $sitemap->add(Url::create('/')->setPriority(1.0)->setChangeFrequency(Url::CHANGE_FREQUENCY_DAILY));

        // Static pages
        $sitemap->add(Url::create('/shop')->setPriority(0.9)->setChangeFrequency(Url::CHANGE_FREQUENCY_DAILY));
        $sitemap->add(Url::create('/blog')->setPriority(0.8)->setChangeFrequency(Url::CHANGE_FREQUENCY_DAILY));
        $sitemap->add(Url::create('/contact')->setPriority(0.5)->setChangeFrequency(Url::CHANGE_FREQUENCY_MONTHLY));

        // Categories
        Category::where('is_visible', true)->each(function ($cat) use ($sitemap) {
            $sitemap->add(
                Url::create(route('shop.category', $cat->slug))
                   ->setPriority(0.8)
                   ->setChangeFrequency(Url::CHANGE_FREQUENCY_WEEKLY)
                   ->setLastModificationDate($cat->updated_at)
            );
        });

        // Products
        Product::where('is_active', true)->each(function ($product) use ($sitemap) {
            $sitemap->add(
                Url::create(route('product', $product->slug))
                   ->setPriority(0.7)
                   ->setChangeFrequency(Url::CHANGE_FREQUENCY_WEEKLY)
                   ->setLastModificationDate($product->updated_at)
            );
        });

        // Blog posts
        BlogPost::published()->each(function ($post) use ($sitemap) {
            $sitemap->add(
                Url::create(route('blog.show', $post->slug))
                   ->setPriority(0.6)
                   ->setChangeFrequency(Url::CHANGE_FREQUENCY_MONTHLY)
                   ->setLastModificationDate($post->updated_at)
            );
        });

        // Static CMS pages
        Page::published()->each(function ($page) use ($sitemap) {
            $sitemap->add(
                Url::create(route('page', $page->slug))
                   ->setPriority(0.5)
                   ->setChangeFrequency(Url::CHANGE_FREQUENCY_MONTHLY)
                   ->setLastModificationDate($page->updated_at)
            );
        });

        return $sitemap->toResponse(request());
    }

    public function robots()
    {
        $content = Setting::get('robots_txt', "User-agent: *\nAllow: /\nDisallow: /adminhu/\nSitemap: " . url('/sitemap.xml'));
        return response($content, 200)->header('Content-Type', 'text/plain');
    }
}
