@if(is_null($project->score_total))
    <div class="mt-3">
        Ce projet n'a pas été noté
    </div>
@else
    <table class="table table-striped table-borderless">
        <tbody>
            <tr>
                <td class="col-10">Compétences Techniques</td>
                <td class="col-2">{{ $project->score_operationality }}</td>
            </tr>
            <tr>
                <td>Compétences Non Techniques</td>
                <td>{{ $project->score_communication }}</td>
            </tr>
            <tr>
                <td><strong>Total</strong></td>
                <td><strong>{{ $project->score_total }}</strong></td>
            </tr>
        </tbody>
    </table>


<!--    <table class="table table-striped table-borderless mt-3">
        <tbody>
            <tr></tr>
            <tr>
                <td colspan="2"><strong>Récompenses suggérées</strong></td>
            </tr>
            <tr>
                <td class="col-10">Thématique : Le Sport</td>
                <td class="col-2">{{ $project->award_engineering }}</td>
            </tr>
            <tr>
                <td class="col-10">Spécial du jury</td>
                <td class="col-2">{{ $project->award_heart }}</td>
            </tr>
            <tr>
                <td>Originalité</td>
                <td>{{ $project->award_originality }}</td>
            </tr>
        </tbody>
    </table>-->
@endif
