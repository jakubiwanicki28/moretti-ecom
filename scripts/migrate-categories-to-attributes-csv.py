#!/usr/bin/env python3
"""
Migracja kategorii / nazwy / SKU → atrybuty Kolekcja i Materiał.
Czyta eksport WooCommerce, zwraca minimalny CSV do importu: Identyfikator + Kategorie + Kolekcja (atrybut 1) + Materiał (atrybut 2).
Kolekcja i Materiał są wypisywane w kolumnach "Nazwa atrybutu 1" / "Wartości atrybutu 1" oraz "Nazwa atrybutu 2" / "Wartości atrybutu 2"
– tak jak w standardowym eksporcie WooCommerce (żeby importer przypisał je do taksonomii pa_kolekcja i pa_materiał).

Źródła Kolekcja (w kolejności): istniejąca wartość atrybutu 1 jeśli nazwa = Kolekcja → Kategorie (CROCO, PIÓRA, …) → prefiks SKU (CR_, PI_, …) → słowa w Nazwa.
Źródła Materiał: istniejąca wartość atrybutu 2 jeśli nazwa = Materiał → Kategorie → słowa w Nazwa.

Użycie: python3 scripts/migrate-categories-to-attributes-csv.py <export.csv> [output.csv]
"""
import csv
import re
import sys
import os

# Mapowanie: kategoria w CSV (lub fragment) → term Kolekcja (nazwa jak w WP, slug będzie np. croco, piora)
KOLEKCJA_MAP = {
    "CROCO": "Croco",
    "PIÓRA": "Pióra",
    "Pióra": "Pióra",
    "Wzory Zwierzęce": "Animals",
    "Animals": "Animals",
    "Snake": "Snake",
}
# Prefiks SKU → Kolekcja
SKU_KOLEKCJA = {
    "CR_": "Croco",
    "PI_": "Pióra",
    "AN_": "Animals",
    "SN_": "Snake",
}
# Słowa w nazwie produktu → Kolekcja (bez rozróżniania wielkości)
NAZWA_KOLEKCJA = [
    ("CROCO LUXE", "Croco"),
    ("CROCO", "Croco"),
    ("SOFT FEATHERS", "Pióra"),
    ("FEATHERS", "Pióra"),
    ("Pióra", "Pióra"),
    ("ANIMALS", "Animals"),
    ("SNAKE", "Snake"),
]
# Mapowanie: kategoria / nazwa → term Materiał (nazwy jak w WP)
MATERIAL_MAP = {
    "Skóra Lakierowana": "Skóra lakierowana",
    "Skóra lakierowana": "Skóra lakierowana",
    "Skóra Matowa": "Skóra matowa",
    "Skóra matowa": "Skóra matowa",
    "Skóra Naturalna": "Skóra naturalna",
    "Skóra naturalna": "Skóra naturalna",
}
NAZWA_MATERIAL = [
    ("lakierowana", "Skóra lakierowana"),
    ("lakierowan", "Skóra lakierowana"),
    ("matowa", "Skóra matowa"),
    ("matow", "Skóra matowa"),
    ("naturalna", "Skóra naturalna"),
    ("naturaln", "Skóra naturalna"),
]
# Kategorie do usunięcia z listy (idą do atrybutów)
REMOVE_CATS = {
    "CROCO", "PIÓRA", "Wzory Zwierzęce", "Snake",
    "Skóra Lakierowana", "Skóra Matowa", "Skóra Naturalna",
    "Wizytowniki",
}
REPLACE_CATS = {
    "Dział Damski": "Portfele damskie",
    "Dział Męski": "Portfele męskie",
}


def get_kolekcja(row, headers, id_idx, cat_idx, sku_idx, name_idx, attr1_name_idx, attr1_val_idx, attr2_name_idx, attr2_val_idx):
    # 1) Już ustawione w atrybucie 1 jako Kolekcja
    if attr1_name_idx is not None and attr1_val_idx is not None and len(row) > max(attr1_name_idx, attr1_val_idx):
        if row[attr1_name_idx].strip() == "Kolekcja" and row[attr1_val_idx].strip():
            return row[attr1_val_idx].strip()
    # 2) Z kategorii
    if cat_idx is not None and len(row) > cat_idx:
        cats_raw = row[cat_idx].strip()
        for part in re.split(r",|>", cats_raw):
            c = part.strip()
            if c in KOLEKCJA_MAP:
                return KOLEKCJA_MAP[c]
    # 3) Z SKU
    if sku_idx is not None and len(row) > sku_idx:
        sku = (row[sku_idx] or "").strip().upper()
        for prefix, val in SKU_KOLEKCJA.items():
            if sku.startswith(prefix):
                return val
    # 4) Z nazwy
    if name_idx is not None and len(row) > name_idx:
        nazwa = (row[name_idx] or "").upper()
        for phrase, val in NAZWA_KOLEKCJA:
            if phrase.upper() in nazwa:
                return val
    return ""


