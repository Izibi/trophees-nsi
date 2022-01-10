@extends('layout')

@section('content')

    <div class="card mt-3 mb-3">
        <div class="card-header">
            <strong>Projects</strong>
            @include('projects.filter')
        </div>
        @if(count($rows))
            <div class="table-responsive">
                <table class="table table-striped active-table">
                    <thead>
                        <tr>
                            <th>{!! SortableTable::th('id', 'ID') !!}</th>
                            <th>{!! SortableTable::th('name', 'Name') !!}</th>
                            @if(Auth::user()->role == 'teacher' || Auth::user()->role == 'admin')
                                <th>{!! SortableTable::th('school_name', 'School') !!}</th>
                            @endif
                            @if(Auth::user()->role == 'admin')
                                <th>{!! SortableTable::th('region_name', 'Region') !!}</th>
                                <th>{!! SortableTable::th('user_name', 'Teacher') !!}</th>
                            @endif
                            <th>{!! SortableTable::th('created_at', 'Submission date') !!}</th>
                            <th>{!! SortableTable::th('status', 'Status') !!}</th>
                        </tr>
                    </thead>
                    @foreach ($rows as $project)
                        <tr data-row-id="{{ $project->id }}" @if($project->status != 'draft') data-actions-disabled="edit" @endif>
                            <td>{{ $project->id }}</td>
                            <td>{{ $project->name }}</td>
                            @if(Auth::user()->role == 'teacher' || Auth::user()->role == 'admin')
                                <td>{{ $project->school_name }}</td>
                            @endif
                            @if(Auth::user()->role == 'admin')                            
                                <td>{{ $project->region_name }}</td>
                                <td>{{ $project->user_name }}</td>
                            @endif
                            <td>{{ $project->created_at }}</td>
                            <td>{{ $project->status }}</td>
                        </tr>
                    @endforeach
                </table>
            </div>
        @else
            @include('common.empty_list')
        @endif    
    </div>
    
    @include('common.paginator')


    <div class="mt-3 mb-3">
        @if(count($rows))
            @if(Auth::user()->role == 'teacher')
                <button class="btn btn-primary active-button" data-action="/projects/:id/edit" data-method="GET" data-action-name="edit">Edit selected project</button>
            @endif
            <button class="btn btn-primary active-button" data-action="/projects/:id" data-method="GET">View selected project</button>
        @endif
        @if(Auth::user()->role == 'teacher')
            <button class="btn btn-primary active-button" data-action="/projects/create" data-method="GET">Deposit new project</button>
        @endif
    </div>    
@endsection