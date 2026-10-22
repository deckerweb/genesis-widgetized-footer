# Building the plugin

[Deutsch](BUILD-de.md)

Use Python 3 from the repository root:

```sh
python3 tools/build-docs.py
python3 tools/build-zip.py
```

Documentation comes from `docs/content.json`. The second command creates and verifies `dist/genesis-widgetized-footer-2.0.2.zip` and its SHA-256 file. It checks the Library manifest and pinned updater, archive identity and internal-file exclusions. It does not upload files, create tags or publish releases. Install the plugin ZIP attached to a GitHub release; the automatically generated source archive also contains artwork sources and build tools.

Runtime requirements: WordPress 6.4+, PHP 8.1+ and Genesis Framework for footer output. Settings remain accessible when Genesis is unavailable.
