@extends('admin.layouts.app')
@section('title', 'Blog Posts')

@section('content')
<div class="page-header">
  <div>
    <div class="page-title">Blog Posts</div>
    <div class="page-sub">{{ $counts['all'] }} total · {{ $counts['published'] }} published · {{ $counts['draft'] }} drafts</div>
  </div>
  <div class="page-actions">
    <a href="{{ route('adminhu.blog.categories') }}" class="btn btn-ghost">Categories</a>
    <a href="{{ route('adminhu.blog.create') }}" class="btn btn-primary">
      <svg viewBox="0 0 24 24"><path d="M12 5v14M5 12h14"/></svg> New post
    </a>
  </div>
</div>

<div class="page-body">
  @php $statusColors = ['published'=>'badge-green','draft'=>'badge-gray','scheduled'=>'badge-amber']; @endphp

  {{-- Tabs --}}
  <div class="tabs" style="margin-bottom:12px;">
    @foreach(['all'=>'All','published'=>'Published','draft'=>'Draft','scheduled'=>'Scheduled'] as $s=>$l)
    <a href="{{ request()->fullUrlWithQuery(['status'=>$s==='all'?null:$s,'page'=>null]) }}"
       class="tab {{ request('status',$s==='all'?null:'x')===$s||($s==='all'&&!request('status'))?'active':'' }}" style="text-decoration:none;">
      {{ $l }} <span style="margin-left:4px;background:var(--bg);border:1px solid var(--border);border-radius:10px;padding:0 6px;font-size:10px;font-weight:700;color:var(--muted);">{{ $counts[$s] }}</span>
    </a>
    @endforeach
  </div>

  <form method="GET" action="{{ route('adminhu.blog.index') }}" style="margin-bottom:12px;display:flex;gap:8px;flex-wrap:wrap;">
    <input type="hidden" name="status" value="{{ request('status') }}">
    <input class="input" type="text" name="q" value="{{ request('q') }}" placeholder="Search title…" style="width:240px;">
    <select class="select" name="cat" style="width:auto;" onchange="this.form.submit()">
      <option value="">All categories</option>
      @foreach($categories as $cat)
        <option value="{{ $cat->id }}" {{ request('cat')==$cat->id?'selected':'' }}>{{ $cat->name }}</option>
      @endforeach
    </select>
    <button type="submit" class="btn btn-ghost">Search</button>
    @if(request()->anyFilled(['q','cat']))
      <a href="{{ route('adminhu.blog.index') }}" class="btn btn-ghost" style="color:var(--red);">Clear</a>
    @endif
  </form>

  <div class="card">
    <div class="table-wrap">
      <table>
        <thead><tr><th>Title</th><th>Category</th><th>Status</th><th>Author</th><th>Published</th><th style="width:80px;"></th></tr></thead>
        <tbody>
          @forelse($posts as $post)
          <tr>
            <td>
              <div style="font-weight:600;max-width:320px;">{{ $post->title }}</div>
              @if($post->excerpt)<div style="font-size:11.5px;color:var(--muted);white-space:nowrap;overflow:hidden;text-overflow:ellipsis;max-width:320px;">{{ $post->excerpt }}</div>@endif
            </td>
            <td style="color:var(--muted);">{{ $post->category?->name ?? '—' }}</td>
            <td><span class="badge {{ $statusColors[$post->status] ?? 'badge-gray' }}">{{ ucfirst($post->status) }}</span></td>
            <td style="font-size:12px;color:var(--muted);">{{ $post->author?->name ?? '—' }}</td>
            <td style="font-size:11.5px;color:var(--muted);">{{ $post->published_at?->format('d M Y') ?? '—' }}</td>
            <td>
              <div style="display:flex;gap:5px;">
                <a href="{{ route('adminhu.blog.edit', $post) }}" class="btn btn-ghost btn-icon btn-icon sm">
                  <svg viewBox="0 0 24 24"><path d="M11 4H4a2 2 0 00-2 2v14a2 2 0 002 2h14a2 2 0 002-2v-7M18.5 2.5a2.121 2.121 0 013 3L12 15l-4 1 1-4 9.5-9.5z"/></svg>
                </a>
                <form method="POST" action="{{ route('adminhu.blog.destroy', $post) }}" onsubmit="return confirm('Delete?')">
                  @csrf @method('DELETE')
                  <button class="btn btn-danger btn-icon btn-icon sm"><svg viewBox="0 0 24 24"><polyline points="3 6 5 6 21 6"/><path d="M19 6l-1 14a2 2 0 01-2 2H8a2 2 0 01-2-2L5 6m3 0V4a2 2 0 012-2h4a2 2 0 012 2v2"/></svg></button>
                </form>
              </div>
            </td>
          </tr>
          @empty
          <tr><td colspan="6"><div class="empty-state" style="padding:40px;"><div class="t">No posts yet</div><div class="s"><a href="{{ route('adminhu.blog.create') }}" style="color:var(--navy);">Write your first post →</a></div></div></td></tr>
          @endforelse
        </tbody>
      </table>
    </div>
    @if($posts->hasPages())
    <div style="padding:10px 16px;border-top:1px solid var(--border);">{{ $posts->appends(request()->query())->links() }}</div>
    @endif
  </div>
</div>
@endsection
