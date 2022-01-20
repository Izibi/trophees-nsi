@extends('layout')

@section('content')

<h1>Modifier un concours</h1>

    <div id="edit-form">
        {!! Form::open()
            ->route($contest ? 'contests.update' : 'contests.store', ['contest' => $contest])
            ->fill($contest)
            !!}
            {{ $contest ? method_field('PUT') : '' }}
            {!! Form::text('name', 'Nom') !!}
            {!! Form::text('year', 'Année') !!}
            {!! Form::textarea('message', 'Message')->attrs(['style' => 'height: 500px']) !!}
            {!! Form::select('status', 'Statut', trans('contest_status')) !!}
        {!! Form::close() !!}
        <div class="mt-5">
            <a class="btn btn-primary" id="btn-ok" href="#">Ok</a>
            <a class="btn btn-primary" href="{{ $refer_page }}">Annuler</a>
            @if($contest)
                <a class="btn btn-primary" id="btn-delete" href="#">Supprimer</a>
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
                if(confirm('Cette action supprimera toutes les données liées à ce concours. Êtes-vous sûr ?')) {
                    var del_form = $('#delete-form>form').first();
                    del_form.submit();
                }
            });


            var locale = '{!! app()->getLocale() !!}';
            tinymce.init({
                selector: '#inp-message',
                language: locale,
                skin: false,
                content_css: false,
                plugins: 'code link lists table',
                toolbar: 'undo redo | styleselect | bold italic | alignleft aligncenter alignright alignjustify | outdent indent | bullist numlist | table | fontsizeselect'
            })
        });
    </script>
@endsection