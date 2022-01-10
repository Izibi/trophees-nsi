window.RegionSelector = function(form, regions) {


    var region_id_inp = form.find('input[name=region_id]').first();
    var region_sel = form.find('select[name=region]').first();
    var country_id_sel = form.find('select[name=country_id]').first();

    var initial_region_id = region_id_inp.val();    

    function refreshRegions() {
        region_sel.empty();
        var country_id = country_id_sel.val();
        var option;
        for(var i=0; i<regions.length; i++) {
            if(regions[i].country_id != country_id) {
                continue;
            }
            option = $('<option/>').attr('value', regions[i].id).text(regions[i].name);
            region_sel.append(option);
        }
    }
    country_id_sel.on('change', function() {
        refreshRegions();
        region_id_inp.val(region_sel.val());
    });
    refreshRegions();
    region_sel.val(region_id_inp.val());

    region_sel.on('change', function() {
        region_id_inp.val(region_sel.val());
    })

}