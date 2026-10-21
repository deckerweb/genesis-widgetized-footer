# Plugin bauen

[English](BUILD.md)

Mit Python 3 im Hauptordner des Repositories:

```sh
python3 tools/build-docs.py
python3 tools/build-zip.py
```

Die Dokumentation stammt aus `docs/content.json`. Der zweite Aufruf erstellt und prüft `dist/genesis-widgetized-footer-2.0.1.zip` samt SHA-256-Datei. Geprüft werden Library-Manifest, gepinnter Updater, Archividentität und Ausschlüsse interner Dateien. Es werden keine Dateien hochgeladen, Tags erstellt oder Releases veröffentlicht. Installiere das Plugin-ZIP am GitHub-Release; das automatisch erzeugte Quellarchiv enthält zusätzlich Grafikquellen und Werkzeuge.

Voraussetzungen: WordPress ab 6.4, PHP ab 8.0 und für die Footer-Ausgabe das Genesis Framework. Ohne Genesis bleiben die Einstellungen erreichbar.
