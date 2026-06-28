@if ($paginator->hasPages())
<div class="pagination">
    {{-- Prev --}}
    @if ($paginator->onFirstPage())
        <span class="pg-disabled">‹</span>
    @else
        <a href="{{ $paginator->previousPageUrl() }}">‹</a>
    @endif

    {{-- Page numbers --}}
    @foreach ($elements as $element)
        @if (is_string($element))
            <span class="pg-dots">…</span>
        @endif
        @if (is_array($element))
            @foreach ($element as $page => $url)
                @if ($page == $paginator->currentPage())
                    <span class="pg-active">{{ $page }}</span>
                @else
                    <a href="{{ $url }}">{{ $page }}</a>
                @endif
            @endforeach
        @endif
    @endforeach

    {{-- Next --}}
    @if ($paginator->hasMorePages())
        <a href="{{ $paginator->nextPageUrl() }}">›</a>
    @else
        <span class="pg-disabled">›</span>
    @endif
</div>
@endif
