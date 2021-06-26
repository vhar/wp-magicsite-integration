<?php
	get_header();
	$options = get_option( 'magicsite_intergration_settings_options' );
	if ( isset( $options['magicsite_url'] ) ) {
		$menu = $magicSite->get_magicsite_nav_menu();
		foreach ( $menu['sveden']['below'] as $item_name => $item ) {
			echo '<div calss="magicsite-menu-item"><a href="/' . $item['type'] . '/' . $item_name . '">' . $item['title'] . '</a></div>';
		}
	} else {
		echo '<div class="form-message">Плагин интеграции с MagicSite не настроен.</div>';
	}
	get_footer();
