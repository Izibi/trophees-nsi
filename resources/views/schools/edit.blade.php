@extends('layout')

@section('content')
    
<h1>Edit school</h1>
    
    <div id="edit-form">
        {!! Form::open()
            ->multipart()
            ->route('schools.update', ['school' => $school]) 
            ->fill($school)
            !!}
            {{ method_field('PUT') }}

            {!! Form::text('name', 'Name') !!}

            {!! Form::text('address', 'Name') !!}
            {!! Form::text('city', 'Name') !!}
            {!! Form::text('zip', 'Name') !!}

            {!! Form::select('country_id', 'Country', $countries) !!}
            {!! Form::hidden('region_id') !!}
            {!! Form::select('region', 'Region', []) !!}
            {!! Form::text('uai', 'UAI') !!}
        {!! Form::close() !!}   
        <div class="mt-5">
            <a class="btn btn-primary" id="btn-ok" href="#">Ok</a>
            <a class="btn btn-primary" href="{{ $refer_page }}">Cancel</a>
            <a class="btn btn-primary" id="btn-delete" href="#">Delete</a>
        </div>
    </div>


    <div class="hidden" id="delete-form">
        {!! Form::open()->route('schools.destroy', ['school' => $school]) !!}
        {{ method_field('DELETE') }}
        {!! Form::hidden('refer_page', $refer_page) !!}
        {!! Form::close() !!}   
    </div>

    <script>
        $(document).ready(function() {
            var form = $('#edit-form>form').first();
           
            $('#btn-ok').click(function(e) {
                e.preventDefault();
                form.submit();
            })

            $('#btn-delete').click(function(e) {
                e.preventDefault();
                if(confirm('This action delete all data related to this school. Are you sure?')) {
                    var del_form = $('#delete-form>form').first();
                    del_form.submit();
                }
            });  
            
            var regions = {!! json_encode($regions) !!}
            RegionSelector($('#edit-form'), regions);
        });
    </script>
@endsection