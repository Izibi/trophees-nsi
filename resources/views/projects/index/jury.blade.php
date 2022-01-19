@extends('layout')

@section('content')
    <div class="card mt-3 mb-3">
        <div class="card-header">
            <h2>Projects</h2>
            @include('projects.index.filter')
        </div>
        @if(count($rows))
            <div class="table-responsive">
                <table class="table table-striped table-borderless active-table">
                    <thead>
                        <tr>
                            <th>{!! SortableTable::th('id', 'ID') !!}</th>
                            <th>{!! SortableTable::th('name', 'Name') !!}</th>
                            <th>{!! SortableTable::th('created_at', 'Submission date') !!}</th>
                        </tr>
                    </thead>
                    @foreach ($rows as $project)
                        <tr data-row-id="{{ $project->id }}" @if($project->status != 'draft') data-actions-disabled="edit" @endif data-redirect-url="{{ $project->view_url }}">
                            <td>{{ $project->id }}</td>
                            <td>{{ $project->name }}</td>
                            <td>{{ $project->created_at }}</td>
                        </tr>
                    @endforeach
                </table>
            </div>
        @else
            @include('common.empty_list')
        @endif
    </div>

    @include('common.paginator')


    <div class="mt-5 mb-3">
        @if(count($rows))
            @if($contest_status == 'grading' || $contest_status == 'deliberating' || $contest_status == 'closed')
                <button class="btn btn-primary active-button" data-action="/projects/:id" data-method="REDIRECT">View selected project</button>
            @endif
        @endif
    </div>
@endsection