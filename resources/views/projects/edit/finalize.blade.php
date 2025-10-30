@extends('layout')

@section('content')
    <div class="container mt-4">
        <h2>Soumission du projet</h2>
        <p class="alert alert-info">
            Vous êtes sur le point de soumettre ce projet finalisé. Veuillez vérifier attentivement toutes les informations ci-dessous avant de confirmer. Ces informations seront utilisées telles quelles si le projet est récompensé.
        </p>

        <div id="finalize-form">

        <div id="finalize-form">
        <div class="card mb-4">
            <div class="card-header">
                <h4>Informations générales</h4>
            </div>
            <div class="card-body">
                <div class="row mb-3">
                    <div class="col-md-3"><strong>Nom du projet :</strong></div>
                    <div class="col-md-9">{{ $project->name }}</div>
                </div>
                <div class="row mb-3">
                    <div class="col-md-3"><strong>Établissement :</strong></div>
                    <div class="col-md-9">{{ $project->school->name }}, {{ $project->school->zip }} {{ $project->school->city }}</div>
                </div>
                <div class="row mb-3">
                    <div class="col-md-3"><strong>Niveau scolaire :</strong></div>
                    <div class="col-md-9">{{ $project->grade->name }}</div>
                </div>
                <hr>
                <div class="mt-3">
                    <div class="form-check">
                        <input class="form-check-input finalize-checkbox" type="checkbox" id="confirm_general" required>
                        <label class="form-check-label" for="confirm_general">
                            Je confirme le nom du projet, l'adresse de l'établissement et le niveau scolaire.
                        </label>
                    </div>
                </div>
                <hr>
            </div>
        </div>

        <div class="card mb-4">
            <div class="card-header">
                <h4>Membres de l'équipe</h4>
            </div>
            <div class="card-body">
                <table class="table table-sm">
                    <thead>
                        <tr>
                            <th>Prénom</th>
                            <th>Nom</th>
                            <th>Genre</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($project->team_members as $member)
                        <tr>
                            <td>{{ $member->first_name }}</td>
                            <td>{{ $member->last_name }}</td>
                            <td>
                                @if($member->gender == 'male')
                                    Masculin
                                @elseif($member->gender == 'female')
                                    Féminin
                                @else
                                    Non renseigné
                                @endif
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
                <div class="mt-2">
                    Composition de l'équipe : 
                    {{ $project->team_girls }} fille(s), 
                    {{ $project->team_boys }} garçon(s)
                    @if($project->team_not_provided > 0)
                        , {{ $project->team_not_provided }} non renseigné(s)
                    @endif
                </div>
                <div class="mt-2">
                    Composition de la classe : 
                    {{ $project->class_girls ?? 0 }} fille(s), 
                    {{ $project->class_boys ?? 0 }} garçon(s)
                    @if(($project->class_not_provided ?? 0) > 0)
                        , {{ $project->class_not_provided }} non renseigné(s)
                    @endif
                </div>
                <hr>
                <div class="mt-3">
                    <div class="form-check">
                        <input class="form-check-input finalize-checkbox" type="checkbox" id="confirm_team" required>
                        <label class="form-check-label" for="confirm_team">
                            Je confirme l'orthographe des noms et prénoms des membres de l'équipe, et la composition de l'équipe et de la classe concernée.
                        </label>
                    </div>
                </div>
                <hr>
            </div>
        </div>

        <div class="card mb-4">
            <div class="card-header">
                <h4>Description du projet</h4>
            </div>
            <div class="card-body">
                <div class="mb-3">
                    <strong>Résumé :</strong>
                    <p>{{ $project->description }}</p>
                </div>
                <div class="mb-3">
                    <strong>Vidéo :</strong>
                    @if($project->video)
                        <a href="{{ $project->video }}" target="_blank">{{ $project->video }}</a>
                    @else
                        <span class="text-muted">Non renseignée</span>
                    @endif
                </div>
                <div class="mb-3">
                    <strong>Dossier technique :</strong>
                    @if($project->url)
                        <a href="{{ $project->url }}" target="_blank">{{ $project->url }}</a>
                    @else
                        <span class="text-danger">Non renseigné</span>
                    @endif
                </div>
                <div class="mb-3">
                    <strong>Nature du code et usage de l'IA :</strong>
                    <p>{{ $project->code_notes }}</p>
                </div>
                <div class="mb-3">
                    <strong>Remarques de l'enseignant :</strong>
                    <p>{{ $project->teacher_notes }}</p>
                </div>
                <hr>
                <div class="mt-3">
                    <div class="form-check">
                        <input class="form-check-input finalize-checkbox" type="checkbox" id="confirm_description" required>
                        <label class="form-check-label" for="confirm_description">
                            Je confirme les informations du projet, et j'ai vérifié que les liens fournis pour la vidéo et le dossier technique fonctionnent et sont accessibles publiquement.
                        </label>
                    </div>
                </div>
                <hr>
            </div>
        </div>

        <div class="alert alert-warning">
            <b>Attention :</b> Une fois le projet soumis, vous ne pourrez plus le modifier.
        </div>

        {!! Form::open()->route('projects.confirm_finalize', ['project' => $project]) !!}
            {!! Form::hidden('refer_page', $refer_page) !!}
            
            <div class="mt-4 mb-5">
                <a href="{{ route('projects.edit', ['project' => $project, 'refer_page' => $refer_page]) }}" class="btn btn-primary">
                    Retour à l'édition
                </a>
                <button type="submit" class="btn btn-secondary" id="btn-submit-finalize" disabled>
                    Soumettre le projet finalisé
                </button>
            </div>
        {!! Form::close() !!}
        </div>
    </div>

    <script>
        $(document).ready(function() {
            function updateSubmitButton() {
                var allChecked = true;
                $('.finalize-checkbox').each(function() {
                    if (!$(this).is(':checked')) {
                        allChecked = false;
                        return false;
                    }
                });
                
                $('#btn-submit-finalize').prop('disabled', !allChecked);
            }
            
            $('.finalize-checkbox').on('change', updateSubmitButton);
            
            // Initial check
            updateSubmitButton();
        });
    </script>
@endsection
