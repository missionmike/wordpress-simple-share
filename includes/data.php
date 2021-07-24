<?php

/**
 * @since 0.5
 * 
 * Data-specific functions and variables included here since v0.5
 */
defined('ABSPATH') or die();

/**
 * @since 0.1
 * Internal data
 */
function dts_smplshare_get_data()
{

	/**
	 * Prepare array of all possible share platforms
	 */
	$smpl_sharers = array(
		/**
		 * @since 0.1
		 */
		'facebook'  => array(
			'name'  => 'facebook',
			'url'   => 'https://www.facebook.com/sharer.php?u={url}',
			'title' => 'Facebook',
			'action' => 'Share on Facebook',
			'amp' 	=> true,
		),
		'twitter'   => array(
			'name'  => 'twitter',
			'url'   => 'https://twitter.com/intent/tweet?url={url}&text={title}&via={via}&hashtags={hashtags}',
			'title' => 'Twitter',
			'action' => 'Tweet it!',
			'short' => 'Tweet',
			'amp'	=> true,
		),
		'linkedin'  => array(
			'name'	=> 'linkedin',
			'url'	=> 'https://www.linkedin.com/shareArticle?url={url}&title={title}',
			'title'	=> 'LinkedIn',
			'action' => 'Share on LinkedIn',
			'amp'	=> true,
		),
		'tumblr'	=> array(
			'name'  => 'tumblr',
			'url'   => 'https://www.tumblr.com/widgets/share/tool?canonicalUrl={url}&title={title}&caption={desc}',
			'title' => 'Tumblr',
			'action' => 'Post on Tumblr.',
			'short' => 'Post',
			'amp'	=> true,
		),
		/**
		 * @since 0.5.2
		 * Removed StumbleUpon
		 *
		'stumbleupon' => array(
			'name'	=> 'stumbleupon',
			'url'	 => 'http://www.stumbleupon.com/submit?url={url}&title={title}',
			'title'   => 'StumbleUpon',
			'action'  => 'Share on StumbleUpon'
		),
		 */
		'reddit'	=> array(
			'name'  => 'reddit',
			'url'   => 'https://reddit.com/submit?url={url}&title={title}',
			'title' => 'Reddit',
			'action' => 'Share on Reddit'
		),
		'email'	 => array(
			'name'  => 'email',
			'url'   => 'mailto:?subject={subject}&body={desc}',
			'title' => 'Email',
			'action' => 'Send in Email',
			'short' => 'Email',
			'amp'	=> true
		),
		/**
		 * @since 0.5
		 */
		'pinterest' => array(
			'name'	=> 'pinterest',
			'url'	=> 'http://pinterest.com/pin/create/link/?url={url}',
			'title' => 'Pinterest',
			'action' => 'Pin it on Pinterest',
			'short' => 'Pin It!',
			'amp'	=> true
		),
	);



	/**
	 * If $post is set, prepare each share URL with the appropriate
	 * data pertinent to this post
	 */
	global $post;

	if (isset($post->ID)) :

		// Prepare share-able URL
		$permalink = get_permalink($post->ID);
		$url	   = urlencode($permalink);

		// Prepare excerpt content for descriptions
		$excerpt   = wp_trim_words($post->post_content);
		$excerpt   = strip_shortcodes($excerpt);
		$excerpt   = html_entity_decode($excerpt);

		// All-in-one SEO plugin support for title
		if (get_post_meta($post->ID, '_aioseop_title', true)) {

			$title = get_post_meta($post->ID, '_aioseop_title', true);

			// Yoast SEO plugin support for title
		} elseif (get_post_meta($post->ID, '_yoast_wpseo_title', true)) {

			$title = get_post_meta($post->ID, '_yoast_wpseo_title', true);

			// Default WP title
		} else {

			$title = the_title_attribute(array('echo' => false));
		}

		// Load plugin options
		$options   = get_option('dts_smplshare_settings');

		// Prepare email subject boiler
		$subject = '';

		if (!empty($options['dts_smplshare_email_subject'])) {
			$subject = $options['dts_smplshare_email_subject'];
		}

		if (strlen($subject) < 1) {
			$subject = 'Check this out!';
		}

		// Swap merge codes for text in subject
		$subject = str_replace('{excerpt}', $excerpt, $subject);
		$subject = str_replace('{title}', $title, $subject);
		$subject = str_replace('{url}', $permalink, $subject);

		// Prepare default description boiler
		$desc = '';

		if (!empty($options['dts_smplshare_email_desc'])) {
			$desc = $options['dts_smplshare_email_desc'];
		}

		if (strlen($desc) < 1) {
			$desc = '{title} | {url}<br /><br />{excerpt}';
		}

		// Swap merge codes for text in description
		$desc = str_replace('{excerpt}', $excerpt, $desc);
		$desc = str_replace('{title}', $title, $desc);
		$desc = str_replace('{url}', $permalink, $desc);
		$desc = dts_smplshare_html_to_nl($desc);

		// Prepare via for Twitter
		$via = '';

		if (!empty($options['dts_smplshare_twitter_via'])) {
			$via = $options['dts_smplshare_twitter_via'];
		}

		$via = str_replace('@', '', $via);

		// Prepare hashtags
		$hashtags = '';

		if (!empty($options['dts_smplshare_hashtags'])) {
			$hashtags = $options['dts_smplshare_hashtags'];
		}

		// Ensure proper hashtag formatting
		$hashtags = str_replace('#', '', $hashtags);
		$hashtags = str_replace(' ', '', $hashtags);

		// URL encoding for attributes
		$title 		= str_replace(' ', '%20', rawurlencode($title));
		$subject 	= str_replace(' ', '%20', rawurlencode($subject));
		$desc 		= str_replace(' ', '%20', rawurlencode($desc));
		$via 		= str_replace(' ', '%20', rawurlencode($via));
		$hashtags 	= str_replace(' ', '%20', rawurlencode($hashtags));

		// Loop through each share item and prepare the share URL w/ attributes included
		foreach ($smpl_sharers as &$smpl_share) :

			$smpl_share = str_replace('{url}', $url, $smpl_share);
			$smpl_share = str_replace('{title}', $title, $smpl_share);
			$smpl_share = str_replace('{subject}', $subject, $smpl_share);
			$smpl_share = str_replace('{desc}', $desc, $smpl_share);

			if (strlen($via) > 0) {
				$smpl_share = str_replace('{via}', $via, $smpl_share);
			} else {
				$smpl_share = str_replace('&via={via}', '', $smpl_share);
			}

			if (strlen($hashtags) > 0) {
				$smpl_share = str_replace('{hashtags}', $hashtags, $smpl_share);
			} else {
				$smpl_share = str_replace('&hashtags={hashtags}', '', $smpl_share);
			}

		endforeach;

	endif;
	// end $post check

	return $smpl_sharers;
}
