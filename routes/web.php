<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Admin;
use App\Http\Controllers\Site;

// ── Frontend ────────────────────────────────────────
Route::get('/',                         [Site\HomeController::class,       'index'])->name('home');
Route::get('/shop',                     [Site\ShopController::class,       'index'])->name('shop');
Route::get('/shop/{slug}',              [Site\ShopController::class,       'category'])->name('shop.category');
Route::get('/products/{slug}',          [Site\ProductPageController::class,'show'])->name('product');
Route::view('/contact', 'pages.contact')->name('contact');
Route::get('/sitemap.xml',              [Site\SitemapController::class,    'index'])->name('sitemap');
Route::get('/robots.txt',               [Site\SitemapController::class,    'robots'])->name('robots');
Route::get('/blog',                     [Site\BlogFrontController::class,  'index'])->name('blog');
Route::get('/blog/category/{slug}',     [Site\BlogFrontController::class,  'category'])->name('blog.category');
Route::get('/blog/{slug}',              [Site\BlogFrontController::class,  'show'])->name('blog.show');
Route::get('/page/{slug}',              [Site\PageFrontController::class,  'show'])->name('page');
Route::get('/search/suggestions',       [Site\SearchController::class,     'suggestions'])->name('search.suggestions');

// Quote cart + submission
Route::prefix('quote')->name('quote.')->group(function () {
    Route::get('cart',                          [Site\CartController::class,       'index'])->name('cart');
    Route::post('cart/add',                     [Site\CartController::class,       'add'])->name('cart.add');
    Route::patch('cart/{key}',                  [Site\CartController::class,       'update'])->name('cart.update');
    Route::delete('cart/{key}',                 [Site\CartController::class,       'remove'])->name('cart.remove');
    Route::delete('cart',                       [Site\CartController::class,       'clear'])->name('cart.clear');
    Route::get('submit',                        [Site\QuoteSubmitController::class,'show'])->name('submit');
    Route::post('submit',                       [Site\QuoteSubmitController::class,'store'])->name('store');
    Route::get('confirmation/{quoteNumber}',    [Site\QuoteSubmitController::class,'confirmation'])->name('confirmation');
});

// Cart shorthand (for form actions in product card)
Route::post('/cart/add',                [Site\CartController::class, 'add'])->name('cart.add');

// ── Customer account auth (public) ─────────────────
Route::prefix('account')->name('account.')->group(function () {
    Route::get('login',    [Site\AccountController::class, 'loginForm'])->name('login');
    Route::post('login',   [Site\AccountController::class, 'login'])->name('login.post');
    Route::get('register', [Site\AccountController::class, 'registerForm'])->name('register');
    Route::post('register',[Site\AccountController::class, 'register'])->name('register.post');
    Route::post('logout',  [Site\AccountController::class, 'logout'])->name('logout');
});

// ── Customer account (protected) ────────────────────
Route::prefix('account')->name('account.')->middleware(['web', 'customer'])->group(function () {
    Route::get('/', [Site\AccountController::class, 'dashboard'])->name('dashboard');
});

// ── Wishlist toggle (AJAX, auth required) ────────────
Route::post('/wishlist/toggle', [Site\WishlistController::class, 'toggle'])->name('wishlist.toggle');

// ── Admin auth (public) ─────────────────────────────
Route::prefix('adminhu')->name('adminhu.')->group(function () {
    Route::get('login', [Admin\AuthController::class, 'loginForm'])->name('login');
    Route::post('login', [Admin\AuthController::class, 'login'])->name('login.post');
    Route::post('logout', [Admin\AuthController::class, 'logout'])->name('logout');
});

