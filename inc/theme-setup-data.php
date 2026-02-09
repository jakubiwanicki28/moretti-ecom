<?php
/**
 * Auto-Setup Engine for Moretti Theme
 * Recreates products, categories, attributes AND IMAGES.
 */

if (!defined('ABSPATH')) exit;

/**
 * Helper to upload image from theme folder to Media Library
 */
function moretti_sideload_image($filename) {
    if (empty($filename)) return 0;

    // Check if image already exists in media library by name
    global $wpdb;
    $attachment_id = $wpdb->get_var($wpdb->prepare(
        "SELECT post_id FROM $wpdb->postmeta WHERE meta_key = '_wp_attached_file' AND meta_value LIKE %s",
        '%' . $filename . '%'
    ));

    if ($attachment_id) return $attachment_id;

    // Path to image in theme
    $image_path = get_template_directory() . '/images/' . $filename;
    if (!file_exists($image_path)) return 0;

    // Upload logic
    require_once(ABSPATH . 'wp-admin/includes/image.php');
    require_once(ABSPATH . 'wp-admin/includes/file.php');
    require_once(ABSPATH . 'wp-admin/includes/media.php');

    $upload_dir = wp_upload_dir();
    $image_data = file_get_contents($image_path);
    $upload_file = $upload_dir['path'] . '/' . $filename;
    file_put_contents($upload_file, $image_data);

    $wp_filetype = wp_check_filetype($filename, null);
    $attachment = array(
        'post_mime_type' => $wp_filetype['type'],
        'post_title' => sanitize_file_name($filename),
        'post_content' => '',
        'post_status' => 'inherit'
    );

    $attach_id = wp_insert_attachment($attachment, $upload_file);
    $attach_data = wp_generate_attachment_metadata($attach_id, $upload_file);
    wp_update_attachment_metadata($attach_id, $attach_data);

    return $attach_id;
}

