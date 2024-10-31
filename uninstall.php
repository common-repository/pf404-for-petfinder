<?php

/**
 * Fired only when PF404 for PetFinder unistalled
 *
 *
 */

// If uninstall not called from WordPress, then exit. That's it!

if ( ! defined( 'WP_UNINSTALL_PLUGIN' ) ) {
	exit;
}

// Delete plugin options
if( get_option( 'pf404_field_heading' ) ) {
	delete_option( 'pf404_field_heading' );
}
if( get_option( 'pf404_field_pagecontent' ) ) {
	delete_option( 'pf404_field_pagecontent' );
}
if( get_option( 'pf404_field_animal' ) ) {
	delete_option( 'pf404_field_animal' );
}
if( get_option( 'pf404_field_apikey' ) ) {
	delete_option( 'pf404_field_apikey' );
}
if( get_option( 'pf404_field_explicitperm' ) ) {
	delete_option( 'pf404_field_explicitperm' );
}



// Drop tables
global $wpdb;
$wpdb->query( "DROP TABLE IF EXISTS " . $wpdb->prefix . "petfinder_404" );

/******* The end. Thanks for using 404 to 301 plugin ********/
