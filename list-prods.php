<?php
define('WP_USE_THEMES', false);
require_once('../../../wp-load.php');
if (!class_exists('WooCommerce')) exit;
$products = wc_get_products(['limit' => -1]);
foreach ($products as $p) {
    echo $p->get_name() . " | ID: " . $p->get_id() . "\n";
}
