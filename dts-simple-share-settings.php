<?php

/**
 * @since 0.1
 * 
 */

defined('ABSPATH') or die();

?>

<div class="wrap">
	<h2>Simple Share Settings</h2>

	<form method="post" action="options.php">
		<?php
		settings_fields('dts_smplshare_settings');
		do_settings_sections('dts_smplshare_settings_sharebar_style');
		?>

		<div class="dts-clear"></div>

		<div class="dts-column-container">
			<div class="dts-column-33">
				<?php
				do_settings_sections('dts_smplshare_settings_post_types');
				do_settings_sections('dts_smplshare_settings_placement');
				?>
			</div>

			<div class="dts-column-33">
				<?php
				do_settings_sections('dts_smplshare_settings_smpl_sharers');
				do_settings_sections('dts_smplshare_settings_support_amp');
				?>
			</div>

			<div class="dts-column-33">
				<?php
				do_settings_sections('dts_smplshare_settings_default_values');
				?>
			</div>
		</div>

		<div class="dts-clear">
			<?php submit_button(); ?>
		</div>
	</form>
</div>