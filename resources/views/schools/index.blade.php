@extends('layout')

@section('content')
    @if(count($rows))
        <div class="card mt-3 mb-3">
            <div class="card-header">Schools</div>

            <div class="table-responsive">
                <table class="table">
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
                    @foreach ($rows as $school)
                        <tr>
                            <td>{{ $school->name }}</td>
                            <td>{{ $school->address }}</td>
                            <td>{{ $school->city }}</td>
                            <td>{{ $school->zip }}</td>
                            <td>{{ $school->country_name }}</td>
                            <td>{{ $school->region_name }}</td>
                            <td>{{ $school->uai }}</td>
                        </tr>
                    @endforeach
                </table>
            </div>
        </div>

        @include('common.paginator')
    @else
        @include('common.empty_list')
    @endif    
@endsection