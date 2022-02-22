<a href="#" class="right-link" data-toggle="collapse" data-target="#users-filter">Filtre</a>

<div class="border-top collapse {{ Request::has('filter') ? 'show' : '' }}" id="users-filter">
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
            {!! Form::text('filter_email', 'Email') !!}
            {!! Form::select('filter_role', 'Rôle', [
                '' => '',
                'teacher' => 'Teacher',
                'jury' => 'Jury',
                'admin' => 'Admin',
            ]) !!}
            {!! Form::select('filter_region_id', 'Territoire', ['' => ''] + $regions) !!}
            {!! Form::submit('Chercher') !!}
            <a href="/users" class="btn btn-primary">Reset</a>
        {!! Form::close() !!}
    </div>
</div>