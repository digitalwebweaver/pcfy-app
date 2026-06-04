@if ($paginator->hasPages())
<nav class="pag-nav" aria-label="Pagination">
  <div class="pag-info">
    Showing <b>{{ $paginator->firstItem() }}–{{ $paginator->lastItem() }}</b> of <b>{{ $paginator->total() }}</b>
  </div>
  <div class="pag-pages">
    {{-- Previous --}}
    @if ($paginator->onFirstPage())
      <span class="pag-btn disabled" aria-disabled="true">‹</span>
    @else
      <a class="pag-btn" href="{{ $paginator->previousPageUrl() }}" rel="prev">‹</a>
    @endif

    {{-- Page numbers --}}
    @foreach ($elements as $element)
      @if (is_string($element))
        <span class="pag-btn dots">…</span>
      @endif
      @if (is_array($element))
        @foreach ($element as $page => $url)
          @if ($page == $paginator->currentPage())
            <span class="pag-btn active" aria-current="page">{{ $page }}</span>
          @else
            <a class="pag-btn" href="{{ $url }}">{{ $page }}</a>
          @endif
        @endforeach
      @endif
    @endforeach

    {{-- Next --}}
    @if ($paginator->hasMorePages())
      <a class="pag-btn" href="{{ $paginator->nextPageUrl() }}" rel="next">›</a>
    @else
      <span class="pag-btn disabled" aria-disabled="true">›</span>
    @endif
  </div>
</nav>
@endif
