import csv
import os
import sys
from typing import List


def extract_id_and_sku(source_csv: str, output_csv: str) -> None:
    with open(source_csv, "r", encoding="utf-8-sig", newline="") as src_fh, open(
        output_csv, "w", encoding="utf-8-sig", newline=""
    ) as out_fh:
        reader = csv.reader(src_fh)
        writer = csv.writer(out_fh)

        try:
            header: List[str] = next(reader)
        except StopIteration:
            return

        # Map header names to indices
        header_index = {name.strip(): idx for idx, name in enumerate(header)}
        idx_id = header_index.get("Identyfikator")
        idx_sku = header_index.get("SKU")

        if idx_id is None or idx_sku is None:
            raise SystemExit(
                "Source CSV must contain 'Identyfikator' and 'SKU' columns."
            )

        # Write minimal header
        writer.writerow(["Identyfikator", "SKU"])

        for row in reader:
            if len(row) <= max(idx_id, idx_sku):
                continue
            product_id = (row[idx_id] or "").strip()
            sku = (row[idx_sku] or "").strip()
            if not product_id:
                continue
            writer.writerow([product_id, sku])


def main() -> None:
    if len(sys.argv) != 3:
        print(
            "Usage: extract_id_sku_moretti.py <source_fixed_csv> <output_csv>",
            file=sys.stderr,
        )
        raise SystemExit(1)

    source_csv = sys.argv[1]
    output_csv = sys.argv[2]

    if not os.path.isfile(source_csv):
        raise SystemExit(f"Source CSV not found: {source_csv}")

    extract_id_and_sku(source_csv, output_csv)
    print(f"Wrote ID+SKU CSV to: {output_csv}")


if __name__ == "__main__":
    main()

