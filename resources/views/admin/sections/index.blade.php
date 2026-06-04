@extends('admin.layouts.app')
@section('title', 'Homepage Sections')

@section('content')
<div class="page-header">
  <div>
    <div class="page-title">Homepage Sections</div>
    <div class="page-sub">Content blocks for each page</div>
  </div>
  <div class="page-actions">
    <a href="{{ route('adminhu.sections.create') }}" class="btn btn-primary">
      <svg viewBox="0 0 24 24"><path d="M12 5v14M5 12h14"/></svg> New section
    </a>
  </div>
</div>

<div class="page-body">
  @forelse($sections as $pageName => $pageSections)
  <div style="margin-bottom:20px;">
    <div style="font-size:11px;font-weight:700;text-transform:uppercase;letter-spacing:.08em;color:var(--muted);margin-bottom:8px;">
      {{ ucfirst($pageName) }}
    </div>
    <div class="card">
      <div class="table-wrap">
        <table>
          <thead><tr><th>Label</th><th>Key</th><th>Visible</th><th>Sort</th><th style="width:80px;"></th></tr></thead>
          <tbody>
            @foreach($pageSections as $section)
            <tr>
              <td style="font-weight:600;">{{ $section->label }}</td>
              <td style="font-size:12px;color:var(--muted);font-family:monospace;">{{ $section->section_key }}</td>
              <td>
                <span class="badge {{ $section->is_visible ? 'badge-green' : 'badge-gray' }}">
                  {{ $section->is_visible ? 'Visible' : 'Hidden' }}
                </span>
              </td>
              <td style="color:var(--muted);">{{ $section->sort_order }}</td>
              <td>
                <div style="display:flex;gap:5px;">
                  <a href="{{ route('adminhu.sections.edit', $section) }}" class="btn btn-ghost btn-icon btn-icon sm">
                    <svg viewBox="0 0 24 24"><path d="M11 4H4a2 2 0 00-2 2v14a2 2 0 002 2h14a2 2 0 002-2v-7M18.5 2.5a2.121 2.121 0 013 3L12 15l-4 1 1-4 9.5-9.5z"/></svg>
                  </a>
                  <form method="POST" action="{{ route('adminhu.sections.destroy', $section) }}" onsubmit="return confirm('Delete section?')">
                    @csrf @method('DELETE')
                    <button class="btn btn-danger btn-icon btn-icon sm"><svg viewBox="0 0 24 24"><polyline points="3 6 5 6 21 6"/><path d="M19 6l-1 14a2 2 0 01-2 2H8a2 2 0 01-2-2L5 6m3 0V4a2 2 0 012-2h4a2 2 0 012 2v2"/></svg></button>
                  </form>
                </div>
              </td>
            </tr>
            @endforeach
          </tbody>
        </table>
      </div>
    </div>
  </div>
  @empty
  <div class="card">
    <div class="empty-state" style="padding:60px;">
      <div class="t">No sections yet</div>
      <div class="s"><a href="{{ route('adminhu.sections.create') }}" style="color:var(--navy);">Create your first section →</a></div>
    </div>
  </div>
  @endforelse
</div>
@endsection
