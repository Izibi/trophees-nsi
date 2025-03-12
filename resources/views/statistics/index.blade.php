@extends('layout')

@section('content')
    <div class="card mt-3 mb-3">
        <div class="card-header">
            <h2>Statistiques</h2>
        </div>

        @if(count($regional_data))
        <h3 style="margin: 16px 0px;">
            Statistiques de
            @if(count($regional_data) == 1)
                votre territoire
            @else
                vos territoires
            @endif
        </h3>
        <div class="table-responsive">
                <table class="table table-bordered text-center">
                    <thead>
                        <tr>
                            <th colspan="2">Territoire</th>
                            <th colspan="13">Projets</th>
                        </tr>
                        <tr>
                            <th rowspan="2" class="align-top">Nom</th>
                            <th rowspan="2" class="align-top">Enseignants</th>
                            <th colspan="3" class="align-top">Brouillons</th>
                            <th colspan="3">Finalisés</th>
                            <th colspan="3">Validés</th>
                            <th colspan="3">Incomplets</th>
                        </tr>
                        <tr style="font-size: 0.7em;">
                            <th>Première</th>
                            <th>Terminale</th>
                            <th>Total</th>
                            <th>Première</th>
                            <th>Terminale</th>
                            <th>Total</th>
                            <th>Première</th>
                            <th>Terminale</th>
                            <th>Total</th>
                            <th>Première</th>
                            <th>Terminale</th>
                            <th>Total</th>
                        </tr>
                    </thead>
                    @foreach($regional_data as $region)
                        @foreach($region['academies'] as $academy)
                            <tr {!! $academy['accent_row'] ? 'class="accent"' : '' !!}>
                                <td>{{ $academy['name'] }}</td>
                                <td>{{ $academy['teachers'] }}</td>
                                <td>{{ $academy['projects_draft_premiere'] }}</td>
                                <td>{{ $academy['projects_draft_terminale'] }}</td>
                                <td>{{ $academy['projects_draft'] }}</td>
                                <td>{{ $academy['projects_finalized_premiere'] }}</td>
                                <td>{{ $academy['projects_finalized_terminale'] }}</td>
                                <td>{{ $academy['projects_finalized'] }}</td>
                                <td>{{ $academy['projects_validated_premiere'] }}</td>
                                <td>{{ $academy['projects_validated_terminale'] }}</td>
                                <td>{{ $academy['projects_validated'] }}</td>
                                <td>{{ $academy['projects_incomplete_premiere'] }}</td>
                                <td>{{ $academy['projects_incomplete_terminale'] }}</td>
                                <td>{{ $academy['projects_incomplete'] }}</td>
                            </tr>
                        @endforeach
                    @endforeach
                </table>
            </div>
            <div class="mt-1">
                <a class="btn btn-primary active-button" data-action="" target="_blank" href="/statistics/export_detail">Télécharger les statistiques de votre territoire au format CSV</a>
            </div>
        @endif

        <h3 style="margin: 16px 0px;">Statistiques nationales</h3>
        @if($isAdmin)
        <p>Statistiques publiques affichées à tous ceux qui ont accès aux statistiques.</p>
        @endif
        @if(count($data))
            <div class="table-responsive">
                <table class="table table-bordered text-center">
                    <thead>
                        <tr>
                            <th colspan="2">Territoire</th>
                            <th colspan="6">Projets</th>
                        </tr>
                        <tr>
                            <th rowspan="2" class="align-top">Nom</th>
                            <th rowspan="2" class="align-top">Enseignants</th>
                            <th colspan="3" class="align-top">Brouillons</th>
                            <th colspan="3">Finalisés</th>
                        </tr>
                        <tr style="font-size: 0.9em;">
                            <th>Première</th>
                            <th>Terminale</th>
                            <th>Total</th>
                            <th>Première</th>
                            <th>Terminale</th>
                            <th>Total</th>
                        </tr>
                    </thead>
                    @foreach($data as $region)
                        @foreach($region['academies'] as $academy)
                            <tr {!! $academy['accent_row'] ? 'class="accent"' : '' !!}>
                                <td>{{ $academy['name'] }}</td>
                                <td>{{ $academy['teachers'] }}</td>
                                <td>{{ $academy['projects_draft_premiere'] }}</td>
                                <td>{{ $academy['projects_draft_terminale'] }}</td>
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
        <div class="mt-1">
            <a class="btn btn-primary active-button" data-action="" target="_blank" href="/statistics/export">Télécharger les statistiques nationales au format CSV</a>
        </div>
    </div>
@endsection
