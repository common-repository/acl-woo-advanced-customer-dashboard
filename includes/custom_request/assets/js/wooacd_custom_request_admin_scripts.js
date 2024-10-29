jQuery(function ($) {
    //Every tiny tools are implemented  in wooacdHelper as global object literal.
    //console.log(wooacd_ajax_object);
    var wooacdHelper = {
        ajax: function (data) {
            return jQuery.post(wooacd_ajax_object.ajax_url, data); //Ajax url,Data
        }
    }
    //console.log('test');
    $(document).ready(function () {
        $('.wooacd-custom-request-content').on('click', '.wooacd-cancel-customer-request', function (e) {
            console.log('hi');
            var id = $(this).attr('custom-cart-id');
            var uid = $(this).attr('user-id');
            console.log(id,uid)
            $("body").addClass("custom-request-active");

            setTimeout(function (e) {                
                $('#custom-cart-id').val(id);
                $('#user-id').val(uid);
            }, 1000);
        });

        $('.wooacd-custom-request-content').on('click', '.approve-customer-request', function (e) {
            var id = $(this).attr('custom-cart-id');
            var uid = $(this).attr('user-id');
            //console.log(id);
            $("body").addClass("approve-request-active");
            setTimeout(function (e) {
                $('#custom_cart_id').val(id);
                $('#user_id').val(uid);
            }, 1000);
        });
        $(".search-autocomplete").autocomplete({
            minChars: 2,
            source: function (string, response) {
                //console.log(string);
                if (string != "") {
                    var req_data = {
                        'action': 'wooacd_add_to_cart_search_product',
                        'search': string
                    }
                    var request = wooacdHelper.ajax(req_data);
                    request.done(function (data) {
                        response(data.data);
                    });
                } else {
                    alert('Enter some letters of product name.');
                }
            },
            select: function (event, ui) {
                //console.log(ui);
                // Set selection
                $('#product_id').val(ui.item.value); // save selected id to input
                $('#wooacd-input-search').val(ui.item.label); // display the selected text
                return false;
            }
        });
        $('#wooacd-approve-request').on('click', function (e) {
            e.preventDefault();
            var req_data = {
                'action': 'wooacd_approve_product',
                'custom_cart_id': $('#custom_cart_id').val(),
                'product_id': $('#product_id').val(),
                'user_id': $('#user_id').val()
            }
            //console.log(req_data);
            var request = wooacdHelper.ajax(req_data);
            request.done(function (response) {
                $('.search-autocomplete').val('');
                $('.approve-request-form').hide();
                $('.approve-request-notice').empty();
                $('.approve-request-notice').show();
                $('.approve-request-notice').append(response);
                wooacd_show_custom_product_item();
                //console.log(response);
            });
        });
        $('#wooacd-request-cancel').on('click', function (e) {
            e.preventDefault();
            //console.log('gfdsgsdg');            
            var req_data = {
                'action': 'wooacd_insert_admin_note',
                'id': $('#custom-cart-id').val(),
                'uid': $('#user-id').val(),
                'note': $('#request-cancellation-reason').val(),
            }
            //req_data
            //console.log(req_data);
            var request = wooacdHelper.ajax(req_data);
            request.done(function (response) {
                //console.log(response);
                $('#custom-cart-id').val('');
                $('#request-cancellation-reason').val('');
                $('.custom-request-form').hide();
                $('.custom-request-notice').empty();
                $('.custom-request-notice').show();
                $('.custom-request-notice').append(response);
                wooacd_show_custom_product_item()
            });
        });
        $('.wooacd-custom-request-close').on('click', function (e) {
            $('.custom-request-form').show();
            $('.custom-request-notice').hide();
            $("body").removeClass("custom-request-active");

        });
        $('.wooacd-approve-request-close').on('click', function (e) {
            $('.approve-request-form').show();
            $('.approve-request-notice').hide();
            $("body").removeClass("approve-request-active");
        });
        $('body').on('click', function (e) {
            //if clicked element is not your element and parents aren't your div
            //console.log('target',e.target);
            // if (e.target.id != 'custom-request-wrapper' && e.target.className != 'wooacd-cancel-customer-request' && $(e.target).parents('#custom-request-wrapper').length == 0) {
            //     if ($('.custom-request-active').length > 0) {
            //         $('.custom-request-form').show();
            //         $('.custom-request-notice').hide();
            //         $("body").removeClass("custom-request-active"); //custom-request-active
            //     }
            // }
        });
        $('body').on('click', function (e) {
            //if clicked element is not your element and parents aren't your div
            //console.log('target',e.target);
            // if (e.target.id != 'approve-request-wrapper' && e.target.className != 'approve-customer-request' && $(e.target).parents('#approve-request-wrapper').length == 0) {
            //     if ($('.approve-request-active').length > 0) {
            //         //console.log('find class');
            //         $('.approve-request-form').show();
            //         $('.approve-request-notice').hide();
            //         $("body").removeClass("approve-request-active"); //approve-request-active
            //     }
            // }
        });
        wooacd_show_custom_product_item()
        $(document).on('click', '.wooacd-breakdown-btn-add', function (e) {
            e.preventDefault();
            var tagLength = $('.wooacd-breakdown-pair').length;
            console.log(tagLength);
            var piarDom = '<span class="wooacd-breakdown-pair"><input placeholder="Label" class="" type="text" name="wooacd_cost_breakdown[' + tagLength + '][label]" value=""/><input placeholder="Value" type="text" name="wooacd_cost_breakdown[' + tagLength + '][value]" value=""/><button class="button wooacd-breakdown-btn-remove" type="button">X</button></span>';
            $('.wooacd-breakdown-container').append(piarDom);
        });
        
        $(document).on('click', '.wooacd-breakdown-btn-remove', function (e) {
            if (confirm('Are you sure to delete this fields set?')) {
                $(this).parent().remove();
            }
        });
        $('body').on('click', '.wooacd_pagination_link', function (e) {            
            var page = $(this).attr("page-no");  
            console.log(page);
            wooacd_show_custom_product_item(page);
        });
    });
    function wooacd_show_custom_product_item(page) {
        var req_data = {
            'action': 'wooacd_show_custom_product_item',
            'page' : page
        }
        var request = wooacdHelper.ajax(req_data);
        request.done(function (response) {
            $('.wooacd-custom-request-content').html(response.html);
        });
    }
});