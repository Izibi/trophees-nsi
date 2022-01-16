@extends('layout')

@section('content')
    <table class="table table-bordered">
        <tbody>
        <tr>
            <td>Name</td>
            <td>{{ $user->name }}</td>
        </tr>
        <tr>
            <td>Login</td>
            <td>{{ $user->login }} </td>
        </tr>
        <tr>
            <td>Professional email</td>
            <td>{{ $user->email }}</td>
        </tr>
        <tr>
            <td>Secondary email</td>
            <td>{{ $user->secondary_email }}</td>
        </tr>
        </tbody>
    </table>


    <div id="edit-form" class="mt-3">
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

            {!! Form::select('region_id', 'Region', [null => ''] + $regions->pluck('name', 'id')->toArray()) !!}
            {!! Form::select('country_id', 'Country', [null => ''] + $countries->pluck('name', 'id')->toArray()) !!}

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

            window.regions = {!! json_encode($regions) !!}
            window.countries = {!! json_encode($countries) !!}
            RegionSelector($('#edit-form'));
        });
    </script>
@endsection