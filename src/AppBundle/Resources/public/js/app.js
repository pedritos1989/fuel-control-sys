var CombustiblesApp = CombustiblesApp || {};

CombustiblesApp.namespace = function (ns_string) {
    var parts = ns_string.split('.'),
        parent = CombustiblesApp,
        i;

    // strip redundant leading global
    if (parts[0] === "CombustiblesApp") {
        parts = parts.slice(1);
    }

    for (i = 0; i < parts.length; i += 1) {
        // create a property if it doesn't exist
        if (typeof parent[parts[i]] === "undefined") {
            parent[parts[i]] = {};
        }
        parent = parent[parts[i]];
    }
    return parent;
};

$(document).ready(function () {
    var notifications = {
        'car': {
            'path': 'carro_vencimiento',
            'showPath': 'carro_show',
            'selector': 'car_expiration_alert',
            'icon': 'car'
        },
        'card': {
            'path': 'tarjeta_vencimiento',
            'showPath': 'tarjeta_show',
            'selector': 'card_expiration_alert',
            'icon': 'credit-card'
        }
    };
    $.each(notifications, function (el, not) {
        $.getJSON(Routing.generate(not.path))
            .done(function (json) {

                var $html = '',
                    notificacions_length = 0;

                $html += '<span class="badge badge-info bounceIn animation-delay6 active">__length__</span>' +
                    '<ul style="width: max-content" class="dropdown-menu notification dropdown-3 pull-right">' +
                    '<li><a href="javascript:;">' + json.header + '</a></li>';
                $.each(json.body, function (key, values) {
                    $html += '<li>\n' +
                        '       <a href="' + Routing.generate(not.showPath, {id: key}) + '">\n' +
                        '         <span class="notification-icon bg-' + values['type'] + '">\n' +
                        '           <i class="fa fa-' + not.icon + '"></i>\n' +
                        '         </span>\n' +
                        '         <span class="m-left-xs">' + values['message'] + '</span>\n' +
                        '         <span class="time text-muted">' + values['date'] + '</span>\n' +
                        '       </a>\n' +
                        '    </li>';
                    notificacions_length++;
                });
                $html = $html.replace(/__length__/g, notificacions_length);
                $('body').find('li#' + not.selector).append($html);
            })
            .fail(function (error) {
                console.error('Unexpected error...');
            });
    });
});