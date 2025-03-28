<table class="table table-striped table-borderless active-table table-vertical-headers">
    <thead>
        <tr>
            <th rowspan="2" style="vertical-align: middle;">{!! SortableTable::th('name', 'Nom') !!}</th>
            <th rowspan="2" style="vertical-align: middle;">{!! SortableTable::th('ratings_amount', 'Nombre de notes') !!}</th>
            <th colspan="4">Scores</th>
<!--            <th colspan="3">Récompenses suggérées</th>-->
        </tr>
        <tr>
            <th>{!! SortableTable::th('score_operationality', 'C. Techniques') !!}</th>
            <th>{!! SortableTable::th('score_communication', 'C. Non Techniques') !!}</th>
            <th><strong>{!! SortableTable::th('score_total', 'Total') !!}</strong></th>
<!--            <th>{!! SortableTable::th('award_engineering', 'Thématique') !!}</th>
            <th>{!! SortableTable::th('award_heart', 'Spécial du jury') !!}</th>
            <th>{!! SortableTable::th('award_originality', 'Originalité') !!}</th>-->
        </tr>
    </thead>
    @foreach ($rows as $project)
        <tr data-row-id="{{ $project->id }}" data-redirect-url="{{ $project->view_url }}">
            <td>{{ $project->name }}</td>
            <td><strong>{{ $project->ratings_amount }}</strong></td>
            <td>{{ $project->score_operationality }}</td>
            <td>{{ $project->score_communication }}</td>
            <td><strong>{{ $project->score_total }}</strong></td>
<!--            <td>{{ $project->award_engineering }}</td>
            <td>{{ $project->award_heart }}</td>
            <td>{{ $project->award_originality }}</td>-->
        </tr>
    @endforeach
</table>
