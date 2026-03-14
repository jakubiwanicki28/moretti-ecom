#!/usr/bin/env python3
"""
Z eksportu produktów (CSV) tworzy arkusz: ID posta + kolumna atrybutu Kolor
(z termem taksonomii już dopasowanym z SKU). Do importu przypisań koloru.

Użycie:
  python scripts/export-id-and-color-from-products.py <eksport_produktow.csv> <wyjscie_id_kolor.csv>

Wymagane kolumny w wejściu: Identyfikator (lub ID) oraz SKU.
Kolor jest wyciągany z SKU (część po ostatnim myślniku) i mapowany na slug
termu taksonomii pa_kolor (Zielony, Fioletowy, Ciemny Brąz, itd.).
"""

import csv
import os
import re
import sys
from typing import Dict, List, Optional, Tuple


# Mapowanie: znormalizowany fragment koloru z SKU -> slug termu w Woo (pa_kolor)
# Slug musi zgadzać się z terminami w WooCommerce → Produkty → Atrybuty → Kolor.
SKU_COLOR_TO_TERM_SLUG: Dict[str, str] = {
    "zielony": "zielony",
    "fioletowy": "fioletowy",
    "bordowy": "bordowy",
    "jasny-braz": "jasny-braz",
    "jasnybraz": "jasny-braz",
    "ciemny-braz": "ciemny-braz",
    "ciemnybraz": "ciemny-braz",
    "czerwony": "czerwony",
    "czarny": "czarny",
    "jasny-roz": "jasny-roz",
    "jasnyroz": "jasny-roz",
    "szary": "szary",
    "zloty": "zloty",
    "złoty": "zloty",
    "granatowy": "granatowy",
    "brazowy": "jasny-braz",  # domyślnie do jasny-braz
    "bezowy": "bezowy",
    "bialy": "bialy",
    "biały": "bialy",
    "niebieski": "niebieski",
    "kremowy": "kremowy",
    "taupe": "taupe",
    "black": "czarny",
    "brown": "jasny-braz",
    "beige": "bezowy",
    "gray": "szary",
    "grey": "szary",
    "white": "bialy",
    "red": "czerwony",
    "blue": "niebieski",
    "navy": "granatowy",
}


def normalize_color_key(raw: str) -> str:
    """Zbliżona logika do moretti_normalize_color_key (PHP): lowercase, myślniki, bez znaków diakrytycznych."""
    if not raw or not isinstance(raw, str):
        return ""
    # Usunięcie akcentów (uproszczone)
    replacements = {
        "ą": "a", "ć": "c", "ę": "e", "ł": "l", "ń": "n",
        "ó": "o", "ś": "s", "ź": "z", "ż": "z",
    }
    s = raw.strip().lower()
    for old, new in replacements.items():
        s = s.replace(old, new)
    s = s.replace("_", "-")
    s = re.sub(r"[^a-z0-9-]+", "", s)
    s = re.sub(r"-+", "-", s)
    return s.strip("-")


def sku_to_color_slug(sku: str) -> Optional[str]:
    """
    Z SKU wyciąga kolor (część po ostatnim myślniku) i zwraca slug termu pa_kolor.
    Model = wszystko przed ostatnim '-', kolor = wszystko po.
    """
    if not sku or not isinstance(sku, str):
        return None
    sku = sku.strip()
    last_dash = sku.rfind("-")
    if last_dash <= 0 or last_dash >= len(sku) - 1:
        return None
    color_raw = sku[last_dash + 1 :].strip()
    if not color_raw:
        return None
    key = normalize_color_key(color_raw)
    if not key:
        return None
    return SKU_COLOR_TO_TERM_SLUG.get(key)


def build_id_color_sheet(
    source_csv: str,
    output_csv: str,
    id_column_names: Tuple[str, ...] = ("Identyfikator", "ID", "id"),
    sku_column_names: Tuple[str, ...] = ("SKU", "sku"),
    color_column_name: str = "Kolor",
    woo_attribute_format: bool = True,
) -> None:
    """
    woo_attribute_format: jeśli True, zapisuje kolumny dla importu atrybutu WooCommerce:
      Identyfikator, Nazwa atrybutu 1, Wartości atrybutu 1 (wartość = slug koloru).
    """
    with open(source_csv, "r", encoding="utf-8-sig", newline="") as src_fh, open(
        output_csv, "w", encoding="utf-8-sig", newline=""
    ) as out_fh:
        reader = csv.reader(src_fh)
        writer = csv.writer(out_fh)

        try:
            header: List[str] = [h.strip() for h in next(reader)]
        except StopIteration:
            return

        header_lower = {h.lower(): i for i, h in enumerate(header)}
        idx_id: Optional[int] = None
        idx_sku: Optional[int] = None
        for name in id_column_names:
            idx_id = header_lower.get(name.lower())
            if idx_id is not None:
                break
        for name in sku_column_names:
            idx_sku = header_lower.get(name.lower())
            if idx_sku is not None:
                break

        if idx_id is None or idx_sku is None:
            raise SystemExit(
                "W wejściowym CSV brak kolumn Identyfikator/ID oraz SKU. "
                f"Kolumny: {header}"
            )

        if woo_attribute_format:
            writer.writerow(["Identyfikator", "Nazwa atrybutu 1", "Wartości atrybutu 1"])
        else:
            writer.writerow(["ID", color_column_name])

        for row in reader:
            if len(row) <= max(idx_id, idx_sku):
                continue
            product_id = (row[idx_id] or "").strip()
            sku = (row[idx_sku] or "").strip()
            if not product_id:
                continue
            term_slug = sku_to_color_slug(sku)
            if woo_attribute_format:
                writer.writerow([product_id, color_column_name, term_slug or ""])
            else:
                writer.writerow([product_id, term_slug or ""])


def main() -> None:
    if len(sys.argv) != 3:
        print(
            "Użycie: python scripts/export-id-and-color-from-products.py <eksport_produktow.csv> <wyjscie_id_kolor.csv>",
            file=sys.stderr,
        )
        raise SystemExit(1)

    source_csv = sys.argv[1]
    output_csv = sys.argv[2]

    if not os.path.isfile(source_csv):
        raise SystemExit(f"Plik nie znaleziony: {source_csv}")

    build_id_color_sheet(source_csv, output_csv)
    print(f"Zapisano arkusz ID + Kolor: {output_csv}")


if __name__ == "__main__":
    main()
