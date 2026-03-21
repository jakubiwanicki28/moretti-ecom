<?php
/**
 * Konfiguracja banerów hero (strona główna).
 *
 * === GDZIE USTAWIAĆ DANE I POZYCJĘ ===
 * Wszystko poniżej: każdy wpis (0, 1, 2, 3...) to jeden slajd. Kolejność = kolejność na stronie.
 *
 * Tekst na slajdzie:
 *   title    – tytuł. Pusty '' = brak tytułu. Łamanie linii: wstaw dosłownie {{BR}} (np. 'PIERWSZA LINIA {{BR}} DRUGA').
 *   subtitle – podtytuł. To samo: {{BR}} = nowa linia w tym samym bloku.
 *   cta_text – tekst przycisku (np. "KUP TERAZ"). Pusty = brak przycisku.
 *
 * Pozycja bloku tekstu (żeby nie zasłaniał ważnych elementów na zdjęciu):
 *   text_position – ustaw na: left-top, left-center, left-bottom, center-top, center-center,
 *                   center-bottom, right-top, right-center, right-bottom.
 *   Np. right-bottom = tekst w prawym dolnym rogu; left-center = tekst po lewej na środku.
 *
 * Przesunięcie samego zdjęcia w poziomie (kadr):
 *   offset_x – liczba px. Plus = zdjęcie w prawo, minus = w lewo. 0 = bez przesunięcia.
 *
 * Indeks = kolejność slajdu (0, 1, 2, ...).
 *
 * offset_x            – przesunięcie zdjęcia w poziomie, desktop (px). Plus = w prawo, minus = w lewo.
 * offset_x_mobile     – (opcjonalnie) to samo dla mobile. Nie podane = użycie offset_x.
 * content_offset_x    – (opcjonalnie) przesunięcie bloku tekstu+CTA w poziomie, desktop (px).
 * content_offset_y    – (opcjonalnie) przesunięcie bloku tekstu+CTA w pionie, desktop (px).
 * content_offset_x_mobile, content_offset_y_mobile – (opcjonalnie) to samo dla mobile.
 * text_position       – pozycja bloku tekstu (desktop). left-top, left-center, … right-bottom.
 * text_position_mobile – (opcjonalnie) pozycja bloku tekstu na mobile. Nie podane = text_position.
 * title               – tytuł slajdu (HTML). Pusty = brak tytułu.
 * subtitle            – podtytuł (HTML). Pusty = brak.
 * cta_text            – tekst przycisku. Domyślnie "KUP TERAZ".
 * cta_url             – (opcjonalnie) gotowy adres przycisku. Jeśli podany, reszta jest ignorowana.
 * cta_filters         – (opcjonalnie) tablica: nazwa parametru => slug termu. Buduje URL sklepu z filtrami.
 * cta_category_slug   – (opcjonalnie) slug kategorii product_cat. Buduje link do archiwum kategorii.
 * overlay_desktop     – (opcjonalnie) nazwa pliku obrazka overlay (desktop), np. tekst jako PNG. W images/banners/.
 * overlay_mobile       – (opcjonalnie) nazwa pliku obrazka overlay (mobile). Pusty = ten sam co desktop.
 *
 * Taksonomie (WooCommerce → Atrybuty → Konfiguruj taksonomie) – slugi z panelu:
 *
 *   Kolekcja (uproszczona nazwa: kolekcja), param: filter_kolekcja
 *     animals | croco | piora | snake
 *
 *   Kolor (uproszczona nazwa: kolor), param: filter_color lub filter_kolor
 *     zielony | fioletowy | bordowy | jasny-braz | ciemny-braz | czerwony | czarny | ...
 *
 *   Materiał (uproszczona nazwa: materiał), param: filter_material
 *     skora-lakierowana | skora-matowa
 *     (wykończenie: lakierowana vs matowa; wszystko to skóra naturalna)
 *
 *   Kategorie (product_cat), cta_category_slug
 *     portfele-meskie | portfele-damskie
 */
return array(
    // Baner 1: Beże i brązy (Matowy prestiż)
    0 => array(
        'offset_x'             => 0,
        'text_position'        => 'left-center',
        'text_position_mobile' => 'left-bottom',
        'title'                => 'MATOWY PRESTIŻ',
        'subtitle'             => 'Odkryj aksamitną strukturę skóry, która definiuje luksus na nowo.{{BR}}Minimalizm w najczystszej formie.',
        'cta_text'             => 'ODKRYJ KOLEKCJĘ',
        'cta_filters'          => array( 'filter_material' => 'skora-matowa' ),
    ),
    // Baner 2: Kolekcja Croco
    1 => array(
        'offset_x'             => 0,
        'text_position'        => 'left-center',
        'text_position_mobile' => 'left-bottom',
        'title'                => 'SIŁA CHARAKTERU',
        'subtitle'             => 'Odważna tekstura Croco dla kobiet, które nie boją się wyróżniać.{{BR}}Elegancja z pazurem.',
        'cta_text'             => 'SPRAWDŹ MODELE',
        'cta_filters'          => array( 'filter_kolekcja' => 'croco' ),
    ),
    // Baner 3: Portfele Męskie
    2 => array(
        'offset_x'             => 0,
        'text_position'        => 'left-center',
        'text_position_mobile' => 'left-bottom',
        'title'                => 'MĘSKI PUNKT{{BR}}WIDZENIA',
        'subtitle'             => 'Ponadczasowa klasyka dla nowoczesnego dżentelmena. {{BR}}Solidność i styl, który przetrwa lata.',
        'cta_text'             => 'ZOBACZ KOLEKCJĘ',
        'cta_category_slug'    => 'portfele-meskie',
    ),
    // Baner 4: Animal Print
    3 => array(
        'offset_x'             => 0,
        'text_position'        => 'left-center',
        'text_position_mobile' => 'right-bottom',
        'title'                => 'DZIKA{{BR}}ELEGANCJA',
        'subtitle'             => 'Wyjdź poza schematy z kolekcją Animal Print.{{BR}}Dodatki, które stają się centrum Twojej stylizacji.',
        'cta_text'             => 'KUP TERAZ',
        'cta_filters'          => array( 'filter_kolekcja' => 'animals' ),
    ),
    // Baner 5: Różowy portfel z dużym logo (Detale, które lśnią)
    4 => array(
        'offset_x'                  => 0,
        'offset_x_mobile'           => 0,
        'text_position'             => 'left-center',
        'text_position_mobile'      => 'left-bottom',
        'content_offset_x'          => 0,
        'content_offset_y'          => 0,
        'content_offset_x_mobile'   => null,
        'content_offset_y_mobile'  => null,
        'title'                     => 'DETALE,{{BR}}KTÓRE LŚNIĄ',
        'subtitle'                  => 'Wyjątkowe wykończenia i kultowe detale Moretti.{{BR}}Pozwól sobie na odrobinę codziennego blasku.',
        'cta_text'                  => 'LISTA ŻYCZEŃ',
        'cta_url'                   => '',
        'cta_category_slug'         => '',
    ),
);
