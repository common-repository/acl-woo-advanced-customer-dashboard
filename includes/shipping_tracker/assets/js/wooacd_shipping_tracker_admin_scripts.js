jQuery(function ($) {
    //Every tiny tools are implemented  in wooacdHelper as global object literal.
    //console.log(wooacd_ajax_object);
    var wooacdHelper = {
        ajax: function (data) {
            return jQuery.post(wooacd_ajax_object.ajax_url, data); //Ajax url,Data
        }
    }
    $(document).ready(function () {
        wooacd_shipping_tracker();
        $(".wooacd-checkbox").change(function (e) {
            e.preventDefault();
            if (this.checked) {
                $(this).val(1);
            } else {
                $(this).val(0);
            }

        });
        $("#wooacd-steps-submit").click(function (e) {
            e.preventDefault();
            var req_data = {
                'action': 'save_shipping_tracker',
                'steps': $("#wooacd-steps").val()
            }
            var request = wooacdHelper.ajax(req_data);
            request.done(function (response) {
                $('#wooacd-steps').val('');
                wooacd_shipping_tracker();

            });
        });
        $(".wooacd-shipping-tracker").on('click', '.wooacd-steps-btn-delete', function (e) {
            if (confirm('Are you sure?')) {
                var req_data = {
                    'action': 'delete_shipping_tracker',
                    'term_id': $(this).attr('tid'),
                }
                var request = wooacdHelper.ajax(req_data);
                request.done(function (response) {
                    wooacd_shipping_tracker();
                });
            }
        });

        $(".wooacd-shipping-tracker").on('click', '.wooacd-steps-td', function (e) {
            var termId = $(this).attr('tid');
            var $this = $(this);
            var $input = $('<input>', {
                value: $this.text(),
                type: 'text',
                blur: function () {
                    $this.text(this.value);
                    var req_data = {
                        'action': 'update_shipping_tracker',
                        'step': this.value,
                        'term_id': termId,
                    }
                    var request = wooacdHelper.ajax(req_data);
                },
                keyup: function (e) {
                    if (e.which === 13) $input.blur();
                }
            }).appendTo($this.empty()).focus();

        });
        $("body").on('click', '.wooacd-steps-btn-edit', function (e) {
            $(this).attr("disabled", true);
            $(this).parents(".wooacd-step-row").find(".wooacd-shipping-step-name").hide();
            $(this).parents(".wooacd-step-row").find(".wooacd-update-shipping-step").show();

            $(this).parents(".wooacd-step-row").siblings().find(".wooacd-shipping-step-name").show();
            $(this).parents(".wooacd-step-row").siblings().find(".wooacd-update-shipping-step").hide();
            $(this).parents(".wooacd-step-row").siblings().find("button").attr("disabled", false);
        });
        $("body").on('click', '.wooacd-steps-btn-update', function (e) {
            //console.log('hi');
            var ShippingStepVal = $(this).siblings('.wooacd-steps-input').val();
            //console.log(ShippingStepVal);
            var termId = $(this).attr('tid');
            var req_data = {
                'action': 'update_shipping_tracker',
                'step': ShippingStepVal,
                'term_id': termId,
            };
            var ShippingStapeRow = $(this).parents(".wooacd-step-row");
            var formUpdateSelector = $(this).parent(".wooacd-update-shipping-step");
            var updateDisplaySelector = $(this).parent().siblings('.wooacd-shipping-step-name');


            var request = wooacdHelper.ajax(req_data);
            request.done(function (response) {

                formUpdateSelector.hide();
                updateDisplaySelector.show().text(ShippingStepVal);
                ShippingStapeRow.find("button").attr("disabled", false);
                // $(this).parent(".wooacd-update-shipping-step").hide();
                //$(this).parent().siblings().find('.wooacd-shipping-step-name').show().text(ShippingStepVal);

            })


        });

    });

    function wooacd_shipping_tracker() {

        var req_data = {
            'action': 'wooacd_shipping_tracker',
        }
        var request = wooacdHelper.ajax(req_data);
        request.done(function (response) {
            $('.wooacd-shipping-tracker').html(response.html);
        });
    }
});