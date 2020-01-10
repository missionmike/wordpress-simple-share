<?php
/**
 * @since 0.5
 * 
 * HTML output functions + shortcode processing
 */

defined( 'ABSPATH' ) or die( 'No script kiddies please!' );

/**
 * Add shortcode for output
 */
function dts_smplshare_shortcodes_init() {

	function dts_smplshare_shortcode_sharebar( $atts, $content = '' ) {

		$options = get_option( 'dts_smplshare_settings' );
		
		if ( empty( $options ) ) {
			return $content;
		}

		$smpl_sharers = dts_smplshare_get_data();

		// Set defaults
		$style	= empty( $options['dts_smplshare_sharebar_style'] ) ? 'dts_sharebar_style_v1' : $options['dts_smplshare_sharebar_style'];
		$class 	= empty( $atts['class'] ) ? '' : $atts['class'];
		$id 	= empty( $atts['id'] ) ? '' : ' id="' . $atts['id'] . '" ';

		$amp_enabled 			= isset( $options['dts_smplshare_settings_enable_amp'] ) ? (bool) $options['dts_smplshare_settings_enable_amp'] : false;
		$amp_enabled_slugcheck 	= isset( $options['dts_smplshare_settings_enable_amp_slugcheck'] ) ? (bool) $options['dts_smplshare_settings_enable_amp_slugcheck'] : false;

		$atts = array(
			'amp_enabled' => $amp_enabled,
			'amp_enabled_slugcheck' => $amp_enabled_slugcheck,
		);

		$sharebar = '<div ' . $id . ' class="dts_smplshare_container ' . $class . ' ' . $style . '">';

		if ( ! empty( $options['dts_order'] ) ) :

			$order = $options['dts_order'];

			foreach( $order as $key ) :
				
				if ( ! isset( $smpl_sharers[$key] ) ) {
					continue;
				}

				$setting_option = 'dts_smplshare_' . $smpl_sharers[$key]['name'];

				if ( ! empty( $options ) && ( ! isset( $options[$setting_option] ) || $options[$setting_option] !== '1' ) ) {
					continue;
				}

				$sharebar .= dts_smplshare_icon_html( $smpl_sharers[$key], $atts );

			endforeach;

		else :

			foreach ( $smpl_sharers as $smpl_sharer ) :

				$setting_option = 'dts_smplshare_' . $smpl_sharer['name'];

				if ( ! empty( $options ) && ( ! isset( $options[$setting_option] ) || $options[$setting_option] !== '1' ) ) {
					continue;
				}

				$sharebar .= dts_smplshare_icon_html( $smpl_sharer, $atts );

			endforeach;

		endif;

		$sharebar .= '</div>';
		
		return $sharebar;
	}
	add_shortcode('dts_sharebar', 'dts_smplshare_shortcode_sharebar');

}
add_action( 'init', 'dts_smplshare_shortcodes_init' );



/**
 * Admin sharebar preview
 */
function dts_smplshare_shortcode_sharebar_preview( $atts, $content = '' ) {

	$options = get_option( 'dts_smplshare_settings' );

	$options = is_array( $options ) ? $options : array();

	if ( empty( $options['dts_smplshare_sharebar_style'] ) ) {
	  	$style = 'dts_sharebar_style_v1';
	} else {
		$style = $options['dts_smplshare_sharebar_style'];
	}

	$smpl_sharers = dts_smplshare_get_data();

	// Defaults
	$class 	= empty( $atts['class'] ) ? '' : $atts['class'];
	$id 	= empty( $atts['id'] ) ? '' : ' id="' . $atts['id'] . '" ';
	
	// Enable preview mode
	$atts = array(
		'preview' => true
	);

	$sharebar = '<div ' . $id . ' class="dts_smplshare_container ' . $class . ' ' . $style . '">';
	$sharebar .= '<ul id="dts-sortable">';

	if ( ! empty( $options['dts_order'] ) ) :

		$order = $options['dts_order'];

		foreach( $order as $key ) :
			
			if ( ! isset( $smpl_sharers[$key] ) ) {
				continue;
			}

			$sharebar .= dts_smplshare_icon_html( $smpl_sharers[$key], $atts );

		endforeach;

		foreach( $smpl_sharers as $smpl_sharer ) :

			if ( ! in_array( $smpl_sharer['name'], $order ) ) {
				$sharebar .= dts_smplshare_icon_html( $smpl_sharer, $atts );
			}

		endforeach;

	else :

		foreach ( $smpl_sharers as $smpl_sharer ) {
			$sharebar .= dts_smplshare_icon_html( $smpl_sharer, $atts );
		}

	endif;

	$sharebar .= '</ul>';
	$sharebar .= '</div>';

	return $sharebar;		
}



