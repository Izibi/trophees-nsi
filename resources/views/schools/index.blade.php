@extends('layout')

@section('content')
    @if(count($rows))
        <div class="card mt-3 mb-3">
            <div class="card-header">Schools</div>

            <div class="table-responsive">
                <table class="table table-striped active-table">
                    <thead>
                        <tr>
                            <th>Name</th>
                            <th>Address</th>
                            <th>City</th>
                            <th>Zipcode</th>
                            <th>Country</th>
                            <th>Region</th>
                            <th>UAI</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($rows as $school)
                            <tr data-row-id="{{ $school->id }}">
                                <td>{{ $school->name }}</td>
                                <td>{{ $school->address }}</td>
                                <td>{{ $school->city }}</td>
                                <td>{{ $school->zip }}</td>
                                <td>{{ $school->country_name }}</td>
                                <td>{{ $school->region_name }}</td>
                                <td>{{ $school->uai }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>

        <div class="mt-3 mb-3">
            <button class="btn btn-primary active-button" action="/schools/:id" method="GET">Edit selected school</button>
            <button class="btn btn-primary active-button" action="/schools/:id/hide" method="POST">Hide selected school</button>
        </div>

        @include('common.paginator')
    @else
        @include('common.empty_list')
    @endif    
@endsection