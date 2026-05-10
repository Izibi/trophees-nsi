@extends('layout')

@section('content')
    <h2>Attribuer un prix</h2>

    <div id="edit-form">
        {!! Form::open()
            ->multipart()
            ->route('awards.update')
            !!}
            
            <p><b>Projet :</b>
                <a href="{{ route('projects.show', ['project' => $project->id]) }}">
                {{ $project->name }}
                </a>
            </p>

            {!! Form::hidden('project_id', $project->id) !!}
            
            @php
                $currentPrizesList = [];
                if($currentRegularAward) {
                    $currentPrizesList[] = $currentRegularAward->prize->name;
                }
                foreach($currentSpecialAwardIndices as $idx) {
                    if(isset($specialPrizes[$idx])) {
                        $currentPrizesList[] = $specialPrizes[$idx]['prize']->name;
                    }
                }
            @endphp
            
            @if(count($currentPrizesList) > 0)
                <div class="mb-3">
                    <b>Prix actuellement attribués :</b> {{ implode(', ', $currentPrizesList) }}
                </div>
            @endif
            
            @if ($errors->any())
                <div class="alert alert-danger">
                    <ul>
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif
            
            @if(count($regularPrizes) > 0)
                {!! Form::select('awardable_id', 'Prix à attribuer', [null => 'Aucun prix'] + $regularPrizes)->value($currentRegularAwardIndex) !!}
            @endif
            
            @if(count($specialPrizes) > 0)
                <div class="mt-3">
                    <h5>Prix spéciaux</h5>
                    @foreach($specialPrizes as $idx => $specialPrize)
                        <div class="form-check">
                            <input class="form-check-input special-prize-checkbox {{ $specialPrize['prize']->special == 'laureat' ? 'laureat-checkbox' : '' }}" 
                                   type="checkbox" 
                                   name="special_prize_ids[]" 
                                   value="{{ $idx }}" 
                                   id="special_prize_{{ $idx }}"
                                   {{ in_array($idx, $currentSpecialAwardIndices) ? 'checked' : '' }}>
                            <label class="form-check-label" for="special_prize_{{ $idx }}">
                                Nominer pour le {{ $specialPrize['prize']->name }}
                            </label>
                            @if($specialPrize['prize']->special == 'laureat')
                                <small class="form-text text-muted">Le projet doit être lauréat d'un autre prix pour être nominé.</small>
                            @endif
                        </div>
                    @endforeach
                </div>
            @endif
            
            @if($phase != 'deliberating-national')
            <div class="mt-4">
                <h5>Commentaire pour le jury (obligatoire)</h5>
                <small class="form-text text-muted">Visible par les membres du jury uniquement.</small>
                {!! Form::textarea('comment')->value($currentComment) !!}
            </div>
            @endif
            
            <div class="mt-4">
                <h5>Commentaire pour l'équipe lauréate ({{ $phase == 'deliberating-national' ? 'obligatoire' : 'facultatif' }})</h5>
                <small class="form-text text-muted">Visible par le professeur déposant uniquement.</small>
                {!! Form::textarea('comment_team')->value($currentCommentTeam) !!}
            </div>

            <div class="mt-5">
                <a class="btn btn-primary" id="btn-ok" href="#">
                    {{ $currentRegularAward ? 'Modifier le prix' : 'Attribuer le prix' }}
                </a>
                <a class="btn btn-primary" href="{{ route('projects.show', ['project' => $project->id]) }}">Annuler</a>
            </div>

        {!! Form::close() !!}
    </div>

    <script>
        $(document).ready(function() {
            var form = $('#edit-form>form').first();
            var regularPrizeSelect = $('select[name="awardable_id"]');
            var laureatCheckboxes = $('.laureat-checkbox');
            var commentField = $('textarea[name="comment"]');
            var commentTeamField = $('textarea[name="comment_team"]');
            var isNationalPhase = {{ $phase == 'deliberating-national' ? 'true' : 'false' }};

            function updateLaureatCheckboxes() {
                // Check if a regular prize is selected (not null/empty/Aucun prix)
                var selectedValue = regularPrizeSelect.val();
                var regularPrizeSelected = selectedValue && selectedValue !== '' && selectedValue !== 'Aucun prix';
                
                laureatCheckboxes.each(function() {
                    if (!regularPrizeSelected) {
                        // No regular prize selected: disable and uncheck laureat checkboxes
                        $(this).prop('disabled', true);
                        $(this).prop('checked', false);
                    } else {
                        // Regular prize selected: enable laureat checkboxes
                        $(this).prop('disabled', false);
                    }
                });
                
                // Make appropriate comment field required based on phase
                if (regularPrizeSelected) {
                    if (isNationalPhase) {
                        commentTeamField.prop('required', true);
                    } else {
                        commentField.prop('required', true);
                    }
                } else {
                    commentField.prop('required', false);
                    commentTeamField.prop('required', false);
                }
            }

            // Update on page load
            if (regularPrizeSelect.length > 0) {
                updateLaureatCheckboxes();
                
                // Update when selection changes
                regularPrizeSelect.on('change', updateLaureatCheckboxes);
            }

            $('#btn-ok').click(function(e) {
                e.preventDefault();
                form.submit();
            })
        });
    </script>
@endsection
