/**
 * h5p Tools  JS
 *
 * @since      0.0.1
 * @package    rw-h5p-tools
 * @author     Frank Neumann-Staude <frank@staude.net>
 *
 */


jQuery(document).ready(function(){
    jQuery(document.body).on('click','.copy-h5p-frontend', function(e) {
        var data = {
            'action': 'copy_h5p',
            'id': jQuery(this).attr("data-h5p-id")
        };
        jQuery.post(my_ajax_object.ajax_url, data, function(response) {
            ret = response;
            obj = jQuery.parseJSON( ret );
            if ( obj.status == 'ok' ) {
                jQuery(location).attr('href', '/wp-admin/admin.php?page=h5p&task=show&id=' + obj.new_id );
            }

        });
    });
});

