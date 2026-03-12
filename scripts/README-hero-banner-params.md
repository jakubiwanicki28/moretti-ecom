# Parametry banerów hero (offset + CTA „Kup teraz”)

Banery na stronie głównej są w karuzeli w `index.php`, zdjęcia z `images/banners/` (np. `1.jpg`, `2.jpg`). Obecnie:
- **Obrazek:** `object-fit: cover`, `object-position: center center` (tak samo na desktop i mobile).
- **Przycisk „Kup teraz”:** jeden link dla wszystkich slajdów → strona sklepu (bez filtrów).

Potrzebne są **dwa parametry per baner**:
1. **Offset horyzontalny** – na mobile (gdy kadr się zoomuje) możliwość przesunięcia kadru w lewo/prawo, żeby ważna część zdjęcia była widoczna.
2. **Cel przycisku „Kup teraz”** – w zależności od slajdu: albo zwykły sklep, albo siatka sklepu z narzuconymi filtrami (np. kolekcja „Krok”, kolor, materiał).

---

## 1. Skąd brać dane per baner?

Banery są dziś budowane z plików w `images/banners/` (numer w nazwie = kolejność). Żeby mieć offset i CTA **per zdjęcie**, potrzebna jest konfiguracja powiązana z plikiem.

**Propozycja: plik konfiguracyjny w theme**

- Ścieżka: np. `images/banners/config.json` (albo w głównym katalogu theme: `hero-banners-config.json`).
- Klucz = **nazwa pliku banera** (np. `1.jpg`, `2.jpg`), żeby jednoznacznie powiązać ustawienia ze slajdem.

Przykład `config.json`:

```json
{
  "1.jpg": {
    "offset_x_mobile": "30%",
    "cta_url": "",
    "cta_filters": {}
  },
  "2.jpg": {
    "offset_x_mobile": "-15%",
    "cta_url": "",
    "cta_filters": { "filter_material": "skora-naturalna" }
  },
  "3.jpg": {
    "offset_x_mobile": "50%",
    "cta_url": "https://twoja-domena.pl/sklep/",
    "cta_filters": {}
  }
}
```

- **offset_x_mobile** – wartość dla `object-position` **tylko na mobile** (np. `"30%"` = kadr przesunięty w prawo, `"-20%"` w lewo; `"center"` lub brak = środek).
- **cta_url** – jeśli podany, przycisk „Kup teraz” na tym slajdzie prowadzi do tego URL (np. strona sklepu, kategoria, dowolna strona).
- **cta_filters** – jeśli **cta_url** jest pusty, link budujesz z bazowego URL sklepu + te parametry (tak jak robi `moretti_build_shop_url` w `woocommerce/archive-product.php`). Dozwolone klucze: `filter_color`, `filter_kolor`, `filter_material`, `filter_size`, ewentualnie `min_price` / `max_price`, `orderby`.

Zasada:  
- jeśli jest **cta_url** → używaj go;  
- jeśli brak **cta_url** a są **cta_filters** → budujesz URL sklepu z `add_query_arg(cta_filters, shop_url)`;  
- jeśli oba puste → fallback na zwykły link do sklepu (jak teraz).

**Alternatywa:** te same pola możesz trzymać w WordPress (np. theme mod z repeaterem albo opcje w Customizerze) zamiast w JSON – wtedy w PHP ładujesz je w tym samym miejscu, gdzie dziś budujesz `$hero_banners`, i przekazujesz do pętli.

---

## 2. Parametr 1: Offset horyzontalny (mobile)

**Cel:** Na viewportach mobilnych zmienić pozycję kadru zdjęcia (lewo/prawo), bez zmiany na desktop.

**Implementacja:**

1. **Dane**  
   Dla każdego slajdu w pętli masz już `$hero_banner['name']` (np. `1.jpg`). Z configu (JSON lub WP) odczytujesz `offset_x_mobile` dla tego pliku. Jeśli brak – używasz `'center'` (obecne zachowanie).

