@extends('layout')

@section('content')
    @if(count($rows))
        <div class="card mt-3 mb-3">
            <div class="card-header">
                <strong>Contests</strong>
            </div>

            <div class="table-responsive">
                <table class="table table-striped active-table">
                    <thead>
                        <tr>
                            <th>{!! SortableTable::th('name', 'Name') !!}</th>
                            <th>{!! SortableTable::th('year', 'Year') !!}</th>
                            <th>{!! SortableTable::th('status', 'Status') !!}</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($rows as $contest)
                            <tr data-row-id="{{ $contest->id }}">
                                <td>{{ $contest->name }}</td>
                                <td>{{ $contest->year }}</td>
                                <td>{{ $contest->status }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>

        <div class="mt-3 mb-3">
            <button class="btn btn-primary active-button" data-action="/contests/:id/edit" data-method="GET">Edit selected contest</button>
            <button class="btn btn-primary active-button" data-action="/contests/create" data-method="GET">Add new contest</button>
        </div>

        @include('common.paginator')
    @else
        @include('common.empty_list')
    @endif
@endsection