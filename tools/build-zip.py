"""Build and verify the reproducible installable WordPress plugin ZIP (no upload)."""
from pathlib import Path
import zipfile,hashlib,json,subprocess,shutil
root=Path(__file__).resolve().parents[1]
out=root/'dist/genesis-widgetized-footer-2.0.2.zip'
out.parent.mkdir(exist_ok=True)
files=sorted(p for p in root.rglob('*') if p.is_file() and not p.name.startswith('github-banner-') and p.suffix != '.scss' and p.name not in ('.gitignore','_config.yml','index.md') and not any(part in ('sources','marketing','branding','wiki','tools','docs','.github','.git','dist','__pycache__') for part in p.relative_to(root).parts))
with zipfile.ZipFile(out,'w',zipfile.ZIP_DEFLATED,compresslevel=9) as z:
 for p in files:
  entry=zipfile.ZipInfo(str(Path(root.name)/p.relative_to(root)),(2026,10,23,0,0,0))
  entry.compress_type=zipfile.ZIP_DEFLATED;entry.external_attr=0o100644 << 16
  z.writestr(entry,p.read_bytes(),compress_type=zipfile.ZIP_DEFLATED,compresslevel=9)
with zipfile.ZipFile(out) as z:
 assert z.testzip() is None
 names=z.namelist();assert len(set(names))==len(names)
 assert all(n.startswith('genesis-widgetized-footer/') and '..' not in Path(n).parts for n in names)
 assert not any(n.endswith(('.py','.cjs','.log','.sqlite','.phar')) or '/.git/' in n for n in names)
 assert not any(any(t in Path(n).name.lower() for t in ['roadmap','spezifikation','sub-spec','audit','pending-plugins','validation','candidate']) for n in names)
 for needed in ['genesis-widgetized-footer.php','uninstall.php','includes/gwfoot-admin.php','languages/genesis-widgetized-footer-de_DE.mo','languages/genesis-widgetized-footer-de_DE_formal.mo','includes/deckerweb-plugin-library/compatibility.json']:
  assert 'genesis-widgetized-footer/'+needed in names
 main=z.read('genesis-widgetized-footer/genesis-widgetized-footer.php').decode();assert 'Version: 2.0.2' in main
 manifest=json.loads(z.read('genesis-widgetized-footer/includes/deckerweb-plugin-library/compatibility.json'))
 for file,digest in manifest['hashes'].items():assert hashlib.sha256(z.read('genesis-widgetized-footer/includes/deckerweb-plugin-library/'+file)).hexdigest()==digest
 assert hashlib.sha256(z.read('genesis-widgetized-footer/includes/deckerweb-github-release-updater-v2.php')).hexdigest()=='0504cedd092804f289fbb0c985475fea69a9dabc0ccde31be5abb5d1ab886645'
 for n in names:
  if n.endswith(('.php','.txt','.md','.js','.css','.json')):
   s=z.read(n).decode('utf8');assert not any(x in s for x in ['ghp_','github_pat_','/Users/','sk-proj-'])
 digest=hashlib.sha256(out.read_bytes()).hexdigest();Path(str(out)+'.sha256').write_text(digest+'  '+out.name+'\n')

 print(len(names),'files; CRC, paths, identity, translations, manifests, updater provenance, internal exclusions and secret scan PASS')
 print(digest)
