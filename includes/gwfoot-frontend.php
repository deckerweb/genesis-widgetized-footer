<?php
/** Genesis footer integration. GPL-2.0-or-later. */
defined( 'ABSPATH' ) || exit;
add_action( 'genesis_before', 'gwfoot_do_footer_widgets' );
add_action( 'wp_enqueue_scripts', 'ddw_gwfoot_styles' );
/**
 * Select the historical footer location without copying theme implementation code.
 * @since 2.0.0
 * @return array Hook, priority, original callback and optional supplementary callback.
 */
function ddw_gwfoot_location() {
 if ( defined( 'PROSE_SETTINGS_FIELD' ) ) { return array( 'genesis_after', 10, 'genesis_do_footer', '' ); }
 if ( function_exists( 'metro_load_google_font' ) ) { return array( 'genesis_after', 12, 'genesis_do_footer', '' ); }
 if ( function_exists( 'modernblogger_comment_form_args' ) ) { return array( 'genesis_footer', 10, 'child_do_footer', '' ); }
 if ( function_exists( 'pretty_footer_top' ) ) { return array( 'genesis_footer', 10, 'genesis_do_footer', 'pretty_footer_top' ); }
 if ( function_exists( 'pretty_do_footer' ) || function_exists( 'pretty_do_footer_top' ) ) { return array( 'genesis_footer', 10, 'pretty_do_footer', 'pretty_do_footer_top' ); }
 return array( 'genesis_footer', 10, 'genesis_do_footer', '' );
}
/**
 * Check whether at least one main footer area has assigned widgets.
 * @since 2.0.0
 * @return bool Whether a main footer replacement is available.
 */
function ddw_gwfoot_has_footer_widgets() {
 return is_active_sidebar( 'gwfoot-footer-one-widget' ) || is_active_sidebar( 'gwfoot-footer-two-widget' );
}
/**
 * Attach active content after child-theme setup and the main query.
 * Empty main areas keep the original footer. Landing templates are evaluated first.
 * @since 1.0.0
 * @return void
 */
function gwfoot_do_footer_widgets() {
 $settings = ddw_gwfoot_settings();
 $GLOBALS['gwfoot_footer_one_width'] = is_active_sidebar( 'gwfoot-footer-two-widget' ) ? 'one-third' : 'full-width';
 $GLOBALS['gwfoot_footer_two_width'] = is_active_sidebar( 'gwfoot-footer-one-widget' ) ? 'two-thirds' : 'full-width';
 list( $hook, $priority, $original, $extra ) = ddw_gwfoot_location();
 if ( ddw_gwfoot_has_footer_widgets() && ( ! ddw_gwfoot_is_landing() || $settings['landing_footer'] ) ) {
  remove_action( $hook, $original, $priority );
  if ( $extra ) { remove_action( 'genesis_after_footer', $extra ); }
  add_action( $hook, 'ddw_gwfoot_render_footer', $priority );
 }
 if ( $settings['disclaimer'] && is_active_sidebar( 'gwfoot-footer-disclaimer-widget' ) && ( ! ddw_gwfoot_is_landing() || $settings['landing_disclaimer'] )
  && ! function_exists( 'agentpress_disclaimer' ) && ! function_exists( 'realpro_disclaimer' ) ) {
  $disclaimer_hook = 'bottom' === $settings['position'] || ! empty( $GLOBALS['gwfoot_force_bottom'] ) || 'genesis_after' === $hook ? 'genesis_after' : 'genesis_footer';
  add_action( $disclaimer_hook, 'gwfoot_display_footer_disclaimer_section', 15 );
 }
}
/**
 * Render the active main areas in one responsive layout wrapper.
 * @since 2.0.0
 * @return void
 */
function ddw_gwfoot_render_footer() {
 $layout = ddw_gwfoot_settings()['layout'];
 $both = is_active_sidebar( 'gwfoot-footer-one-widget' ) && is_active_sidebar( 'gwfoot-footer-two-widget' );
 echo '<div class="gwfoot-footer-areas gwfoot-layout-' . esc_attr( $both ? $layout : 'full' ) . '">';
 if ( is_active_sidebar( 'gwfoot-footer-one-widget' ) ) { gwfoot_display_footer_one_section(); }
 if ( is_active_sidebar( 'gwfoot-footer-two-widget' ) ) { gwfoot_display_footer_two_section(); }
 echo '</div>';
}
/**
 * Return buffered widgets, with a request-local recursion guard and unique IDs.
 * Widget HTML is trusted to the widget's own WordPress rendering contract.
 * @since 2.0.0
 * @param string $area Historical area slug.
 * @return string Widget markup, or empty for inactive, unknown or recursive areas.
 */
