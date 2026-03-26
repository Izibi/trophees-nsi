<div id="rating-form">
    {!! Form::open()->route('projects.set_rating', ['project' => $project])->fill($rating)->attrs(['class' => 'form-compact']) !!}

    	{!! Form::text('score_operationality', 'Compétences Techniques = 50 points')
            ->type('number')->min(0)->max(50)
	        ->wrapperAttrs(['class' => 'form-group-compact rating-input'])
            ->options(Rating::rangeOptions(50))
	    ->help('<b>Respect du programme de NSI</b><br>Langages autorisés, au programme de NSI<br>Réinvestissement de notions vues en cours<br>Projet représentatif du programme de NSI<br><br><b>Programmation et Développement</b><br>Maîtrise des langages de programmation (Python)<br>Utilisation réfléchie et maîtrisée des bibliothèques<br>Facilité de lecture du code<br>Degré de création originale<br>Reproductibilité de la démonstration<br><br><b>Algorithmique et Structures de Données</b><br>Conception et implémentation d\'algorithmes efficaces<br>Utilisation appropriée des structures de données<br><br><b>Documentation technique</b><br>Qualité et complétude de la documentation du projet<br>Citation des sources externes<br>Guide d\'utilisation<br>Facilité de compréhension et d\'utilisation') !!}
        <div class="mb-3">
            <input type="hidden" name="cannot_evaluate_technical" id="cannot-evaluate-technical-hidden" value="{{ $rating && $rating->cannot_evaluate_technical ? '1' : '0' }}" />
            <input type="checkbox" id="cannot-evaluate-technical" {{ $rating && $rating->cannot_evaluate_technical ? 'checked' : '' }} />
            <label for="cannot-evaluate-technical">Je ne peux pas évaluer les compétences techniques</label>
        </div>
        {!! Form::text('score_communication', 'Compétences Non Techniques = 50 points')
            ->type('number')->min(0)->max(50)
            ->wrapperAttrs(['class' => 'form-group-compact rating-input'])
            ->options(Rating::rangeOptions(50))->help('<b>Travail en Équipe</b><br>Pertinence de l\'organisation définie dans le groupe<br>Collaboration et communication avec les membres de l\'équipe<br>Prise de décisions collectives<br>Répartition équilibrée et équitable des tâches<br><br><b>Analyse critique du projet</b><br>Identification et degré de résolution des difficultés<br>Justification des solutions ou pistes de solution proposées<br><br><b>Créativité et Innovation</b><br>Pertinence et utilité du projet<br>Originalité des idées et des solutions proposées<br>Capacité à innover et à proposer des améliorations<br><br><b>Usage de l\'IA</b><br>Clarté et pertinence de l\'utilisation<br>Utilisation limitée, réfléchie et justifiée<br>Transparence et proportionnalité de l\'usage<br><br><b>Présentation du projet</b><br>Clarté et efficacité de la présentation<br>Capacité à expliquer les concepts<br>Pertinence de la démonstration vidéo') !!}
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
        var technicalInput = $('input[name="score_operationality"]');
        var cannotEvaluateCheckbox = $('#cannot-evaluate-technical');
        var cannotEvaluateHidden = $('#cannot-evaluate-technical-hidden');
        
        // Initialize based on checkbox state (from database)
        if (cannotEvaluateCheckbox.is(':checked')) {
            technicalInput.val('');
            technicalInput.prop('disabled', true);
        }
        
        // Handle checkbox change
        cannotEvaluateCheckbox.on('change', function() {
            if ($(this).is(':checked')) {
                technicalInput.val('');
                technicalInput.prop('disabled', true);
                cannotEvaluateHidden.val('1');
            } else {
                technicalInput.prop('disabled', false);
                cannotEvaluateHidden.val('0');
            }
            refreshTotal();
        });
        
        function refreshTotal() {
            var total = 0;
            var max = 100;
            
            // If cannot evaluate technical, max is 50, otherwise 100
            if (cannotEvaluateCheckbox.is(':checked')) {
                max = 50;
                // Only count communication score
                var commVal = parseInt($('input[name="score_communication"]').val(), 10) || 0;
                total = commVal;
            } else {
                // Count both scores
                sels.each(function() {
                    var el = $(this);
                    if (!el.prop('disabled')) {
                        total += parseInt(el.val(), 10) || 0;
                    }
                });
            }
            
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
                // If checkbox is checked, ensure the field is enabled before submit so the empty value is sent
                if (cannotEvaluateCheckbox.is(':checked')) {
                    technicalInput.prop('disabled', false);
                }
                var form = $('#rating-form form').first();
                form.append('<input type="hidden" name="published" value="1"/>');
                form.submit();
            }
        })
    })
</script>
