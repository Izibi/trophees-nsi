@extends('layout')

@section('content')
    @if(count($rows))
        <div class="card mt-3 mb-3">
            <div class="card-header">Users</div>

            <div class="table-responsive">
                <table class="table table-striped active-table">
                    <thead>
                        <tr>
                            <th>Last name</th>
                            <th>First name</th>
                            <th>Professional email</th>
                            <th>Secondary email</th>
                            <th>Teacher status</th>
                            <th>Role</th>
                            <th>Region</th>
                            <th>Registration date</th>
                            <th>Last login date</th>
                        </tr>
                    </thead>
                    @foreach ($rows as $user)
                        <tr data-row-id="{{ $user->id }}">
                            <td>{{ $user->first_name }}</td>
                            <td>{{ $user->last_name }}</td>
                            <td>{{ $user->email }}</td>
                            <td>{{ $user->secondary_email }}</td>
                            <td>{{ $user->validated ? 'validated' : 'not validated' }}</td>
                            <td>{{ $user->role }}</td>
                            <td>{{ $user->region_name }}</td>
                            <td>{{ $user->created_at }}</td>
                            <td>{{ $user->last_login_at }}</td>
                        </tr>
                    @endforeach
                </table>
            </div>
        </div>

        <div class="mt-3 mb-3">
            <button class="btn btn-primary active-button" action="/users/:id/edit" method="GET">Edit selected user</button>        
        </div>

        @include('common.paginator')
    @else
        @include('common.empty_list')
    @endif    
@endsection