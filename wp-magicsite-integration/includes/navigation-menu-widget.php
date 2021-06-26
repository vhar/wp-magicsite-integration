<?php
/**
 * MagicSite Menu Navigation Widget
 *
 */

class MagicSiteNavigationMenu_Widget extends WP_Widget {
	public function __construct() {
		$widget_options = [
			'classname'   => 'magicsite-navigation-menu-widget',
			'description' => 'Виджет меню навигации для MagicSiteIntegration',
		];
		parent::__construct( 'magicsite_navigation_menu', 'MagicSite Menu', $widget_options );
  }

	public function widget( $args, $instance ) {
		echo $args['before_widget'];
		echo $args['before_title'] . 'Сведения об образовательной организации' . $args['after_title'];
		$term = get_term_by( 'name', 'MagicSiteMenu', 'nav_menu' );
		$nav_menu_id = $term->term_id;

		$menu_args['menu']            = $nav_menu_id;
		$menu_args['container']       = 'div';
		$menu_args['container_class'] = 'magicsite-navigation-menu-wrapper widget_nav_menu';
		$menu_args['menu_class']      = 'magicsite-navigation-menu';
		$menu_args['menu_id']         = 'magicsite-navigation-menu';
		$menu_args['container_id']    = 'magicsite-nav-' . $nav_menu_id;
		$menu_args['items_wrap']      = '<ul id="%1$s" class="%2$s">%3$s</ul>';
		$menu_args['walker']          = new MagicSite_Walker_Nav_Menu;

		echo wp_nav_menu($menu_args);

		echo $args['after_widget'];
	}
}

add_action( 'widgets_init', function(){
	register_widget('MagicSiteNavigationMenu_Widget');
});
