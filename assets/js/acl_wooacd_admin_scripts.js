jQuery(function ($) {
    //Every tiny tools are implemented  in wooacdHelper as global object literal.
    var wooacdHelper = {
        ajax: function (data) {
            return jQuery.post(wooacd_ajax_object.ajax_url, data); //Ajax url,Data
        }
    };
    jQuery('.wooacd-accordion > div > .wooacd-accordion-title ').click(function () {
        jQuery(this).parent().addClass("wooacd-accordion-item-active");
        jQuery(this).parent().siblings().removeClass("wooacd-accordion-item-active");
    });
    //Multiple Tags
    $(document).on('click', '#wooacd-tracker-steps-btn-add', function (e) {
        var itemLength = $('.wooacd-tracker-steps-item').length;
        var itemDom = '<div class="wooacd-tracker-steps-item">'
            + '<input type="text" name="acl_wooacd_tracker_steps[' + itemLength + ']" value="" placeholder="Tracking Step">'
            + '<button class="button wooacd-tracker-steps-btn-remove" type="button">X</button>'
            + '</div>';
        $('.wooacd-tracker-steps-wrapper').append(itemDom);
    });

    $(document).on('click', '.wooacd-tracker-steps-btn-remove', function (e) {
        if (confirm('Are you sure to delete this tracking step?')) {
            $(this).parent().remove();
        }
    });
    $(document).on('click', '#wooacd-phrase-btn-add', function (e) {
        var itemLength = $('.wooacd-phrase-item').length;
        var itemDom = '<div class="wooacd-phrase-item">'
            + '<input type="text" name="acl_wooacd_phrase[' + itemLength + ']" value="" placeholder="Phrase">'
            + '<button class="button wooacd-phrase-btn-remove" type="button">X</button>'
            + '</div>';
        $('.wooacd-phrase-wrapper').append(itemDom);
    });
    $(document).on('click', '.wooacd-phrase-btn-remove', function (e) {
        if (confirm('Are you sure to delete this phrase?')) {
            $(this).parent().remove();
        }
    });
    if ($('#estimated_delivery_time_enable').attr('checked', 'checked')) {
        //$('#set_estimated_delivery_time').attr('type','text').val('21');
        //console.log('dhsagd');
        var req_data = {
            'action': 'wooacd_get_default_estimated_time'
        }
        var request = wooacdHelper.ajax(req_data);
        request.done(function (response) {
            $('#estimated_delivery_time_enable').parents('label').after('<p class="wooacd_delivery_time">Set Estimated Delivery Time<input type="text" name="acl_wooacd_set_estimated_delivery_time" id ="set_estimated_delivery_time" value = "'+response.days+'">Days</p>');
        });

    }
    $(document).on('change', '#estimated_delivery_time_disable', function (e) {
        e.preventDefault();
        if ($(this).attr('checked', 'checked')) {
            $('.wooacd_delivery_time').remove();
        }
    });
    $(document).on('change', '#estimated_delivery_time_enable', function (e) {
        e.preventDefault();
        if ($(this).attr('checked', 'checked')) {
            var req_data = {
                'action': 'wooacd_get_default_estimated_time'
            }
            var request = wooacdHelper.ajax(req_data);
            request.done(function (response) {
                $('#estimated_delivery_time_enable').parents('label').after('<p class="wooacd_delivery_time">Set Estimated Delivery Time<input type="text" name="acl_wooacd_set_estimated_delivery_time" id ="set_estimated_delivery_time" value = "'+response.days+'">Days</p>');
            });
        }
    });
});