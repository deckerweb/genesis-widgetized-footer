<?php
/** Website administration and local documentation. GPL-2.0-or-later. */
defined( 'ABSPATH' ) || exit;
add_action( 'admin_menu', 'ddw_gwfoot_admin_menu', 20 );
add_action( 'admin_init', 'ddw_gwfoot_register_settings' );
add_action( 'admin_enqueue_scripts', 'ddw_gwfoot_admin_assets' );
add_filter( 'plugin_action_links_' . plugin_basename( GWFOOT_PLUGIN_FILE ), 'ddw_gwfoot_widgets_page_link' );
add_filter( 'option_page_capability_gwfoot_settings', 'ddw_gwfoot_settings_capability' );
/**
 * Permit widget administrators to save this website's settings.
 * @since 2.0.0
 * @return string Required WordPress capability.
 */
function ddw_gwfoot_settings_capability() { return 'edit_theme_options'; }
/**
 * Register settings under Genesis, or Settings when the parent theme is unavailable.
 * @since 2.0.0
 * @return void
 */
function ddw_gwfoot_admin_menu() {
 add_submenu_page( ddw_gwfoot_has_genesis() ? 'genesis' : 'options-general.php', __( 'Widgetized Footer', 'genesis-widgetized-footer' ), __( 'Widgetized Footer', 'genesis-widgetized-footer' ), 'edit_theme_options', 'gwfoot-settings', 'ddw_gwfoot_settings_page' );
}
/**
 * Register the Settings API sanitizer; WordPress supplies nonce and capability checks.
 * @since 2.0.0
 * @return void
 */
function ddw_gwfoot_register_settings() {
 register_setting( 'gwfoot_settings', 'gwfoot_settings', array( 'type' => 'array', 'sanitize_callback' => 'ddw_gwfoot_sanitize_settings', 'show_in_rest' => false ) );
}
/**
 * Return the current website's settings URL.
 * @since 2.0.0
 * @return string Local admin URL.
 */
function ddw_gwfoot_settings_url() {
 return admin_url( ( ddw_gwfoot_has_genesis() ? 'admin.php' : 'options-general.php' ) . '?page=gwfoot-settings' );
}
/**
 * Add the representative Settings link ahead of native plugin actions.
 * @since 1.0.0
 * @param array $links Existing action links.
 * @return array Permission-appropriate action links.
 */
function ddw_gwfoot_widgets_page_link( $links ) {
 if ( ! is_network_admin() && current_user_can( 'edit_theme_options' ) ) {
  array_unshift( $links, '<a href="' . esc_url( ddw_gwfoot_settings_url() ) . '">' . esc_html__( 'Settings', 'genesis-widgetized-footer' ) . '</a>' );
 }
 /**
  * Filter this plugin's action links.
  * @since 1.0.0
  * @param array $links Permission-appropriate plugin action links.
  */
 return apply_filters( 'gwfoot_filter_settings_page_link', $links );
}
/**
 * Enqueue local styles and progressive controls only on this settings screen.
 * @since 2.0.0
 * @param string $hook Current screen hook suffix.
 * @return void
 */
function ddw_gwfoot_admin_assets( $hook ) {
 if ( ! in_array( $hook, array( 'genesis_page_gwfoot-settings', 'settings_page_gwfoot-settings' ), true ) ) { return; }
 wp_enqueue_style( 'gwfoot-admin', plugins_url( 'css/gwfoot-admin.css', GWFOOT_PLUGIN_FILE ), array(), GWFOOT_VERSION );
 wp_enqueue_style( 'ddw-document-dialogs-v1', plugins_url( 'css/deckerweb-footer.css', GWFOOT_PLUGIN_FILE ), array(), GWFOOT_VERSION );
 wp_enqueue_script( 'ddw-document-dialogs-v1', plugins_url( 'js/deckerweb-footer.js', GWFOOT_PLUGIN_FILE ), array(), GWFOOT_VERSION, true );
 wp_enqueue_script( 'gwfoot-admin', plugins_url( 'js/gwfoot-admin.js', GWFOOT_PLUGIN_FILE ), array(), GWFOOT_VERSION, true );
 wp_localize_script( 'gwfoot-admin', 'gwfootCopy', array( 'success' => __( 'Copied.', 'genesis-widgetized-footer' ), 'manual' => __( 'Select and copy the shortcode manually.', 'genesis-widgetized-footer' ) ) );
}
/**
 * Print a labeled binary setting with a reliable unchecked value.
 * @since 2.0.0
 * @param string $key Valid settings key.
 * @param string $label Already translated visible label.
 * @param array $settings Effective settings.
 * @return void
 */
