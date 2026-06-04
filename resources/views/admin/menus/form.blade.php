@extends('admin.layouts.app')
@section('title', $menu->exists ? 'Edit Menu' : 'New Menu')

@section('content')
<div class="page-header">
  <div><div class="page-title">{{ $menu->exists ? 'Edit: '.$menu->name : 'New Menu' }}</div></div>
  <div class="page-actions">
    <a href="{{ route('adminhu.menus.index') }}" class="btn btn-ghost">← Back</a>
  </div>
</div>

<div class="page-body">
<form method="POST"
      action="{{ $menu->exists ? route('adminhu.menus.update', $menu) : route('adminhu.menus.store') }}"
      style="max-width:480px;">
  @csrf
  @if($menu->exists) @method('PUT') @endif

  <div class="card">
    <div class="card-body" style="display:flex;flex-direction:column;gap:12px;">
      <div class="field">
        <label>Menu name <span class="req">*</span></label>
        <input class="input" type="text" name="name" value="{{ old('name', $menu->name) }}" required placeholder="e.g. Main Navigation">
        @error('name')<div class="field-error">{{ $message }}</div>@enderror
      </div>
      <div class="field">
        <label>Location <span class="req">*</span></label>
        <select class="select" name="location" style="width:100%;">
          @foreach(['primary' => 'Primary (main nav)', 'footer' => 'Footer', 'mobile' => 'Mobile', 'sidebar' => 'Sidebar'] as $val => $lbl)
            <option value="{{ $val }}" {{ old('location', $menu->location) === $val ? 'selected' : '' }}>{{ $lbl }}</option>
          @endforeach
        </select>
        @error('location')<div class="field-error">{{ $message }}</div>@enderror
      </div>
      <div style="display:flex;gap:8px;">
        <button type="submit" class="btn btn-primary">{{ $menu->exists ? 'Save' : 'Create' }}</button>
        <a href="{{ route('adminhu.menus.index') }}" class="btn btn-ghost">Cancel</a>
      </div>
    </div>
  </div>
</form>
</div>
@endsection
