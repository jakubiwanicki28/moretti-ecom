<?php
/**
 * Footer template - STYNRA style overhaul
 * 
 * @package Moretti
 */
?>

<?php
$moretti_footer_page_url = static function (array $slugs, $fallback = '/') {
    foreach ($slugs as $slug) {
        $page = get_page_by_path($slug, OBJECT, 'page');
        if ($page instanceof WP_Post && $page->post_status === 'publish') {
            $permalink = get_permalink($page->ID);
            if (!empty($permalink)) {
                return $permalink;
            }
        }
    }

    return home_url($fallback);
};
?>

<footer style="background-color: #ffffff; padding-top: 80px;">
    <div style="max-width: 1280px; margin: 0 auto; padding-left: 48px; padding-right: 48px;">
        <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(250px, 1fr)); gap: 48px; padding-bottom: 64px;">
            
            <!-- Column 1: O NAS -->
            <div>
                <h4 style="font-size: 11px; font-weight: 700; letter-spacing: 0.2em; color: #2a2826; margin-bottom: 32px; text-transform: uppercase;">O NAS</h4>
                <div style="font-size: 13px; color: #766a5d; letter-spacing: 0.02em; line-height: 1.8; font-weight: 400;">
                    <p style="margin: 0;">Moretti to marka stworzona z myślą o osobach ceniących klasyczną elegancję i najwyższą jakość. Nasze portfele to połączenie ponadczasowego designu z dbałością o każdy detal. Wierzymy, że przedmioty codziennego użytku powinny być nie tylko funkcjonalne, ale również stanowić wyraz Twojego osobistego stylu.</p>
                </div>
            </div>

            <!-- Column 2: KONTAKT -->
            <div>
                <h4 style="font-size: 11px; font-weight: 700; letter-spacing: 0.2em; color: #2a2826; margin-bottom: 32px; text-transform: uppercase;">Kontakt</h4>
                <div style="font-size: 13px; color: #766a5d; text-transform: uppercase; letter-spacing: 0.05em; line-height: 1.9; font-weight: 500;">
                    <p style="margin: 0 0 6px 0;"><a href="mailto:kontakt@morettifashion.com" style="color: #766a5d; text-decoration: none;">kontakt@morettifashion.com</a></p>
                    <p style="margin: 0 0 6px 0;"><a href="tel:+48725538100" style="color: #766a5d; text-decoration: none;">TEL: (+48) 725 538 100</a></p>
                    <div style="display: flex; flex-direction: column; gap: 12px; margin-top: 24px;">
                        <a href="https://www.instagram.com/morettigalanteria/" target="_blank" style="display: flex; align-items: center; gap: 10px; font-size: 11px; color: #2a2826; text-decoration: none; text-transform: uppercase; letter-spacing: 0.1em; font-weight: 700;" onmouseover="this.style.opacity='0.7'" onmouseout="this.style.opacity='1'">
                            <svg width="18" height="18" fill="currentColor" viewBox="0 0 24 24"><path d="M12 2.163c3.204 0 3.584.012 4.85.07 3.252.148 4.771 1.691 4.919 4.919.058 1.265.069 1.645.069 4.849 0 3.205-.012 3.584-.069 4.849-.149 3.225-1.664 4.771-4.919 4.919-1.266.058-1.644.07-4.85.07-3.204 0-3.584-.012-4.849-.07-3.26-.149-4.771-1.699-4.919-4.92-.058-1.265-.07-1.644-.07-4.849 0-3.204.013-3.583.07-4.849.149-3.227 1.664-4.771 4.919-4.919 1.266-.057 1.645-.069 4.849-.069zM12 0C8.741 0 8.333.014 7.053.072 2.695.272.273 2.69.073 7.052.014 8.333 0 8.741 0 12c0 3.259.014 3.668.072 4.948.2 4.358 2.618 6.78 6.98 6.98 1.281.058 1.689.072 4.948.072 3.259 0 3.668-.014 4.948-.072 4.354-.2 6.782-2.618 6.979-6.98.059-1.28.073-1.689.073-4.948 0-3.259-.014-3.668-.072-4.947-.196-4.354-2.617-6.78-6.979-6.98C15.668.014 15.259 0 12 0zm0 5.838a6.162 6.162 0 100 12.324 6.162 6.162 0 000-12.324zM12 16a4.162 6.162 0 110-8.324 4.162 4.162 0 010 8.324zM18.406 4.406a1.44 1.44 0 100 2.88 1.44 1.44 0 000-2.88z"/></svg>
                            Instagram
                        </a>
                        <a href="https://www.facebook.com/profile.php?id=100094137496882" target="_blank" rel="noopener noreferrer" style="display: flex; align-items: center; gap: 10px; font-size: 11px; color: #2a2826; text-decoration: none; text-transform: uppercase; letter-spacing: 0.1em; font-weight: 700;" onmouseover="this.style.opacity='0.7'" onmouseout="this.style.opacity='1'">
                            <svg width="18" height="18" fill="currentColor" viewBox="0 0 24 24"><path d="M24 12.073c0-6.627-5.373-12-12-12s-12 5.373-12 12c0 5.99 4.388 10.954 10.125 11.854v-8.385H7.078v-3.47h3.047V9.43c0-3.007 1.792-4.669 4.533-4.669 1.312 0 2.686.235 2.686.235v2.953H15.83c-1.491 0-1.956.925-1.956 1.874v2.25h3.328l-.532 3.47h-2.796v8.385C19.612 23.027 24 18.062 24 12.073z"/></svg>
                            Facebook
                        </a>
                        <a href="#" style="display: flex; align-items: center; gap: 10px; font-size: 11px; color: #2a2826; text-decoration: none; text-transform: uppercase; letter-spacing: 0.1em; font-weight: 700;" onmouseover="this.style.opacity='0.7'" onmouseout="this.style.opacity='1'">
                            <svg width="18" height="18" fill="currentColor" viewBox="0 0 24 24"><path d="M19.59 6.69a4.83 4.83 0 01-3.77-4.25V2h-3.45v13.67a2.89 2.89 0 01-2.88 2.5 2.89 2.89 0 01-2.89-2.89 2.89 2.89 0 012.89-2.89c.28 0 .54.04.79.1V9.01a6.33 6.33 0 00-.79-.05 6.34 6.34 0 00-6.34 6.34 6.34 6.34 0 006.34 6.34 6.34 6.34 0 006.33-6.34V8.69a8.27 8.27 0 004.84 1.55V6.79a4.85 4.85 0 01-1.07-.1z"/></svg>
                            TikTok
                        </a>
                    </div>
                </div>
            </div>

            <!-- Column 3: REGULAMIN -->
            <div>
                <h4 style="font-size: 11px; font-weight: 700; letter-spacing: 0.2em; color: #2a2826; margin-bottom: 32px; text-transform: uppercase;">Regulamin</h4>
                <ul style="list-style: none; padding: 0; margin: 0;">
                    <li style="margin-bottom: 14px;"><a href="<?php echo esc_url($moretti_footer_page_url(array('regulamin-sklepu'))); ?>" style="font-size: 13px; text-transform: uppercase; letter-spacing: 0.05em; color: #766a5d; text-decoration: none; transition: color 0.2s; font-weight: 500;" onmouseover="this.style.color='#2a2826'" onmouseout="this.style.color='#766a5d'">Regulamin sklepu</a></li>
                    <li><a href="<?php echo esc_url($moretti_footer_page_url(array('polityka-prywatnosci'))); ?>" style="font-size: 13px; text-transform: uppercase; letter-spacing: 0.05em; color: #766a5d; text-decoration: none; transition: color 0.2s; font-weight: 500;" onmouseover="this.style.color='#2a2826'" onmouseout="this.style.color='#766a5d'">Polityka prywatności</a></li>
                </ul>
            </div>

            <!-- Column 4: INFORMACJE -->
            <div>
                <h4 style="font-size: 11px; font-weight: 700; letter-spacing: 0.2em; color: #2a2826; margin-bottom: 32px; text-transform: uppercase;">Informacje</h4>
                <ul style="list-style: none; padding: 0; margin: 0;">
                    <li style="margin-bottom: 14px;"><a href="<?php echo esc_url($moretti_footer_page_url(array('dostawa-i-platnosci', 'koszty-dostawy'))); ?>" style="font-size: 13px; text-transform: uppercase; letter-spacing: 0.05em; color: #766a5d; text-decoration: none; transition: color 0.2s; font-weight: 500;" onmouseover="this.style.color='#2a2826'" onmouseout="this.style.color='#766a5d'">Dostawa i płatności</a></li>
                    <li style="margin-bottom: 14px;"><a href="<?php echo esc_url($moretti_footer_page_url(array('zwroty'))); ?>" style="font-size: 13px; text-transform: uppercase; letter-spacing: 0.05em; color: #766a5d; text-decoration: none; transition: color 0.2s; font-weight: 500;" onmouseover="this.style.color='#2a2826'" onmouseout="this.style.color='#766a5d'">Zwroty</a></li>
                    <li><a href="<?php echo esc_url($moretti_footer_page_url(array('reklamacje'))); ?>" style="font-size: 13px; text-transform: uppercase; letter-spacing: 0.05em; color: #766a5d; text-decoration: none; transition: color 0.2s; font-weight: 500;" onmouseover="this.style.color='#2a2826'" onmouseout="this.style.color='#766a5d'">Reklamacje</a></li>
                </ul>
                <div style="margin-top: 32px; padding-top: 24px; border-top: 1px solid #f3f4f6;">
                    <a href="https://portfelland.pl/" target="_blank" rel="noopener" style="display: inline-flex; align-items: center; gap: 8px; font-size: 11px; font-weight: 700; text-transform: uppercase; letter-spacing: 0.15em; color: #2a2826; text-decoration: none; transition: opacity 0.2s;" onmouseover="this.style.opacity='0.6'" onmouseout="this.style.opacity='1'">
                        <svg width="12" height="12" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"></path></svg>
                        Sprzedaż hurtowa
                    </a>
                </div>
            </div>
        </div>

        <!-- Copyright Bar -->
        <div style="display: flex; flex-wrap: wrap; justify-content: space-between; align-items: center; padding: 32px 0; border-top: 1px solid #f3f4f6; gap: 24px;">
            <p style="font-size: 9px; color: #a8a09d; text-transform: uppercase; letter-spacing: 0.15em; margin: 0; font-weight: 600;">
                &copy; <?php echo date('Y'); ?> MORETTI. WSZELKIE PRAWA ZASTRZEŻONE. POWERED BY MORETTI.
            </p>

            <p style="font-size: 9px; color: #a8a09d; text-transform: uppercase; letter-spacing: 0.15em; margin: 0; font-weight: 600;">
                DESIGN & DEVELOPED BY VISUAL CONTENT
            </p>
        </div>
    </div>

    <!-- HUGE LOGO TEXT -->
    <div style="text-align: center; user-select: none; pointer-events: none; padding: 96px 48px; font-size: 15vw; font-weight: 700; line-height: 1; letter-spacing: -0.05em; color: #2a2826; opacity: 0.05; overflow: hidden; white-space: nowrap;">
        MORETTI
    </div>
