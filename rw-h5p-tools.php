<?php
/**
 * H5P Tools
 *
 * @author    Frank Neumann-Staude <frank@staude.net>
 * @license   GPL2
 * @link      https://staude.net
 * @copyright 2020 Frank Neumann-Staude
 *
 * Plugin Name:       H5P-Tools
 * Plugin URI:        https://staude.net
 * Description:       rpi Virtuell OER creation tools
 * Version:           0.0.1
 * Author:            Frank Neumann-Staude
 * Author URI:        https://staude.net
 * Text Domain:       rw-h5p-tools
 * License:           GPL2
 * License URI:       https://opensource.org/licenses/GPL-2.0
 * Domain Path:       /languages
 */

function h5p_tools_register_admin_plugin_styles() {
	wp_enqueue_script( 'rw-h5p-tools-admin-js',  plugin_dir_url( __FILE__ ) . 'js/rw-h5p-tools-admin.js' );
}
add_action('admin_enqueue_scripts','h5p_tools_register_admin_plugin_styles' );

function h5p_tools_register_plugin_styles() {
	wp_enqueue_script( 'rw-h5p-tools-js',  plugin_dir_url( __FILE__ ) . 'js/rw-h5p-tools.js' );
	wp_localize_script( 'rw-h5p-tools-js', 'my_ajax_object',
		array( 'ajax_url' => admin_url( 'admin-ajax.php' ) ) );
}
add_action('wp_enqueue_scripts','h5p_tools_register_plugin_styles' );



function my_action_callback_copy_h5p() {
	global $wpdb;
	/*
	 * @todo !!! NONCE prüfen
	 */
	$id = (int) sanitize_text_field( $_REQUEST['id'] );
	$action = sanitize_text_field( $_REQUEST['action'] );
	$user = get_current_user_id();
	$kopie_nr = rand( 1000, 9999);
	if ( $action == "copy_h5p" and is_int( $id) ) {
		//
		// @todo check ob h5p reusable ist, sonst darf "eigentlich" nicht kopiert werden
		//

		$wpdb->query(
			$wpdb->prepare("insert into `".$wpdb->prefix."h5p_contents`( created_at,updated_at,user_id,title,library_id,parameters,filtered,slug,embed_type,disable,content_type,authors,source,year_from,year_to,license,license_version,license_extras,author_comments,changes,default_language) 
select created_at,NOW() as updated_at,%d as user_id,concat ( title, ' Kopie %d') as title,library_id,parameters,filtered,slug,embed_type,disable,content_type,authors,source,year_from,year_to,license,license_version,license_extras,author_comments,changes,default_language from ".$wpdb->prefix."h5p_contents where id = %d", $user , $kopie_nr, $id
			)
		);
		$new_id = $wpdb->insert_id;

		// Media kopieren
		$plugin = H5P_Plugin::get_instance();
		$h5pstorage = $plugin->get_h5p_instance('storage');
		$h5pstorage->copyPackage($new_id, $id );

		// @TODO Fehler prüfen
		$return = array(
			'new_id' => $new_id,
			'old_id' => $id,
			'status' => 'ok',
		);
		echo json_encode( $return );
	}
	wp_die();
}

add_action( 'wp_ajax_copy_h5p',  'my_action_callback_copy_h5p' );

function add_copy_button( $id ) {
	if (is_user_logged_in() ) {
		echo "<button class='copy-h5p-frontend' data-h5p-id='" . $id . "'>Kopieren</button>";
	}
}
add_action( 'rw_h5p_block', 'add_copy_button', 10,1 );