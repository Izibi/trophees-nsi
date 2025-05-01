<a href="#" class="right-link" data-toggle="collapse" data-target="#schools-filter">Filtre</a>

<div class="border-top collapse {{ Request::has('filter') ? 'show' : '' }}" id="schools-filter">
    <div class="mt-3">
        {!! Form::open()->method('GET')->fill(Request::all()) !!}
            @if(Request::has('sort_field'))
                {!! Form::hidden('sort_field') !!}
            @endif
            @if(Request::has('sort_order'))
                {!! Form::hidden('sort_order') !!}
            @endif
            {!! Form::hidden('filter', '1') !!}
            {!! Form::text('filter_name', 'Nom') !!}
            {!! Form::text('filter_address', 'Adresse') !!}
            {!! Form::text('filter_city', 'Ville') !!}
            {!! Form::text('filter_zip', 'Code postal') !!}
            {!! Form::select('filter_region_id', 'Territoire', ['' => ''] + $regions) !!}
            {!! Form::text('filter_uai', 'UAI') !!}
            {!! Form::select('filter_hidden', 'Caché', [
                '' => '',
                '1' => 'Oui',
                '0' => 'Non'
            ]) !!}
            {!! Form::submit('Chercher') !!}
            <a href="/schools" class="btn btn-primary">Reset</a>
        {!! Form::close() !!}
    </div>
</div>