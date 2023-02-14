@if(Auth::user()->coordinator && Auth::user()->role == 'teacher')
    <h2 class="ml-5">
        <div class="btn-group" role="group">
            <a href="/projects" class="btn btn-sm {{ $coordinator ? 'btn-primary' : 'btn-secondary' }}">My projects</a>
            <a href="/projects?coordinator=1" class="btn btn-sm {{ !$coordinator ? 'btn-primary' : 'btn-secondary' }}">Projects in my territory</a>
        </div>
    </h2>
@endif

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
            @if($coordinator)
                {!! Form::hidden('coordinator', 1) !!}
            @endif
            {!! Form::hidden('filter', '1') !!}
            {!! Form::text('filter_id', 'ID de projet') !!}
            {!! Form::text('filter_name', 'Nom') !!}

            @if(Auth::user()->role == 'teacher' || Auth::user()->role == 'admin')
                {!! Form::text('filter_school', 'Établissement') !!}
            @endif
            @if(Auth::user()->role == 'admin')
                {!! Form::select('filter_region_id', 'Territoire')->options(['' => ''] + $regions) !!}
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