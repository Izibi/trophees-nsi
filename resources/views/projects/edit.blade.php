@extends('layout')

@section('content')
    <div id="edit-form">
        {!! Form::open()
            ->multipart()
            ->route($project ? 'projects.update' : 'projects.store', ['project' => $project])
            ->fill($project)
            !!}
            {{ $project ? method_field('PUT') : '' }}

            {!! Form::text('name', 'Project name') !!}

            {!! Form::select('school_id', 'School', $schools['options'])->help('<a id="btn-open-schools-manager" href="#">Edit my list of schools</a>') !!}

            {!! Form::select('grade_id', 'Grade', [null => ''] + $grades->pluck('name', 'id')->toArray()) !!}

            <div class="row">
                <div class="col-4">
                    {!! Form::text('team_girls', 'Team girls')->wrapperAttrs(['class' => 'mb-0']) !!}
                </div>
                <div class="col-4">
                    {!! Form::text('team_boys', 'Team boys')->wrapperAttrs(['class' => 'mb-0']) !!}
                </div>
                <div class="col-4">
                    {!! Form::text('team_not_provided', 'Not provided')->wrapperAttrs(['class' => 'mb-0']) !!}
                </div>
            </div>
            <p><small class="form-text text-muted">We have a separate prize for girls to encourage female participation.</small></p>

            {!! Form::textarea('description', 'Description')
                ->attrs(['style' => 'height: 200px'])
                ->help('<div id="description-counter"></div>') !!}

            {!! Form::text('video', 'Video')
                ->help('Please upload your video to <a href="https://peertube.fr" target="_blank">peertube.fr</a> and copy/paste video URL here.') !!}


            <div class="row">
                <div class="col-4 file-box">
                    <span class="file-box-title">Image</span>
                    @if($project && !is_null($project->image_file))
                        - <a href="{{ Storage::disk('uploads')->url($project->image_file) }}" target="_blank">download</a> or
                        <a href="#" class="link-delete-file" data-file="image_file">delete</a>
                    @endif
                    <div class="custom-file mt-2">
                        <span class="custom-file-clear" title="Clear">&times;</span>
                        <input name="image_file" id="inp-image_file" type="file" accept=".jpg,.jpeg,.png,.gif" class="custom-file-input">
                        <label for="inp-image_file" class="custom-file-label text-truncate">Choose file...</label>
                    </div>
                </div>
                <div class="col-4 file-box">
                    <span class="file-box-title">Presentation PDF</span>
                    @if($project && !is_null($project->presentation_file))
                        - <a href="{{ Storage::disk('uploads')->url($project->presentation_file) }}" target="_blank">download</a> or
                        <a href="#" class="link-delete-file" data-file="presentation_file">delete</a>
                    @endif
                    <div class="custom-file mt-2">
                        <span class="custom-file-clear" title="Clear">&times;</span>
                        <input name="presentation_file" id="inp-presentation_file" type="file" accept=".pdf" class="custom-file-input">
                        <label for="inp-presentation_file" class="custom-file-label text-truncate">Choose file...</label>
                    </div>
                </div>
                <div class="col-4 file-box">
                    <span class="file-box-title">Zip of project</span>
                    @if($project && !is_null($project->zip_file))
                        - <a href="{{ Storage::disk('uploads')->url($project->zip_file) }}" target="_blank">download</a> or
                        <a href="#" class="link-delete-file" data-file="zip_file">delete</a>
                    @endif
                    <div class="custom-file mt-2">
                        <span class="custom-file-clear" title="Clear">&times;</span>
                        <input name="zip_file" id="inp-zip_file" type="file" accept=".zip" class="custom-file-input">
                        <label for="inp-zip_file" class="custom-file-label text-truncate">Choose file...</label>
                    </div>
                    <small>Should include executable, source codes and documentation. See <a href="#">here</a> for details.</small>
                </div>
            </div>

            <div class="mt-5">
                <input type="hidden" name="cb_tested_by_teacher" value="0"/>
                {!! Form::checkbox('cb_tested_by_teacher', 'I hereby declare that I have tested the project and that what is shown in the video really works.')
                    ->checked($project && $project->tested_by_teacher) !!}
            </div>

            <div class="mt-5">
                <a class="btn btn-primary" id="btn-save-draft" href="#">Save Draft</a>
                <a class="btn btn-secondary" id="btn-submit-finalized" href="#">Submit finalized project</a>
                @if($project)
                    <a class="btn btn-primary" id="btn-delete" href="#">Delete</a>
                @endif
                <a class="btn btn-primary" href="{{ $refer_page }}">Cancel</a>
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
                if(confirm('This will change project status to finalized, cancellation will not be possible. Continue?')) {
                    form.append('<input type="hidden" name="finalize" value="1"/>');
                    form.submit();
                }
            });

            $('#btn-delete').click(function(e) {
                e.preventDefault();
                if(confirm('Are you sure?')) {
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