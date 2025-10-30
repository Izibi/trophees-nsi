@extends('layout')

@section('content')

<h1>Modifier l'établissement</h1>

<p>ID : {{ $school->id }}</p>

    <div id="edit-form">
        {!! Form::open()
            ->route('schools.update', ['school' => $school])
            ->fill($school)
            !!}
            {{ method_field('PUT') }}

            {!! Form::text('name', 'Nom') !!}

            {!! Form::text('address', 'Adresse') !!}
            {!! Form::text('city', 'Ville') !!}
            {!! Form::text('zip', 'Code postal') !!}

            {!! Form::select('region_id', 'Région', $regions) !!}
            {!! Form::select('country_id', 'Pays', [null => ''] + $countries->pluck('name', 'id')->toArray()) !!}
            {!! Form::text('uai', 'UAI') !!}

            {!! Form::select('academy_id', 'Choisir une académie', [null => ''] + $academies->pluck('name', 'id')->toArray()) !!}
        {!! Form::close() !!}
        <div class="mt-5">
            <a class="btn btn-primary" id="btn-ok" href="#">
                Sauvegarder
                @if($school->verified == 0)
                    et marquer vérifié
                @endif
            </a>
            <a class="btn btn-primary" href="{{ $refer_page }}">Annuler</a>
            <a class="btn btn-primary" id="btn-delete" href="#">Supprimer</a>
        </div>
        <hr>
        <div>
            <h3>Fusionner avec un autre établissement</h3>
            <p>L'établissemment courant sera supprimé, et toutes les données associées seront transférées vers l'établissement sélectionné ci-dessous.</p>
            {!! Form::open()
                ->route('schools.merge', ['school' => $school])
                ->fill([]) !!}
                {!! Form::select('merge_school_id', 'Choisir un établissement', ['' => ''] + $other_schools) !!}
                {!! Form::hidden('refer_page', $refer_page) !!}
                {!! Form::submit('Fusionner les établissements') !!}
            {!! Form::close() !!}
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