2. **HTML**  
   Na elemencie `<img>` (albo na `.moretti-hero-slide`) ustaw **inline style** lub **data-atrybut** z wartością pozycji, żeby CSS mógł z tego skorzystać.

   Przykład (inline style tylko na mobile nie jest wygodny, więc lepiej **data-atrybut** + CSS):

   - W pętli slajdów w `index.php` dodaj np.  
     `data-object-position-x="<?php echo esc_attr($hero_banner_offset_x); ?>"`  
     gdzie `$hero_banner_offset_x` to wartość z configu (np. `30%`, `-20%`, `center`).

   - W CSS (np. w `index.php` w bloku `<style>` albo w pliku CSS hero) w media query **tylko mobile** (np. `max-width: 767px`):

   ```css
   #moretti-home-hero .moretti-hero-slide img[data-object-position-x] {
     object-position: var(--hero-object-x, center) center;
   }
   ```

   - W JS przy zmianie slajdu (gdy aktualizujesz „active” slide) ustawiasz na kontenerze hero (np. `#moretti-home-hero`) zmienną CSS:
     `--hero-object-x: 30%;`  
     wartość wziętą z `data-object-position-x` **aktywnego** slajdu. Dzięki temu tylko widoczny slajd decyduje o pozycji (a na mobile wszystkie slajdy są w DOM, więc lepiej sterować jedną zmienną z aktywnego slajdu).

   **Prostsza wersja bez JS:**  
   Dla każdego slajdu w CSS nadaj klasę lub attribute selector i ustaw `object-position` w media query mobile. Np. slajd ma `data-slide-index="0"`, a w configu dla `1.jpg` jest `offset_x_mobile: "30%"`. W PHP nadajesz na tym slajdzie style w `<style>` w formie:

   ```css
   @media (max-width: 767px) {
     #moretti-home-hero .moretti-hero-slide[data-slide-index="0"] img {
       object-position: 30% center;
     }
   }
   ```

   Wartość `30%` z configu. W ten sposób nie potrzebujesz JS do object-position.

3. **Gdzie w kodzie**  
   - Odczyt configu: na początku `index.php`, tam gdzie budujesz `$hero_banners` – po pętli `scandir` dla każdego elementu `$hero_banners` dopisujesz klucze np. `offset_x_mobile` i `cta_url` / `cta_filters` z pliku JSON (lub z theme mod).  
   - Render: w pętli `foreach ($hero_banners as $hero_banner_index => $hero_banner)` przy `<div class="moretti-hero-slide">` / `<img>` dodajesz atrybut lub wygenerowany fragment CSS.  
   - CSS: w tym samym pliku w `<style>` (lub w pliku CSS motywu) w media query `(max-width: 767px)` dodajesz reguły dla `#moretti-home-hero .moretti-hero-slide img` z `object-position: X center` (X z configu).

---

## 3. Parametr 2: Przycisk „Kup teraz” → link / filtry sklepu

**Cel:** Klik w „Kup teraz” na danym banerze ma prowadzić do sklepu (lub innej strony), ewentualnie do siatki z narzuconymi filtrami (kolekcja, kolor, materiał, rozmiar).

**Implementacja:**

1. **Dane**  
   Per baner (np. z `config.json`):  
   - **cta_url** – gotowy URL (np. strona sklepu, kategoria produktu, kolekcja).  
   - **cta_filters** – tablica parametrów zapytania (np. `filter_material`, `filter_color`). Używana tylko gdy **cta_url** jest pusty; wtedy budujesz URL sklepu i dodajesz te parametry.

   Sklep w WooCommerce obsługuje m.in. (z `woocommerce/archive-product.php`):
   - `filter_color` / `filter_kolor`
   - `filter_material`
   - `filter_size`
   - `min_price`, `max_price`, `orderby`

   Kategoria/kolekcja: możesz albo podać w **cta_url** bezpośrednio link do archiwum kategorii (np. `/product-category/portfele-meskie/`), albo – jeśli kiedyś dodasz filtr po „kolekcja” (np. atrybut) – wpisać go w **cta_filters**.

