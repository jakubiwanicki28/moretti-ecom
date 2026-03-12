<?php
/**
 * Konfiguracja banerów hero (strona główna).
 * Indeks = kolejność slajdu (0, 1, 2, ...).
 *
 * offset_x  – przesunięcie zdjęcia w poziomie (px). Plus = w prawo, minus = w lewo. 0 = bez przesunięcia.
 * cta_url   – adres przycisku "Kup teraz". Pusty = strona sklepu.
 *
 * Linki:
 * - Kolekcja (atrybut): https://morettifashion.com/sklep/?filter_kolekcja=SLUG (np. animos, croco)
 * - Materiał: https://morettifashion.com/sklep/?filter_material=SLUG (np. skora-naturalna)
 * - Kategoria (męskie): https://morettifashion.com/product-category/portfele-meskie/
 */
return array(
    0 => array(
        'offset_x' => 0,
        'cta_url'  => 'https://morettifashion.com/sklep/?filter_kolekcja=animos',
    ),
    1 => array(
        'offset_x' => 0,
        'cta_url'  => 'https://morettifashion.com/sklep/?filter_material=skora-naturalna',
    ),
    2 => array(
        'offset_x' => 0,
        'cta_url'  => 'https://morettifashion.com/product-category/portfele-meskie/',
    ),
    3 => array(
        'offset_x' => 0,
        'cta_url'  => 'https://morettifashion.com/sklep/?filter_kolekcja=croco',
    ),
);
