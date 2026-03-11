import csv
import os
import re
import sys
import unicodedata
from typing import Dict, Tuple, Optional, Any


def remove_accents(value: str) -> str:
    """
    Remove Polish/diacritic characters, keep case.
    """
    normalized = unicodedata.normalize("NFKD", value)
    return "".join(ch for ch in normalized if not unicodedata.combining(ch))


def slug_from_color(color_name: str) -> str:
    """
    Build KOLOR_SLUG from human-readable color.
    Example: 'Jasny Brąz' -> 'Jasny-Braz'
    """
    color_name = (color_name or "").strip()
    if not color_name:
        return ""

    no_accents = remove_accents(color_name)
    # Replace spaces and underscores with hyphens
    slug = re.sub(r"[\\s_]+", "-", no_accents)
    # Collapse multiple hyphens
    slug = re.sub(r"-{2,}", "-", slug)
    # Strip leading/trailing hyphens
    slug = slug.strip("-")
    return slug


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


def build_master_map(master_path: str) -> Tuple[Dict[str, str], Dict[str, Any]]:
    """
    Return:
      - mapping: product_id (string) -> final_sku
      - diagnostics: various counters and ambiguous cases
    """
    mapping: Dict[str, str] = {}
    diagnostics: Dict[str, Any] = {
        "rows_total": 0,
        "rows_with_id": 0,
        # rows_missing_id: master rows where we couldn't parse post ID
        "rows_missing_id": [],
        "rows_missing_base_or_color": [],
        "rows_with_empty_final": 0,
    }

    with open(master_path, "r", encoding="utf-8-sig", newline="") as fh:
        reader = csv.reader(fh)
        try:
            header = next(reader)
        except StopIteration:
            return mapping, diagnostics

        # Determine column indices by name (robust to column order)
        header_index = {name.strip(): idx for idx, name in enumerate(header)}

        def col(name: str) -> int:
            return header_index.get(name, -1)

        idx_url = col("Link do edycji produktu w sklepie ")
        if idx_url < 0:
            # Fallback without trailing space
            idx_url = col("Link do edycji produktu w sklepie")

        idx_sku_final = col("SKU FINAL (with color)")
        idx_color = col("Kolor")
        idx_sku_base = col("[SKU BASE]")

        for row in reader:
            diagnostics["rows_total"] += 1

            # Pad row if it's shorter than header
            if len(row) < len(header):
                row = row + [""] * (len(header) - len(row))

            url = row[idx_url] if idx_url >= 0 and idx_url < len(row) else ""
            product_id = parse_post_id_from_url(url)
            if not product_id:
                diagnostics["rows_missing_id"].append(
                    {
                        "row": diagnostics["rows_total"],
                        "url": url,
                    }
                )
                continue

            diagnostics["rows_with_id"] += 1

            sku_base = (
                row[idx_sku_base].strip()
                if idx_sku_base >= 0 and idx_sku_base < len(row)
                else ""
            )
            color_name = (
                row[idx_color].strip()
                if idx_color >= 0 and idx_color < len(row)
                else ""
            )
            sku_final_raw = (
                row[idx_sku_final].strip()
                if idx_sku_final >= 0 and idx_sku_final < len(row)
                else ""
            )

            if not sku_base or not color_name:
                diagnostics["rows_missing_base_or_color"].append(
                    {
                        "product_id": product_id,
                        "row": diagnostics["rows_total"],
                        "sku_base": sku_base,
                        "color": color_name,
                        "sku_final_raw": sku_final_raw,
                    }
                )

            color_slug = slug_from_color(color_name)

            # If we have an explicit final SKU, we could trust it, but we want
            # to normalize and ensure it is always BASE-COLOR_SLUG, without
            # CR_/PI_/CROCO- style prefixes.
            if sku_base and color_slug:
                final_sku = f"{sku_base}-{color_slug}"
            elif sku_final_raw:
                # Fallback: try to strip any leading prefix up to first letter
                tmp = sku_final_raw
                # Replace multiple hyphens, trim spaces
                tmp = re.sub(r"\\s+", "", tmp)
                tmp = re.sub(r"-{2,}", "-", tmp).strip("-")
                final_sku = tmp
            else:
                diagnostics["rows_with_empty_final"] += 1
                continue

            mapping[product_id] = final_sku

    return mapping, diagnostics


