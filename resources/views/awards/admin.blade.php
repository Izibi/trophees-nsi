@extends('layout')

@section('content')
    <div class="card mt-3 mb-3">
        <div class="card-header">
            <h2>Projets lauréats</h2>
        </div>

        <div class="table-responsive">
            <table class="table table-bordered text-center">
                <thead>
                    <tr>
                        <th></th>
                        @foreach($prizes as $prize)
                            <th>{{ $prize->name }}</th>
                        @endforeach
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td><b>Nationaux</b></td>
                        @foreach($national as $prize)
                            <td>
                                @if($prize['awarded'])
                                    <a href="{{ route('projects.show', ['project' => $prize['awarded']->project->id]) }}">{{ $prize['awarded']->project->name }}</a>
                                @else
                                    <i>Non attribué</i>
                                @endif
                            </td>
                        @endforeach
                    </tr>
                    <tr>
                        <td><b>Territoires</b></td>
                        <td colspan="{{ count($prizes) }}"></td>
                    </tr>
                @foreach($territorial as $region)
                    <tr>
                        <td>{{ $region['region']->name }}</td>
                        @foreach($region['prizes'] as $prize)
                            <td>
                                @if($prize['awarded'])
                                    <a href="{{ route('projects.show', ['project' => $prize['awarded']->project->id]) }}">{{ $prize['awarded']->project->name }}</a>
                                @else
                                    <i>Non attribué</i>
                                @endif
                            </td>
                        @endforeach
                    </tr>
                @endforeach
                </tbody>
            </table>
        </div>
    </div>

    <div class="mt-5 mb-3">
        <a class="btn btn-primary active-button" data-action="" target="_blank" href="/awards/export">Télécharger les lauréats au format CSV</a>
    </div>
@endsection
