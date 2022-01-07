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

            {!! Form::select('grade_id', 'Grade', $grades) !!}

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

            {!! Form::textarea('description', 'Description') !!}

            {!! Form::urlInput('video_url', 'Video')->help('Please upload your video to freetube and put link here') !!}


            <div class="row">
                <div class="col-6">
                    {!! Form::file('presentation_file', 'Presentation PDF')->attrs(['accept' => '.pdf']) !!}
                    @if($project && !is_null($project->presentation_file))
                        <a href="{{ Storage::disk('uploads')->url($project->presentation_file) }}" target="_blank">View uploaded file</a>
                    @endif
                </div>
                <div class="col-6">
                    {!! Form::file('image_file', 'Image')->attrs(['accept' => '.jpg,.jpeg,.png,.gif']) !!}
                    @if($project && !is_null($project->image_file))
                        <a href="{{ Storage::disk('uploads')->url($project->image_file) }}" target="_blank" class="border d-block" 
                            style="width: 160px; height: 120px; background: center / contain no-repeat url({{ Storage::disk('uploads')->url($project->image_file) }})">
                        </a>
                    @endif            
                </div>
            </div>

            <div class="mt-5">
                <a class="btn btn-primary" id="btn-save-draft" href="#">Save Draft</a>
                <a class="btn btn-primary" id="btn-submit" href="#">Submit</a>
                @if($project)
                    <a class="btn btn-primary" id="btn-delete" href="#">Delete</a>
                @endif
                <a class="btn btn-primary" href="{{ $refer_page }}">Cancel</a>
            </div>
        {!! Form::close() !!}   
    </div>


    <script>
        window.user_schools = {!! json_encode($schools['data']) !!}
        window.regions = {!! json_encode($regions) !!}
    </script>
    
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
            var form = $('#edit-form>form').first();
           
            $('#btn-save-draft').click(function(e) {
                e.preventDefault();
                form.append('<input type="hidden" name="status" value="draft"/>');
                form.submit();
            })


            $('#btn-submit').click(function(e) {
                e.preventDefault();
                form.submit();
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

        });
    </script>
@endsection