var selection = function () {
    this.cardLoader = new card();

    this.startEvents();
};

selection.prototype.startEvents = function () {
    var self = this;

    $('select.selection-section').on('change', function (e) {
        var section = $(this).val();

        if (typeof section !== 'undefined' && section !== '') {
            self.cardLoader.enable();
            self.cardLoader.loadBySection(section);
        } else {
            self.cardLoader.disable();
        }
    });
};