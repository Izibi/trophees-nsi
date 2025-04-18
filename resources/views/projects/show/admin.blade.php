@extends('layout')

@section('content')
    <div class="row">
        <div class="col-7">
            @include('projects.details')

            <div class="mt-5">
                {!! Form::open()->route('projects.set_status', ['project' => $project])->fill($project) !!}
                    {!! Form::select('status', 'Statut')->options(trans('project_status')) !!}
                    {!! Form::textarea('message', 'Message')->help('Message pour un projet incomplet (le message n\'est envoyé que lors d\'un changement de statut)') !!}
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
    <script type="text/javascript">
        function displayMessage() {
            var status = $('select[name=status]').val();
            if (status == 'incomplete') {
                $('textarea[name=message]').parent().show();
            } else {
                $('textarea[name=message]').parent().hide();
            }
        }

        $(document).ready(function() {
            $('select[name=status]').change(displayMessage);
            displayMessage();
        });
    </script>
@endsection