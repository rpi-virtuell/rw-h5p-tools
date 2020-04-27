/**
 * h5p Tools Admin JS
 *
 * @since      0.0.1
 * @package    rw-h5p-tools
 * @author     Frank Neumann-Staude <frank@staude.net>
 *
 */


jQuery(document).ready(function(){
    //
    // ! Geht nicht, das JS von H5p wird erst dynamisch reingerendert, das ist nach document.ready noch nicht vorhanden !
    //
    var url = document.URL;
    if ( url.indexOf( 'admin.php?page=h5p') !== -1  ) {
        jQuery("#h5p-contents > table > tbody").find("tr").each(function(){
            alert( "4");
            jQuery(this).find('td').eq(0).after('<td>test</td>');
        });

    }


    // h5p WP Backend Ansicht eines Materials. Beispiel: /wp-admin/admin.php?page=h5p&task=show&id=6
    if ( url.indexOf( 'admin.php?page=h5p&task=show') !== -1 ) {

        // @TODO wpnoce ins js einschleifen, security!

        // Kopieren Button oben im H2 hinzufügen
        var id= getUrlVars()["id"];
        jQuery("#wpbody-content > div.wrap > h2").append("<a class='add-new-h2 copy-h5p-detail-view' data-h5p-id='" + id + "'>Kopieren</a>");
    }

    jQuery(document.body).on('click','.copy-h5p-detail-view', function(e) {
        // Kopierbutton in Detailansicht wurde geklickt
        var data = {
            'action': 'copy_h5p',
            'id': jQuery(this).attr("data-h5p-id")
        };
        jQuery.post(ajaxurl, data, function(response) {
            ret = response;
            obj = jQuery.parseJSON( ret );
            if ( obj.status == 'ok' ) {
                jQuery(location).attr('href', '/wp-admin/admin.php?page=h5p&task=show&id=' + obj.new_id );
            }

        });




    });
});



//
//
// Helper
//
//

function getUrlVars() {
    var vars = {};
    var parts = window.location.href.replace(/[?&]+([^=&]+)=([^&]*)/gi, function(m,key,value) {
        vars[key] = value;
    });
    return vars;
}

