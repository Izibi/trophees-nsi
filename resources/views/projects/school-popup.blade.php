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
                        <div class="list-select" id="schools-my" style="height: 348px"></div>
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



<script>

    var overlay = {

        el: false,

        render: function() {
            if(this.el) {
                return;
            }
            this.el = $('<div class="screen-overlay"><div class="lds-ellipsis"><div></div><div></div><div></div><div></div></div></div>');
            $(document.body).append(this.el);
        },

        show: function() {
            this.render();
            this.el.show();
        },
 
        hide: function() {
            this.el.hide();
        }
    }



    function formatSchoolName(school) {
        return school.name + ', ' + school.zip + ' ' + school.city + ', ' + school.country.name;
    }



    function UserSchools() {

        var selection = null;
        if(!('user_schools' in window)) {
            window.user_schools = [];
        }

        function render() {
            var el = $('#schools-my');
            el.empty();
            for(var i=0; i<window.user_schools.length; i++) {
                el.append('<div class="list-item" data-school-id="' + window.user_schools[i].id + '">' + formatSchoolName(window.user_schools[i]) + '</div>') ;
            }
            el.find('div').on('click', function() {
                el.find('div.list-item-selected').removeClass('list-item-selected');
                $(this).addClass('list-item-selected');
                var id = $(this).data('school-id');
                select(id);
            })
        }

        function select(id) {
            selection = id;
            $('#btn-school-use').prop('disabled', selection === null)
            $('#btn-school-delete').prop('disabled', selection === null)
        }

        function refreshParentSelect() {
            var el = $('#edit-form select[name=school_id]');
            el.empty();
            var option;
            for(var i=0; i<window.user_schools.length; i++) {
                option = $('<option/>').attr('value', window.user_schools[i].id).text(formatSchoolName(window.user_schools[i]));
                el.append(option);
            }
            el.val(selection);
        }

        function doRequest(id, action) {
            select(null);
            overlay.show();
            $.ajax({
                dataType: 'json',
                url: '/user_schools/' + action,
                method: 'POST',
                data: {
                    id: id
                },
                success: function(data) {
                    overlay.hide();
                    window.user_schools = data;
                    refreshParentSelect();
                    render();
                }
            });
        }


        return {

            refresh: function() {
                render();
                select(null);
            },

            setSchools: function(schools) {
                window.user_schools = schools;
                render();
                select(null);
            },

            refreshParentSelect: function() {
                if(selection !== null) {
                    refreshParentSelect();
                }
            },

            addSchool: function(id) {
                doRequest(id, 'add');
            },

            removeSeleted: function() {
                if(selection !== null) {
                    doRequest(selection, 'remove');
                }
            }
        }
    }




    function SearchSchool() {

        var selection = null;
        var list = [];

        $('#inp-school-search-q').val('');

        function select(id) {
            selection = id;
            $('#btn-school-add').prop('disabled', selection === null)
        }
        select(null);


        function render() {
            var el = $('#schools-search');
            el.empty();
            if(!list.length) {
                el.text('Your search did not match any schools.');
                return;
            }
            for(var i=0; i<list.length; i++) {
                el.append('<div class="list-item" data-school-id="' + list[i].id + '">' + formatSchoolName(list[i]) + '</div>') ;
            }
            el.find('div').on('click', function() {
                el.find('div.list-item-selected').removeClass('list-item-selected');
                $(this).addClass('list-item-selected');
                var id = $(this).data('school-id');
                select(id);
            })
        }        

        function load(q) {
            select(null);
            overlay.show();
            $.ajax({
                dataType: 'json',
                url: '/user_schools/search',
                data: {
                    q: q
                },
                success: function(data) {
                    overlay.hide();
                    list = data;
                    render();
                }
            });
        }

        return {

            search: function(q) {
                $('#schools-search').empty();
                load(q);
            },

            getSelection: function() {
                return selection;
            }
        }
    }



    function FormSchool(options) {

        var groups = $('#section-schools-editor .form-group');

        function showGroupError(form_group, message) {
            form_group.find('.form-control').addClass('is-invalid');
            form_group.find('.invalid-feedback').remove();
            form_group.append('<div class="invalid-feedback">' + message + '</div>');
        }

        function hideGroupError(form_group) {
            form_group.find('.invalid-feedback').remove();
            form_group.find('.form-control').removeClass('is-invalid');                
        }

        function displayErrors(errors) {
            groups.each(function() {
                var group = $(this);
                var control = group.find('.form-control');
                var name = control.attr('name');
                if(name in errors) {
                    showGroupError(group, errors[name][0])
                } else {
                    hideGroupError(group)
                }
            });
        }

        function resetErrors() {
            groups.each(function() {
                var group = $(this);
                group.find('.invalid-feedback').remove();
                group.find('.form-control').removeClass('is-invalid');
            })
        }


        function resetValues() {
            groups.each(function() {
                var group = $(this);
                var control = group.find('.form-control');
                control.val('');
            });
        }

        function reset() {
            resetErrors()
            resetValues();
            refreshRegions();
        }


        function collectFormData() {
            var res = {};
            groups.each(function() {
                var group = $(this);
                var control = group.find('.form-control');
                var name = control.attr('name');
                res[name] = control.val();
            });
            return res;
        }

        function sendRequest(data) {
            overlay.show();
            $.ajax({
                dataType: 'json',
                url: '/user_schools/create',
                data: data,
                method: 'POST',
                success: function(data) {
                    overlay.hide();
                    if(data.success) {
                        reset();                        
                        options.onCreate(data.schools);
                    }
                },
                error: function(xhr, status, error) {
                    var res = xhr.responseJSON;
                    if(res.errors) {
                        overlay.hide();
                        displayErrors(res.errors)
                    }
                }
            });
        }


        var region_sel = $('#section-schools-editor select[name=region_id]').first();
        var country_sel = $('#section-schools-editor select[name=country_id]').first();
        function refreshRegions() {
            region_sel.empty();
            var country_id = country_sel.val();
            var first_region_id = null;
            var option;
            for(var i=0; i<regions.length; i++) {
                if(regions[i].country_id != country_id) {
                    continue;
                }
                if(first_region_id === null) {
                    first_region_id = regions[i].id;
                }
                option = $('<option/>').attr('value', regions[i].id).text(regions[i].name);
                region_sel.append(option);
            }
            region_sel.val(first_region_id);
        }
        country_sel.on('change', refreshRegions);
        refreshRegions();
        
        return {

            reset: function() {
                reset();
            },

            submit: function() {
                var data = collectFormData();
                sendRequest(data);
            }
        }
    }



    function SchoolsManager() {

        var modal_el = $('#schools-manager-modal');
        var user_schools = UserSchools();
        var search_school = SearchSchool();
        var form_school = FormSchool({
            onCreate: function(schools) {
                $('#section-schools-editor').hide();
                user_schools.setSchools(schools);
                $('#section-schools-manager').show();
            }
        });
        

        // schools manager section
        $('#btn-school-use').on('click', function() {
            user_schools.refreshParentSelect();
            modal_el.modal('hide');
        })        

        $('#btn-school-search').on('click', function() {
            var q = $('#inp-school-search-q').val().trim();
            if(q.length > 0) {
                search_school.search(q);
            }
        });

        $('#btn-school-add').on('click', function() {
            var id = search_school.getSelection();
            user_schools.addSchool(id);
        });

        $('#btn-school-delete').on('click', function() {
            user_schools.removeSeleted();
        });        


        // create school editor section
        $('#btn-school-show-create').on('click', function() {
            form_school.reset();
            $('#section-schools-manager').hide();
            $('#section-schools-editor').show();
        });


        $('#btn-school-create-cancel').on('click', function() {
            $('#section-schools-manager').show();
            $('#section-schools-editor').hide();
        })

        $('#btn-school-create').on('click', function() {
            form_school.submit();
        })

        return {
            show: function() {
                modal_el.modal('show');
                user_schools.refresh();
            },

            hide: function() {
                modal_el.modal('hide');
            }
        }
    }
    
    var schools_manager = SchoolsManager();


//schools_manager.show();    
//$('#section-schools-manager').hide();
//$('#section-schools-editor').show();
</script>