var _selector = '.monthpicker';

$(_selector).datepicker({
    autoclose: true,
    language: 'es',
    format: "mm/yyyy",
    startView: "months",
    minViewMode: "months",
    orientation: 'bottom'
});

$(function () {
    $('a[href="#monthly"]').on('click', function (e) {
        e.preventDefault();
        $('button[id$="_report_monthly"]').trigger('click');
    });
    $('a[href="#vehicle"]').on('click', function (e) {
        e.preventDefault();
        $('button[id$="_report_vehicle"]').trigger('click');
    });
    $('a[href="#year"]').on('click', function (e) {
        e.preventDefault();
        $(_selector).val('');
        $('button[id$="_report_year"]').trigger('click');
    });
});

$(document).ready(function () {
    $.getJSON(Routing.generate('grafico_operativo_anual'))
        .done(function (response) {
            new Morris.Area({
                element: 'grafico_operativo_anual',
                data: response,
                xkey: 'm',
                ykeys: ['d', 'r', 'e'],
                labels: ['Diésel', 'Gasolina Regular', 'Gasolina Especial'],
                parseTime: false
            });
        })
        .fail(function (error) {
            console.error('Ha ocurrido un error inesperado...')
        });
});