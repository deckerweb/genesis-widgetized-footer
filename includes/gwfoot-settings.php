<?php
/** Website settings and historical configuration. GPL-2.0-or-later. */
defined( 'ABSPATH' ) || exit;
/**
 * Describe the historical widget areas with translated names.
 * @since 2.0.0
 * @return array Area slug mapped to sidebar identity and labels.
 */
function ddw_gwfoot_areas() {
 return array(
  'footer-one' => array( 'id' => 'gwfoot-footer-one-widget', 'name' => __( 'Footer Area #1', 'genesis-widgetized-footer' ), 'description' => __( 'First widget area within the Genesis footer.', 'genesis-widgetized-footer' ) ),
  'footer-two' => array( 'id' => 'gwfoot-footer-two-widget', 'name' => __( 'Footer Area #2', 'genesis-widgetized-footer' ), 'description' => __( 'Second widget area within the Genesis footer.', 'genesis-widgetized-footer' ) ),
  'footer-disclaimer' => array( 'id' => 'gwfoot-footer-disclaimer-widget', 'name' => __( 'Footer Disclaimer', 'genesis-widgetized-footer' ), 'description' => __( 'Optional disclaimer below the main footer content.', 'genesis-widgetized-footer' ) ),
 );
}
/**
 * Read validated defaults, keeping explicit historical constants authoritative.
 * @since 2.0.0
 * @return array Effective website settings. No persistent writes occur.
 */
function ddw_gwfoot_settings() {
 $saved = get_option( 'gwfoot_settings', array() );
 $settings = ddw_gwfoot_sanitize_settings( is_array( $saved ) ? $saved : array() );
 foreach ( array( 'landing_footer' => 'GWFOOT_REMOVE_LANDING_FOOTER', 'disclaimer' => 'GWFOOT_NO_DISCLAIMER_WIDGET_AREA', 'landing_disclaimer' => 'GWFOOT_LANDING_DISCLAIMER' ) as $key => $constant ) {
  if ( defined( $constant ) ) { $settings[$key] = 'landing_disclaimer' === $key ? (bool) constant( $constant ) : ! constant( $constant ); }
 }
 // Historically this constant controlled the disclaimer on every page.
 if ( defined( 'GWFOOT_LANDING_DISCLAIMER' ) && ! GWFOOT_LANDING_DISCLAIMER ) { $settings['disclaimer'] = false; }
 return $settings;
}
/**
 * Validate the entire small settings object and discard unknown fields.
 * @since 2.0.0
 * @param mixed $input Submitted or stored values.
 * @return array Safe complete settings with historical defaults.
 */
function ddw_gwfoot_sanitize_settings( $input ) {
 $input = is_array( $input ) ? $input : array();
 $out = array();
 foreach ( array( 'landing_footer' => false, 'disclaimer' => true, 'landing_disclaimer' => true ) as $key => $default ) {
  $out[$key] = array_key_exists( $key, $input ) ? in_array( $input[$key], array( true, 1, '1' ), true ) : $default;
 }
 $out['layout'] = isset( $input['layout'] ) && in_array( $input['layout'], array( 'one-third', 'equal', 'two-thirds' ), true ) ? $input['layout'] : 'one-third';
 $out['position'] = isset( $input['position'] ) && in_array( $input['position'], array( 'auto', 'bottom' ), true ) ? $input['position'] : 'auto';
 return $out;
}
/**
 * Detect historical landing templates after the main query is available.
 * @since 2.0.0
 * @return bool Whether this request uses a recognized landing template.
 */
function ddw_gwfoot_is_landing() {
 return is_page_template( array( 'page_landing.php', 'page-landing.php', 'landing.php', 'template-landing.php', 'template_page-landing.php' ) );
}
