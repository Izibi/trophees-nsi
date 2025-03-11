@extends('layout')

@section('content')
    <div style="display: none" id="team-member-template">
        @include(
            'projects.edit.team-member',
            [ 'member' => null ]
        )
    </div>


    <div id="edit-form">
        {!! Form::open()
            ->multipart()
            ->route($project ? 'projects.update' : 'projects.store', ['project' => $project])
            ->fill($project)
            !!}
            {{ $project ? method_field('PUT') : '' }}
            {!! Form::hidden('refer_page', $refer_page) !!}

            {!! Form::text('name', 'Nom du projet') !!}
            {!! Form::select('school_id', 'Établissement', [null => ''] + $schools['options'])->help(Auth::user()->role == 'teacher' ? '<a id="btn-open-schools-manager" href="#">Modifier ma liste d\'établissements</a>' : false) !!}

            <div class="mt-5 mb-5">
                <h5>Membres de l'équipe</h5>
                <p><small class="form-text text-muted">Précisez la composition de l'équipe pour ce projet. Vérifiez bien l'orthographe des noms et des prénoms ! Les attestations signées sont à joindre au dossier avant la validation du dépôt.</small></p>
                <div class="row mt-5 mb-3" id="team-members-header">
                    <div class="col-1"></div>
                    <div class="col-2">Prénom</div>
                    <div class="col-2">Nom</div>
                    <div class="col-2">Genre</div>
                    <div class="col-5">
                        Autorisations signées<br>
                        <small>Ajoutez l'autorisation signée pour chaque élève. Le document à compléter est <a href="https://trophees-nsi.fr/ressources" target="_blank">disponible ici</a>.</small>
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


            {!! Form::select('grade_id', 'Niveau scolaire', [null => ''] + $grades->pluck('name', 'id')->toArray()) !!}


            <div class="mt-5">
                <h5>Répartition de la classe</h5>
            	<p><small class="form-text text-muted">Précisez la répartition totale des élèves en NSI pour le niveau renseigné ci-dessus.</small></p>

                <div class="row">
                    <div class="col-4">
                        {!! Form::text('class_girls', 'Nombre de filles')->wrapperAttrs(['class' => 'mb-0']) !!}
                    </div>
                    <div class="col-4">
                        {!! Form::text('class_boys', 'Nombre de garçons')->wrapperAttrs(['class' => 'mb-0']) !!}
                    </div>
                    <div class="col-4">
                        {!! Form::text('class_not_provided', 'Non renseigné')->wrapperAttrs(['class' => 'mb-0']) !!}
                    </div>
                </div>
            </div>

	    <p>&nbsp;</p>


            {!! Form::textarea('description', 'Résumé du projet')
                ->attrs(['style' => 'height: 200px'])
                ->help('Ce résumé doit être écrit par les membres de l\'équipe. Ce texte sera utilisé pour les communications officielles du projet (site internet, réseaux sociaux). <div id="description-counter" class="text-right text-muted"></div>') !!}

            {!! Form::text('video', 'Vidéo')
                ->placeholder('https://')
                ->help('La vidéo est à publier sur <a href="https://tube-sciences-technologies.apps.education.fr/" target="_blank">l\'instance Peertube Tube Sciences & Technologies</a>. Renseignez ici son URL.') !!}

            {!! Form::text('url', 'Dossier technique')
                ->placeholder('https://')
                ->help('Le dossier technique est à déposer sur <a href="https://docs.forge.apps.education.fr/#qui-peut-sinscrire-et-participer-a-la-forge-des-communs-numeriques-educatifs" target="_blank">la forge des communs numériques éducatifs</a>. Les éléments du dossier technique et l\'organisation sont <a href="https://trophees-nsi.fr/ressources" target="_blank">précisés ici</a>.') !!}

            <div class="row">
                @include('projects.edit.file-input', [
                    'title' => 'Image',
                    'description' => 'Veuillez fournir une image carrée, de taille '.config('nsi.project.image_max_width').'px &#10005;'.config('nsi.project.image_max_height').' px.',
                    'extensions' => '.jpg,.jpeg,.png,.gif',
                    'key' => 'image_file',
                    'file' => $project ? $project->image_file : null,
                    'class' => 'col-6 file-box mb-4'
                ])
            </div>

            {!! Form::textarea('teacher_notes', 'Remarques de l\'enseignant')
                ->attrs(['style' => 'height: 200px'])
                ->help("Merci de bien vouloir apporter des précisions utiles à porter à la connaissance des membres du jury (contexte de réalisation du projet, motivation et implication des élèves, progression des élèves durant l'année scolaire) et confirmer la bonne vérification du fonctionnement du projet.") !!}

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
        		{!! Form::checkbox('cb_video_authorization', 'Je certifie que tous les éleves de ce projet ont une autorisation signé pour l\'utilisation de l\'image ou de la voix et de leurs oeuvres.')
                    ->checked($project && $project->video_authorization) !!}
            </div>
            <div class="mt-2">
                <input type="hidden" name="cb_reglament_accepted" value="0"/>
        		{!! Form::checkbox('cb_reglament_accepted', 'Je certifie avoir lu et accepté le <a href="https://trophees-nsi.fr/reglement" target="_blank">règlement du concours</a>.')
                    ->checked($project && $project->reglament_accepted) !!}
            </div>

            <div class="mt-5" id="controls-bar">
                <a class="btn btn-primary" id="btn-save-draft" href="#">Enregistrer le brouillon</a>
                <a class="btn btn-secondary" id="btn-submit-finalized" href="#">Soumettre le projet finalisé</a>
                @if($project)
                    <a class="btn btn-primary" id="btn-delete" href="#">Supprimer</a>
                @endif
                <a class="btn btn-primary" href="{{ $refer_page }}">Annuler</a>
            </div>
        {!! Form::close() !!}
    </div>

    @include('projects.edit.school-popup')


    @if($project)
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

            var project_editor = ProjectEditor({
                form: $('#edit-form>form').first(),
                config: config,
                onError: function() {
                    $('#controls-bar').show();
                }
            });

            $('#btn-save-draft').click(function(e) {
                $('#controls-bar').hide();
                project_editor.submit();
            })

            $('#btn-submit-finalized').click(function(e) {
                var text = 'Vous êtes sur le point de soumettre le projet, il ne sera plus possible de le modifier. Continuer ?';
                if(confirm(text)) {
                    $('#controls-bar').hide();
                    project_editor.submit({
                        finalize: '1'
                    });
                }
            });

            $('#btn-delete').click(function(e) {
                e.preventDefault();
                if(confirm('Êtes-vous certain de vouloir supprimer ce projet ?')) {
                    var del_form = $('#delete-form>form').first();
                    del_form.submit();
                }
            });




            // schools popup
            var schools_manager = SchoolsManager();

            $('#btn-open-schools-manager').on('click', function() {
                schools_manager.show();
            });








            // debug
            //schools_manager.show();
            //$('#section-schools-manager').hide();
            //$('#section-schools-editor').show();
        });
    </script>
@endsection
