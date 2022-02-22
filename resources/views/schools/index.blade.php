@extends('layout')

@section('content')

    <div class="card mt-3 mb-3">
        <div class="card-header">
            <h2>Schools</h2>
            @include('schools.filter')
        </div>

        @if(count($rows))
            <div class="table-responsive">
                <table class="table table-striped table-borderless active-table">
                    <thead>
                        <tr>
                            <th>{!! SortableTable::th('name', 'Nom') !!}</th>
                            <th>{!! SortableTable::th('address', 'Adresse') !!}</th>
                            <th>{!! SortableTable::th('city', 'Ville') !!}</th>
                            <th>{!! SortableTable::th('zip', 'Code postal') !!}</th>
                            <th>{!! SortableTable::th('country', 'Pays') !!}</th>
                            <th>{!! SortableTable::th('region', 'Territoire') !!}</th>
                            <th>{!! SortableTable::th('uai', 'UAI') !!}</th>
                            <th>{!! SortableTable::th('hidden', 'Hidden') !!}</th>
                            <th>{!! SortableTable::th('projects_amount', 'Projects amount') !!}</th>

                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($rows as $school)
                            <tr data-row-id="{{ $school->id }}">
                                <td>{{ $school->name }}</td>
                                <td>{{ $school->address }}</td>
                                <td>{{ $school->city }}</td>
                                <td>{{ $school->zip }}</td>
                                <td>{{ $school->country_name }}</td>
                                <td>{{ $school->region_name }}</td>
                                <td>{{ $school->uai }}</td>
                                <td>{{ $school->hidden ? 'Oui' : 'Non' }}</td>
                                <td>{{ $school->projects_amount }}</td>

                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @else
            @include('common.empty_list')
        @endif
    </div>

    @include('common.paginator')

    @if(count($rows))
        <div class="mt-3 mb-3">
            <button class="btn btn-primary active-button" data-action="/schools/:id/edit" data-method="GET">Modifier l'établissement sélectionné</button>
            <button class="btn btn-primary active-button" data-action="/schools/:id/hide" data-method="POST">Cacher l'établissement sélectionné</button>
        </div>
    @endif
@endsection