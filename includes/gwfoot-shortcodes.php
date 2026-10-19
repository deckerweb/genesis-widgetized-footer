<?php
/** Backward-compatible footer shortcodes. GPL-2.0-or-later. */
defined( 'ABSPATH' ) || exit;
add_shortcode( 'gwfoot-widget-area', 'ddw_gwfoot_shortcode_widget_area' );
add_shortcode( 'gwfoot-lastupdated', 'ddw_gwfoot_shortcode_last_updated' );
/**
 * Return widgets at the shortcode's position; never emit content directly.
 * @since 1.4.0
 * @param array|string $atts Shortcode attributes with the historical area slug.
 * @return string Buffered area output.
 */
function ddw_gwfoot_shortcode_widget_area( $atts ) {
 $atts = shortcode_atts( array( 'area' => '' ), $atts, 'gwfoot-widget-area' );
 $output = is_string( $atts['area'] ) ? ddw_gwfoot_area_html( $atts['area'] ) : '';
 // Preserve the historical misspelled filter only when Not Found does not own it.
 // Both plugins previously used the same name; applying it here together mixes outputs.
 if ( ! defined( 'GWNF_PLUGIN_FILE' ) && ! function_exists( 'ddw_gwnf_shortcode_widget_area' ) ) {
  /**
   * Legacy misnamed filter, retained for sites using the old customization contract.
   * @since 1.4.0
   * @deprecated 2.0.0 Use gwfoot_filter_shortcode_widget_area.
   * @param string $output Buffered footer area HTML.
   */
  $output = apply_filters( 'gwnf_filter_shortcode_widget_area', $output );
 }
 /**
  * Filter returned footer shortcode markup.
  * @since 2.0.0
  * @param string $output Buffered HTML returned to WordPress.
  */
 return apply_filters( 'gwfoot_filter_shortcode_widget_area', $output );
}
/**
 * Find the most recently modified published item in the historical supported types.
 * Results are memoized for the current request and website only.
 * @since 1.4.0
 * @return WP_Post|false Most recently modified public item, or false.
 */
function ddw_gwfoot_get_latest_updated_post() {
 static $cache = array();
 /**
  * Filter the latest-publication query while preserving publish-only visibility.
  * @since 1.4.0
  * @param array $args Query arguments passed to get_posts().
  */
 $args = apply_filters( 'gwfoot_filter_latest_update_date_query_args', array( 'post_type' => array( 'post', 'page', 'product', 'download' ), 'numberposts' => 1, 'post_status' => 'publish', 'orderby' => 'modified', 'order' => 'DESC', 'update_post_meta_cache' => false, 'update_post_term_cache' => false ) );
 $args = is_array( $args ) ? $args : array();
 $args['post_status'] = 'publish';
 $args['has_password'] = false;
 $args['numberposts'] = 1;
 $key = get_current_blog_id() . ':' . md5( wp_json_encode( $args ) );
 if ( ! array_key_exists( $key, $cache ) ) { $posts = get_posts( $args ); $cache[$key] = $posts[0] ?? false; }
 return $cache[$key];
}
/**
 * Return the localized latest-update date with a validated wrapper element.
 * @since 1.4.0
 * @param array|string $atts Date format, wrapper, classes and surrounding plain text.
 * @return string Safe date markup or an empty string when no publication exists.
 */
function ddw_gwfoot_shortcode_last_updated( $atts ) {
 $atts = shortcode_atts( array( 'date_format' => get_option( 'date_format' ), 'wrapper' => 'p', 'class' => '', 'text_before' => __( 'Latest update date:', 'genesis-widgetized-footer' ), 'text_after' => '' ), $atts, 'gwfoot-lastupdated' );
 $post = ddw_gwfoot_get_latest_updated_post();
 if ( ! $post ) { return ''; }
 $tag = in_array( $atts['wrapper'], array( 'p', 'div', 'span', 'small', 'time' ), true ) ? $atts['wrapper'] : 'p';
 $classes = array_map( 'sanitize_html_class', preg_split( '/\s+/', (string) $atts['class'] ) );
 $date = get_post_modified_time( (string) $atts['date_format'], false, $post, true );
 $datetime = 'time' === $tag ? ' datetime="' . esc_attr( get_post_modified_time( 'c', false, $post ) ) . '"' : '';
 $output = '<' . $tag . $datetime . ' class="gwfoot-lastupdated ' . esc_attr( implode( ' ', $classes ) ) . '">' . esc_html( ( $atts['text_before'] ? $atts['text_before'] . ' ' : '' ) . $date . ( $atts['text_after'] ? ' ' . $atts['text_after'] : '' ) ) . '</' . $tag . '>';
 /**
  * Filter the latest-update shortcode markup after escaping the plain text.
  * @since 1.4.0
  * @param string $output Complete HTML returned by the shortcode.
  */
 return apply_filters( 'gwfoot_filter_shortcode_last_updated', $output );
}
