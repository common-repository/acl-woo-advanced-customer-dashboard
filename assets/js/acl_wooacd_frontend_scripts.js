jQuery(function ($) {
    //Every tiny tools are implemented  in wooacdHelper as global object literal.

    var wooacdHelper = {
        ajax: function (data) {
            return jQuery.post(wooacd_ajax_object.ajax_url, data); //Ajax url,Data
        }
    }
    $('.woocommerce-MyAccount-navigation-link--notifications').parents('nav').parent().addClass('wooacd-customer-dashboard-container');

    //Tab
    $('.wooacd-my-custom-cart-header >a:not(:first)').addClass('wooacd-tab-inactive');
    $('.wooacd-my-custom-cart-content > div').hide();
    $('.wooacd-my-custom-cart-content > div:first').show();


    $('.wooacd-my-custom-cart-header> a').on('click', function () {
        var t = $(this).attr('href');
        $('.wooacd-my-custom-cart-header >a').addClass('wooacd-tab-inactive');
        $(this).removeClass('wooacd-tab-inactive');
        $('.wooacd-my-custom-cart-content> div').hide();
        $('.wooacd-my-custom-cart-content > div' + t).fadeIn('slow');
        return false;

    });

    if ($(this).hasClass('wooacd-tab-inactive')) { //this is the start of our condition
        $('.wooacd-my-custom-cart-header> a').addClass('wooacd-tab-inactive');
        $(this).removeClass('wooacd-tab-inactive');
        $('.wooacd-my-custom-cart-content >div').hide();
        $(t).fadeIn('slow');
    }

});
