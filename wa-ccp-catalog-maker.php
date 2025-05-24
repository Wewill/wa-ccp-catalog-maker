<?php

/*
Plugin Name: WA CCP Catalog Maker
Author: Justin Petermann
Version: 2.0
Requires at least: 4
Tested up to: 4.4
Stable tag: 1.0
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html
Description: PDF Maker for catalogue and others
*/

add_action('plugins_loaded', 'fct_wa_ccp_catalog_maker');

$ccppm = False;

function fct_wa_ccp_catalog_maker() {
	global $ccppm;
	require_once('includes/ccppm.inc.php');
	$ccppm = new ccppm();
	$ccppm->run();
}

function load_wp_media_files() {
	wp_enqueue_media();
}
add_action( 'admin_enqueue_scripts', 'load_wp_media_files' );
