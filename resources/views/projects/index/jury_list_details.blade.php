<table class="table table-striped table-borderless active-table">
    <thead>
        <tr>
            <th class="col-1">{!! SortableTable::th('id', 'ID') !!}</th>
            <th class="col-7">{!! SortableTable::th('name', 'Nom') !!}</th>
            <th class="col-2">{!! SortableTable::th('created_at', 'Date de soumission') !!}</th>
            <th class="col-2">{!! SortableTable::th('rating_published', 'Ma note') !!}</th>
        </tr>
    </thead>
    @foreach ($rows as $project)
        <tr data-row-id="{{ $project->id }}" @if($project->status != 'draft') data-actions-disabled="edit" @endif data-redirect-url="{{ $project->view_url }}">
            <td>{{ $project->id }}</td>
            <td>{{ $project->name }}</td>
            <td>{{ $project->created_at }}</td>
            <td>
                @if(!is_null($project->rating_published))
                    {{ $project->rating_published ? 'Publiée' : 'Brouillon' }}
                @else
                    Non évalué
                @endif
            </td>
        </tr>
    @endforeach
</table>