function moretti_run_site_setup() {
    if (!class_exists('WooCommerce')) return 'Błąd: WooCommerce nie jest zainstalowany.';

    $output = "🚀 Rozpoczynam głęboką synchronizację sklepu Moretti...<br>";

    // 1. Atrybuty
    $attributes = array(
        'Kolor' => 'color',
        'Materiał' => 'material',
        'Wielkość' => 'wielkosc'
    );

    foreach ($attributes as $name => $slug) {
        if (!taxonomy_exists(wc_attribute_taxonomy_name($slug))) {
            wc_create_attribute(array('name' => $name, 'slug' => $slug, 'type' => 'select', 'has_archives' => true));
            $output .= "✅ Stworzono atrybut: $name<br>";
        }
    }

    // 2. Kategorie i Kolory
    $terms = array(
        'pa_color' => array('Czarny', 'Brązowy', 'Beżowy', 'Czerwony', 'Szary', 'Granatowy'),
        'product_cat' => array('Portfele Męskie', 'Portfele Damskie', 'Etui na karty', 'Akcesoria skórzane')
    );

    foreach ($terms as $tax => $values) {
        foreach ($values as $val) {
            if (!term_exists($val, $tax)) {
                wp_insert_term($val, $tax);
                $output .= "✅ Dodano opcję: $val do $tax<br>";
            }
        }
    }

    // 3. Dane Produktów (zrzut z Twojego lokalu)
    $products_data = array(
        array(
            'title' => 'ZEBRA ANIMALS',
            'cat' => 'Portfele Damskie',
            'price' => '199',
            'image' => 'Front-3-72062-2588-Portfel-Duzy-x-Zebra-Animals-Large.jpeg',
            'gallery' => array(
                'Logo-72062-2588-Portfel-Duzy-x-Zebra-Animals-Large.jpeg',
                'Sesja-AI-Front-x-Animals-Produkt-Large.jpeg',
                'Prawy-Bok-72062-2588-duzy-portfel-x-zebra-Animals-Large.jpeg'
            ),
            'variations' => array(
                array('color' => 'Beżowy', 'img' => 'Front-Pantera-72062-2588-Duzy-portfel-x-pantera-animals-Large.jpeg'),
                array('color' => 'Czarny', 'img' => 'Prawy-Bok-72062-2588-duzy-portfel-x-zebra-Animals-Large.jpeg'),
                array('color' => 'Brązowy', 'img' => 'Front-1-72062-2588-Duzy-Portfel-x-Cow-Animals-Large.jpeg'),
            )
        ),
        array(
            'title' => 'Portfel Męski Classic',
            'cat' => 'Portfele Męskie',
            'price' => '149',
            'image' => 'men-wallets.jpg',
            'variations' => array(
                array('color' => 'Czarny', 'img' => 'men-wallets.jpg'),
                array('color' => 'Brązowy', 'img' => 'men-wallets.jpg'),
                array('color' => 'Granatowy', 'img' => 'men-wallets.jpg'),
            )
        ),
        array(
            'title' => 'Portfel Męski Slim RFID',
            'cat' => 'Portfele Męskie',
            'price' => '129',
            'image' => 'men-wallets.jpg',
            'variations' => array(
                array('color' => 'Czarny', 'img' => 'men-wallets.jpg'),
                array('color' => 'Brązowy', 'img' => 'men-wallets.jpg'),
                array('color' => 'Granatowy', 'img' => 'men-wallets.jpg'),
            )
        ),
        array(
            'title' => 'Portfel Męski Premium Bull',
            'cat' => 'Portfele Męskie',
            'price' => '199',
            'image' => 'men-wallets.jpg',
            'variations' => array(
                array('color' => 'Czarny', 'img' => 'men-wallets.jpg'),
                array('color' => 'Brązowy', 'img' => 'men-wallets.jpg'),
                array('color' => 'Granatowy', 'img' => 'men-wallets.jpg'),
            )
        ),
        array(
            'title' => 'Portfel Damski Elegance',
            'cat' => 'Portfele Damskie',
            'price' => '179',
            'image' => 'woman-holding-wallet.png',
            'variations' => array(
                array('color' => 'Czerwony', 'img' => 'woman-holding-wallet.png'),
                array('color' => 'Beżowy', 'img' => 'woman-holding-wallet.png'),
                array('color' => 'Szary', 'img' => 'woman-holding-wallet.png'),
            )
        ),
        array(
            'title' => 'Portfel Damski Soft Touch',
            'cat' => 'Portfele Damskie',
            'price' => '159',
            'image' => 'woman-holding-wallet.png',
            'variations' => array(
                array('color' => 'Czerwony', 'img' => 'woman-holding-wallet.png'),
                array('color' => 'Beżowy', 'img' => 'woman-holding-wallet.png'),
                array('color' => 'Szary', 'img' => 'woman-holding-wallet.png'),
            )
        )
    );

    foreach ($products_data as $p_data) {
        $existing = get_page_by_path(sanitize_title($p_data['title']), OBJECT, 'product');
        if ($existing) {
            $output .= "ℹ️ Aktualizacja produktu: {$p_data['title']}<br>";
            $product = wc_get_product($existing->ID);
        } else {
            $product = new WC_Product_Variable();
            $product->set_name($p_data['title']);
            $product->set_status('publish');
            $product->set_description('Ekskluzywny produkt z nowej kolekcji Moretti. Wykonany z dbałością o każdy detal.');
        }

        // Set Category
        $cat = get_term_by('name', $p_data['cat'], 'product_cat');
        if ($cat) $product->set_category_ids(array($cat->term_id));

        // Set Main Image
        if (!empty($p_data['image'])) {
            $img_id = moretti_sideload_image($p_data['image']);
            if ($img_id) $product->set_image_id($img_id);
        }

        // Set Gallery
        if (!empty($p_data['gallery'])) {
            $gallery_ids = array();
            foreach ($p_data['gallery'] as $gallery_img) {
                $gid = moretti_sideload_image($gallery_img);
                if ($gid) $gallery_ids[] = $gid;
            }
            $product->set_gallery_image_ids($gallery_ids);
        }

        // Set Attributes (Color)
        $colors_list = array();
        foreach ($p_data['variations'] as $v) $colors_list[] = $v['color'];

        $attribute = new WC_Product_Attribute();
        $attribute->set_id(wc_attribute_taxonomy_id_by_name('color'));
        $attribute->set_name('pa_color');
        $attribute->set_options($colors_list);
        $attribute->set_visible(true);
        $attribute->set_variation(true);
        $product->set_attributes(array($attribute));
        
        $product_id = $product->save();

        // Variations
        foreach ($p_data['variations'] as $v_data) {
            $variation_id = 0;
            $existing_variations = $product->get_children();
            
            // Look for existing variation
            foreach ($existing_variations as $ev_id) {
                $v_obj = wc_get_product($ev_id);
                if ($v_obj->get_attributes()['pa_color'] === sanitize_title($v_data['color'])) {
                    $variation_id = $ev_id;
                    break;
                }
            }

            if ($variation_id) {
                $variation = new WC_Product_Variation($variation_id);
            } else {
                $variation = new WC_Product_Variation();
                $variation->set_parent_id($product_id);
                $variation->set_attributes(array('pa_color' => sanitize_title($v_data['color'])));
            }

            $variation->set_regular_price($p_data['price']);
            $variation->set_manage_stock(true);
            $variation->set_stock_quantity(10);
            $variation->set_status('publish');

            if (!empty($v_data['img'])) {
                $v_img_id = moretti_sideload_image($v_data['img']);
                if ($v_img_id) $variation->set_image_id($v_img_id);
            }

            $variation->save();
        }
        $output .= "📦 Zsynchronizowano: <b>{$p_data['title']}</b> (zdjęcia + warianty)<br>";
    }

    return $output . "<br>✅ <b>Synchronizacja zakończona! Wszystkie zdjęcia wgrane do Media Library.</b>";
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
        <h1>Synchronizacja Sklepu Moretti</h1>
        <p>Ten proces pobierze zdjęcia z plików motywu i wgra je do Media Library, a następnie podepnie pod produkty.</p>
        <form method="post">
            <input type="submit" name="run_moretti_setup" class="button button-primary button-large" value="Uruchom Pełną Synchronizację">
        </form>
        <div style="margin-top: 20px; padding: 15px; background: #fff; border: 1px solid #ccd0d4; max-height: 400px; overflow-y: auto;">
            <?php
            if (isset($_POST['run_moretti_setup'])) {
                echo moretti_run_site_setup();
            } else {
                echo "Gotowy do synchronizacji...";
            }
            ?>
        </div>
    </div>
    <?php
}
