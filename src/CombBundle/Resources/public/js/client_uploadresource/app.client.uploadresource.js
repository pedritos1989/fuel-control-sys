CombustiblesApp.namespace('Client.UploadResource');

+(function ($, router, bootbox, noty, app) {
    /**
     * Client Upload Resource Class.
     *
     * @constructor
     */
    var UploadResource = function () {

        this.formid = 'client_uploadedresource_type';
        this.deleteFormid = 'uploadresource_delete';
        this.container = 'body';
        this.openedDialog = undefined;
        this.attachSelected = undefined;

        this.uploadDialog = function (json) {
            var self = this;

            self.openedDialog = bootbox.dialog({
                title: Translator.trans('actions.up') + ' ' + Translator.transChoice('client.uploadresource.title', 1).toLowerCase(),
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
                title: Translator.trans('actions.delete') + ' ' + Translator.transChoice('client.uploadresource.title', 1).toLowerCase(),
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

    UploadResource.prototype.startEvents = function () {
        var self = this;

        $(self.container).off('click', 'a[href="#add"]');
        $(self.container).on('click', 'a[href="#add"]', function (e) {
            e.preventDefault();
            self.attachSelected = $(this).attr('data-id');
            var jqXHR = $.getJSON(router.generate('client_uploadresource_new'));
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

    UploadResource.prototype.load = function () {
        setTimeout(function () {
            location.reload();
        }, 4000);
    };

    UploadResource.prototype.always = function () {
        $('input[id$="_file"]').fileinput({
            // showCaption: false,
            showUpload: false//,
            // showPreview: false
        });
    };

    UploadResource.prototype.create = function () {
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

    UploadResource.prototype.delete = function () {
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

    app.Client.UploadResource = UploadResource;
}(window.jQuery, window.Routing, window.bootbox, window.noty, window.CombustiblesApp));
