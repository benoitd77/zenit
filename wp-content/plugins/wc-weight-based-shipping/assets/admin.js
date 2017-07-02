jQuery(function($) {
    'use strict';

    var $table = $("#woowbs_shipping_methods");

    $table.find('td').click(function(e) {
        if (e.target == this) {
            location.href = $(this).parent().data('settingsUrl')
        }
    });

    var orderUpdatingRequest = null;

    $table.find('tbody').on("sortupdate", function(event, ui) {

        var ids = $(this).find('[data-profile-id]').map(function() {
            return $(this).attr('data-profile-id');
        }).get();

        if (orderUpdatingRequest) {
            orderUpdatingRequest.abort();
        }

        $table.addClass('in-progress');

        //noinspection JSUnresolvedVariable
        orderUpdatingRequest =
            jQuery.post(woowbs_ajax_vars.ajax_url, {
                'action': 'woowbs_update_rules_order',
                'profiles': ids
            })
            .always(function() {
                $table.removeClass('in-progress');
                orderUpdatingRequest = null;
            });
    });
});