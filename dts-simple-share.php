<?php
   /*
   Plugin Name: DT's Simple Share
   Plugin URI: https://dtweb.design/simple-share/
   Description: Simple social media/email sharebar. Add shortcode [dts_sharebar] wherever you want them to show up!
   Version: 0.0.1
   Author: Michael R. Dinerstein
   Author URI: https://dtweb.design/
   License: GPL2
   */
   
defined( 'ABSPATH' ) or die( 'No script kiddies please!' );



/**
 * Register and enqueue styles/scripts for admin
 */
function dts_smplshare_register_admin_scripts() {
    $version = '07192017';

    wp_register_style( 'dts_ss_styles_admin', plugins_url( 'css/styles-admin.css', __FILE__ ), false, $version );
    wp_register_script( 'dts_ss_scripts_admin', plugins_url( 'js/scripts-admin.js', __FILE__ ), false, $version );
}
add_action( 'admin_init', 'dts_smplshare_register_admin_scripts' );

function dts_smplshare_enqueue_admin_scripts() {
    wp_enqueue_style( 'dts_ss_styles_admin' );
    wp_enqueue_script( 'jquery' );
    wp_enqueue_script( 'dts_ss_scripts_admin' );
}
add_action( 'admin_enqueue_scripts', 'dts_smplshare_enqueue_admin_scripts' );



/**
 * Register and enqueue styles/scripts for front-end
 */
function dts_smplshare_register_scripts() {
    $version = '07192017';

    wp_register_style( 'dts_ss_styles', plugins_url( 'css/styles.css', __FILE__ ), false, $version );
    wp_register_script( 'dts_ss_scripts', plugins_url( 'js/scripts.js', __FILE__ ), false, $version );
}
add_action( 'init', 'dts_smplshare_register_scripts' );

function dts_smplshare_enqueue_scripts() {
    wp_enqueue_style( 'dts_ss_styles' );
    wp_enqueue_script( 'jquery' );
    wp_enqueue_script( 'dts_ss_scripts' );
}
add_action( 'wp_enqueue_scripts', 'dts_smplshare_enqueue_scripts' );



/**
 * Action links on plugin page:
 * Add 'Settings' Link
 */
function dts_smplshare_action_links( $actions, $plugin_file ) {
    static $plugin;

    if ( !isset( $plugin ) )
        $plugin = plugin_basename( __FILE__ );
    if ( $plugin === $plugin_file ) :

        $settings = array(
            'settings' => '<a href="' . esc_url( get_admin_url( null, 'options-general.php?page=dts-simple-share' ) ) . '">' . __('Settings', 'General') . '</a>'
        );
        $actions = array_merge( $settings, $actions );

    endif;

    return $actions;
}
add_filter( 'plugin_action_links', 'dts_smplshare_action_links', 10, 5 );



/**
 * Add options on plugin activation
 */
function dts_smplshare_activate() {
    add_option( 'dts_smplshare_settings' );
}
register_activation_hook( __FILE__, 'dts_smplshare_activate' );



/**
 * Remove plugin-specific options on plugin deactivation
 */
function dts_smplshare_remove() {
    delete_option( 'dts_smplshare_settings' );
}
register_deactivation_hook( __FILE__, 'dts_smplshare_remove' );



/**
 * Init plugin on admin_init
 */
