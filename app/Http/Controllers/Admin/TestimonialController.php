<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Testimonial;
use Illuminate\Http\Request;

class TestimonialController extends Controller
{
    public function index()
    {
        $testimonials = Testimonial::orderBy('sort_order')->orderByDesc('created_at')->get();
        return view('admin.testimonials.index', compact('testimonials'));
    }

    public function create()
    {
        return view('admin.testimonials.form', ['testimonial' => new Testimonial]);
    }

    public function store(Request $request)
    {
        Testimonial::create($this->validated($request));
        return redirect()->route('adminhu.testimonials.index')->with('success', 'Testimonial added.');
    }

    public function show(Testimonial $testimonial) { return redirect()->route('adminhu.testimonials.edit', $testimonial); }

    public function edit(Testimonial $testimonial)
    {
        return view('admin.testimonials.form', compact('testimonial'));
    }

    public function update(Request $request, Testimonial $testimonial)
    {
        $testimonial->update($this->validated($request));
        return redirect()->route('adminhu.testimonials.edit', $testimonial)->with('success', 'Testimonial saved.');
    }

    public function destroy(Testimonial $testimonial)
    {
        $testimonial->delete();
        return redirect()->route('adminhu.testimonials.index')->with('success', 'Testimonial deleted.');
    }

    private function validated(Request $request): array
    {
        $data = $request->validate([
            'name'        => ['required', 'string', 'max:100'],
            'role'        => ['nullable', 'string', 'max:100'],
            'city'        => ['nullable', 'string', 'max:100'],
            'body'        => ['required', 'string'],
            'rating'      => ['nullable', 'integer', 'min:1', 'max:5'],
            'is_verified' => ['nullable', 'boolean'],
            'is_active'   => ['nullable', 'boolean'],
            'sort_order'  => ['nullable', 'integer'],
        ]);
        $data['is_verified'] = $request->boolean('is_verified');
        $data['is_active']   = $request->boolean('is_active');
        return $data;
    }
}