// ── Admin panel (protected) ─────────────────────────
Route::prefix('adminhu')->name('adminhu.')->middleware(['web', App\Http\Middleware\Admin\AdminMiddleware::class])->group(function () {

    // Dashboard
    Route::get('/', [Admin\DashboardController::class, 'index'])->name('dashboard');

    // Profile
    Route::get('profile', [Admin\AuthController::class, 'profile'])->name('profile');

    // Products
    Route::get('products/import',           [Admin\ProductController::class, 'importForm'])->name('products.import.form');
    Route::post('products/import',          [Admin\ProductController::class, 'import'])->name('products.import');
    Route::get('products/sample-csv',       [Admin\ProductController::class, 'downloadSample'])->name('products.sample');
    Route::delete('products/{product}/images', [Admin\ProductController::class, 'deleteImage'])->name('products.image.delete');
    Route::post('products/{product}/images/reorder', [Admin\ProductController::class, 'reorderImages'])->name('products.image.reorder');
    Route::resource('products', Admin\ProductController::class);

    // Categories & Brands
    Route::resource('categories', Admin\CategoryController::class);
    Route::resource('brands', Admin\BrandController::class);

    // Quotes
    Route::post('quotes/{quote}/duplicate',     [Admin\QuoteController::class, 'duplicate'])->name('quotes.duplicate');
    Route::post('quotes/{quote}/respond',      [Admin\QuoteController::class, 'respond'])->name('quotes.respond');
    Route::patch('quotes/{quote}/status',      [Admin\QuoteController::class, 'updateStatus'])->name('quotes.status');
    Route::post('quotes/{quote}/items',        [Admin\QuoteController::class, 'updateItems'])->name('quotes.items');
    Route::post('quotes/{quote}/assign',       [Admin\QuoteController::class, 'assign'])->name('quotes.assign');
    Route::get('quotes/{quote}/pdf',           [Admin\QuoteController::class, 'downloadPdf'])->name('quotes.pdf');
    Route::resource('quotes', Admin\QuoteController::class);

    // Orders
    Route::post('quotes/{quote}/convert-to-order',          [Admin\OrderController::class, 'convertFromQuote'])->name('quotes.convert');
    Route::patch('orders/{order}/status',                   [Admin\OrderController::class, 'updateStatus'])->name('orders.status');
    Route::post('orders/{order}/items',                     [Admin\OrderController::class, 'updateItems'])->name('orders.items');
    Route::post('orders/{order}/shipments',                 [Admin\OrderController::class, 'addShipment'])->name('orders.shipments.store');
    Route::patch('orders/{order}/shipments/{shipment}',     [Admin\OrderController::class, 'updateShipment'])->name('orders.shipments.update');
    Route::resource('orders', Admin\OrderController::class)->only(['index', 'create', 'store', 'show', 'update']);

    // Contact
    Route::resource('contacts', Admin\ContactController::class);

    // Blog
    Route::get('blog/categories',                    [Admin\BlogController::class, 'categories'])->name('blog.categories');
    Route::post('blog/categories',                   [Admin\BlogController::class, 'storeCategory'])->name('blog.categories.store');
    Route::delete('blog/categories/{category}',      [Admin\BlogController::class, 'destroyCategory'])->name('blog.categories.destroy');
    Route::resource('blog', Admin\BlogController::class)->parameters(['blog' => 'blog']);

    // Static pages
    Route::resource('pages', Admin\PageController::class);

    // Menus
    Route::get('menus/{menu}/items',                 [Admin\MenuController::class, 'items'])->name('menus.items');
    Route::post('menus/{menu}/items',                [Admin\MenuController::class, 'storeItem'])->name('menus.items.store');
    Route::delete('menus/{menu}/items/{item}',       [Admin\MenuController::class, 'destroyItem'])->name('menus.items.destroy');
    Route::post('menus/{menu}/items/reorder',        [Admin\MenuController::class, 'reorder'])->name('menus.items.reorder');
    Route::resource('menus', Admin\MenuController::class);

    // Sections / Banners / Testimonials / Trust
    Route::resource('sections', Admin\SectionController::class);
    Route::resource('banners', Admin\BannerController::class);
    Route::resource('testimonials', Admin\TestimonialController::class);
    Route::resource('trust', Admin\TrustItemController::class);

    // Media Library
    Route::get('media',                              [Admin\MediaController::class, 'index'])->name('media.index');
    Route::post('media',                             [Admin\MediaController::class, 'store'])->name('media.store');
    Route::delete('media/{media}',                   [Admin\MediaController::class, 'destroy'])->name('media.destroy');

    // Users
    Route::resource('users', Admin\UserController::class);

    // Settings
    Route::get('settings',         [Admin\SettingsController::class, 'index'])->name('settings.index');
    Route::put('settings',         [Admin\SettingsController::class, 'update'])->name('settings.update');
    Route::post('settings',        [Admin\SettingsController::class, 'update']);
});
