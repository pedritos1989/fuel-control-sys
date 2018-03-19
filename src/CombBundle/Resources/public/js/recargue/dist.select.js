var dist = function (options) {
    this.options = $.extend(true, dist.DEFAULTS, options);
};

dist.prototype.loadByTarjeta = function (tarjeta) {
    var self = this,
        jqXHR = $.ajax({
            url: Routing.generate('recargue_dist', {'tarjeta': tarjeta}),
            dataType: 'json'
        });

    jqXHR.done(function (json) {
        if (jqXHR.status === 200) {
            var $distSelect = $(self.options.selector);

            $distSelect.find('option').remove();
            for (var idx in json) {
                $distSelect.append($('<option value="' + json[idx].id + '">' + json[idx].name + '</option>'))
            }

            console.log('Successful Dists load...');
        }
    }).fail(function () {
        console.log('fail');
    });
}
;

dist.prototype.isDisabled = function () {
    var $distSelect = $(this.options.selector);

    return $distSelect.hasClass('disabled');
};

dist.prototype.disable = function () {
    var $distSelect = $(this.options.selector);

    $distSelect.find('option').remove();
    $distSelect.addClass('disabled');
    $distSelect.attr('disabled', 'disabled');

    $(document).trigger('recargue.dist.disabled');
};

dist.prototype.enable = function () {
    var $distSelect = $(this.options.selector);

    $distSelect.removeClass('disabled');
    $distSelect.removeAttr('disabled');

    $(document).trigger('recargue.dist.enabled');
};

dist.DEFAULTS = {
    selector: 'select.recargue-dist'
};