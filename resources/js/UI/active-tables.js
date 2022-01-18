function init() {

    var selection = null;

    var buttons = $('.active-button');


    // tables

    function select(tr) {
        var id = tr.data('row-id');
        if(isNaN(id)) {
            return;
        }
        if(selection) {
            selection.el.removeClass('active-row');
        }
        selection = {
            id: id,
            el: tr
        }
        tr.addClass('active-row')

        refreshButtons();

        var disabled_actions = tr.data('actions-disabled');
        if(typeof disabled_actions !== 'undefined') {
            disabled_actions = disabled_actions.split(',');
            buttons.each(function() {
                var btn = $(this);
                var action = btn.data('action-name');
                btn.prop('disabled',  action && disabled_actions.indexOf(action) !== -1)
            })
        }
    }

    $('table.active-table tr').each(function() {
        var tr = $(this);
        tr.on('click', function() {
            select(tr);
        })
    })




    // buttons
    function refreshButtons() {
        buttons.each(function() {
            var btn = $(this);
            var action = btn.data('action');
            var require_selection = action.indexOf(':id') !== -1;
            $(this).prop('disabled', require_selection && !selection)
        });
    }
    refreshButtons();



    function redirect(action, method) {
        //console.log(action, method, selection)
        if(action.indexOf(':id') !== -1) {
            if(!selection) {
                return;
            }
            action = action.replace(':id', selection.id);
        }
        var form = $('<form>');
        form.attr('action', action);
        method = method || 'GET';
        form.attr('method', method);

        var hidden = $('<input type="hidden" name="refer_page"/>');
        hidden.val(location.href);
        form.append(hidden);

        if(method == 'POST') {
            var hidden = $('<input type="hidden" name="_token"/>');
            hidden.val($('meta[name="csrf-token"]').attr('content'));
            form.append(hidden);
        }

        $(document.body).append(form);
        form.submit();
    }

    buttons.each(function() {
        var btn = $(this)
        btn.on('click', function() {
            var confirmation = btn.data('confirmation');
            if(!confirmation || confirm(confirmation)) {
                redirect(btn.data('action'), btn.data('method'))
            }
        })
    })

}

$(document).ready(init);