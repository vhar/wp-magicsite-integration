<div class='wrap'>
	<h1>Настройки интеграции с MagicSite</h1>
	<form name="" method="POST" action="options.php">
		<?php
		settings_fields( 'magicsite_intergration_settings' );
		do_settings_sections( 'magicsite-integration-settings' );
		submit_button();
		?>
	</form>
</div><div class="clear"></div>
