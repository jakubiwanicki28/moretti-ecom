#!/usr/bin/env python3
"""
Konfiguracja kasy przez WooCommerce REST API: które metody dostawy są włączone i w jakiej kolejności,
nazwy metod płatności. Ten sam skrypt dla lokalnej kopii i produkcji.

  scripts/checkout-config.py local                 # pokaż różnice (nic nie zmienia)
  scripts/checkout-config.py local --apply         # zastosuj, kopia stanu „przed” w scripts/checkout-config-backups/
  scripts/checkout-config.py prod  --apply         # produkcja — tylko po akceptacji
  scripts/checkout-config.py prod  --rollback scripts/checkout-config-backups/<plik>.json

Dostęp: local — hasło aplikacji z scripts/local-env/.app-password (tworzy je setup.sh);
prod — klucz WooCommerce z pęku kluczy macOS (usługa „moretti-woo-api”).

Uzasadnienie (zamówienia 2026-04..10, 208 opłaconych): Paczkomat 82, punkt DPD 63, kurier InPost 39,
kurier DPD 11, Orlen 8, DHL 1, UPS 0. Metody płatności: kolejność ustala motyw (inc/checkout.php).
"""
import argparse
import base64
import json
import os
import subprocess
import sys
import urllib.request
from datetime import datetime

ROOT = os.path.dirname(os.path.dirname(os.path.abspath(__file__)))
BACKUPS = os.path.join(ROOT, "scripts", "checkout-config-backups")

# Metody dostawy w strefie „Polska”, w kolejności wyświetlania. Rozpoznawane po nazwie
# (instance_id różni się między lokalną kopią a produkcją). Niewymienione zostają wyłączone.
SHIPPING = [
    "Dostawa InPost do Paczkomatu",
    "Dostawa do punktu DPD",
    "Dostawa do punktu Orlen Paczka",
    "Kurier InPost na adres",
    "Kurier DPD na adres",
]

PAYMENT_TITLES = {
    "p24-online-payments": "Szybki przelew online",
}


def auth_header(target):
    if target == "local":
        password = open(os.path.join(ROOT, "scripts", "local-env", ".app-password")).read().strip()
        return "Basic " + base64.b64encode(f"admin:{password}".encode()).decode()

    def keychain(service, account=False):
        out = subprocess.run(
            ["security", "find-generic-password", "-s", service] + ([] if account else ["-w"]),
            capture_output=True, text=True, check=True,
        ).stdout
        return out.split('"acct"<blob>="')[1].split('"')[0] if account else out.strip()

    key = keychain("moretti-woo-api", account=True)
    secret = keychain("moretti-woo-api")
    return "Basic " + base64.b64encode(f"{key}:{secret}".encode()).decode()


class Api:
    def __init__(self, target):
        self.base = {"local": "http://localhost:8080", "prod": "https://www.morettifashion.com"}[target]
        self.auth = auth_header(target)

    def call(self, method, path, body=None):
        req = urllib.request.Request(
            f"{self.base}/wp-json/wc/v3{path}",
            data=json.dumps(body).encode() if body is not None else None,
            method=method,
            headers={"Authorization": self.auth, "Content-Type": "application/json", "User-Agent": "moretti-checkout-config"},
        )
        with urllib.request.urlopen(req, timeout=60) as resp:
            return json.load(resp)


def poland_zone(api):
    zone = next((z for z in api.call("GET", "/shipping/zones") if z["name"] == "Polska"), None)
    if not zone:
        sys.exit("Brak strefy wysyłki „Polska”.")
    return zone["id"]


def snapshot(api, zone_id):
    methods = api.call("GET", f"/shipping/zones/{zone_id}/methods")
    gateways = api.call("GET", "/payment_gateways")
    return {
        "shipping": [{"instance_id": m["instance_id"], "title": m["title"], "enabled": m["enabled"], "order": m["order"]} for m in methods],
        "payments": [{"id": g["id"], "title": g["title"]} for g in gateways if g["id"] in PAYMENT_TITLES],
    }


def plan(state):
    changes = []
    for m in state["shipping"]:
        want_enabled = m["title"] in SHIPPING
        want_order = SHIPPING.index(m["title"]) + 1 if want_enabled else m["order"]
        if m["enabled"] != want_enabled or m["order"] != want_order:
            changes.append(("shipping", m["instance_id"], {"enabled": want_enabled, "order": want_order},
                            f'{m["title"]}: {"wł." if m["enabled"] else "wył."}#{m["order"]} → {"wł." if want_enabled else "wył."}#{want_order}'))
    missing = set(SHIPPING) - {m["title"] for m in state["shipping"]}
    if missing:
        sys.exit(f"Brak metod dostawy o nazwach: {', '.join(sorted(missing))}")
    for g in state["payments"]:
        if g["title"] != PAYMENT_TITLES[g["id"]]:
            changes.append(("payment", g["id"], {"settings": {"title": PAYMENT_TITLES[g["id"]]}},
                            f'{g["id"]}: „{g["title"]}” → „{PAYMENT_TITLES[g["id"]]}”'))
    return changes


def apply(api, zone_id, changes):
    for kind, ident, body, _ in changes:
        if kind == "shipping":
            api.call("PUT", f"/shipping/zones/{zone_id}/methods/{ident}", body)
        else:
            api.call("PUT", f"/payment_gateways/{ident}", body)


def main():
    parser = argparse.ArgumentParser()
    parser.add_argument("target", choices=["local", "prod"])
    parser.add_argument("--apply", action="store_true")
    parser.add_argument("--rollback", metavar="BACKUP_JSON")
    args = parser.parse_args()

    api = Api(args.target)
    zone_id = poland_zone(api)

    if args.rollback:
        before = json.load(open(args.rollback))
        for m in before["shipping"]:
            api.call("PUT", f"/shipping/zones/{zone_id}/methods/{m['instance_id']}", {"enabled": m["enabled"], "order": m["order"]})
        for g in before["payments"]:
            api.call("PUT", f"/payment_gateways/{g['id']}", {"settings": {"title": g["title"]}})
        print(f"Przywrócono stan z {args.rollback}")
        return

    state = snapshot(api, zone_id)
    changes = plan(state)
    if not changes:
        print("Konfiguracja aktualna — brak zmian.")
        return
    for *_, label in changes:
        print("  " + label)
    if not args.apply:
        print("\n(podgląd — dodaj --apply, żeby zastosować)")
        return

    os.makedirs(BACKUPS, exist_ok=True)
    backup = os.path.join(BACKUPS, f"{args.target}-{datetime.now():%Y%m%d-%H%M%S}.json")
    json.dump(state, open(backup, "w"), ensure_ascii=False, indent=1)
    apply(api, zone_id, changes)
    print(f"\nZastosowano {len(changes)} zmian. Kopia stanu „przed”: {os.path.relpath(backup, ROOT)}")


if __name__ == "__main__":
    main()
