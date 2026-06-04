<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Setting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Artisan;

class SettingsController extends Controller
{
    public function index()
    {
        $settings = Setting::all()->keyBy('key');
        return view('admin.settings.index', compact('settings'));
    }

    public function update(Request $request)
    {
        $tab = $request->input('tab', 'brand');

        $rules = match($tab) {
            'brand'        => ['site_name' => 'required|string|max:100', 'site_tagline' => 'nullable|string|max:200', 'accent_color' => 'nullable|string|max:20'],
            'contact'      => ['contact_phone' => 'nullable|string|max:30', 'contact_email' => 'nullable|email|max:100', 'contact_address' => 'nullable|string|max:300', 'whatsapp_number' => 'nullable|string|max:20', 'whatsapp_message' => 'nullable|string|max:300'],
            'social'       => ['social_instagram' => 'nullable|max:300', 'social_linkedin' => 'nullable|max:300', 'social_facebook' => 'nullable|max:300', 'social_twitter' => 'nullable|max:300', 'social_youtube' => 'nullable|max:300'],
            'seo'          => ['seo_title' => 'nullable|string|max:160', 'seo_description' => 'nullable|string|max:320', 'robots_txt' => 'nullable|string'],
            'smtp'         => ['mail_driver' => 'required|in:log,smtp,sendmail', 'mail_host' => 'nullable|string|max:200', 'mail_port' => 'nullable|integer', 'mail_username' => 'nullable|string|max:200', 'mail_password' => 'nullable|string|max:200', 'mail_from_name' => 'nullable|string|max:100', 'mail_from_email' => 'nullable|email|max:100'],
            'analytics'    => ['ga_id' => 'nullable|string|max:50', 'gtm_id' => 'nullable|string|max:50'],
            'footer'       => ['footer_copyright' => 'nullable|string|max:300', 'footer_col1_title' => 'nullable|string|max:60', 'footer_col2_title' => 'nullable|string|max:60', 'footer_col3_title' => 'nullable|string|max:60'],
            'announcement' => [],
            default        => [],
        };

        $validated = $request->validate($rules);

        if ($tab === 'announcement') {
            $items = array_filter(array_map('trim', explode("\n", $request->input('announcement_items_text', ''))));
            Setting::set('announcement_items', json_encode(array_values($items)), 'announcement');
            Setting::set('announcement_enabled', $request->boolean('announcement_enabled') ? '1' : '0', 'announcement');
            Artisan::call('cache:clear');
            return back()->with('success', 'Announcement settings saved.')->with('tab', $tab);
        }

        $group = in_array($tab, ['brand','contact','social','seo','smtp','analytics','footer','announcement']) ? $tab : 'general';

        foreach ($validated as $key => $value) {
            Setting::set($key, $value ?? '', $group);
        }

        Artisan::call('cache:clear');
        return back()->with('success', 'Settings saved.')->with('tab', $tab);
    }
}
