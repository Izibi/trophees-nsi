<a href="#" class="right-link" data-toggle="collapse" data-target="#projects-filter">Filtre</a>

<div class="border-top collapse {{ Request::has('filter') ? 'show' : '' }}" id="projects-filter">
    <div class="mt-3">
        {!! Form::open()->method('GET')->fill(Request::all()) !!}
            @if(Request::has('sort_field'))
                {!! Form::hidden('sort_field') !!}
            @endif
            @if(Request::has('sort_order'))
                {!! Form::hidden('sort_order') !!}
            @endif
            {!! Form::hidden('filter', '1') !!}
            {!! Form::text('filter_id', 'ID de projet') !!}
            {!! Form::text('filter_name', 'Nom') !!}

            @if(Auth::user()->role == 'teacher' || Auth::user()->role == 'admin')
                {!! Form::text('filter_school', 'Établissement') !!}
            @endif
            @if(Auth::user()->role == 'admin')
                {!! Form::text('filter_region', 'Territoire') !!}
                {!! Form::text('filter_user_name', 'Enseignant') !!}
            @endif
            {!! Form::select('filter_status', 'Statut')->options([
                '' => '',
                'draft' => 'Brouillon',
                'finalized' => 'Finalisé',
                'validated' => 'Validé',
                'incomplete' => 'Incomplet',
                'masked' => 'Masqué'
            ]) !!}
            {!! Form::submit('Chercher') !!}
            <a href="/projects" class="btn btn-primary">Reset</a>
        {!! Form::close() !!}
    </div>
</div>