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
        console.log(action, method, selection)
        if(!selection) {
            return;
        }
        action = action.replace(':id', selection.id);
        var form = $('<form>');
        form.attr('action', action);
        form.attr('method', method || 'GET');
        var hidden = $('<input type="hidden" name="refer_page"/>');
        hidden.val(location.href);
        form.append(hidden);
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