<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Spatie\MediaLibrary\MediaCollections\Models\Media;

class MediaController extends Controller
{
    public function index(Request $request)
    {
        $query = Media::latest();

        if ($request->filled('q')) {
            $query->where('file_name', 'like', "%{$request->q}%");
        }
        if ($request->filled('type')) {
            $query->where('mime_type', 'like', $request->type.'%');
        }

        $media = $query->paginate(40)->withQueryString();
        return view('admin.media.index', compact('media'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'files'   => ['required', 'array'],
            'files.*' => ['file', 'max:10240'],
        ]);

        $uploaded = 0;
        foreach ($request->file('files', []) as $file) {
            \App\Models\Product::first()?->addMedia($file)->withCustomProperties(['standalone' => true])->toMediaCollection('standalone');
            $uploaded++;
        }

        return back()->with('success', "{$uploaded} file(s) uploaded.");
    }

    public function destroy(Media $media)
    {
        $media->delete();
        return back()->with('success', 'File deleted.');
    }
}
