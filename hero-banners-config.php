<?php
/**
 * Konfiguracja banerów hero (strona główna).
 * Indeks = kolejność slajdu (0, 1, 2, ...).
 *
 * offset_x  – przesunięcie zdjęcia w poziomie (px). Plus = w prawo, minus = w lewo. 0 = bez przesunięcia.
 * cta_url   – adres przycisku "Kup teraz". Pusty = strona sklepu.
 *
 * Obowiązujące atrybuty (WooCommerce): Kolekcja, Kolor, Materiał.
 * Slug w URL musi być dokładnie taki jak w panelu (WooCommerce → Atrybuty → Konfiguruj taksonomie).
 *
 * Kolekcja: Animals, Croco, Pióra, Snake  → slug np. animals, croco, piora, snake
 * Materiał: Skóra lakierowana, Skóra matowa, Skóra naturalna → slug np. skora-naturalna
 * Kolor: (użyj filter_color=SLUG)
 *
 * Kategorie: portfele-meskie, portfele-damskie (Portfele męskie / Portfele damskie).
 */
return array(
    // Slajd 0: Kolekcja Animals (slug w WP zwykle lowercase)
    0 => array(
        'offset_x' => 0,
        'cta_url'  => 'https://www.morettifashion.com/sklep/?filter_kolekcja=animals',
    ),
    // Slajd 1: Materiał – Skóra naturalna
    1 => array(
        'offset_x' => 0,
        'cta_url'  => 'https://www.morettifashion.com/sklep/?filter_material=skora-naturalna',
    ),
    // Slajd 2: Kategoria Portfele męskie
    2 => array(
        'offset_x' => 0,
        'cta_url'  => 'https://www.morettifashion.com/product-category/portfele-meskie/',
    ),
    // Slajd 3: Kolekcja Croco
    3 => array(
        'offset_x' => 0,
        'cta_url'  => 'https://www.morettifashion.com/sklep/?filter_kolekcja=croco',
    ),
);
