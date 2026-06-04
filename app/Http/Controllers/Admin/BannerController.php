<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Banner;
use Illuminate\Http\Request;

class BannerController extends Controller
{
    public function index()
    {
        $banners = Banner::orderBy('sort_order')->orderByDesc('created_at')->get();
        return view('admin.banners.index', compact('banners'));
    }

    public function create()
    {
        return view('admin.banners.form', ['banner' => new Banner]);
    }

    public function store(Request $request)
    {
        Banner::create($this->validated($request));
        return redirect()->route('adminhu.banners.index')->with('success', 'Banner created.');
    }

    public function show(Banner $banner) { return redirect()->route('adminhu.banners.edit', $banner); }

    public function edit(Banner $banner)
    {
        return view('admin.banners.form', compact('banner'));
    }

    public function update(Request $request, Banner $banner)
    {
        $banner->update($this->validated($request));
        return redirect()->route('adminhu.banners.edit', $banner)->with('success', 'Banner saved.');
    }

    public function destroy(Banner $banner)
    {
        $banner->delete();
        return redirect()->route('adminhu.banners.index')->with('success', 'Banner deleted.');
    }

    private function validated(Request $request): array
    {
        $data = $request->validate([
            'eyebrow'    => ['nullable', 'string', 'max:100'],
            'title'      => ['required', 'string', 'max:255'],
            'subtitle'   => ['nullable', 'string', 'max:500'],
            'btn_text'   => ['nullable', 'string', 'max:60'],
            'btn_url'    => ['nullable', 'string', 'max:500'],
            'theme'      => ['required', 'in:navy,amber,white,dark'],
            'is_active'  => ['nullable', 'boolean'],
            'sort_order' => ['nullable', 'integer'],
            'starts_at'  => ['nullable', 'date'],
            'ends_at'    => ['nullable', 'date', 'after_or_equal:starts_at'],
        ]);
        $data['is_active'] = $request->boolean('is_active');
        return $data;
    }
}
