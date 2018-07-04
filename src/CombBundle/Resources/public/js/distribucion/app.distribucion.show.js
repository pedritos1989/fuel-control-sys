CombustiblesApp.namespace('Distribucion.Show');

+(function ($, app) {

    var Distribucion = function (distribucionId) {
        this.distribucionId = distribucionId;
        this.resources = new app.Distribucion.Manage(distribucionId);

        this.startEvents();
    };

    Distribucion.prototype.startEvents = function () {
        var self = this;
        // on show tab events
        $('a[data-toggle="tab"]').on('show.bs.tab', function (e) {
            var href = $(e.target).attr('href');
            var relhref = $(e.relatedTarget).attr('href');

            if (href === '#uploadresources') {
                self.resources.load();
            } else if (href !== '#showdata') {
                throw Error('This is not a valid tab!!!');
            }
        });
    };

    app.Distribucion.Show = Distribucion;
}(window.jQuery, window.CombustiblesApp));
