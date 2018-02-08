$.blockUI.defaults.css = {};
$.blockUI.defaults.message = '<div class="loader fa-spin"></div>';
$(document).ready(function () {
    [].forEach.call($('select'), function (el) {
        if ($(el).parent().hasClass('has-error')) {
            $(el).parent().children('span').children('span').children('span').attr('style', 'border-color: #d02d2d');
        }
    });
});