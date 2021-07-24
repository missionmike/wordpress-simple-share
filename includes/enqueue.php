<?php

/**
 * @since 0.5
 * 
 * Enqueue any style and script dependencies here
 */

defined('ABSPATH') or die();

/**
 * @since 0.1
 * Register styles/scripts for admin
 */
function dts_smplshare_register_admin_scripts()
{
	$version = '20200109';

	// Some public styles + scripts are used on back-end
	wp_register_style('font-awesome', plugins_url() . '/dts-simple-share/public/css/brands.min.css', false, $version);
	wp_register_style('dts_ss_styles', plugins_url() . '/dts-simple-share/public/css/styles.css', false, $version);

	// Back-end specific styles + scripts
	wp_register_style('dts_ss_styles_admin', plugins_url() . '/dts-simple-share/admin/css/styles-admin.css', false, $version);
	wp_register_script('dts_ss_scripts_admin', plugins_url() . '/dts-simple-share/admin/js/scripts-admin.js', false, $version);
}
add_action('admin_init', 'dts_smplshare_register_admin_scripts');



/**
 * @since 0.1
 * Enqueue styles/scripts for admin
 */
function dts_smplshare_enqueue_admin_scripts()
{
	wp_enqueue_style('font-awesome');
	wp_enqueue_style('dts_ss_styles');
	wp_enqueue_style('dts_ss_styles_admin');
	wp_enqueue_script('jquery');
	wp_enqueue_script('jquery-ui-sortable');
	wp_enqueue_script('dts_ss_scripts_admin');
}
add_action('admin_enqueue_scripts', 'dts_smplshare_enqueue_admin_scripts');



/**
 * @since 0.1
 * Register styles/scripts for front-end
 */
function dts_smplshare_register_scripts()
{
	$version = '20200109';

	wp_register_style('font-awesome', plugins_url() . '/dts-simple-share/public/css/brands.min.css', false, $version);
	wp_register_style('dts_ss_styles', plugins_url() . '/dts-simple-share/public/css/styles.css', false, $version);
	wp_register_script('dts_ss_scripts', plugins_url() . '/dts-simple-share/public/js/scripts.js', false, $version);
}
add_action('init', 'dts_smplshare_register_scripts');



/**
 * @since 0.1
 * Enqueue styles/scripts for front-end
 */
function dts_smplshare_enqueue_scripts()
{
	wp_enqueue_style('font-awesome');
	wp_enqueue_style('dts_ss_styles');
	wp_enqueue_script('jquery');
	wp_enqueue_script('dts_ss_scripts');
}
add_action('wp_enqueue_scripts', 'dts_smplshare_enqueue_scripts');
