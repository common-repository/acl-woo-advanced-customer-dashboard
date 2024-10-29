jQuery(function ($) {
    //Every tiny tools are implemented  in wooacdHelper as global object literal.
    var wooacdHelper = {
        ajax: function (data) {
            return jQuery.post(wooacd_custom_request_ajax_object.ajax_url, data); //Ajax url,Data
        }
    }
    //console.log('test');
    $('.wooacd-custom-request').on('click', function (e) {
              
        if ($(this).siblings('.wooacd-custom-product-id').length > 0) {
            $('.wooacd-loader').show();
            var wooProId = $(this).siblings('.wooacd-custom-product-id').val();
            //console.log(wooProId);
            var req_data = {
                'action': 'wooacd_grab_local_url',
                'product_id': wooProId
            };
            //console.log(req_data);
            var request = wooacdHelper.ajax(req_data);
            request.done(function (response) {
                //console.log(response);
                var productTitle = response.title;
                if (typeof productTitle === 'string' || productTitle instanceof String) {
                    //console.log(productTitle);                
                    $('.custom-request-form').children('form').prepend('<p class="wooacd-product-title">' + productTitle + '</p><input type="hidden" id="request-product-link" value="' + response.url + '">');
                    $('.custom-request-form').find('#wooacd-product-link').remove().end();
                    $('.wooacd-loader').hide();
                    $("body").addClass("custom-request-active");
                } else {
                    //$('.custom-request-form').append('<p>'+productTitle+'</p>');
                    //console.log(` title field is not working`);
                    $('.custom-request-form').find('#request-product-link').val($('#wooacd-product-link').val());
                    $("body").addClass("custom-request-active");
                }
            });
        }else{
            $("body").addClass("custom-request-active");
        }        
    });
    $('.wooacd-custom-product-request-search').on('click', function (e) {
        e.preventDefault();

        var link = $('#wooacd-product-link').val();
        if (link === "" && isValidURL(link)) {
            alert("Please Enter a Product URL");
        } else {
            //console.log('hi');
            $('.wooacd-loader').show();
            var req_data = {
                'action': 'wooacd_custom_request_window',
                'product_link': link
            }

            var request = wooacdHelper.ajax(req_data);
            request.done(function (response) {
                console.log(response);
                var productTitle = response.title;
                if (typeof productTitle === 'string' || productTitle instanceof String) {
                    //console.log(productTitle);                
                    $('.custom-request-form').children('form').prepend('<p class="wooacd-product-title">' + productTitle + '</p><input type="hidden" id="request-product-link" value="' +
                        $('#wooacd-product-link').val() + '">');
                    $('.custom-request-form').find('#wooacd-product-link').remove().end();
                    $("body").addClass("custom-request-active");

                } else {
                    //$('.custom-request-form').append('<p>'+productTitle+'</p>');
                    //console.log(` title field is not working`);
                    $('.custom-request-form').find('#request-product-link').val($('#wooacd-product-link').val());
                    $("body").addClass("custom-request-active");
                }
                $('.wooacd-loader').hide();
                //$('.custom-request-active').html(response.html);

            });
        }
    });

    $(document).on('click', '#request-submit', function (e) {
        e.preventDefault();
        $('.wooacd-loader').show();
        $('.validation-message').empty();
        if ($('#wooacd-visitor').val() === 'guest') {
            if ($("#request-full-name").val() != '') {
                if ($("#request-email").val() != '' && isValidEmail($("#request-email").val()) === true) {
                    if ($("#request-phone-number").val() != '' && phoneNumber($("#request-phone-number").val()) === true) {
                        if ($("#request-product-link").val() != '' && isValidURL($('#request-product-link').val()) === true) {
                            if ($("#request-product-qty").val() != '' && $('#request-product-qty').val() >= 1 && isNumber($('#request-product-qty').val())) {
                                var full_name = $("#request-full-name").val();
                                var email = $("#request-email").val();
                                var phone_number = $("#request-phone-number").val();
                                var product_link = $('#request-product-link').val();
                                var note = $('#request-product-note').val();
                                var quantity = $('#request-product-qty').val();
                                var req_data = {
                                    'action': 'wooacd_submit_request',
                                    'full_name': full_name,
                                    'email': email,
                                    'phone_number': phone_number,
                                    'product_link': product_link,
                                    'note': note,
                                    'quantity': quantity
                                }
                                console.log(req_data);
                                var request = wooacdHelper.ajax(req_data);
                                request.done(function (response) {
                                    $('#request-full-name').val('');
                                    $("#request-email").val('');
                                    $("#request-phone-number").val('');
                                    $('#request-product-link').val('');
                                    $('#request-product-note').val('');
                                    $('#request-product-qty').val('1');
                                    $('.wooacd-product-title').empty();
                                    $('.custom-request-form').hide();
                                    $('.custom-request-notice').show();
                                    $('.custom-request-notice').empty();
                                    $('.custom-request-notice').append(response.message);
                                });
                            } else {
                                $('#request-product-qty').siblings('.validation-message').show().text("Please Enter Minimum 1 Quantity");
                            }
                        } else {
                            $('#request-product-link').siblings('.validation-message').show().text("Your url is Invalid");
                        }
                    } else {
                        $('#request-phone-number').siblings('.validation-message').show().text("Please enter a number");
                    }
                } else {
                    $('#request-email').siblings('.validation-message').show().text("Your email is Invalid");
                }
            } else {
                $('#request-full-name').siblings('.validation-message').show().text("Please enter full name");
            }
        } else if ($('#wooacd-visitor').val() === 'user') {
            //console.log('hi');
            if ($("#request-product-link").val() != '' && isValidURL($('#request-product-link').val()) === true) {
                if ($('#request-product-qty').val() >= 1 && isNumber($('#request-product-qty').val())) {
                    var product_link = $('#request-product-link').val();
                    var note = $('#request-product-note').val();
                    var quantity = $('#request-product-qty').val();
                    var req_data = {
                        'action': 'wooacd_submit_request',
                        'product_link': product_link,
                        'note': note,
                        'quantity': quantity
                    }
                    console.log(req_data);
                    var request = wooacdHelper.ajax(req_data);
                    request.done(function (response) {
                        $('#request-product-link').val('');
                        $('#request-product-note').val('');
                        $('#request-product-qty').val('1');
                        $('.wooacd-product-title').empty();
                        $('.custom-request-form').hide();
                        $('.custom-request-notice').show();
                        $('.custom-request-notice').empty();
                        $('.custom-request-notice').append(response.message);
                    });
                } else {
                    $('#request-product-qty').siblings('.validation-message').show().text("Please Enter Minimum 1 Quantity");
                }
            } else {
                $('#request-product-link').siblings('.validation-message').show().text("Your url is Invalid");
            }

        }
        $('.wooacd-loader').hide();
    });
    $(document).on('click', '.wooacd-custom-request-close', function (e) {
        e.preventDefault();
        //console.log('hello');
        $('.custom-request-form').show();
        $('.custom-request-notice').hide();
        $("body").removeClass("custom-request-active");
        $('.wooacd-product-title').remove();
        $("#request-full-name").val('');
        $("#request-email").val('');
        $("#request-phone_number").val('');
        $('#request-product-link').val('');
        $('#request-product-qty').val('1');
        $('#wooacd-product-link').val('');
    });
    // $('body').on('click', function (e) {
    //     //if clicked element is not your element and parents aren't your div
    //     if (e.target.id != 'custom-request-wrapper' && e.target.id != 'wooacd-custom-request' && $(e.target).parents('#custom-request-wrapper').length == 0) {
    //         if ($('.custom-request-active').length > 0) {
    //             $('.custom-request-form').show();
    //             $('.custom-request-notice').hide();
    //             $("body").removeClass("custom-request-active"); //custom-request-active
    //         }

    //     }
    // });
    $('.wooacd_toggle').on('click', function (e) {
        e.preventDefault();
        $(".wooacd_breakdown").toggle();

    });
    // var coll = document.getElementsByClassName("collapsible");
    // var i;
    // for (i = 0; i < coll.length; i++) {
    //     coll[i].addEventListener("click", function () {
    //         this.classList.toggle("active");
    //         var content = this.nextElementSibling;
    //         if (content.style.display === "block") {
    //             content.style.display = "none";
    //         } else {
    //             content.style.display = "block";
    //         }
    //     });
    // }
    // Add something to given element placeholder
    function addToPlaceholder(toAdd, el) {
        el.attr('placeholder', el.attr('placeholder') + toAdd);
        // Delay between symbols "typing" 
        return new Promise(resolve => setTimeout(resolve, 100));

    }

    // Clear placeholder attribute in given element
    function clearPlaceholder(el) {
        el.attr("placeholder", "");
    }

    // Print one phrase
    function printPhrase(phrase, el, phraseNum, itemIdex) {
        //console.log(phrase,phraseNum,itemIdex);
        return new Promise(resolve => {
            // Clear placeholder before typing next phrase
            clearPlaceholder(el);
            let letters = phrase.split('');
            // For each letter in phrase
            letters.reduce(
                (promise, letter, index) => promise.then(_ => {
                    // Resolve promise when all letters are typed
                    if (index === letters.length - 1) {
                        // Delay before start next phrase "typing"                        
                        setTimeout(resolve, 1000);
                        if (parseInt(itemIdex + 1) == phraseNum) {
                            run();
                        }
                    }
                    return addToPlaceholder(letter, el);
                }),
                Promise.resolve()
            );
        });
    }

    // Print given phrases to element
    function printPhrases(phrases, el) {
        // For each phrase
        // wait for phrase to be typed
        // before start typing next

        phrases.reduce((promise, phrase, index) => promise.then(_ => printPhrase(phrase, el, phrases.length, index)), Promise.resolve());
    }

    // Start typing
    //wooacd_custom_request_ajax_object.phrases
    function run() {
        // let phrases = [
        //     "Paste an item link to request or find (ex: https://www.amazon.com/XXXX/)",
        //     "Paste an item link to request or find (ex: https://www.ebay.com/XXXX/)"
        // ];
        //console.log(wooacd_custom_request_ajax_object.phrases);
        //console.log(phrases);
        //let phrases = wooacd_custom_request_ajax_object.phrases;
        //console.log(wooacd_custom_request_ajax_object.phrases);
        if (wooacd_custom_request_ajax_object.phrases.length != 0) {
            $.when($(document).find('#wooacd-product-link')).then(printPhrases(wooacd_custom_request_ajax_object.phrases, $('#wooacd-product-link')));
        }
        return;
    }
    //console.log(wooacd_custom_request_ajax_object.phrases);
    run();
    //var elm;
    function isValidURL(u) {
        var elm = document.createElement('input');
        elm.setAttribute('type', 'url');
        elm.value = u;
        return elm.validity.valid;
    }
    //var em
    function isValidEmail(e) {
        var em = document.createElement('input');
        em.setAttribute('type', 'email');
        em.value = e;
        return em.validity.valid;
    }

    function isNumber(n) {
        var num = document.createElement('input');
        num.setAttribute('type', 'number');
        num.value = n;
        return num.validity.valid;
    }

    function phoneNumber(p) {
        var tel = document.createElement('input');
        tel.setAttribute('type', 'tel');
        tel.value = p;
        return tel.validity.valid;
    }
});