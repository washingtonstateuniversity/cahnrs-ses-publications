<?php
/**
* Plugin Name: CAHNRS - SES Publications
* Plugin URI:  https://github.com/jweston491/ses-publications
* Description: Adds SES Publications Post Type
* Version:     1.0.0
* Author:      CAHNRS Communications, Efren Vasquez
* Author URI:  http://cahnrs.wsu.edu/communications/
* License:     Copyright Washington State University
* License URI: http://copyright.wsu.edu
*/

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

function cahnrs_ses_init(){
	require_once __DIR__ . '/includes/plugin.php';
}

add_action( 'after_setup_theme', 'cahnrs_ses_init' );

