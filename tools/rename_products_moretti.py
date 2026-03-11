import csv
import os
import re
import sys
from typing import Dict, Any, List, Tuple, Optional


def parse_post_id_from_url(url: str) -> Optional[str]:
    """
    Extract post ID from wp-admin edit URL.
    Example: '...post.php?post=346&action=edit' -> '346'
    """
    if not url:
        return None
    match = re.search(r"[?&]post=(\d+)", url)
    if not match:
        return None
    return match.group(1)


def normalize_model_code(sku_base: str) -> str:
    """
    Strip techniczne prefiksy typu CR_/PI_/SL_/SM_ tak,
    żeby w nawiasie został kod zgodny z producentem,
    np. PI_AC44 -> AC44, CR_DE33-23-3 -> DE33-23-3, SM_3807-1806 -> 3807-1806.
    """
    code = (sku_base or "").strip()
    if not code:
        return ""
    m = re.match(r"^[A-Z]{1,4}[_-](.+)$", code)
    if m:
        return m.group(1)
    return code


def classify_gender_and_collection(raw_dzial: str) -> Tuple[str, str]:
    """
    From 'Dział' column determine gender label and collection label.
    Collection label is marketing-friendly (EN, CAPS).
    """
    text = (raw_dzial or "").lower()

    gender = ""
    if "damski" in text:
        gender = "damski"
    elif "męski" in text or "meski" in text:
        gender = "męski"

    collection = ""
    # More marketing / storytelling style collection names (all-caps).
    if "croco" in text or "croc" in text:
        # Lakierowane, struktura krokodyla – mocny, biżuteryjny efekt.
        collection = "CROCO LUXE"
    elif "pióra" in text or "piora" in text:
        # Lżejsze, smukłe portfele o delikatniejszym charakterze.
        collection = "SOFT FEATHERS"
    elif "skóra matowa" in text or "skora matowa" in text:
        # Matowa skóra – bardziej codzienna, miejska elegancja.
        collection = "URBAN MATTE"
    elif "skóra lakierowana" in text or "skora lakierowana" in text:
        # Lakier – mocny połysk, wieczorowy vibe.
        collection = "MIDNIGHT GLOSS"

    return gender, collection


def build_new_name(collection: str, color: str, sku_base: str) -> str:
    """
    Build final product name:
    'Moretti [COLLECTION] [Kolor] ([SKU_BASE])'
    """
    color = (color or "").strip()
    collection = (collection or "").strip()
    sku_base = normalize_model_code(sku_base)

    parts: List[str] = ["Moretti"]
    if collection:
        parts.append(collection)
    if color:
        parts.append(color)

    name_core = " ".join(parts)
    if sku_base:
        return f"{name_core} ({sku_base})"
    return name_core


def build_name_map_from_master(master_path: str) -> Tuple[Dict[str, str], Dict[str, Any]]:
    """
    Returns:
      - id_to_name: product_id -> new_name
      - diagnostics: stats + list of rows without id / color / dzial
    """
    id_to_name: Dict[str, str] = {}
    diag: Dict[str, Any] = {
        "rows_total": 0,
        "rows_with_id": 0,
        "rows_missing_id": [],
        "rows_missing_fields": [],
    }

    with open(master_path, "r", encoding="utf-8-sig", newline="") as fh:
        reader = csv.reader(fh)
        try:
            header = next(reader)
        except StopIteration:
            return id_to_name, diag

        header_index = {name.strip(): idx for idx, name in enumerate(header)}

        def col(name: str) -> int:
            return header_index.get(name, -1)

        idx_url = col("Link do edycji produktu w sklepie ")
        if idx_url < 0:
            idx_url = col("Link do edycji produktu w sklepie")

        idx_dzial = col("Dział")
        idx_color = col("Kolor")
        idx_sku_base = col("[SKU BASE]")

        for row in reader:
            diag["rows_total"] += 1
            if len(row) < len(header):
                row = row + [""] * (len(header) - len(row))

            url = row[idx_url] if idx_url >= 0 and idx_url < len(row) else ""
            product_id = parse_post_id_from_url(url)
            if not product_id:
                diag["rows_missing_id"].append(
                    {"row": diag["rows_total"], "url": url}
                )
                continue

            diag["rows_with_id"] += 1
            raw_dzial = (
                row[idx_dzial].strip()
                if idx_dzial >= 0 and idx_dzial < len(row)
                else ""
            )
            sku_base = (
                row[idx_sku_base].strip()
                if idx_sku_base >= 0 and idx_sku_base < len(row)
                else ""
            )
            color = (
                row[idx_color].strip()
                if idx_color >= 0 and idx_color < len(row)
                else ""
            )

            if not color and not raw_dzial:
                diag["rows_missing_fields"].append(
                    {
                        "product_id": product_id,
                        "row": diag["rows_total"],
                        "dzial": raw_dzial,
                        "color": color,
                    }
                )
                continue

            _gender, collection = classify_gender_and_collection(raw_dzial)
            new_name = build_new_name(collection, color, sku_base)
            id_to_name[product_id] = new_name

    return id_to_name, diag


