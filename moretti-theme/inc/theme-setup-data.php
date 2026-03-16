<?php
/**
 * Auto-Setup Engine for Moretti Theme
 * Recreates products, categories and attributes.
 */

if (!defined('ABSPATH')) exit;

function moretti_run_site_setup() {
    if (!class_exists('WooCommerce')) return 'Błąd: WooCommerce nie jest zainstalowany.';

    $output = "🚀 Rozpoczynam konfigurację sklepu Moretti...<br>";

    // 1. Atrybuty
    $attributes = array(
        'Kolor' => 'color',
        'Materiał' => 'material',
        'Wielkość' => 'wielkosc'
    );

    foreach ($attributes as $name => $slug) {
        if (!taxonomy_exists(wc_attribute_taxonomy_name($slug))) {
            wc_create_attribute(array('name' => $name, 'slug' => $slug, 'type' => 'select'));
            $output .= "✅ Stworzono atrybut: $name<br>";
        }
    }

    // 2. Termy (Kolory itp)
    $terms = array(
        'pa_color' => array('Czarny', 'Brązowy', 'Beżowy', 'Czerwony', 'Szary', 'Granatowy'),
        'pa_material' => array('Skóra Naturalna'),
        'product_cat' => array('Portfele Męskie', 'Portfele Damskie')
    );

    foreach ($terms as $tax => $values) {
        foreach ($values as $val) {
            if (!term_exists($val, $tax)) {
                wp_insert_term($val, $tax);
                $output .= "✅ Dodano opcję: $val do $tax<br>";
            }
        }
    }

    // 3. Produkty
    $products_to_create = array(
        // Men's
        array('title' => 'Portfel Męski Classic', 'cat' => 'Portfele Męskie', 'colors' => array('Czarny', 'Brązowy', 'Granatowy'), 'price' => 149),
        array('title' => 'Portfel Męski Slim RFID', 'cat' => 'Portfele Męskie', 'colors' => array('Czarny', 'Brązowy', 'Granatowy'), 'price' => 129),
        array('title' => 'Portfel Męski Premium Bull', 'cat' => 'Portfele Męskie', 'colors' => array('Czarny', 'Brązowy', 'Granatowy'), 'price' => 199),
        // Women's
        array('title' => 'Portfel Damski Elegance', 'cat' => 'Portfele Damskie', 'colors' => array('Czerwony', 'Beżowy', 'Szary'), 'price' => 179),
        array('title' => 'Portfel Damski Soft Touch', 'cat' => 'Portfele Damskie', 'colors' => array('Czerwony', 'Beżowy', 'Szary'), 'price' => 159),
        array('title' => 'Portfel Damski Grande', 'cat' => 'Portfele Damskie', 'colors' => array('Czerwony', 'Beżowy', 'Szary'), 'price' => 229),
    );

    foreach ($products_to_create as $p_data) {
        $existing = get_page_by_path(sanitize_title($p_data['title']), OBJECT, 'product');
        if ($existing) {
            $output .= "ℹ️ Produkt już istnieje: {$p_data['title']}<br>";
            continue;
        }

        $product = new WC_Product_Variable();
        $product->set_name($p_data['title']);
        $product->set_status('publish');
        $product->set_description('Ekskluzywny produkt z nowej kolekcji Moretti. Wykonany z dbałością o każdy detal.');
        
        $cat = get_term_by('name', $p_data['cat'], 'product_cat');
        if ($cat) $product->set_category_ids(array($cat->term_id));

        $attribute = new WC_Product_Attribute();
        $attribute->set_id(wc_attribute_taxonomy_id_by_name('color'));
        $attribute->set_name('pa_color');
        $attribute->set_options($p_data['colors']);
        $attribute->set_visible(true);
        $attribute->set_variation(true);
        $product->set_attributes(array($attribute));
        
        $product_id = $product->save();

        foreach ($p_data['colors'] as $color) {
            $variation = new WC_Product_Variation();
            $variation->set_parent_id($product_id);
            $variation->set_attributes(array('pa_color' => sanitize_title($color)));
            $variation->set_regular_price($p_data['price']);
            $variation->set_manage_stock(true);
            $variation->set_stock_quantity(10);
            $variation->set_status('publish');
            $variation->save();
        }
        $output .= "📦 Wygenerowano: <b>{$p_data['title']}</b> (+ warianty)<br>";
    }

    return $output . "<br>✅ <b>Konfiguracja zakończona pomyślnie!</b>";
}

// Add Admin Menu
add_action('admin_menu', function() {
    add_menu_page(
        'Moretti Setup',
        'Moretti Setup',
        'manage_options',
        'moretti-setup',
        'moretti_setup_page_html',
        'dashicons-rest-api',
        2
    );
});

function moretti_setup_page_html() {
    ?>
    <div class="wrap">
        <h1>Konfiguracja Sklepu Moretti</h1>
        <p>Kliknij poniższy przycisk, aby zsynchronizować produkty, kategorie i atrybuty z bazy lokalnej (GitHub).</p>
        <form method="post">
            <input type="submit" name="run_moretti_setup" class="button button-primary button-large" value="Zrekonstruuj Sklep">
        </form>
        <div style="margin-top: 20px; padding: 15px; background: #fff; border: 1px solid #ccd0d4;">
            <?php
            if (isset($_POST['run_moretti_setup'])) {
                echo moretti_run_site_setup();
            } else {
                echo "Czekam na uruchomienie...";
            }
            ?>
        </div>
    </div>
    <?php
}
