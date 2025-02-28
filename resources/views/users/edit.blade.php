@extends('layout')

@section('content')
    <table class="table table-bordered">
        <tbody>
        <tr>
            <td>Nom</td>
            <td>{{ $user->name }}</td>
        </tr>
        <tr>
            <td>Login</td>
            <td>{{ $user->login }} </td>
        </tr>
        <tr>
            <td>Email professionel</td>
            <td>{{ $user->email }}</td>
        </tr>
        <tr>
            <td>Email secondaire</td>
            <td>{{ $user->secondary_email }}</td>
        </tr>
        </tbody>
    </table>


    <div id="edit-form" class="mt-3">
        {!! Form::open()
            ->route('users.update', ['user' => $user])
            ->fill($user)
            !!}
            {{ method_field('PUT') }}

            {!!Form::select('validated', 'Utilisateur validé',
                [
                    '0' => 'Non',
                    '1' => 'Oui',
                ]
            )!!}

            {!!Form::select('role', 'Rôle',
                [
                    'teacher' => 'Enseignant',
                    'jury' => 'Jury',
                    'admin' => 'Admin',
                ]
            )->attrs(['onchange' => 'updateRoleEditorDisplay()']) !!}

            <div class="roles-teacher">
                {!! Form::select('region_id', 'Territoire', [null => ''] + $regions->pluck('name', 'id')->toArray()) !!}
                {!! Form::select('country_id', 'Pays', [null => ''] + $countries->pluck('name', 'id')->toArray()) !!}
                <label for="cb_coordinator">Coordinateur</label>
                <input type="checkbox" name="cb_coordinator" id="cb_coordinator" value="1" {{ $coordinator ? 'checked' : '' }}>
                <p><i>Note : un enseignant membre du jury doit être marqué comme jury puis avoir le rôle "Enseignant".</i></p>
            </div>

            <div class="roles-editor">
                <label><b>Rôles supplémentaires</b></label><br>
                <div id="roles-list">
                </div>
                <button class="btn btn-primary" type="button" onclick="generateRoleSelector()">Ajouter un rôle</button>
            </div>

        {!! Form::close() !!}

        <div class="mt-5">
            <a class="btn btn-primary" id="btn-ok" href="#">Ok</a>
            <a class="btn btn-primary" href="{{ $refer_page }}">Annuler</a>
            <a class="btn btn-primary" id="btn-delete" href="#">Supprimer l'utilisateur</a>
        </div>
    </div>


    <div class="hidden" id="delete-form">
        {!! Form::open()->route('users.destroy', ['user' => $user]) !!}
        {{ method_field('DELETE') }}
        {!! Form::hidden('refer_page', $refer_page) !!}
        {!! Form::close() !!}
    </div>


    <script>
        function updateRoleEditorDisplay() {
            $('.roles-teacher').toggle($('select[name=role]').val() == 'teacher');
            $('.roles-editor').toggle($('select[name=role]').val() == 'jury');
        }

        function generateRoleSelector(data) {
            var select = $('<div class="form-group form-inline"></div>');
            // Roles have a type and a target_id
            var id = $('<input type="hidden">').attr('name', 'roles_id[]');
            var type = $('<select class="form-control roles-type" name="roles_type[]" onchange="updateRoleSelector($(this).parent(), null)"></select>');
            type.append($('<option value="teacher">Enseignant</option>'));
            type.append($('<option value="coordinator">Coordinateur</option>'));
            type.append($('<option value="territorial">Jury territorial</option>'));
            type.append($('<option value="prize">Jury national</option>'));
            type.append($('<option value="president-territorial">Président territorial</option>'));
            type.append($('<option value="president-prize">Président national</option>'));
            var rem = $('<button class="btn btn-danger" type="button" onclick="$(this).parent().remove()">Retirer</button>');
            select.append(rem);
            select.append(id);
            select.append(type);
            if(data) {
                id.val(data.id);
                type.val(data.type);
            }
            setTimeout(function() {
                updateRoleSelector(select, data && data.target_id);
            }, 10);
            $('#roles-list').append(select);
        }

        function updateRoleSelector(el, val) {
            var descriptions = {
                'teacher': 'Permet au membre du jury de déposer des dossiers.',
                'coordinator': 'Doit aussi être jury territorial pour être coordinateur sur ce territoire.'
            }
            var type = el.find('select.roles-type').first().val();
            el.find('.roles-target').remove();
            if(type == 'territorial' || type == 'president-territorial') {
                var target = $('<select class="form-control inline-block roles-target" name="roles_target[]"></select>');
                for(var i in regions) {
                    target.append($('<option value="' + regions[i].id + '">' + regions[i].name + '</option>'));
                }
            } else if(type == 'prize' || type == 'president-prize') {
                var target = $('<select class="form-control inline-block roles-target" name="roles_target[]"></select>');
                for(var i in prizes) {
                    target.append($('<option value="' + prizes[i].id + '">' + prizes[i].name + '</option>'));
                }
            } else if(descriptions[type]) {
                var target = $('<i class="roles-target">' + descriptions[type] + '</i><input class="roles-target" type="hidden" name="roles_target[]">');
            } else {
                var target = $('<input class="roles-target" type="hidden" name="roles_target[]">');
            }
            el.append(target);
            if(val) {
                el.find('select.roles-target').first().val(val);
            }
        }

        $(document).ready(function() {
            var form = $('#edit-form>form').first();

            $('#btn-ok').click(function(e) {
                e.preventDefault();
                form.submit();
            })

            $('#btn-delete').click(function(e) {
                e.preventDefault();
                if(confirm('Are you sure?')) {
                    var del_form = $('#delete-form>form').first();
                    del_form.submit();
                }
            });

            window.regions = {!! json_encode($regions) !!};
            window.countries = {!! json_encode($countries) !!};
            window.prizes = {!! json_encode($prizes) !!};

            window.cur_roles = {!! json_encode($user->roles) !!};
            for(var i in window.cur_roles) {
                generateRoleSelector(cur_roles[i]);
            }
            updateRoleEditorDisplay();

            RegionSelector($('#edit-form'));
        });
    </script>
@endsection
