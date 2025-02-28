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
        <tr data-row-id="{{ $project->id }}" data-redirect-url="{{ $project->view_url }}">
            <td>{{ $project->id }}</td>
            <td>{{ $project->name }} <a href="{{ $project->view_url }}" class="new-tab" target="_blank">↗</a></td>
            <td>{{ $project->created_at }}</td>
            <td>
                @if($rating = $project->getUserRating($user))
                    {{ $rating->published == 1 ? 'Publiée' : 'Brouillon' }}
                @else
                    Non évalué
                @endif
            </td>
        </tr>
    @endforeach
</table>