function dts_smplshare_init() {

    load_plugin_textdomain('dts-simple-share', false, basename( dirname( __FILE__ ) ) . '/languages' );
    register_setting( 'dts_smplshare_settings', 'dts_smplshare_settings', 'dts_smplshare_settings_validate' );


   /**
     * Section: Share bar placement
     */
    function dts_smplshare_settings_placement_text() {
        echo '<p>Select placement for sharebar on posts/pages (if all unchecked, the shortcode [dts_sharebar] will still work manually)</p>';
    }
    add_settings_section( 'dts_smplshare_settings_placement', __( 'Sharebar Placement', 'dts-simple-share' ), 'dts_smplshare_settings_placement_text', 'dts_smplshare_settings' );

    $dts_smplshare_settings_show_on_top = function() {
        $options = get_option( 'dts_smplshare_settings' );
        $setting_name = 'dts_smplshare_placement_top';
        $options[$setting_name] = isset( $options[$setting_name] ) ? $options[$setting_name] : false;
        ?>
        <input type="checkbox" name="dts_smplshare_settings[<?= $setting_name; ?>]" value="1" <?php checked( $options[$setting_name], 1); ?> />
        <?php
    };
    add_settings_field( 'dts_smplshare_settings_show_on_top', 'Top of page/post', $dts_smplshare_settings_show_on_top, 'dts_smplshare_settings', 'dts_smplshare_settings_placement' );

    $dts_smplshare_settings_show_on_bottom = function() {
        $options = get_option( 'dts_smplshare_settings' );
        $setting_name = 'dts_smplshare_placement_bottom';
        $options[$setting_name] = isset( $options[$setting_name] ) ? $options[$setting_name] : false;
        ?>
        <input type="checkbox" name="dts_smplshare_settings[<?= $setting_name; ?>]" value="1" <?php checked( $options[$setting_name], 1); ?> />
        <?php
    };
    add_settings_field( 'dts_smplshare_settings_show_on_bottom', 'Bottom of page/post', $dts_smplshare_settings_show_on_bottom, 'dts_smplshare_settings', 'dts_smplshare_settings_placement' );


    /**
     * Section: Share Icons Available
     */
    function dts_smplshare_settings_show_option() {
        echo '<p>If you <strong>don\'t</strong> wish to show a particular share icon, <strong>uncheck</strong> it here.</p>';
    }
    add_settings_section( 'dts_smplshare_settings_smpl_sharers', __( 'Available Platforms (icons)', 'dts-simple-share' ), 'dts_smplshare_settings_show_option', 'dts_smplshare_settings' );

    $smpl_sharers = dts_smplshare_get_data();
    $smpl_sharer_category = '';
    foreach( $smpl_sharers as $smpl_sharer ) :

        if ( is_string( $smpl_sharer ) ) :
            $smpl_sharer_category = $smpl_sharer;
            continue;
        endif;

        $dts_smplshare_settings_show_option = function() use ( $smpl_sharer, $smpl_sharer_category ) {
            $options = get_option( 'dts_smplshare_settings' );
            $setting_name = 'dts_smplshare_' . $smpl_sharer['name'];
            
            if ( empty( $options ) ) :
                $options = array();
                $options[$setting_name] = '1';
            endif;

            $dts_class = $options[$setting_name] === '1' ? 'checked' : 'unchecked';

            ?>
            <label for="dts_checkbox_<?= $setting_name; ?>"></label>
            <input type="checkbox" name="dts_smplshare_settings[<?= $setting_name; ?>]" id="dts_checkbox_<?= $setting_name; ?>" value="1" <?php checked( $options[$setting_name], '1' ); ?> />
            <?php
        };

        add_settings_field( 'dts_smplshare_' . $smpl_sharer['name'], $smpl_sharer['title'], $dts_smplshare_settings_show_option, 'dts_smplshare_settings', 'dts_smplshare_settings_smpl_sharers' );
    endforeach;


    /**
     * Section: Default field values
     */
    function dts_smpleshare_settings_default_values_text() {
    	echo '<p>Enter/edit default values for share meta. Variables available: {title} {url} {excerpt}</p>';
    }
    add_settings_section( 'dts_smplshare_settings_default_values', __( 'Default Values', 'dts-simple-share' ), 'dts_smpleshare_settings_default_values_text', 'dts_smplshare_settings' );

    $dts_smplshare_setting_email_subject = function() {
        $options = get_option( 'dts_smplshare_settings' );
        $setting_name = 'dts_smplshare_email_subject';
        $options[$setting_name] = isset( $options[$setting_name] ) ? $options[$setting_name] : 'Check this out!';
        ?>
        <input type="text" name="dts_smplshare_settings[<?= $setting_name; ?>]" value="<?= $options[$setting_name]; ?>" />
        <?php
    };
    add_settings_field( 'dts_smplshare_setting_email_subject', 'Default email share subject:', $dts_smplshare_setting_email_subject, 'dts_smplshare_settings', 'dts_smplshare_settings_default_values' );

    $dts_smplshare_setting_email_desc = function() {
        $options = get_option( 'dts_smplshare_settings' );
        $setting_name = 'dts_smplshare_email_desc';
        $options[$setting_name] = isset( $options[$setting_name] ) ? $options[$setting_name] : '{title} | {url}<br /><br />{excerpt}';
        ?>
        <input type="text" name="dts_smplshare_settings[<?= $setting_name; ?>]" value="<?= $options[$setting_name]; ?>" />
        <?php
    };
    add_settings_field( 'dts_smplshare_setting_email_desc', 'Default email share body:', $dts_smplshare_setting_email_desc, 'dts_smplshare_settings', 'dts_smplshare_settings_default_values' );

    $dts_smplshare_setting_twitter_via = function() {
        $options = get_option( 'dts_smplshare_settings' );
        $setting_name = 'dts_smplshare_twitter_via';
        $options[$setting_name] = isset( $options[$setting_name] ) ? $options[$setting_name] : '';
        ?>
        <input type="text" name="dts_smplshare_settings[<?= $setting_name; ?>]" value="<?= $options[$setting_name]; ?>" />
        <?php
    };
    add_settings_field( 'dts_smplshare_setting_twitter_via', 'Twitter Via (don\'t include "@")', $dts_smplshare_setting_twitter_via, 'dts_smplshare_settings', 'dts_smplshare_settings_default_values' );

    $dts_smplshare_setting_hashtags = function() {
        $options = get_option( 'dts_smplshare_settings' );
        $setting_name = 'dts_smplshare_hashtags';
        $options[$setting_name] = isset( $options[$setting_name] ) ? $options[$setting_name] : '';
        ?>
        <input type="text" name="dts_smplshare_settings[<?= $setting_name; ?>]" value="<?= $options[$setting_name]; ?>" />
        <?php
    };
    add_settings_field( 'dts_smplshare_setting_hashtags', 'Hashtags for Twitter (comma separated, don\'t include "#" or spaces)', $dts_smplshare_setting_hashtags, 'dts_smplshare_settings', 'dts_smplshare_settings_default_values' );


    /**
     * Section: Share bar enable on post types:
     */
    function dts_smplshare_settings_post_types_text() {
        echo '<p>Select which post types to <strong>enable</strong> <em>DT\'s Simple Share</em> (the shortcode [dts_sharebar] will <em>not</em> work manually if disabled on post type)</p>';
    }
    add_settings_section( 'dts_smplshare_settings_post_types', __( 'Enable on Post Types', 'dts-simple-share' ), 'dts_smplshare_settings_post_types_text', 'dts_smplshare_settings' );

    $post_types = get_post_types( '', 'objects' );
    foreach ( $post_types as $post_type ) :
        if ( $post_type->name === 'attachment' || $post_type->name === 'revision' || $post_type->name === 'nav_menu_item' )
            continue;
        
        $dts_smplshare_settings_post_type_field = function() use ( $post_type ) {
            $options = get_option( 'dts_smplshare_settings' );
            $setting_name = 'dts_post_types_' . $post_type->name;
            $options[$setting_name] = isset( $options[$setting_name] ) ? $options[$setting_name] : false;
            ?>
            <input type="checkbox" name="dts_smplshare_settings[<?= $setting_name; ?>]" value="1" <?php checked( $options[$setting_name], 1 ); ?> />
            <?php
        };
        add_settings_field( 'dts_post_types_' . $post_type->name, $post_type->labels->name, $dts_smplshare_settings_post_type_field, 'dts_smplshare_settings', 'dts_smplshare_settings_post_types' );
    endforeach;
}
add_action( 'admin_init', 'dts_smplshare_init' );



