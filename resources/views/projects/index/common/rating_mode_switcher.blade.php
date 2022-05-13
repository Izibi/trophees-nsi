@if($rating_mode_accessible)
    @if($rating_mode)
        <a href="/projects" class="right-link">Voir les détails</a>
    @else
        <a href="/projects?rating_mode=1" class="right-link">Voir les scores</a>
    @endif
@endif
