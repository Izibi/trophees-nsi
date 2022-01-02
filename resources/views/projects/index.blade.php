@extends('layout')

@section('content')
    @if(count($rows))
        <div class="card mt-3 mb-3">
            <div class="card-header">Projects</div>

            <div class="table-responsive">
                <table class="table table-striped active-table">
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
                        <tr data-row-id="{{ $project->id }}">
                            <td>{{ $project->id }}</td>
                            <td>{{ $project->name }}</td>
                            <td>{{ $project->school_name }}</td>
                            <td>{{ $project->region_name }}</td>
                            <td>{{ $project->created_at }}</td>
                            <td>{{ $project->status }}</td>
                        </tr>
                    @endforeach
                </table>
            </div>
        </div>

        <div class="mt-3 mb-3">
            <button class="btn btn-primary active-button" action="/projects/:id/edit" method="GET">Edit selected project</button>
            <button class="btn btn-primary active-button" action="/projects/:id" method="GET">View selected project</button>
            <button class="btn btn-primary active-button" action="/projects/create" method="GET">Deposit new project</button>
        </div>

        @include('common.paginator')
    @else
        @include('common.empty_list')
    @endif    
@endsection