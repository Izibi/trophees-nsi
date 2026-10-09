@extends('layout')

@section('content')
    <div class="container mt-4">
        <h2>Soumission du projet</h2>
        <p class="alert alert-info">
            Vous êtes sur le point de soumettre ce projet. Veuillez vérifier attentivement toutes les informations ci-dessous avant de confirmer. Si le dossier est validé, il sera remis en l'état aux membres du jury. Les informations transmises sur le projet seront également utilisées pour toutes les communications officielles. Le commentaire de l'enseignant est accessible uniquement pour les membres du jury.
        </p>

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
                <div class="row mb-3">
                    <div class="col-md-3"><strong>Répartition filles/garçons dans la classe :</strong></div>
                    <div class="col-md-9">
                        {{ $project->class_girls ?? 0 }} fille(s),
                        {{ $project->class_boys ?? 0 }} garçon(s)
                        @if(($project->class_not_provided ?? 0) > 0)
                            , {{ $project->class_not_provided }} non renseigné(s)
                        @endif
                    </div>
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
                            <th>Autorisation signée</th>
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
                            <td>
                                @if($member->parental_permissions_file)
                                    <a href="{{ Storage::disk('uploads')->url($member->parental_permissions_file) }}" target="_blank">PDF</a>
                                @else
                                    <span class="text-danger">Non fournie</span>
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
                <div class="mt-4">
                    <strong>Organisation du travail et répartition des tâches :</strong>
                    <p style="white-space: pre-wrap;">{{ $project->work_organisation }}</p>
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
                <h4>Présentation du projet</h4>
            </div>
            <div class="card-body">
                <div class="mb-3">
                    <strong>Résumé du projet :</strong>
                    <p style="white-space: pre-wrap;">{{ $project->description }}</p>
                </div>
                <div class="mb-3">
                    <strong>Description du projet :</strong>
                    <p style="white-space: pre-wrap;">{{ $project->project_description }}</p>
                </div>
                <div class="mb-3">
                    <strong>Étapes de réalisation du projet :</strong>
                    <p style="white-space: pre-wrap;">{{ $project->project_steps }}</p>
                </div>
                <div class="mb-3">
                    <strong>Ouverture :</strong>
                    <p style="white-space: pre-wrap;">{{ $project->project_outlook }}</p>
                </div>
                <div class="mb-3">
                    <strong>Image du projet :</strong>
                    @if($project->image_file)
                        <div class="mt-2">
                            <a href="{{ Storage::disk('uploads')->url($project->image_file) }}" target="_blank">
                                <img src="{{ Storage::disk('uploads')->url($project->image_file) }}"
                                    alt="Image du projet"
                                    class="img-fluid"
                                    style="max-width: 200px;">
                            </a>
                        </div>
                    @else
                        <span class="text-muted">Non renseignée</span>
                    @endif
                </div>
                <div class="mb-3">
                    <strong>Démonstration vidéo :</strong>
                    @if($project->video)
                        <a href="{{ $project->video }}" target="_blank">{{ $project->video }}</a>
                    @else
                        <span class="text-muted">Non renseignée</span>
                    @endif
                </div>
                <hr>
                <div class="mt-3">
                    <div class="form-check">
                        <input class="form-check-input finalize-checkbox" type="checkbox" id="confirm_description" required>
                        <label class="form-check-label" for="confirm_description">
                            Je confirme les informations de présentation du projet, et j'ai vérifié que le lien fourni pour la vidéo fonctionne et est accessible publiquement.
                        </label>
                    </div>
                </div>
                <hr>
            </div>
        </div>

        <div class="card mb-4">
            <div class="card-header">
                <h4>Informations techniques</h4>
            </div>
            <div class="card-body">
                <div class="mb-3">
                    <strong>Dossier technique :</strong>
                    @if($project->url)
                        <a href="{{ $project->url }}" target="_blank">{{ $project->url }}</a>
                    @else
                        <span class="text-danger">Non renseigné</span>
                    @endif
                </div>
                <div class="mb-3">
                    <strong>Nature du code :</strong>
                    <p style="white-space: pre-wrap;">{{ $project->code_notes }}</p>
                </div>
                <div class="mb-3">
                    <strong>Validation de l'opérationnalité du projet / de son fonctionnement :</strong>
                    <p style="white-space: pre-wrap;">{{ $project->operationality_notes }}</p>
                </div>
                <div class="mb-3">
                    <strong>Part d'utilisation de l'IA :</strong>
                    <p>{{ $project->ai_usage_percent }}{{ is_numeric($project->ai_usage_percent) ? ' %' : '' }}</p>
                </div>
                <div class="mb-3">
                    <strong>Usage de l'IA :</strong>
                    <p style="white-space: pre-wrap;">{{ $project->ai_usage_notes }}</p>
                </div>
                <hr>
                <div class="mt-3">
                    <div class="form-check">
                        <input class="form-check-input finalize-checkbox" type="checkbox" id="confirm_technical" required>
                        <label class="form-check-label" for="confirm_technical">
                            Je confirme les informations techniques du projet, et j'ai vérifié que le lien du dossier technique fonctionne et est accessible publiquement.
                        </label>
                    </div>
                </div>
                <hr>
            </div>
        </div>

        <div class="card mb-4">
            <div class="card-header">
                <h4>Vérification et validation</h4>
            </div>
            <div class="card-body">
                <div class="mb-3">
                    <strong>Informations complémentaires (commentaire de l'enseignant sur le projet et l'équipe) :</strong>
                    <p style="white-space: pre-wrap;">{{ $project->teacher_notes }}</p>
                </div>
                <div class="row mb-2">
                    <div class="col-md-8"><strong>Test effectué par l'enseignant :</strong></div>
                    <div class="col-md-4">
                        @if($project->tested_by_teacher)
                            <span class="badge badge-success">Certifié</span>
                        @else
                            <span class="badge badge-danger">Non certifié</span>
                        @endif
                    </div>
                </div>
                <div class="row mb-2">
                    <div class="col-md-8"><strong>Autorisations signées pour l'utilisation de l'image, de la voix et des œuvres :</strong></div>
                    <div class="col-md-4">
                        @if($project->video_authorization)
                            <span class="badge badge-success">Certifié</span>
                        @else
                            <span class="badge badge-danger">Non certifié</span>
                        @endif
                    </div>
                </div>
                <div class="row mb-2">
                    <div class="col-md-8"><strong>Règlement du concours lu et accepté :</strong></div>
                    <div class="col-md-4">
                        @if($project->reglament_accepted)
                            <span class="badge badge-success">Certifié</span>
                        @else
                            <span class="badge badge-danger">Non certifié</span>
                        @endif
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