def update_woo_export(
    woo_path: str, id_to_name: Dict[str, str], output_path: str
) -> Dict[str, Any]:
    """
    Update Nazwa and first <h3><b>...</b></h3> in Opis using id_to_name.
    """
    diag: Dict[str, Any] = {
        "rows_total": 0,
        "rows_with_id": 0,
        "rows_with_updated_name": 0,
        "rows_without_mapping": [],
    }

    with open(woo_path, "r", encoding="utf-8-sig", newline="") as in_fh, open(
        output_path, "w", encoding="utf-8-sig", newline=""
    ) as out_fh:
        reader = csv.reader(in_fh)
        writer = csv.writer(out_fh)

        try:
            header = next(reader)
        except StopIteration:
            return diag

        writer.writerow(header)
        header_index = {name.strip(): idx for idx, name in enumerate(header)}

        idx_id = header_index.get("Identyfikator", 0)
        idx_name = header_index.get("Nazwa", -1)
        idx_desc = header_index.get("Opis", -1)

        for row in reader:
            diag["rows_total"] += 1
            if len(row) < len(header):
                row = row + [""] * (len(header) - len(row))

            product_id = row[idx_id].strip()
            if product_id:
                diag["rows_with_id"] += 1

            new_name = id_to_name.get(product_id)
            if new_name and idx_name >= 0:
                row[idx_name] = new_name

                # Update first <h3><b>...</b></h3> in description, if present
                if idx_desc >= 0 and row[idx_desc]:
                    desc_html = row[idx_desc]

                    def repl(match: re.Match) -> str:
                        return f"<h3><b>{new_name}</b></h3>"

                    desc_updated, count = re.subn(
                        r"<h3><b>.*?</b></h3>", repl, desc_html, count=1, flags=re.IGNORECASE | re.DOTALL
                    )
                    if count > 0:
                        row[idx_desc] = desc_updated

                diag["rows_with_updated_name"] += 1
            elif product_id and product_id not in id_to_name:
                diag["rows_without_mapping"].append(
                    {
                        "product_id": product_id,
                        "existing_name": row[idx_name] if idx_name >= 0 else "",
                    }
                )

            writer.writerow(row)

    return diag


def export_minimal_name_csv(
    full_csv_path: str, output_csv_path: str
) -> None:
    """
    From updated Woo export produce CSV with only Identyfikator and Nazwa.
    """
    with open(full_csv_path, "r", encoding="utf-8-sig", newline="") as in_fh, open(
        output_csv_path, "w", encoding="utf-8-sig", newline=""
    ) as out_fh:
        reader = csv.reader(in_fh)
        writer = csv.writer(out_fh)

        try:
            header = next(reader)
        except StopIteration:
            return

        header_index = {name.strip(): idx for idx, name in enumerate(header)}
        idx_id = header_index.get("Identyfikator")
        idx_name = header_index.get("Nazwa")

        if idx_id is None or idx_name is None:
            raise SystemExit("CSV must contain 'Identyfikator' and 'Nazwa' columns.")

        writer.writerow(["Identyfikator", "Nazwa"])

        for row in reader:
            if len(row) <= max(idx_id, idx_name):
                continue
            product_id = (row[idx_id] or "").strip()
            name = (row[idx_name] or "").strip()
            if not product_id:
                continue
            writer.writerow([product_id, name])


