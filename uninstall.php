<?php
/** Preserve website settings and widget content; clean only owned shared temporary data. GPL-2.0-or-later. */
defined( 'WP_UNINSTALL_PLUGIN' ) || exit;
require_once __DIR__ . '/includes/deckerweb-plugin-library/lifecycle.php';
deckerweb_library_uninstall_v3( __DIR__ . '/genesis-widgetized-footer.php' );
