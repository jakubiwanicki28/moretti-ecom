<?php
/**
 * Main template file - Homepage Redesign (STYNRA Style)
 * 
 * @package Moretti
 */

get_header();

$hero_data = moretti_get_home_hero_data();
extract($hero_data, EXTR_SKIP);
include get_template_directory() . '/template-parts/home-hero.php';

include get_template_directory() . '/template-parts/home-content.php';

get_footer();
