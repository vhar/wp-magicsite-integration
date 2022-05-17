<?php
	get_header();
	$options = get_option( 'magicsite_intergration_settings_options' );
	if ( isset( $options['magicsite_url'] ) ) {
		if ( have_posts() ) {
			while ( have_posts() ) {
				the_post();
				?>
				<article <?php echo post_class(); ?>>
					<header class="entry-header">
						<h1 class="entry-title"><?php echo the_title(); ?></h1>
					</header>
					<div class="entry-content">
						<?php echo the_content(); ?>
					</div>
				</article>
				<?php
			}
		} else {
			echo '<div class="form-message">Плагин интеграции с MagicSite установлен неверно.</div>';
		}
		?>
	<?php
	} else {
		echo '<div class="form-message">Плагин интеграции с MagicSite не настроен.', 'wp-magicsite-intergation</div>';
	}
	get_footer();
