<table class="table table-striped table-borderless active-table table-vertical-headers">
    <thead>
        <tr>
            <th rowspan="2" style="vertical-align: middle;">{!! SortableTable::th('name', 'Nom') !!}</th>
	    <th rowspan="2" style="vertical-align: middle;">{!! SortableTable::th('ratings_amount', 'Nombre de notes') !!}</th>
            <th colspan="10">Scores</th>
            <th colspan="5">Récompenses suggérées</th>
        </tr>
        <tr>
            <th>{!! SortableTable::th('score_idea', 'Idée') !!}</th>
            <th>{!! SortableTable::th('score_communication', 'Communication') !!}</th>
            <th>{!! SortableTable::th('score_presentation', 'Presentation') !!}</th>
            <th>{!! SortableTable::th('score_image', 'Image') !!}</th>
            <th>{!! SortableTable::th('score_logic', 'Logique') !!}</th>
            <th>{!! SortableTable::th('score_creativity', 'Créativité') !!}</th>
            <th>{!! SortableTable::th('score_organisation', 'Organisation') !!}</th>
            <th>{!! SortableTable::th('score_operationality', 'Opérationnalité') !!}</th>
            <th>{!! SortableTable::th('score_ouverture', 'Ouverture') !!}</th>
            <th>{!! SortableTable::th('score_total', 'Total') !!}</th>
            <th>{!! SortableTable::th('award_mixed', 'Mixité') !!}</th>
            <th>{!! SortableTable::th('award_citizenship', 'Citoyenneté') !!}</th>
            <th>{!! SortableTable::th('award_engineering', 'Ingénierie') !!}</th>
            <th>{!! SortableTable::th('award_heart', 'Coup de coeur') !!}</th>
            <th>{!! SortableTable::th('award_originality', 'Originalité') !!}</th>
        </tr>
    </thead>
    @foreach ($rows as $project)
        <tr data-row-id="{{ $project->id }}" @if($project->status != 'draft') data-actions-disabled="edit" @endif data-redirect-url="{{ $project->view_url }}">
            <td>{{ $project->name }}</td>
            <td><strong>{{ $project->ratings_amount }}</strong></td>
            <td>{{ $project->score_idea }}</td>
            <td>{{ $project->score_communication }}</td>
            <td>{{ $project->score_presentation }}</td>
            <td>{{ $project->score_image }}</td>
            <td>{{ $project->score_logic }}</td>
            <td>{{ $project->score_creativity }}</td>
            <td>{{ $project->score_organisation }}</td>
            <td>{{ $project->score_operationality }}</td>
            <td>{{ $project->score_ouverture }}</td>
            <td><strong>{{ $project->score_total }}</strong></td>
            <td>{{ $project->award_mixed }}</td>
            <td>{{ $project->award_citizenship }}</td>
            <td>{{ $project->award_engineering }}</td>
            <td>{{ $project->award_heart }}</td>
            <td>{{ $project->award_originality }}</td>
        </tr>
    @endforeach
</table>
