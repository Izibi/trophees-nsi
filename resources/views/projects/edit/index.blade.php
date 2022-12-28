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

            {!! Form::select('school_id', 'Établissement', $schools['options'])->help(Auth::user()->role == 'teacher' ? '<a id="btn-open-schools-manager" href="#">Modifier ma liste d\'établissements</a>' : false) !!}

            {!! Form::select('grade_id', 'Niveau', [null => ''] + $grades->pluck('name', 'id')->toArray()) !!}

            {!! Form::select('academy_id', 'Choisir une académie', [null => ''] + $academies->pluck('name', 'id')->toArray()) !!}

            <div class="mt-5 mb-5">
                <h5>Team members</h5>
                <p><small class="form-text text-muted">Précisez la composition de l'équipe pour ce projet. La mixité de l'équipe pourra être prise en compte pour certains prix.</small></p>
                <div id="team-members">
                    @if($project)
                        @foreach($project->team_members as $member)
                            @include('projects.edit.team-member', [
                                'member' => $member
                            ])
                        @endforeach
                    @endif
                </div>
                <a href="#" id="btn-add-member">Add team member</a>
            </div>





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
            <p><small class="form-text text-muted">Précisez la répartition des élèves en NSI pour le niveau renseigné ci-dessus.</small></p>

            {!! Form::textarea('description', 'Résumé du projet')
                ->attrs(['style' => 'height: 200px'])
                ->help('<div id="description-counter" class="text-right text-muted"></div>') !!}

            {!! Form::text('video', 'Vidéo')
                ->placeholder('https://')
                ->help('La vidéo doit être publiée sur <a href="https://peertube.fr" target="_blank">peertube.fr</a>. Renseignez ici son URL.') !!}

            {!! Form::text('url', 'URL')->placeholder('https://') !!}

            <div class="row">
                @include('projects.edit.file-input', [
                    'title' => 'Image',
                    'description' => 'Taille maximum : 20Mo. Veuillez fournir une image carrée, de taille '.config('nsi.project.image_max_width').'px &#10005;'.config('nsi.project.image_max_height').' px.',
                    'extensions' => '.jpg,.jpeg,.png,.gif',
                    'key' => 'image_file',
                    'file' => $project ? $project->image_file : null,
                    'class' => 'col-6 file-box mb-4'
                ])

                @include('projects.edit.file-input', [
                    'title' => 'PDF de présentation',
                    'description' => 'Taille maximum : 20Mo. Voir <a href="https://trophees-nsi.fr/preparer-votre-participation" target="_blank">ici</a> pour le contenu demandé dans ce pdf.',
                    'extensions' => '.pdf',
                    'key' => 'presentation_file',
                    'file' => $project ? $project->presentation_file : null,
                    'class' => 'col-6 file-box mb-4'
                ])
            </div>

            {!! Form::textarea('teacher_notes', 'Remarques de l\'enseignant')
                ->attrs(['style' => 'height: 200px']) !!}

            <div class="mt-5">
                <input type="hidden" name="cb_reglament_accepted" value="0"/>
                {!! Form::checkbox('cb_reglament_accepted', 'Je certifie également avoir testé moi-même le projet, et confirme que celui-ci fonctionne comme présenté dans la vidéo. Je certifie enfin que tous les éleves dont l\'image ou la voix est présente sur la vidéo ont une autorisation parentale signée.')
                    ->checked($project && $project->reglament_accepted) !!}
            </div>
            <div class="mt-2">
                <input type="hidden" name="cb_tested_by_teacher" value="0"/>
                {!! Form::checkbox('cb_tested_by_teacher', 'Je certifie avoir lu et accepté le <a href="https://trophees-nsi.fr/le-reglement" target="_blank">règlement du concours</a>.')
                    ->checked($project && $project->tested_by_teacher) !!}
            </div>

            <div class="mt-5">
                <a class="btn btn-primary" id="btn-save-draft" href="#">Enregistrer le brouillon</a>
                <a class="btn btn-secondary" id="btn-submit-finalized" href="#">Soumettre le projet finalisé</a>
                @if($project)
                    <a class="btn btn-primary" id="btn-delete" href="#">Supprimer</a>
                @endif
                <a class="btn btn-primary" href="{{ $refer_page }}">Annuler</a>
            </div>
        {!! Form::close() !!}
    </div>

    @include('projects.school-popup')


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
            var config = {!! json_encode(config('nsi.project')) !!}

            var form = $('#edit-form>form').first();

            $('#btn-save-draft').click(function(e) {
                e.preventDefault();
                form.submit();
            })

            $('#btn-submit-finalized').click(function(e) {
                e.preventDefault();
                if(confirm('Vous êtes sur le point de soumettre le projet, il ne sera plus possible de le modifier. Continuer ?')) {
                    form.append('<input type="hidden" name="finalize" value="1"/>');
                    form.submit();
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



            // description counter
            var inp_description = $('#inp-description');
            var description_counter = $('#description-counter');
            function refreshDescriptionCounter() {
                var l = inp_description.val().length;
                if(l >= config.description_max_length) {
                    inp_description.val(inp_description.val().substr(0, config.description_max_length));
                    l = config.description_max_length;
                }
                description_counter.text(l + '/' + config.description_max_length);
            }
            refreshDescriptionCounter();
            inp_description.bind('input propertychange', refreshDescriptionCounter);


            // file inputs
            $('.custom-file-input').on('change', function() {
                var el = $(this);
                var name = el.val().split("\\").pop();
                el.siblings('.custom-file-label').addClass('selected').html(name);
                el.closest('.custom-file').addClass('custom-file-selected');
            });
            $('.custom-file-clear').on('click', function(e) {
                e.preventDefault();
                var el = $(this);
                el.siblings('input').val('');
                el.siblings('.custom-file-label').removeClass('selected').html('');
                el.closest('.custom-file').removeClass('custom-file-selected');
            });

            $('.link-delete-file').on('click', function(e) {
                e.preventDefault();
                var el = $(this);

                var team_member_row = el.closest('.team-member-row');
                if(team_member_row.length) {
                    var file = 'team_member.' + team_member_row.find('input[name="team_member_id[]"]').val();
                } else {
                    var file = 'project.' + el.data('file');
                }
                var text = el.siblings('.file-box-title').text() + ' - ' + el.text();
                el.closest('.file-box').empty().append(text).append(
                    $('<input type="hidden">').attr('name', 'delete_uploads[]').val(file)
                )
            })


            // team members
            $('#btn-add-member').on('click', function(e) {
                e.preventDefault();
                var row = $('#team-member-template').clone(true);
                row.find('.is-valid').removeClass('.is-valid');
                $('#team-members').append(row);
                row.show();
            })

            $('.btn-remove-member').on('click', function(e) {
                e.preventDefault();
                $(this).closest('.team-member-row').remove();
            })


            // debug
            //schools_manager.show();
            //$('#section-schools-manager').hide();
            //$('#section-schools-editor').show();
        });
    </script>
@endsection
