@extends('layout')

@section('content')
    @if(count($rows))
        <div class="card mt-3 mb-3">
            <div class="card-header">Projects</div>

            <div class="table-responsive">
                <table class="table table-striped active-table">
                    <thead>
                        <tr>
                            <th>{!! SortableTable::th('id', 'Number') !!}</th>
                            <th>{!! SortableTable::th('name', 'Name') !!}</th>
                            <th>{!! SortableTable::th('school', 'School') !!}</th>
                            <th>{!! SortableTable::th('region', 'Region') !!}</th>
                            <th>{!! SortableTable::th('created_at', 'Submission date') !!}</th>
                            <th>{!! SortableTable::th('status', 'Status') !!}</th>
                        </tr>
                    </thead>
                    @foreach ($rows as $project)
                        <tr data-row-id="{{ $project->id }}" @if($project->status != 'draft') data-actions-disabled="edit" @endif>
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
        @include('common.paginator')
    @else
        @include('common.empty_list')
    @endif    

    <div class="mt-3 mb-3">
        @if(count($rows))
            <button class="btn btn-primary active-button" data-action="/projects/:id/edit" data-method="GET" data-action-name="edit">Edit selected project</button>
            <button class="btn btn-primary active-button" data-action="/projects/:id" data-method="GET">View selected project</button>
        @endif
        @if(Auth::user()->role == 'teacher')
            <button class="btn btn-primary active-button" data-action="/projects/create" data-method="GET">Deposit new project</button>
        @endif
    </div>    
@endsection