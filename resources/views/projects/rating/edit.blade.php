<div id="rating-form">
    {!! Form::open()->route('projects.set_rating', ['project' => $project])->fill($rating)->attrs(['class' => 'form-compact']) !!}

	{!! Form::text('score_idea', 'Présentation du projet = 30 points')
            ->type('number')->min(0)->max(30)
	    ->wrapperAttrs(['class' => 'form-group-compact'])
            ->help('Projet inscrit dans le programme NSI (première ou terminale).<br>Idée globale - originalité et créativité.<br>Organisation du travail : composition de l\'équipe, rôles, et répartition des tâches.<br>Ouverture : idées d\'amélioration, pistes de développement, analyse critique.<br>Respect des consignes.') !!}
        {!! Form::text('score_operationality', 'Fonctionnement et opérationnalité = 40 points')
            ->type('number')->min(0)->max(40)
            ->wrapperAttrs(['class' => 'form-group-compact'])
            ->options(Rating::rangeOptions(40))->help('Qualité et structure du code.<br>Reproductibilité de la démonstration.<br>Test et validation - correction des bugs.') !!}
        {!! Form::text('score_communication', 'Communication et qualité du dossier = 30 points')
            ->type('number')->min(0)->max(30)
            ->wrapperAttrs(['class' => 'form-group-compact'])
            ->options(Rating::rangeOptions(30))->help('Présentation écrite.<br>Présentation orale.<br>Démonstration du projet.') !!}
        <div class="mt-3">
            Total : <strong><span id="rating-total">--</span> sur <span id="rating-max">--</span></strong>
        </div>
        <div class="mt-3">
            À envisager pour un prix spécial :
            <input type="hidden" name="cb_award_heart" value="0"/>
            {!! Form::checkbox('cb_award_heart', 'Prix spécial du jury')->checked($rating ? $rating->award_heart : false) !!}
            <input type="hidden" name="cb_award_originality" value="0"/>
            {!! Form::checkbox('cb_award_originality', 'Prix de la créativité')->checked($rating ? $rating->award_originality : false) !!}
            <input type="hidden" name="cb_award_engineering" value="0"/>
            {!! Form::checkbox('cb_award_engineering', 'Prix de l’ingénierie')->checked($rating ? $rating->award_engineering : false) !!}
            <input type="hidden" name="cb_award_citizenship" value="0"/>
            {!! Form::checkbox('cb_award_citizenship', 'Prix de l’initiative citoyenne')->checked($rating ? $rating->award_citizenship : false) !!}
        </div>

        <div class="mt-3">
            {!! Form::textarea('notes', 'Notes') !!}
        </div>

        @if($contest->status == 'grading' || $contest->status == 'deliberating')
            <div class="mt-3">
                @if(!$rating || !$rating->published)
                    <div class="mb-2">
                        {!! Form::submit('Enregistrer le brouillon') !!}
                    </div>
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
        var sels = $('#rating-form input');
        function refreshTotal() {
            var total = 0;
	    var max = 0;
            sels.each(function() {
		var el = $(this);
                // max += parseInt(el.find('option:last').val(), 10) || 0;
                total += parseInt(el.val(), 10) || 0;
            })
            $('#rating-total').text(total);
            $('#rating-max').text(100);
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
