<?php
   /*
   Plugin Name: DT's Simple Share
   Plugin URI: https://dtweb.design/simple-share/
   Description: Simple social media/email sharebar. Specify platforms and location, or use shortcode [dts_sharebar] wherever you want them to show up!
   Version: 0.3
   Author: Michael R. Dinerstein
   Author URI: https://dtweb.design/
   License: GPL2
   */
   
defined( 'ABSPATH' ) or die( 'No script kiddies please!' );



/**
 * Register and enqueue styles/scripts for admin
 */
function dts_smplshare_register_admin_scripts() {
    $version = '20171104';

    wp_register_style( 'font-awesome', plugins_url( 'css/font-awesome.min.css', __FILE__ ), false, $version );
    wp_register_style( 'dts_ss_styles', plugins_url( 'css/styles.css', __FILE__ ), false, $version );
    wp_register_style( 'dts_ss_styles_admin', plugins_url( 'css/styles-admin.css', __FILE__ ), false, $version );
    wp_register_script( 'dts_ss_scripts_admin', plugins_url( 'js/scripts-admin.js', __FILE__ ), false, $version );
}
add_action( 'admin_init', 'dts_smplshare_register_admin_scripts' );

function dts_smplshare_enqueue_admin_scripts() {
    wp_enqueue_style( 'font-awesome' );
    wp_enqueue_style( 'dts_ss_styles' );
    wp_enqueue_style( 'dts_ss_styles_admin' );
    wp_enqueue_script( 'jquery' );
    wp_enqueue_script( 'jquery-ui-sortable' );
    wp_enqueue_script( 'dts_ss_scripts_admin' );
}
add_action( 'admin_enqueue_scripts', 'dts_smplshare_enqueue_admin_scripts' );



/**
 * Register and enqueue styles/scripts for front-end
 */
function dts_smplshare_register_scripts() {
    $version = '20171104';

    wp_register_style( 'font-awesome', plugins_url( 'css/font-awesome.min.css', __FILE__ ), false, $version );
    wp_register_style( 'dts_ss_styles', plugins_url( 'css/styles.css', __FILE__ ), false, $version );
    wp_register_script( 'dts_ss_scripts', plugins_url( 'js/scripts.js', __FILE__ ), false, $version );
}
add_action( 'init', 'dts_smplshare_register_scripts' );

