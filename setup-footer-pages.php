<?php
/**
 * Setup Footer Pages - Create missing pages and hide Kontakt
 * 
 * Run this file once by visiting: http://your-domain.com/wp-content/themes/moretti-theme/setup-footer-pages.php
 * Or run via WP-CLI: wp eval-file setup-footer-pages.php
 */

require_once('../../../wp-load.php');

// Simplified security for local dev
if (strpos($_SERVER['REMOTE_ADDR'], '127.0.0.1') === false && strpos($_SERVER['REMOTE_ADDR'], '::1') === false && !current_user_can('manage_options')) {
    die('❌ You must be logged in as administrator to run this script (or be on localhost).');
}

echo '<h1>🔧 Moretti Footer Pages Setup</h1>';
echo '<style>body{font-family:monospace;padding:40px;background:#f5f5f5;} .success{color:green;} .error{color:red;} .info{color:blue;}</style>';

// 1. UKRYJ STRONĘ KONTAKT (zmień na draft)
echo '<h2>1. Ukrywanie strony Kontakt</h2>';
$kontakt = get_page_by_path('kontakt');
if ($kontakt) {
    wp_update_post(array(
        'ID' => $kontakt->ID,
        'post_status' => 'draft'
    ));
    echo '<p class="success">✅ Strona "Kontakt" ukryta (draft)</p>';
} else {
    echo '<p class="info">ℹ️ Strona "Kontakt" nie istnieje</p>';
}

// 2. TWORZENIE NOWYCH STRON
echo '<h2>2. Tworzenie nowych stron</h2>';

$pages_to_create = array(
    'regulamin-sklepu' => array(
        'title' => 'Regulamin sklepu',
        'content' => '<h1>Regulamin sklepu</h1><p>Lorem ipsum dolor sit amet, consectetur adipiscing elit. Treść regulaminu do uzupełnienia przez klienta.</p>'
    ),
    'polityka-prywatnosci' => array(
        'title' => 'Polityka prywatności',
        'content' => '<h1>Polityka prywatności</h1><p>Lorem ipsum dolor sit amet, consectetur adipiscing elit. Treść polityki prywatności do uzupełnienia przez klienta.</p>'
    ),
    'polityka-plikow-cookies' => array(
        'title' => 'Polityka Plików Cookies',
        'content' => '<h1>Polityka Plików Cookies</h1><p>Lorem ipsum dolor sit amet, consectetur adipiscing elit. Treść polityki cookies do uzupełnienia przez klienta.</p>'
    ),
    'koszty-dostawy' => array(
        'title' => 'Koszty dostawy i metody płatności',
        'content' => '<h1>Koszty dostawy i metody płatności</h1>
        <h2>Koszty dostawy</h2>
        <ul>
            <li>Kurier InPost: 15 zł</li>
            <li>Darmowa dostawa powyżej 250 zł</li>
        </ul>
        <h2>Metody płatności</h2>
        <ul>
            <li>Przelew bankowy</li>
            <li>Płatność online (PayU, Przelewy24)</li>
            <li>BLIK</li>
        </ul>'
    ),
    'zwroty' => array(
        'title' => 'Zwroty',
        'content' => '<h1>Zwroty</h1>
        <p>Masz 30 dni na bezpłatny zwrot towaru.</p>
        <h2>Jak dokonać zwrotu?</h2>
        <ol>
            <li>Skontaktuj się z nami mailowo</li>
            <li>Wyślij produkt na adres zwrotów</li>
            <li>Otrzymasz zwrot pieniędzy w ciągu 14 dni</li>
        </ol>'
    ),
    'reklamacje' => array(
        'title' => 'Reklamacje',
        'content' => '<h1>Reklamacje</h1>
        <p>Każdy produkt Moretti objęty jest 2-letnią gwarancją.</p>
        <h2>Jak zgłosić reklamację?</h2>
        <ol>
            <li>Wyślij zdjęcia wady na email@moretti.pl</li>
            <li>Odeślij produkt na adres naszej pracowni</li>
            <li>Reklamacja zostanie rozpatrzona w ciągu 14 dni</li>
        </ol>'
    )
);

foreach ($pages_to_create as $slug => $page_data) {
    $existing = get_page_by_path($slug);
    
    if ($existing) {
        echo '<p class="info">ℹ️ Strona "' . $page_data['title'] . '" już istnieje</p>';
    } else {
        $page_id = wp_insert_post(array(
            'post_title' => $page_data['title'],
            'post_name' => $slug,
            'post_content' => $page_data['content'],
            'post_status' => 'publish',
            'post_type' => 'page',
            'post_author' => 1
        ));
        
        if ($page_id) {
            echo '<p class="success">✅ Utworzono stronę: "' . $page_data['title'] . '" (/' . $slug . ')</p>';
        } else {
            echo '<p class="error">❌ Błąd przy tworzeniu: "' . $page_data['title'] . '"</p>';
        }
    }
}

echo '<h2>3. Podsumowanie</h2>';
echo '<p class="success">✅ Konfiguracja zakończona!</p>';
echo '<p>Możesz teraz:</p>';
echo '<ul>';
echo '<li>Edytować treści stron w WordPress Admin → Strony</li>';
echo '<li>Zaktualizować dane kontaktowe w footer.php (adres email, numer WhatsApp)</li>';
echo '<li>Zaktualizować lorem ipsum w sekcji "O NAS" w stopce</li>';
echo '</ul>';

echo '<hr>';
echo '<h3>📄 Lista wszystkich stron:</h3>';
$all_pages = get_pages(array('post_status' => array('publish', 'draft')));
echo '<table border="1" cellpadding="10">';
echo '<tr><th>Tytuł</th><th>Slug</th><th>Status</th><th>Link</th></tr>';
foreach ($all_pages as $page) {
    echo '<tr>';
    echo '<td>' . $page->post_title . '</td>';
    echo '<td>/' . $page->post_name . '</td>';
    echo '<td>' . $page->post_status . '</td>';
    echo '<td><a href="' . get_permalink($page->ID) . '" target="_blank">Zobacz</a></td>';
    echo '</tr>';
}
echo '</table>';
