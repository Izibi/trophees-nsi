@extends('layout')

@section('content')
    <div class="row">
        <div class="col-7">
            @include('projects.details')

            <div class="mt-5">
                {!! Form::open()->route('projects.set_status', ['project' => $project])->fill($project) !!}
                    {!! Form::select('status', 'Status')->options([
                        'draft' => 'Brouillon',
                        'finalized' => 'Finalisé',
                        'validated' => 'Validé',
                        'incomplete' => 'Incomplet',
                        'masked' => 'Masqué'
                    ]) !!}
                    {!! Form::hidden('refer_page', $refer_page) !!}
                    <div class="mt-3">
                        {!! Form::submit('Enregistrer') !!}
                        <a class="btn btn-primary" href="{{ $refer_page }}">Annuler</a>
                    </div>
                {!! Form::close() !!}
            </div>
        </div>
        <div class="col-5">
            <div class="card">
                <div class="card-header">Notes aggrégées</div>
                <div class="card-body p-1">
                    @include('projects.rating.show')
                </div>
            </div>

        </div>
    </div>
@endsection