def get_material(row, headers, cat_idx, name_idx, attr2_name_idx, attr2_val_idx):
    # 1) Już ustawione w atrybucie 2 jako Materiał
    if attr2_name_idx is not None and attr2_val_idx is not None and len(row) > max(attr2_name_idx, attr2_val_idx):
        if row[attr2_name_idx].strip() == "Materiał" and row[attr2_val_idx].strip():
            return row[attr2_val_idx].strip()
    # 2) Z kategorii
    if cat_idx is not None and len(row) > cat_idx:
        cats_raw = row[cat_idx].strip()
        for part in re.split(r",|>", cats_raw):
            c = part.strip()
            if c in MATERIAL_MAP:
                return MATERIAL_MAP[c]
    # 3) Z nazwy
    if name_idx is not None and len(row) > name_idx:
        nazwa = (row[name_idx] or "").lower()
        for phrase, val in NAZWA_MATERIAL:
            if phrase in nazwa:
                return val
    return ""


def main():
    input_path = sys.argv[1] if len(sys.argv) > 1 else None
    output_path = sys.argv[2] if len(sys.argv) > 2 else None
    if not input_path or not os.path.isfile(input_path):
        print("Użycie: python3 migrate-categories-to-attributes-csv.py <export.csv> [output.csv]", file=sys.stderr)
        sys.exit(1)
    if not output_path:
        base, _ = os.path.splitext(input_path)
        output_path = base + "-migracja-kategorie-atrybuty.csv"

    with open(input_path, "r", encoding="utf-8-sig") as f:
        r = csv.reader(f)
        headers = [h.strip() for h in next(r)]
        id_idx = 0
        cat_idx = next((i for i, h in enumerate(headers) if h == "Kategorie"), None)
        sku_idx = next((i for i, h in enumerate(headers) if h == "SKU"), None)
        name_idx = next((i for i, h in enumerate(headers) if h == "Nazwa"), None)
        attr1_name_idx = next((i for i, h in enumerate(headers) if h == "Nazwa atrybutu 1"), None)
        attr1_val_idx = next((i for i, h in enumerate(headers) if h == "Wartości atrybutu 1"), None)
        attr2_name_idx = next((i for i, h in enumerate(headers) if h == "Nazwa atrybutu 2"), None)
        attr2_val_idx = next((i for i, h in enumerate(headers) if h == "Wartości atrybutu 2"), None)
        if cat_idx is None:
            print("Brak kolumny Kategorie.", file=sys.stderr)
            sys.exit(1)

        rows_out = []
        for row in r:
            if len(row) <= max(id_idx, cat_idx):
                continue
            pid = row[id_idx].strip()
            if not pid or not pid.isdigit():
                continue
            cats_raw = row[cat_idx].strip()
            cats = [c.strip() for c in re.split(r",|>", cats_raw) if c.strip()]
            new_cats = []
            for c in cats:
                c = REPLACE_CATS.get(c, c)
                if c in REMOVE_CATS:
                    continue
                if c not in new_cats:
                    new_cats.append(c)
            if new_cats and "Portfele" not in new_cats:
                new_cats.insert(0, "Portfele")
            kolekcja = get_kolekcja(row, headers, id_idx, cat_idx, sku_idx, name_idx,
                                    attr1_name_idx, attr1_val_idx, attr2_name_idx, attr2_val_idx)
            material = get_material(row, headers, cat_idx, name_idx, attr2_name_idx, attr2_val_idx)

            rows_out.append({
                "Identyfikator": pid,
                "Kategorie": ", ".join(new_cats),
                "Nazwa atrybutu 1": "Kolekcja",
                "Wartości atrybutu 1": kolekcja,
                "Nazwa atrybutu 2": "Materiał",
                "Wartości atrybutu 2": material,
            })

    out_headers = [
        "Identyfikator",
        "Kategorie",
        "Nazwa atrybutu 1",
        "Wartości atrybutu 1",
        "Nazwa atrybutu 2",
        "Wartości atrybutu 2",
    ]
    with open(output_path, "w", encoding="utf-8", newline="") as f:
        w = csv.DictWriter(f, fieldnames=out_headers, extrasaction="ignore")
        w.writeheader()
        w.writerows(rows_out)

    print(f"Zapisano {len(rows_out)} wierszy do {output_path}")
    with_k = sum(1 for r in rows_out if r.get("Wartości atrybutu 1"))
    with_m = sum(1 for r in rows_out if r.get("Wartości atrybutu 2"))
    print(f"  Kolekcja ustawiona: {with_k}, Materiał ustawiony: {with_m}")


if __name__ == "__main__":
    main()
