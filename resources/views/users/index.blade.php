@extends('layout')

@section('content')
    @if(count($rows))
        <div class="card mt-3 mb-3">
            <div class="card-header">
                <h2>Users</h2>
            </div>

            <div class="table-responsive">
                <table class="table table-striped table-borderless active-table">
                    <thead>
                        <tr>
                            <th>{!! SortableTable::th('name', 'Name') !!}</th>
                            <th>{!! SortableTable::th('email', 'Professional email') !!}</th>
                            <th>{!! SortableTable::th('secondary_email', 'Secondary email') !!}</th>
                            <th>{!! SortableTable::th('validated', 'Teacher status') !!}</th>
                            <th>{!! SortableTable::th('role', 'Role') !!}</th>
                            <th>{!! SortableTable::th('country', 'Country') !!}</th>
                            <th>{!! SortableTable::th('region', 'Region') !!}</th>
                            <th>{!! SortableTable::th('created_at', 'Registration date') !!}</th>
                            <th>{!! SortableTable::th('last_login_at', 'Last login date') !!}</th>
                        </tr>
                    </thead>
                    @foreach ($rows as $user)
                        <tr data-row-id="{{ $user->id }}">
                            <td>{{ $user->name }}</td>
                            <td>{{ $user->email }}</td>
                            <td>{{ $user->secondary_email }}</td>
                            <td>{{ $user->validated ? 'validated' : 'not validated' }}</td>
                            <td>{{ $user->role }}</td>
                            <td>{{ $user->country_name }}</td>
                            <td>{{ $user->region_name }}</td>
                            <td>{{ $user->created_at }}</td>
                            <td>{{ $user->last_login_at }}</td>
                        </tr>
                    @endforeach
                </table>
            </div>
        </div>

        <div class="mt-3 mb-3">
            <button class="btn btn-primary active-button" data-action="/users/:id/edit" data-method="GET">Edit selected user</button>
        </div>

        @include('common.paginator')
    @else
        @include('common.empty_list')
    @endif
@endsection