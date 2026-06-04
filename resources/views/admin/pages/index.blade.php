@extends('admin.layouts.app')
@section('title', 'Static Pages')

@section('content')
<div class="page-header">
  <div>
    <div class="page-title">Static Pages</div>
    <div class="page-sub">{{ $pages->count() }} pages</div>
  </div>
  <div class="page-actions">
    <a href="{{ route('adminhu.pages.create') }}" class="btn btn-primary">
      <svg viewBox="0 0 24 24"><path d="M12 5v14M5 12h14"/></svg> New page
    </a>
  </div>
</div>

<div class="page-body">
  <div class="card">
    <div class="table-wrap">
      <table>
        <thead><tr><th>Title</th><th>Slug</th><th>Template</th><th>Nav</th><th>Status</th><th style="width:80px;"></th></tr></thead>
        <tbody>
          @forelse($pages as $page)
          <tr>
            <td style="font-weight:600;">{{ $page->title }}</td>
            <td style="font-size:12px;color:var(--muted);">/{{ $page->slug }}</td>
            <td style="font-size:12px;color:var(--muted);">{{ ucfirst($page->template) }}</td>
            <td>
              @if($page->show_in_nav)
                <span class="badge badge-green">Yes</span>
              @else
                <span class="badge badge-gray">No</span>
              @endif
            </td>
            <td>
              <span class="badge {{ $page->is_published ? 'badge-green' : 'badge-gray' }}">
                {{ $page->is_published ? 'Published' : 'Draft' }}
              </span>
            </td>
            <td>
              <div style="display:flex;gap:5px;">
                <a href="{{ route('adminhu.pages.edit', $page) }}" class="btn btn-ghost btn-icon btn-icon sm">
                  <svg viewBox="0 0 24 24"><path d="M11 4H4a2 2 0 00-2 2v14a2 2 0 002 2h14a2 2 0 002-2v-7M18.5 2.5a2.121 2.121 0 013 3L12 15l-4 1 1-4 9.5-9.5z"/></svg>
                </a>
                <form method="POST" action="{{ route('adminhu.pages.destroy', $page) }}" onsubmit="return confirm('Delete this page?')">
                  @csrf @method('DELETE')
                  <button class="btn btn-danger btn-icon btn-icon sm"><svg viewBox="0 0 24 24"><polyline points="3 6 5 6 21 6"/><path d="M19 6l-1 14a2 2 0 01-2 2H8a2 2 0 01-2-2L5 6m3 0V4a2 2 0 012-2h4a2 2 0 012 2v2"/></svg></button>
                </form>
              </div>
            </td>
          </tr>
          @empty
          <tr><td colspan="6"><div class="empty-state" style="padding:40px;"><div class="t">No pages yet</div><div class="s"><a href="{{ route('adminhu.pages.create') }}" style="color:var(--navy);">Create your first page →</a></div></div></td></tr>
          @endforelse
        </tbody>
      </table>
    </div>
  </div>
</div>
@endsection
