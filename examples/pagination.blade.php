@if ($paginator->hasPages())
    <nav class="page-nav">
        <ul class="page-nav-list">
            @if ($paginator->currentPage() > 2)
                <li class="page-nav-item">
                    <a class="page-nav-link"
                       href="{{ Route::localize(request()->route()->getName(), ['page' => 1] + request()->except(['page'])) }}"
                       aria-label="First">&laquo;</a>
                </li>
            @endif
            @if ($paginator->onFirstPage())
                <li class="page-nav-item page-nav-disabled" aria-disabled="true" aria-label="Prev">
                    <span class="page-nav-link" aria-hidden="true">&lsaquo;</span>
                </li>
            @else
                <li class="page-nav-item">
                    <a class="page-nav-link"
                       href="{{ Route::localize(request()->route()->getName(), ['page' => $paginator->currentPage() - 1] + request()->except(['page'])) }}"
                       rel="prev" aria-label="Prev">&lsaquo;</a>
                </li>
            @endif
            @foreach ($elements as $element)
                @if (is_string($element))
                    <li class="page-nav-item page-nav-disabled" aria-disabled="true">
                        <span class="page-nav-link">{{ $element }}</span>
                    </li>
                @endif
                @if (is_array($element))
                    @foreach ($element as $page => $url)
                        @if ($page == $paginator->currentPage())
                            <li class="page-nav-item page-nav-current" aria-current="page">
                                <span class="page-nav-link">{{ $page }}</span>
                            </li>
                        @else
                            <li class="page-nav-item">
                                <a class="page-nav-link"
                                   href="{{ Route::localize(request()->route()->getName(), ['page' => $page] + request()->except(['page'])) }}">
                                    {{ $page }}
                                </a>
                            </li>
                        @endif
                    @endforeach
                @endif
            @endforeach
            @if ($paginator->hasMorePages())
                <li class="page-nav-item">
                    <a class="page-nav-link"
                       href="{{ Route::localize(request()->route()->getName(), ['page' => $paginator->currentPage() + 1] + request()->except(['page'])) }}"
                       rel="next" aria-label="Next">&rsaquo;</a>
                </li>
            @else
                <li class="page-nav-item page-nav-disabled" aria-disabled="true" aria-label="Next">
                    <span class="page-nav-link" aria-hidden="true">&rsaquo;</span>
                </li>
            @endif
            @php $lastPage = $paginator->lastPage(); @endphp
            @if ($paginator->currentPage() < $lastPage - 1)
                <li class="page-nav-item">
                    <a class="page-nav-link"
                       href="{{ Route::localize(request()->route()->getName(), ['page' => $lastPage] + request()->except(['page'])) }}"
                       aria-label="Last">&raquo;</a>
                </li>
            @endif
        </ul>
    </nav>
@endif
