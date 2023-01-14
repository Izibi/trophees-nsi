<div class="row team-member-row">
    <div class="col-1">
        <a href="#" class="btn-remove-member" title="Remove team member">&times;</a>
    </div>
    <input type="hidden" name="team_member_id[]" value="{{ $member ? $member->id : '' }}"/>
    <div class="col-2">
        {!! Form::text(
            'team_member_first_name[]',
            null,
            $member ? $member->first_name : ''
        )->wrapperAttrs(['class' => 'mb-0']) !!}
    </div>
    <div class="col-2">
        {!! Form::text(
            'team_member_last_name[]',
            null,
            $member ? $member->last_name : ''
        )->wrapperAttrs(['class' => 'mb-0']) !!}
    </div>
    <div class="col-2">
        {!! Form::select(
            'team_member_gender[]',
            false,
            [
                null => '',
                'male' => 'Masculin',
		'female' => 'Féminin',
		'other' => 'Non renseigné'
            ],
            $member ? $member->gender : ''
        ) !!}
    </div>
    @include('projects.edit.file-input', [
        'title' => false,
        'description' => false,
        'extensions' => '.pdf',
        'key' => 'team_member_parental_permissions_file[]',
        'file' => $member ? $member->parental_permissions_file : null,
        'class' => 'col-5 file-box'
    ])

</div>
