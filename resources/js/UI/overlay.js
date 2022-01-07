window.overlay = {

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