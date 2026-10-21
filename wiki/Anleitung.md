# Anleitung

[English](User-guide)

## Wie beginne ich?

Füge unter Design > Widgets Inhalte zu Footer-Bereich #1, Footer-Bereich #2 oder Footer-Disclaimer hinzu. Unter Genesis > Widgetized Footer findest du Status und Anzeigeoptionen.

## Was passiert bei leeren Bereichen?

Leere Bereiche bleiben verborgen. Sind beide Hauptbereiche leer, bleibt der Genesis-Standardfooter erhalten. Ein einzelner belegter Hauptbereich nutzt die volle Breite. Widgets mit eigenen Anzeigeregeln können trotzdem eine leere Ausgabe erzeugen.

## Wie funktionieren Landingpages?

Erkannt werden die fünf bisherigen Templates: page_landing.php, page-landing.php, landing.php, template-landing.php und template_page-landing.php. Die Hauptbereiche sind dort standardmäßig verborgen, der Disclaimer bleibt aktiviert. Beides ist einstellbar. Theme-Templates können Footer-Hooks unabhängig davon entfernen.

## Welche Shortcodes gibt es?

Verwende [gwfoot-widget-area area="footer-one"], area="footer-two" oder area="footer-disclaimer". [gwfoot-lastupdated] unterstützt date_format, wrapper, class, text_before und text_after. Erlaubte Wrapper: p, div, span, small und time. Das letzte Datum berücksichtigt veröffentlichte, nicht passwortgeschützte Beiträge, Seiten, Produkte und Downloads.

## Was passiert mit vorhandenen Code-Konstanten?

Explizite GWFOOT_REMOVE_LANDING_FOOTER, GWFOOT_NO_DISCLAIMER_WIDGET_AREA und GWFOOT_LANDING_DISCLAIMER behalten Vorrang. Bisher deaktiviert GWFOOT_LANDING_DISCLAIMER=false den Disclaimer überall; dieses Verhalten bleibt erhalten. GWFOOT_NO_WIDGETS_SHORTCODE und GWFOOT_SHORTCODE_FEATURES bleiben unterstützt. Die Einstellungsseite nennt erkannte Konstanten.