function ddw_gwfoot_checkbox( $key, $label, $settings ) {
 echo '<p><input type="hidden" name="gwfoot_settings[' . esc_attr( $key ) . ']" value="0"><label><input id="gwfoot-' . esc_attr( $key ) . '" type="checkbox" name="gwfoot_settings[' . esc_attr( $key ) . ']" value="1" ' . checked( $settings[$key], true, false ) . '> ' . esc_html( $label ) . '</label></p>';
}
/**
 * Render setup status, website settings, preview and safe local documentation.
 * @since 2.0.0
 * @return void
 */
function ddw_gwfoot_settings_page() {
 if ( ! current_user_can( 'edit_theme_options' ) ) { wp_die( esc_html__( 'You are not allowed to manage these settings.', 'genesis-widgetized-footer' ), '', array( 'response' => 403 ) ); }
 $settings = ddw_gwfoot_settings();
 echo '<div class="wrap gwfoot-settings"><header class="gwfoot-header"><img class="gwfoot-mark" src="' . esc_url( plugins_url( 'assets/icon.svg', GWFOOT_PLUGIN_FILE ) ) . '" width="64" height="64" alt=""><div><h1>Genesis Widgetized Footer</h1><p>' . esc_html__( 'Your Genesis footer. Your widgets.', 'genesis-widgetized-footer' ) . '</p></div></header><hr class="wp-header-end">';
 settings_errors( 'gwfoot_settings' );
 // Core redirects store the generic success notice under general rather than the option name.
 settings_errors( 'general' );
 if ( ! ddw_gwfoot_has_genesis() ) {
  echo '<div class="notice notice-warning inline"><p>' . esc_html__( 'Genesis is not active on this website. Footer features are paused; saved widgets and settings are preserved.', 'genesis-widgetized-footer' ) . '</p></div>';
 }
 $constants = array_filter( array( 'GWFOOT_REMOVE_LANDING_FOOTER', 'GWFOOT_NO_DISCLAIMER_WIDGET_AREA', 'GWFOOT_LANDING_DISCLAIMER', 'GWFOOT_NO_WIDGETS_SHORTCODE', 'GWFOOT_SHORTCODE_FEATURES' ), 'defined' );
 if ( $constants ) {
  echo '<div class="notice notice-info inline"><p>' . esc_html__( 'Existing code configuration takes precedence over matching settings:', 'genesis-widgetized-footer' ) . ' <code>' . esc_html( implode( ', ', $constants ) ) . '</code></p></div>';
 }
 echo '<section class="gwfoot-card" aria-labelledby="gwfoot-setup"><h2 id="gwfoot-setup">' . esc_html__( 'Setup overview', 'genesis-widgetized-footer' ) . '</h2><p>' . esc_html__( 'These areas replace the copyright and credits footer, independently of the theme’s regular footer widget columns.', 'genesis-widgetized-footer' ) . '</p><ul class="gwfoot-areas">';
 $widgets = wp_get_sidebars_widgets();
 foreach ( ddw_gwfoot_areas() as $slug => $area ) {
  $count = count( (array) ( $widgets[$area['id']] ?? array() ) );
  $available = ddw_gwfoot_has_genesis() && isset( $GLOBALS['wp_registered_sidebars'][$area['id']] );
  echo '<li><h3>' . esc_html( $area['name'] ) . '</h3><p>' . esc_html__( 'Assigned widgets:', 'genesis-widgetized-footer' ) . ' <strong>' . esc_html( number_format_i18n( $count ) ) . '</strong></p>';
  if ( ! $available ) { echo '<p>' . esc_html__( 'This area is unavailable with the current theme or code configuration.', 'genesis-widgetized-footer' ) . '</p>'; }
  elseif ( 'footer-disclaimer' === $slug && ! $settings['disclaimer'] ) { echo '<p>' . esc_html__( 'Automatic disclaimer output is disabled.', 'genesis-widgetized-footer' ) . '</p>'; }
  elseif ( ! $count ) { echo '<p>' . esc_html( 'footer-disclaimer' === $slug ? __( 'No disclaimer is displayed.', 'genesis-widgetized-footer' ) : __( 'An empty area stays hidden. If both main areas are empty, Genesis keeps its standard footer.', 'genesis-widgetized-footer' ) ) . '</p>'; }
  if ( $available ) { echo '<a href="' . esc_url( admin_url( 'widgets.php#' . $area['id'] ) ) . '">' . esc_html__( 'Edit this widget area', 'genesis-widgetized-footer' ) . '</a>'; }
  echo '</li>';
 }
 echo '</ul><p class="description">' . esc_html__( 'Widgets may have their own visibility rules. Check the footer while logged out as well.', 'genesis-widgetized-footer' ) . '</p></section>';
 echo '<form method="post" action="' . esc_url( admin_url( 'options.php' ) ) . '" class="gwfoot-card">';
 settings_fields( 'gwfoot_settings' );
 echo '<h2>' . esc_html__( 'Footer display', 'genesis-widgetized-footer' ) . '</h2><p>' . esc_html__( 'These settings apply only to this website.', 'genesis-widgetized-footer' ) . '</p>';
 echo '<p><label for="gwfoot-layout"><strong>' . esc_html__( 'Two-column layout', 'genesis-widgetized-footer' ) . '</strong></label></p><select id="gwfoot-layout" name="gwfoot_settings[layout]">';
 foreach ( array( 'one-third' => __( 'One third / two thirds', 'genesis-widgetized-footer' ), 'equal' => __( 'Equal columns', 'genesis-widgetized-footer' ), 'two-thirds' => __( 'Two thirds / one third', 'genesis-widgetized-footer' ) ) as $value => $label ) {
  echo '<option value="' . esc_attr( $value ) . '" ' . selected( $settings['layout'], $value, false ) . '>' . esc_html( $label ) . '</option>';
 }
 echo '</select><p class="description">' . esc_html__( 'One populated area uses the full width. Columns stack on small screens.', 'genesis-widgetized-footer' ) . '</p>';
 ddw_gwfoot_checkbox( 'landing_footer', __( 'Display the main footer areas on landing-page templates', 'genesis-widgetized-footer' ), $settings );
 echo '<h2>' . esc_html__( 'Footer Disclaimer', 'genesis-widgetized-footer' ) . '</h2>';
 ddw_gwfoot_checkbox( 'disclaimer', __( 'Display the disclaimer when widgets are assigned', 'genesis-widgetized-footer' ), $settings );
 ddw_gwfoot_checkbox( 'landing_disclaimer', __( 'Also display the disclaimer on landing-page templates', 'genesis-widgetized-footer' ), $settings );
 echo '<p><label for="gwfoot-position"><strong>' . esc_html__( 'Disclaimer position', 'genesis-widgetized-footer' ) . '</strong></label></p><select id="gwfoot-position" name="gwfoot_settings[position]">';
 foreach ( array( 'auto' => __( 'Within the theme footer', 'genesis-widgetized-footer' ), 'bottom' => __( 'After the theme footer', 'genesis-widgetized-footer' ) ) as $value => $label ) { echo '<option value="' . esc_attr( $value ) . '" ' . selected( $settings['position'], $value, false ) . '>' . esc_html( $label ) . '</option>'; }
 echo '</select>';
 submit_button();
 echo '</form><section class="gwfoot-card"><h2>' . esc_html__( 'Preview and test', 'genesis-widgetized-footer' ) . '</h2><p><a class="button" href="' . esc_url( home_url( '/' ) ) . '" target="_blank" rel="noopener noreferrer">' . esc_html__( 'Open the frontend in a new tab', 'genesis-widgetized-footer' ) . '</a> <a class="button" href="' . esc_url( add_query_arg( array( 'url' => home_url( '/' ), 'autofocus[panel]' => 'widgets' ), admin_url( 'customize.php' ) ) ) . '">' . esc_html__( 'Preview in the Customizer', 'genesis-widgetized-footer' ) . '</a></p><p>' . esc_html__( 'Preview a normal page and a landing page. Also test in a private browser window.', 'genesis-widgetized-footer' ) . '</p></section>';
 echo '<section class="gwfoot-card"><h2>' . esc_html__( 'Copy shortcodes', 'genesis-widgetized-footer' ) . '</h2>';
 $codes = array( 'footer-one' => '[gwfoot-widget-area area="footer-one"]', 'footer-two' => '[gwfoot-widget-area area="footer-two"]', 'footer-disclaimer' => '[gwfoot-widget-area area="footer-disclaimer"]', 'lastupdated' => '[gwfoot-lastupdated]' );
 $areas = ddw_gwfoot_areas();
 foreach ( $codes as $key => $code ) {
  $label = $areas[$key]['name'] ?? __( 'Latest update date', 'genesis-widgetized-footer' );
  echo '<div class="gwfoot-shortcode"><label for="gwfoot-code-' . esc_attr( $key ) . '">' . esc_html( $label ) . '</label><div><input id="gwfoot-code-' . esc_attr( $key ) . '" type="text" readonly value="' . esc_attr( $code ) . '"><button hidden type="button" class="button gwfoot-copy" data-gwfoot-copy="gwfoot-code-' . esc_attr( $key ) . '">' . esc_html__( 'Copy shortcode', 'genesis-widgetized-footer' ) . '</button></div></div>';
 }
 echo '<p id="gwfoot-copy-status" role="status" aria-live="polite"></p><p class="description">' . esc_html__( 'Widget-area shortcodes display assigned widgets at their insertion point, independently of automatic footer display settings.', 'genesis-widgetized-footer' ) . '</p></section>';
 ddw_gwfoot_admin_footer();
 echo '</div>';
}
/**
 * Render local EN/DE documentation with native dialogs and real file fallbacks.
 * @since 2.0.0
 * @return void
 */
