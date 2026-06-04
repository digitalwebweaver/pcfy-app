<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\TrustItem;
use Illuminate\Http\Request;

class TrustItemController extends Controller
{
    public function index()
    {
        $items = TrustItem::orderBy('sort_order')->get();
        return view('admin.trust.index', compact('items'));
    }

    public function create()
    {
        return view('admin.trust.form', ['item' => new TrustItem]);
    }

    public function store(Request $request)
    {
        TrustItem::create($this->validated($request));
        return redirect()->route('adminhu.trust.index')->with('success', 'Trust item added.');
    }

    public function show(TrustItem $trust) { return redirect()->route('adminhu.trust.edit', $trust); }

    public function edit(TrustItem $trust)
    {
        return view('admin.trust.form', ['item' => $trust]);
    }

    public function update(Request $request, TrustItem $trust)
    {
        $trust->update($this->validated($request));
        return redirect()->route('adminhu.trust.edit', $trust)->with('success', 'Trust item saved.');
    }

    public function destroy(TrustItem $trust)
    {
        $trust->delete();
        return redirect()->route('adminhu.trust.index')->with('success', 'Trust item deleted.');
    }

    private function validated(Request $request): array
    {
        $data = $request->validate([
            'icon_svg'   => ['nullable', 'string'],
            'title'      => ['required', 'string', 'max:100'],
            'subtitle'   => ['nullable', 'string', 'max:200'],
            'is_visible' => ['nullable', 'boolean'],
            'sort_order' => ['nullable', 'integer'],
        ]);
        $data['is_visible'] = $request->boolean('is_visible');
        return $data;
    }
}
