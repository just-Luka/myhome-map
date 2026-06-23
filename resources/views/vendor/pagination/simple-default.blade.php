@if ($paginator->hasPages())
<div class="pagination">
    @if ($paginator->onFirstPage())
        <span style="opacity:.35;padding:6px 12px;border-radius:6px;background:#14172a;border:1px solid #1e2235;font-size:13px;color:#94a3b8">← Prev</span>
    @else
        <a href="{{ $paginator->previousPageUrl() }}">← Prev</a>
    @endif

    @if ($paginator->hasMorePages())
        <a href="{{ $paginator->nextPageUrl() }}">Next →</a>
    @else
        <span style="opacity:.35;padding:6px 12px;border-radius:6px;background:#14172a;border:1px solid #1e2235;font-size:13px;color:#94a3b8">Next →</span>
    @endif
</div>
@endif
