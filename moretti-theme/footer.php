<?php
/**
 * Footer template - STYNRA style overhaul
 * 
 * @package Moretti
 */
?>

<footer style="background-color: #ffffff; padding-top: 80px;">
    <div style="max-width: 1280px; margin: 0 auto; padding-left: 48px; padding-right: 48px;">
        <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(250px, 1fr)); gap: 48px; padding-bottom: 64px;">
            
            <!-- Column 1: Links -->
            <div>
                <h4 style="font-size: 11px; font-weight: 700; letter-spacing: 0.2em; color: #2a2826; margin-bottom: 32px; text-transform: uppercase;">Strony Główne</h4>
                <ul style="list-style: none; padding: 0; margin: 0;">
                    <li style="margin-bottom: 14px;"><a href="<?php echo home_url(); ?>" style="font-size: 13px; text-transform: uppercase; letter-spacing: 0.05em; color: #766a5d; text-decoration: none; transition: color 0.2s; font-weight: 500;" onmouseover="this.style.color='#2a2826'" onmouseout="this.style.color='#766a5d'">Start</a></li>
                    <li style="margin-bottom: 14px;"><a href="<?php echo get_permalink(wc_get_page_id('shop')); ?>" style="font-size: 13px; text-transform: uppercase; letter-spacing: 0.05em; color: #766a5d; text-decoration: none; transition: color 0.2s; font-weight: 500;" onmouseover="this.style.color='#2a2826'" onmouseout="this.style.color='#766a5d'">Sklep</a></li>
                    <li style="margin-bottom: 14px;"><a href="/o-nas" style="font-size: 13px; text-transform: uppercase; letter-spacing: 0.05em; color: #766a5d; text-decoration: none; transition: color 0.2s; font-weight: 500;" onmouseover="this.style.color='#2a2826'" onmouseout="this.style.color='#766a5d'">O nas</a></li>
                    <li style="margin-bottom: 14px;"><a href="/kontakt" style="font-size: 13px; text-transform: uppercase; letter-spacing: 0.05em; color: #766a5d; text-decoration: none; transition: color 0.2s; font-weight: 500;" onmouseover="this.style.color='#2a2826'" onmouseout="this.style.color='#766a5d'">Kontakt</a></li>
                    <li><a href="/faq" style="font-size: 13px; text-transform: uppercase; letter-spacing: 0.05em; color: #766a5d; text-decoration: none; transition: color 0.2s; font-weight: 500;" onmouseover="this.style.color='#2a2826'" onmouseout="this.style.color='#766a5d'">FAQ</a></li>
                </ul>
            </div>

            <!-- Column 2: Inner Pages -->
            <div>
                <h4 style="font-size: 11px; font-weight: 700; letter-spacing: 0.2em; color: #2a2826; margin-bottom: 32px; text-transform: uppercase;">Klienci</h4>
                <ul style="list-style: none; padding: 0; margin: 0;">
                    <li style="margin-bottom: 14px;"><a href="/polityka-prywatnosci" style="font-size: 13px; text-transform: uppercase; letter-spacing: 0.05em; color: #766a5d; text-decoration: none; transition: color 0.2s; font-weight: 500;" onmouseover="this.style.color='#2a2826'" onmouseout="this.style.color='#766a5d'">Polityka prywatności</a></li>
                    <li><a href="/regulamin" style="font-size: 13px; text-transform: uppercase; letter-spacing: 0.05em; color: #766a5d; text-decoration: none; transition: color 0.2s; font-weight: 500;" onmouseover="this.style.color='#2a2826'" onmouseout="this.style.color='#766a5d'">Regulamin</a></li>
                </ul>
            </div>

            <!-- Column 3: Info -->
            <div>
                <h4 style="font-size: 11px; font-weight: 700; letter-spacing: 0.2em; color: #2a2826; margin-bottom: 32px; text-transform: uppercase;">Pracownia</h4>
                <div style="font-size: 13px; color: #766a5d; text-transform: uppercase; letter-spacing: 0.05em; line-height: 1.7; font-weight: 500;">
                    <p style="margin: 0 0 14px 0;">ul. Kaletnicza 15<br>00-001 Warszawa, PL</p>
                    <p style="margin: 0;">Pon - Pt: 09:00 — 17:00</p>
                </div>
            </div>

            <!-- Column 4: Newsletter -->
            <div>
                <h4 style="font-size: 11px; font-weight: 700; letter-spacing: 0.2em; color: #2a2826; margin-bottom: 32px; text-transform: uppercase;">Zapisz się do newslettera</h4>
                <form style="position: relative;">
                    <input 
                        type="email" 
                        placeholder="EMAIL@MORETTI.COM"
                        style="width: 100%; background-color: transparent; border: none; border-bottom: 1px solid #d1d5db; padding: 14px 90px 14px 0; font-size: 12px; color: #2a2826; font-family: inherit; transition: border-color 0.2s; letter-spacing: 0.05em;"
                        onfocus="this.style.borderColor='#2a2826'; this.style.outline='none';"
                        onblur="this.style.borderColor='#d1d5db';"
                    >
                    <button type="submit" style="position: absolute; right: 0; top: 50%; transform: translateY(-50%); font-size: 11px; font-weight: 700; text-transform: uppercase; letter-spacing: 0.15em; color: #2a2826; background: none; border: none; cursor: pointer; padding: 0; transition: color 0.2s;" onmouseover="this.style.color='#766a5d'" onmouseout="this.style.color='#2a2826'">Zapisz</button>
                </form>
            </div>
        </div>

        <!-- Social & Copyright Bar -->
        <div style="display: flex; flex-wrap: wrap; justify-content: space-between; align-items: center; padding: 32px 0; border-top: 1px solid #f3f4f6; gap: 24px;">
            <div style="display: flex; gap: 24px;">
                <a href="#" style="font-size: 12px; color: #766a5d; text-decoration: none; text-transform: uppercase; letter-spacing: 0.1em; font-weight: 700; transition: color 0.2s;" onmouseover="this.style.color='#2a2826'" onmouseout="this.style.color='#766a5d'">Facebook</a>
                <a href="#" style="font-size: 12px; color: #766a5d; text-decoration: none; text-transform: uppercase; letter-spacing: 0.1em; font-weight: 700; transition: color 0.2s;" onmouseover="this.style.color='#2a2826'" onmouseout="this.style.color='#766a5d'">Instagram</a>
                <a href="#" style="font-size: 12px; color: #766a5d; text-decoration: none; text-transform: uppercase; letter-spacing: 0.1em; font-weight: 700; transition: color 0.2s;" onmouseover="this.style.color='#2a2826'" onmouseout="this.style.color='#766a5d'">LinkedIn</a>
            </div>
            
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

<script>
    // Mobile menu toggle
    document.getElementById('mobile-menu-link')?.addEventListener('click', function(e) {
        e.preventDefault();
        const menu = document.getElementById('mobile-menu');
        menu.classList.toggle('hidden');
    });
</script>

</body>
</html>
