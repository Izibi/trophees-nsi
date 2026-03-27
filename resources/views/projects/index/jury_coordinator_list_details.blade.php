<table class="table table-striped table-borderless active-table">
    <thead>
        <tr>
            <th class="col-1">{!! SortableTable::th('id', 'ID') !!}</th>
            <th class="col-7">{!! SortableTable::th('name', 'Nom') !!}</th>
            <th class="col-3">{!! SortableTable::th('school_name', 'Établissement') !!}</th>
            <th class="col-2">{!! SortableTable::th('created_at', 'Date de soumission') !!}</th>
            <th class="col-1">{!! SortableTable::th('status', 'Statut') !!}</th>
            @if($view['rate'])
            <th class="col-2">{!! SortableTable::th('rating_published', 'Ma note') !!}</th>
            @endif
        </tr>
    </thead>
    @foreach ($rows as $project)
        <tr data-row-id="{{ $project->id }}" data-redirect-url="{{ $project->view_url }}"
            @if($project->status == 'incomplete') class="row-alert" @endif
            @if($project->status != 'draft' && $project->status != 'incomplete') data-actions-disabled="edit" @endif
            @if($project->status == 'draft' && $contest->status != 'open') data-actions-disabled="edit" @endif
            @if($project->status == 'incomplete' && $contest->status != 'open' && $contest->status != 'instruction') data-actions-disabled="edit" @endif
            >
            <td>{{ $project->id }}</td>
            <td>{{ $project->name }}</td>
            <td>{{ $project->school ? $project->school->name : '' }}</td>
            <td>{{ $project->created_at }}</td>
            <td>@lang('project_status.'.$project->status)</td>
            @if($view['rate'])
            <td>
                @if($rating = $project->getUserRating($user))
                    {{ $rating->published == 1 ? 'Publiée' : 'Brouillon' }}
                @else
                    Non évalué
                @endif
            </td>
            @endif
        </tr>
    @endforeach
</table>
