@extends('layout')

@section('content')
    <div class="card mt-3 mb-3">
        <div class="card-header">
            <h2>Sélection des projets lauréats</h2>
        </div>

        @if(($phase == 'deliberating-territorial' && count($territorial) > 0) || ($phase == 'deliberating-national' && count($national) > 0))
        <p>
            En tant que président du jury, vous devez sélectionner
            @if(count($territorial) > 0 || count($national) > 1)
            les projets lauréats pour chaque prix.
            @else
            le projet lauréat pour le prix.
            @endif
            <br>
            Pour ce faire, en bas de la page d'un projet, cliquez sur le bouton "Attribuer un prix" et sélectionnez le prix correspondant. Attention, si vous attribuez un même prix à plusieurs projets, seule l'attribution la plus récente sera prise en compte.
            <br>
            Cette page vous permet de vérifier les projets lauréats pour chaque prix.
        </p>
        @else
        <div class="alert alert-danger">
            La phase actuelle du concours ne vous permet pas de modifier les projets lauréats.
        </div>
        @endif

        @foreach($territorial as $region)
            <h3>Territoire {{ $region['region']->name }}</h3>
            <ul>
                @foreach($region['prizes'] as $prize)
                    <li>
                        <b>{{ $prize['prize']->name }}</b> :
                        @if($prize['awarded'])
                            <a href="{{ route('projects.show', ['project' => $prize['awarded']->project->id]) }}">{{ $prize['awarded']->project->name }}</a>
                        @elseif($phase != 'deliberating-territorial')
                            <i>Non attribuable pendant cette phase du concours</i>
                        @else
                            <i>Non attribué</i>
                        @endif
                    </li>
                @endforeach
            </ul>
        @endforeach

        @foreach($national as $prize)
            <h3>Prix national {{ $prize['prize']->name }}</h3>
            <p>
            @if($prize['awarded'])
                Attribué à <a href="{{ route('projects.show', ['project' => $prize['awarded']->project->id]) }}">{{ $prize['awarded']->project->name }}</a>
            @elseif($phase != 'deliberating-national')
                <i>Non attribuable pendant cette phase du concours</i>
            @else
                <i>Non attribué</i>
            @endif
            </p>
        @endforeach
    </div>

    <div class="mt-5 mb-3">
        <a class="btn btn-primary active-button" data-action="" target="_blank" href="/awards/export">Télécharger les lauréats au format CSV</a>
    </div>
@endsection
