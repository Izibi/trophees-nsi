@extends('layout')

@section('content')
    <div class="row">
        <div class="col-7">
            @include('projects.details')
            @if($can_award)
            <br>
            <p>
                <a href="{{ route('awards.edit', ['project' => $project->id]) }}" class="btn btn-primary">
                    {{ count($relevant_awards ?? $awards) > 0 ? 'Modifier l\'attribution de prix' : 'Attribuer un prix' }}
                </a>
            </p>
            @endif
        </div>
        <div class="col-5">
            <div class="card">
                <div class="card-header">
                    <ul class="nav nav-tabs card-header-tabs" id="myTab" role="tablist">
                        <li class="nav-item">
                            <a class="nav-link active" data-toggle="tab" href="#panel-my-ratings" role="tab">
                                Mes notes {{ $rating && !$rating->published ? '(draft)' : '' }}
                            </a>
                        </li>
                        @if(($contest->status == 'deliberating-territorial' || $contest->status == 'deliberating-national') || (($user->hasRole('coordinator') || $user->hasRole('president-territorial') || $user->hasRole('president-prize')) && ($contest->status == 'grading-territorial' || $contest->status == 'grading-national')))
                            <li class="nav-item">
                                <a class="nav-link" data-toggle="tab" href="#panel-aggregated-ratings" role="tab">Moyenne des notes</a>
                            </li>
                        @endif
                    </ul>
                </div>
                <div class="card-body tab-content p-0">
                    <div class="tab-pane fade show active p-3" id="panel-my-ratings" role="tabpanel">
                        @include('projects.rating.edit')
                    </div>
                    @if(($contest->status == 'deliberating-territorial' || $contest->status == 'deliberating-national') || (($user->hasRole('coordinator') || $user->hasRole('president-territorial') || $user->hasRole('president-prize')) && ($contest->status == 'grading-territorial' || $contest->status == 'grading-national')))
                        <div class="tab-pane fade p-0" id="panel-aggregated-ratings" role="tabpanel">
                            @include('projects.rating.show')
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
@endsection