</footer>

<?php wp_footer(); ?>

<!-- ===== WISHLIST DRAWER ===== -->
<div id="moretti-wishlist-overlay"
     onclick="if(event.target===this) morettiCloseWishlist()"
     style="display:none; position:fixed; inset:0; background:rgba(0,0,0,0.45); z-index:9998;"></div>

<div id="moretti-wishlist-drawer"
     style="display:none; position:fixed; top:0; right:0; bottom:0; width:min(520px,100vw);
            background:#fff; z-index:9999; overflow-y:auto; box-shadow:-4px 0 32px rgba(0,0,0,0.15);
            flex-direction:column;">

    <!-- Header -->
    <div style="display:flex; align-items:center; justify-content:space-between;
                padding:24px 28px; border-bottom:1px solid #f0ede9; position:sticky; top:0; background:#fff; z-index:1;">
        <h2 style="margin:0; font-size:13px; font-weight:700; letter-spacing:0.2em; text-transform:uppercase; color:#2a2826;">
            Ulubione <span id="wishlist-drawer-count" style="font-weight:400; color:#766a5d;"></span>
        </h2>
        <button onclick="morettiCloseWishlist()"
                style="background:none; border:none; cursor:pointer; padding:4px; color:#2a2826; line-height:1;">
            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5">
                <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/>
            </svg>
        </button>
    </div>

    <!-- Body -->
    <div id="moretti-wishlist-body" style="padding:24px 28px; flex:1;">
        <div id="wishlist-loading" style="text-align:center; padding:60px 0; color:#766a5d; font-size:13px;">
            Ładowanie...
        </div>
    </div>
