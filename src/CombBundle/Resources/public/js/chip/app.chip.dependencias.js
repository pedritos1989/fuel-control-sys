$(function () {
    var _selector = 'select.chip-tarjeta',
        saldo_inicial = 'input.input-saldo-inicial',
        saldo_final = 'input.input-saldo-final',
        cant_comb = 'input.input-cant-comb';
    $(_selector).on('change', function () {
        var $saldoInicial = $(_selector + ' option:selected').data('initial');
        $(saldo_inicial).val($saldoInicial);
        $(saldo_final).val($saldoInicial);
        $(cant_comb).val(0);
    });

    $(cant_comb).on('change paste keyup', function () {
        var diff = $(saldo_inicial).val() - $(this).val();
        $(saldo_final).val(diff);
    });
});