function dts_smplshare_enqueue_scripts() {
    wp_enqueue_style( 'font-awesome' );
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
     * Section: Share bar style
     */
    function dts_smplshare_settings_sharebar_style_text() {
    	echo '<p>Select preferred sharebar style (hover mouse to check hover effect).</p>';
    	$atts = array(
    		'id'	=> 'admin_sharebar_example'
    	);
        echo '<p class="text-align-center" style="margin-bottom:0;"><strong>Drag &amp; drop</strong> to reorder. <span id="dts_order_status" class="dts-alert"></span></p>';

    	echo dts_smplshare_shortcode_sharebar_preview( $atts );
    }
    add_settings_section( 'dts_smplshare_settings_sharebar_style', __( 'Sharebar Style', 'dts-simple-share' ), 'dts_smplshare_settings_sharebar_style_text', 'dts_smplshare_settings' );

    $dts_smplshare_settings_style_select = function() {
    	$options = get_option( 'dts_smplshare_settings' );
    	$setting_name = 'dts_smplshare_sharebar_style';
    	$options[$setting_name] = isset( $options[$setting_name] ) ? $options[$setting_name] : 'dts_sharebar_style_v1';
    	?>
    	<input type="radio" name="dts_smplshare_settings[<?= $setting_name; ?>]" value="dts_sharebar_style_v1" class="dts_sharebar_style_radio" <?php checked( $options[$setting_name] == 'dts_sharebar_style_v1' ); ?> />Rectangle<br />
    	<input type="radio" name="dts_smplshare_settings[<?= $setting_name; ?>]" value="dts_sharebar_style_v2" class="dts_sharebar_style_radio" <?php checked( $options[$setting_name] == 'dts_sharebar_style_v2' ); ?> />Round
    	<?php
    };
    add_settings_field( 'dts_smplshare_settings_style_select', 'Sharebar Style', $dts_smplshare_settings_style_select, 'dts_smplshare_settings', 'dts_smplshare_settings_sharebar_style' );


   /**
     * Section: Share bar placement
     */
    function dts_smplshare_settings_placement_text() {
        echo '<p>Select placement for sharebar on posts/pages (the shortcode [dts_sharebar] will still work manually regardless)</p>';
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
        echo '<p>If you wish to show a particular share icon, check it here.</p>';
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

            if ( isset( $options[$setting_name] ) ) :
                $dts_class = $options[$setting_name] === '1' ? 'checked' : 'unchecked';
            else :
                $dts_class = 'unchecked';
                $options[$setting_name] = false;
            endif;

            ?>
            <label for="dts_checkbox_<?= $setting_name; ?>"></label>
            <input type="checkbox" name="dts_smplshare_settings[<?= $setting_name; ?>]" id="dts_checkbox_<?= $setting_name; ?>" value="1" <?php checked( $options[$setting_name], '1' ); ?> class="dts_sharebar_platform_checkbox dts_sharebar_platform_<?= $smpl_sharer['name']; ?>" data-name="<?= $smpl_sharer['name']; ?>" />
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

	    if ( empty( $options['dts_smplshare_sharebar_style'] ) )
	    	$style = 'dts_sharebar_style_v1';
	    else
	    	$style = $options['dts_smplshare_sharebar_style'];

	    $smpl_sharers = dts_smplshare_get_data();

	    $class 	= empty( $atts['class'] ) ? '' : $atts['class'];
	    $id 	= empty( $atts['id'] ) ? '' : ' id="' . $atts['id'] . '" ';

	    $sharebar = '<div ' . $id . ' class="dts_smplshare_container ' . $class . ' ' . $style . '">';

        if ( ! empty( $options['dts_order'] ) ) :

            $order = $options['dts_order'];

            foreach( $order as $key ) :
   
                $setting_option = 'dts_smplshare_' . $smpl_sharers[$key]['name'];

                if ( ! empty( $options ) && ( ! isset( $options[$setting_option] ) || $options[$setting_option] !== '1' ) )
                        continue;

                $sharebar .= dts_smplshare_icon_html( $smpl_sharers[$key] );

            endforeach;

            foreach( $smpl_sharers as $smpl_sharer ) :

                $setting_option = 'dts_smplshare_' . $smpl_sharer['name'];

                if ( ! empty( $options ) && ( ! isset( $options[$setting_option] ) || $options[$setting_option] !== '1' ) )
                        continue;

                if ( ! in_array( $smpl_sharer['name'], $order ) )
                    $sharebar .= dts_smplshare_icon_html( $smpl_sharer );

            endforeach;

        else :

    	    foreach ( $smpl_sharers as $smpl_sharer ) :

    	        $setting_option = 'dts_smplshare_' . $smpl_sharer['name'];

    	        if ( ! empty( $options ) && ( ! isset( $options[$setting_option] ) || $options[$setting_option] !== '1' ) )
    	                continue;

    	        $sharebar .= dts_smplshare_icon_html( $smpl_sharer );

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
    if ( empty( $options ) )
    	return $content;

	if ( empty( $options['dts_smplshare_sharebar_style'] ) )
	  	$style = 'dts_sharebar_style_v1';
	else
	   	$style = $options['dts_smplshare_sharebar_style'];

	$smpl_sharers = dts_smplshare_get_data();

    $class 	= empty( $atts['class'] ) ? '' : $atts['class'];
	$id 	= empty( $atts['id'] ) ? '' : ' id="' . $atts['id'] . '" ';

	$sharebar = '<div ' . $id . ' class="dts_smplshare_container ' . $class . ' ' . $style . '">';
    $sharebar .= '<ul id="dts-sortable">';

    if ( ! empty( $options['dts_order'] ) ) :

        $order = $options['dts_order'];

        foreach( $order as $key )
            $sharebar .= dts_smplshare_icon_html( $smpl_sharers[$key], true );

        foreach( $smpl_sharers as $smpl_sharer ) :

            if ( ! in_array( $smpl_sharer['name'], $order ) )
                $sharebar .= dts_smplshare_icon_html( $smpl_sharer, true );

        endforeach;

    else :

        foreach ( $smpl_sharers as $smpl_sharer )
            $sharebar .= dts_smplshare_icon_html( $smpl_sharer, true );

    endif;

    $sharebar .= '</ul>';
    $sharebar .= '</div>';
    return $sharebar;		
}


/**
 * Since 0.2
 * Generate HTML for share icons
 * 
 * @param $smpl_sharer array
 * @param $preview boolean
 * @return $html string
 */
function dts_smplshare_icon_html( $smpl_sharer = array(), $preview = false ) {

    if ( ! isset( $smpl_sharer['url'] ) || ! isset( $smpl_sharer['action'] ) || ! isset( $smpl_sharer['name'] ) )
        return;

    $short = isset( $smpl_sharer['short'] ) ? $smpl_sharer['short'] : 'Share';

    $html = '';

    if ( $preview === false ) :
        $html .= '<a class="dts_smplshare dts_smplshare_sharelink" href="' . $smpl_sharer['url'] . '" target="_blank" title="' . $smpl_sharer['action'] . '" data-name="' . $smpl_sharer['name'] . '">';
        $html .=    '<span class="dts_smplshare_icon_container ' . $smpl_sharer['name'] . '" title="' . $smpl_sharer['action'] . '"></span>';
        $html .=    '<span class="dts_smplshare_icon_desc_container ' . $smpl_sharer['name'] . '" title="' . $smpl_sharer['action'] . '">' . $short . '</span>';
        $html .= '</a>';
    else :
        $html .= '<li class="sortable" data-name="' . $smpl_sharer['name'] . '">';
        $html .= '<a class="dts_smplshare" data-name="' . $smpl_sharer['name'] . '" href="#" title="' . $smpl_sharer['action'] . '">';
        $html .=    '<span class="dts_smplshare_icon_container ' . $smpl_sharer['name'] . '" title="' . $smpl_sharer['action'] . '"></span>';
        $html .=    '<span class="dts_smplshare_icon_desc_container ' . $smpl_sharer['name'] . '" title="' . $smpl_sharer['action'] . '">' . $short . '</span>';
        $html .= '</a>';
        $html .= '</li>';
    endif;

    return $html;
}


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
 * Since 0.2
 * Update sort order via AJAX
 */
function dts_smplshare_setorder() {

    if ( current_user_can( 'manage_options' ) ) :

        $data = isset( $_POST['data'] ) ? $_POST['data'] : '0';

        if ( strlen( $data ) > 0 ) :
            $data = stripcslashes( $data );
            $data = json_decode( $data );

            $options = get_option( 'dts_smplshare_settings' );

            if ( !empty( $options ) ) :

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


/**
 * Internal data
 */
function dts_smplshare_get_data() {

    global $post;

    $smpl_sharers = array(

        'facebook'  => array(
            'name'  => 'facebook',
            'url'   => 'https://www.facebook.com/sharer.php?u={url}',
            'title' => 'Facebook',
            'action'=> 'Share on Facebook'
        ),
        'twitter'   => array(
            'name'  => 'twitter',
            'url'   => 'https://twitter.com/intent/tweet?url={url}&text={title}&via={via}&hashtags={hashtags}',
            'title' => 'Twitter',
            'action'=> 'Tweet it!',
            'short' => 'Tweet'
        ),
        'googleplus'=> array(
            'name'  => 'googleplus',
            'url'   => 'https://plus.google.com/share?url={url}',
            'title' => 'Google+',
            'action'=> 'Share on Google+'
        ),
        'linkedin'  => array(
        	'name'	=> 'linkedin',
        	'url'	=> 'https://www.linkedin.com/shareArticle?url={url}&title={title}',
        	'title'	=> 'LinkedIn',
        	'action'=> 'Share on LinkedIn'
        ),
        'tumblr'    => array(
            'name'  => 'tumblr',
            'url'   => 'https://www.tumblr.com/widgets/share/tool?canonicalUrl={url}&title={title}&caption={desc}',
            'title' => 'Tumblr',
            'action'=> 'Post on Tumblr.',
            'short' => 'Post'
        ),
        'stumbleupon' => array(
            'name'    => 'stumbleupon',
            'url'     => 'http://www.stumbleupon.com/submit?url={url}&title={title}',
            'title'   => 'StumbleUpon',
            'action'  => 'Share on StumbleUpon'
        ),
        'reddit'    => array(
            'name'  => 'reddit',
            'url'   => 'https://reddit.com/submit?url={url}&title={title}',
            'title' => 'Reddit',
            'action'=> 'Share on Reddit'
        ),
        'email'     => array(
            'name'  => 'email',
            'url'   => 'mailto:?subject={subject}&body={desc}',
            'title' => 'Email',
            'action'=> 'Send in Email',
            'short' => 'Email'
        )
    );


    if ( isset( $post->ID ) ) :

	    $options   = get_option( 'dts_smplshare_settings' );

		$permalink = get_permalink( $post->ID );
        $url       = urlencode( $permalink );

        $excerpt   = wp_trim_words( $post->post_content );
        $excerpt   = strip_shortcodes( $excerpt );
        $excerpt   = html_entity_decode( $excerpt );
        
        if ( get_post_meta( $post->ID, '_aioseop_title', true ) )
            $title = get_post_meta( $post->ID, '_aioseop_title', true );
        
        elseif ( get_post_meta( $post->ID, '_yoast_wpseo_title', true) )
            $title = get_post_meta( $post->ID, '_yoast_wpseo_title', true);

        else
            $title = the_title_attribute( array( 'echo' => false ) );


        $subject = '';
    	if ( ! empty( $options['dts_smplshare_email_subject'] ) )
        	$subject = $options['dts_smplshare_email_subject'];

        if ( strlen( $subject ) < 1 )
        	$subject = 'Check this out!';
        
        $subject = str_replace( '{excerpt}', $excerpt, $subject );
        $subject = str_replace( '{title}', $title, $subject );
        $subject = str_replace( '{url}', $permalink, $subject );


        $desc = '';
        if ( ! empty( $options['dts_smplshare_email_desc'] ) )
        	$desc = $options['dts_smplshare_email_desc'];

        if ( strlen( $desc ) < 1 ) 
        	$desc = '{title} | {url}<br /><br />{excerpt}';

        $desc = str_replace( '{excerpt}', $excerpt, $desc );
        $desc = str_replace( '{title}', $title, $desc );
        $desc = str_replace( '{url}', $permalink, $desc );
        $desc = dts_smplshare_html_to_nl( $desc );

        $via = '';
        if ( ! empty( $options['dts_smplshare_twitter_via'] ) )
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