def write_report(
    report_path: str,
    master_diag: Dict[str, Any],
    woo_diag: Dict[str, Any],
) -> None:
    with open(report_path, "w", encoding="utf-8") as rep:
        rep.write("MASTER SHEET DIAGNOSTICS\n")
        rep.write(f"Total rows: {master_diag['rows_total']}\n")
        rep.write(f"Rows with product ID: {master_diag['rows_with_id']}\n")
        rep.write(f"Rows missing ID: {len(master_diag['rows_missing_id'])}\n")
        rep.write(
            f"Rows missing Dział/Color: {len(master_diag['rows_missing_fields'])}\n\n"
        )

        if master_diag["rows_missing_id"]:
            rep.write("Rows in master sheet without detectable product ID (post=) (first 50):\n")
            for item in master_diag["rows_missing_id"][:50]:
                rep.write(f"  row {item['row']}: url={item['url']}\n")
            rep.write("\n")

        if master_diag["rows_missing_fields"]:
            rep.write("Rows in master sheet missing Dział or Kolor (first 50):\n")
            for item in master_diag["rows_missing_fields"][:50]:
                rep.write(
                    f"  row {item['row']}: id={item['product_id']}, dzial='{item['dzial']}', color='{item['color']}'\n"
                )
            rep.write("\n")

        rep.write("WOO EXPORT DIAGNOSTICS\n")
        rep.write(f"Total rows: {woo_diag['rows_total']}\n")
        rep.write(f"Rows with product ID: {woo_diag['rows_with_id']}\n")
        rep.write(f"Rows with updated name: {woo_diag['rows_with_updated_name']}\n")
        rep.write(
            f"Rows with product ID but no mapping: {len(woo_diag['rows_without_mapping'])}\n"
        )
        if woo_diag["rows_without_mapping"]:
            rep.write("First 50 Woo rows without mapping:\n")
            for item in woo_diag["rows_without_mapping"][:50]:
                rep.write(
                    f"  id={item['product_id']}, existing_name='{item['existing_name']}'\n"
                )


def main() -> None:
    if len(sys.argv) != 3:
        print(
            "Usage: rename_products_moretti.py <woo_export_csv> <master_sheet_csv>",
            file=sys.stderr,
        )
        raise SystemExit(1)

    woo_path = sys.argv[1]
    master_path = sys.argv[2]

    if not os.path.isfile(woo_path):
        raise SystemExit(f"Woo export CSV not found: {woo_path}")
    if not os.path.isfile(master_path):
        raise SystemExit(f"Master sheet CSV not found: {master_path}")

    id_to_name, master_diag = build_name_map_from_master(master_path)

    base_dir, woo_name = os.path.split(woo_path)
    full_fixed_path = os.path.join(
        base_dir, woo_name.replace(".csv", "") + "-names-fixed.csv"
    )

    woo_diag = update_woo_export(woo_path, id_to_name, full_fixed_path)

    minimal_csv_path = os.path.join(
        base_dir, woo_name.replace(".csv", "") + "-id-name-only.csv"
    )
    export_minimal_name_csv(full_fixed_path, minimal_csv_path)

    report_path = os.path.join(
        base_dir, woo_name.replace(".csv", "") + "-names-report.txt"
    )
    write_report(report_path, master_diag, woo_diag)

    print(f"Written updated Woo CSV (names+descriptions) to: {full_fixed_path}")
    print(f"Written minimal ID+Name CSV to: {minimal_csv_path}")
    print(f"Written diagnostics report to: {report_path}")


if __name__ == "__main__":
    main()