</div>

<script>
function morettiOpenWishlist(e) {
    if (e) e.preventDefault();

    var overlay = document.getElementById('moretti-wishlist-overlay');
    var drawer  = document.getElementById('moretti-wishlist-drawer');
    var body    = document.getElementById('moretti-wishlist-body');
    var countEl = document.getElementById('wishlist-drawer-count');

    overlay.style.display = 'block';
    drawer.style.display  = 'flex';
    document.body.style.overflow = 'hidden';

    // Odczytaj IDs z cookie
    var ids = [];
    try {
        var row = document.cookie.split('; ').find(function(r){ return r.startsWith('moretti_wishlist='); });
        if (row) ids = JSON.parse(decodeURIComponent(row.split('=')[1])) || [];
    } catch(ex) { ids = []; }

    ids = ids.map(Number).filter(function(n){ return n > 0; });

    if (ids.length === 0) {
        countEl.textContent = '';
        body.innerHTML =
            '<div style="text-align:center; padding:80px 24px; color:#766a5d;">' +
            '<svg style="width:48px;height:48px;margin:0 auto 20px;display:block;opacity:.25" fill="none" stroke="currentColor" viewBox="0 0 24 24">' +
            '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"/></svg>' +
            '<p style="font-size:13px;font-weight:700;letter-spacing:.1em;text-transform:uppercase;color:#2a2826;margin-bottom:8px">Brak ulubionych</p>' +
            '<p style="font-size:13px;margin-bottom:28px">Kliknij serce na produkcie, aby dodać go do ulubionych.</p>' +
            '<a href="' + (typeof woocommerce_params !== "undefined" ? woocommerce_params.shop_url : "/sklep/") + '" ' +
            'onclick="morettiCloseWishlist()" ' +
            'style="display:inline-block;background:#2a2826;color:#fff;font-size:11px;font-weight:700;letter-spacing:.15em;text-transform:uppercase;padding:13px 28px;text-decoration:none;">' +
            'Przejdź do sklepu</a>' +
            '</div>';
        return;
    }

    countEl.textContent = '(' + ids.length + ')';
    body.innerHTML = '<div id="wishlist-loading" style="text-align:center;padding:60px 0;color:#766a5d;font-size:13px;">Ładowanie...</div>';

    // AJAX – pobierz HTML produktów
    var form = new FormData();
    form.append('action', 'moretti_wishlist_products');
    form.append('ids', ids.join(','));

    fetch('<?php echo esc_url(admin_url("admin-ajax.php")); ?>', { method: 'POST', body: form })
        .then(function(r){ return r.json(); })
        .then(function(data) {
            if (data.success && data.data.html) {
                body.innerHTML =
                    '<ul class="products" style="list-style:none;padding:0;margin:0;display:grid;' +
                    'grid-template-columns:repeat(auto-fill,minmax(200px,1fr));gap:20px;">' +
                    data.data.html + '</ul>';
            } else {
                body.innerHTML = '<p style="text-align:center;padding:40px 0;color:#766a5d;font-size:13px;">Brak produktów.</p>';
            }
        })
        .catch(function() {
            body.innerHTML = '<p style="text-align:center;padding:40px 0;color:#e2401c;font-size:13px;">Błąd ładowania. Odśwież stronę.</p>';
        });
}

