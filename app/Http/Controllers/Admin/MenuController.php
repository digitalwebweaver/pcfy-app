<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Menu;
use App\Models\MenuItem;
use App\Models\Page;
use Illuminate\Http\Request;

class MenuController extends Controller
{
    public function index()
    {
        $menus = Menu::withCount('allItems')->get();
        return view('admin.menus.index', compact('menus'));
    }

    public function create()
    {
        return view('admin.menus.form', ['menu' => new Menu]);
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name'     => ['required', 'string', 'max:100'],
            'location' => ['required', 'string', 'max:50'],
        ]);
        $menu = Menu::create($data);
        return redirect()->route('adminhu.menus.items', $menu)->with('success', 'Menu created.');
    }

    public function show(Menu $menu) { return redirect()->route('adminhu.menus.items', $menu); }

    public function edit(Menu $menu)
    {
        return view('admin.menus.form', compact('menu'));
    }

    public function update(Request $request, Menu $menu)
    {
        $data = $request->validate([
            'name'     => ['required', 'string', 'max:100'],
            'location' => ['required', 'string', 'max:50'],
        ]);
        $menu->update($data);
        return redirect()->route('adminhu.menus.index')->with('success', 'Menu updated.');
    }

    public function destroy(Menu $menu)
    {
        $menu->allItems()->delete();
        $menu->delete();
        return redirect()->route('adminhu.menus.index')->with('success', 'Menu deleted.');
    }

    public function items(Menu $menu)
    {
        $items      = $menu->allItems()->with(['page', 'category'])->get();
        $pages      = Page::published()->orderBy('title')->get();
        $categories = Category::orderBy('name')->get();
        return view('admin.menus.items', compact('menu', 'items', 'pages', 'categories'));
    }

    public function storeItem(Request $request, Menu $menu)
    {
        $data = $request->validate([
            'label'       => ['required', 'string', 'max:100'],
            'type'        => ['required', 'in:url,page,category'],
            'url'         => ['nullable', 'string', 'max:500'],
            'page_id'     => ['nullable', 'exists:pages,id'],
            'category_id' => ['nullable', 'exists:categories,id'],
            'open_new_tab'=> ['nullable', 'boolean'],
        ]);
        $data['menu_id']      = $menu->id;
        $data['is_visible']   = true;
        $data['open_new_tab'] = $request->boolean('open_new_tab');
        $data['sort_order']   = $menu->allItems()->max('sort_order') + 1;

        MenuItem::create($data);
        return back()->with('success', 'Item added.');
    }

    public function destroyItem(Menu $menu, MenuItem $item)
    {
        $item->delete();
        return back()->with('success', 'Item removed.');
    }

    public function reorder(Request $request, Menu $menu)
    {
        $request->validate(['order' => ['required', 'array']]);
        foreach ($request->order as $i => $id) {
            MenuItem::where('id', $id)->where('menu_id', $menu->id)->update(['sort_order' => $i]);
        }
        return response()->json(['ok' => true]);
    }
}