/**
 * Validate plugin settings on save
 */
function dts_smplshare_settings_validate( $input ) {
    /* Add validations for data here. */
    return $input;
}



/**
 * Add DT's Simple Share to Settings Menu
 */
function dts_smplshare_init_menu() {
    function dts_smplshare_options_page() {
        include( plugin_dir_path( __FILE__ ) . 'dts-simple-share-settings.php' );
    }
    add_options_page( __( 'DT\'s Simple Share', 'dts-simple-share' ), __( 'DT\'s Simple Share', 'dts-simple-share' ), 'manage_options', 'dts-simple-share', 'dts_smplshare_options_page' );
}
add_action( 'admin_menu', 'dts_smplshare_init_menu' );



/**
 * Add shortcode for output
 */
function dts_smplshare_shortcodes_init() {

	function dts_smplshare_shortcode_sharebar( $atts, $content = '' ) {

	    global $post;

	    $options = get_option( 'dts_smplshare_settings' );
	    $setting_option = 'dts_post_types_' . $post->post_type;

	    if ( empty( $options ) )
	    	return $content;

	    if ( empty( $options[$setting_option] ) || $options[$setting_option] !== '1' )
	        return $content;

	    $smpl_sharers = dts_smplshare_get_data();

	    $class = empty( $atts['class'] ) ? '' : $atts['class'];
	    $sharebar = '<div class="dts_smplshare_container ' . $class . '">';

	    foreach ( $smpl_sharers as $smpl_sharer ) :

	        if ( is_string( $smpl_sharer ) )
	            continue;

	        $setting_option = 'dts_smplshare_' . $smpl_sharer['name'];

	        if ( !empty( $options ) && $options[$setting_option] !== '1' )
	                continue;

	        $sharebar .= '<a class="dts_smplshare" href="' . $smpl_sharer['url'] . '" target="_blank" title="' . $smpl_sharer['action'] . '">';
	        $sharebar .=    '<span class="dts_smplshare_icon_container" style="background-image: url(' . plugins_url( 'images/' . $smpl_sharer['icon'], __FILE__ ) . '" title="' . $smpl_sharer['action'] . '"></span>';
	        $sharebar .= '</a>';
	    endforeach;

	    $sharebar .= '</div>';
	    return $sharebar;
	}
	add_shortcode('dts_sharebar', 'dts_smplshare_shortcode_sharebar');
}
add_action( 'init', 'dts_smplshare_shortcodes_init' );



