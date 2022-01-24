@extends('layout')

@section('content')
    <div class="row">
        <div class="col-7">
            @include('projects.details')
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
                        @if($contest->status == 'deliberating')
                            <li class="nav-item">
                                <a class="nav-link" data-toggle="tab" href="#panel-aggregated-ratings" role="tab">Notes aggrégées</a>
                            </li>
                        @endif
                    </ul>
                </div>
                <div class="card-body tab-content p-0">
                    <div class="tab-pane fade show active p-3" id="panel-my-ratings" role="tabpanel">
                        @include('projects.rating.edit')
                    </div>
                    @if($contest->status == 'deliberating')
                        <div class="tab-pane fade p-0" id="panel-aggregated-ratings" role="tabpanel">
                            @include('projects.rating.show')
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
@endsection