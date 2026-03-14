<?php
/**
 * Konfiguracja banerów hero (strona główna).
 * Indeks = kolejność slajdu (0, 1, 2, ...).
 *
 * offset_x            – przesunięcie zdjęcia w poziomie (px). Plus = w prawo, minus = w lewo. 0 = bez przesunięcia.
 * text_position       – (opcjonalnie) pozycja bloku tekstu: 'left-top'|'left-center'|'left-bottom'|'center-top'|'center-center'|'center-bottom'|'right-top'|'right-center'|'right-bottom'. Domyślnie 'left-center'.
 * title               – (opcjonalnie) tytuł slajdu (HTML). Pusty = brak tytułu.
 * subtitle            – (opcjonalnie) podtytuł (HTML). Pusty = brak.
 * cta_text            – (opcjonalnie) tekst przycisku. Domyślnie "KUP TERAZ".
 * cta_url             – (opcjonalnie) gotowy adres przycisku. Jeśli podany, reszta jest ignorowana.
 * cta_filters         – (opcjonalnie) tablica: nazwa parametru => slug termu. Buduje URL sklepu z filtrami.
 * cta_category_slug   – (opcjonalnie) slug kategorii product_cat. Buduje link do archiwum kategorii.
 * overlay_desktop     – (opcjonalnie) nazwa pliku obrazka overlay (desktop), np. tekst jako PNG. W images/banners/.
 * overlay_mobile      – (opcjonalnie) nazwa pliku obrazka overlay (mobile). Pusty = ten sam co desktop.
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
    0 => array(
        'offset_x'      => 0,
        'text_position' => 'left-center',
        'title'         => 'MORETTI FASHION<br>ELEGANCJA I&nbsp;STYL',
        'subtitle'      => 'Odkryj naszą wyselekcjonowaną kolekcję portfeli premium.',
        'cta_text'      => 'KUP TERAZ',
        'cta_filters'   => array( 'filter_kolekcja' => 'animals' ),
    ),
    1 => array(
        'offset_x'      => 0,
        'text_position' => 'left-center',
        'title'         => '',
        'subtitle'      => '',
        'cta_text'      => 'KUP TERAZ',
        'cta_filters'   => array( 'filter_material' => 'skora-matowa' ),
    ),
    2 => array(
        'offset_x'           => 0,
        'text_position'      => 'left-center',
        'title'              => '',
        'subtitle'           => '',
        'cta_text'           => 'KUP TERAZ',
        'cta_category_slug'  => 'portfele-meskie',
    ),
    3 => array(
        'offset_x'      => 0,
        'text_position' => 'left-center',
        'title'         => '',
        'subtitle'      => '',
        'cta_text'      => 'KUP TERAZ',
        'cta_filters'   => array( 'filter_kolekcja' => 'croco' ),
    ),
);
