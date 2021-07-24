<?php

defined('ABSPATH') or die();



/**
 * @since 0.1
 * Add options on plugin activation
 */
function dts_smplshare_activate()
{
	add_option('dts_smplshare_settings');
}
register_activation_hook(__FILE__, 'dts_smplshare_activate');



/**
 * @since 0.1
 * Remove plugin-specific options on plugin deactivation
 */
function dts_smplshare_remove()
{
	delete_option('dts_smplshare_settings');
}
register_deactivation_hook(__FILE__, 'dts_smplshare_remove');



/**
 * @since 0.1
 * Action links on plugin page: Add 'Settings' Link
 * 
 */
function dts_smplshare_action_links($actions, $plugin_file)
{
	if ($plugin_file === 'dts-simple-share/dts-simple-share.php') {

		$settings = array(
			'settings' => '<a href="' . esc_url(get_admin_url(null, 'options-general.php?page=dts-simple-share')) . '">' . __('Settings', 'General') . '</a>'
		);
		$actions = array_merge($settings, $actions);
	}

	return $actions;
}
add_filter('plugin_action_links', 'dts_smplshare_action_links', 10, 5);



/**
 * @since 0.1
 * Add Simple Share to Settings Menu
 */
function dts_smplshare_init_menu()
{
	function dts_smplshare_options_page()
	{
		include(plugin_dir_path(__FILE__) . '../dts-simple-share-settings.php');
	}
	add_options_page(__('Simple Share', 'dts-simple-share'), __('Simple Share', 'dts-simple-share'), 'manage_options', 'dts-simple-share', 'dts_smplshare_options_page');
}
add_action('admin_menu', 'dts_smplshare_init_menu');



/**
 * @since 0.1
 * Validate plugin settings on save
 */
function dts_smplshare_settings_validate($input)
{
	/* TODO: Add validations for data here. */
	return $input;
}



/**
 * @since 0.1
 * Init plugin on admin_init
 */
