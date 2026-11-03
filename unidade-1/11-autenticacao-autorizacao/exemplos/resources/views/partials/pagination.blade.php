@if ($paginator->hasPages())
    <nav aria-label="Paginação" class="flex flex-wrap items-center justify-between gap-3 border-t border-slate-200 pt-4">
        @if ($paginator->onFirstPage())
            <span class="text-slate-500">Anterior</span>
        @else
            <a href="{{ $paginator->previousPageUrl() }}" rel="prev" class="text-blue-700 underline">Anterior</a>
        @endif

        <span>Página {{ $paginator->currentPage() }} de {{ $paginator->lastPage() }}</span>

        @if ($paginator->hasMorePages())
            <a href="{{ $paginator->nextPageUrl() }}" rel="next" class="text-blue-700 underline">Próxima</a>
        @else
            <span class="text-slate-500">Próxima</span>
        @endif
    </nav>
@endif