function morettiCloseWishlist() {
    document.getElementById('moretti-wishlist-overlay').style.display = 'none';
    document.getElementById('moretti-wishlist-drawer').style.display  = 'none';
    document.body.style.overflow = '';
}

document.addEventListener('keydown', function(e){
    if (e.key === 'Escape') morettiCloseWishlist();
});
</script>

<script>
    // ========================================
    // MOBILE MENU - Slide-in/slide-out
    // ========================================
    const mobileMenuLink = document.getElementById('mobile-menu-link');
    const mobileMenu = document.getElementById('mobile-menu');
    const mobileMenuClose = document.getElementById('mobile-menu-close');
    const mobileMenuOverlay = document.getElementById('mobile-menu-overlay');
    const mobileMenuLinks = document.querySelectorAll('.mobile-menu-link');

    // Open menu
    mobileMenuLink?.addEventListener('click', function(e) {
        e.preventDefault();
        mobileMenu.classList.remove('-translate-x-full');
        mobileMenuOverlay.classList.remove('hidden');
        document.body.style.overflow = 'hidden';
    });

    // Close menu function
    function closeMobileMenu() {
        mobileMenu.classList.add('-translate-x-full');
        mobileMenuOverlay.classList.add('hidden');
        document.body.style.overflow = '';
    }

    // Close button
    mobileMenuClose?.addEventListener('click', closeMobileMenu);

    // Overlay click
    mobileMenuOverlay?.addEventListener('click', closeMobileMenu);

    // Close menu when clicking any link
    mobileMenuLinks.forEach(link => {
        link.addEventListener('click', function() {
            setTimeout(closeMobileMenu, 150);
        });
    });

    // ========================================
    // MOBILE SEARCH - Full-screen overlay
    // ========================================
    const searchToggleMobile = document.getElementById('search-toggle-mobile');
    const searchOverlayMobile = document.getElementById('search-overlay-mobile');
    const searchCloseMobile = document.getElementById('search-close-mobile');
    const searchInputMobile = document.getElementById('search-input-mobile');

    // Open mobile search
    searchToggleMobile?.addEventListener('click', function(e) {
        e.preventDefault();
        searchOverlayMobile.classList.remove('translate-y-full');
        document.body.style.overflow = 'hidden';
        // Focus input after animation
        setTimeout(() => {
            searchInputMobile?.focus();
        }, 300);
    });

    // Close mobile search
    function closeMobileSearch() {
        searchOverlayMobile.classList.add('translate-y-full');
        document.body.style.overflow = '';
    }

    searchCloseMobile?.addEventListener('click', closeMobileSearch);

    // ========================================
    // DESKTOP SEARCH - Dropdown bar
    // ========================================
    const searchToggleDesktop = document.getElementById('search-toggle-desktop');
    const searchBarDesktop = document.getElementById('search-bar-desktop');
    const searchCloseDesktop = document.getElementById('search-close-desktop');
    const searchInputDesktop = document.getElementById('search-input-desktop');

    // Open desktop search
    searchToggleDesktop?.addEventListener('click', function(e) {
        e.preventDefault();
        if (searchBarDesktop.style.display === 'none' || searchBarDesktop.style.display === '') {
            searchBarDesktop.style.display = 'block';
            setTimeout(() => {
                searchInputDesktop?.focus();
            }, 100);
        } else {
            searchBarDesktop.style.display = 'none';
        }
    });

    // Close desktop search
    searchCloseDesktop?.addEventListener('click', function() {
        searchBarDesktop.style.display = 'none';
    });
</script>

</body>
</html>