2. **Budowanie URL w PHP**  
   W `index.php`, w miejscu gdzie dziś jest jeden link:

   ```php
   <a href="<?php echo esc_url(get_permalink(wc_get_page_id('shop'))); ?>" ...>KUP TERAZ</a>
   ```

   zrób tak:
   - Dla **aktywnego** slajdu nie da się w PHP wybrać „który slajd jest aktywny” (to jest po stronie JS). Dlatego trzeba **jeden przycisk zamienić na wiele przycisków** (jeden per slajd), schowanych w tym samym miejscu i pokazywanych tylko gdy dany slajd jest aktywny – **albo** jeden przycisk, a **href** ustawiasz w JS na podstawie indeksu slajdu.

   **Wariant A – wiele linków (bez JS do href):**  
   W pętli banerów generujesz np.:

   ```php
   <?php foreach ($hero_banners as $idx => $hero_banner) :
       $cta = $hero_banner['cta_url'] ?? '';
       if ($cta === '' && !empty($hero_banner['cta_filters'])) {
           $cta = add_query_arg($hero_banner['cta_filters'], get_permalink(wc_get_page_id('shop')));
       }
       if ($cta === '') {
           $cta = get_permalink(wc_get_page_id('shop'));
       }
   ?>
   <a href="<?php echo esc_url($cta); ?>"
      class="moretti-hero-cta ... <?php echo $idx !== 0 ? 'hidden' : ''; ?>"
      data-slide-index="<?php echo (int) $idx; ?>">KUP TERAZ</a>
   <?php endforeach; ?>
   ```

   W JS przy zmianie slajdu (`currentSlide`) przełączasz klasę (np. `hidden` / block), żeby widoczny był tylko link dla `data-slide-index === currentSlide`. W ten sposób każdy slajd ma swój link, bez zmiany href w JS.

   **Wariant B – jeden przycisk, href z JS:**  
   Zostawiasz jeden `<a id="moretti-hero-cta">`. W PHP gdzieś (np. w `<script>` lub `data-*` na sekcji) przekazujesz tablicę URLi w kolejności slajdów, np. `var heroCtaUrls = [ "<?php echo esc_js($url1); ?>", "..." ];`. W obsłudze zmiany slajdu robisz `document.getElementById('moretti-hero-cta').href = heroCtaUrls[currentSlide];`.

3. **Spójność z filtrami sklepu**  
   Sklep w `archive-product.php` czyta `$_GET['filter_material']` itd. i buduje `tax_query`. Wystarczy, że link z banera ma te same nazwy parametrów (np. `?filter_material=skora-naturalna`). Nie musisz wywoływać `moretti_build_shop_url` w index (ta funkcja jest w archive-product). Wystarczy `add_query_arg($hero_banner['cta_filters'], get_permalink(wc_get_page_id('shop')))` w index, z tym samym zestawem kluczy (`filter_color`, `filter_material`, itd.).

4. **Kolekcja / kategoria**  
   - Jeśli „kolekcja” to **kategoria WooCommerce** (product_cat): w configu podaj w **cta_url** bezpośrednio URL kategorii, np. `https://twoja-domena.pl/product-category/krok/` (slug kategorii).  
   - Jeśli „kolekcja” to **atrybut produktu** (np. pa_kolekcja): dopóki w `archive-product.php` nie ma obsługi `filter_kolekcja`, musisz albo dodać tam obsługę tego parametru w `tax_query`, albo trzymać kolekcje jako kategorie i używać **cta_url** do kategorii.

---

## 4. Krótka checklista (co zrobić samodzielnie)

- [ ] Dodać plik konfiguracyjny (np. `images/banners/config.json`) lub theme mod z danymi per plik banera: `offset_x_mobile`, `cta_url`, `cta_filters`.
- [ ] W `index.php` przy budowaniu `$hero_banners` wczytać config i dla każdego wpisu dopisać te pola (fallback: offset `center`, CTA = sklep).
- [ ] **Offset:** W pętli slajdów ustawić na slajdzie atrybut (np. `data-object-position-x`) lub wygenerować CSS w media query mobile z `object-position: X center`. Ewentualnie w JS przy zmianie slajdu ustawiać `--hero-object-x` na kontenerze.
- [ ] **CTA:** Albo wiele linków „Kup teraz” (jeden per slajd, pokazywany według `currentSlide`), albo jeden link z tablicą URLi w JS i ustawianiem `href` przy zmianie slajdu.
- [ ] URL dla CTA: jeśli jest `cta_url` – użyć; jeśli nie – z `cta_filters` zbudować `add_query_arg(cta_filters, get_permalink(wc_get_page_id('shop')))`; jeśli nic – fallback na sklep.
- [ ] (Opcja) W archive-product dodać obsługę np. `filter_kolekcja` (taxonomy atrybutu kolekcji), jeśli chcesz linkować do „kolekcja X” przez filtry zamiast przez URL kategorii.

Dzięki temu na mobile będziesz mógł ustawiać offset kadru per baner, a przycisk „Kup teraz” będzie prowadził do sklepu z wybranymi filtrami lub do podanego URL (np. kategoria/kolekcja).
