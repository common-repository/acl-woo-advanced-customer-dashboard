jQuery(function ($) {
    //Every tiny tools are implemented  in wooacdHelper as global object literal.
    //console.log(wooacd_ajax_object);
    var wooacdHelper = {
        ajax: function (data) {
            return jQuery.post(wooacd_ajax_object.ajax_url, data); //Ajax url,Data
        }
    }
    $(document).ready(function () {
        $(".notify-status").on('click', function (e) {
            //e.preventDefault();
            console.log('aaaa');
            var nid = $(this).attr('nid');
            var req_data ={
                'action' : 'change_notification_status',
                'nid' : nid
            } 
            console.log(req_data);
            var request = wooacdHelper.ajax(req_data);
            // request.done(function (response) {
            //     console.log(response);

            // })
                
            
        });
    });
});