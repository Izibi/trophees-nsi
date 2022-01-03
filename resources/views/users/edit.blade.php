@extends('layout')

@section('content')
    <p>
        {{ $user->first_name }} {{ $user->last_name }} 
    </p>
    <p>
        {{ $user->email }}
    </p>
    <p>
        {{ $user->secondary_email }}
    </p>
    @if($user->region_id)
        <p>
            {{ $user->region->name }}
        </p>    
    @endif


    <div id="edit-form">
        {!! Form::open()
            ->route('users.update', ['user' => $user]) 
            ->fill($user)            
            !!}
            {{ method_field('PUT') }}    

            {!!Form::select('validated', 'Validated user', 
                [
                    '0' => 'No',
                    '1' => 'Yes',
                ]
            )!!}

            {!!Form::select('role', 'Role', 
                [
                    'teacher' => 'Teacher',
                    'jury' => 'Jury',
                    'admin' => 'Admin',
                ]
            )!!}
           
        {!! Form::close() !!}

        <div class="mt-5">
            <a class="btn btn-primary" id="btn-ok" href="#">Ok</a>
            <a class="btn btn-primary" href="{{ $refer_page }}">Cancel</a>
            <a class="btn btn-primary" id="btn-delete" href="#">Delete user</a>
        </div>        
    </div>


    <div class="hidden" id="delete-form">
        {!! Form::open()->route('users.destroy', ['user' => $user]) !!}
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
                if(confirm('Are you sure?')) {
                    var del_form = $('#delete-form>form').first();
                    del_form.submit();
                }
            });            
        });
    </script>    
@endsection