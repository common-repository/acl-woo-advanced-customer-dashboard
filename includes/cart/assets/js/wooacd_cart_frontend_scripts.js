jQuery(function ($) {
    //Every tiny tools are implemented  in wooacdHelper as global object literal.
    var wooacdHelper = {
        ajax: function (data) {
            return jQuery.post(wooacd_ajax_object.ajax_url, data); //Ajax url,Data
        }
    }
    $(document).ready(function () {
        wooacd_show_cart();
        wooacd_show_pending();
        wooacd_show_rejected();
        $('.wooacd-my-custom-cart').on('click', '.wooacd-remove', function (e) {
            e.preventDefault();
            var req_data = {
                'action': 'wooacd_remove_product',
                'cart_id': $(this).attr('wooacd-cart-pid'),
                'request_id': $(this).attr('wooacd-request-pid')
            }
            var request = wooacdHelper.ajax(req_data);
            request.done(function (response) {
                wooacd_show_cart();
                wooacd_show_pending();
            });
        });
        $('.wooacd-my-custom-cart').on('change', '.wooacd-custom-cart-quantity', function (e) {
            e.preventDefault();
            var req_data = {
                'action': 'wooacd_update_quantity',
                'quantity': $(this).val(),
                'id': $(this).attr('pid')
            }
            var request = wooacdHelper.ajax(req_data);
            request.done(function (response) {
                wooacd_show_pending();
            });
        });
        $('.wooacd-my-custom-cart').on('change', '.qty', function (e) {
            e.preventDefault();
            var thisSelector = $(this);
            var qty = thisSelector.val();
            var cart_item_key = thisSelector.attr("cart-item-id");
            var price = thisSelector.parent().siblings('.product-price').children('.woocommerce-Price-amount').text().replace(/[^0-9\.]/g, '');
            var currencySign = thisSelector.parent().siblings('.product-price').children('.woocommerce-Price-amount').children('.woocommerce-Price-currencySymbol').text();

            var itemTotal = thisSelector.parent().siblings('.product-subtotal').children('.woocommerce-Price-amount').text().replace(/[^0-9\.]/g, '');
            var subTotal = qty * price;
            var increasePrice = parseInt(subTotal - itemTotal);
            var cartTotal = parseInt(thisSelector.parents('#wooacd-tab-1').children().find('.cart-subtotal').children().find('.woocommerce-Price-amount').text().replace(/[^0-9\.]/g, ''));
            var orderTotal = parseInt(thisSelector.parents('#wooacd-tab-1').children().find('.order-total').children().find('.woocommerce-Price-amount').text().replace(/[^0-9\.]/g, ''));
            var newCartTotal = parseInt(cartTotal + increasePrice);
            var newOrderTotal = parseInt(orderTotal + increasePrice);
            var req_data = {
                'action': 'wooacd_update_cart_quantity',
                'cart_item_key': cart_item_key,
                'qty': qty,
            }
            var request = wooacdHelper.ajax(req_data);
            request.done(function (response) {
                thisSelector.parent().siblings('.product-subtotal').children('.woocommerce-Price-amount').html('<span class="woocommerce-Price-currencySymbol">' + currencySign + '</span>' + subTotal.toFixed(2));
                thisSelector.parents('#wooacd-tab-1').children().find('.cart-subtotal').children().find('.woocommerce-Price-amount').html('<span class="woocommerce-Price-currencySymbol">' + currencySign + '</span>' + newCartTotal.toFixed(2));
                thisSelector.parents('#wooacd-tab-1').children().find('.order-total').children().find('.woocommerce-Price-amount').html('<strong><span class="woocommerce-Price-currencySymbol">' + currencySign + '</span>' + newOrderTotal.toFixed(2) + '</strong>')
            });
        });
        $("body").on('click', '.wooacd_toggle', function (e) {
            console.log('hi');
            e.preventDefault();
            $(".wooacd_breakdown").toggle();

        });
    });

    function wooacd_show_cart() {
        var req_data = {
            'action': 'wooacd_show_cart',
        }
        //console.log(req_data);
        var request = wooacdHelper.ajax(req_data);
        request.done(function (response) {
            //console.log(response);
            $('.wooacd-customer-cart').html(response.html);
        });
    }
    function wooacd_show_pending() {
        var req_data = {
            'action': 'wooacd_show_pending',
        }
        //console.log(req_data);
        var request = wooacdHelper.ajax(req_data);
        request.done(function (response) {
            //console.log(response);
            $('.wooacd-customer-pending').html(response.html);

        });
    }
    function wooacd_show_rejected() {
        var req_data = {
            'action': 'wooacd_rejected_request',
        }
        var request = wooacdHelper.ajax(req_data);
        request.done(function (response) {
            $('.wooacd-customer-reject').html(response.html);
        });
    }
});