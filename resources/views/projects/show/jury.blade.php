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
                                My ratings {{ $rating && !$rating->published ? '(draft)' : '' }}
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" data-toggle="tab" href="#panel-aggregated-ratings" role="tab">Aggregated ratings</a>
                        </li>
                    </ul>
                </div>
                <div class="card-body tab-content p-0">
                    <div class="tab-pane fade show active p-3" id="panel-my-ratings" role="tabpanel">
                        @include('projects.rating.edit')
                    </div>
                    <div class="tab-pane fade p-1" id="panel-aggregated-ratings" role="tabpanel">
                        @include('projects.rating.show')
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection