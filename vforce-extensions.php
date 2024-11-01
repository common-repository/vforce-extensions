<?php
/**
 * Plugin Name: VForce Extensions
 * Description: Plugin to streamline VForce integration
 * Version: 1.0.3
 * Author: Virtual Inc
 * Author URI: https://virtualinc.com
 * License: GPL2
 */
 
/*  Copyright 2020  Virtual Inc (email webadmin@virtualinc.com)

    This program is free software; you can redistribute it and/or modify
    it under the terms of the GNU General Public License, version 2, as 
    published by the Free Software Foundation.

    This program is distributed in the hope that it will be useful,
    but WITHOUT ANY WARRANTY; without even the implied warranty of
    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
    GNU General Public License for more details.

    You should have received a copy of the GNU General Public License
    along with this program; if not, write to the Free Software
    Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
*/

// include our vforce_create_input_element function
include 'inc/functions.php';
include 'inc/admin-page.php';
include 'inc/formassembly_shortcodes.php';
include 'inc/vforce_hooks_before_page_load.php';
global $vforce_helper;
$vforce_helper = get_option('vforce_helper');

add_filter( 'template_include', 'vforce_set_template_include_variable', 1000 );
function vforce_set_template_include_variable( $t )
{
    $GLOBALS['current_theme_template'] = basename($t);
    return $t;
}

function vforce_get_current_template_url( $echo = false )
{
    if( !isset( $GLOBALS['current_theme_template'] ) )
        return false;
    if( $echo )
        echo $GLOBALS['current_theme_template'];
    else
        return $GLOBALS['current_theme_template'];
}

// This simply cleans up the WP admin bar slightly and adds a section to show which page template is currently in use.
function vforce_update_admin_bar($wp_adminbar) {
  $vforce_helper = get_option('vforce_helper');

  // remove unnecessary items
  $wp_adminbar->remove_node('wp-logo');
  // Uncommenting the below line will remove WP Engine Quick Links, and possibly other things from the admin bar.
  // $wp_adminbar->remove_node('customize');
  $wp_adminbar->remove_node('comments');

  // add current theme file debug output menu item
  $wp_adminbar->add_node([
    'id' => 'virtualinc',
    'title' => 'Current Theme File - ' . vforce_get_current_template_url() .'',
    'href' => '#',
  ]);

  $associationId = ($vforce_helper['variable']['association-id'] && $vforce_helper['variable']['association-id'] != '') ? 'Association ID: ' . $vforce_helper['variable']['association-id'] : 'Click here to set Association Id';
  $wp_adminbar->add_menu( array( 'id'    => 'vforce_extensions_admin_bar', 'title' => 'VForce Settings' ) );
  $wp_adminbar->add_menu( array( 'id'	=> 'vforce_extensions_admin_bar_status','parent' => 'vforce_extensions_admin_bar', 'title'  => $associationId ) );


}
// admin_bar_menu hook
add_action('admin_bar_menu', 'vforce_update_admin_bar', 999);
// Add a menu page
function vforce_settings_plugin_menu()
{
	$vforce_admin_page = add_menu_page ('VForce Settings', 'VForce Settings', 'manage_options', 'vforce-settings', 'vforce_create_settings_page');
}
add_action('admin_menu', 'vforce_settings_plugin_menu');

// now load the scripts we need
function vforce_load_variables_and_scripts ($hook)
{
  $vforce_helper = get_option('vforce_helper');
  // Load our global js file.  Any variables in this file will be available sitewide
  wp_enqueue_script( 'init-global', plugin_dir_url( __FILE__ ) . 'inc/js/init-global.js', array('jquery'), '1.0', true );
  $scripts = $vforce_helper['script'];

  if($vforce_helper['script'])
  {
    foreach($vforce_helper['script'] as $key => $value )
    {
      $scptPath = explode('_', $key);
      if($value != 'false'){
        wp_enqueue_script( $key, plugin_dir_url( __FILE__ ) . 'inc/' . $scptPath[0] . '/js/' . $scptPath[1] . '.js', array('jquery'), '1.0', true );
  
      }
    }
  }
  wp_localize_script( 'init-global', 'vforce_helper', $vforce_helper );

}

// and make sure it loads with our custom script
add_action('wp_enqueue_scripts', 'vforce_load_variables_and_scripts');
add_action('admin_enqueue_scripts', 'vforce_load_variables_and_scripts');


function vforce_load_bootstrap_js()
{
    // link some styles to the admin page
  if(get_admin_page_title() === 'VForce Settings') {
    $vforce_stylesheet = plugins_url ('style.css', __FILE__);
    wp_enqueue_style ('vforce_stylesheet', $vforce_stylesheet );
  }
}
add_action('admin_enqueue_scripts', 'vforce_load_bootstrap_js');

add_action( 'admin_footer', 'vforce_process_setting_update_ajax' ); // Write our JS below here

function vforce_process_setting_update_ajax() { ?>
<script type="text/javascript">


function updateVforceSetting(setting, value, optionType) {

  var data = {
    'action': 'update_vforce_setting',
    'option_name': setting,
    'option_value': value,
    'setting_type': optionType
  }

  <!-- toggleHiddenFields() -->
  jQuery.post(ajaxurl, data, function (response) {
    ajaxSuccessMsg(response)
  });
}

 function toggleHiddenFields(){
  if (vforce_helper[optionType] && vforce_helper[optionType][setting] && vforce_helper[optionType][setting] == 'true') {
    jQuery(setting ).show()
  } else {
    jQuery('.formidable-product-selector-wrapper').hide()
  }
}

  function ajaxSuccessMsg(response) {
    let res = JSON.parse(response)
      let toast = jQuery('.toast')
      
      jQuery('.toast-status').text(res.status)
      jQuery('.toast').show('fadein')
      setTimeout(function(){ jQuery('.toast').hide('fadeout') }, 5000);
  }


</script> 
<?php }

add_action( 'wp_ajax_update_vforce_setting', 'vforce_update_org_settings' );

function vforce_update_org_settings()
{

  $action = sanitize_text_field($_POST['action']);
  $option_name = sanitize_text_field($_POST['option_name']);
  $option_value = sanitize_text_field($_POST['option_value']);
  $setting_type = sanitize_text_field($_POST['setting_type']);

  $settings = get_option('vforce_helper');
  $settings[$setting_type][$option_name] = $option_value;
  update_option('vforce_helper', $settings);
    $response = array(
      'value' => get_option($option_name),
      'status' => 'Success'
    );
    return json_encode($response);
  
	wp_die(); // this is required to terminate immediately and return a proper response
}