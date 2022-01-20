@extends('layout')

@section('content')
    @if(count($rows))
        <div class="card mt-3 mb-3">
            <div class="card-header">
                <h2>Utilisateurs</h2>
            </div>

            <div class="table-responsive">
                <table class="table table-striped table-borderless active-table">
                    <thead>
                        <tr>
                            <th>{!! SortableTable::th('name', 'Nom') !!}</th>
                            <th>{!! SortableTable::th('email', 'Email professionel') !!}</th>
                            <th>{!! SortableTable::th('secondary_email', 'Email secondaire') !!}</th>
                            <th>{!! SortableTable::th('validated', 'Statut de validation') !!}</th>
                            <th>{!! SortableTable::th('role', 'Rôle') !!}</th>
                            <th>{!! SortableTable::th('country', 'Pays') !!}</th>
                            <th>{!! SortableTable::th('region', 'Territoire') !!}</th>
                            <th>{!! SortableTable::th('created_at', 'Date d'enregistrement') !!}</th>
                            <th>{!! SortableTable::th('last_login_at', 'Date de dernière connexion') !!}</th>
                        </tr>
                    </thead>
                    @foreach ($rows as $user)
                        <tr data-row-id="{{ $user->id }}">
                            <td>{{ $user->name }}</td>
                            <td>{{ $user->email }}</td>
                            <td>{{ $user->secondary_email }}</td>
                            <td>{{ $user->validated ? 'validé' : 'non validé' }}</td>
                            <td>{{ $user->role }}</td>
                            <td>{{ $user->country_name }}</td>
                            <td>{{ $user->region_name }}</td>
                            <td>{{ $user->created_at }}</td>
                            <td>{{ $user->last_login_at }}</td>
                        </tr>
                    @endforeach
                </table>
            </div>
        </div>

        <div class="mt-3 mb-3">
            <button class="btn btn-primary active-button" data-action="/users/:id/edit" data-method="GET">Modifier l'utilisateur sélectionné</button>
            <a class="btn btn-primary" href="export/users" target="_blank">Télécharger au format CSV</a>
        </div>

        @include('common.paginator')
    @else
        @include('common.empty_list')
    @endif
@endsection