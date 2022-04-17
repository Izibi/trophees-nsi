@extends('layout')

@section('content')
    <div class="alert alert-warning">This action will delete user <strong>#{{ $user->id }}</strong> {{ $user->name }}.</div>
    {!! Form::open()->route('admin_interface.user_delete') !!}
        {!! Form::hidden('redirect_url', $redirect_url) !!}
        {!! Form::hidden('user_id', $user->id) !!}
        {!! Form::text('backup_user_id', 'Backup user ID')->help('Enter user ID to backup all data assigned to deleteable user. <a href="#" id="link-use-admin-id">Click to use admin ID</a>.') !!}
        {!! Form::submit('Delete user') !!}
    {!! Form::close() !!}

    <script>
        $('#link-use-admin-id').on('click', function() {
            $('input[name=backup_user_id]').val({!! Auth::user()->id !!})
        })
    </script>

@endsection