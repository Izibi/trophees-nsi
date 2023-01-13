// reqire window.regions and window.countries arrays

window.RegionSelector = function(form) {


    var region_sel = form.find('select[name=region_id]').first();
    var country_sel = form.find('select[name=country_id]').first();
    var academy_sel = form.find('select[name=academy_id]').first();

    var main_country_id = window.regions.find(function(r) {
        return r.country_id !== null;
    }).id;

    function getRegion(id) {
        return window.regions.find(function(r) {
            return r.id == id;
        })
    }

    function onRegionChange(initial) {
        var region_id  = region_sel.val();
        var region = getRegion(region_id);

        var is_main_country = region && region.country_id !== null;
        if(!initial && region) {
            country_sel.val(is_main_country ? region.country_id : '');
        }
        country_sel.find('option[value="' + main_country_id + '"]').prop('disabled', !is_main_country);
        country_sel.attr('readonly', is_main_country);
        country_sel.closest('.form-group').toggle(region && !is_main_country);

        if(window.academies) {
            var region_academies = [];
            for(var i=0; i<window.academies.length; i++) {
                if(window.academies[i].region_id == region_id) {
                    region_academies.push('' + window.academies[i].id);
                }
            }
            if(!initial) {
                if(region_academies.length == 1) {
                    academy_sel.val(region_academies[0]);
                } else {
                    academy_sel.val('');
                }
            }
            academy_sel.find('option').each(function() {
                var opt = $(this);
                if(region_academies.indexOf(opt.val()) === -1) {
                    opt.hide();
                } else {
                    opt.show();
                }
            });
            academy_sel.closest('.form-group').toggle(region_academies.length > 0);
        }
    }


    region_sel.on('change', function() {
        onRegionChange();
    });
    onRegionChange(true);


    return {

        reset: function() {
            if(!window.regions.length) {
                return;
            }
            var r = window.regions[0];
            region_sel.val(r.id);
            onRegionChange();
        }
    }

}