@if($rating_mode_accessible)
    @if($rating_mode)
        <a href="{{ $url_nonrating }}" class="right-link">Voir les détails</a>
    @else
        <a href="{{ $url_rating }}" class="right-link">Voir les scores</a>
    @endif
@endif
