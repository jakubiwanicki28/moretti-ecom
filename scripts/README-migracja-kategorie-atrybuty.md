# Migracja kategorii → atrybuty (Kolekcja, Materiał)

## Wygenerowany CSV

Skrypt `migrate-categories-to-attributes-csv.py` czyta eksport WooCommerce i tworzy **minimalny CSV do importu**: tylko kolumny, które aktualizujemy (Identyfikator, Kategorie, atrybuty 4 i 5), żeby nie nadpisywać reszty danych.

- **Wejście:** eksport produktów WooCommerce (CSV z kolumną Kategorie).
- **Wyjście:** `moretti-migracja-kategorie-atrybuty.csv` (domyślnie w katalogu podanym jako drugi argument).

Kolumny wyjściowe:

| Kolumna | Opis |
|--------|------|
| Identyfikator | ID posta produktu – **używane do matchowania** przy imporcie. |
| Kategorie | Nowa lista kategorii (bez CROCO, PIÓRA, Wzory Zwierzęce, Skóra*, Wizytowniki; Dział Damski/Męski zamienione na Portfele damskie/męskie). |
| Nazwa atrybutu 4 / Wartości atrybutu 4 | Kolekcja (Croco, Pióra, Animals). |
| Nazwa atrybutu 5 / Wartości atrybutu 5 | Materiał (Skóra lakierowana, Skóra matowa, Skóra naturalna). |

## Użycie skryptu

```bash
python3 scripts/migrate-categories-to-attributes-csv.py /ścieżka/do/eksport.csv [/ścieżka/do/wynik.csv]
```

Jeśli nie podasz drugiego argumentu, wynik zapisze się obok pliku wejściowego z sufiksem `-migracja-kategorie-atrybuty.csv`.

## Import w WordPress

1. **Produkty → Import** (lub Eksport/Import WooCommerce).
2. Wybierz plik `moretti-migracja-kategorie-atrybuty.csv`.
3. **Dopasuj kolumny:** upewnij się, że "Identyfikator" jest mapowany na pole identyfikatora produktu (ID), żeby **aktualizować istniejące** produkty.
4. Tryb: **Aktualizuj istniejące produkty** (po Identyfikatorze).
5. Uruchom import – zaktualizowane zostaną tylko kategorie i atrybuty 4 (Kolekcja) oraz 5 (Materiał).

Jeśli importer nie rozpoznaje "Nazwa atrybutu 4/5", w mapowaniu kolumn ustaw te kolumny na odpowiednie pola atrybutów (Kolekcja, Materiał).

## Co nie jest w CSV

- **Kolor** – nie zmieniamy; pozostaje w produktach.
- Pozostałe kolumny (nazwa, cena, obrazy, SKU itd.) – nie są w pliku, więc import ich nie nadpisze.