function ddw_gwfoot_admin_footer() {
 require_once GWFOOT_PLUGIN_DIR . '/includes/deckerweb-changelog-v1.php';
 require_once GWFOOT_PLUGIN_DIR . '/includes/deckerweb-admin-footer-v1.php';
 $de = str_starts_with( determine_locale(), 'de_' );
 $formal = 'de_DE_formal' === determine_locale();
 $documents = array();
 foreach ( array( 'readme' => array( $de ? ( $formal ? 'README-de-formal.txt' : 'README-de.txt' ) : 'README.txt', __( 'Documentation', 'genesis-widgetized-footer' ) ), 'faq' => array( $de ? ( $formal ? 'FAQ-de-formal.txt' : 'FAQ-de.txt' ) : 'FAQ.txt', __( 'FAQ', 'genesis-widgetized-footer' ) ), 'changelog' => array( $de ? ( $formal ? 'CHANGELOG-de-formal.txt' : 'CHANGELOG-de.txt' ) : 'CHANGELOG.txt', __( 'Changelog', 'genesis-widgetized-footer' ) ) ) as $key => $data ) {
  $documents[$key] = array( 'label' => $data[1], 'url' => plugins_url( $data[0], GWFOOT_PLUGIN_FILE ), 'text' => (string) file_get_contents( GWFOOT_PLUGIN_DIR . '/' . $data[0] ) );
 }
 \Deckerweb\AdminFooter\V1\StructuredFooter::render( array( 'id' => 'gwfoot', 'about' => __( 'About this plugin', 'genesis-widgetized-footer' ), 'name' => 'Genesis Widgetized Footer', 'version' => GWFOOT_VERSION, 'version_label' => __( 'Version', 'genesis-widgetized-footer' ), 'slogan' => __( 'Your Genesis footer. Your widgets.', 'genesis-widgetized-footer' ), 'copyright' => '© 2012–2026', 'repository' => 'https://github.com/deckerweb', 'website' => __( 'More plugins', 'genesis-widgetized-footer' ), 'close' => __( 'Close', 'genesis-widgetized-footer' ), 'documents' => $documents ) );
}

