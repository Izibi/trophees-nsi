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

            {!! Form::select('school_id', 'School', $schools) !!}
            {!! Form::select('grade_id', 'Grade', $grades) !!}

            <div class="row">
                <div class="col-4">
                    {!! Form::text('team_girls', 'Team girls') !!}
                </div>
                <div class="col-4">
                    {!! Form::text('team_boys', 'Team boys') !!}
                </div>
                <div class="col-4">
                    {!! Form::text('team_not_provided', 'Not provided') !!}
                </div>        
            </div>
            <p><small class="form-text text-muted">We have a separate prize for girls to encourage female participation.</small></p>

            {!! Form::textarea('description', 'Description') !!}

            {!! Form::urlInput('video_url', 'Video')->help('Please upload your video to freetube and put link here') !!}

            {!! Form::file('presentation_file', 'Presentation PDF')->attrs(['accept' => '.pdf']) !!}
            @if($project && !is_null($project->presentation_file))
                <a href="{{ Storage::disk('presentation_files')->url($project->presentation_file) }}" target="_blank">View uploaded file</a>
            @endif

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
        });
    </script>
@endsection