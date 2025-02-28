<div id="rating-form">
    {!! Form::open()->route('projects.set_rating', ['project' => $project])->fill($rating)->attrs(['class' => 'form-compact']) !!}

    	{!! Form::text('score_operationality', 'Compétences Techniques = 50 points')
            ->type('number')->min(0)->max(50)
	        ->wrapperAttrs(['class' => 'form-group-compact rating-input'])
            ->options(Rating::rangeOptions(50))
	    ->help('<b>Respect du programme de NSI</b><br><br><b>Programmation et Développement</b><br>Maîtrise des langages de programmation (Python)<br>Utilisation limitée des frameworks et bibliothèques<br>Qualité et lisibilité du code<br>Reproductibilité de la démonstration<br><br><b>Algorithmique et Structures de Données</b><br>Conception et implémentation d’algorithmes efficaces<br>Utilisation appropriée des structures de données<br><br><b>Documentation technique</b><br>Qualité et complétude de la documentation du projet<br>Citation des sources externes, transparence sur l’usage éventuel d’IA<br>Guide d’utilisation<br>Facilité de compréhension et d’utilisation') !!}
        {!! Form::text('score_communication', 'Compétences Non Techniques = 50 points')
            ->type('number')->min(0)->max(50)
            ->wrapperAttrs(['class' => 'form-group-compact rating-input'])
            ->options(Rating::rangeOptions(50))->help('<b>Travail en Équipe</b><br>Planification et organisation du travail<br>Collaboration et communication avec les membres de l’équipe<br>Gestion des conflits et prise de décisions collectives<br><br><b>Analyse critique du projet</b><br>Savoir identifier et résoudre les difficultés<br><br><b>Créativité et Innovation</b><br>Originalité des idées et des solutions proposées<br>Capacité à innover et à proposer des améliorations<br><br><b>Présentation du projet</b><br>Clarté et efficacité de la présentation<br>Capacité à expliquer les concepts<br>Pertinence de la démonstration vidéo') !!}
        <div class="mt-3">
            Total : <strong><span id="rating-total">--</span> sur <span id="rating-max">--</span></strong>
        </div>
<!--        <div class="mt-3">
            À envisager pour un prix spécial :
            <input type="hidden" name="cb_award_engineering" value="0"/>
            {!! Form::checkbox('cb_award_engineering', 'Prix thématique : Le sport')->checked($rating ? $rating->award_engineering : false) !!}
            <input type="hidden" name="cb_award_heart" value="0"/>
            {!! Form::checkbox('cb_award_heart', 'Prix spécial du jury')->checked($rating ? $rating->award_heart : false) !!}
            <input type="hidden" name="cb_award_originality" value="0"/>
            {!! Form::checkbox('cb_award_originality', 'Prix de la créativité')->checked($rating ? $rating->award_originality : false) !!}
        </div>-->

        <div class="mt-3">
            {!! Form::textarea('notes', 'Notes') !!}
        </div>

        @if($can_rate)
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
        var sels = $('#rating-form .rating-input input');
        function refreshTotal() {
            var total = 0;
            var max = 0;
            sels.each(function() {
                var el = $(this);
                // max += parseInt(el.find('option:last').val(), 10) || 0;
                total += parseInt(el.val(), 10) || 0;
            });
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
