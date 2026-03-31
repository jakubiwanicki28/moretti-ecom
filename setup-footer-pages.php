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

// Treści stron prawnych zgodne z: UPK (2014, nowelizacja 2023), RODO/GDPR (2016/679),
// Dyrektywa Omnibus (2019/2161, wdrożona od 01.01.2023), UoSUDE (2002), ADR/ODR.
$pages_to_create = array(
    'regulamin-sklepu' => array(
        'title' => 'Regulamin sklepu',
        'content' => '
<p><em>Niniejszy Regulamin wchodzi w życie z dniem 1 kwietnia 2026 r.</em></p>

<h2>§ 1. Postanowienia ogólne i definicje</h2>
<ol>
    <li>Sklep internetowy dostępny pod adresem <strong>morettifashion.com</strong> (dalej: „Sklep") prowadzony jest przez <strong>LIDA Dariusz Cała</strong>, prowadzącego działalność gospodarczą pod marką <strong>Moretti Fashion</strong>, NIP: <strong>5261119292</strong>, REGON: <strong>015161906</strong>, adres: Nadrzeczna 14, GD Hala 5, Box A-07, 05-552 (dalej: „Sprzedawca").</li>
    <li>Dane kontaktowe Sprzedawcy: e-mail: <a href="mailto:kontakt@morettifashion.com">kontakt@morettifashion.com</a>, tel. <a href="tel:+48725538100">+48 725 538 100</a> (pon.–pt., godz. 9:00–17:00).</li>
    <li>Niniejszy Regulamin określa zasady korzystania ze Sklepu, zawierania umów sprzedaży na odległość oraz prawa i obowiązki stron. Regulamin jest udostępniany nieodpłatnie, w sposób umożliwiający jego pobranie, utrwalenie i wydruk.</li>
    <li>Użyte w Regulaminie pojęcia oznaczają:
        <ul>
            <li><strong>Konsument</strong> – osoba fizyczna zawierająca ze Sprzedawcą umowę niezwiązaną bezpośrednio z jej działalnością gospodarczą lub zawodową;</li>
            <li><strong>Przedsiębiorca-Konsument</strong> – osoba fizyczna zawierająca umowę związaną z prowadzoną działalnością gospodarczą, ale nieposiadającą dla niej charakteru zawodowego (art. 38a UPK); korzysta z większości praw konsumenckich;</li>
            <li><strong>Klient</strong> – Konsument lub Przedsiębiorca-Konsument;</li>
            <li><strong>Towar</strong> – rzecz ruchoma będąca przedmiotem umowy sprzedaży;</li>
            <li><strong>Umowa sprzedaży</strong> – umowa sprzedaży Towaru zawierana na odległość za pośrednictwem Sklepu;</li>
            <li><strong>UPK</strong> – ustawa z dnia 30 maja 2014 r. o prawach konsumenta (Dz.U. 2014 poz. 827 ze zm.).</li>
        </ul>
    </li>
    <li>Ogłoszenia, opisy produktów i ceny widoczne w Sklepie stanowią zaproszenie do zawarcia umowy w rozumieniu art. 71 Kodeksu cywilnego, a nie ofertę.</li>
    <li>Do korzystania ze Sklepu wymagane jest urządzenie z dostępem do Internetu i aktualną przeglądarką internetową obsługującą JavaScript i pliki cookies.</li>
</ol>

<h2>§ 2. Składanie i realizacja zamówień</h2>
<ol>
    <li>Zamówienia można składać przez całą dobę, 7 dni w tygodniu, za pośrednictwem strony internetowej Sklepu.</li>
    <li>W celu złożenia zamówienia Klient:
        <ol>
            <li>dodaje wybrane Towary do koszyka;</li>
            <li>przechodzi do kasy i podaje dane niezbędne do realizacji zamówienia (imię, nazwisko, adres dostawy, adres e-mail, numer telefonu);</li>
            <li>wybiera sposób dostawy i formę płatności;</li>
            <li>zapoznaje się z Regulaminem i potwierdza jego akceptację;</li>
            <li>składa zamówienie poprzez kliknięcie przycisku <strong>„Zamawiam i płacę"</strong> – co oznacza złożenie oferty zakupu z obowiązkiem zapłaty (art. 17 ust. 1 UPK).</li>
        </ol>
    </li>
    <li>Po złożeniu zamówienia Klient otrzymuje na podany adres e-mail automatyczne potwierdzenie jego otrzymania.</li>
    <li><strong>Umowa sprzedaży zostaje zawarta z chwilą przesłania przez Sprzedawcę potwierdzenia przyjęcia zamówienia do realizacji</strong> na adres e-mail Klienta. Do tego momentu Sprzedawca może odmówić przyjęcia zamówienia w przypadku braku dostępności Towaru lub podania przez Klienta nieprawdziwych danych.</li>
    <li>Sprzedawca niezwłocznie przekazuje treść zawartej umowy Klientowi na trwałym nośniku (e-mail z potwierdzeniem zamówienia zawierający niniejszy Regulamin lub link do jego aktualnej treści).</li>
    <li>Sprzedawca zastrzega sobie prawo do weryfikacji zamówień i kontaktu z Klientem w celu ich potwierdzenia.</li>
</ol>

<h2>§ 3. Ceny</h2>
<ol>
    <li>Wszystkie ceny podane w Sklepie są cenami brutto w złotych polskich (PLN) i zawierają podatek VAT. Cena Towaru nie obejmuje kosztów dostawy, które są podawane odrębnie na etapie składania zamówienia.</li>
    <li>Cena Towaru obowiązująca w chwili złożenia zamówienia jest wiążąca dla obu stron i nie może ulec zmianie po zawarciu Umowy sprzedaży.</li>
    <li><strong>Informacja o obniżkach cen (Dyrektywa Omnibus):</strong> W przypadku prezentowania obniżonej ceny Towaru, obok ceny obniżonej wyświetlana jest najniższa cena tego Towaru obowiązująca w Sklepie w okresie 30 dni przed wprowadzeniem obniżki, zgodnie z art. 4 ust. 2 ustawy z dnia 9 maja 2014 r. o informowaniu o cenach towarów i usług.</li>
    <li>Sprzedawca zastrzega sobie prawo do zmiany cen Towarów. Zmiany cen nie dotyczą zamówień złożonych przed ich wprowadzeniem.</li>
</ol>

<h2>§ 4. Formy płatności</h2>
<ol>
    <li>Dostępne formy płatności:
        <ul>
            <li><strong>Płatność online</strong> – karta płatnicza (Visa, Mastercard), BLIK, przelew ekspresowy – za pośrednictwem serwisu Przelewy24 (operator: PayPro SA, ul. Pastelowa 8, 60-198 Poznań). Realizacja zamówienia następuje po zaksięgowaniu płatności;</li>
            <li><strong>Przelew tradycyjny</strong> – na rachunek bankowy Sprzedawcy. Dane do przelewu przesyłane są e-mailem po złożeniu zamówienia. Zamówienie realizowane jest po zaksięgowaniu wpłaty. Klient zobowiązany jest do dokonania zapłaty w terminie 3 dni roboczych od złożenia zamówienia; po upływie tego terminu zamówienie może zostać anulowane;</li>
            <li><strong>Płatność za pobraniem</strong> – należność uiszczana jest przy odbiorze przesyłki od kuriera. Realizacja zamówienia następuje niezwłocznie po jego przyjęciu.</li>
        </ul>
    </li>
    <li>Sprzedawca nie pobiera od Klientów dodatkowych opłat za korzystanie z określonych metod płatności ponad rzeczywiste koszty ponoszone przez Sprzedawcę z tego tytułu.</li>
</ol>

<h2>§ 5. Dostawa</h2>
<ol>
    <li>Dostawa Towarów realizowana jest wyłącznie na terytorium Rzeczypospolitej Polskiej.</li>
    <li>Dostępne metody dostawy, ich koszty oraz szacowane terminy prezentowane są na etapie składania zamówienia.</li>
    <li>Zamówienia są kompletowane i wysyłane w ciągu <strong>1–3 dni roboczych</strong> od:
        <ul>
            <li>zaksięgowania płatności – w przypadku płatności online lub przelewu tradycyjnego;</li>
            <li>złożenia zamówienia – w przypadku płatności za pobraniem.</li>
        </ul>
    </li>
    <li>Maksymalny termin dostarczenia Towaru nie przekroczy <strong>14 dni roboczych</strong> od zawarcia Umowy sprzedaży, chyba że strony uzgodniły inaczej. W przypadku niemożności dotrzymania tego terminu Sprzedawca niezwłocznie poinformuje Klienta drogą e-mailową.</li>
    <li>Przy odbiorze przesyłki Klient powinien sprawdzić jej stan zewnętrzny. W przypadku stwierdzenia uszkodzeń mechanicznych powstałych w transporcie, Klient powinien sporządzić protokół szkody w obecności kuriera i niezwłocznie powiadomić Sprzedawcę.</li>
    <li>Ryzyko utraty lub uszkodzenia Towaru przechodzi na Klienta (Konsumenta) z chwilą objęcia Towaru przez Klienta lub wskazaną przez niego osobę trzecią. W relacjach B2B ryzyko przechodzi z chwilą wydania Towaru przewoźnikowi.</li>
</ol>

<h2>§ 6. Prawo odstąpienia od umowy</h2>
<ol>
    <li>Konsumentowi przysługuje prawo odstąpienia od Umowy sprzedaży zawartej na odległość bez podawania przyczyny, w terminie <strong>14 dni</strong> od dnia otrzymania Towaru (art. 27 UPK). Jeśli zamówienie obejmuje wiele Towarów dostarczanych osobno, termin liczy się od otrzymania ostatniego Towaru.</li>
    <li>Oświadczenie o odstąpieniu można złożyć:
        <ul>
            <li>drogą e-mailową na adres: <a href="mailto:kontakt@morettifashion.com">kontakt@morettifashion.com</a>;</li>
            <li>listownie na adres: LIDA Dariusz Cała – Moretti Fashion, Nadrzeczna 14, GD Hala 5, Box A-07, 05-552;</li>
            <li>z wykorzystaniem wzoru formularza stanowiącego <strong>Załącznik nr 1</strong> do niniejszego Regulaminu (stosowanie wzoru nie jest obowiązkowe).</li>
        </ul>
    </li>
    <li>Do zachowania terminu wystarczy wysłanie oświadczenia przed jego upływem. Sprzedawca niezwłocznie potwierdza otrzymanie oświadczenia o odstąpieniu na trwałym nośniku.</li>
    <li>Konsument zobowiązany jest zwrócić Towar Sprzedawcy lub przekazać go osobie upoważnionej przez Sprzedawcę niezwłocznie, nie później niż w ciągu <strong>14 dni od dnia złożenia oświadczenia o odstąpieniu</strong>. Do zachowania terminu wystarczy odesłanie Towaru przed jego upływem na adres: LIDA Dariusz Cała – Moretti Fashion, Nadrzeczna 14, GD Hala 5, Box A-07, 05-552.</li>
    <li>Bezpośrednie koszty odesłania Towaru (przesyłka zwrotna) ponosi Konsument.</li>
    <li>Sprzedawca zwraca wszystkie dokonane przez Konsumenta płatności, w tym koszty dostarczenia Towaru (z wyjątkiem dodatkowych kosztów wynikających z wybrania przez Konsumenta sposobu dostarczenia innego niż najtańszy zwykły sposób oferowany przez Sprzedawcę), niezwłocznie, nie później niż w terminie <strong>14 dni</strong> od dnia otrzymania zwróconego Towaru lub dowodu jego odesłania (w zależności od tego, co nastąpi wcześniej). Sprzedawca może wstrzymać zwrot płatności do czasu otrzymania Towaru lub potwierdzenia jego odesłania (art. 32 ust. 3 UPK).</li>
    <li>Zwrot płatności dokonywany jest przy użyciu tego samego sposobu płatności, którym posłużył się Konsument, chyba że Konsument wyraźnie zgodził się na inny sposób, który nie wiąże się dla niego z żadnymi kosztami.</li>
    <li>Konsument ponosi odpowiedzialność za zmniejszenie wartości Towaru będące wynikiem korzystania z niego w sposób wykraczający poza konieczny do stwierdzenia charakteru, cech i funkcjonowania Towaru (art. 34 ust. 4 UPK).</li>
</ol>

<h2>§ 7. Wyłączenia prawa odstąpienia od umowy</h2>
<p>Na podstawie art. 38 UPK prawo odstąpienia od umowy <strong>nie przysługuje</strong> w odniesieniu do umów:</p>
<ol>
    <li>o świadczenie usług, za które Konsument jest zobowiązany do zapłaty ceny, jeżeli Sprzedawca wykonał w pełni usługę za wyraźną zgodą Konsumenta, który przed wykonaniem usługi został poinformowany o utracie prawa do odstąpienia po jej wykonaniu;</li>
    <li>w których cena lub wynagrodzenie zależą od wahań na rynku finansowym, nad którymi Sprzedawca nie sprawuje kontroli, i które mogą wystąpić przed upływem terminu do odstąpienia;</li>
    <li>w których przedmiotem świadczenia jest rzecz nieprefabrykowana, wyprodukowana według specyfikacji Konsumenta lub służąca zaspokojeniu jego zindywidualizowanych potrzeb (towar personalizowany/na zamówienie indywidualne);</li>
    <li>w których przedmiotem świadczenia jest rzecz ulegająca szybkiemu zepsuciu lub mająca krótki termin przydatności do użycia;</li>
    <li>w których przedmiotem świadczenia jest rzecz dostarczana w zapieczętowanym opakowaniu, której po otwarciu opakowania nie można zwrócić ze względu na ochronę zdrowia lub ze względów higienicznych, jeżeli opakowanie zostało otwarte po dostarczeniu;</li>
    <li>w których przedmiotem świadczenia są nagrania dźwiękowe lub wizualne albo programy komputerowe dostarczane w zapieczętowanym opakowaniu, jeżeli opakowanie zostało otwarte po dostarczeniu.</li>
</ol>

<h2>§ 8. Niezgodność towaru z umową (reklamacje)</h2>
<ol>
    <li>Sprzedawca jest odpowiedzialny wobec Konsumenta za <strong>niezgodność Towaru z umową</strong> istniejącą w chwili dostarczenia i ujawnioną w ciągu <strong>2 lat</strong> od tej chwili, na zasadach określonych w art. 43a–43g UPK (przepisy obowiązujące od 1 stycznia 2023 r.).</li>
    <li>Domniemywa się, że brak zgodności Towaru z umową, który ujawnił się w ciągu 2 lat od dostarczenia, istniał w chwili dostarczenia.</li>
    <li>Jeżeli Towar jest niezgodny z umową, Konsument może żądać (uprawnienia pierwszorzędne):
        <ul>
            <li><strong>naprawy</strong> Towaru, albo</li>
            <li><strong>wymiany</strong> Towaru na nowy wolny od wad.</li>
        </ul>
        Sprzedawca może odmówić wybranego przez Konsumenta sposobu, jeśli jest niemożliwy lub wymagałby nadmiernych kosztów – w takim przypadku stosuje sposób alternatywny.
    </li>
    <li>Konsument może żądać <strong>obniżenia ceny</strong> albo <strong>odstąpić od umowy</strong> (uprawnienia drugorzędne), gdy:
        <ul>
            <li>Sprzedawca odmówił naprawy i wymiany;</li>
            <li>Sprzedawca nie doprowadził Towaru do zgodności z umową;</li>
            <li>brak zgodności wystąpił ponownie pomimo próby naprawy lub wymiany;</li>
            <li>brak zgodności jest na tyle istotny, że uzasadnia natychmiastowe obniżenie ceny lub odstąpienie bez uprzedniego żądania naprawy/wymiany;</li>
            <li>z oświadczenia lub zachowania Sprzedawcy wyraźnie wynika, że nie doprowadzi on Towaru do zgodności w rozsądnym czasie.</li>
        </ul>
        Prawo odstąpienia od umowy w tym trybie nie przysługuje, jeżeli brak zgodności jest nieistotny.
    </li>
    <li>Sprzedawca ponosi koszty naprawy lub wymiany, w tym koszty przesyłki zwrotnej.</li>
    <li>Reklamację należy zgłosić na adres e-mail: <a href="mailto:kontakt@morettifashion.com">kontakt@morettifashion.com</a>. Zgłoszenie powinno zawierać: numer zamówienia, opis niezgodności, dokumentację zdjęciową (jeśli możliwa) oraz żądanie Konsumenta (naprawa, wymiana, obniżenie ceny lub zwrot).</li>
    <li>Sprzedawca udzieli odpowiedzi na reklamację w terminie <strong>14 dni</strong> od jej otrzymania. Brak odpowiedzi w tym terminie uważany jest za uznanie reklamacji za zasadną (art. 7a UPK).</li>
    <li>Sprzedawca zwraca należność z tytułu obniżenia ceny lub odstąpienia od umowy w terminie 14 dni od złożenia przez Konsumenta stosownego oświadczenia.</li>
</ol>

<h2>§ 9. Gwarancja</h2>
<ol>
    <li>Towary mogą być objęte gwarancją producenta lub Sprzedawcy. Informacja o gwarancji i jej warunkach jest każdorazowo podawana w opisie Towaru.</li>
    <li>Gwarancja jest uprawnieniem dodatkowym i nie wyłącza, nie ogranicza ani nie zawiesza uprawnień Konsumenta wynikających z przepisów o niezgodności Towaru z umową.</li>
    <li>Warunki gwarancji udostępniane są Konsumentowi przed zawarciem umowy i dołączane do Towaru.</li>
</ol>

<h2>§ 10. Ochrona danych osobowych</h2>
<ol>
    <li>Administratorem danych osobowych Klientów jest LIDA Dariusz Cała (Moretti Fashion), NIP: 5261119292.</li>
    <li>Dane osobowe przetwarzane są zgodnie z Rozporządzeniem Parlamentu Europejskiego i Rady (UE) 2016/679 (RODO).</li>
    <li>Szczegółowe informacje dotyczące przetwarzania danych osobowych, w tym Twoich praw, znajdziesz w <a href="/polityka-prywatnosci/">Polityce prywatności</a>.</li>
</ol>

<h2>§ 11. Pozasądowe rozwiązywanie sporów (ADR/ODR)</h2>
<ol>
    <li>Konsument ma prawo skorzystać z pozasądowych sposobów rozpatrywania reklamacji i dochodzenia roszczeń, w szczególności:
        <ul>
            <li>mediacji prowadzonej przez właściwy Wojewódzki Inspektorat Inspekcji Handlowej;</li>
            <li>postępowania przed Stałym Polubownym Sądem Konsumenckim przy właściwym Wojewódzkim Inspektoracie Inspekcji Handlowej;</li>
            <li>pomocy powiatowego (miejskiego) rzecznika konsumentów.</li>
        </ul>
        Szczegółowe informacje dostępne na stronie Urzędu Ochrony Konkurencji i Konsumentów: <a href="https://www.uokik.gov.pl" target="_blank" rel="noopener noreferrer">www.uokik.gov.pl</a>.
    </li>
    <li>Zgodnie z rozporządzeniem Parlamentu Europejskiego i Rady (UE) nr 524/2013, pod adresem <a href="https://ec.europa.eu/consumers/odr/" target="_blank" rel="noopener noreferrer">https://ec.europa.eu/consumers/odr/</a> dostępna jest platforma internetowego systemu rozstrzygania sporów (platforma ODR) pomiędzy konsumentami a przedsiębiorcami na poziomie unijnym. Adres e-mail Sprzedawcy: <a href="mailto:kontakt@morettifashion.com">kontakt@morettifashion.com</a>.</li>
    <li>W przypadku odrzucenia reklamacji przez Sprzedawcę, Klient zostanie poinformowany, czy Sprzedawca wyraża zgodę na udział w postępowaniu w ramach ADR.</li>
</ol>

<h2>§ 12. Postanowienia końcowe</h2>
<ol>
    <li>W sprawach nieuregulowanych niniejszym Regulaminem zastosowanie mają przepisy prawa polskiego, w szczególności ustawy z dnia 23 kwietnia 1964 r. – Kodeks cywilny oraz ustawy z dnia 30 maja 2014 r. o prawach konsumenta.</li>
    <li>Sprzedawca zastrzega sobie prawo do zmiany Regulaminu z ważnych przyczyn (zmiana przepisów prawa, zmiana warunków technicznych, zmiana zakresu działalności). O każdej zmianie Sprzedawca poinformuje z co najmniej 14-dniowym wyprzedzeniem poprzez zamieszczenie informacji na stronie Sklepu. Do umów zawartych przed zmianą Regulaminu stosuje się wersję Regulaminu obowiązującą w chwili złożenia zamówienia.</li>
    <li>Sądem właściwym do rozstrzygania sporów wynikających z Umowy sprzedaży jest sąd właściwy według przepisów Kodeksu postępowania cywilnego. Postanowienie o właściwości sądu nie ogranicza bezwzględnie obowiązujących przepisów chroniących Konsumenta.</li>
    <li>Prawem właściwym dla umów zawieranych na podstawie niniejszego Regulaminu jest prawo polskie. Wybór prawa polskiego nie pozbawia Konsumenta mającego miejsce zamieszkania w państwie członkowskim UE ochrony przyznanej mu przepisami tego państwa, których nie można wyłączyć w drodze umowy.</li>
    <li>Regulamin wchodzi w życie z dniem 1 kwietnia 2026 r.</li>
</ol>

<hr>
<h2>Załącznik nr 1 – Wzór formularza odstąpienia od umowy</h2>
<p><em>(Wypełnij i odeślij ten formularz tylko wtedy, gdy chcesz odstąpić od umowy)</em></p>
<p>
Adresat:<br>
LIDA Dariusz Cała – Moretti Fashion<br>
Nadrzeczna 14, GD Hala 5, Box A-07, 05-552<br>
E-mail: <a href="mailto:kontakt@morettifashion.com">kontakt@morettifashion.com</a>
</p>
<p>Niniejszym informuję o moim odstąpieniu od umowy sprzedaży następujących rzeczy:</p>
<p>
Nazwa Towaru: ………………………………………………………………………………<br>
Numer zamówienia: ………………………………………………………………………<br>
Data zawarcia umowy / data odbioru Towaru: ……………………………………<br>
Imię i nazwisko Konsumenta: ………………………………………………………<br>
Adres Konsumenta: ……………………………………………………………………<br>
Numer rachunku bankowego do zwrotu (opcjonalnie): ………………………<br><br>
Podpis Konsumenta (tylko jeżeli formularz przesyłany jest w formie papierowej):<br>
………………………………………<br><br>
Data: ………………………………
</p>'
    ),
    'polityka-prywatnosci' => array(
        'title' => 'Polityka prywatności',
        'content' => '
<p><em>Niniejsza Polityka prywatności obowiązuje od 1 kwietnia 2026 r. i spełnia wymogi art. 13 Rozporządzenia Parlamentu Europejskiego i Rady (UE) 2016/679 z dnia 27 kwietnia 2016 r. (RODO).</em></p>

<h2>1. Administrator danych osobowych</h2>
<p>Administratorem Twoich danych osobowych jest <strong>LIDA Dariusz Cała</strong>, prowadzący działalność gospodarczą pod marką <strong>Moretti Fashion</strong>:</p>
<ul>
    <li>NIP: <strong>5261119292</strong>, REGON: <strong>015161906</strong></li>
    <li>Adres: Nadrzeczna 14, GD Hala 5, Box A-07, 05-552</li>
    <li>E-mail: <a href="mailto:kontakt@morettifashion.com">kontakt@morettifashion.com</a></li>
    <li>Telefon: <a href="tel:+48725538100">+48 725 538 100</a></li>
</ul>
<p>Nie wyznaczyliśmy Inspektora Ochrony Danych. We wszystkich sprawach dotyczących danych osobowych możesz kontaktować się bezpośrednio z Administratorem na wskazane powyżej dane.</p>

<h2>2. Cele i podstawy prawne przetwarzania danych</h2>
<p>Przetwarzamy Twoje dane osobowe wyłącznie w konkretnych, wyraźnych i prawnie uzasadnionych celach:</p>

<table>
    <thead><tr><th>Cel przetwarzania</th><th>Podstawa prawna (art. 6 ust. 1 RODO)</th><th>Okres przechowywania</th></tr></thead>
    <tbody>
        <tr>
            <td>Realizacja zamówienia i wykonanie umowy sprzedaży (w tym: dostawa, obsługa płatności, komunikacja)</td>
            <td><strong>lit. b</strong> – wykonanie umowy</td>
            <td>Czas trwania umowy + 6 lat od jej zakończenia (przedawnienie roszczeń)</td>
        </tr>
        <tr>
            <td>Wystawienie faktury VAT / paragonu fiskalnego i prowadzenie dokumentacji księgowej</td>
            <td><strong>lit. c</strong> – obowiązek prawny (ustawa o rachunkowości, Ordynacja podatkowa)</td>
            <td>5 lat od końca roku podatkowego, w którym powstało zobowiązanie podatkowe</td>
        </tr>
        <tr>
            <td>Obsługa reklamacji i zwrotów</td>
            <td><strong>lit. b</strong> – wykonanie umowy / <strong>lit. c</strong> – obowiązek prawny</td>
            <td>2 lata od dostarczenia Towaru + czas na dochodzenie roszczeń</td>
        </tr>
        <tr>
            <td>Dochodzenie i obrona przed roszczeniami</td>
            <td><strong>lit. f</strong> – prawnie uzasadniony interes Administratora</td>
            <td>Do upływu okresu przedawnienia roszczeń (do 6 lat)</td>
        </tr>
        <tr>
            <td>Marketing bezpośredni własnych produktów (newsletter, informacje handlowe) – po wyrażeniu zgody</td>
            <td><strong>lit. a</strong> – zgoda (możliwa do cofnięcia w dowolnym momencie)</td>
            <td>Do cofnięcia zgody lub wniesienia sprzeciwu</td>
        </tr>
        <tr>
            <td>Analityka i statystyki oglądalności strony (pliki cookies analityczne) – po wyrażeniu zgody</td>
            <td><strong>lit. a</strong> – zgoda</td>
            <td>Do cofnięcia zgody / wygaśnięcia cookies (max. 26 miesięcy)</td>
        </tr>
        <tr>
            <td>Zapewnienie bezpieczeństwa systemów informatycznych i przeciwdziałanie nadużyciom</td>
            <td><strong>lit. f</strong> – prawnie uzasadniony interes Administratora</td>
            <td>Do 12 miesięcy od zdarzenia</td>
        </tr>
    </tbody>
</table>

<h2>3. Zakres zbieranych danych</h2>
<p>W związku z realizacją zamówień przetwarzamy następujące dane: imię i nazwisko, adres dostawy (ulica, numer, kod pocztowy, miasto), adres e-mail, numer telefonu. W przypadku wystawiania faktury na firmę: nazwa firmy, NIP, adres siedziby. Podanie danych niezbędnych do realizacji zamówienia jest warunkiem zawarcia umowy – bez nich realizacja zamówienia nie jest możliwa.</p>

<h2>4. Odbiorcy danych osobowych</h2>
<p>Twoje dane osobowe mogą być przekazywane wyłącznie podmiotom, których udział jest niezbędny do realizacji usług:</p>
<ul>
    <li><strong>Operatorzy płatności:</strong> PayPro SA (operator serwisu Przelewy24), ul. Pastelowa 8, 60-198 Poznań – w celu obsługi transakcji płatniczych;</li>
    <li><strong>Firmy kurierskie i operatorzy logistyczni:</strong> InPost SA, DPD Polska Sp. z o.o., DHL Parcel Polska Sp. z o.o., Poczta Polska SA – w celu dostarczenia zamówienia;</li>
    <li><strong>Dostawcy oprogramowania i usług hostingowych:</strong> dostawca hostingu i platformy WordPress/WooCommerce – w celu utrzymania i funkcjonowania Sklepu;</li>
    <li><strong>Biuro rachunkowe / firma księgowa</strong> – w zakresie niezbędnym do prowadzenia dokumentacji finansowej;</li>
    <li><strong>Google LLC</strong> – w zakresie usług analitycznych (Google Analytics) po wyrażeniu zgody na cookies analityczne; dane mogą być transferowane do USA na podstawie standardowych klauzul umownych (SCC);</li>
    <li><strong>Podmioty prawnicze</strong> – w przypadku konieczności dochodzenia lub obrony roszczeń.</li>
</ul>
<p>Nie sprzedajemy Twoich danych osobowych podmiotom trzecim w celach marketingowych.</p>

<h2>5. Przekazywanie danych poza Europejski Obszar Gospodarczy (EOG)</h2>
<p>Korzystanie z usług Google Analytics może wiązać się z transferem Twoich danych do Stanów Zjednoczonych. Transfer odbywa się na podstawie standardowych klauzul umownych (SCC) zatwierdzonych przez Komisję Europejską, co zapewnia odpowiedni poziom ochrony Twoich danych. Masz prawo uzyskać kopię wdrożonych zabezpieczeń, kontaktując się z nami.</p>

<h2>6. Twoje prawa wynikające z RODO</h2>
<p>Na podstawie przepisów RODO przysługują Ci następujące prawa:</p>
<ul>
    <li><strong>Prawo dostępu do danych</strong> (art. 15 RODO) – możesz zażądać informacji o tym, jakie Twoje dane przetwarzamy, w jakim celu i przez jaki czas;</li>
    <li><strong>Prawo do sprostowania danych</strong> (art. 16 RODO) – możesz zażądać poprawienia nieprawidłowych lub uzupełnienia niekompletnych danych;</li>
    <li><strong>Prawo do usunięcia danych</strong> (art. 17 RODO, „prawo do bycia zapomnianym") – możesz zażądać usunięcia danych, gdy nie są już niezbędne do celów, dla których zostały zebrane, lub gdy wycofasz zgodę (jeśli przetwarzanie opierało się na zgodzie);</li>
    <li><strong>Prawo do ograniczenia przetwarzania</strong> (art. 18 RODO) – możesz zażądać ograniczenia przetwarzania w określonych sytuacjach (np. gdy kwestionujesz prawidłowość danych);</li>
    <li><strong>Prawo do przenoszenia danych</strong> (art. 20 RODO) – możesz otrzymać swoje dane w ustrukturyzowanym formacie (np. CSV) i przesłać je innemu administratorowi – dotyczy danych przetwarzanych na podstawie zgody lub umowy, w sposób zautomatyzowany;</li>
    <li><strong>Prawo do sprzeciwu</strong> (art. 21 RODO) – możesz w dowolnym momencie wnieść sprzeciw wobec przetwarzania Twoich danych opartego na prawnie uzasadnionym interesie Administratora (art. 6 ust. 1 lit. f RODO), w tym wobec profilowania. Po wniesieniu sprzeciwu Administrator nie może dalej przetwarzać tych danych, chyba że wykaże istnienie ważnych prawnie uzasadnionych podstaw nadrzędnych;</li>
    <li><strong>Prawo do cofnięcia zgody</strong> (art. 7 ust. 3 RODO) – jeśli przetwarzanie odbywa się na podstawie Twojej zgody, możesz ją cofnąć w dowolnym momencie bez wpływu na zgodność z prawem przetwarzania dokonanego przed jej cofnięciem;</li>
    <li><strong>Prawo do wniesienia skargi</strong> (art. 77 RODO) – masz prawo wniesienia skargi do organu nadzorczego – Prezesa Urzędu Ochrony Danych Osobowych (UODO), ul. Stawki 2, 00-193 Warszawa, e-mail: kancelaria@uodo.gov.pl, strona: <a href="https://uodo.gov.pl" target="_blank" rel="noopener noreferrer">uodo.gov.pl</a>.</li>
</ul>
<p>Aby skorzystać z powyższych praw, skontaktuj się z nami: <a href="mailto:kontakt@morettifashion.com">kontakt@morettifashion.com</a>. Odpowiemy bez zbędnej zwłoki, nie później niż w ciągu 1 miesiąca od otrzymania żądania (z możliwością przedłużenia o kolejne 2 miesiące w przypadkach skomplikowanych).</p>

<h2>7. Profilowanie i zautomatyzowane podejmowanie decyzji</h2>
<p>Nie stosujemy zautomatyzowanego podejmowania decyzji, które wywoływałoby istotne skutki prawne wobec Konsumentów. Możemy analizować historię zakupów w celu personalizacji ofert (np. wyświetlanie rekomendowanych produktów) – jest to profilowanie, które jednak nie wywołuje dla Ciebie żadnych wiążących skutków prawnych ani podobnie istotnych skutków.</p>

<h2>8. Pliki cookies</h2>
<p>Sklep morettifashion.com używa plików cookies. Szczegółowe informacje na temat stosowanych plików cookies, ich rodzajów, celów oraz sposobu zarządzania zgodami znajdziesz w <a href="/polityka-plikow-cookies/">Polityce plików cookies</a>.</p>

<h2>9. Bezpieczeństwo danych</h2>
<p>Stosujemy odpowiednie techniczne i organizacyjne środki bezpieczeństwa chroniące Twoje dane przed nieuprawnionym dostępem, utratą lub zniszczeniem, w tym: szyfrowanie połączeń protokołem TLS/SSL, kontrolę dostępu do systemów przetwarzających dane osobowe, regularne aktualizacje oprogramowania. W przypadku naruszenia ochrony danych osobowych mogącego powodować ryzyko dla praw i wolności osób fizycznych, poinformujemy UODO w ciągu 72 godzin, a Ciebie – jeśli ryzyko będzie wysokie.</p>

<h2>10. Zmiany Polityki prywatności</h2>
<p>Zastrzegamy sobie prawo do zmiany niniejszej Polityki prywatności. O istotnych zmianach poinformujemy poprzez zamieszczenie informacji na stronie Sklepu. Aktualna wersja jest zawsze dostępna pod adresem: <a href="https://www.morettifashion.com/polityka-prywatnosci/">morettifashion.com/polityka-prywatnosci/</a>. Data ostatniej aktualizacji: 1 kwietnia 2026 r.</p>'
    ),
    'polityka-plikow-cookies' => array(
        'title' => 'Polityka plików cookies',
        'content' => '
<p><em>Niniejsza Polityka cookies obowiązuje od 1 kwietnia 2026 r.</em></p>

<h2>1. Czym są pliki cookies?</h2>
<p>Pliki cookies (ciasteczka) to małe pliki tekstowe zapisywane na Twoim urządzeniu (komputer, tablet, smartfon) przez przeglądarkę internetową podczas odwiedzania stron internetowych. Pliki cookies nie są programami i nie mogą wykonywać żadnych operacji na Twoim urządzeniu. Umożliwiają natomiast zapamiętanie Twoich preferencji i działań na stronie.</p>

<h2>2. Kto jest administratorem danych zbieranych przez cookies?</h2>
<p>Administratorem danych osobowych zbieranych za pośrednictwem plików cookies jest <strong>LIDA Dariusz Cała</strong> (Moretti Fashion), NIP: 5261119292, Nadrzeczna 14, GD Hala 5, Box A-07, 05-552. Kontakt: <a href="mailto:kontakt@morettifashion.com">kontakt@morettifashion.com</a>.</p>

<h2>3. Jakich plików cookies używamy i jakie są ich cele?</h2>
<table>
    <thead>
        <tr><th>Kategoria</th><th>Przykłady / Dostawca</th><th>Cel</th><th>Podstawa prawna</th><th>Czas przechowywania</th></tr>
    </thead>
    <tbody>
        <tr>
            <td><strong>Niezbędne</strong></td>
            <td>WordPress session, WooCommerce cart, CSRF token</td>
            <td>Konieczne do prawidłowego działania Sklepu: sesja użytkownika, zawartość koszyka, bezpieczeństwo formularzy. Bez nich Sklep nie może działać poprawnie.</td>
            <td>Art. 6 ust. 1 lit. f RODO (uzasadniony interes) – <strong>nie wymagają zgody</strong></td>
            <td>Sesja lub do 1 roku</td>
        </tr>
        <tr>
            <td><strong>Funkcjonalne</strong></td>
            <td>Preferencje języka, zapamiętanie logowania</td>
            <td>Zapamiętują Twoje preferencje w celu ułatwienia korzystania ze Sklepu przy kolejnych wizytach.</td>
            <td>Art. 6 ust. 1 lit. a RODO (zgoda)</td>
            <td>Do 1 roku</td>
        </tr>
        <tr>
            <td><strong>Analityczne</strong></td>
            <td>Google Analytics 4 (Google LLC)</td>
            <td>Zbierają anonimowe dane statystyczne o ruchu na stronie (liczba odwiedzin, czas wizyty, źródła ruchu). Pomagają nam ulepszać Sklep. Dane mogą być przekazywane do USA (SCC).</td>
            <td>Art. 6 ust. 1 lit. a RODO (zgoda) – <strong>wymagana Twoja zgoda</strong></td>
            <td>Do 26 miesięcy</td>
        </tr>
        <tr>
            <td><strong>Marketingowe</strong></td>
            <td>Google Ads, Meta Pixel (Facebook/Instagram)</td>
            <td>Umożliwiają wyświetlanie dopasowanych reklam na innych stronach internetowych oraz mierzenie skuteczności kampanii reklamowych. Dane mogą być przekazywane do USA (SCC).</td>
            <td>Art. 6 ust. 1 lit. a RODO (zgoda) – <strong>wymagana Twoja zgoda</strong></td>
            <td>Do 1 roku</td>
        </tr>
    </tbody>
</table>

<h2>4. Zgoda na pliki cookies</h2>
<p>Przy pierwszej wizycie na stronie wyświetlamy baner umożliwiający wyrażenie lub odmowę zgody na poszczególne kategorie cookies. Możesz:</p>
<ul>
    <li>zaakceptować wszystkie kategorie cookies;</li>
    <li>zaakceptować tylko wybrane kategorie (granularna zgoda);</li>
    <li>odrzucić wszystkie nienezbędne cookies.</li>
</ul>
<p>Odmowa zgody jest równie łatwa jak jej udzielenie. Możesz w każdej chwili <strong>wycofać lub zmienić udzieloną zgodę</strong> klikając w link zarządzania preferencjami cookies dostępny w stopce strony lub w ustawieniach przeglądarki.</p>
<p>Wycofanie zgody nie wpływa na zgodność z prawem przetwarzania, które miało miejsce przed jej wycofaniem.</p>

<h2>5. Jak zarządzać plikami cookies w przeglądarce?</h2>
<p>Możesz w dowolnym momencie zmienić ustawienia cookies w swojej przeglądarce. Instrukcje dla najpopularniejszych przeglądarek:</p>
<ul>
    <li><strong>Google Chrome:</strong> Ustawienia → Prywatność i bezpieczeństwo → Pliki cookie i inne dane witryn</li>
    <li><strong>Mozilla Firefox:</strong> Ustawienia → Prywatność i bezpieczeństwo → Ciasteczka i dane stron</li>
    <li><strong>Safari:</strong> Preferencje → Prywatność → Zarządzaj danymi witryny</li>
    <li><strong>Microsoft Edge:</strong> Ustawienia → Pliki cookie i uprawnienia witryn</li>
    <li><strong>Opera:</strong> Ustawienia → Zaawansowane → Prywatność i bezpieczeństwo → Pliki cookie</li>
</ul>
<p><strong>Uwaga:</strong> Wyłączenie lub usunięcie niezbędnych plików cookies (kategoria „Niezbędne") może uniemożliwić prawidłowe korzystanie ze Sklepu, w tym dodawanie produktów do koszyka i składanie zamówień.</p>

<h2>6. Informacje dodatkowe</h2>
<p>Więcej informacji o przetwarzaniu Twoich danych osobowych, w tym o przysługujących Ci prawach (dostęp, sprostowanie, usunięcie, sprzeciw), znajdziesz w <a href="/polityka-prywatnosci/">Polityce prywatności</a>.</p>'
    ),
    'dostawa-i-platnosci' => array(
        'title' => 'Dostawa i płatności',
        'content' => '
<h2>Ceny towarów</h2>
<p>Wszystkie ceny podane w Sklepie są cenami brutto w złotych polskich (PLN) i zawierają podatek VAT. Koszty dostawy nie są wliczone w cenę Towaru i są podawane odrębnie na etapie składania zamówienia. Cena Towaru obowiązująca w chwili złożenia zamówienia jest wiążąca dla obu stron.</p>
<p>W przypadku prezentowania promocji lub obniżek cen, obok ceny promocyjnej wyświetlana jest najniższa cena Towaru obowiązująca w Sklepie w ciągu 30 dni przed wprowadzeniem obniżki (zgodnie z Dyrektywą Omnibus).</p>

<h2>Formy płatności</h2>
<p>Akceptujemy następujące formy płatności:</p>
<ul>
    <li><strong>Płatność online</strong> – karta płatnicza (Visa, Mastercard), BLIK, przelew ekspresowy (Pay By Link) za pośrednictwem serwisu Przelewy24 (operator: PayPro SA, ul. Pastelowa 8, 60-198 Poznań). Zamówienie przekazywane jest do realizacji niezwłocznie po pozytywnej autoryzacji płatności.</li>
    <li><strong>Przelew tradycyjny</strong> – na rachunek bankowy Sprzedawcy. Dane do przelewu (numer rachunku, dane odbiorcy, tytuł przelewu) przesyłane są e-mailem po złożeniu zamówienia. Zamówienie realizowane jest po zaksięgowaniu wpłaty na rachunku Sprzedawcy. <strong>Termin zapłaty: 3 dni robocze od złożenia zamówienia.</strong> Po bezskutecznym upływie tego terminu zamówienie może zostać anulowane.</li>
    <li><strong>Płatność za pobraniem</strong> – należność płatna gotówką lub kartą bezpośrednio kurierowi przy odbiorze przesyłki. Zamówienie przekazywane jest do realizacji niezwłocznie po jego złożeniu i potwierdzeniu przez Sprzedawcę.</li>
</ul>
<p>Sprzedawca nie pobiera dodatkowych opłat za korzystanie z żadnej z powyższych metod płatności.</p>

<h2>Dostawa</h2>
<p>Realizujemy dostawy wyłącznie na terytorium Rzeczypospolitej Polskiej. Dostępne metody dostawy oraz ich aktualne koszty wyświetlane są w koszyku na etapie składania zamówienia.</p>
<table>
    <thead><tr><th>Metoda dostawy</th><th>Szacowany czas dostawy po wysyłce</th></tr></thead>
    <tbody>
        <tr><td>Kurier (DPD, DHL lub równoważny)</td><td>1–2 dni robocze</td></tr>
        <tr><td>Paczkomat InPost</td><td>1–2 dni robocze</td></tr>
        <tr><td>Poczta Polska – list priorytetowy/paczka</td><td>2–4 dni robocze</td></tr>
    </tbody>
</table>
<p>Podane czasy dostawy są szacunkowe i dotyczą dostaw po wysyłce przez Sprzedawcę. Sprzedawca nie ponosi odpowiedzialności za opóźnienia wynikające z przyczyn leżących wyłącznie po stronie przewoźnika.</p>

<h2>Czas realizacji zamówienia</h2>
<p>Zamówienia kompletowane i przekazywane do wysyłki są w ciągu <strong>1–3 dni roboczych</strong> od:</p>
<ul>
    <li>zaksięgowania płatności – przy płatności online lub przelewie tradycyjnym;</li>
    <li>złożenia zamówienia – przy płatności za pobraniem.</li>
</ul>
<p>Łączny maksymalny czas dostarczenia Towaru (realizacja + dostawa) nie powinien przekroczyć <strong>7 dni roboczych</strong>. W przypadku niemożności dotrzymania tego terminu Sprzedawca niezwłocznie powiadomi Klienta drogą e-mailową i zaproponuje dalsze postępowanie.</p>

<h2>Odbiór przesyłki i protokół szkody</h2>
<p>Przy odbiorze przesyłki prosimy o sprawdzenie jej stanu zewnętrznego przed podpisaniem potwierdzenia odbioru. Jeśli opakowanie nosi ślady uszkodzeń mechanicznych (wgiecenia, rozdarcia, ślady zalania), należy:</p>
<ol>
    <li>sporządzić protokół szkody w obecności kuriera (kurier ma obowiązek protokół sporządzić na żądanie);</li>
    <li>niezwłocznie skontaktować się ze Sprzedawcą: <a href="mailto:kontakt@morettifashion.com">kontakt@morettifashion.com</a>, dołączając zdjęcia uszkodzeń i skan/zdjęcie protokołu.</li>
</ol>
<p>Sporządzenie protokołu szkody znacząco przyspiesza rozpatrzenie ewentualnej reklamacji.</p>

<h2>Dowód zakupu</h2>
<p>Do każdego zamówienia wystawiamy paragon fiskalny lub, na wyraźne życzenie Klienta złożone przed sfinalizowaniem zamówienia, fakturę VAT. Dokument zakupu dołączany jest do przesyłki lub przesyłany drogą elektroniczną (e-mail).</p>

<h2>Zwrot kosztów dostawy przy odstąpieniu od umowy</h2>
<p>W przypadku skorzystania z prawa odstąpienia od umowy (zwrot w ciągu 14 dni), Sprzedawca zwraca Klientowi koszty dostawy Towaru do Klienta, jednak wyłącznie do wysokości najtańszej dostępnej w Sklepie standardowej opcji dostawy. Jeśli Klient wybrał droższą metodę dostawy (np. ekspresową), różnica w kosztach nie jest zwracana. Koszty odesłania Towaru do Sprzedawcy ponosi Klient.</p>'
    ),
    'zwroty' => array(
        'title' => 'Zwroty',
        'content' => '
<h2>Prawo do odstąpienia od umowy – informacja dla Konsumenta</h2>
<p>Masz prawo odstąpić od umowy zawartej na odległość <strong>bez podawania przyczyny</strong> w terminie <strong>14 dni</strong> od dnia, w którym Ty lub wskazana przez Ciebie osoba trzecia (inna niż przewoźnik) objęła Towar w posiadanie. Jeśli zamówienie obejmowało kilka Towarów dostarczanych osobno, termin 14 dni liczy się od otrzymania ostatniego z nich.</p>
<p>Jeśli Sprzedawca nie poinformował Cię o prawie do odstąpienia, termin na odstąpienie wynosi <strong>12 miesięcy</strong> od daty otrzymania Towaru.</p>

<h2>Jak złożyć oświadczenie o odstąpieniu?</h2>
<p>Aby odstąpić od umowy, musisz poinformować Sprzedawcę o swojej decyzji przed upływem terminu 14 dni. Możesz to zrobić:</p>
<ul>
    <li>wysyłając e-mail na adres: <a href="mailto:kontakt@morettifashion.com">kontakt@morettifashion.com</a> – podaj numer zamówienia i informację o odstąpieniu;</li>
    <li>wysyłając pismo listownie na adres: LIDA Dariusz Cała – Moretti Fashion, Nadrzeczna 14, GD Hala 5, Box A-07, 05-552;</li>
    <li>wypełniając i odsyłając <strong>formularz odstąpienia od umowy</strong> (wzór poniżej lub w <a href="/regulamin-sklepu/">Regulaminie sklepu</a> – stosowanie wzoru nie jest obowiązkowe).</li>
</ul>
<p>Sprzedawca niezwłocznie prześle Ci potwierdzenie otrzymania oświadczenia o odstąpieniu na trwałym nośniku (e-mail).</p>

<h2>Jak zwrócić towar?</h2>
<ol>
    <li><strong>Wyślij nam oświadczenie o odstąpieniu</strong> (patrz wyżej) – możesz to zrobić jednocześnie z wysyłką Towaru.</li>
    <li><strong>Zapakuj Towar</strong> starannie, najlepiej w oryginalne opakowanie. Upewnij się, że Towar jest dobrze zabezpieczony na czas transportu.</li>
    <li><strong>Odeślij Towar</strong> na adres Sprzedawcy w ciągu <strong>14 dni od złożenia oświadczenia o odstąpieniu</strong>:<br>
        <strong>LIDA Dariusz Cała – Moretti Fashion</strong><br>
        Nadrzeczna 14, GD Hala 5, Box A-07<br>
        05-552<br>
        Do zachowania terminu wystarczy nadanie przesyłki przed jego upływem.
    </li>
    <li><strong>Koszty zwrotu:</strong> Bezpośrednie koszty odesłania Towaru (przesyłka zwrotna) ponosi Klient/Konsument.</li>
</ol>

<h2>Kiedy otrzymasz zwrot pieniędzy?</h2>
<p>Sprzedawca zwróci Ci wszystkie dokonane płatności, w tym koszty dostarczenia Towaru (z wyjątkiem dodatkowych kosztów wynikających z wybrania przez Ciebie innej metody dostawy niż najtańsza standardowa dostawa oferowana przez Sprzedawcę), niezwłocznie, nie później niż w terminie <strong>14 dni</strong> od dnia otrzymania Towaru lub dowodu jego nadania (w zależności od tego, co nastąpi wcześniej).</p>
<p>Zwrot płatności dokonywany jest przy użyciu tej samej metody płatności, którą wybrałeś przy zakupie, chyba że wyraźnie zgodzisz się na inny sposób, który nie wiąże się dla Ciebie z żadnymi kosztami.</p>

<h2>Stan towaru przy zwrocie</h2>
<p>Ponosisz odpowiedzialność za zmniejszenie wartości Towaru będące wynikiem korzystania z niego w sposób wykraczający poza konieczny do stwierdzenia jego charakteru, cech i funkcjonowania (art. 34 ust. 4 UPK). Innymi słowy – możesz sprawdzić Towar tak, jak zrobiłbyś to w sklepie stacjonarnym, ale nie powinieneś go używać w normalny sposób, bo wówczas jego wartość spada i Sprzedawca może proporcjonalnie obniżyć zwracaną kwotę.</p>

<h2>Kiedy prawo odstąpienia nie przysługuje?</h2>
<p>Prawo odstąpienia nie przysługuje w odniesieniu do Towaru:</p>
<ul>
    <li>wykonanego według Twojej specyfikacji lub służącego zaspokojeniu Twoich zindywidualizowanych potrzeb (towar personalizowany/na zamówienie indywidualne);</li>
    <li>dostarczanego w zapieczętowanym opakowaniu, którego po otwarciu nie można zwrócić ze względu na ochronę zdrowia lub higienę, jeżeli opakowanie zostało otwarte po dostarczeniu.</li>
</ul>

<h2>Formularz odstąpienia od umowy (wzór)</h2>
<p><em>(Wypełnij i odeślij ten formularz tylko wtedy, gdy chcesz odstąpić od umowy)</em></p>
<p>
Adresat:<br>
LIDA Dariusz Cała – Moretti Fashion<br>
Nadrzeczna 14, GD Hala 5, Box A-07, 05-552<br>
E-mail: <a href="mailto:kontakt@morettifashion.com">kontakt@morettifashion.com</a>
</p>
<p>Niniejszym informuję o moim odstąpieniu od umowy sprzedaży następujących rzeczy:</p>
<p>
Nazwa Towaru: ………………………………………………………………………………<br>
Numer zamówienia: ………………………………………………………………………<br>
Data zawarcia umowy / data odbioru Towaru: ……………………………………<br>
Imię i nazwisko Konsumenta: ………………………………………………………<br>
Adres Konsumenta: ……………………………………………………………………<br>
Numer rachunku bankowego do zwrotu (opcjonalnie): ………………………<br><br>
Podpis Konsumenta (tylko jeżeli formularz przesyłany jest w formie papierowej):<br>
………………………………………<br><br>
Data: ………………………………
</p>

<h2>Kontakt w sprawach zwrotów</h2>
<p>W razie pytań: <a href="mailto:kontakt@morettifashion.com">kontakt@morettifashion.com</a> lub tel. <a href="tel:+48725538100">+48 725 538 100</a> (pon.–pt., godz. 9:00–17:00).</p>'
    ),
    'reklamacje' => array(
        'title' => 'Reklamacje',
        'content' => '
<p><em>Informacje dotyczące reklamacji zgodne z przepisami obowiązującymi od 1 stycznia 2023 r. (ustawa z dnia 4 listopada 2022 r. o zmianie ustawy o prawach konsumenta implementująca Dyrektywę towarową UE 2019/771).</em></p>

<h2>Podstawa prawna odpowiedzialności Sprzedawcy</h2>
<p>Sprzedawca jest odpowiedzialny wobec Konsumenta za <strong>niezgodność Towaru z umową</strong> istniejącą w chwili dostarczenia Towaru i ujawnioną w ciągu <strong>2 lat</strong> od tej chwili (art. 43a UPK). Jeżeli brak zgodności ujawnił się w ciągu 2 lat od dostarczenia Towaru, domniemywa się, że istniał on w chwili dostarczenia – ciężar udowodnienia czegoś przeciwnego spoczywa na Sprzedawcy.</p>
<p>Konsument nie jest zobowiązany do zgłaszania wady w żadnym szczególnym terminie – wystarczy, że nastąpi to w ciągu 2-letniego okresu odpowiedzialności.</p>

<h2>Kiedy Towar jest niezgodny z umową?</h2>
<p>Towar jest niezgodny z umową m.in. gdy:</p>
<ul>
    <li>nie odpowiada opisowi, rodzajowi, ilości lub jakości zaprezentowanej w Sklepie;</li>
    <li>nie posiada właściwości, o których zapewniała reklama lub etykieta;</li>
    <li>jest niekompletny lub uszkodzony;</li>
    <li>nie nadaje się do normalnego użytku danego rodzaju rzeczy.</li>
</ul>

<h2>Twoje uprawnienia – hierarchia żądań</h2>
<p><strong>W pierwszej kolejności</strong> możesz żądać (Twój wybór):</p>
<ul>
    <li><strong>Naprawy</strong> Towaru, albo</li>
    <li><strong>Wymiany</strong> Towaru na nowy wolny od wad.</li>
</ul>
<p>Sprzedawca wykona wybraną przez Ciebie czynność w rozsądnym czasie i bez nadmiernych niedogodności dla Ciebie. Sprzedawca pokrywa <strong>wszystkie koszty związane z naprawą lub wymianą</strong>, w tym koszty przesyłki. Sprzedawca może odmówić wybranego sposobu wyłącznie jeśli jest niemożliwy lub wymagałby nadmiernych kosztów – w takim przypadku stosuje alternatywny sposób.</p>

<p><strong>W drugiej kolejności</strong>, lub bezpośrednio gdy brak zgodności jest istotny, możesz żądać:</p>
<ul>
    <li><strong>Obniżenia ceny</strong> – proporcjonalnie do stopnia niezgodności Towaru; lub</li>
    <li><strong>Odstąpienia od umowy</strong> i zwrotu pełnej ceny.</li>
</ul>
<p>Uprawnienia drugorzędne przysługują w szczególności gdy: Sprzedawca odmówił naprawy i wymiany; Sprzedawca nie doprowadził Towaru do zgodności; brak zgodności wystąpił ponownie pomimo naprawy/wymiany; brak zgodności jest na tyle istotny, że uzasadnia natychmiastowe żądanie obniżki ceny lub zwrotu; Sprzedawca wyraźnie odmawia doprowadzenia Towaru do zgodności.</p>

<h2>Jak złożyć reklamację?</h2>
<ol>
    <li><strong>Wyślij zgłoszenie reklamacyjne</strong> na adres e-mail: <a href="mailto:kontakt@morettifashion.com">kontakt@morettifashion.com</a>. W zgłoszeniu podaj:
        <ul>
            <li>numer zamówienia;</li>
            <li>dokładny opis stwierdzonej niezgodności / wady;</li>
            <li>datę zakupu i datę stwierdzenia wady;</li>
            <li>swoje żądanie: <strong>naprawa / wymiana / obniżenie ceny (podaj kwotę) / odstąpienie od umowy</strong>;</li>
            <li>zdjęcia dokumentujące wadę (jeśli możliwe).</li>
        </ul>
    </li>
    <li><strong>Otrzymasz odpowiedź</strong> w terminie <strong>14 dni</strong> od dnia otrzymania zgłoszenia. Brak odpowiedzi w tym terminie uważany jest za uznanie reklamacji za zasadną (art. 7a UPK).</li>
    <li><strong>Odesłanie Towaru:</strong> jeśli rozpatrzenie reklamacji wymaga odesłania produktu, Sprzedawca poinformuje Cię o tym i zapewni lub pokryje koszty zwrotu. Nie odsyłaj Towaru bez wcześniejszego uzgodnienia ze Sprzedawcą.</li>
</ol>

<h2>Adres do korespondencji i odesłania towaru w ramach reklamacji</h2>
<p>
    <strong>LIDA Dariusz Cała – Moretti Fashion</strong><br>
    Nadrzeczna 14, GD Hala 5, Box A-07<br>
    05-552<br>
    E-mail: <a href="mailto:kontakt@morettifashion.com">kontakt@morettifashion.com</a>
</p>

<h2>Pozasądowe rozwiązywanie sporów konsumenckich (ADR/ODR)</h2>
<p>Jeśli nie jesteś zadowolony ze sposobu rozpatrzenia reklamacji, możesz skorzystać z bezpłatnych pozasądowych metod rozstrzygania sporów:</p>
<ul>
    <li><strong>Mediacja</strong> prowadzona przez Wojewódzki Inspektorat Inspekcji Handlowej;</li>
    <li><strong>Stały Polubowny Sąd Konsumencki</strong> przy Inspektoracie Inspekcji Handlowej;</li>
    <li><strong>Pomoc rzecznika konsumentów</strong> – powiatowy lub miejski rzecznik konsumentów;</li>
    <li><strong>UOKiK</strong> – informacje pod adresem <a href="https://www.uokik.gov.pl" target="_blank" rel="noopener noreferrer">www.uokik.gov.pl</a>.</li>
</ul>
<p>Zgodnie z rozporządzeniem (UE) nr 524/2013 udostępniamy link do unijnej platformy internetowego rozstrzygania sporów (ODR): <a href="https://ec.europa.eu/consumers/odr/" target="_blank" rel="noopener noreferrer">https://ec.europa.eu/consumers/odr/</a>. Adres e-mail Sprzedawcy do celów ODR: <a href="mailto:kontakt@morettifashion.com">kontakt@morettifashion.com</a>.</p>
<p>W przypadku odrzucenia reklamacji Sprzedawca poinformuje Cię, czy wyraża zgodę na udział w postępowaniu ADR.</p>

<h2>Kontakt</h2>
<p><a href="mailto:kontakt@morettifashion.com">kontakt@morettifashion.com</a> | tel. <a href="tel:+48725538100">+48 725 538 100</a> (pon.–pt., godz. 9:00–17:00)</p>'
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
