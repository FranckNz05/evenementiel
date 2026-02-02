@props(['paginator'])

@if ($paginator->hasPages())
    <div class="mt-5">
        <nav aria-label="Page navigation">
            <ul class="pagination justify-content-center">
                <!-- Premier -->
                <li class="page-item {{ $paginator->onFirstPage() ? 'disabled' : '' }}">
                    <a class="page-link" href="{{ $paginator->url(1) }}&{{ http_build_query(request()->except('page')) }}" aria-label="First">
                        <span aria-hidden="true">&laquo;&laquo;</span>
                    </a>
                </li>
                <!-- Précédent -->
                <li class="page-item {{ $paginator->onFirstPage() ? 'disabled' : '' }}">
                    <a class="page-link" href="{{ $paginator->previousPageUrl() }}&{{ http_build_query(request()->except('page')) }}" aria-label="Previous">
                        <span aria-hidden="true">&laquo;</span>
                    </a>
                </li>

                <!-- Pages -->
                @php
                    $current = $paginator->currentPage();
                    $last = $paginator->lastPage();
                    $window = 2; // Nombre de pages à afficher de chaque côté de la page actuelle
                    
                    $start = max(1, $current - $window);
                    $end = min($last, $current + $window);
                    
                    // Afficher toujours la première page
                    if ($start > 1) {
                        echo '<li class="page-item"><a class="page-link" href="' . $paginator->url(1) . '&' . http_build_query(request()->except('page')) . '">1</a></li>';
                        if ($start > 2) {
                            echo '<li class="page-item disabled"><span class="page-link">...</span></li>';
                        }
                    }
                    
                    // Afficher les pages autour de la page actuelle
                    for ($i = $start; $i <= $end; $i++) {
                        echo '<li class="page-item ' . ($i == $current ? 'active' : '') . '">';
                        echo '<a class="page-link" href="' . $paginator->url($i) . '&' . http_build_query(request()->except('page')) . '">' . $i . '</a>';
                        echo '</li>';
                    }
                    
                    // Afficher toujours la dernière page
                    if ($end < $last) {
                        if ($end < $last - 1) {
                            echo '<li class="page-item disabled"><span class="page-link">...</span></li>';
                        }
                        echo '<li class="page-item"><a class="page-link" href="' . $paginator->url($last) . '&' . http_build_query(request()->except('page')) . '">' . $last . '</a></li>';
                    }
                @endphp

                <!-- Suivant -->
                <li class="page-item {{ !$paginator->hasMorePages() ? 'disabled' : '' }}">
                    <a class="page-link" href="{{ $paginator->nextPageUrl() }}&{{ http_build_query(request()->except('page')) }}" aria-label="Next">
                        <span aria-hidden="true">&raquo;</span>
                    </a>
                </li>
                <!-- Dernier -->
                <li class="page-item {{ !$paginator->hasMorePages() ? 'disabled' : '' }}">
                    <a class="page-link" href="{{ $paginator->url($paginator->lastPage()) }}&{{ http_build_query(request()->except('page')) }}" aria-label="Last">
                        <span aria-hidden="true">&raquo;&raquo;</span>
                    </a>
                </li>
            </ul>
        </nav>
    </div>
@endif