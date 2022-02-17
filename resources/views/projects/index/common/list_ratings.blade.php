<table class="table table-striped table-borderless active-table table-vertical-headers">
    <thead>
        <tr>
            <th>{!! SortableTable::th('name', 'Nom') !!}</th>
            <th>{!! SortableTable::th('ratings_amount', 'Ratings amount') !!}</th>
            <th>{!! SortableTable::th('score_idea', 'Score idea') !!}</th>
            <th>{!! SortableTable::th('score_communication', 'Score  communication') !!}</th>
            <th>{!! SortableTable::th('score_presentation', 'Score presentation') !!}</th>
            <th>{!! SortableTable::th('score_image', 'Score image') !!}</th>
            <th>{!! SortableTable::th('score_logic', 'Score logic') !!}</th>
            <th>{!! SortableTable::th('score_creativity', 'Score creativity') !!}</th>
            <th>{!! SortableTable::th('score_organisation', 'Score organisation') !!}</th>
            <th>{!! SortableTable::th('score_operationality', 'Score operationality') !!}</th>
            <th>{!! SortableTable::th('score_ouverture', 'Score ouverture') !!}</th>
            <th>{!! SortableTable::th('score_total', 'Score total') !!}</th>
            <th>{!! SortableTable::th('award_mixed', 'Awards mixed') !!}</th>
            <th>{!! SortableTable::th('award_citizenship', 'Awards citizenship') !!}</th>
            <th>{!! SortableTable::th('award_engineering', 'Awards engineering') !!}</th>
            <th>{!! SortableTable::th('award_heart', 'Awards heart') !!}</th>
            <th>{!! SortableTable::th('award_originality', 'Awards originality') !!}</th>
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