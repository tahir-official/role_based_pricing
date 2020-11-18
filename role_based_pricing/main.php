<?php
/**
 * Plugin Name: Role-Based Pricing (Woocommerce)
 * Plugin URI: https://www.bitcot.com/
 * Description: This plugin added new role for woocommerce product.
 * Version: 1.0
 * Author: tahir mansuri
 * Author URI: https://www.bitcot.com/
 */
include( plugin_dir_path( __FILE__ ) . 'include/function.php');
include( plugin_dir_path( __FILE__ ) . 'include/admin_setting.php');
register_activation_hook( __FILE__, 'rbr_plugin_activate' );
function rbr_plugin_activate(){

    // Require parent plugin
    if ( ! is_plugin_active( 'woocommerce/woocommerce.php' ) and current_user_can( 'activate_plugins' ) ) {
        // Stop activation redirect and show error
        wp_die('Sorry, but this plugin requires the woocommerce Plugin to be installed and active. <br><a href="' . admin_url( 'plugins.php' ) . '">&laquo; Return to Plugins</a>');
    }
    if(!role_exists( 'distributor' ) ) {
  		add_role(
		    'distributor',
		    __( 'Distributor', 'testdomain' ),
		    array(
		        'read'         => true,  // true allows this capability
		        //'edit_posts'   => true,
		        //'delete_posts' => false, // Use false to explicitly deny
		    )
		);
    }


}

register_deactivation_hook( __FILE__, 'rbr_plugin_deactivate' );
function rbr_plugin_deactivate() {
     global $wpdb;

     $wp_roles = new WP_Roles();
     $wp_roles->remove_role("distributor");
    
}
/*check woocommerce plugin active or not*/


function rbr_include_script() {
 
    if ( ! did_action( 'wp_enqueue_media' ) ) {
        wp_enqueue_media();
    }
  
    wp_enqueue_script( 'rbr',  plugin_dir_url( __FILE__ ).'js/rbrscript.js', array('jquery'), null, false );
}
add_action( 'admin_enqueue_scripts', 'rbr_include_script' );