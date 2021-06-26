<?php

class MagicSite_Walker_Nav_Menu extends Walker_Nav_Menu {
	public $tree_type = array( 'post_type', 'taxonomy', 'custom' );
	public $db_fields = array( 'parent' => 'menu_item_parent', 'id' => 'db_id' );

	public function start_lvl( &$output, $depth = 0, $args = array() ) {
		$indent = str_repeat("\t", $depth);
		$output .= "\n$indent<ul class=\"magicsite-navigation-submenu\">\n";
	}

	public function end_lvl( &$output, $depth = 0, $args = array() ) {
		$indent = str_repeat("\t", $depth);
		$output .= "$indent</ul>\n";
	}

	public function display_element( $element, &$children_elements, $max_depth, $depth, $args, &$output ) {
		if ( isset( $children_elements[$element->ID] ) ){
			$element->has_sub = 1;
		} else{
			$element->has_sub = 0;
		}
		parent::display_element( $element, $children_elements, $max_depth, $depth, $args, $output );
	}

  public function start_el( &$output, $item, $depth = 0, $args = array(), $id = 0 ) {
		$indent = ( $depth ) ? str_repeat( "\t", $depth ) : '';
		$classes = empty( $item->classes ) ? array() : (array) $item->classes;
		$classes[] = 'magicsite-navigation-menu-item-' . $item->ID;
		$classes[] = 'magicsite-navigation-item-level-' . $depth;

		$this->item_classes = apply_filters( 'nav_menu_css_class', array_filter( $classes ), $item, $args, $depth );

    $class_names = join( ' ', $this->item_classes );
		$class_names = $class_names ? ' class="' . esc_attr( $class_names ) . '"' : '';

		$output .= $indent . '<li' . $class_names . '>';

		$atts = [];
		$atts['title'] = $item->attr_title ?? '';
	  $atts['href'] = $item->url ?? '';

    $atts = apply_filters( 'nav_menu_link_attributes', $atts, $item, $args, $depth );

		$attributes = '';
		foreach ( $atts as $attr => $value ) {
			if ( ! empty( $value ) ) {
				$value = ( 'href' === $attr ) ? esc_url( $value ) : esc_attr( $value );
				if ( $attr === 'href' && ( $custom_url = ( isset( $data['custom_url'] ) ? $data['custom_url'] : '' ) ) ){
					$value = do_shortcode( $custom_url );
				}
				$attributes .= ' ' . $attr . '="' . $value . '"';
			}
		}

		$title = apply_filters( 'the_title', $item->title, $item->ID );
		$title = do_shortcode( $title );

		$item_output = '';
		$item_output .= $args->before;
		$item_output .= '<a ' . $attributes . '>';
		$item_output .= $args->link_before . $title . $args->link_after;
		$item_output .= '</a>';

		$output .= apply_filters( 'walker_nav_menu_start_el', $item_output, $item, $depth, $args );
	}

	public function end_el( &$output, $item, $depth = 0, $args = array() ) {
		$output .= "</li>\n";
	}
}
