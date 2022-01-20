@extends('layout')

@section('content')
    <div class="card mt-3 mb-3">
        <div class="card-header">
            <h2>Projets</h2>
            @include('projects.index.filter')
        </div>
        @if(count($rows))
            <div class="table-responsive">
                <table class="table table-striped table-borderless active-table">
                    <thead>
                        <tr>
                            <th>{!! SortableTable::th('id', 'ID') !!}</th>
                            <th>{!! SortableTable::th('name', 'Nom') !!}</th>
                            <th>{!! SortableTable::th('school_name', 'Établissement') !!}</th>
                            <th>{!! SortableTable::th('region_name', 'Territoire') !!}</th>
                            <th>{!! SortableTable::th('user_name', 'Enseignant') !!}</th>
                            <th>{!! SortableTable::th('created_at', 'Date de soumission') !!}</th>
                            <th>{!! SortableTable::th('status', 'Statut') !!}</th>
                        </tr>
                    </thead>
                    @foreach ($rows as $project)
                        <tr data-row-id="{{ $project->id }}" @if($project->status != 'draft') data-actions-disabled="edit" @endif data-redirect-url="{{ $project->view_url }}">
                            <td>{{ $project->id }}</td>
                            <td>{{ $project->name }}</td>
                            <td>{{ $project->school_name }}</td>
                            <td>{{ $project->region_name }}</td>
                            <td>{{ $project->user_name }}</td>
                            <td>{{ $project->created_at }}</td>
                            <td>@lang('project_status.'.$project->status)</td>
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
            <button class="btn btn-primary active-button" data-action="/projects/:id" data-method="REDIRECT">Afficher le projet sélectionné</button>
        @endif
    </div>
@endsection