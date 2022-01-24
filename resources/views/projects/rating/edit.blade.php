<div id="rating-form">
    {!! Form::open()->route('projects.set_rating', ['project' => $project])->fill($rating)->attrs(['class' => 'form-compact']) !!}

        {!! Form::select('score_idea', 'Idée globale')
            ->wrapperAttrs(['class' => 'form-group-compact'])
            ->options(Rating::rangeOptions(5))->help('Inscrite dans le programme NSI') !!}
        {!! Form::select('score_communication', 'Comunication')
            ->wrapperAttrs(['class' => 'form-group-compact'])
            ->options(Rating::rangeOptions(5))->help('Documentation et lisibilité du programme présenté') !!}
        {!! Form::select('score_presentation', 'Présentation orale')
            ->wrapperAttrs(['class' => 'form-group-compact'])
            ->options(Rating::rangeOptions(5))->help('Vidéo') !!}
        {!! Form::select('score_image', 'Image')
            ->wrapperAttrs(['class' => 'form-group-compact'])
            ->options(Rating::rangeOptions(2)) !!}
        {!! Form::select('score_logic', 'Critère de logique')
            ->wrapperAttrs(['class' => 'form-group-compact'])
            ->options(Rating::rangeOptions(5))->help('Présentation des étapes du projet / planification') !!}
        {!! Form::select('score_creativity', 'Originalité et créativité')
            ->wrapperAttrs(['class' => 'form-group-compact'])
            ->options(Rating::rangeOptions(5)) !!}
        {!! Form::select('score_organisation', 'Organisation')
            ->wrapperAttrs(['class' => 'form-group-compact'])
            ->options(Rating::rangeOptions(5))->help('Composition de l\'équipe/tâche/répartition<br>Dont 2 points pour la mixité dans l\'équipe') !!}
        {!! Form::select('score_operationality', 'Fonctionnement et opérationnalité')
            ->wrapperAttrs(['class' => 'form-group-compact'])
            ->options(Rating::rangeOptions(5)) !!}
        {!! Form::select('score_ouverture', 'Ouverture')
            ->wrapperAttrs(['class' => 'form-group-compact'])
            ->options(Rating::rangeOptions(3))->help('Idées d\'améliorations, de diffusion et pistes de développement') !!}
        <div class="mt-3">
            Total : <strong><span id="rating-total">--</span> sur <span id="rating-max">--</span></strong>
        </div>
        <div class="mt-3">
            À envisager pour un prix spécial :
            <div class="row">
                <div class="col-6">
                    <input type="hidden" name="cb_award_mixed" value="0"/>
                    {!! Form::checkbox('cb_award_mixed', 'Mixité')->checked($rating ? $rating->award_mixed : false) !!}
                </div>
                <div class="col-6">
                    <input type="hidden" name="cb_award_citizenship" value="0"/>
                    {!! Form::checkbox('cb_award_citizenship', 'Citoyenneté')->checked($rating ? $rating->award_citizenship : false) !!}
                </div>
                <div class="col-6">
                    <input type="hidden" name="cb_award_engineering" value="0"/>
                    {!! Form::checkbox('cb_award_engineering', 'Ingénierie')->checked($rating ? $rating->award_engineering : false) !!}
                </div>
                <div class="col-6">
                    <input type="hidden" name="cb_award_heart" value="0"/>
                    {!! Form::checkbox('cb_award_heart', 'Coup de coeur')->checked($rating ? $rating->award_heart : false) !!}
                </div>
                <div class="col-6">
                    <input type="hidden" name="cb_award_originality" value="0"/>
                    {!! Form::checkbox('cb_award_originality', 'Originalité')->checked($rating ? $rating->award_originality : false) !!}
                </div>
            </div>
        </div>

        <div class="mt-3">
            {!! Form::textarea('notes', 'Notes') !!}
        </div>

        @if($contest->status == 'grading' || $contest->status == 'deliberating')
            <div class="mt-3">
                @if(!$rating || !$rating->published)
                    {!! Form::submit('Enregistrer le brouillon') !!}
                @endif
                <a class="btn btn-primary" href="#" id="btn-submit-rating">Finaliser</a>
                <a class="btn btn-primary" href="{{ $refer_page }}">Annuler</a>
            </div>
        @else
            <script>
                $('#rating-form select, #rating-form input, #rating-form textarea').prop('disabled', 'disabled');
            </script>

        @endif
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
            })
            $('#rating-total').text(total);
            $('#rating-max').text(max);
        }
        sels.on('change', function() {
            refreshTotal();
        })
        refreshTotal();


        $('#btn-submit-rating').on('click', function(e) {
            e.preventDefault();
            if(confirm('Vous allez soumettre vos notes. Continuer ?')) {
                var form = $('#rating-form form').first();
                form.append('<input type="hidden" name="published" value="1"/>');
                form.submit();
            }
        })
    })
</script>