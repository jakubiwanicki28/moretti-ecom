<?php
/**
 * Szablon strony głównej (front page).
 * Gdy w Ustawieniach > Czytanie wybrano "Stronę statyczną", WordPress ładuje ten plik
 * zamiast page.php – więc hero ze strzałkami (#moretti-hero-prev / #moretti-hero-next)
 * pochodzi stąd (template-parts/home-hero.php), a nie z bloków/pluginu.
 *
 * @package Moretti
 */

get_header();

$hero_data = moretti_get_home_hero_data();
extract($hero_data, EXTR_SKIP);
include get_template_directory() . '/template-parts/home-hero.php';

while (have_posts()) {
    the_post();
    the_content();
}

get_footer();
