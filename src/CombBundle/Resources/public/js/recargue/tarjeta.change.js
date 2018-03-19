var recargue = function () {
    this.distLoader = new dist();

    this.startEvents();
};

recargue.prototype.startEvents = function () {
    var self = this;

    $('select.recargue-tarjeta').on('change', function (e) {
        var tarjeta = $(this).val();

        if (typeof tarjeta !== 'undefined' && tarjeta !== '') {
            self.distLoader.enable();
            self.distLoader.loadByTarjeta(tarjeta);
        } else {
            self.distLoader.disable();
        }
    });
};