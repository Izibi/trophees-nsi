@if(is_null($project->score_total))
    This project was not rated.
@else
    <table class="table table-striped table-borderless">
        <tbody>
            <tr>
                <td class="col-10">Idée globale</td>
                <td class="col-2">{{ $project->score_idea }}</td>
            </tr>
            <tr>
                <td>Comunication</td>
                <td>{{ $project->score_communication }}</td>
            </tr>
            <tr>
                <td>Présentation orale</td>
                <td>{{ $project->score_presentation }}</td>
            </tr>
            <tr>
                <td>Image</td>
                <td>{{ $project->score_image }}</td>
            </tr>
            <tr>
                <td>Critère de logique</td>
                <td>{{ $project->score_logic }}</td>
            </tr>
            <tr>
                <td>Originalité et créativité</td>
                <td>{{ $project->score_creativity }}</td>
            </tr>
            <tr>
                <td>Organisation</td>
                <td>{{ $project->score_organisation }}</td>
            </tr>
            <tr>
                <td>Fonctionnement et opérationnalité</td>
                <td>{{ $project->score_operationality }}</td>
            </tr>
            <tr>
                <td>Ouverture</td>
                <td>{{ $project->score_ouverture }}</td>
            </tr>
            <tr>
                <td><strong>Total</strong></td>
                <td><strong>{{ $project->score_total }}</strong></td>
            </tr>
        </tbody>
    </table>


    <table class="table table-striped table-borderless mt-3">
        <tbody>
            <tr></tr>
            <tr>
                <td colspan="2"><strong>Project awards</strong></td>
            </tr>
            <tr>
                <td class="col-10">Mixité</td>
                <td class="col-2">{{ $project->award_mixed }}</td>
            </tr>
            <tr>
                <td>Citoyenneté</td>
                <td>{{ $project->award_citizenship }}</td>
            </tr>
            <tr>
                <td>Ingénierie</td>
                <td>{{ $project->award_engineering }}</td>
            </tr>
            <tr>
                <td>Coup de coeur</td>
                <td>{{ $project->award_heart }}</td>
            </tr>
            <tr>
                <td>Originalité</td>
                <td>{{ $project->award_originality }}</td>
            </tr>
        </tbody>
    </table>
@endif