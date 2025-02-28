@extends('layout')

@section('content')
    @if(count($rows))
        <div class="card mt-3 mb-3">
            <div class="card-header">
                <h2>Concours</h2>
            </div>

            <div class="table-responsive">
                <table class="table table-striped table-borderless active-table">
                    <thead>
                        <tr>
                            <th>{!! SortableTable::th('name', 'Nom') !!}</th>
                            <th>{!! SortableTable::th('year', 'Année') !!}</th>
                            <th>{!! SortableTable::th('status', 'Statut') !!}</th>
                            <th>{!! SortableTable::th('active', 'Actif') !!}</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($rows as $contest)
                            <tr data-row-id="{{ $contest->id }}" @if($contest->active) data-actions-disabled="activate" @endif>
                                <td>{{ $contest->name }}</td>
                                <td>{{ $contest->year }}</td>
                                <td>@lang('contest_status.'.$contest->status)</td>
                                <td>{!! $contest->active ? '<span class="badge badge-success">Oui</span>' : '' !!}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>

        <div class="mt-3 mb-3">
            <button class="btn btn-primary active-button" data-action="/contests/:id/edit" data-method="GET">Modifier le concours sélectionné</button>
            <button class="btn btn-primary active-button" data-action="/contests/create" data-method="GET">Ajouter un nouveau concours</button>
            <button class="btn btn-secondary active-button"
                data-action="/contests/:id/activate" data-method="POST"
                data-action-name="activate"
                data-confirmation="This action will change active contest. Continue?">Activer ce concours</button>
        </div>

        @include('common.paginator')
    @else
        @include('common.empty_list')
    @endif
@endsection