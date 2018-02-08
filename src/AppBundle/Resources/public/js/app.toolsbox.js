/**
 * Font Awesome Checkbox Changer
 * @constructor
 */
var fa_checkbox = function () {
};

fa_checkbox.prototype.enable = function () {
    $('input.fa-checkbox').each(function (idx, elem) {
        var $this = $(elem),
            clazz = $this.is(':checked')
                ? fa_checkbox.DEFAULTS.selected_icon + " " + fa_checkbox.DEFAULTS.selected_style
                : ($this.data('status') === 'closed' ? fa_checkbox.DEFAULTS.closed_icon + " " + fa_checkbox.DEFAULTS.closed_style
                    : fa_checkbox.DEFAULTS.unselected_icon + " " + fa_checkbox.DEFAULTS.unselected_style),
            fachkbox = $("<i class=\"fa " + clazz + "\">");

        fachkbox.data("content", $this.data('content'));
        fachkbox.data("status", $this.data('status'));

        $this.hide();
        $this.parent().append(fachkbox);
    });
};

fa_checkbox.prototype.reload = function (collection) {
    var self = this;

    $("i.fa.fa-check-square-o, i.fa.fa-square-o").each(function (idx, elem) {
        var value = $(elem).data('content');
        if (collection.contains(value)) {
            self.select(elem);
        } else {
            self.unselect(elem);
        }
    });
};

fa_checkbox.prototype.toggle = function (elem) {
    $(elem)
        .hasClass(fa_checkbox.DEFAULTS.selected_icon)
        ? this.unselect(elem)
        : this.select(elem);
};

fa_checkbox.prototype.unselect = function (elem) {
    $(elem)
        .slideUp(fa_checkbox.DEFAULTS.animation_duration);

    setTimeout(function () {
        $(elem)
            .removeClass(fa_checkbox.DEFAULTS.selected_icon + " " + fa_checkbox.DEFAULTS.selected_style)
            .addClass(fa_checkbox.DEFAULTS.unselected_icon + " " + fa_checkbox.DEFAULTS.unselected_style)
            .slideDown(fa_checkbox.DEFAULTS.animation_duration);
    }, fa_checkbox.DEFAULTS.animation_duration);
};

fa_checkbox.prototype.select = function (elem) {
    $(elem)
        .slideUp(fa_checkbox.DEFAULTS.animation_duration);

    setTimeout(function () {
        $(elem)
            .removeClass(fa_checkbox.DEFAULTS.unselected_icon + " " + fa_checkbox.DEFAULTS.unselected_style)
            .addClass(fa_checkbox.DEFAULTS.selected_icon + " " + fa_checkbox.DEFAULTS.selected_style)
            .slideDown(fa_checkbox.DEFAULTS.animation_duration);
    }, fa_checkbox.DEFAULTS.animation_duration);
};

fa_checkbox.DEFAULTS = {
    animation_duration: 150,
    unselected_icon: 'fa-square-o',
    selected_icon: 'fa-check-square-o',
    selected_style: 'text-primary',
    unselected_style: 'text-muted',
    closed_icon: 'fa-square',
    closed_style: 'text-muted-light'
};


/**
 * Async Loader
 * @constructor
 */
var AsyncLoader = function () {

    /**
     * Handle jQuery XHR & convert into Promise
     *
     * @param jqXHR
     * @returns {Promise}
     */
    this.handle = function (jqXHR) {
        return new Promise(function (resolve, reject) {
            jqXHR
                .done(function (response) {
                    resolve(response);
                })
                .fail(function () {
                    reject(new Error(Translator.trans('request.error')));
                });
        });
    }
};

/**
 * Loads uri
 *
 * @param uri
 * @param args
 *
 * @returns {Promise}
 */
AsyncLoader.prototype.load = function (uri, args) {
    var jqXHR;

    if (typeof args !== 'undefined') {
        jqXHR = $.get(uri + "?" + args);
    } else {
        jqXHR = $.get(uri);
    }

    return this.handle(jqXHR);
};

/**
 *
 * @param uri
 * @param data
 *
 * @returns {Promise}
 */
AsyncLoader.prototype.json = function (uri, data) {
    var jqXHR;
    if (typeof data === 'undefined') {
        data = {};
    }

    jqXHR = $.getJSON(uri, data);

    return this.handle(jqXHR);
};

/**
 *
 * @param uri
 * @param data
 *
 * @returns {Promise}
 */
AsyncLoader.prototype.post = function (uri, data) {
    var jqXHR;
    if (typeof data === 'undefined') {
        data = {};
    }

    jqXHR = $.post(uri, data);

    return this.handle(jqXHR);
};

AsyncLoader.DEFAULTS = {
    pagination_selector: "ul.pagination li",
    anchor_selector: "a"
};


/**
 * Collection Handler
 * @constructor
 */
var CollectionHandler = function () {
    this.collection = [];
    this.size = 0;
};

CollectionHandler.prototype.add = function (value) {
    this.collection.push(value);
    this.size++;
};

CollectionHandler.prototype.remove = function (value) {
    var idx = this.collection.indexOf(value);
    if (-1 !== idx) {
        this.collection.splice(idx, 1);
        this.size--;
    }
};

CollectionHandler.prototype.contains = function (value) {
    return -1 !== this.collection.indexOf(value);
};

CollectionHandler.prototype.isEmpty = function () {
    return 0 === this.size;
};

CollectionHandler.prototype.size = function () {
    return this.size;
};

CollectionHandler.prototype.toArray = function () {
    return this.collection;
};
