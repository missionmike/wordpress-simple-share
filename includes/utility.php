<?php
/**
 * @since 0.5
 * 
 * Utility functions here
 */

defined( 'ABSPATH' ) or die( 'No script kiddies please!' );



/**
 * @since 0.5
 * Check if URL is /amp/ page
 */
function dts_smplshare_is_amp_url( $amp_enabled_slugcheck = false ) {
	
	error_log( $amp_enabled_slugcheck );
	
	/**
	 * @since 0.5
	 * Support for AMP for WordPress plugin
	 * @link https://amp-wp.org/
	 */
	if( function_exists( 'is_amp_endpoint' ) && is_amp_endpoint() ) {
		return true;
	}

	/**
	 * @since 0.5
	 * Support for AMPforWP plugin
	 * @link https://ampforwp.com/
	 */
	if( function_exists( 'ampforwp_is_amp_endpoint' ) && ampforwp_is_amp_endpoint() ) {
		return true;
	}

	/**
	 * @since 0.5
	 * Fall-back catch-all for any URL ending with /amp/
	 */
	if ( $amp_enabled_slugcheck === true && substr( $_SERVER['REQUEST_URI'], -5 ) === '/amp/' ) {
		return true;
	}

	return false;
}



/**
 * @since 0.1
 * Utility function to convert HTML breaks to newline characters for use in Email formatting
 * 
 * @param string $content
 */
function dts_smplshare_html_to_nl( $content = '' ) {
	
	$breaks = array( "<br />", "<br>", "<br/>", "<br />", "&lt;br /&gt;", "&lt;br/&gt;", "&lt;br&gt;" );
	$content = str_ireplace( $breaks, "\r\n", $content );

	return $content;
}
