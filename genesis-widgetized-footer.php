<?php
/**
 * Plugin Name: Genesis Widgetized Footer
 * Plugin URI: https://github.com/deckerweb/genesis-widgetized-footer
 * Description: Customize the Genesis footer and optional disclaimer with widgets, responsive layouts and shortcodes.
 * Version: 2.0.2
 * Requires at least: 6.4
 * Requires PHP: 8.1
 * Author: David Decker – DECKERWEB
 * Author URI: https://github.com/deckerweb
 * License: GPL v2 or later
 * License URI: https://www.gnu.org/licenses/gpl-2.0.html
 * Text Domain: genesis-widgetized-footer
 * Domain Path: /languages/
 * Update URI: https://github.com/deckerweb/genesis-widgetized-footer
 * GitHub Plugin URI: https://github.com/deckerweb/genesis-widgetized-footer
 *
 * Copyright © 2012–2026 David Decker – DECKERWEB.
 * SPDX-License-Identifier: GPL-2.0-or-later
 */
defined( 'ABSPATH' ) || exit;
define( 'GWFOOT_VERSION', '2.0.2' );
define( 'GWFOOT_PLUGIN_FILE', __FILE__ );
define( 'GWFOOT_PLUGIN_DIR', __DIR__ );
define( 'GWFOOT_PLUGIN_BASEDIR', dirname( plugin_basename( __FILE__ ) ) );
/**
 * Filter the relative directory containing bundled host translations.
 * @since 1.0.0
 * @param string $directory Relative plugin language directory.
 */
define( 'GWFOOT_LANG_DIR', apply_filters( 'gwfoot_filter_lang_dir', GWFOOT_PLUGIN_BASEDIR . '/languages/' ) );
define( 'GWFOOT_SCRIPT_SUFFIX', defined( 'SCRIPT_DEBUG' ) && SCRIPT_DEBUG ? '' : '.min' );
require_once __DIR__ . '/includes/deckerweb-plugin-library/bootstrap.php';
deckerweb_library_register_v2( __FILE__, array(), __DIR__ . '/includes/deckerweb-plugin-library' );
require_once __DIR__ . '/includes/gwfoot-settings.php';
add_action( 'init', 'ddw_gwfoot_setup', 1 );
add_action( 'widgets_init', 'ddw_gwfoot_register_widget_areas' );
/**
 * Load translations, host components and only the current site's supported theme features.
 * @since 1.0.0
 * @return void
 */
function ddw_gwfoot_setup() {
 $domain = 'genesis-widgetized-footer';
 $locale = apply_filters( 'plugin_locale', determine_locale(), $domain );
 if ( is_string( $locale ) && preg_match( '/^[a-zA-Z0-9_@-]+$/D', $locale ) ) {
  /**
   * Filter the custom translation override path.
   * @since 1.0.0
   * @param string $path Local MO path, loaded before bundled translations.
   */
  $path = apply_filters( 'gwfoot_filter_wp_lang_dir', WP_LANG_DIR . '/' . $domain . '/' . $domain . '-' . $locale . '.mo' );
  if ( is_string( $path ) && is_readable( $path ) ) { load_textdomain( $domain, $path ); }
 }
 load_plugin_textdomain( $domain, false, GWFOOT_LANG_DIR );
 require_once GWFOOT_PLUGIN_DIR . '/includes/gwfoot-updater.php';
 ddw_gwfoot_register_updater();
 if ( is_admin() ) { require_once GWFOOT_PLUGIN_DIR . '/includes/gwfoot-admin.php'; }
 if ( ! ddw_gwfoot_has_genesis() ) { return; }
 require_once GWFOOT_PLUGIN_DIR . '/includes/gwfoot-frontend.php';
 if ( ! defined( 'GWFOOT_SHORTCODE_FEATURES' ) || GWFOOT_SHORTCODE_FEATURES ) {
  require_once GWFOOT_PLUGIN_DIR . '/includes/gwfoot-shortcodes.php';
 }
 if ( ( ! defined( 'GWFOOT_NO_WIDGETS_SHORTCODE' ) || ! GWFOOT_NO_WIDGETS_SHORTCODE ) && ! has_filter( 'widget_text', 'do_shortcode' ) ) {
  add_filter( 'widget_text', 'do_shortcode' );
 }
}
/**
 * Detect Genesis using the active parent and its public API on this website.
 * @since 2.0.0
 * @return bool Whether theme features may run.
 */
function ddw_gwfoot_has_genesis() {
 return 'genesis' === get_template() && function_exists( 'genesis_register_widget_area' );
}
/**
 * Register the three historical sidebar identities without migrating user content.
 * @since 1.0.0
 * @return void
 */
function ddw_gwfoot_register_widget_areas() {
 if ( ! ddw_gwfoot_has_genesis() ) { return; }
 foreach ( ddw_gwfoot_areas() as $area => $data ) {
  if ( 'footer-disclaimer' === $area && ( ( defined( 'GWFOOT_NO_DISCLAIMER_WIDGET_AREA' ) && GWFOOT_NO_DISCLAIMER_WIDGET_AREA ) || function_exists( 'agentpress_disclaimer' ) || function_exists( 'realpro_disclaimer' ) ) ) { continue; }
  $key = str_replace( '-', '_', $area );
  /**
   * Filter the widget area title (footer_one, footer_two or footer_disclaimer).
   * @since 1.0.0
   * @param string $title Translated title returned to register_sidebar().
   */
  $title = apply_filters( 'gwfoot_filter_' . $key . '_widget_title', $data['name'] );
  /**
   * Filter the widget area description for the corresponding title filter.
   * @since 1.0.0
   * @param string $description Translated sidebar description.
   */
  $description = apply_filters( 'gwfoot_filter_' . $key . '_widget_description', $data['description'] );
  register_sidebar( array( 'id' => $data['id'], 'name' => $title, 'description' => $description,
   'before_widget' => '<div id="%1$s" class="gwfoot-' . esc_attr( $area ) . ' widget-area %2$s">',
   'after_widget' => '</div>', 'before_title' => '<h3 class="widget-title widgettitle">', 'after_title' => '</h3>' ) );
 }
}
/**
 * Read a single plugin header value for legacy integrations.
 * @since 1.3.0
 * @param string $key WordPress plugin header key.
 * @return string Header value, or an empty string for unknown keys.
 */
function ddw_gwfoot_plugin_get_data( $key ) {
 if ( ! function_exists( 'get_plugin_data' ) ) { require_once ABSPATH . 'wp-admin/includes/plugin.php'; }
 $data = get_plugin_data( GWFOOT_PLUGIN_FILE, false, false );
 return is_string( $key ) && isset( $data[$key] ) ? $data[$key] : '';
}
