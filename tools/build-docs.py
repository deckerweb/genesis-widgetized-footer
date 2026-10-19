"""Generate synchronized English and German documentation from docs/content.json."""
from pathlib import Path
import json
r=Path(__file__).resolve().parents[1]
content=json.loads((r/'docs/content.json').read_text())
for lang,c in content.items():
 de=lang=='de';suffix='-de' if de else ''
 history='== Changelog ==\n\n'+'\n\n'.join('= '+v+' =\n'+'\n'.join('* '+i for i in items) for v,items in c['changes'])+'\n'
 (r/f'CHANGELOG{suffix}.txt').write_text(history)
 faq='\n\n'.join('= '+q+' =\n'+a for q,a in c['faq'][:7])
 full=''
 for heading,indexes in [(('Einstieg und Anzeige' if de else 'Setup and display'),[0,1,2,3]),(('Daten und Netzwerk' if de else 'Data and network'),[4,5,6,12]),(('Shortcodes und Code-Konfiguration' if de else 'Shortcodes and code configuration'),[7,8,10]),(('Kompatibilität und Pflege' if de else 'Compatibility and maintenance'),[9,11])]:
  full+='== '+heading+' ==\n\n'+'\n\n'.join('= '+c['faq'][i][0]+' =\n'+c['faq'][i][1] for i in indexes)+'\n\n'
 (r/f'FAQ{suffix}.txt').write_text(('== Fragen nach Themen ==' if de else '== FAQ by topic ==')+'\n\n'+full+'\n')
 text='=== Genesis Widgetized Footer ===\n\n'+c['slogan']+'\n\n'+c['about']+'\n\nVersion: 2.0.0 | WordPress >= 6.4 | PHP >= 8.0 | Genesis Framework\n\n'+('== Auf einen Blick ==' if de else '== At a glance ==')+'\n'+'\n'.join('* '+f for f in c['features'])+'\n\n== Installation ==\n'+c['install']+'\n\n== FAQ ==\n'+faq+'\n\n'+('Vollständige Fragen nach Themen: FAQ-de.txt' if de else 'Complete FAQ by topic: FAQ.txt')+'\n\n'+history+'\n== David Decker – DECKERWEB ==\n'+('Seit 2012 ein schlanker Helfer für Genesis-Websites.' if de else 'A small helper for Genesis websites since 2012.')+'\n\n== Support ==\n'+c['support']+'\n\n== Library / Updater ==\n'+c['faq'][11][1]+'\n\n== Copyright / GPL ==\n'+c['license']+'\n'
 (r/f'README{suffix}.txt').write_text(text)
 md='# Genesis Widgetized Footer\n\n![Genesis Widgetized Footer](assets/github-banner-'+lang+'.png)\n\n'+c['slogan']+'\n\n'+('[English](README.md)' if de else '[Deutsch](README-de.md)')+'\n\n'+c['about']+'\n\n**2.0.0 · WordPress ≥6.4 · PHP ≥8.0 · Genesis Framework**\n\n'+('[FAQ](FAQ-de.txt) · [Änderungsverlauf](CHANGELOG-de.txt)' if de else '[FAQ](FAQ.txt) · [Changelog](CHANGELOG.txt)')+'\n\n'+text[text.index('== Auf' if de else '== At'):]
 import re
 md=re.sub(r'^== (.+) ==$',r'## \1',md,flags=re.M);md=re.sub(r'^= (.+) =$',r'### \1',md,flags=re.M)
 toc=('## Inhalt\n\n- [Auf einen Blick](#auf-einen-blick)\n- [Installation](#installation)\n- [FAQ](#faq)\n- [Änderungsverlauf](#changelog)\n\n' if de else '## Contents\n\n- [At a glance](#at-a-glance)\n- [Installation](#installation)\n- [FAQ](#faq)\n- [Changelog](#changelog)\n\n')
 md=md.replace('## Auf einen Blick' if de else '## At a glance',toc+('## Auf einen Blick' if de else '## At a glance'),1)
 md+=('\nUnterstützung: [Ko-fi](https://ko-fi.com/deckerweb) · [Buy Me a Coffee](https://buymeacoffee.com/daveshine) · [PayPal](https://paypal.me/deckerweb)\n' if de else '\nSupport: [Ko-fi](https://ko-fi.com/deckerweb) · [Buy Me a Coffee](https://buymeacoffee.com/daveshine) · [PayPal](https://paypal.me/deckerweb)\n')
 (r/f'README{suffix}.md').write_text(md)
 (r/f'SECURITY{suffix}.txt').write_text(c['support']+'\n')
 (r/f'SECURITY{suffix}.md').write_text(('# Sicherheit\n\n[English](SECURITY.md)\n\n' if de else '# Security\n\n[Deutsch](SECURITY-de.md)\n\n')+c['support']+'\n')
