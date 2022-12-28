<div class="row mb-4 team-member-row">
    <input type="hidden" name="team_member_id[]" value="{{ $member ? $member->id : '' }}"/>
    <div class="col-md-2 col-sm-6">
        {!! Form::text(
            'team_member_first_name[]',
            'First name',
            $member ? $member->first_name : ''
        )->wrapperAttrs(['class' => 'mb-0']) !!}
    </div>
    <div class="col-md-2 col-sm-6">
        {!! Form::text(
            'team_member_last_name[]',
            'Last name',
            $member ? $member->last_name : ''
        )->wrapperAttrs(['class' => 'mb-0']) !!}
    </div>
    <div class="col-md-2 col-sm-6">
        {!! Form::select(
            'team_member_gender[]',
            'Gender',
            [
                null => '',
                'male' => 'Male',
                'female' => 'Female'
            ],
            $member ? $member->gender : ''
        ) !!}
    </div>
    @include('projects.edit.file-input', [
        'title' => 'Autorisations parentales',
        'description' => 'Taille maximum : 20Mo. Voir <a href="https://trophees-nsi.fr/preparer-votre-participation" target="_blank">ici</a> pour le contenu demandé dans ce pdf.',
        'extensions' => '.pdf',
        'key' => 'team_member_parental_permissions_file[]',
        'file' => $member ? $member->parental_permissions_file : null,
        'class' => 'col-5 file-box'
    ])

    <div class="col-1">
        <a href="#" class="btn-remove-member" title="Remove team member">&times;</a>
    </div>
</div>