/**
 * Add sharebar to post/page header/footer depending on settings
 */
function dts_smplshare_sharebar_auto( $content ) {

    $options = get_option( 'dts_smplshare_settings' );

    if ( !empty( $options['dts_smplshare_placement_top'] ) && $options['dts_smplshare_placement_top'] === '1' ) {
        $content = do_shortcode( '[dts_sharebar]' ) . $content;
    }

    if ( !empty( $options['dts_smplshare_placement_bottom'] ) && $options['dts_smplshare_placement_bottom'] === '1' ) {
        $content .= do_shortcode( '[dts_sharebar]' );
    }

    return $content;
}
add_filter( 'the_content', 'dts_smplshare_sharebar_auto' );



/**
 * Internal data
 */
function dts_smplshare_get_data() {

    global $post;

    $smpl_sharers = array(

        'Sharing Options',

        array(
            'name'  => 'facebook',
            'url'   => 'https://www.facebook.com/sharer.php?u={url}',
            'title' => 'Facebook',
            'action'=> 'Share on Facebook',
            'icon'  => 'facebook.png'
        ),
        array(
            'name'  => 'twitter',
            'url'   => 'https://twitter.com/intent/tweet?url={url}&text={title}&via={via}&hashtags={hashtags}',
            'title' => 'Twitter',
            'action'=> 'Share on Twitter',
            'icon'  => 'twitter.png'
        ),
        array(
            'name'  => 'googleplus',
            'url'   => 'https://plus.google.com/share?url={url}',
            'title' => 'Google+',
            'action'=> 'Share on Google+',
            'icon'  => 'googleplus.png'
        ),
        array(
            'name'  => 'email',
            'url'   => 'mailto:?subject={subject}&body={desc}',
            'title' => 'Email',
            'action'=> 'Send in Email',
            'icon'  => 'email.png'
        )
    );


    if ( isset( $post->ID ) ) :

	    $options = get_option( 'dts_smplshare_settings' );

		$permalink = get_permalink( $post->ID );
        $url = urlencode( $permalink );
        $excerpt = wp_trim_words( $post->post_content );
        $excerpt = strip_shortcodes( $excerpt );
        $excerpt = html_entity_decode( $excerpt );

        $title = the_title_attribute( array( 'echo' => false ) );
        if ( get_post_meta( $post->ID, '_aioseop_title', true ) )
            $title = get_post_meta( $post->ID, '_aioseop_title', true );
        if ( get_post_meta( $post->ID, '_yoast_wpseo_title', true) )
            $title = get_post_meta( $post->ID, '_yoast_wpseo_title', true);

        $subject = '';
    	if ( !empty( $options['dts_smplshare_email_subject'] ) )
        	$subject = $options['dts_smplshare_email_subject'];
        if ( strlen( $subject ) < 1 )
        	$subject = 'Check this out!';
        $subject = str_replace( '{excerpt}', $excerpt, $subject );
        $subject = str_replace( '{title}', $title, $subject );
        $subject = str_replace( '{url}', $permalink, $subject );

        $desc = '';
        if ( !empty( $options['dts_smplshare_email_desc'] ) )
        	$desc = $options['dts_smplshare_email_desc'];
        if ( strlen( $desc ) < 1 ) 
        	$desc = '{title} | {url}<br /><br />{excerpt}';
        $desc = str_replace( '{excerpt}', $excerpt, $desc );
        $desc = str_replace( '{title}', $title, $desc );
        $desc = str_replace( '{url}', $permalink, $desc );
        $desc = dts_smplshare_html_to_nl( $desc );

        $via = '';
        if ( !empty( $options['dts_smplshare_twitter_via'] ) )
        	$via = $options['dts_smplshare_twitter_via'];
        $via = str_replace( '@', '', $via );

        $hashtags = '';
        if ( !empty( $options['dts_smplshare_hashtags'] ) )
        	$hashtags = $options['dts_smplshare_hashtags'];
        $hashtags = str_replace( '#', '', $hashtags );

        $title 		= str_replace( ' ', '%20', rawurlencode( $title ) );
        $subject 	= str_replace( ' ', '%20', rawurlencode( $subject ) );
        $desc 		= str_replace( ' ', '%20', rawurlencode( $desc ) );
        $via 		= str_replace( ' ', '%20', rawurlencode( $via ) );
        $hashtags 	= str_replace( ' ', '%20', rawurlencode( $hashtags ) );

        foreach ( $smpl_sharers as &$smpl_share ) :
            $smpl_share = str_replace( '{url}', $url, $smpl_share );
            $smpl_share = str_replace( '{title}', $title, $smpl_share );
            $smpl_share = str_replace( '{subject}', $subject, $smpl_share );
            $smpl_share = str_replace( '{desc}', $desc, $smpl_share );

            if ( strlen( $via ) > 0 )
	            $smpl_share = str_replace( '{via}', $via, $smpl_share );
    		else
    			$smpl_share = str_replace( '&via={via}', '', $smpl_share );

    		if ( strlen( $hashtags ) > 0 )
	            $smpl_share = str_replace( '{hashtags}', $hashtags, $smpl_share );
    		else
    			$smpl_share = str_replace( '&hashtags={hashtags}', '', $smpl_share );

        endforeach;

    endif;

    return $smpl_sharers;
}


function dts_smplshare_html_to_nl( $content = '' ) {
	$breaks = array( "<br />", "<br>", "<br/>", "<br />", "&lt;br /&gt;", "&lt;br/&gt;", "&lt;br&gt;" );
	$content = str_ireplace( $breaks, "\r\n", $content );
	return $content;
}

?>
