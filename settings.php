<?php
if ( ! defined( 'WPINC' ) ) {
	die('What are looking?');
}

function pf404_settings_init() {

 register_setting( 'pf404', 'pf404_options', 'pf404_reload_data' );
 
 add_settings_section(
 'pf404_section_developers',
 __( 'Start using PetFinder404', 'pf404' ),
 'pf404_section_developers_cb',
 'pf404'
 );
 
  add_settings_field(
 'pf404_field_explicitperm', 
 __( 'Allow link to PetFinder from 404 Page (Required for plugin to work)', 'pf404' ),
 'pf404_field_explicitperm_cb',
 'pf404',
 'pf404_section_developers',
 [
 'label_for' => 'pf404_field_explicitperm',
 'class' => 'pf404_row',
 'pf404_custom_data' => 'custom',
 ]
 );

  add_settings_field(
 'pf404_field_apikey', 
 __( 'PetFinder API Key', 'pf404' ),
 'pf404_field_apikey_cb',
 'pf404',
 'pf404_section_developers',
 [
 'label_for' => 'pf404_field_apikey',
 'class' => 'pf404_row',
 'pf404_custom_data' => 'custom',
 ]
 );


 add_settings_field(
 'pf404_field_animal', 
 __( 'Animal', 'pf404' ),
 'pf404_field_animal_cb',
 'pf404',
 'pf404_section_developers',
 [
 'label_for' => 'pf404_field_animal',
 'class' => 'pf404_row',
 'pf404_custom_data' => 'custom',
 ]
 );

add_settings_field(
 'pf404_field_heading', 
 __( '404 Page Heading', 'pf404' ),
 'pf404_field_heading_cb',
 'pf404',
 'pf404_section_developers',
 [
 'label_for' => 'pf404_field_heading',
 'class' => 'pf404_row',
 'pf404_custom_data' => 'custom',
 ]
 );

add_settings_field(
 'pf404_field_pagecontent', 
 __( '404 Page Content', 'pf404' ),
 'pf404_field_pagecontent_cb',
 'pf404',
 'pf404_section_developers',
 [
 'label_for' => 'pf404_field_pagecontent',
 'class' => 'pf404_row',
 'pf404_custom_data' => 'custom',
 ]
 );

}
 
add_action( 'admin_init', 'pf404_settings_init' );

//Used to kick off new pull from api since plugin settings were changed.
function pf404_reload_data ($input){
	
    petfinder404::load_api_data($input['pf404_field_apikey'], $input['pf404_field_animal']);         
	return $input;
}
 
function pf404_section_developers_cb( $args ) {
 ?>
 <p id="<?php echo esc_attr( $args['id'] ); ?>"><?php _e( 'You need a <a href="https://www.petfinder.com/developers/api-key" target="_blank" >free PetFinder API Key</a> to use this plugin.', 'pf404' ); ?></p>
 <?php
}
 
function pf404_field_explicitperm_cb( $args ) {

 $options = get_option( 'pf404_options' );

 ?>
 <input value="Yes" type="checkbox" id="<?php echo esc_attr( $args['label_for'] ); ?>"
 data-custom="<?php echo esc_attr( $args['pf404_custom_data'] ); ?>"
 name="pf404_options[<?php echo esc_attr( $args['label_for'] ); ?>]" <?php echo isset( $options[ $args['label_for'] ] ) ? ( 'checked' ) : ( '' ); ?> />

 <?php
}

