<?php

class Wporg_Developer_Sidebar_Navigation_Walker extends WPorg_Handbook_Walker {
	public function start_el( &$output, $data_object, $depth = 0, $args = array(), $current_object_id = 0 ) {
		$indent = str_repeat( "\t", $depth );
		$page = $data_object;
		$current_page_id = $current_object_id;

		$css_class = array( 'tree-nav__item' );
		$is_open = false;

		if ( $args['walker']->has_children ) {
			$css_class[] = 'is-expandable';
		}

		if ( ! empty( $current_page_id ) ) {
            $_current_page = get_post( $current_page_id );

            if ( $_current_page && in_array( $page->ID, $_current_page->ancestors, true ) ) {
                $css_class[] = 'current_page_ancestor';
				$is_open = true;
            }

            if ( $page->ID == $current_page_id ) {
                $css_class[] = 'current_page_item';
				$is_open = true;
            } elseif ( $_current_page && $page->ID === $_current_page->post_parent ) {
                $css_class[] = 'current_page_parent';
				$is_open = true;
            }
        } elseif ( get_option( 'page_for_posts' ) == $page->ID ) {
            $css_class[] = 'current_page_parent';
        }

		$css_classes = implode( ' ', $css_class );

		$is_open_attr = '';
		if ( $is_open ) {
			$is_open_attr .= ' open';
		}

		$output .= $indent .
			'<details class="' . $css_classes . '"' . $is_open_attr . '>';

		$output .= '<summary class="tree-nav__item-title"><a href="' . esc_attr( get_permalink( $page ) ) . '">';
		$output .= esc_html( $page->post_title );
		$output .= '</a></summary>';

		$output .= "\n";
	}

	public function end_el( &$output, $item, $depth = 0, $args = array() ) {
		$indent = str_repeat( "\t", $depth );

		$output .= $indent . '</details>';
	}

	public function start_lvl( &$output, $depth = 0, $args = array() ) {
		if ( isset( $args['item_spacing'] ) && 'preserve' === $args['item_spacing'] ) {
			$t = "\t";
			$n = "\n";
		} else {
			$t = '';
			$n = '';
		}
		$indent  = str_repeat( $t, $depth );
		$output .= "{$n}{$indent}{$n}";
	}

	public function end_lvl( &$output, $depth = 0, $args = array() ) {
		if ( isset( $args['item_spacing'] ) && 'preserve' === $args['item_spacing'] ) {
			$t = "\t";
			$n = "\n";
		} else {
			$t = '';
			$n = '';
		}
		$indent  = str_repeat( $t, $depth );
		$output .= "{$indent}{$n}";
	}
}
