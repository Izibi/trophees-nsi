@extends('layout')

@section('content')
    @if(count($rows))
        <div class="card mt-3 mb-3">
            <div class="card-header">Projects</div>

            <div class="table-responsive">
                <table class="table">
                    <thead>
                        <tr>
                            <th>Number</th>
                            <th>Name</th>
                            <th>School</th>
                            <th>Region</th>
                            <th>Submission date</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    @foreach ($rows as $project)
                        <tr>
                            <td>{{ $project->id }}</td>
                            <td>{{ $project->name }}</td>
                            <td>{{ $project->school->name }}</td>
                            <td>{{ $project->school->region->name }}</td>
                            <td>{{ $project->created_at }}</td>
                            <td>{{ $project->status }}</td>
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