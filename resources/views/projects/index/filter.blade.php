<a href="#" class="float-right" data-toggle="collapse" data-target="#projects-filter">Filter</a>

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
            {!! Form::text('filter_id', 'Project ID') !!}
            {!! Form::text('filter_name', 'Name') !!}            

            @if(Auth::user()->role == 'teacher' || Auth::user()->role == 'admin')
                {!! Form::text('filter_school', 'School') !!}
            @endif
            @if(Auth::user()->role == 'admin')
                {!! Form::text('filter_region', 'Region') !!}
                {!! Form::text('filter_user_name', 'Teacher') !!}
            @endif            
            {!! Form::select('filter_status', 'Status')->options([
                '' => '',
                'draft' => 'Draft',
                'finalized' => 'Finalized',
                'validated' => 'Validated',
                'incomplete' => 'Incomplete',
                'masked' => 'Masked'
            ]) !!}
            {!! Form::submit('Search') !!}
            <a href="/projects" class="btn btn-primary">Reset</a>
        {!! Form::close() !!}
    </div>
</div>