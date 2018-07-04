CombustiblesApp.namespace('Recargue.Estado');

+(function ($, router, bootbox, noty, app) {
    /**
     * RecargueEstado Manage Class.
     *
     * @param recargueId
     * @constructor
     */
    var Manage = function (recargueId) {
        this.recargueId = recargueId;
        this.formid = 'recargue_estado_change';
        this.openedDialog = undefined;
        this.container = 'body';

        this.uploadDialog = function (json) {
            var self = this;

            self.openedDialog = bootbox.dialog({
                title: Translator.trans('request.card.change'),
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

    };

    Manage.prototype.startEvents = function () {
        var self = this;

        $(self.container).off('click', 'a[href="#changer"]');
        $(self.container).on('click', 'a[href="#changer"]', function (e) {
            e.preventDefault();
            var jqXHR = $.getJSON(router.generate('status_changer', {'id': self.recargueId}));
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

    };

    Manage.prototype.always = function () {

        $('.switch-comp').bootstrapSwitch({
            labelText: Translator.trans('request.card.check.label'),
            onText: Translator.trans('request.card.check.yes'),
            offText: Translator.trans('request.card.check.no'),
            offColor: 'danger',
            labelWidth: 130
        });
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
                    setTimeout(function () {
                        location.reload();
                    }, 4000);

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

    app.Recargue.Estado = Manage;
}(window.jQuery, window.Routing, window.bootbox, window.noty, window.CombustiblesApp));
