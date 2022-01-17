@extends('layout')

@section('content')
    <div class="row">
        <div class="col-6">
            @include('projects.details')

            <div class="mt-3">
                {!! Form::open()->route('projects.set_status', ['project' => $project])->fill($project) !!}
                    {!! Form::select('status', 'Status')->options([
                        'draft' => 'Draft',
                        'finalized' => 'Finalized',
                        'validated' => 'Validated',
                        'incomplete' => 'Incomplete',
                        'masked' => 'Masked'
                    ]) !!}
                    {!! Form::hidden('refer_page', $refer_page) !!}
                    <div class="mt-3">
                        {!! Form::submit('Save') !!}
                        <a class="btn btn-primary" href="{{ $refer_page }}">Cancel</a>
                    </div>
                {!! Form::close() !!}
            </div>
        </div>
        <div class="col-6">
            @include('projects.rating.show')
        </div>
    </div>
@endsection