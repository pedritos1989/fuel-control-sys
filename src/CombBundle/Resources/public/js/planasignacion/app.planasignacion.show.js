CombustiblesApp.namespace('PlanAsignacion.Show');

+(function ($, app) {

    var PlanAsignacion = function (planAsignacionId) {
        this.planAsignacionId = planAsignacionId;
        this.resources = new app.PlanAsignacion.Manage(planAsignacionId);

        this.startEvents();
    };

    PlanAsignacion.prototype.startEvents = function () {
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

    app.PlanAsignacion.Show = PlanAsignacion;
}(window.jQuery, window.CombustiblesApp));
