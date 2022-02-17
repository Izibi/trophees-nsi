@if($rating_mode_accessible)
    @if($rating_mode)
        <a href="/projects" class="right-link">Show details</a>
    @else
        <a href="/projects?rating_mode=1" class="right-link">Show ratings</a>
    @endif
@endif