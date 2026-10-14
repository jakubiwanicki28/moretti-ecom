#!/usr/bin/env python3
"""
Generuje inc/data/pl-postcodes.php — współrzędne polskich kodów pocztowych do centrowania mapy punktów odbioru.

  scripts/build-pl-postcodes.py

Źródło: GeoNames, https://download.geonames.org/export/zip/PL.zip (CC BY 4.0, https://www.geonames.org).
Kod → mediana współrzędnych miejscowości z tym kodem. Brakujące kody i błędne wpisy (np. 00-200 przypisany
do wsi pod Makowem) obsługuje fallback na prefiksy „XX-X” i „XX” — wpis odległy o > 40 km od mediany
swojego prefiksu „XX-X” jest pomijany.
"""
import csv
import io
import math
import os
import statistics
import urllib.request
import zipfile
from collections import defaultdict

ROOT = os.path.dirname(os.path.dirname(os.path.abspath(__file__)))
OUT = os.path.join(ROOT, "inc", "data", "pl-postcodes.php")
SOURCE = "https://download.geonames.org/export/zip/PL.zip"
MAX_OUTLIER_KM = 40


def distance_km(a, b):
    lat1, lon1, lat2, lon2 = map(math.radians, (a[0], a[1], b[0], b[1]))
    h = math.sin((lat2 - lat1) / 2) ** 2 + math.cos(lat1) * math.cos(lat2) * math.sin((lon2 - lon1) / 2) ** 2
    return 2 * 6371 * math.asin(math.sqrt(h))


def median_point(points):
    return (round(statistics.median(p[0] for p in points), 3), round(statistics.median(p[1] for p in points), 3))


def main():
    raw = urllib.request.urlopen(SOURCE, timeout=60).read()
    text = zipfile.ZipFile(io.BytesIO(raw)).read("PL.txt").decode("utf-8")

    by_code = defaultdict(list)
    for row in csv.reader(io.StringIO(text), delimiter="\t"):
        by_code[row[1]].append((float(row[9]), float(row[10])))

    by_prefix3, by_prefix2 = defaultdict(list), defaultdict(list)
    for code, points in by_code.items():
        by_prefix3[code[:4]].extend(points)
        by_prefix2[code[:2]].extend(points)

    prefix3 = {p: median_point(v) for p, v in by_prefix3.items()}
    prefix2 = {p: median_point(v) for p, v in by_prefix2.items()}

    codes, skipped = {}, 0
    for code, points in by_code.items():
        point = median_point(points)
        if distance_km(point, prefix3[code[:4]]) > MAX_OUTLIER_KM:
            skipped += 1
            continue
        codes[code] = point

    def php_map(data):
        return ",".join(f"'{k}'=>[{v[0]},{v[1]}]" for k, v in sorted(data.items()))

    os.makedirs(os.path.dirname(OUT), exist_ok=True)
    with open(OUT, "w") as f:
        f.write("<?php\n// Wygenerowane przez scripts/build-pl-postcodes.py — nie edytować ręcznie.\n")
        f.write(f"// Dane: GeoNames ({SOURCE}), licencja CC BY 4.0, https://www.geonames.org\n")
        f.write("return [\n")
        f.write(f"'codes'=>[{php_map(codes)}],\n")
        f.write(f"'prefix3'=>[{php_map(prefix3)}],\n")
        f.write(f"'prefix2'=>[{php_map(prefix2)}],\n")
        f.write("];\n")

    print(f"{len(codes)} kodów, {len(prefix3)} prefiksów XX-X, {len(prefix2)} XX; pominięte odstające: {skipped}")
    print(f"→ {os.path.relpath(OUT, ROOT)} ({os.path.getsize(OUT) // 1024} KB)")


if __name__ == "__main__":
    main()
