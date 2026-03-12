# Migracja kategorii / nazwy / SKU → atrybuty (Kolekcja, Materiał)

## Wygenerowany CSV

Skrypt `migrate-categories-to-attributes-csv.py` czyta eksport WooCommerce i tworzy **minimalny CSV do importu**: Identyfikator, Kategorie, Kolekcja (atrybut 1), Materiał (atrybut 2).

- **Wejście:** eksport produktów WooCommerce (CSV z kolumnami Kategorie, SKU, Nazwa oraz opcjonalnie atrybuty 1 i 2).
- **Wyjście:** np. `moretti-migracja-kolekcja-material.csv` (ścieżka jako drugi argument).

Kolekcja jest uzupełniana w kolejności: istniejąca wartość atrybutu 1 → Kategorie (CROCO, PIÓRA, Animals, Snake) → prefiks SKU (CR_→Croco, PI_→Pióra) → słowa w Nazwa (CROCO LUXE, SOFT FEATHERS itd.).  
Materiał: istniejąca wartość atrybutu 2 → Kategorie → słowa w Nazwa.

Kolumny wyjściowe:

| Kolumna | Opis |
|--------|------|
| Identyfikator | ID produktu – **matchowanie** przy imporcie. |
| Kategorie | Kategorie po wyczyszczeniu (bez CROCO, PIÓRA, Skóra* itd.; Dział* → Portfele*). |
| Nazwa atrybutu 1 / Wartości atrybutu 1 | Kolekcja (Croco, Pióra, Animals, Snake). |
| Nazwa atrybutu 2 / Wartości atrybutu 2 | Materiał (Skóra lakierowana, Skóra matowa, Skóra naturalna). |

## Użycie skryptu

```bash
python3 scripts/migrate-categories-to-attributes-csv.py /ścieżka/do/eksport.csv [/ścieżka/do/wynik.csv]
```

Jeśli nie podasz drugiego argumentu, wynik zapisze się obok pliku wejściowego z sufiksem `-migracja-kategorie-atrybuty.csv`.

## Import w WordPress – dwie opcje

### Opcja A: Importer CSV (najpierw spróbuj tej)

1. **WooCommerce → Produkty → Import** → wybierz plik `moretti-migracja-kolekcja-material.csv`.
2. **Mapowanie kolumn – ważne:**  
   Każda kolumna musi trafić na **właściwe pole z numerem**, a nie na jedno wspólne:
   - **Nazwa atrybutu 1** → mapuj na **„Nazwa atrybutu 1”** (albo „Attribute 1 name” – pole z cyfrą 1).
   - **Wartości atrybutu 1** → **„Wartości atrybutu 1”** (Attribute 1 values).
   - **Nazwa atrybutu 2** → **„Nazwa atrybutu 2”** (pole z cyfrą 2).
   - **Wartości atrybutu 2** → **„Wartości atrybutu 2”**.

   **Nie mapuj** obu par na jedno pole typu „Nazwa atrybutu” / „Wartości atrybutu” bez numeru – wtedy importer ustawi tylko jeden atrybut i Liczba przy Kolekcja/Materiał zostanie 0.
3. **Identyfikator** → Identyfikator produktu (do aktualizacji istniejących).
4. Tryb: **Aktualizuj istniejące produkty** (po Identyfikatorze).
5. Uruchom import. Sprawdź w **Produkty → Atrybuty → Kolekcja** (i Materiał), czy Liczba się zwiększyła.

Jeśli w „Mapuj do pola” **nie ma** opcji „Nazwa atrybutu 1” / „Wartości atrybutu 1” (tylko jedno pole „Nazwa atrybutu”), importer nie obsługuje wielu atrybutów z tego pliku – wtedy użyj **Opcji B**.

### Opcja B: Skrypt PHP (gdy importer nie przypisuje termów)

Skrypt czyta ten sam CSV i **bezpośrednio** przypisuje termy Kolekcja/Materiał do produktów (bez importera).

1. Plik CSV musi być w folderze motywu:  
   `wp-content/themes/TWOJ-MOTYW/scripts/moretti-migracja-kolekcja-material.csv`
2. Jako administrator wejdź jednorazowo na:  
   `https://twoja-domena.pl/?moretti_assign_kolekcja_material=1`
3. Pojawi się przekierowanie i komunikat typu „Zaktualizowano X produktów”. Odśwież **Produkty → Atrybuty → Kolekcja** (kolumna Liczba) i sklep.

## Co nie jest w CSV

- **Kolor** – nie zmieniamy; pozostaje w produktach.
- Pozostałe kolumny (nazwa, cena, obrazy, SKU itd.) – nie są w pliku, więc import ich nie nadpisze.

## Po imporcie: produkty się nie wyświetlają

Importer WooCommerce przy aktualizacji istniejących produktów z CSV **bez kolumn „Opublikowano” i „Widoczność w katalogu”** może ustawić status na **Szkic** lub widoczność na **ukryty**. Wtedy strona sklepu i kategorie są puste, bo motyw pokazuje tylko produkty ze statusem **Opublikowany** i widoczne w katalogu.

**Rozwiązanie (jednorazowo):**

1. **Z poziomu motywu (zalecane)** – będąc zalogowanym jako administrator, wejdź na dowolną stronę z parametrem:  
   `https://twoja-domena.pl/?moretti_fix_visibility=1`  
   Naprawa wykona się, nastąpi przekierowanie i komunikat. Odśwież sklep.

2. **Skrypt PHP** – jeśli wolisz:  
   `https://twoja-domena.pl/wp-content/themes/NAZWA-MOTYWU/scripts/fix-product-visibility-after-import.php`  
   (zamień NAZWA-MOTYWU na faktyczną nazwę folderu motywu, np. `moretti-ecom`).  
   Lub przez WP-CLI:  
   `wp eval-file wp-content/themes/NAZWA-MOTYWU/scripts/fix-product-visibility-after-import.php`

W obu przypadkach wszystkim produktom ustawiane są `post_status = publish` i `catalog_visibility = visible`.
