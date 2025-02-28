@extends('layout')

@section('content')
    <h2>Attribuer un prix</h2>

    <div id="edit-form">
        {!! Form::open()
            ->multipart()
            ->route('awards.update')
            ->fill($award)
            !!}
            
            <p><b>Projet :</b>
                <a href="{{ route('projects.show', ['project' => $project->id]) }}">
                {{ $project->name }}
                </a>
            </p>

            {!! Form::hidden('project_id', $project->id) !!}
            @if($award)
                <p><b>Prix attribué :</b> {{ $award->prize->name }}</p>
                {!! Form::hidden('awardable_id', 0) !!}
            @else
                {!! Form::select('awardable_id', 'Prix à attribuer', [null => ''] + $awardable) !!}
            @endif 
            {!! Form::textarea('comment', 'Commentaire') !!}

            <div class="mt-5">
                <a class="btn btn-primary" id="btn-ok" href="#">
                    @if($award)
                        Modifier le commentaire
                    @else
                        Attribuer le prix
                    @endif                    
                </a>
                <a class="btn btn-primary" href="{{ route('projects.show', ['project' => $project->id]) }}">Annuler</a>
                @if($award)
                <a class="btn btn-primary" id="btn-delete" href="{{ route('awards.delete', ['award' => $award->id]) }}">Supprimer l'attribution de prix</a>
                @endif
            </div>

        {!! Form::close() !!}
    </div>

    <script>
        $(document).ready(function() {
            var form = $('#edit-form>form').first();

            $('#btn-ok').click(function(e) {
                e.preventDefault();
                form.submit();
            })
        });
    </script>
@endsection