function pf404_field_animal_cb( $args ) {

 $options = get_option( 'pf404_options' );

 ?>
 <select id="<?php echo esc_attr( $args['label_for'] ); ?>"
 data-custom="<?php echo esc_attr( $args['pf404_custom_data'] ); ?>"
 name="pf404_options[<?php echo esc_attr( $args['label_for'] ); ?>]"
 >
 <option value="dog" <?php echo isset( $options[ $args['label_for'] ] ) ? ( selected( $options[ $args['label_for'] ], 'dog', false ) ) : ( '' ); ?>>
 <?php esc_html_e( 'Dog', 'pf404' ); ?>
 </option>
 <option value="cat" <?php echo isset( $options[ $args['label_for'] ] ) ? ( selected( $options[ $args['label_for'] ], 'cat', false ) ) : ( '' ); ?>>
 <?php esc_html_e( 'Cat', 'pf404' ); ?>
 </option>
  <option value="smallfurry" <?php echo isset( $options[ $args['label_for'] ] ) ? ( selected( $options[ $args['label_for'] ], 'smallfurry', false ) ) : ( '' ); ?>>
 <?php esc_html_e( 'Small Furry', 'pf404' ); ?>
 </option>
 <option value="bird" <?php echo isset( $options[ $args['label_for'] ] ) ? ( selected( $options[ $args['label_for'] ], 'bird', false ) ) : ( '' ); ?>>
 <?php esc_html_e( 'Bird', 'pf404' ); ?>
 </option>
  <option value="horse" <?php echo isset( $options[ $args['label_for'] ] ) ? ( selected( $options[ $args['label_for'] ], 'horse', false ) ) : ( '' ); ?>>
 <?php esc_html_e( 'Horse', 'pf404' ); ?>
 </option>
 <option value="reptile" <?php echo isset( $options[ $args['label_for'] ] ) ? ( selected( $options[ $args['label_for'] ], 'reptile', false ) ) : ( '' ); ?>>
 <?php esc_html_e( 'Reptile', 'pf404' ); ?>
 </option>
  <option value="barnyard" <?php echo isset( $options[ $args['label_for'] ] ) ? ( selected( $options[ $args['label_for'] ], 'barnyard', false ) ) : ( '' ); ?>>
 <?php esc_html_e( 'Barnyard', 'pf404' ); ?>
 </option>
 </select>

 <?php
}

function pf404_field_apikey_cb( $args ) {

 $options = get_option( 'pf404_options' );

 ?>
 <input value="<?php echo esc_attr($options[ $args['label_for'] ]) ?>" type="text" id="<?php echo esc_attr( $args['label_for'] ); ?>"
 data-custom="<?php echo esc_attr( $args['pf404_custom_data'] ); ?>"
 name="pf404_options[<?php echo esc_attr( $args['label_for'] ); ?>]" />

 <?php
}
 
 function pf404_field_heading_cb( $args ) {

 $options = get_option( 'pf404_options' );

 ?>
 <input value="<?php echo esc_attr($options[ $args['label_for'] ]) ?>" type="text" id="<?php echo esc_attr( $args['label_for'] ); ?>"
 data-custom="<?php echo esc_attr( $args['pf404_custom_data'] ); ?>"
 name="pf404_options[<?php echo esc_attr( $args['label_for'] ); ?>]" />

 <?php
}

function pf404_field_pagecontent_cb( $args ) {

 $options = get_option( 'pf404_options' );

 ?>
 <textarea rows="5" cols="100" id="<?php echo esc_attr( $args['label_for'] ); ?>" 
 data-custom="<?php echo esc_attr( $args['pf404_custom_data'] ); ?>"
 name="pf404_options[<?php echo esc_attr( $args['label_for'] ); ?>]" ><?php echo esc_attr($options[ $args['label_for'] ]) ?></textarea>

 <?php
}
/**
 * top level menu
 */
function pf404_options_page() {
 // add top level menu page
 add_submenu_page(
 	'options-general.php',
 'PF404 for PetFinder Settings',
 'PF404 for PetFinder',
 'manage_options',
 'pf404',
 'pf404_options_page_html'
 );
}
 
add_action( 'admin_menu', 'pf404_options_page' );
 
add_action( 'admin_post_pf404', 'am_our_action_hook_function' );
function am_our_action_hook_function() {
    
    die( 'Missing target.' );
}

function pf404_options_page_html() {
 
 if ( ! current_user_can( 'manage_options' ) ) {
 return;
 } 
 
 // show error/update messages
 settings_errors( 'pf404_messages' );
 ?>
 <div class="wrap">
 <h1><?php echo esc_html( get_admin_page_title() ); ?></h1>
 <form action="<?php echo admin_url( 'options.php' ); ?>" method="post">
 <?php wp_nonce_field(); ?>
 <?php
 // output security fields for the registered setting "pf404"
 settings_fields( 'pf404' );
 // output setting sections and their fields
 // (sections are registered for "pf404", each field is registered to a specific section)
 do_settings_sections( 'pf404' );
 // output save settings button
 submit_button( 'Save Settings' );
 
 ?>
 </form>
 </div>
 <?php
} ?>