# A platform-shaped metadata source, not a WordPress.org distribution.
metadata='=== Genesis Widgetized Footer ===\nContributors: daveshine\nTags: genesis, footer, widgets, shortcodes\nRequires at least: 6.4\nTested up to: 7.1.3\nRequires PHP: 8.0\nStable tag: 2.0.0\nLicense: GPLv2 or later\nLicense URI: https://www.gnu.org/licenses/gpl-2.0.html\n\nCustomize the Genesis footer and optional disclaimer with widgets.\n\n'
(r/'readme.txt').write_text(metadata+(r/'README.txt').read_text().split('\n\n',1)[1])

# Keep Markdown lists separated from headings and add native GitHub destinations.
for lang in content:
 de=lang=='de';suffix='-de' if de else ''
 p=r/f'README{suffix}.md';md=p.read_text()
 md=re.sub(r'(^#{1,6} .+)$\n(?!\n)',r'\1\n\n',md,flags=re.M)
 links=('[Wiki](https://github.com/deckerweb/genesis-widgetized-footer/wiki/Start) · [Sicherheit](SECURITY-de.md) · [GitHub-Releases](https://github.com/deckerweb/genesis-widgetized-footer/releases)' if de else '[Wiki](https://github.com/deckerweb/genesis-widgetized-footer/wiki) · [Security](SECURITY.md) · [GitHub releases](https://github.com/deckerweb/genesis-widgetized-footer/releases)')
 md=md.replace('## Inhalt' if de else '## Contents',links+'\n\n'+('## Inhalt' if de else '## Contents'),1)
 md=md.replace('## FAQ',('## Hauptfunktionen\n\nDie beiden Hauptbereiche ersetzen den Copyright- und Credits-Bereich. Der optionale Disclaimer ergänzt einen Hinweistext. Die Anzeigeoptionen gelten je Website; Shortcodes geben zugewiesene Widgets an ihrer Einfügeposition aus.\n\n' if de else '## Main features\n\nThe two main areas replace the copyright and credits footer. The optional disclaimer adds a separate notice. Display settings apply per website; shortcodes render assigned widgets at their insertion point.\n\n')+'## FAQ',1)
 md=md.replace('## Library / Updater', '## Library / Updater')
 p.write_text(md)

# Public Wiki sources and Pages are derived from the same bilingual content.
w=r/'wiki';w.mkdir(exist_ok=True)
for lang,c in content.items():
 de=lang=='de'; suffix='-de' if de else ''
 faq_title='Fragen-nach-Themen' if de else 'FAQ-by-topic'
 (w/(faq_title+'.md')).write_text(('# Fragen nach Themen\n\n[English](FAQ-by-topic)\n\n' if de else '# FAQ by topic\n\n[Deutsch](Fragen-nach-Themen)\n\n')+re.sub(r'^=+ (.+?) =+$',r'## \1',(r/f'FAQ{suffix}.txt').read_text(),flags=re.M))
 home='Start' if de else 'Home'
 (w/(home+'.md')).write_text('# Genesis Widgetized Footer\n\n'+c['slogan']+'\n\n'+('[English](Home)' if de else '[Deutsch](Start)')+'\n\n'+c['about']+'\n\n'+c['install']+'\n\n'+('[Fragen nach Themen](Fragen-nach-Themen)' if de else '[FAQ by topic](FAQ-by-topic)')+'\n\n'+('[Vollständige Dokumentation](https://github.com/deckerweb/genesis-widgetized-footer/blob/main/README-de.md)' if de else '[Full documentation](https://github.com/deckerweb/genesis-widgetized-footer/blob/main/README.md)')+'\n')
(w/'_Sidebar.md').write_text('**Genesis Widgetized Footer**\n\n- [English](Home)\n- [FAQ by topic](FAQ-by-topic)\n- [Deutsch](Start)\n- [Fragen nach Themen](Fragen-nach-Themen)\n- [GitHub](https://github.com/deckerweb/genesis-widgetized-footer)\n')
page=(r/'README.md').read_text()
page=page.replace('](README-de.md)','](https://github.com/deckerweb/genesis-widgetized-footer/blob/main/README-de.md)')
(r/'index.md').write_text('---\nlayout: default\n---\n\n'+page)
