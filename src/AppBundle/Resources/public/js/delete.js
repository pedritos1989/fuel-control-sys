$(function () {
    $('body').on('click', '.delete-selection', function () {

        var url = Routing.generate($(this).data('action'), {id: $(this).data('id')});

        $.delete = function (url, data, callback, type) {

            if ($.isFunction(data)) {
                type = type || callback,
                    callback = data,
                    data = {};
            }

            return $.ajax({
                url: url,
                type: 'DELETE',
                success: callback,
                data: data,
                contentType: type
            });
        };

        $.delete(url, function (response) {
            var cont = 1;
            var arr = [];
            do {
                [].forEach.call($(response), function (el) {
                    if ($(el).attr('id') === 'param' + cont) {
                        arr[cont] = $(el).attr('val');
                        cont++;
                    }
                });
            } while (cont < 6);

            bootbox.dialog({
                title: '<i class="fa fa-trash-o fa-lg"></i> ' + arr[1],
                message: arr[2] + response,
                buttons: {
                    delete: {
                        label: '<i class="fa fa-trash-o"></i> ' + arr[4],
                        className: "btn-danger btn-sm",
                        callback: function () {
                            $.delete(url, $('form[name=form]').serialize(), function (data) {
                                noty({
                                    text: data.message,
                                    type: data.type,
                                    layout: data.layout,
                                    timeout: data.time
                                });
                                setTimeout(function () {
                                    location.href = Routing.generate(arr[5]);
                                }, data.time + 1000);
                            });
                        }
                    },
                    cancel: {
                        label: '<i class="fa fa-remove"></i> ' + arr[3],
                        className: "btn-default btn-sm"
                    }
                }
            });
        });
    });
});