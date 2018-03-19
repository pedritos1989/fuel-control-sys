CombustiblesApp.namespace('PlanAsignacion.Manage');

+(function ($, router, bootbox, noty, app) {
    /**
     * PlanAsignacion Manage Class.
     *
     * @param planAsignacionId
     * @constructor
     */
    var Manage = function (planAsignacionId) {
        this.planAsignacionId = planAsignacionId;
        this.formid = 'cant_x_plan_type';
        this.deleteFormid = 'cantxplan_delete';
        this.container = 'div#uploadresources';
        this.openedDialog = undefined;
        this.serviceSelect = 'select#cant_x_plan_type_servicio';
        this.totalAsignado = 'input#total-asignado';
        this.totalDisponible = 'input#total-disponible';
        this.cantidadInsertada = 'input#cant_x_plan_type_cantidad';

        this.uploadDialog = function (json) {
            var self = this;

            self.openedDialog = bootbox.dialog({
                title: Translator.trans('plan.amount.new'),
                message: json.html,
                buttons: {
                    confirm: {
                        label: '<i class="fa fa-check"></i> ' + Translator.trans('actions.confirm'),
                        className: 'btn-primary btn-sm',
                        callback: function () {
                            self.create();

                            return false;
                        }
                    },
                    cancel: {
                        label: '<i class="fa fa-remove"></i> ' + Translator.trans('actions.cancel'),
                        className: 'btn-default btn-sm'
                    }
                }
            });
            self.always();
        };

        this.deleteDialog = function (json) {
            var self = this;

            self.openedDialog = bootbox.dialog({
                title: Translator.trans('plan.amount.delete'),
                message: json.html,
                buttons: {
                    confirm: {
                        label: '<i class="fa fa-trash"></i> ' + Translator.trans('actions.delete'),
                        className: 'btn-danger btn-sm',
                        callback: function () {
                            self.delete();

                            return false;
                        }
                    },
                    cancel: {
                        label: '<i class="fa fa-remove"></i> ' + Translator.trans('actions.cancel'),
                        className: 'btn-default btn-sm'
                    }
                }
            });
        };
    };

    Manage.prototype.startEvents = function () {
        var self = this;

        $(self.container).off('click', 'a[href="#add"]');
        $(self.container).on('click', 'a[href="#add"]', function (e) {
            e.preventDefault();
            var jqXHR = $.getJSON(router.generate('cant_plan_new', {'planAsignacion': self.planAsignacionId}));
            jqXHR.done(function (json) {
                if (jqXHR.status !== 200) {
                    return;
                }
                self.uploadDialog(json);

            }).fail(function () {
                noty({
                    text: Translator.trans('unexpected_error'),
                    type: 'error',
                    timeout: 3000
                });
            });
        });

        $(self.container).off('click', 'a[href="#delete"]');
        $(self.container).on('click', 'a[href="#delete"]', function (e) {
            e.preventDefault();

            var target = $(e.currentTarget).data('target'),
                jqXHR = $.getJSON(target);

            jqXHR.done(function (json) {
                if (jqXHR.status !== 200) {
                    return;
                }

                self.deleteDialog(json);
            }).fail(function () {
                noty({
                    text: Translator.trans('unexpected_error'),
                    type: 'error',
                    timeout: 3000
                });
            });
        });
    };

    Manage.prototype.load = function () {
        var self = this,
            jqXHR = $.get(router.generate('cant_plan_index', {'planAsignacion': self.planAsignacionId}));

        jqXHR.done(function (response) {
            $(self.container).html(response);

            self.startEvents();
        }).fail(function () {
            noty({
                text: Translator.trans('unexpected_error'),
                type: 'error',
                timeout: 3000
            });
        });
    };

    Manage.prototype.always = function () {
        var self = this;
        var $selector = $('select');
        $selector.select2();
        $('div.bootbox').find('.select2-container').attr('style', 'width: 100%');
        [].forEach.call($selector, function (el) {
            if ($(el).parent().hasClass('has-error')) {
                $(el).parent().children('span').children('span').children('span').attr('style', 'border-color: #d02d2d');
            } else if ($(el).val() !== '')
                getAvailables(self, $(el).val());
        });

        $(self.serviceSelect).on('change', function () {
            if ($(this).val() !== '')
                getAvailables(self, $(this).val());
        });

        function getAvailables(self, value) {
            var route = router.generate('cant_plan_service_changed', {
                    'planAsignacion': self.planAsignacionId,
                    'servicio': value
                }),
                jqXHR = $.getJSON(route);
            jqXHR.done(function (json) {
                if (jqXHR.status !== 200)
                    return;

                $(self.totalAsignado).val(json.cantidad);
                $(self.totalDisponible).val(json.cantidad - json.consumido);
            }).fail(function () {
                noty({
                    text: Translator.trans('unexpected_error'),
                    type: 'error',
                    timeout: 3000
                });
            })
        }
    };

    Manage.prototype.create = function (e) {

        var self = this,
            $form = $('form#' + self.formid);

        $form.ajaxSubmit({
            dataType: 'JSON',
            success: function (_json, statusText, _jqXHR) {
                if (_jqXHR.status === 200) {
                    $form.replaceWith(_json.html);
                    self.always();
                } else if (_jqXHR.status === 201) {
                    noty({
                        text: _json.message,
                        type: _json.type,
                        timeout: 3000
                    });

                    self.openedDialog.modal("hide");

                    // reload content
                    self.load();
                }
            },
            error: function (_jqXHR) {
                if (typeof _jqXHR.responseJSON !== 'undefined') {
                    var _json = _jqXHR.responseJSON;
                    noty({
                        text: _json.message,
                        type: 'error',
                        timeout: 3000
                    });
                } else {
                    noty({
                        text: Translator.trans('unexpected_error'),
                        type: 'error',
                        timeout: 3000
                    });
                }
            }
        });
    };

    Manage.prototype.delete = function () {
        var self = this,
            $form = $('form#' + self.deleteFormid);

        $form.ajaxSubmit({
            dataType: 'JSON',
            success: function (_json, statusText, _jqXHR) {
                if (_jqXHR.status === 202) {
                    noty({
                        text: _json.message,
                        type: _json.type,
                        timeout: 3000
                    });

                    self.openedDialog.modal("hide");

                    // reload content
                    self.load();
                }
            },
            error: function (_jqXHR) {
                if (typeof _jqXHR.responseJSON !== 'undefined') {
                    var _json = _jqXHR.responseJSON;
                    noty({
                        text: _json.message,
                        type: 'error',
                        timeout: 3000
                    });
                } else {
                    noty({
                        text: Translator.trans('unexpected_error'),
                        type: 'error',
                        timeout: 3000
                    });
                }
            }
        });
    };

    app.PlanAsignacion.Manage = Manage;
}(window.jQuery, window.Routing, window.bootbox, window.noty, window.CombustiblesApp));
