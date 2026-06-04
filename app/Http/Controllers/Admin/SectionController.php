<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Section;
use Illuminate\Http\Request;

class SectionController extends Controller
{
    public function index()
    {
        $sections = Section::orderBy('page')->orderBy('sort_order')->get()->groupBy('page');
        return view('admin.sections.index', compact('sections'));
    }

    public function create()
    {
        return view('admin.sections.form', ['section' => new Section]);
    }

    public function store(Request $request)
    {
        $data = $this->validated($request);
        Section::create($data);
        return redirect()->route('adminhu.sections.index')->with('success', 'Section created.');
    }

    public function show(Section $section) { return redirect()->route('adminhu.sections.edit', $section); }

    public function edit(Section $section)
    {
        return view('admin.sections.form', compact('section'));
    }

    public function update(Request $request, Section $section)
    {
        $section->update($this->validated($request));
        return redirect()->route('adminhu.sections.edit', $section)->with('success', 'Section saved.');
    }

    public function destroy(Section $section)
    {
        $section->delete();
        return redirect()->route('adminhu.sections.index')->with('success', 'Section deleted.');
    }

    private function validated(Request $request): array
    {
        $data = $request->validate([
            'page'        => ['required', 'string', 'max:50'],
            'section_key' => ['required', 'string', 'max:50'],
            'label'       => ['required', 'string', 'max:100'],
            'is_visible'  => ['nullable', 'boolean'],
            'sort_order'  => ['nullable', 'integer'],
            'content'     => ['nullable', 'array'],
        ]);
        $data['is_visible'] = $request->boolean('is_visible');

        // collect content fields from request
        $content = [];
        foreach ($request->all() as $key => $val) {
            if (str_starts_with($key, 'content_')) {
                $content[substr($key, 8)] = $val;
            }
        }
        $data['content'] = $content ?: null;

        return $data;
    }
}
