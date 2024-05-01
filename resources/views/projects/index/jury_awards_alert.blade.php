@if($awards_count !== false)
    @if($awards_count['award_mixed'] > $awards_limit['award_mixed'])
        <div class="alert alert-danger">
            Limite d'attribution du prix mixed atteinte ({{ $awards_count['award_mixed'] }} sur {{ $awards_limit['award_mixed'] }})
        </div>
    @endif
    @if($awards_count['award_citizenship'] > $awards_limit['award_citizenship'])
        <div class="alert alert-danger">
            Limite d'attribution du prix citizenship atteinte ({{ $awards_count['award_citizenship'] }} sur {{ $awards_limit['award_citizenship'] }})
        </div>
    @endif
    @if($awards_count['award_engineering'] > $awards_limit['award_engineering'])
        <div class="alert alert-danger">
            Limite d'attribution du prix théatique atteinte ({{ $awards_count['award_engineering'] }} sur {{ $awards_limit['award_engineering'] }})
        </div>
    @endif
    @if($awards_count['award_heart'] > $awards_limit['award_heart'])
        <div class="alert alert-danger">
            Limite d'attribution du prix spécial du jury atteinte ({{ $awards_count['award_heart'] }} sur {{ $awards_limit['award_heart'] }})
        </div>
    @endif
    @if($awards_count['award_originality'] > $awards_limit['award_originality'])
        <div class="alert alert-danger">
            Limite d'attribution du prix de la créativité atteinte ({{ $awards_count['award_originality'] }} sur {{ $awards_limit['award_originality'] }})
        </div>
    @endif
@endif