/**
 * @since 0.2
 * Generate HTML for share icons
 * 
 * @param $smpl_sharer array
 * @param $atts array
 * @return $html string
 */
function dts_smplshare_icon_html( $smpl_sharer = array(), $atts = array() ) {

	if ( ! isset( $smpl_sharer['url'] ) || ! isset( $smpl_sharer['action'] ) || ! isset( $smpl_sharer['name'] ) ) {
		return;
	}

	// Preview is off by default
	$atts['preview'] = isset( $atts['preview'] ) ? $atts['preview'] : false;
	
	// AMP is off by default
	$atts['amp_enabled'] = isset( $atts['amp_enabled'] ) ? $atts['amp_enabled'] : false;

	// AMP catchall is off by default
	$atts['amp_enabled_slugcheck'] = isset( $atts['amp_enabled_slugcheck'] ) ? $atts['amp_enabled_slugcheck'] : false;

	// Set default Short string value
	$short = isset( $smpl_sharer['short'] ) ? $smpl_sharer['short'] : 'Share';

	$html = '';

	if ( $atts['preview'] === false ) {

		if ( dts_smplshare_is_amp_url( $atts['amp_enabled_slugcheck'] ) === true ) {
			
			if ( ( $atts['amp_enabled_slugcheck'] === true || $atts['amp_enabled'] === true ) && isset( $smpl_sharer['amp'] ) && (string) $smpl_sharer['amp'] === '1' ) {
				$html .= '<amp-social-share type="' . $smpl_sharer['name'] . '"></amp-social-share>';
			}

		} else {
				
			$html .= '<a class="dts_smplshare dts_smplshare_sharelink" href="' . $smpl_sharer['url'] . '" target="_blank" title="' . $smpl_sharer['action'] . '" data-name="' . $smpl_sharer['name'] . '">';
			$html .=	'<span class="dts_smplshare_icon_container ' . $smpl_sharer['name'] . '" title="' . $smpl_sharer['action'] . '"></span>';
			$html .=	'<span class="dts_smplshare_icon_desc_container ' . $smpl_sharer['name'] . '" title="' . $smpl_sharer['action'] . '">' . $short . '</span>';
			$html .= '</a>';

		}
	
	} else {

		$html .= '<li class="sortable" data-name="' . $smpl_sharer['name'] . '">';
		$html .= '<a class="dts_smplshare" data-name="' . $smpl_sharer['name'] . '" href="#" title="' . $smpl_sharer['action'] . '">';
		$html .=	'<span class="dts_smplshare_icon_container ' . $smpl_sharer['name'] . '" title="' . $smpl_sharer['action'] . '"></span>';
		$html .=	'<span class="dts_smplshare_icon_desc_container ' . $smpl_sharer['name'] . '" title="' . $smpl_sharer['action'] . '">' . $short . '</span>';
		$html .= '</a>';
		$html .= '</li>';

	}

	return $html;
}



/**
 * @since 0.1
 * Add sharebar to post/page header/footer depending on settings
 */
function dts_smplshare_sharebar_auto( $content ) {

	global $post;

	$options = get_option( 'dts_smplshare_settings' );

	if ( empty( $options ) ) {
		return $content;
	}

	$setting_option = 'dts_post_types_' . $post->post_type;

	if ( empty( $options[$setting_option] ) || $options[$setting_option] !== '1' ) {
		return $content;
	}

	if ( !empty( $options['dts_smplshare_placement_top'] ) && $options['dts_smplshare_placement_top'] === '1' ) {
		$content = do_shortcode( '[dts_sharebar]' ) . $content;
	}

	if ( !empty( $options['dts_smplshare_placement_bottom'] ) && $options['dts_smplshare_placement_bottom'] === '1' ) {
		$content .= do_shortcode( '[dts_sharebar]' );
	}

	return $content;
}
add_filter( 'the_content', 'dts_smplshare_sharebar_auto' );
