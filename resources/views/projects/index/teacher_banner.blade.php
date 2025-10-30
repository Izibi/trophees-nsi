@include('common.school-popup')

@if($user->schools()->count() == 0)
<div id="teacher-schools-banner" class="alert alert-warning">
    <p>Vous n'avez pas encore renseigné votre établissement scolaire.</p>
    <p><a href="#" id="btn-open-schools-manager" class="btn btn-primary">Renseigner mon établissement scolaire</a></p>
</div>
<div id="teacher-schools-success" class="alert alert-success" style="display: none;">
    <p>Merci d'avoir renseigné votre établissement scolaire !</p>
    <p>Vous pouvez modifier cette information de nouveau sur la page de dépôt de projets.</p>
</div>
@endif

<script>
$(document).ready(function() {
    // schools popup
    var schools_manager = SchoolsManager();

    $('#btn-open-schools-manager').on('click', function() {
        schools_manager.show();
    });
});
</script>


<div id="teacher-estimate-banner" class="{{ is_null($user->estimated) || $needs_estimated ? 'alert alert-warning' : '' }}">
    @if(is_null($user->estimated) || $needs_estimated)
    <p>Afin de préparer le concours dans les meilleures conditions, veuillez indiquer une estimation du nombre de projets que vous pensez soumettre cette année.</p>

        @if($needs_estimated)
        <p><b>Cela fait plus d'un mois que vous n'avez pas mis à jour cette information. Merci de bien vouloir reconfirmer.</b></p>
        @endif
    @endif

    <form id="teacher-estimate-form" class="mt-3">
        @csrf
        <div class="row align-items-center">
            <div class="col-auto">
                <label for="estimated-projects" class="col-form-label">Je prévois de soumettre environ</label>
            </div>
            <div class="col-auto">
                <input type="number" id="estimated-projects" name="estimated" class="form-control" 
                       min="0" max="100" value="{{ $user->estimated ?? '' }}" required>
            </div>
            <div class="col-auto">
                <label for="estimated-projects" class="col-form-label">projets.</label>
            </div>
            <div class="col-auto">
                <button type="submit" class="btn btn-primary">Enregistrer</button>
            </div>
        </div>
    </form>
</div>

<div id="teacher-estimate-success" class="alert alert-success" style="display: none;">
    <p>Merci d'avoir fourni cette information !</p>
</div>
