<?php
/**
 * Konfiguracja banerów hero (strona główna).
 * Indeks = kolejność slajdu (0, 1, 2, ...).
 *
 * offset_x           – przesunięcie zdjęcia w poziomie (px). Plus = w prawo, minus = w lewo. 0 = bez przesunięcia.
 * cta_url            – (opcjonalnie) gotowy adres przycisku. Jeśli podany, reszta jest ignorowana.
 * cta_filters        – (opcjonalnie) tablica: nazwa parametru => slug termu. Buduje URL sklepu z filtrami.
 * cta_category_slug  – (opcjonalnie) slug kategorii product_cat. Buduje link do archiwum kategorii.
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
 *     skora-lakierowana | skora-matowa | skora-naturalna
 *
 *   Kategorie (product_cat), cta_category_slug
 *     portfele-meskie | portfele-damskie
 */
return array(
    0 => array(
        'offset_x'    => 0,
        'cta_filters' => array( 'filter_kolekcja' => 'animals' ),
    ),
    1 => array(
        'offset_x'    => 0,
        'cta_filters' => array( 'filter_material' => 'skora-naturalna' ),
    ),
    2 => array(
        'offset_x'           => 0,
        'cta_category_slug'  => 'portfele-meskie',
    ),
    3 => array(
        'offset_x'    => 0,
        'cta_filters' => array( 'filter_kolekcja' => 'croco' ),
    ),
);
