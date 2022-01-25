@extends('layout')

@section('content')
    <div id="edit-form">
        {!! Form::open()
            ->multipart()
            ->route($project ? 'projects.update' : 'projects.store', ['project' => $project])
            ->fill($project)
            !!}
            {{ $project ? method_field('PUT') : '' }}

            {!! Form::text('name', 'Nom du projet') !!}

            {!! Form::select('school_id', 'Établissement', $schools['options'])->help('<a id="btn-open-schools-manager" href="#">Modifier ma liste d\'établissements</a>') !!}

            {!! Form::select('grade_id', 'Niveau', [null => ''] + $grades->pluck('name', 'id')->toArray()) !!}

            <div class="row">
                <div class="col-4">
                    {!! Form::text('team_girls', 'Nombre de filles')->wrapperAttrs(['class' => 'mb-0']) !!}
                </div>
                <div class="col-4">
                    {!! Form::text('team_boys', 'Nombre de garçons')->wrapperAttrs(['class' => 'mb-0']) !!}
                </div>
                <div class="col-4">
                    {!! Form::text('team_not_provided', 'Non renseigné')->wrapperAttrs(['class' => 'mb-0']) !!}
                </div>
            </div>
            <p><small class="form-text text-muted">Précisez la composition de l'équipe pour ce projet. La mixité de l'équipe pourra être prise en compte pour certains prix.</small></p>

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
                ->help('<div id="description-counter"></div>') !!}

            {!! Form::text('video', 'Vidéo')
                ->help('La vidéo doit être publiée sur <a href="https://peertube.fr" target="_blank">peertube.fr</a>. Renseignez ici son URL.') !!}


            <div class="row">
                <div class="col-6 file-box mb-4">
                    <span class="file-box-title">Image</span>
                    @if($project && !is_null($project->image_file))
                        - <a href="{{ Storage::disk('uploads')->url($project->image_file) }}" target="_blank">télécharger</a> ou
                        <a href="#" class="link-delete-file" data-file="image_file">supprimer</a>
                    @else
                        <div class="custom-file mt-2">
                            <span class="custom-file-clear" title="Clear">&times;</span>
                            <input name="image_file" id="inp-image_file" type="file" accept=".jpg,.jpeg,.png,.gif" class="custom-file-input">
                            <label for="inp-image_file" class="custom-file-label text-truncate">Choisir un fichier...</label>
                        </div>
                    @endif
                </div>
                <div class="col-6 file-box mb-4">
                    <span class="file-box-title">PDF de présentation</span>
                    @if($project && !is_null($project->presentation_file))
                        - <a href="{{ Storage::disk('uploads')->url($project->presentation_file) }}" target="_blank">télécharger</a> ou
                        <a href="#" class="link-delete-file" data-file="presentation_file">supprimer</a>
                    @else
                        <div class="custom-file mt-2">
                            <span class="custom-file-clear" title="Clear">&times;</span>
                            <input name="presentation_file" id="inp-presentation_file" type="file" accept=".pdf" class="custom-file-input">
                            <label for="inp-presentation_file" class="custom-file-label text-truncate">Choisir un fichier...</label>
                        </div>
                        <small>Voir <a href="https://trophees-nsi.fr/preparer-votre-participation" target="_blank">ici</a> pour le contenu demandé dans ce pdf.</small>
                    @endif
                </div>
                <div class="col-6 file-box mb-4">
                    <span class="file-box-title">Zip du projet</span>
                    @if($project && !is_null($project->zip_file))
                        - <a href="{{ Storage::disk('uploads')->url($project->zip_file) }}" target="_blank">télécharger</a> ou
                        <a href="#" class="link-delete-file" data-file="zip_file">supprimer</a>
                    @else
                        <div class="custom-file mt-2">
                            <span class="custom-file-clear" title="Clear">&times;</span>
                            <input name="zip_file" id="inp-zip_file" type="file" accept=".zip" class="custom-file-input">
                            <label for="inp-zip_file" class="custom-file-label text-truncate">Choisir un fichier...</label>
                        </div>
                        <small>Voir <a href="https://trophees-nsi.fr/preparer-votre-participation" target="_blank">ici</a> pour le contenu demandé dans ce zip.</small>
                    @endif
                </div>
                <div class="col-6 file-box mb-4">
                    <span class="file-box-title">Autorisations parentales</span>
                    @if($project && !is_null($project->parental_permissions_file))
                        - <a href="{{ Storage::disk('uploads')->url($project->parental_permissions_file) }}" target="_blank">télécharger</a> ou
                        <a href="#" class="link-delete-file" data-file="parental_permissions_file">supprimer</a>
                    @else
                        <div class="custom-file mt-2">
                            <span class="custom-file-clear" title="Clear">&times;</span>
                            <input name="parental_permissions_file" id="inp-parental_permissions_file" type="file" class="custom-file-input">
                            <label for="inp-parental_permissions_file" class="custom-file-label text-truncate">Choisir un fichier...</label>
                        </div>
                    @endif
                </div>
            </div>

            <div class="mt-5">
                <input type="hidden" name="cb_tested_by_teacher" value="0"/>
                {!! Form::checkbox('cb_tested_by_teacher', 'Je certifie que le projet soumis est en conformité avec le <a href="https://trophees-nsi.fr/le-reglement" target="_blank">règlement du concours</a>. Je certifie également avoir testé moi-même le projet, et confirme que celui-ci fonctionne comme présenté dans la vidéo. Je certifie enfin que tous les éleves dont l\'image ou la voix est présente sur la vidéo ont une autorisation parentale signée.')
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
                var cn = l <= config.description_max_length ? 'text-success' : 'text-danger';
                description_counter.attr('class', 'text-right ' + cn);
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
                var file = el.data('file');
                var text = el.siblings('.file-box-title').text() + ' - ' + el.text();
                el.closest('.file-box').empty().append(text).append(
                    $('<input type="hidden">').attr('name', 'delete_uploads[]').val(file)
                )
            })

            // debug
            //schools_manager.show();
            //$('#section-schools-manager').hide();
            //$('#section-schools-editor').show();
        });
    </script>
@endsection