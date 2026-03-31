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
<h2>Postanowienia ogólne</h2>
<ol>
    <li>Sklep internetowy dostępny pod adresem morettifashion.com prowadzony jest przez <strong>LIDA Dariusz Cała</strong>, NIP: <strong>5261119292</strong>, REGON: <strong>015161906</strong>, adres: Nadrzeczna 14, GD Hala 5, Box A-07, 05-552 Wólka Kosowska.</li>
    <li>Kontakt ze Sprzedawcą: <a href="mailto:kontakt@morettifashion.com">kontakt@morettifashion.com</a>, tel. <a href="tel:+48725538100">+48 725 538 100</a>.</li>
    <li>Ogłoszenia, opisy produktów i ceny widoczne w Sklepie stanowią zaproszenie do zawarcia umowy w rozumieniu art. 71 Kodeksu cywilnego, a nie ofertę w rozumieniu art. 66 Kodeksu cywilnego.</li>
    <li>Do korzystania ze Sklepu wymagane jest urządzenie z dostępem do Internetu i aktualną przeglądarką internetową.</li>
</ol>

<h2>Składanie zamówień</h2>
<ol>
    <li>Zamówienia można składać przez całą dobę, 7 dni w tygodniu, za pośrednictwem strony internetowej.</li>
    <li>Warunkiem złożenia zamówienia jest zaakceptowanie niniejszego Regulaminu.</li>
    <li>Po złożeniu zamówienia Klient otrzymuje potwierdzenie jego przyjęcia na podany adres e-mail. Umowa sprzedaży zostaje zawarta z chwilą potwierdzenia zamówienia przez Sprzedawcę.</li>
    <li>Sprzedawca zastrzega sobie prawo do odmowy realizacji zamówienia w przypadku braku dostępności towaru lub podania przez Klienta nieprawdziwych danych.</li>
</ol>

<h2>Ceny i płatności</h2>
<ol>
    <li>Wszystkie ceny podane w Sklepie są cenami brutto w złotych polskich (PLN) i zawierają podatek VAT.</li>
    <li>Cena podana przy produkcie w chwili złożenia zamówienia jest wiążąca dla obu stron.</li>
    <li>Dostępne formy płatności:
        <ul>
            <li>płatność online (karta płatnicza, BLIK, przelew ekspresowy),</li>
            <li>przelew tradycyjny na rachunek bankowy Sprzedawcy,</li>
            <li>płatność za pobraniem przy odbiorze przesyłki.</li>
        </ul>
    </li>
    <li>W przypadku przelewu tradycyjnego Klient zobowiązany jest do dokonania zapłaty w terminie 3 dni roboczych od złożenia zamówienia.</li>
</ol>

<h2>Dostawa</h2>
<ol>
    <li>Dostawa realizowana jest na terenie Polski za pośrednictwem kuriera lub Poczty Polskiej.</li>
    <li>Koszty dostawy są podawane na etapie składania zamówienia i zależą od wybranej metody dostawy.</li>
    <li>Czas realizacji zamówienia wynosi od 1 do 5 dni roboczych od momentu zaksięgowania płatności (lub od złożenia zamówienia w przypadku płatności za pobraniem).</li>
    <li>Sprzedawca nie ponosi odpowiedzialności za opóźnienia w dostawie wynikające z przyczyn leżących po stronie przewoźnika.</li>
    <li>Przy odbiorze przesyłki Klient powinien sprawdzić jej stan. W przypadku stwierdzenia uszkodzeń należy sporządzić protokół szkody w obecności kuriera.</li>
</ol>

