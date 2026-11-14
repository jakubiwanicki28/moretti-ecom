#!/usr/bin/env python3
"""
Strony prawne (Regulamin, Dostawa i płatności, Zwroty, Reklamacje, Polityka prywatności) — treść trzymana w repo,
w content/legal-pages/<slug>.html, wgrywana przez WordPress REST API. Ten sam skrypt dla lokalnej kopii i produkcji.

  scripts/legal-pages.py local                 # pokaż różnice (nic nie zmienia)
  scripts/legal-pages.py local --apply         # zastosuj, kopia stanu „przed” w scripts/legal-pages-backups/
  scripts/legal-pages.py prod  --apply         # produkcja — tylko po akceptacji
  scripts/legal-pages.py prod  --rollback scripts/legal-pages-backups/<plik>.json

Źródłem prawdy jest repo. Punktem wyjścia (2026-11-14) była treść z produkcji, a nie stary setup-footer-pages.php,
który miał nieaktualne dane firmy (LIDA Dariusz Cała zamiast LIDA DUO Lilija Cała) i nadpisałby poprawki z panelu.
Zmiany w treści robimy w plikach, nie w panelu WordPressa — inaczej następne --apply je cofnie (skrypt to pokaże).

Dostęp: local — hasło aplikacji z scripts/local-env/.app-password; prod — hasło aplikacji WP z pęku kluczy macOS
(usługa „moretti-wp-app-password”).
"""
import argparse
import base64
import difflib
import html
import json
import os
import re
import subprocess
import sys
import urllib.parse
import urllib.request
from datetime import datetime

ROOT = os.path.dirname(os.path.dirname(os.path.abspath(__file__)))
CONTENT = os.path.join(ROOT, "content", "legal-pages")
BACKUPS = os.path.join(ROOT, "scripts", "legal-pages-backups")

# slug strony → (plik w content/legal-pages, tytuł przy tworzeniu nowej strony)
PAGES = {
    "regulamin-sklepu": ("regulamin-sklepu.html", "Regulamin sklepu"),
    "regulamin-sklepu-do-30-11-2026": ("regulamin-sklepu-2026-04-01.html", "Regulamin sklepu (wersja do 30.11.2026)"),
    "dostawa-i-platnosci": ("dostawa-i-platnosci.html", "Dostawa i płatności"),
    "zwroty": ("zwroty.html", "Zwroty"),
    "reklamacje": ("reklamacje.html", "Reklamacje"),
    "polityka-prywatnosci": ("polityka-prywatnosci.html", "Polityka prywatności"),
}

BASES = {"local": "http://localhost:8080", "prod": "https://www.morettifashion.com"}


def auth_header(target):
    if target == "local":
        password = open(os.path.join(ROOT, "scripts", "local-env", ".app-password")).read().strip()
        return "Basic " + base64.b64encode(f"admin:{password}".encode()).decode()

    def keychain(account=False):
        out = subprocess.run(
            ["security", "find-generic-password", "-s", "moretti-wp-app-password"] + ([] if account else ["-w"]),
            capture_output=True, text=True, check=True,
        ).stdout
        return out.split('"acct"<blob>="')[1].split('"')[0] if account else out.strip()

    return "Basic " + base64.b64encode(f"{keychain(True)}:{keychain()}".encode()).decode()


def api(target, method, path, body=None):
    req = urllib.request.Request(
        BASES[target] + "/wp-json/wp/v2/" + path,
        method=method,
        data=json.dumps(body).encode() if body is not None else None,
        headers={"Authorization": auth_header(target), "Content-Type": "application/json", "User-Agent": "moretti-legal-pages"},
    )
    with urllib.request.urlopen(req, timeout=60) as resp:
        return json.loads(resp.read())


def find_page(target, slug):
    query = urllib.parse.urlencode({"slug": slug, "context": "edit", "status": "publish,draft,private"})
    found = api(target, "GET", "pages?" + query)
    return found[0] if found else None


def as_text(markup):
    markup = re.sub(r"<!--.*?-->", "", markup, flags=re.S)
    markup = re.sub(r"</(p|li|h\d|tr|div)>|<br\s*/?>", "\n", markup)
    text = html.unescape(re.sub(r"<[^>]+>", " ", markup))
    return [re.sub(r"\s+", " ", line).strip() for line in text.splitlines() if line.strip()]


def main():
    parser = argparse.ArgumentParser(description=__doc__, formatter_class=argparse.RawDescriptionHelpFormatter)
    parser.add_argument("target", choices=BASES)
    parser.add_argument("--apply", action="store_true")
    parser.add_argument("--rollback", metavar="BACKUP_JSON")
    args = parser.parse_args()

    if args.rollback:
        backup = json.load(open(args.rollback))
        for slug, page in backup["pages"].items():
            if page is None:
                current = find_page(args.target, slug)
                if current:
                    api(args.target, "POST", f"pages/{current['id']}", {"status": "draft"})
                    print(f"{slug}: nie istniała przed zmianą — przeniesiona do szkiców")
                continue
            api(args.target, "POST", f"pages/{page['id']}", {"content": page["content"], "status": page["status"], "title": page["title"]})
            print(f"{slug}: przywrócona")
        return

    changes, before = [], {}
    for slug, (filename, title) in PAGES.items():
        wanted = open(os.path.join(CONTENT, filename)).read()
        current = find_page(args.target, slug)
        before[slug] = None if current is None else {
            "id": current["id"], "status": current["status"],
            "title": current["title"]["raw"], "content": current["content"]["raw"],
        }
        if current is None:
            print(f"\n=== {slug}: BRAK — zostanie utworzona")
            changes.append((slug, None, title, wanted))
            continue
        if current["content"]["raw"].strip() == wanted.strip() and current["status"] == "publish":
            print(f"=== {slug}: bez zmian")
            continue
        print(f"\n=== {slug}: różnice (tekst strony)")
        for line in difflib.unified_diff(as_text(current["content"]["raw"]), as_text(wanted), lineterm="", n=0):
            if line[:1] in "+-" and line[:3] not in ("+++", "---"):
                print("  " + line[:220])
        changes.append((slug, current["id"], title, wanted))

    if not args.apply or not changes:
        if changes and not args.apply:
            print("\nNic nie zmieniono. Dodaj --apply, żeby zastosować.")
        return

    os.makedirs(BACKUPS, exist_ok=True)
    backup_path = os.path.join(BACKUPS, f"{args.target}-{datetime.now():%Y%m%d-%H%M%S}.json")
    json.dump({"target": args.target, "pages": before}, open(backup_path, "w"), ensure_ascii=False, indent=1)
    print(f"\nKopia stanu przed zmianą: {backup_path}")

    for slug, page_id, title, wanted in changes:
        if page_id is None:
            created = api(args.target, "POST", "pages", {"slug": slug, "title": title, "content": wanted, "status": "publish"})
            print(f"{slug}: utworzona (id {created['id']})")
        else:
            api(args.target, "POST", f"pages/{page_id}", {"content": wanted, "status": "publish"})
            print(f"{slug}: zaktualizowana")


if __name__ == "__main__":
    sys.exit(main())
