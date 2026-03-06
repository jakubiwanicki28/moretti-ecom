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

// Content copied/adapted from Portfelland legal pages for Moretti brand.
$pages_to_create = array(
    'regulamin-sklepu' => array(
        'title' => 'Regulamin sklepu',
        'content' => '
        <h1>Regulamin sklepu internetowego Moretti</h1>
        <ol>
            <li>Ogłoszenia, reklamy, cenniki i inne informacje o produktach podane na stronie sklepu internetowego mają charakter informacyjny i stanowią zaproszenie do zawarcia umowy (art. 71 Kodeksu cywilnego).</li>
            <li>Sprzedaż prowadzona jest przez: <strong>LIDA DARIUSZ CAŁA</strong>, NIP: <strong>5261119292</strong>, REGON: <strong>015161906</strong>, adres: Nadrzeczna 14, GD Hala 5, Box A-07, 05-552 Wólka Kosowska.</li>
            <li>Prezentacja towaru oraz jego ceny nie oznacza gwarancji dostępności towaru ani możliwości realizacji zamówienia w każdym przypadku.</li>
            <li>Minimalna wartość zamówienia hurtowego wynosi <strong>1200 zł netto</strong>.</li>
            <li>W przypadku zakupu detalicznego konsument, który zawarł umowę na odległość lub poza lokalem przedsiębiorstwa, może w terminie 14 dni odstąpić od umowy bez podawania przyczyny.</li>
        </ol>

        <h2>Dane osobowe</h2>
        <ol>
            <li>Dane osobowe przetwarzane są zgodnie z obowiązującymi przepisami prawa, w szczególności zgodnie z RODO (Rozporządzenie Parlamentu Europejskiego i Rady (UE) 2016/679).</li>
            <li>Administrator zapewnia, że dane są przetwarzane zgodnie z prawem, rzetelnie i w sposób przejrzysty, wyłącznie w określonych celach i przez okres niezbędny do ich realizacji.</li>
            <li>Osobom, których dane dotyczą, przysługuje prawo do: informacji, sprostowania, usunięcia, ograniczenia przetwarzania, sprzeciwu, wycofania zgody oraz wniesienia skargi do organu nadzorczego.</li>
        </ol>

        <h2>Własność intelektualna</h2>
        <ol>
            <li>Prawa do serwisu oraz treści w nim zawartych należą do Sprzedawcy lub uprawnionych podmiotów trzecich.</li>
            <li>Logotypy, nazwy, treści, grafiki, elementy kodu i układu strony są chronione przepisami prawa i nie mogą być kopiowane ani rozpowszechniane bez zgody właściciela praw.</li>
        </ol>

        <h2>Postanowienia końcowe</h2>
        <ol>
            <li>W sprawach nieuregulowanych niniejszym regulaminem zastosowanie mają przepisy prawa polskiego.</li>
            <li>Sprzedawca zastrzega sobie prawo do zmiany regulaminu. Do umów zawartych przed zmianą stosuje się regulamin obowiązujący w chwili złożenia zamówienia.</li>
            <li>Wszelkie odstępstwa od regulaminu wymagają formy pisemnej pod rygorem nieważności.</li>
            <li>Sądem właściwym do rozstrzygania sporów jest sąd właściwy według siedziby Sprzedawcy, z zastrzeżeniem bezwzględnie obowiązujących przepisów chroniących konsumentów.</li>
        </ol>'
    ),
    'polityka-prywatnosci' => array(
        'title' => 'Polityka prywatności',
        'content' => '
        <h1>Polityka prywatności</h1>
        <p>Strony internetowe Moretti wykorzystują pliki cookies oraz technologie działające w analogiczny sposób. Pliki cookies zapisywane są przez przeglądarkę na urządzeniu końcowym użytkownika (np. komputer, smartfon) i są niezbędne do prawidłowego funkcjonowania serwisu.</p>

        <h2>Cele wykorzystywania plików cookies</h2>
        <ul>
            <li>utrzymanie sesji użytkownika po zalogowaniu,</li>
            <li>dostosowanie i optymalizacja serwisu do potrzeb użytkowników,</li>
            <li>tworzenie statystyk oglądalności podstron,</li>
            <li>personalizacja treści marketingowych,</li>
            <li>zapewnienie bezpieczeństwa i niezawodności działania serwisu.</li>
        </ul>

        <h2>Rodzaje plików cookies</h2>
        <ul>
            <li><strong>Sesyjne</strong> – przechowywane do momentu zamknięcia przeglądarki lub zakończenia sesji.</li>
            <li><strong>Stałe</strong> – przechowywane przez określony czas lub do momentu usunięcia przez użytkownika.</li>
        </ul>

        <h2>Okres przechowywania danych</h2>
        <p>Dane przetwarzane z wykorzystaniem plików cookies przechowywane są przez okres niezbędny do realizacji celów, dla których zostały zebrane, nie dłużej niż 360 dni, chyba że przepisy prawa wymagają dłuższego okresu.</p>

        <h2>Prawa użytkownika</h2>
        <p>Użytkownik może zarządzać plikami cookies poprzez ustawienia swojej przeglądarki. Ograniczenie stosowania cookies może wpłynąć na niektóre funkcjonalności serwisu.</p>

        <h2>Administrator danych</h2>
        <p>Administratorem danych jest <strong>LIDA DARIUSZ CAŁA</strong>, NIP: <strong>5261119292</strong>, REGON: <strong>015161906</strong>.</p>
        <p>W razie pytań dotyczących prywatności prosimy o kontakt: <a href="mailto:hurtportfelland@gmail.com">hurtportfelland@gmail.com</a>, tel. <a href="tel:+48725538100">+48 725 538 100</a>.</p>'
    ),
    'polityka-plikow-cookies' => array(
        'title' => 'Polityka Plików Cookies',
        'content' => '<h1>Polityka Plików Cookies</h1><p>Lorem ipsum dolor sit amet, consectetur adipiscing elit. Treść polityki cookies do uzupełnienia przez klienta.</p>'
    ),
    'dostawa-i-platnosci' => array(
        'title' => 'Dostawa i płatności',
        'content' => '
        <h1>Dostawa i płatności</h1>
        <ol>
            <li>Wszystkie ceny towarów podawane w sklepie są podawane w wartościach netto i brutto w złotych polskich (zawierają podatek VAT). Cena towaru podana w chwili złożenia zamówienia jest wiążąca dla obu stron.</li>
            <li>Koszty związane z dostawą towaru (np. transport, doręczenie, usługi pocztowe) ponosi Klient, o ile wyraźnie nie wskazano inaczej. Informacja o kosztach dostawy przekazywana jest na etapie składania zamówienia.</li>
            <li>Klient może wybrać formę płatności:
                <ul>
                    <li>przedpłata (przelew/płatność online) – realizacja po zaksięgowaniu wpłaty,</li>
                    <li>za pobraniem – płatność przy odbiorze towaru,</li>
                    <li>płatność przy odbiorze osobistym (gotówka lub karta), jeśli dostępny jest odbiór osobisty.</li>
                </ul>
            </li>
            <li>Na każdy sprzedany produkt sklep wystawia dowód zakupu i doręcza go Klientowi.</li>
            <li>Klient zobowiązany jest do zapłaty w terminie 7 dni od dnia zawarcia umowy sprzedaży, o ile wybrany sposób płatności nie wymaga innego terminu.</li>
        </ol>'
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
        wp_update_post(array(
            'ID' => $existing->ID,
            'post_title' => $page_data['title'],
            'post_content' => $page_data['content'],
            'post_status' => 'publish',
        ));
        echo '<p class="success">✅ Zaktualizowano stronę: "' . $page_data['title'] . '" (/' . $slug . ')</p>';
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

// Backward compatibility: if old "koszty-dostawy" page exists, keep it as redirect stub.
$legacy_delivery_page = get_page_by_path('koszty-dostawy');
if ($legacy_delivery_page) {
    wp_update_post(array(
        'ID' => $legacy_delivery_page->ID,
        'post_title' => 'Koszty dostawy i metody płatności',
        'post_content' => '<p>Ta treść została przeniesiona do strony <a href="' . esc_url(home_url('/dostawa-i-platnosci/')) . '">Dostawa i płatności</a>.</p>',
        'post_status' => 'publish',
    ));
    echo '<p class="info">ℹ️ Zaktualizowano starą stronę /koszty-dostawy jako przekierowanie treści.</p>';
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