def apply_mapping_to_woo(
    woo_export_path: str, mapping: Dict[str, str], output_path: str
) -> Dict[str, Any]:
    """
    Read Woo export CSV, replace SKU where we have mapping, and write new CSV.
    """
    diagnostics: Dict[str, Any] = {
        "woo_rows_total": 0,
        "woo_rows_with_id": 0,
        "woo_rows_updated": 0,
        "woo_rows_unmapped": [],
    }

    with open(woo_export_path, "r", encoding="utf-8-sig", newline="") as in_fh, open(
        output_path, "w", encoding="utf-8-sig", newline=""
    ) as out_fh:
        reader = csv.reader(in_fh)
        writer = csv.writer(out_fh)

        try:
            header = next(reader)
        except StopIteration:
            return diagnostics

        writer.writerow(header)

        header_index = {name.strip(): idx for idx, name in enumerate(header)}
        idx_id = header_index.get("Identyfikator", 0)
        idx_sku = header_index.get("SKU", -1)

        for row in reader:
            diagnostics["woo_rows_total"] += 1
            if len(row) < len(header):
                row = row + [""] * (len(header) - len(row))

            product_id = row[idx_id].strip()
            if product_id:
                diagnostics["woo_rows_with_id"] += 1

            mapped_sku = mapping.get(product_id)
            if mapped_sku and idx_sku >= 0:
                row[idx_sku] = mapped_sku
                diagnostics["woo_rows_updated"] += 1
            elif product_id and product_id not in mapping:
                diagnostics["woo_rows_unmapped"].append(
                    {
                        "product_id": product_id,
                        "existing_sku": row[idx_sku] if idx_sku >= 0 else "",
                    }
                )

            writer.writerow(row)

    return diagnostics


def main() -> None:
    if len(sys.argv) != 3:
        print(
            "Usage: standardize_sku_moretti.py <woo_export_csv> <master_sheet_csv>",
            file=sys.stderr,
        )
        sys.exit(1)

    woo_path = sys.argv[1]
    master_path = sys.argv[2]

    if not os.path.isfile(woo_path):
        raise SystemExit(f"Woo export CSV not found: {woo_path}")
    if not os.path.isfile(master_path):
        raise SystemExit(f"Master sheet CSV not found: {master_path}")

    mapping, master_diag = build_master_map(master_path)

    base_dir, woo_name = os.path.split(woo_path)
    output_name = woo_name.replace(".csv", "") + "-sku-fixed.csv"
    output_path = os.path.join(base_dir, output_name)

    woo_diag = apply_mapping_to_woo(woo_path, mapping, output_path)

    # Simple text report next to output CSV
    report_path = os.path.join(base_dir, woo_name.replace(".csv", "") + "-sku-report.txt")
    with open(report_path, "w", encoding="utf-8") as rep:
        rep.write("MASTER SHEET DIAGNOSTICS\n")
        rep.write(f"Total rows: {master_diag['rows_total']}\n")
        rep.write(f"Rows with product ID: {master_diag['rows_with_id']}\n")
        rep.write(
            f"Rows missing base or color: {len(master_diag['rows_missing_base_or_color'])}\n"
        )
        rep.write(
            f"Rows with empty final when no base/color: {master_diag['rows_with_empty_final']}\n"
        )
        rep.write(f"Mapping entries: {len(mapping)}\n\n")

        if master_diag["rows_missing_id"]:
            rep.write("Rows in master sheet without detectable product ID (post=):\n")
            for item in master_diag["rows_missing_id"][:50]:
                rep.write(f"  row {item['row']}: url={item['url']}\n")
            rep.write("\n")

        if master_diag["rows_missing_base_or_color"]:
            rep.write("Rows in master sheet missing SKU BASE or Color (first 50):\n")
            for item in master_diag["rows_missing_base_or_color"][:50]:
                rep.write(
                    f"  row {item['row']}: id={item['product_id']}, base='{item['sku_base']}', color='{item['color']}', sku_final_raw='{item['sku_final_raw']}'\n"
                )
            rep.write("\n")

        rep.write("WOO EXPORT DIAGNOSTICS\n")
        rep.write(f"Total rows: {woo_diag['woo_rows_total']}\n")
        rep.write(f"Rows with product ID: {woo_diag['woo_rows_with_id']}\n")
        rep.write(f"Rows with updated SKU: {woo_diag['woo_rows_updated']}\n")

        unmapped = woo_diag.get("woo_rows_unmapped", [])
        rep.write(f"Rows with product ID but no mapping: {len(unmapped)}\n")
        if unmapped:
            rep.write("First 50 unmapped rows (for review):\n")
            for item in unmapped[:50]:
                rep.write(
                    f"  id={item['product_id']}, existing_sku='{item['existing_sku']}'\n"
                )

    print(f"Written fixed CSV to: {output_path}")
    print(f"Written diagnostics report to: {report_path}")


if __name__ == "__main__":
    main()