<h2>Prawo odstąpienia od umowy</h2>
<ol>
    <li>Konsument ma prawo odstąpić od umowy zawartej na odległość bez podawania przyczyny w terminie <strong>14 dni</strong> od dnia otrzymania towaru.</li>
    <li>Oświadczenie o odstąpieniu należy złożyć drogą e-mailową na adres <a href="mailto:kontakt@morettifashion.com">kontakt@morettifashion.com</a> lub listownie na adres Sprzedawcy przed upływem wskazanego terminu.</li>
    <li>Zwracany towar należy odesłać na adres Sprzedawcy w ciągu 14 dni od złożenia oświadczenia o odstąpieniu.</li>
    <li>Sprzedawca zwróci wszystkie otrzymane od Konsumenta płatności, w tym koszty dostarczenia towaru (z wyjątkiem dodatkowych kosztów wynikających z wybranego przez Konsumenta sposobu dostawy innego niż najtańszy zwykły sposób), niezwłocznie, a w każdym przypadku nie później niż w terminie 14 dni od dnia otrzymania zwróconego towaru.</li>
    <li>Zwrot płatności dokonywany jest przy użyciu takich samych sposobów płatności, jakie zostały przez Konsumenta użyte przy pierwotnej transakcji, chyba że Konsument wyraził zgodę na inne rozwiązanie.</li>
    <li>Prawo odstąpienia nie przysługuje w odniesieniu do towarów wykonanych na indywidualne zamówienie Klienta.</li>
</ol>

<h2>Reklamacje</h2>
<ol>
    <li>Sprzedawca odpowiada za wady towaru na podstawie przepisów o rękojmi zawartych w Kodeksie cywilnym.</li>
    <li>Reklamację należy zgłosić na adres e-mail <a href="mailto:kontakt@morettifashion.com">kontakt@morettifashion.com</a>, opisując stwierdzoną wadę i dołączając zdjęcia.</li>
    <li>Sprzedawca rozpatrzy reklamację w terminie 14 dni kalendarzowych od jej otrzymania i poinformuje Klienta o sposobie jej rozpatrzenia.</li>
</ol>

<h2>Dane osobowe</h2>
<ol>
    <li>Administratorem danych osobowych Klientów jest <strong>LIDA Dariusz Cała</strong>, NIP: 5261119292.</li>
    <li>Dane osobowe przetwarzane są zgodnie z RODO (Rozporządzenie Parlamentu Europejskiego i Rady (UE) 2016/679) wyłącznie w celu realizacji zamówień i obsługi Klientów.</li>
    <li>Szczegółowe informacje dotyczące przetwarzania danych osobowych znajdują się w <a href="/polityka-prywatnosci/">Polityce prywatności</a>.</li>
</ol>

<h2>Postanowienia końcowe</h2>
<ol>
    <li>W sprawach nieuregulowanych niniejszym Regulaminem zastosowanie mają przepisy prawa polskiego, w szczególności Kodeksu cywilnego oraz ustawy o prawach konsumenta.</li>
    <li>Sprzedawca zastrzega sobie prawo do zmiany Regulaminu. Do umów zawartych przed zmianą stosuje się Regulamin obowiązujący w chwili złożenia zamówienia.</li>
    <li>Konsument ma możliwość skorzystania z pozasądowego sposobu rozpatrywania reklamacji i dochodzenia roszczeń przed Stałym Polubownym Sądem Konsumenckim. Informacje dostępne na stronie <a href="https://www.uokik.gov.pl" target="_blank" rel="noopener">www.uokik.gov.pl</a>.</li>
    <li>Sądem właściwym do rozstrzygania sporów ze Sprzedawcą jest sąd właściwy według siedziby Sprzedawcy, z zastrzeżeniem bezwzględnie obowiązujących przepisów chroniących konsumentów.</li>
