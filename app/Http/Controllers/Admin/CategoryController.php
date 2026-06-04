<?php
namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Category;
use Illuminate\Http\Request;

class CategoryController extends Controller
{
    public function index()
    {
        $categories = Category::with('parent')
            ->withCount('products')
            ->orderBy('sort_order')
            ->orderBy('name')
            ->paginate(40);
        return view('admin.categories.index', compact('categories'));
    }

    public function create()
    {
        $parents = Category::whereNull('parent_id')->orderBy('name')->get();
        return view('admin.categories.form', ['category' => new Category, 'parents' => $parents]);
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name'             => ['required', 'string', 'max:100'],
            'parent_id'        => ['nullable', 'exists:categories,id'],
            'description'      => ['nullable', 'string', 'max:500'],
            'icon_svg'         => ['nullable', 'string'],
            'is_visible'       => ['nullable', 'boolean'],
            'sort_order'       => ['nullable', 'integer'],
            'meta_title'       => ['nullable', 'string', 'max:160'],
            'meta_description' => ['nullable', 'string', 'max:320'],
        ]);

        $data['is_visible'] = $request->boolean('is_visible', true);
        $data['sort_order'] = $data['sort_order'] ?? 0;
        $data['parent_id']  = $data['parent_id'] ?: null;

        Category::create($data);
        return redirect()->route('adminhu.categories.index')->with('success', 'Category created.');
    }

    public function show(Category $category) { return redirect()->route('adminhu.categories.edit', $category); }

    public function edit(Category $category)
    {
        $parents = Category::whereNull('parent_id')->where('id', '!=', $category->id)->orderBy('name')->get();
        return view('admin.categories.form', compact('category', 'parents'));
    }

    public function update(Request $request, Category $category)
    {
        $data = $request->validate([
            'name'             => ['required', 'string', 'max:100'],
            'parent_id'        => ['nullable', 'exists:categories,id'],
            'description'      => ['nullable', 'string', 'max:500'],
            'icon_svg'         => ['nullable', 'string'],
            'is_visible'       => ['nullable', 'boolean'],
            'sort_order'       => ['nullable', 'integer'],
            'meta_title'       => ['nullable', 'string', 'max:160'],
            'meta_description' => ['nullable', 'string', 'max:320'],
        ]);

        $data['is_visible'] = $request->boolean('is_visible', true);
        $data['sort_order'] = $data['sort_order'] ?? 0;
        $data['parent_id']  = $data['parent_id'] ?: null;

        $category->update($data);
        return redirect()->route('adminhu.categories.index')->with('success', 'Category updated.');
    }

    public function destroy(Category $category)
    {
        if ($category->products()->count()) {
            return back()->with('error', 'Cannot delete category with products. Reassign products first.');
        }
        if ($category->children()->count()) {
            return back()->with('error', 'Cannot delete category with sub-categories. Remove children first.');
        }
        $category->delete();
        return redirect()->route('adminhu.categories.index')->with('success', 'Category deleted.');
    }
}
