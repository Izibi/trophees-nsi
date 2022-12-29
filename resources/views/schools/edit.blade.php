@extends('layout')

@section('content')

<h1>Edit school</h1>

    <div id="edit-form">
        {!! Form::open()
            ->route('schools.update', ['school' => $school])
            ->fill($school)
            !!}
            {{ method_field('PUT') }}

            {!! Form::text('name', 'Nom') !!}

            {!! Form::text('address', 'Adresse') !!}
            {!! Form::text('city', 'Ville') !!}
            {!! Form::text('zip', 'Fichier zip') !!}

            {!! Form::select('region_id', 'Rétion', $regions) !!}
            {!! Form::select('country_id', 'Pays', [null => ''] + $countries->pluck('name', 'id')->toArray()) !!}
            {!! Form::text('uai', 'UAI') !!}

            {!! Form::select('academy_id', 'Choisir une académie', [null => ''] + $academies->pluck('name', 'id')->toArray()) !!}
        {!! Form::close() !!}
        <div class="mt-5">
            <a class="btn btn-primary" id="btn-ok" href="#">Ok</a>
            <a class="btn btn-primary" href="{{ $refer_page }}">Annuler</a>
            <a class="btn btn-primary" id="btn-delete" href="#">Supprimer</a>
        </div>
    </div>


    <div class="hidden" id="delete-form">
        {!! Form::open()->route('schools.destroy', ['school' => $school]) !!}
        {{ method_field('DELETE') }}
        {!! Form::hidden('refer_page', $refer_page) !!}
        {!! Form::close() !!}
    </div>

    <script>
        window.regions = {!! json_encode($regions) !!}
        window.countries = {!! json_encode($countries) !!}
        window.academies = {!! json_encode($academies) !!}


        $(document).ready(function() {
            var form = $('#edit-form>form').first();

            $('#btn-ok').click(function(e) {
                e.preventDefault();
                form.submit();
            })

            $('#btn-delete').click(function(e) {
                e.preventDefault();
                if(confirm('Cette action va supprimer toutes les données associées à cet établissement. Êtes-vous sûr ?')) {
                    var del_form = $('#delete-form>form').first();
                    del_form.submit();
                }
            });
            RegionSelector($('#edit-form'));
        });
    </script>
@endsection