</ol>'
    ),
    'polityka-prywatnosci' => array(
        'title' => 'Polityka prywatności',
        'content' => '
<h2>Administrator danych osobowych</h2>
<p>Administratorem Twoich danych osobowych jest <strong>LIDA Dariusz Cała</strong>, prowadzący działalność pod marką <strong>Moretti Fashion</strong>, NIP: <strong>5261119292</strong>, REGON: <strong>015161906</strong>, adres: Nadrzeczna 14, GD Hala 5, Box A-07, 05-552 Wólka Kosowska.</p>
<p>Kontakt w sprawach dotyczących danych osobowych: <a href="mailto:kontakt@morettifashion.com">kontakt@morettifashion.com</a>, tel. <a href="tel:+48725538100">+48 725 538 100</a>.</p>

<h2>Podstawy i cele przetwarzania danych</h2>
<p>Twoje dane osobowe przetwarzamy w następujących celach:</p>
<ul>
    <li><strong>Realizacja zamówień</strong> (art. 6 ust. 1 lit. b RODO) – imię, nazwisko, adres dostawy, adres e-mail, numer telefonu. Dane przechowywane są przez czas niezbędny do realizacji umowy i obsługi ewentualnych reklamacji lub zwrotów.</li>
    <li><strong>Wypełnienie obowiązków prawnych</strong> (art. 6 ust. 1 lit. c RODO) – dane niezbędne do wystawienia faktur i dokumentacji księgowej przechowywane są przez 5 lat od końca roku podatkowego.</li>
    <li><strong>Prawnie uzasadniony interes Administratora</strong> (art. 6 ust. 1 lit. f RODO) – dochodzenie i obrona roszczeń przez okres przedawnienia roszczeń wynikający z przepisów prawa.</li>
    <li><strong>Marketing bezpośredni</strong> (na podstawie zgody, art. 6 ust. 1 lit. a RODO) – wysyłka newslettera i informacji handlowych, jeśli wyraziłeś na to zgodę. Zgodę możesz wycofać w dowolnym momencie.</li>
</ul>

<h2>Odbiorcy danych</h2>
<p>Twoje dane mogą być przekazywane wyłącznie podmiotom, które współpracują z nami przy realizacji usług:</p>
<ul>
    <li>firmom kurierskim i Poczcie Polskiej (w celu dostarczenia zamówienia),</li>
    <li>operatorom płatności elektronicznych (w celu obsługi transakcji),</li>
    <li>dostawcom oprogramowania sklepu i usług hostingowych,</li>
    <li>kancelariom prawnym i podmiotom księgowym (w zakresie niezbędnym do świadczenia usług).</li>
</ul>
<p>Nie sprzedajemy Twoich danych osobowych podmiotom trzecim.</p>

<h2>Twoje prawa</h2>
<p>Na podstawie RODO przysługują Ci następujące prawa:</p>
<ul>
    <li><strong>Prawo dostępu</strong> – możesz zażądać informacji o tym, jakie dane przetwarzamy.</li>
    <li><strong>Prawo do sprostowania</strong> – możesz poprosić o poprawienie nieprawidłowych danych.</li>
    <li><strong>Prawo do usunięcia</strong> – możesz zażądać usunięcia danych, gdy nie są już potrzebne do celów, w których zostały zebrane.</li>
    <li><strong>Prawo do ograniczenia przetwarzania</strong> – możesz zażądać ograniczenia przetwarzania danych w określonych sytuacjach.</li>
    <li><strong>Prawo do przenoszenia danych</strong> – możesz otrzymać swoje dane w ustrukturyzowanym formacie.</li>
    <li><strong>Prawo do sprzeciwu</strong> – możesz sprzeciwić się przetwarzaniu opartemu na prawnie uzasadnionym interesie.</li>
    <li><strong>Prawo do wycofania zgody</strong> – jeśli przetwarzanie opiera się na zgodzie, możesz ją wycofać w dowolnym momencie bez wpływu na zgodność z prawem przetwarzania dokonanego przed jej wycofaniem.</li>
</ul>
<p>Aby skorzystać z powyższych praw, skontaktuj się z nami pod adresem <a href="mailto:kontakt@morettifashion.com">kontakt@morettifashion.com</a>. Masz również prawo wniesienia skargi do Prezesa Urzędu Ochrony Danych Osobowych (PUODO), ul. Stawki 2, 00-193 Warszawa.</p>

<h2>Pliki cookies</h2>
<p>Sklep morettifashion.com używa plików cookies (ciasteczek) – małych plików tekstowych zapisywanych na Twoim urządzeniu przez przeglądarkę internetową. Cookies wykorzystujemy do:</p>
<ul>
    <li>utrzymania sesji zalogowanego użytkownika,</li>
    <li>zapamiętania zawartości koszyka zakupowego,</li>
    <li>analizy ruchu na stronie (statystyki, np. Google Analytics),</li>
    <li>personalizacji treści i reklam.</li>
</ul>
<p>Możesz zarządzać plikami cookies poprzez ustawienia swojej przeglądarki. Ograniczenie cookies może wpłynąć na działanie niektórych funkcji sklepu (np. koszyk zakupowy). Szczegółowe informacje znajdziesz w <a href="/polityka-plikow-cookies/">Polityce plików cookies</a>.</p>

<h2>Bezpieczeństwo danych</h2>
<p>Stosujemy odpowiednie środki techniczne i organizacyjne, aby chronić Twoje dane przed nieuprawnionym dostępem, utratą lub zniszczeniem. Transmisja danych odbywa się z użyciem protokołu szyfrowania SSL.</p>

<h2>Zmiany polityki prywatności</h2>
<p>Zastrzegamy sobie prawo do zmiany niniejszej Polityki prywatności. O wszelkich istotnych zmianach poinformujemy na stronie sklepu. Aktualna wersja jest zawsze dostępna pod adresem morettifashion.com/polityka-prywatnosci/.</p>'
    ),
    'polityka-plikow-cookies' => array(
        'title' => 'Polityka Plików Cookies',
        'content' => '
<h2>Czym są pliki cookies?</h2>
<p>Pliki cookies (ciasteczka) to małe pliki tekstowe zapisywane na Twoim urządzeniu (komputer, tablet, smartfon) przez przeglądarkę internetową podczas odwiedzania stron internetowych. Pliki cookies nie pobierają żadnych danych osobowych ani informacji poufnych z Twojego urządzenia.</p>

<h2>Jakich cookies używamy?</h2>
<table>
    <thead>
        <tr><th>Rodzaj</th><th>Cel</th><th>Czas przechowywania</th></tr>
    </thead>
    <tbody>
        <tr>
            <td><strong>Niezbędne</strong></td>
            <td>Zapewniają prawidłowe działanie sklepu: sesja użytkownika, koszyk zakupowy, bezpieczeństwo.</td>
            <td>Sesja / do 1 roku</td>
        </tr>
        <tr>
            <td><strong>Funkcjonalne</strong></td>
            <td>Zapamiętują Twoje preferencje (np. wybrany język, zalogowanie).</td>
            <td>Do 1 roku</td>
        </tr>
        <tr>
            <td><strong>Analityczne</strong></td>
            <td>Zbierają anonimowe dane o ruchu na stronie (Google Analytics) – pomagają nam ulepszać sklep.</td>
            <td>Do 2 lat</td>
        </tr>
        <tr>
            <td><strong>Marketingowe</strong></td>
            <td>Umożliwiają wyświetlanie dopasowanych reklam i śledzenie skuteczności kampanii.</td>
            <td>Do 1 roku</td>
        </tr>
    </tbody>
</table>

<h2>Jak zarządzać plikami cookies?</h2>
<p>Możesz w dowolnym momencie zmienić ustawienia dotyczące plików cookies w ustawieniach swojej przeglądarki:</p>
<ul>
    <li><strong>Google Chrome:</strong> Ustawienia → Prywatność i bezpieczeństwo → Pliki cookie i inne dane witryn</li>
    <li><strong>Mozilla Firefox:</strong> Ustawienia → Prywatność i bezpieczeństwo → Ciasteczka i dane stron</li>
    <li><strong>Safari:</strong> Preferencje → Prywatność → Zarządzaj danymi witryny</li>
    <li><strong>Microsoft Edge:</strong> Ustawienia → Pliki cookie i uprawnienia witryn</li>
</ul>
<p>Pamiętaj, że wyłączenie niezbędnych plików cookies może uniemożliwić prawidłowe korzystanie ze sklepu (np. dodawanie produktów do koszyka).</p>

<h2>Administrator</h2>
<p>Administratorem danych zbieranych za pośrednictwem plików cookies jest <strong>LIDA Dariusz Cała</strong> (Moretti Fashion), NIP: 5261119292. Kontakt: <a href="mailto:kontakt@morettifashion.com">kontakt@morettifashion.com</a>.</p>
<p>Więcej informacji o przetwarzaniu danych osobowych znajdziesz w <a href="/polityka-prywatnosci/">Polityce prywatności</a>.</p>'
    ),
    'dostawa-i-platnosci' => array(
        'title' => 'Dostawa i płatności',
        'content' => '
<h2>Ceny</h2>
<p>Wszystkie ceny podane w sklepie są cenami brutto w złotych polskich (PLN) i zawierają podatek VAT. Cena obowiązująca w chwili złożenia zamówienia jest wiążąca dla obu stron.</p>

<h2>Formy płatności</h2>
<ul>
    <li><strong>Płatność online</strong> – karta płatnicza (Visa, Mastercard), BLIK, przelew ekspresowy za pośrednictwem operatora płatności. Zamówienie realizowane jest niezwłocznie po zaksięgowaniu płatności.</li>
    <li><strong>Przelew tradycyjny</strong> – na rachunek bankowy Sprzedawcy. Dane do przelewu przesyłane są e-mailem po złożeniu zamówienia. Zamówienie realizowane jest po zaksięgowaniu wpłaty. Termin zapłaty: 3 dni robocze.</li>
    <li><strong>Płatność za pobraniem</strong> – należność uiszczana jest przy odbiorze przesyłki. Zamówienie realizowane jest niezwłocznie po jego przyjęciu.</li>
</ul>

<h2>Dostawa</h2>
<p>Realizujemy dostawy na terenie Polski. Dostępne metody dostawy oraz ich koszty wyświetlane są na etapie składania zamówienia.</p>
<ul>
    <li><strong>Kurier</strong> – dostawa w 1–2 dni robocze od momentu nadania przesyłki.</li>
    <li><strong>Poczta Polska</strong> – dostawa w 2–4 dni robocze od momentu nadania przesyłki.</li>
    <li><strong>Paczkomat InPost</strong> – dostawa w 1–2 dni robocze od momentu nadania przesyłki.</li>
</ul>

<h2>Czas realizacji zamówienia</h2>
<p>Zamówienia są realizowane w ciągu <strong>1–3 dni roboczych</strong> od momentu zaksięgowania płatności (lub od przyjęcia zamówienia w przypadku płatności za pobraniem). W przypadku wydłużonego czasu realizacji Klient zostanie poinformowany drogą e-mailową.</p>

<h2>Odbiór przesyłki</h2>
<p>Przy odbiorze przesyłki prosimy o sprawdzenie jej stanu zewnętrznego. Jeśli opakowanie nosi ślady uszkodzeń, należy sporządzić protokół szkody w obecności kuriera i niezwłocznie skontaktować się z nami pod adresem <a href="mailto:kontakt@morettifashion.com">kontakt@morettifashion.com</a>.</p>

<h2>Dowód zakupu</h2>
<p>Do każdego zamówienia wystawiamy paragon lub fakturę VAT (na życzenie Klienta). Dokument zakupu dołączany jest do przesyłki lub przesyłany drogą elektroniczną.</p>'
    ),
    'zwroty' => array(
        'title' => 'Zwroty',
        'content' => '
<h2>Prawo do odstąpienia od umowy</h2>
<p>Jako konsument masz prawo odstąpić od umowy zawartej na odległość bez podawania przyczyny w terminie <strong>14 dni</strong> od dnia otrzymania towaru. Nie musisz uzasadniać swojej decyzji.</p>

<h2>Jak dokonać zwrotu?</h2>
<ol>
    <li><strong>Poinformuj nas</strong> – wyślij wiadomość e-mail na adres <a href="mailto:kontakt@morettifashion.com">kontakt@morettifashion.com</a> przed upływem 14 dni od otrzymania zamówienia. Podaj numer zamówienia i informację, że chcesz odstąpić od umowy.</li>
    <li><strong>Zapakuj towar</strong> – zapakuj produkt starannie, najlepiej w oryginalne opakowanie. Dołącz dowód zakupu (paragon lub fakturę).</li>
    <li><strong>Wyślij przesyłkę</strong> – odeślij towar na adres:<br>
        <strong>LIDA Dariusz Cała – Moretti Fashion</strong><br>
        Nadrzeczna 14, GD Hala 5, Box A-07<br>
        05-552 Wólka Kosowska<br>
        Przesyłkę należy nadać w ciągu 14 dni od złożenia oświadczenia o odstąpieniu.
    </li>
    <li><strong>Zwrot pieniędzy</strong> – po otrzymaniu i sprawdzeniu towaru zwrócimy Ci pełną kwotę zamówienia (wraz z kosztami najtańszej dostępnej opcji dostawy) w ciągu <strong>14 dni</strong>. Zwrot dokonywany jest tą samą metodą płatności, którą zapłaciłeś.</li>
</ol>

<h2>Warunki zwrotu</h2>
<ul>
    <li>Towar powinien być w stanie niezmienionym – nieużywany, bez śladów użytkowania, z oryginalnymi metkami.</li>
    <li>Koszty odesłania towaru ponosi Klient.</li>
    <li>Prawo odstąpienia nie dotyczy produktów wykonanych na indywidualne zamówienie.</li>
</ul>

<h2>Kontakt</h2>
<p>W razie pytań dotyczących zwrotów: <a href="mailto:kontakt@morettifashion.com">kontakt@morettifashion.com</a> lub tel. <a href="tel:+48725538100">+48 725 538 100</a>.</p>'
    ),
    'reklamacje' => array(
        'title' => 'Reklamacje',
        'content' => '
<h2>Rękojmia za wady</h2>
<p>Wszystkie produkty Moretti Fashion objęte są <strong>2-letnią rękojmią</strong> za wady fizyczne i prawne, wynikającą z przepisów Kodeksu cywilnego. Jeśli zakupiony produkt jest wadliwy lub niezgodny z opisem, możesz zgłosić reklamację.</p>

<h2>Jak zgłosić reklamację?</h2>
<ol>
    <li><strong>Skontaktuj się z nami</strong> – wyślij wiadomość e-mail na adres <a href="mailto:kontakt@morettifashion.com">kontakt@morettifashion.com</a>. W wiadomości podaj:
        <ul>
            <li>numer zamówienia,</li>
            <li>opis stwierdzonej wady,</li>
            <li>swoje żądanie (naprawa, wymiana, obniżenie ceny lub zwrot pieniędzy),</li>
            <li>zdjęcia dokumentujące wadę.</li>
        </ul>
    </li>
    <li><strong>Oczekuj na odpowiedź</strong> – rozpatrzymy Twoją reklamację w terminie <strong>14 dni kalendarzowych</strong> od jej otrzymania i poinformujemy Cię o sposobie jej rozpatrzenia.</li>
    <li><strong>Odesłanie towaru</strong> – jeśli rozpatrzenie reklamacji wymaga odesłania produktu, poinformujemy Cię o tym w odpowiedzi. Koszty odesłania towaru w ramach reklamacji pokrywa Sprzedawca.</li>
</ol>

<h2>Adres do odesłania towaru</h2>
<p>
    <strong>LIDA Dariusz Cała – Moretti Fashion</strong><br>
    Nadrzeczna 14, GD Hala 5, Box A-07<br>
    05-552 Wólka Kosowska
</p>

<h2>Pozasądowe rozwiązywanie sporów</h2>
<p>Jeśli nie jesteś zadowolony z rozstrzygnięcia reklamacji, masz prawo skorzystać z pozasądowych metod rozpatrywania reklamacji i dochodzenia roszczeń. Informacje dostępne są na stronie Urzędu Ochrony Konkurencji i Konsumentów: <a href="https://www.uokik.gov.pl" target="_blank" rel="noopener">www.uokik.gov.pl</a>.</p>

<h2>Kontakt</h2>
<p><a href="mailto:kontakt@morettifashion.com">kontakt@morettifashion.com</a> | tel. <a href="tel:+48725538100">+48 725 538 100</a></p>'
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
