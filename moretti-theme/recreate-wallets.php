<?php
require_once('/var/www/html/wp-load.php');

if (!class_exists('WooCommerce')) {
    die('WooCommerce nie jest zainstalowany.');
}

echo "Rozpoczynam transformację w sklep z portfelami...\n";

// 1. Usuwamy stare produkty
$old_products = get_posts(array('post_type' => 'product', 'numberposts' => -1));
foreach ($old_products as $p) {
    wp_delete_post($p->ID, true);
}
echo "Usunięto stare produkty.\n";

// 2. Kategorie portfeli
$categories = array(
    'Portfele Męskie' => 'Klasyczne i nowoczesne portfele dla mężczyzn, wykonane z najwyższej jakości skóry.',
    'Portfele Damskie' => 'Eleganckie portfele damskie, łączące styl z funkcjonalnością.',
    'Portfele Slim' => 'Minimalistyczne portfele typu slim, idealne do przedniej kieszeni.',
    'Etui na karty' => 'Małe i poręczne etui na karty płatnicze i dokumenty.',
    'Akcesoria skórzane' => 'Breloki, paski i inne akcesoria z naturalnej skóry.'
);

$cat_ids = array();
foreach ($categories as $name => $desc) {
    $term = get_term_by('name', $name, 'product_cat');
    if (!$term) {
        $term = wp_insert_term($name, 'product_cat', array('description' => $desc));
        $cat_ids[$name] = $term['term_id'];
    } else {
        $cat_ids[$name] = $term->term_id;
    }
}

// 3. Produkty (Portfele)
$wallets = array(
    array(
        'name' => 'Portfel Męski Klasyczny Czarny',
        'price' => '149',
        'cat' => 'Portfele Męskie',
        'desc' => 'Klasyczny portfel męski wykonany z licowej skóry bydlęcej. Posiada ochronę RFID, miejsce na 8 kart oraz dużą kieszeń na bilon.',
        'colors' => array('Czarny', 'Brązowy')
    ),
    array(
        'name' => 'Slim Wallet Carbon Edition',
        'price' => '129',
        'cat' => 'Portfele Slim',
        'desc' => 'Nowoczesny portfel typu slim z wykończeniem o teksturze włókna węglowego. Mieści do 6 kart i posiada klips na banknoty.',
        'colors' => array('Czarny', 'Grafitowy')
    ),
    array(
        'name' => 'Elegance Red - Portfel Damski',
        'price' => '199',
        'cat' => 'Portfele Damskie',
        'desc' => 'Duży, elegancki portfel damski zapinany na zamek. Wykonany z lakierowanej skóry w kolorze głębokiej czerwieni.',
        'colors' => array('Czerwony', 'Bordowy', 'Czarny')
    ),
    array(
        'name' => 'Vintage Brown Card Holder',
        'price' => '79',
        'cat' => 'Etui na karty',
        'desc' => 'Minimalistyczne etui na karty wykonane z ręcznie przecieranej skóry typu vintage. Idealne dla fanów stylu retro.',
        'colors' => array('Brązowy', 'Kononiakowy')
    ),
    array(
        'name' => 'Portfel Męski z Grawerem - Cognac',
        'price' => '169',
        'cat' => 'Portfele Męskie',
        'desc' => 'Stylowy portfel w kolorze koniakowym. Skóra garbowana roślinnie, która z czasem nabiera pięknej patyny.',
        'colors' => array('Koniakowy', 'Czekoladowy')
    ),
    array(
        'name' => 'Minimalist Bifold Grey',
        'price' => '139',
        'cat' => 'Portfele Slim',
        'desc' => 'Ultra cienki portfel typu bifold. Brak podszewki sprawia, że jest niesamowicie płaski nawet po wypełnieniu.',
        'colors' => array('Szary', 'Granatowy')
    ),
    array(
        'name' => 'Portfel Damski Pastel Mint',
        'price' => '179',
        'cat' => 'Portfele Damskie',
        'desc' => 'Subtelny portfel w pastelowym odcieniu mięty. Wykonany z miękkiej skóry nappa.',
        'colors' => array('Miętowy', 'Pudrowy Róż')
    ),
    array(
        'name' => 'Brelok Skórzany Premium',
        'price' => '49',
        'cat' => 'Akcesoria skórzane',
        'desc' => 'Solidny brelok do kluczy wykonany ze skrawków skóry premium używanej do produkcji naszych portfeli.',
        'colors' => array('Czarny', 'Brązowy', 'Koniakowy')
    )
);

foreach ($wallets as $data) {
    $product = new WC_Product_Variable();
    $product->set_name($data['name']);
    $product->set_description($data['desc']);
    $product->set_short_description('Skóra naturalna, ochrona RFID, polska marka.');
    $product->set_status('publish');
    $product->set_category_ids(array($cat_ids[$data['cat']]));
    
    $product_id = $product->save();
    
    // Atrybut Kolor
    $attribute = new WC_Product_Attribute();
    $attribute->set_id(0);
    $attribute->set_name('Kolor');
    $attribute->set_options($data['colors']);
    $attribute->set_visible(true);
    $attribute->set_variation(true);
    $product->set_attributes(array($attribute));
    $product->save();
    
    foreach ($data['colors'] as $color) {
        $variation = new WC_Product_Variation();
        $variation->set_parent_id($product_id);
        $variation->set_attributes(array('kolor' => $color));
        $variation->set_regular_price($data['price']);
        $variation->set_price($data['price']);
        $variation->set_stock_status('instock');
        $variation->save();
    }
    
    echo "Dodano: " . $data['name'] . "\n";
}

// 4. Czyścimy flagi
delete_option('moretti_sample_products_created');
update_option('moretti_sample_products_created', true);

echo "\nGotowe! Sklep został zaktualizowany pod portfele.\n";
