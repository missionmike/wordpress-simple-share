<?php
    defined( 'ABSPATH' ) or die( 'No script kiddies please!' );
?>

<div class="wrap">
	<h2>DT's Simple Share Settings</h2>

	<form method="post" action="options.php"> 
		<?php
		    settings_fields( 'dts_smplshare_settings' );

		    do_settings_sections( 'dts_smplshare_settings' );

		    submit_button();
		?>
	</form>
</div>