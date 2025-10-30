<div class="modal" id="schools-manager-modal">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Établissements</h5>
                <button type="button" class="close" data-dismiss="modal">
                    <span>&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div class="row" id="section-schools-manager">
                    <div class="col-6">
                        <h2>Mes établissements</h2>
                        <div class="list-select" id="schools-my" style="height: 354px"></div>
                        <div class="mt-3">
                            <button class="btn btn-primary" id="btn-school-use">Utiliser cet établissement</button>
                            <button class="btn btn-primary" id="btn-school-delete">Retirer de ma liste</button>
                            <button class="btn btn-default" id="btn-school-close">Fermer</button>
                        </div>
                    </div>
                    <div class="col-6">
                        <h2>Autres établissements</h2>
                        <div class="input-group mb-3">
                            <input type="text" class="form-control" placeholder="Nom de l'école, ville, code postal ou UAI" id="inp-school-search-q">
                            <div class="input-group-append">
                                <button class="btn btn-primary" type="button" id="btn-school-search">Recherche</button>
                            </div>
                        </div>
                        <div class="list-select" id="schools-search" style="height: 300px">
                        </div>
                        <div class="mt-3">
                            <button class="btn btn-primary" id="btn-school-add">Ajouter à mes établissements</button>
                            <button class="btn btn-primary" id="btn-school-show-create">Créer un nouvel établissement</button>
                        </div>
                    </div>
                </div>

                <div id="section-schools-editor" style="display: none">
                    <h2>Ajouter un établissement</h2>
                    {!! Form::open() !!}
                        <div class="row">
                            <div class="col-6">
                                {!! Form::text('name', 'Nom') !!}
                                {!! Form::text('uai', 'UAI') !!}
                            </div>
                            <div class="col-6">
                                {!! Form::text('address', 'Addresse') !!}
                                {!! Form::text('city', 'Ville') !!}
                                {!! Form::text('zip', 'ZIP') !!}
                                {!! Form::select('region_id', 'Territoire', $regions) !!}
                                {!! Form::select('country_id', 'Pays', $countries) !!}
                                {!! Form::select('academy_id', 'Académie', $academies) !!}
                            </div>
                        </div>
                    {!! Form::close() !!}
                    <div class="mt-3">
                        <button class="btn btn-primary" id="btn-school-create">Créer</button>
                        <button class="btn btn-primary" id="btn-school-create-cancel">Annuler</button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    window.regions = {!! json_encode($regions) !!}
    window.countries = {!! json_encode($countries) !!}
    window.user_schools = {!! json_encode($schools['data']) !!}
    window.academies = {!! json_encode($academies) !!}
</script>