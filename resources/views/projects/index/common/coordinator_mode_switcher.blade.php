@if($coordinator && $view['type'] != 'own')
    @if($coordinator_mode)
        <a href="{{ $url_noncoordinatorjury }}" class="right-link">Voir les projets à évaluer</a>
    @else
        <a href="{{ $url_coordinatorjury }}" class="right-link">Voir tous les projets</a>
    @endif
@endif
