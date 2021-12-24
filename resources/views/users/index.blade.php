@extends('layout')

@section('content')
    @if(count($rows))
        <div class="card mt-3 mb-3">
            <div class="card-header">Users</div>

            <div class="table-responsive">
                <table class="table">
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
                        <tr>
                            <td>{{ $user->first_name }}</td>
                            <td>{{ $user->last_name }}</td>

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