function dts_smplshare_init()
{
	load_plugin_textdomain('dts-simple-share', false, basename(dirname(__FILE__)) . '/languages');

	// Register available settings
	register_setting('dts_smplshare_settings', 'dts_smplshare_settings');
	register_setting('dts_smplshare_settings', 'dts_smplshare_settings_sharebar_style');
	register_setting('dts_smplshare_settings', 'dts_smplshare_settings_placement');
	register_setting('dts_smplshare_settings', 'dts_smplshare_settings_smpl_sharers');
	register_setting('dts_smplshare_settings', 'dts_smplshare_settings_default_values');
	register_setting('dts_smplshare_settings', 'dts_smplshare_settings_post_types');
	register_setting('dts_smplshare_settings', 'dts_smplshare_settings_enable_amp');
	register_setting('dts_smplshare_settings', 'dts_smplshare_settings_enable_amp_slugcheck');


	/**
	 * Section: Share bar style info
	 */
	function dts_smplshare_settings_sharebar_style_text()
	{

		echo '<p>Select preferred sharebar style (hover mouse to check hover effect).</p>';

		// Reminder to save settings notice
		$options = get_option('dts_smplshare_settings');

		if (empty($options)) :

			echo '<div class="notice notice-warning is-dismissible">';
			echo '	<p><strong>Reminder: Please save all settings to fully activate the plugin.</strong> Share links/icons will not appear on site until settings are saved.</p>';
			echo '	<button type="button" class="notice-dismiss">';
			echo '		<span class="screen-reader-text">Dismiss this notice.</span>';
			echo '	</button>';
			echo '</div>';

		endif;

		$atts = array(
			'id'	=> 'admin_sharebar_example'
		);

		echo '<p class="text-align-center" style="margin-bottom:0;"><strong>Drag &amp; drop</strong> to reorder. <span id="dts_order_status" class="dts-alert"></span></p>';

		echo dts_smplshare_shortcode_sharebar_preview($atts);
	}
	add_settings_section('dts_smplshare_settings_sharebar_style', __('Sharebar Style', 'dts-simple-share'), 'dts_smplshare_settings_sharebar_style_text', 'dts_smplshare_settings_sharebar_style');



	/**
	 * Section: Share bar style settings
	 */
	$dts_smplshare_settings_style_select = function () {

		$options = get_option('dts_smplshare_settings');

		$options = is_array($options) ? $options : array();

		$options['dts_smplshare_sharebar_style'] = isset($options['dts_smplshare_sharebar_style']) ? $options['dts_smplshare_sharebar_style'] : 'dts_sharebar_style_v1';

		echo '<input type="radio" name="dts_smplshare_settings[dts_smplshare_sharebar_style]" value="dts_sharebar_style_v1" class="dts_sharebar_style_radio" ';
		checked($options['dts_smplshare_sharebar_style'], 'dts_sharebar_style_v1');
		echo ' />Rectangle<br />';

		echo '<input type="radio" name="dts_smplshare_settings[dts_smplshare_sharebar_style]" value="dts_sharebar_style_v2" class="dts_sharebar_style_radio" ';
		checked($options['dts_smplshare_sharebar_style'], 'dts_sharebar_style_v2');
		echo ' />Round';
	};
	add_settings_field('dts_smplshare_settings_style_select', __('Sharebar Style', 'dts-simple-share'), $dts_smplshare_settings_style_select, 'dts_smplshare_settings_sharebar_style', 'dts_smplshare_settings_sharebar_style');



	/**
	 * Section: Share bar placement
	 */
	function dts_smplshare_settings_placement_text()
	{
		echo '<p>Select automatic placement for sharebar on enabled posts/pages. (alternatively, use [dts_sharebar] in content for manual placement)</p>';
	}
	add_settings_section('dts_smplshare_settings_placement', __('Sharebar Placement', 'dts-simple-share'), 'dts_smplshare_settings_placement_text', 'dts_smplshare_settings_placement');

	$dts_smplshare_settings_show_on_top = function () {

		$options = get_option('dts_smplshare_settings');

		$options = is_array($options) ? $options : array();

		$options['dts_smplshare_placement_top'] = isset($options['dts_smplshare_placement_top']) ? $options['dts_smplshare_placement_top'] : false;

		echo '<input type="checkbox" name="dts_smplshare_settings[dts_smplshare_placement_top]" value="1" ';
		checked($options['dts_smplshare_placement_top'], 1);
		echo ' />';
	};
	add_settings_field('dts_smplshare_settings_show_on_top', __('Top of page/post', 'dts-simple-share'), $dts_smplshare_settings_show_on_top, 'dts_smplshare_settings_placement', 'dts_smplshare_settings_placement');

	$dts_smplshare_settings_show_on_bottom = function () {

		$options = get_option('dts_smplshare_settings');

		$options = is_array($options) ? $options : array();

		$options['dts_smplshare_placement_bottom'] = isset($options['dts_smplshare_placement_bottom']) ? $options['dts_smplshare_placement_bottom'] : false;

		echo '<input type="checkbox" name="dts_smplshare_settings[dts_smplshare_placement_bottom]" value="1" ';
		checked($options['dts_smplshare_placement_bottom'], 1);
		echo ' />';
	};
	add_settings_field('dts_smplshare_settings_show_on_bottom', __('Bottom of page/post', 'dts-simple-share'), $dts_smplshare_settings_show_on_bottom, 'dts_smplshare_settings_placement', 'dts_smplshare_settings_placement');



	/**
	 * Section: AMP Support
	 */
	function dts_smplshare_settings_support_amp_text()
	{
		echo '<p>';
		echo '<strong>Plugin</strong> option supports <a href="https://wordpress.org/plugins/amp/" target="_blank" rel="noreferrer nofollow">AMP</a> or ';
		echo '<a href="https://wordpress.org/plugins/accelerated-mobile-pages/" target="_blank" rel="noreferrer nofollow">AMP for WP – Accelerated Mobile Pages</a> plugins.<br />';
		echo '<strong>Slug Only</strong> detects the presence of /amp/ at the end of the post URL only.';
		echo '</p>';
	}
	add_settings_section('dts_smplshare_settings_support_amp', __('Google AMP Support', 'dts-simple-share'), 'dts_smplshare_settings_support_amp_text', 'dts_smplshare_settings_support_amp');

	$dts_smplshare_settings_amp = function () {

		$options = get_option('dts_smplshare_settings');

		$options = is_array($options) ? $options : array();

		$options['dts_smplshare_settings_enable_amp'] = isset($options['dts_smplshare_settings_enable_amp']) ? $options['dts_smplshare_settings_enable_amp'] : false;

		echo '<input type="checkbox" name="dts_smplshare_settings[dts_smplshare_settings_enable_amp]" value="1" ';
		checked($options['dts_smplshare_settings_enable_amp'], 1);
		echo ' />';
	};
	add_settings_field('dts_smplshare_settings_amp', __('AMP Support: Plugin', 'dts-simple-share'), $dts_smplshare_settings_amp, 'dts_smplshare_settings_support_amp', 'dts_smplshare_settings_support_amp');

	$dts_smplshare_settings_amp_slugcheck = function () {

		$options = get_option('dts_smplshare_settings');

		$options = is_array($options) ? $options : array();

		$options['dts_smplshare_settings_enable_amp_slugcheck'] = isset($options['dts_smplshare_settings_enable_amp_slugcheck']) ? $options['dts_smplshare_settings_enable_amp_slugcheck'] : false;

		echo '<input type="checkbox" name="dts_smplshare_settings[dts_smplshare_settings_enable_amp_slugcheck]" value="1" ';
		checked($options['dts_smplshare_settings_enable_amp_slugcheck'], 1);
		echo ' />';
	};
	add_settings_field('dts_smplshare_settings_amp_slugcheck', __('AMP Support: Slug Only', 'dts-simple-share'), $dts_smplshare_settings_amp_slugcheck, 'dts_smplshare_settings_support_amp', 'dts_smplshare_settings_support_amp');



	/**
	 * Section: Share Icons Available
	 */
	function dts_smplshare_settings_show_option()
	{
		echo '<p>If you wish to show a particular share icon, check it here.</p>';
	}
	add_settings_section('dts_smplshare_settings_smpl_sharers', __('Available Platforms (icons)', 'dts-simple-share'), 'dts_smplshare_settings_show_option', 'dts_smplshare_settings_smpl_sharers');

	$smpl_sharers = dts_smplshare_get_data();

	$smpl_sharer_category = '';

	foreach ($smpl_sharers as $smpl_sharer) :

		// If it's a string, it's a title to display - so continue
		if (is_string($smpl_sharer)) :

			$smpl_sharer_category = $smpl_sharer;

			continue;

		endif;

		$dts_smplshare_settings_show_option = function () use ($smpl_sharer, $smpl_sharer_category) {

			$options = get_option('dts_smplshare_settings');

			$options = is_array($options) ? $options : array();

			$setting_name = 'dts_smplshare_' . $smpl_sharer['name'];

			if (empty($options)) {
				$options[$setting_name] = '1';
			}

			if (isset($options[$setting_name])) {
				$dts_class = $options[$setting_name] === '1' ? 'checked' : 'unchecked';
			} else {
				$dts_class = 'unchecked';
				$options[$setting_name] = false;
			}

			echo '<label for="dts_checkbox_' . $setting_name . '"></label>';
			echo '<input type="checkbox" name="dts_smplshare_settings[' . $setting_name . ']" id="dts_checkbox_' . $setting_name . '" value="1" ';
			checked($options[$setting_name], '1');
			echo ' class="dts_sharebar_platform_checkbox dts_sharebar_platform_' . $smpl_sharer['name'] . '" data-name="' . $smpl_sharer['name'] . '" />';
		};

		add_settings_field('dts_smplshare_' . $smpl_sharer['name'], $smpl_sharer['title'], $dts_smplshare_settings_show_option, 'dts_smplshare_settings_smpl_sharers', 'dts_smplshare_settings_smpl_sharers');
	endforeach;


	/**
	 * Section: Default field values
	 */
	function dts_smpleshare_settings_default_values_text()
	{
		echo '<p>Enter/edit default values for share meta. Variables available: {title} {url} {excerpt}</p>';
	}
	add_settings_section('dts_smplshare_settings_default_values', __('Default Values', 'dts-simple-share'), 'dts_smpleshare_settings_default_values_text', 'dts_smplshare_settings_default_values');


	/**
	 * Email default subject
	 */
	$dts_smplshare_setting_email_subject = function () {

		$options = get_option('dts_smplshare_settings');

		$options = is_array($options) ? $options : array();

		$options['dts_smplshare_email_subject'] = isset($options['dts_smplshare_email_subject']) ? $options['dts_smplshare_email_subject'] : __('Check this out!');

		echo '<input type="text" name="dts_smplshare_settings[dts_smplshare_email_subject]" value="' . $options['dts_smplshare_email_subject'] . '" />';
	};
	add_settings_field('dts_smplshare_setting_email_subject', '<p>Email subject:</p>', $dts_smplshare_setting_email_subject, 'dts_smplshare_settings_default_values', 'dts_smplshare_settings_default_values');


	/**
	 * Email default description/body
	 */
	$dts_smplshare_setting_email_desc = function () {

		$options = get_option('dts_smplshare_settings');

		$options = is_array($options) ? $options : array();

		$options['dts_smplshare_email_desc'] = isset($options['dts_smplshare_email_desc']) ? $options['dts_smplshare_email_desc'] : '{title} | {url}<br /><br />{excerpt}';

		echo '<input type="text" name="dts_smplshare_settings[dts_smplshare_email_desc]" value="' . $options['dts_smplshare_email_desc'] . '" />';
	};
	add_settings_field('dts_smplshare_setting_email_desc', 'Email body:', $dts_smplshare_setting_email_desc, 'dts_smplshare_settings_default_values', 'dts_smplshare_settings_default_values');


	/**
	 * Twitter via default value
	 */
	$dts_smplshare_setting_twitter_via = function () {

		$options = get_option('dts_smplshare_settings');

		$options = is_array($options) ? $options : array();

		$options['dts_smplshare_twitter_via'] = isset($options['dts_smplshare_twitter_via']) ? $options['dts_smplshare_twitter_via'] : '';

		echo '<input type="text" name="dts_smplshare_settings[dts_smplshare_twitter_via]" value="' . $options['dts_smplshare_twitter_via'] . '" />';
	};
	add_settings_field('dts_smplshare_setting_twitter_via', 'Twitter @Via', $dts_smplshare_setting_twitter_via, 'dts_smplshare_settings_default_values', 'dts_smplshare_settings_default_values');


	/**
	 * Twitter default hashtags
	 */
	$dts_smplshare_setting_hashtags = function () {

		$options = get_option('dts_smplshare_settings');

		$options = is_array($options) ? $options : array();

		$options['dts_smplshare_hashtags'] = isset($options['dts_smplshare_hashtags']) ? $options['dts_smplshare_hashtags'] : '';

		echo '<input type="text" name="dts_smplshare_settings[dts_smplshare_hashtags]" value="' . $options['dts_smplshare_hashtags'] . '" />';
	};
	add_settings_field('dts_smplshare_setting_hashtags', 'Hashtags for Twitter (comma separated)', $dts_smplshare_setting_hashtags, 'dts_smplshare_settings_default_values', 'dts_smplshare_settings_default_values');


	/**
	 * Section: Share bar enable on post types:
	 */
	function dts_smplshare_settings_post_types_text()
	{
		echo '<p>Select which post types to <strong>enable</strong> <em>DT\'s Simple Share</em> by default.<br />Shortcode <strong>[dts_sharebar]</strong> works wherever placed, regardless of this setting.</p>';
	}
	add_settings_section('dts_smplshare_settings_post_types', __('Enable on Post Types', 'dts-simple-share'), 'dts_smplshare_settings_post_types_text', 'dts_smplshare_settings_post_types');

	$post_types = get_post_types('', 'objects');

	$ignore_post_types = array(
		'attachment',
		'revision',
		'nav_menu_item'
	);

	foreach ($post_types as $post_type) :

		if (in_array($post_type->name, $ignore_post_types)) {
			continue;
		}

		$dts_smplshare_settings_post_type_field = function () use ($post_type) {

			$options = get_option('dts_smplshare_settings');

			$options = is_array($options) ? $options : array();

			$setting_name = 'dts_post_types_' . $post_type->name;

			$options[$setting_name] = isset($options[$setting_name]) ? $options[$setting_name] : false;

			echo '<input type="checkbox" name="dts_smplshare_settings[' . $setting_name . ']" value="1" ';
			checked($options[$setting_name], 1);
			echo ' />';
		};
		add_settings_field('dts_post_types_' . $post_type->name, $post_type->labels->name, $dts_smplshare_settings_post_type_field, 'dts_smplshare_settings_post_types', 'dts_smplshare_settings_post_types');

	endforeach;
}
add_action('admin_init', 'dts_smplshare_init');
