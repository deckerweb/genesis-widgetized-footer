# User guide

[Deutsch](Anleitung)

## How do I get started?

Add widgets to Footer Area #1, Footer Area #2 or Footer Disclaimer under Appearance > Widgets. Open Genesis > Widgetized Footer to review setup and display settings.

## What happens when an area is empty?

Empty areas stay hidden. When both main areas are empty, the standard Genesis footer remains. One populated main area uses the full width. Widgets with their own visibility rules can still produce an empty result.

## How do landing pages work?

The five historical landing templates are recognized: page_landing.php, page-landing.php, landing.php, template-landing.php and template_page-landing.php. Main areas are hidden there by default; the disclaimer remains enabled. Both are configurable. Theme templates may independently remove footer hooks.

## Which shortcodes are available?

Use [gwfoot-widget-area area="footer-one"], area="footer-two" or area="footer-disclaimer". [gwfoot-lastupdated] accepts date_format, wrapper, class, text_before and text_after. Allowed wrappers: p, div, span, small and time. The latest date uses published, non-password-protected posts, pages, products and downloads.

## What happens to existing code constants?

Explicit GWFOOT_REMOVE_LANDING_FOOTER, GWFOOT_NO_DISCLAIMER_WIDGET_AREA and GWFOOT_LANDING_DISCLAIMER retain precedence. Historically GWFOOT_LANDING_DISCLAIMER=false disables the disclaimer everywhere; this behavior is preserved. GWFOOT_NO_WIDGETS_SHORTCODE and GWFOOT_SHORTCODE_FEATURES remain supported. The settings screen lists detected constants.
