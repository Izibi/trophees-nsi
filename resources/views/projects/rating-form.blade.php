<h2>My ratings</h2>

<div id="rating-form">
    {!! Form::open()->route('projects.set_rating', ['project' => $project])->fill($rating) !!}

        {!! Form::select('score_idea', 'Idée globale')->options(Rating::rangeOptions(5))->help('Inscrite dans le programme NSI') !!}
        {!! Form::select('score_communication', 'Comunication')->options(Rating::rangeOptions(5))->help('Documentation et lisibilité du programme présenté') !!}
        {!! Form::select('score_presentation', 'Présentation orale')->options(Rating::rangeOptions(5))->help('Vidéo') !!}
        {!! Form::select('score_image', 'Image')->options(Rating::rangeOptions(2)) !!}
        {!! Form::select('score_logic', 'Critère de logique')->options(Rating::rangeOptions(5))->help('Présentation des étapes du projet / planification') !!}
        {!! Form::select('score_creativity', 'Originalité et créativité')->options(Rating::rangeOptions(5)) !!}
        {!! Form::select('score_organisation', 'Organisation')->options(Rating::rangeOptions(5))->help('Composition de l\'équipe/tâche/répartition<br>Dont 2 points pour la mixité dans l\'équipe') !!}
        {!! Form::select('score_operationality', 'Fonctionnement et opérationnalité')->options(Rating::rangeOptions(5)) !!}
        {!! Form::select('score_ouverture', 'Ouverture')->options(Rating::rangeOptions(3))->help('Idées d\'améliorations, de diffusion et pistes de développement') !!}


        <div class="mt-3">
            Total: <span id="rating-total">--</span> of <span id="rating-max">--</span>
        </div>
        <div class="mt-3">
            {!! Form::submit('Submit') !!}
        </div>
    {!! Form::close() !!}   
</div>


<script>
    $(document).ready(function() {
        var sels = $('#rating-form select');
        function refreshTotal() {
            var total = 0;
            var max = 0;
            sels.each(function() {
                var el = $(this);
                max += parseInt(el.find('option:last').val(), 10) || 0;
                total += parseInt(el.val(), 10) || 0;
                console.log(el.find('option:last').val())
            })
            $('#rating-total').text(total);
            $('#rating-max').text(max);
        }
        sels.on('change', function() {
            refreshTotal();    
        })
        refreshTotal();
    })
</script>