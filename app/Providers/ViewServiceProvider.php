<?php

namespace App\Providers;

use App\Models\Category;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;

class ViewServiceProvider extends ServiceProvider
{
    public function register(): void {}

    public function boot(): void
    {
        View::composer(['layouts.site', 'pages.*'], function ($view) {
            $view->with('siteCategories', Category::whereNull('parent_id')
                ->visible()
                ->orderBy('sort_order')
                ->limit(8)
                ->get()
            );
        });
    }
}
