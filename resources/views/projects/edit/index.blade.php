@extends('layout')

@section('content')
    @if($editor_type == 'student' && $project && $project->status == 'completed')
        <div class="alert alert-success">Votre projet est terminé. Il va être vérifié par votre professeur. Vous pouvez encore le modifier jusqu'à la validation définitive par votre professeur.</div>
    @endif

    {{-- Lien vers l'exemple de projet masqué temporairement
    <p>@if($editor_type == 'teacher')Pour vous aider à compléter ce formulaire, n'hésitez pas à consulter <a href="/projects/exemple" target="_blank">cet exemple de projet complété</a>.@endif</p>
    --}}

    @if($editor_type == 'teacher' && $project)
        <div class="card mt-3 mb-4" id="student-code-box">
            <div class="card-header">Code de connexion des élèves</div>
            <div class="card-body">
                <p class="mb-2">Transmettez ce code à vos élèves : il leur permet de se connecter et de modifier ce projet. Ce code est spécifique à ce projet.</p>
                <div class="d-flex align-items-center">
                    @if($project->student_code)
                        <span class="badge badge-secondary p-2 mr-3" style="font-size: 1.3rem; letter-spacing: 3px;">{{ $project->student_code }}</span>
                        {!! Form::open()->route('projects.renew_code', ['project' => $project]) !!}
                            <button type="submit" class="btn btn-sm btn-outline-primary"
                                onclick="return confirm('Générer un nouveau code ? L\'ancien code ne sera plus valide et les élèves connectés seront déconnectés.');">Renouveler le code</button>
                        {!! Form::close() !!}
                    @else
                        <em class="mr-3">Aucun code n'a encore été généré pour ce projet.</em>
                        {!! Form::open()->route('projects.renew_code', ['project' => $project]) !!}
                            <button type="submit" class="btn btn-sm btn-primary">Générer le code</button>
                        {!! Form::close() !!}
                    @endif
                </div>
            </div>
        </div>
    @endif

    <div style="display: none" id="team-member-template">
        @include(
            'projects.edit.team-member',
            [ 'member' => null ]
        )
    </div>


    <div id="edit-form">
        {!! Form::open()
            ->multipart()
            ->route($submit_route, $editor_type == 'teacher' ? ['project' => $project] : [])
            ->fill($project)
            !!}
            {{ ($project && $editor_type == 'teacher') ? method_field('PUT') : '' }}
            {!! Form::hidden('refer_page', $refer_page) !!}

            <ul class="nav nav-tabs" id="project-edit-tabs">
                <li class="nav-item">
                    <a class="nav-link active" data-toggle="tab" href="#tab-general">Informations générales</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" data-toggle="tab" href="#tab-team">Composition de l'équipe</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" data-toggle="tab" href="#tab-presentation">Présentation du projet</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" data-toggle="tab" href="#tab-technical">Informations techniques</a>
                </li>
                @if($editor_type == 'teacher')
                <li class="nav-item">
                    <a class="nav-link" data-toggle="tab" href="#tab-validation">Vérification et validation</a>
                </li>
                @endif
            </ul>

            <div class="tab-content mt-3">
            <div class="tab-pane fade show active" id="tab-general">

            {!! Form::text('name', 'Nom du projet') !!}
            {!! Form::select('school_id', 'Établissement scolaire de l\'équipe', [null => ''] + $schools['options'])
                ->disabled($editor_type == 'student')
                ->help(
                "Sélectionnez l'établissement scolaire de vos élèves parmi la liste proposée. Le territoire de rattachement sera automatiquement attribué.<br><b>Vérifiez bien l'adresse complète avant de valider !</b>" . (
                $editor_type == 'teacher' ? '<br><a id="btn-open-schools-manager" href="#">Modifier ma liste d\'établissements</a>' : false))
            !!}

            {!! Form::select('grade_id', 'Niveau d\'étude de l\'équipe', [null => ''] + $grades->pluck('name', 'id')->toArray())
                ->disabled($editor_type == 'student')
                ->help("Tous les membres de l'équipe doivent être dans la même classe de NSI au moment du dépôt du dossier.") !!}

            <div class="mt-5">
                <h5>Répartition filles/garçons dans la classe</h5>
                <p><small class="form-text text-muted">Précisez la répartition totale des élèves, selon le genre, dans votre classe de NSI pour le niveau renseigné ci-dessus. <b>Une attention particulière sera accordée aux efforts engagés pour constituer des équipes mixtes.</b></small></p>

                <div class="row">
                    <div class="col-4">
                        {!! Form::text('class_girls', 'Nombre de filles')->disabled($editor_type == 'student')->wrapperAttrs(['class' => 'mb-0']) !!}
                    </div>
                    <div class="col-4">
                        {!! Form::text('class_boys', 'Nombre de garçons')->disabled($editor_type == 'student')->wrapperAttrs(['class' => 'mb-0']) !!}
                    </div>
                    <div class="col-4">
                        {!! Form::text('class_not_provided', 'Non renseigné')->disabled($editor_type == 'student')->wrapperAttrs(['class' => 'mb-0']) !!}
                    </div>
                </div>
            </div>

            </div>
            <div class="tab-pane fade" id="tab-team">

            <div class="mt-5 mb-5">
                <h5>Membres de l'équipe</h5>
                <p><small class="form-text text-muted">Précisez la composition de l'équipe pour ce projet comme suit : prénom en minuscule, nom en majuscule. Vérifiez bien l'orthographe des prénoms et des noms. Ces données seront utilisées pour l'édition des attestations de participation, les diplômes territoriaux et nationaux et la publication des projets lauréats. Les autorisations signées sont obligatoirement à joindre au dossier avant de soumettre le projet.</small></p>
                <div class="row mt-5 mb-3" id="team-members-header">
                    <div class="col-1"></div>
                    <div class="col-2">Prénom</div>
                    <div class="col-2">Nom</div>
                    <div class="col-2">Genre</div>
                    <div class="col-5">
                        Autorisations signées<br>
                        <small>Ajoutez l'autorisation signée au format PDF pour chaque élève. Le document à compléter est <a href="https://trophees-nsi.fr/ressources" target="_blank">disponible ici</a>.</small>
                    </div>
                </div>
                <div id="team-members" data-max-members="{{ config('nsi.project.team_size_max') }}">
                    @if($project)
                        @foreach($project->team_members as $member)
                            @include('projects.edit.team-member', [
                                'member' => $member
                            ])
                        @endforeach
                    @endif
                </div>
                <a href="#" id="btn-add-member">Ajouter un membre de l'équipe</a>
            </div>

            {!! Form::textarea('work_organisation', 'Organisation du travail et répartition des tâches')
                ->attrs(['style' => 'height: 200px'])
                ->help('Pouvez-vous présenter chaque membre de l\'équipe et préciser son rôle dans ce projet ? Pourquoi cette organisation du travail et comment avez-vous réparti les tâches ? Combien de temps avez-vous passé sur le projet (estimation) ? Attention ! Chaque membre de l\'équipe doit impérativement réaliser un aspect technique du projet présenté (hors design, gestion de projet, rédaction de la documentation technique ou montage vidéo). Une attention particulière sera également accordée aux efforts engagés pour intégrer les valeurs d\'inclusion et de mixité dans la composition de l\'équipe et la répartition des tâches (dans la mesure du possible). <b>Minimum 50 caractères.</b>') !!}

            </div>
            <div class="tab-pane fade" id="tab-presentation">

            {!! Form::textarea('description', 'Résumé du projet')
                ->attrs(['style' => 'height: 200px'])
                ->help('Résumez votre projet en 500 caractères maximum. <b>Vulgarisez vos propos !</b> Ce texte sera utilisé pour les communications officielles du projet auprès du grand public (site internet, réseaux sociaux). <div id="description-counter" class="text-right text-muted"></div>') !!}

            {!! Form::textarea('project_description', 'Description du projet')
                ->attrs(['style' => 'height: 200px'])
                ->help('Comment est né ce projet ? Quelle était votre problématique initiale ? Quels sont les objectifs ? À quels besoins répondez-vous ? <b>Minimum 50 caractères.</b>') !!}

            {!! Form::textarea('project_steps', 'Étapes de réalisation du projet')
                ->attrs(['style' => 'height: 200px'])
                ->help('Précisez les différentes étapes de réalisation de votre projet, de l\'idée jusqu\'au développement de votre solution. <b>Minimum 50 caractères.</b>') !!}

            {!! Form::textarea('project_outlook', 'Ouverture')
                ->attrs(['style' => 'height: 200px'])
                ->help('Quelles sont les nouvelles fonctionnalités à moyen terme ? Avez-vous des idées d\'amélioration de votre projet ? Pourriez-vous apporter une analyse critique de votre projet ? Si c\'était à refaire, que changeriez-vous dans votre organisation, les fonctionnalités du projet et les choix techniques ? Quelles compétences/appétences/connaissances avez-vous développées grâce à ce concours ? En quoi votre projet favorise-t-il l\'inclusion ? <b>Minimum 50 caractères.</b>') !!}

            <div class="row">
                @include('projects.edit.file-input', [
                    'title' => 'Image du projet',
                    'description' => 'Veuillez fournir une image au format PNG ou JPG. Dimensions : '.config('nsi.project.image_max_width').'px x '.config('nsi.project.image_max_height').'px (carré).',
                    'extensions' => '.jpg,.jpeg,.png',
                    'key' => 'image_file',
                    'file' => $project ? $project->image_file : null,
                    'class' => 'col-6 file-box mb-4'
                ])
            </div>

            {!! Form::text('video', 'Démonstration vidéo')
                ->placeholder('https://')
                ->help('La vidéo doit obligatoirement inclure une démonstration du fonctionnement du projet dans sa totalité (capture vidéo de l\'écran avec les commentaires, en bande son). Durée : 2 min à 3 min maximum. La vidéo est à publier sur <a href="https://tube-sciences-technologies.apps.education.fr/" target="_blank">l\'instance Peertube Tube Sciences & Technologies</a> par votre professeur. Renseignez ici son URL.') !!}

            </div>
            <div class="tab-pane fade" id="tab-technical">

            {!! Form::text('url', 'Dossier technique')
                ->placeholder('https://')
                ->help('Le dossier technique est à déposer sur <a href="https://forge.apps.education.fr/users/sign_in" target="_blank">la forge des communs numériques éducatifs</a> par votre professeur. Les livrables à fournir et la nomenclature à respecter sont précisés dans le règlement du concours. Renseignez ici l\'URL du projet (choisissez le niveau de visibilité "publique" dans les paramètres du projet).') !!}

            {!! Form::textarea('code_notes', 'Nature du code')
                ->attrs(['style' => 'height: 200px'])
                ->help('Précisez si le projet est une création originale. Si non, l\'exploitation de codes existants est clairement énoncée, les autrices ou les auteurs sont identifiés et mentionnés. <b>Minimum 20 caractères.</b>') !!}

            {!! Form::textarea('operationality_notes', 'Validation de l\'opérationnalité du projet / de son fonctionnement')
                ->attrs(['style' => 'height: 200px'])
                ->help('Pouvez-vous présenter l\'état d\'avancement du projet au moment du dépôt ? (ce qui est terminé, en cours de réalisation, reste à faire). Quelles approches avez-vous mis en œuvre pour vérifier l\'absence de bugs et garantir une facilité d\'utilisation de votre projet ? Quelles sont les difficultés rencontrées et les solutions apportées ? <b>Minimum 50 caractères.</b>') !!}

            {!! Form::text('ai_usage_percent', 'Part d\'utilisation de l\'IA')
                ->help('Précisez le pourcentage d\'utilisation de l\'Intelligence Artificielle (ou une estimation) sur l\'ensemble du projet développé.') !!}

            {!! Form::textarea('ai_usage_notes', 'Usage de l\'IA')
                ->attrs(['style' => 'height: 200px'])
                ->help('Si vous avez eu recours à l\'IA, précisez la liste des tâches/actions effectuées avec l\'IA, le modèle LLM utilisé et la valeur ajoutée de l\'IA dans votre projet.') !!}

            </div>
            @if($editor_type == 'teacher')
            <div class="tab-pane fade" id="tab-validation">

            {!! Form::textarea('teacher_notes', 'Informations complémentaires (commentaire de l\'enseignant sur le projet et l\'équipe)')
                ->attrs(['style' => 'height: 200px'])
                ->help("Préciser le contexte de réalisation du projet, le degré de motivation et d'implication des élèves, la progression des élèves durant l'année scolaire... et toute information utile à porter à la connaissance des membres du jury. Ce commentaire n'est pas visible par les élèves. Minimum 50 caractères.") !!}

            <div class="mt-5">
                <i>Taille maximum des fichiers : 20Mo</i>
            </div>

            <div class="mt-5">
                <input type="hidden" name="cb_tested_by_teacher" value="0"/>
        		{!! Form::checkbox('cb_tested_by_teacher', 'Je certifie avoir testé moi-même le projet, et confirme que celui-ci fonctionne comme présenté dans la vidéo. ')
                    ->checked($project && $project->tested_by_teacher) !!}
            </div>
            <div class="mt-2">
                <input type="hidden" name="cb_video_authorization" value="0"/>
        		{!! Form::checkbox('cb_video_authorization', 'Je certifie que tous les élèves de ce projet ont une autorisation signée pour l\'utilisation de l\'image ou de la voix et de leurs oeuvres.')
                    ->checked($project && $project->video_authorization) !!}
            </div>
            <div class="mt-2">
                <input type="hidden" name="cb_reglament_accepted" value="0"/>
        		{!! Form::checkbox('cb_reglament_accepted', 'Je certifie que mes élèves et moi-même avons lu et accepté le <a href="https://trophees-nsi.fr/reglement" target="_blank">règlement du concours</a>.')
                    ->checked($project && $project->reglament_accepted) !!}
            </div>

            </div>
            @endif
            </div><!-- /tab-content -->

            <div class="mt-5" id="controls-bar">
                <a class="btn btn-primary" id="btn-save-draft" href="#">Enregistrer le brouillon</a>
                @if($editor_type == 'teacher')
                    <a class="btn btn-secondary" id="btn-submit-finalized" href="#">Soumettre le projet finalisé</a>
                    @if($project)
                        <a class="btn btn-primary" id="btn-delete" href="#">Supprimer</a>
                    @endif
                @else
                    @if($project->status != 'completed')
                        <a class="btn btn-secondary" id="btn-mark-completed" href="#">Marquer le projet comme terminé</a>
                    @endif
                @endif
                <a class="btn btn-primary" href="{{ $refer_page }}">Annuler</a>
            </div>
        {!! Form::close() !!}
    </div>

    @include('common.school-popup')


    @if($project && $editor_type == 'teacher')
        <div class="hidden" id="delete-form">
            {!! Form::open()->route('projects.destroy', ['project' => $project]) !!}
            {{ method_field('DELETE') }}
            {!! Form::hidden('refer_page', $refer_page) !!}
            {!! Form::close() !!}
        </div>
    @endif

    <script>
        $(document).ready(function() {
            var config = {!! json_encode(config('nsi.project')) !!};

            // Tab system : purely client-side. All fields stay in the DOM (just
            // hidden), so the whole form is submitted regardless of the active tab.
            function activateTab(tabId) {
                $('#project-edit-tabs a').removeClass('active');
                $('#project-edit-tabs a[href="#' + tabId + '"]').addClass('active');
                $('#edit-form .tab-pane').removeClass('active show');
                $('#edit-form #' + tabId).addClass('active show');
            }
            $('#project-edit-tabs a').on('click', function(e) {
                e.preventDefault();
                activateTab($(this).attr('href').substring(1));
            });

            var project_editor = ProjectEditor({
                form: $('#edit-form>form').first(),
                config: config,
                activateTab: activateTab,
                onError: function() {
                    $('#controls-bar').show();
                    // switch to the tab containing the first field with an error
                    var $invalid = $('#edit-form .is-invalid').first();
                    if($invalid.length) {
                        var tabId = $invalid.closest('.tab-pane').attr('id');
                        if(tabId) {
                            activateTab(tabId);
                        }
                    }
                }
            });

            $('#btn-save-draft').click(function(e) {
                $('#controls-bar').hide();
                project_editor.submit();
            })

            $('#btn-submit-finalized').click(function(e) {
                $('#controls-bar').hide();
                project_editor.submit({
                    finalize: '1'
                });
            });

            $('#btn-mark-completed').click(function(e) {
                $('#controls-bar').hide();
                project_editor.submit({
                    complete: '1'
                });
            });

            $('#btn-delete').click(function(e) {
                e.preventDefault();
                if(confirm('Êtes-vous certain de vouloir supprimer ce projet ?')) {
                    var del_form = $('#delete-form>form').first();
                    del_form.submit();
                }
            });

            @if($editor_type == 'teacher')
            // schools popup
            var schools_manager = SchoolsManager();

            $('#btn-open-schools-manager').on('click', function() {
                schools_manager.show();
            });
            @endif
        });
    </script>
@endsection
