<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Page;
use Illuminate\Http\Request;

class PageController extends Controller
{
    public function index()
    {
        $pages = Page::orderBy('sort_order')->orderBy('title')->get();
        return view('admin.pages.index', compact('pages'));
    }

    public function create()
    {
        return view('admin.pages.form', ['page' => new Page]);
    }

    public function store(Request $request)
    {
        $data = $this->validated($request);
        Page::create($data);
        return redirect()->route('adminhu.pages.index')->with('success', 'Page created.');
    }

    public function show(Page $page) { return redirect()->route('adminhu.pages.edit', $page); }

    public function edit(Page $page)
    {
        return view('admin.pages.form', compact('page'));
    }

    public function update(Request $request, Page $page)
    {
        $page->update($this->validated($request, $page));
        return redirect()->route('adminhu.pages.edit', $page)->with('success', 'Page saved.');
    }

    public function destroy(Page $page)
    {
        $page->delete();
        return redirect()->route('adminhu.pages.index')->with('success', 'Page deleted.');
    }

    private function validated(Request $request, ?Page $page = null): array
    {
        $data = $request->validate([
            'title'            => ['required', 'string', 'max:255'],
            'body'             => ['nullable', 'string'],
            'template'         => ['required', 'in:default,full-width,landing'],
            'is_published'     => ['nullable', 'boolean'],
            'show_in_nav'      => ['nullable', 'boolean'],
            'sort_order'       => ['nullable', 'integer'],
            'meta_title'       => ['nullable', 'string', 'max:160'],
            'meta_description' => ['nullable', 'string', 'max:320'],
        ]);

        $data['is_published'] = $request->boolean('is_published');
        $data['show_in_nav']  = $request->boolean('show_in_nav');

        return $data;
    }
}
