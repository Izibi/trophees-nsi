@extends('layout')

@section('content')

<h1>Edit contest</h1>

    <div id="edit-form">
        {!! Form::open()
            ->route($contest ? 'contests.update' : 'contests.store', ['contest' => $contest])
            ->fill($contest)
            !!}
            {{ $contest ? method_field('PUT') : '' }}
            {!! Form::text('name', 'Name') !!}
            {!! Form::text('year', 'Year') !!}
            {!! Form::textarea('message', 'Message')->attrs(['style' => 'height: 300px']) !!}
            {!! Form::select('status', 'Status', [
                'preparing' => 'preparing',
                'open' => 'open',
                'grading' => 'grading',
                'deliberating' => 'deliberating',
                'closed' => 'closed'
            ]) !!}
        {!! Form::close() !!}
        <div class="mt-5">
            <a class="btn btn-primary" id="btn-ok" href="#">Ok</a>
            <a class="btn btn-primary" href="{{ $refer_page }}">Cancel</a>
            @if($contest)
                <a class="btn btn-primary" id="btn-delete" href="#">Delete</a>
            @endif
        </div>
    </div>


    @if($contest)
        <div class="hidden" id="delete-form">
            {!! Form::open()->route('contests.destroy', ['contest' => $contest]) !!}
            {{ method_field('DELETE') }}
            {!! Form::hidden('refer_page', $refer_page) !!}
            {!! Form::close() !!}
        </div>
    @endif

    <script>
        $(document).ready(function() {
            var form = $('#edit-form>form').first();

            $('#btn-ok').click(function(e) {
                e.preventDefault();
                form.submit();
            })

            $('#btn-delete').click(function(e) {
                e.preventDefault();
                if(confirm('This action will delete all data related to this contest. Are you sure?')) {
                    var del_form = $('#delete-form>form').first();
                    del_form.submit();
                }
            });
        });
    </script>
@endsection