<div class="modal" id="schools-manager-modal">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Schools</h5>
                <button type="button" class="close" data-dismiss="modal">
                    <span>&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div class="row" id="section-schools-manager">
                    <div class="col-6">
                        <h2>My schools</h2>
                        <div class="list-select" id="schools-my" style="height: 354px"></div>
                        <div class="mt-3">
                            <button class="btn btn-primary" id="btn-school-use">Use this school</button>
                            <button class="btn btn-primary" id="btn-school-delete">Delete from my schools</button>
                            <button class="btn btn-primary" id="btn-school-show-create">Add new school</button>
                        </div>
                    </div>
                    <div class="col-6">
                        <h2>Other schools</h2>
                        <div class="input-group mb-3">
                            <input type="text" class="form-control" placeholder="School name, city or zipcode" id="inp-school-search-q">
                            <div class="input-group-append">
                                <button class="btn btn-primary" type="button" id="btn-school-search">Search</button>
                            </div>
                        </div>                        
                        <div class="list-select" id="schools-search" style="height: 300px">
                        </div>
                        <div class="mt-3">
                            <button class="btn btn-primary" id="btn-school-add">Add to my schools</button>
                        </div>
                    </div>
                </div>

                <div id="section-schools-editor" style="display: none">
                    <h2>Add school</h2>
                    {!! Form::open() !!}
                        <div class="row">
                            <div class="col-6">
                                {!! Form::text('name', 'Name') !!}
                                {!! Form::text('uai', 'UAI') !!}                            
                            </div>
                            <div class="col-6">
                                {!! Form::text('address', 'Address') !!}
                                {!! Form::text('city', 'City') !!}
                                {!! Form::text('zip', 'ZIP') !!}
                                {!! Form::select('country_id', 'Country', $countries) !!}
                                {!! Form::select('region_id', 'Region', []) !!}
                            </div>
                        </div>
                    {!! Form::close() !!}
                    <div class="mt-3">
                        <button class="btn btn-primary" id="btn-school-create">Create</button>
                        <button class="btn btn-primary" id="btn-school-create-cancel">Cancel</button>
                    </div>                    
                </div>
            </div>
        </div>
    </div>
</div>


<script>
    var regions = {!! json_encode($regions) !!}
</script>