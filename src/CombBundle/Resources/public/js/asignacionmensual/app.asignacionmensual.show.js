CombustiblesApp.namespace('AsignacionMensual.Show');

+(function ($, app) {

    var AsignacionMensual = function (asignacionMensualId) {
        this.asignacionMensualId = asignacionMensualId;
        this.resources = new app.AsignacionMensual.Manage(asignacionMensualId);

        this.startEvents();
    };

    AsignacionMensual.prototype.startEvents = function () {
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

    app.AsignacionMensual.Show = AsignacionMensual;
}(window.jQuery, window.CombustiblesApp));
