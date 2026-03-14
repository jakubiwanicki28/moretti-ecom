<?php
/**
 * Szablon strony głównej (front page).
 * WordPress zawsze ładuje ten plik dla strony głównej (niezależnie od ustawienia Czytanie).
 * – Hero ze strzałkami: template-parts/home-hero.php
 * – "Najnowsze wpisy" → hero + pełna zawartość (NOWOŚCI, carousele itd.)
 * – "Strona statyczna" → hero + treść wybranej strony (the_content).
 *
 * @package Moretti
 */

get_header();

$hero_data = moretti_get_home_hero_data();
extract($hero_data, EXTR_SKIP);
include get_template_directory() . '/template-parts/home-hero.php';

if (get_option('show_on_front') === 'page') {
    while (have_posts()) {
        the_post();
        the_content();
    }
} else {
    include get_template_directory() . '/template-parts/home-content.php';
}

get_footer();
