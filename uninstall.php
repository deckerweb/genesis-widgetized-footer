<?php
/** Preserve website settings and widget content; clean only owned shared temporary data. GPL-2.0-or-later. */
defined( 'WP_UNINSTALL_PLUGIN' ) || exit;
require_once __DIR__ . '/includes/deckerweb-plugin-library/lifecycle.php';
deckerweb_library_uninstall_v3( __DIR__ . '/genesis-widgetized-footer.php' );

// Remove only this repository's current and legacy updater metadata caches.
$gwfoot_updater_class = require __DIR__ . '/includes/deckerweb-github-release-updater-v2.php';
if ( is_string( $gwfoot_updater_class ) && method_exists( $gwfoot_updater_class, 'clear_repository_cache' ) ) {
 $gwfoot_updater_class::clear_repository_cache( 'https://github.com/deckerweb/genesis-widgetized-footer' );
}
