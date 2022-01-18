@extends('layout')

@section('content')
    @if(count($rows))
        <div class="card mt-3 mb-3">
            <div class="card-header">
                <h2>Schools</h2>
            </div>

            <div class="table-responsive">
                <table class="table table-striped table-borderless active-table">
                    <thead>
                        <tr>
                            <th>{!! SortableTable::th('name', 'Name') !!}</th>
                            <th>{!! SortableTable::th('address', 'Address') !!}</th>
                            <th>{!! SortableTable::th('city', 'City') !!}</th>
                            <th>{!! SortableTable::th('zip', 'Zipcode') !!}</th>
                            <th>{!! SortableTable::th('country', 'Country') !!}</th>
                            <th>{!! SortableTable::th('region', 'Region') !!}</th>
                            <th>{!! SortableTable::th('uai', 'UAI') !!}</th>
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
            <button class="btn btn-primary active-button" data-action="/schools/:id/edit" data-method="GET">Edit selected school</button>
            <button class="btn btn-primary active-button" data-action="/schools/:id/hide" data-method="POST">Hide selected school</button>
        </div>

        @include('common.paginator')
    @else
        @include('common.empty_list')
    @endif
@endsection