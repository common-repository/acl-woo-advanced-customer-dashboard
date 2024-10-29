jQuery(function ($) {
    //Every tiny tools are implemented  in wooacdHelper as global object literal.
    //console.log(wooacd_ajax_object);
    var wooacdHelper = {
        ajax: function (data) {
            return jQuery.post(wooacd_ajax_object.ajax_url, data); //Ajax url,Data
        }
    }
})