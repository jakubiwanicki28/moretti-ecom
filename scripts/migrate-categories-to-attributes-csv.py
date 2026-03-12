#!/usr/bin/env python3
"""
Migracja kategorii → atrybuty (Kolekcja, Materiał).
Czyta eksport WooCommerce, zwraca minimalny CSV do importu: Identyfikator + Kategorie + Kolekcja + Materiał.
Użycie: python3 scripts/migrate-categories-to-attributes-csv.py <export.csv> [output.csv]
"""
import csv
import sys
import os

# Mapowanie: kategoria w CSV → term atrybutu (nazwy jak w WP)
KOLEKCJA_MAP = {
    "CROCO": "Croco",
    "PIÓRA": "Pióra",
    "Wzory Zwierzęce": "Animals",
}
MATERIAL_MAP = {
    "Skóra Lakierowana": "Skóra lakierowana",
    "Skóra Matowa": "Skóra matowa",
    "Skóra Naturalna": "Skóra naturalna",
}
# Kategorie do usunięcia z produktu (idą do atrybutów lub na śmietnik)
REMOVE_CATS = {
    "CROCO", "PIÓRA", "Wzory Zwierzęce",
    "Skóra Lakierowana", "Skóra Matowa", "Skóra Naturalna",
    "Wizytowniki",
}
# Zamiana starych nazw na docelowe (w CSV mogą być jeszcze Dział*)
REPLACE_CATS = {
    "Dział Damski": "Portfele damskie",
    "Dział Męski": "Portfele męskie",
}


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
        headers = next(r)
        # Kolumny
        id_idx = 0
        cat_idx = next((i for i, h in enumerate(headers) if h.strip() == "Kategorie"), None)
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
            cats = [c.strip() for c in cats_raw.split(",") if c.strip()]

            # Nowe kategorie: zamiana Dział* → Portfele*, usunięcie tych co idą do atrybutów
            new_cats = []
            for c in cats:
                c = REPLACE_CATS.get(c, c)
                if c in REMOVE_CATS:
                    continue
                if c not in new_cats:
                    new_cats.append(c)
            # Upewnij się, że jest Portfele i jedna z płci
            if new_cats and "Portfele" not in new_cats:
                new_cats.insert(0, "Portfele")
            if new_cats and not any(x in new_cats for x in ("Portfele damskie", "Portfele męskie")):
                # Zachowaj istniejącą strukturę – nie zgaduj płci
                pass

            # Kolekcja (pierwsze dopasowanie)
            kolekcja = ""
            for c in cats:
                if c in KOLEKCJA_MAP:
                    kolekcja = KOLEKCJA_MAP[c]
                    break
            # Materiał (pierwsze dopasowanie)
            material = ""
            for c in cats:
                if c in MATERIAL_MAP:
                    material = MATERIAL_MAP[c]
                    break

            rows_out.append({
                "Identyfikator": pid,
                "Kategorie": ", ".join(new_cats),
                "Nazwa atrybutu 4": "Kolekcja" if kolekcja else "",
                "Wartości atrybutu 4": kolekcja,
                "Nazwa atrybutu 5": "Materiał" if material else "",
                "Wartości atrybutu 5": material,
            })

    # Minimalny zestaw kolumn do aktualizacji: ID (match) + kategorie + atrybuty 4 i 5
    out_headers = [
        "Identyfikator",
        "Kategorie",
        "Nazwa atrybutu 4",
        "Wartości atrybutu 4",
        "Nazwa atrybutu 5",
        "Wartości atrybutu 5",
    ]
    with open(output_path, "w", encoding="utf-8", newline="") as f:
        w = csv.DictWriter(f, fieldnames=out_headers, extrasaction="ignore")
        w.writeheader()
        w.writerows(rows_out)

    print(f"Zapisano {len(rows_out)} wierszy do {output_path}")


if __name__ == "__main__":
    main()
