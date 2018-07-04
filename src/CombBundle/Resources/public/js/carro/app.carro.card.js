var card = function (options) {
    this.options = $.extend(true, card.DEFAULTS, options);
};

card.prototype.loadBySection = function (section) {
    var self = this,
        jqXHR = $.ajax({
            url: Routing.generate('selection_card', {'section': section}),
            dataType: 'json'
        });

    jqXHR.done(function (json) {
        if (jqXHR.status === 200) {
            var $cardSelect = $(self.options.selector);

            $cardSelect.find('option').remove();
            for (var idx in json) {
                $cardSelect.append(
                    $('<option title="' + json[idx].service + '" value="' + json[idx].id + '">' + json[idx].name + '</option>')
                );
            }

            console.log('Successful Cards load...');
        }
    }).fail(function () {
        console.error('fail');
    });
}
;

card.prototype.isDisabled = function () {
    var $cardSelect = $(this.options.selector);

    return $cardSelect.hasClass('disabled');
};

card.prototype.disable = function () {
    var $cardSelect = $(this.options.selector);

    $cardSelect.find('option').remove();
    $cardSelect.addClass('disabled');
    $cardSelect.attr('disabled', 'disabled');

    $(document).trigger('selection.card.disabled');
};

card.prototype.enable = function () {
    var $cardSelect = $(this.options.selector);

    $cardSelect.removeClass('disabled');
    $cardSelect.removeAttr('disabled');

    $(document).trigger('selection.card.enabled');
};

card.DEFAULTS = {
    selector: 'select.selection-card'
};