add_filter( 'plugin_row_meta', 'ddw_gwfoot_plugin_links', 10, 2 );
add_action( 'load-widgets.php', 'ddw_gwfoot_widgets_help_tab' );
/**
 * Keep useful local documentation links in plugin metadata.
 * @since 1.0.0
 * @param array $links Existing row metadata links.
 * @param string $file Plugin basename for this row.
 * @return array Filtered metadata for this plugin only.
 */
function ddw_gwfoot_plugin_links( $links, $file ) {
 if ( $file !== plugin_basename( GWFOOT_PLUGIN_FILE ) ) { return $links; }
 $de = str_starts_with( determine_locale(), 'de_' );
 $formal = 'de_DE_formal' === determine_locale();
 $links[] = '<a href="' . esc_url( plugins_url( $de ? ( $formal ? 'README-de-formal.txt' : 'README-de.txt' ) : 'README.txt', GWFOOT_PLUGIN_FILE ) ) . '">' . esc_html__( 'Documentation', 'genesis-widgetized-footer' ) . '</a>';
 /**
  * Filter this plugin's row metadata, including its local documentation link.
  * @since 1.0.0
  * @param array $links Metadata links for Genesis Widgetized Footer.
  */
 return apply_filters( 'gwfoot_filter_plugin_links', $links );
}
/**
 * Keep contextual help available on WordPress's widget administration screen.
 * @since 1.1.0
 * @return void
 */
