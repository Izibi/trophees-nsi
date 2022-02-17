@if($awards_count !== false)
    @if($awards_count['award_mixed'] > $awards_limit['award_mixed'])
        <div class="alert alert-danger">
            award_mixed quantity exceeded ({{ $awards_count['award_mixed'] }} of {{ $awards_limit['award_mixed'] }})
        </div>
    @endif
    @if($awards_count['award_citizenship'] > $awards_limit['award_citizenship'])
        <div class="alert alert-danger">
            award_citizenship quantity exceeded ({{ $awards_count['award_citizenship'] }} of {{ $awards_limit['award_citizenship'] }})
        </div>
    @endif
    @if($awards_count['award_engineering'] > $awards_limit['award_engineering'])
        <div class="alert alert-danger">
            award_engineering quantity exceeded ({{ $awards_count['award_engineering'] }} of {{ $awards_limit['award_engineering'] }})
        </div>
    @endif
    @if($awards_count['award_heart'] > $awards_limit['award_heart'])
        <div class="alert alert-danger">
            award_heart quantity exceeded ({{ $awards_count['award_heart'] }} of {{ $awards_limit['award_heart'] }})
        </div>
    @endif
    @if($awards_count['award_originality'] > $awards_limit['award_originality'])
        <div class="alert alert-danger">
            award_originality quantity exceeded ({{ $awards_count['award_originality'] }} of {{ $awards_limit['award_originality'] }})
        </div>
    @endif
@endif