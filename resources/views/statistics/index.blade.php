@extends('layout')

@section('content')
    <div class="card mt-3 mb-3">
        <div class="card-header">
            <h2>Statistiques</h2>
        </div>

        @if(count($data))
            <div class="table-responsive">
                <table class="table table-bordered text-center">
                    <thead>
                        <tr>
                            <th colspan="2">Région</th>
                            <th colspan="2">Académie</th>
                            <th colspan="4">Projets</th>
                        </tr>
                        <tr>
                            <th rowspan="2" class="align-top">Nom</th>
                            <th rowspan="2" class="align-top">Enseignants</th>
                            <th rowspan="2" class="align-top">Nom</th>
                            <th rowspan="2" class="align-top">Enseignants</th>
                            <th rowspan="2" class="align-top">Brouillons</th>
                            <th colspan="3">Finalisés</th>
                        </tr>
                        <tr>
                            <th>Première</th>
                            <th>Terminale</th>
                            <th>Total</th>
                        </tr>
                    </thead>
                    @foreach($data as $region)
                        @foreach($region['academies'] as $academy)
                            <tr {!! $academy['accent_row'] ? 'class="accent"' : '' !!}>
                                @if($loop->first)
                                    <td rowspan="{{ count($region['academies']) }}">{{ $region['name'] }}</td>
                                    <td rowspan="{{ count($region['academies']) }}">{{ $region['teachers'] }}</td>
                                @endif
                                <td>{{ $academy['name'] }}</td>
                                <td>{{ $academy['teachers'] }}</td>
                                <td>{{ $academy['projects_draft'] }}</td>
                                <td>{{ $academy['projects_finalized_premiere'] }}</td>
                                <td>{{ $academy['projects_finalized_terminale'] }}</td>
                                <td>{{ $academy['projects_finalized'] }}</td>
                            </tr>
                        @endforeach
                    @endforeach
                </table>
            </div>
        @else
            @include('common.empty_list')
        @endif
    </div>
@endsection
