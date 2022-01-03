function init() {

    var selection = null;


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
    }

    $('table.active-table>tbody>tr').each(function() {
        var tr = $(this);
        tr.on('click', function() {
            select(tr);
        })
    })




    // buttons
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

    $('button.active-button').each(function() {
        var btn = $(this)
        btn.on('click', function() {
            redirect(btn.attr('action'), btn.attr('method'))
        })
    })

}

$(document).ready(init);