function ddw_gwfoot_widgets_help_tab() {
 $screen = get_current_screen();
 if ( ! $screen || ! current_user_can( 'edit_theme_options' ) || ! ddw_gwfoot_has_genesis() ) { return; }
 /**
  * Filter the callable used to render widget administration help.
  * @since 1.1.0
  * @param callable $callback Callback that prints the help panel.
  */
 $callback = apply_filters( 'gwfoot_filter_help_tab_content', 'ddw_gwfoot_widgets_help_content' );
 if ( is_callable( $callback ) ) { $screen->add_help_tab( array( 'id' => 'gwfoot-widgets-help', 'title' => 'Genesis Widgetized Footer', 'callback' => $callback ) ); }
}
/**
 * Print the concise widget help and link to the full settings/documentation screen.
 * @since 1.0.0
 * @return void
 */
function ddw_gwfoot_widgets_help_content() {
 echo '<p>' . esc_html__( 'These areas replace the copyright and credits footer, independently of the theme’s regular footer widget columns.', 'genesis-widgetized-footer' ) . '</p><p><a href="' . esc_url( ddw_gwfoot_settings_url() ) . '">' . esc_html__( 'Settings', 'genesis-widgetized-footer' ) . '</a></p>';
}
/**
 * Return a safe local help sidebar for existing customization callbacks.
 * @since 1.4.0
 * @return string Linked settings label.
 */
function ddw_gwfoot_help_sidebar_content() {
 return '<p><a href="' . esc_url( ddw_gwfoot_settings_url() ) . '">' . esc_html__( 'Settings', 'genesis-widgetized-footer' ) . '</a></p>';
}
