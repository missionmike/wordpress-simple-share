<?php
/**
 * @since 0.5
 * 
 * AJAX endpoint functions here
 */

defined( 'ABSPATH' ) or die( 'No script kiddies please!' );

/**
 * @since 0.2
 * Update sort order via AJAX
 */
function dts_smplshare_setorder() {

	if ( current_user_can( 'manage_options' ) ) :

		$data = isset( $_POST['data'] ) ? $_POST['data'] : '0';

		if ( strlen( $data ) > 0 ) :

			$data = stripcslashes( $data );
			$data = json_decode( $data );

			$options = get_option( 'dts_smplshare_settings' );

			if ( ! empty( $options ) ) :

				$options['dts_order'] = $data;

			else :

				$options = array(
					'dts_order' => $data
				);

			endif;

			update_option( 'dts_smplshare_settings', $options );

		endif;

		echo 'success';

	else :

		echo 'Error: Permission denied.';

	endif;

	die();
}
add_action( 'wp_ajax_dts_smplshare_setorder', 'dts_smplshare_setorder' );
