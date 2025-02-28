<table class="table table-striped table-borderless active-table">
    <thead>
        <tr>
            <th class="col-1">{!! SortableTable::th('id', 'ID') !!}</th>
            <th class="col-5">{!! SortableTable::th('name', 'Nom') !!}</th>
            <th class="col-3">{!! SortableTable::th('school_name', 'Établissement') !!}</th>
            <th class="col-2">{!! SortableTable::th('created_at', 'Date de soumission') !!}</th>
            <th class="col-1">{!! SortableTable::th('status', 'Statut') !!}</th>
        </tr>
    </thead>
    @foreach ($rows as $project)
        <tr data-row-id="{{ $project->id }}" data-redirect-url="{{ $project->view_url }}"
            @if($project->status == 'incomplete') class="row-alert" @endif
            @if($project->status != 'draft' && $project->status != 'incomplete') data-actions-disabled="edit" @endif
            @if($project->status == 'draft' && $contest->status != 'open') data-actions-disabled="edit" @endif
            @if($project->status == 'incomplet' && $contest->status != 'open' && $contest->status != 'instruction') data-actions-disabled="edit" @endif
            >
            <td>{{ $project->id }}</td>
            <td>{{ $project->name }}</td>
            <td>{{ $project->school->name }}</td>
            <td>{{ $project->created_at }}</td>
            <td>@lang('project_status.'.$project->status)</td>
        </tr>
    @endforeach
</table>