function ddw_gwfoot_area_html( $area ) {
 static $rendering = array(), $instances = array();
 $areas = ddw_gwfoot_areas();
 if ( ! isset( $areas[$area] ) || ! is_active_sidebar( $areas[$area]['id'] ) || ! empty( $rendering[$area] ) ) { return ''; }
 $rendering[$area] = true;
 $instances[$area] = ( $instances[$area] ?? 0 ) + 1;
 $id = 'gwfoot-' . $area . '-area' . ( $instances[$area] > 1 ? '-' . $instances[$area] : '' );
 $class = 'gwfoot-' . $area . '-section';
 if ( 'footer-disclaimer' !== $area ) {
  $both = is_active_sidebar( 'gwfoot-footer-one-widget' ) && is_active_sidebar( 'gwfoot-footer-two-widget' );
  $class .= ' gwfoot-' . $area . '-' . ( $both ? ( 'footer-one' === $area ? 'one-third' : 'two-thirds' ) : 'full-width' );
 }
 $level = ob_get_level();
 ob_start();
 try {
  dynamic_sidebar( $areas[$area]['id'] );
  $html = ob_get_clean();
 } finally {
  while ( ob_get_level() > $level ) { ob_end_clean(); }
  unset( $rendering[$area] );
 }
 return '<div id="' . esc_attr( $id ) . '" class="' . esc_attr( $class ) . '">' . ( 'footer-disclaimer' === $area ? '<div class="gwfoot-footer-disclaimer-content">' . $html . '</div>' : $html ) . '</div>';
}
/**
 * Print the first historical footer area.
 * @since 1.0.0
 * @return void
 */
function gwfoot_display_footer_one_section() { echo ddw_gwfoot_area_html( 'footer-one' ); }
/**
 * Print the second historical footer area.
 * @since 1.0.0
 * @return void
 */
function gwfoot_display_footer_two_section() { echo ddw_gwfoot_area_html( 'footer-two' ); }
/**
 * Print the optional historical disclaimer area.
 * @since 1.0.0
 * @return void
 */
function gwfoot_display_footer_disclaimer_section() { echo ddw_gwfoot_area_html( 'footer-disclaimer' ); }
/**
 * Remove this plugin's main replacement on recognized landing pages.
 * Kept callable for existing integrations; normal registration evaluates this first.
 * @since 1.2.0
 * @return void
 */
function gwfoot_landing_templates_undo_footer_widgets() {
 if ( ! ddw_gwfoot_is_landing() || ddw_gwfoot_settings()['landing_footer'] ) { return; }
 list( $hook, $priority ) = ddw_gwfoot_location();
 remove_action( $hook, 'ddw_gwfoot_render_footer', $priority );
 remove_action( $hook, 'gwfoot_display_footer_one_section', $priority );
 remove_action( $hook, 'gwfoot_display_footer_two_section', $priority );
}
/**
 * Move the disclaimer to genesis_after for historical custom snippets.
 * @since 1.0.0
 * @return void
 */
function __gwfoot_footer_disclaimer_bottom() {
 $GLOBALS['gwfoot_force_bottom'] = true;
 $registered = false !== has_action( 'genesis_footer', 'gwfoot_display_footer_disclaimer_section' ) || false !== has_action( 'genesis_after', 'gwfoot_display_footer_disclaimer_section' );
 remove_action( 'genesis_footer', 'gwfoot_display_footer_disclaimer_section', 15 );
 remove_action( 'genesis_after', 'gwfoot_display_footer_disclaimer_section', 15 );
 if ( $registered ) { add_action( 'genesis_after', 'gwfoot_display_footer_disclaimer_section', 15 ); }
}
/**
 * Enqueue local, stable-versioned CSS only while a plugin area is populated.
 * Area shortcodes can be used outside the automatic footer, including on landing pages.
 * @since 1.0.0
 * @return void
 */
function ddw_gwfoot_styles() {
 foreach ( ddw_gwfoot_areas() as $area ) {
  if ( is_active_sidebar( $area['id'] ) ) {
   wp_enqueue_style( 'genesis-widgetized-footer', plugins_url( 'css/gwfoot-styles.css', GWFOOT_PLUGIN_FILE ), array(), GWFOOT_VERSION );
   /**
    * Enqueue additional site-specific footer styles after the plugin handle.
    * @since 1.0.0
    */
   do_action( 'gwfoot_load_styles' );
   break;
  }
 }
}
