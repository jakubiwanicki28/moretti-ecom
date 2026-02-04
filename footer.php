<?php
/**
 * Footer template - STYNRA style overhaul
 * 
 * @package Moretti
 */
?>

<footer class="bg-white pt-20">
    <div class="container mx-auto px-4">
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-12 pb-16">
            
            <!-- Column 1: Links -->
            <div>
                <h4 class="text-[10px] font-bold tracking-[0.2em] text-charcoal mb-8 uppercase">Strony Główne</h4>
                <ul class="space-y-4 text-xs uppercase tracking-widest text-taupe-600">
                    <li><a href="<?php echo home_url(); ?>" class="hover:text-charcoal transition-colors">Start</a></li>
                    <li><a href="<?php echo get_permalink(wc_get_page_id('shop')); ?>" class="hover:text-charcoal transition-colors">Sklep</a></li>
                    <li><a href="/o-nas" class="hover:text-charcoal transition-colors">O nas</a></li>
                    <li><a href="/kontakt" class="hover:text-charcoal transition-colors">Kontakt</a></li>
                    <li><a href="/faq" class="hover:text-charcoal transition-colors">FAQ</a></li>
                </ul>
            </div>

            <!-- Column 2: Inner Pages -->
            <div>
                <h4 class="text-[10px] font-bold tracking-[0.2em] text-charcoal mb-8 uppercase">Klienci</h4>
                <ul class="space-y-4 text-xs uppercase tracking-widest text-taupe-600">
                    <li><a href="<?php echo get_permalink(get_option('woocommerce_myaccount_page_id')); ?>" class="hover:text-charcoal transition-colors">Logowanie</a></li>
                    <li><a href="<?php echo get_permalink(get_option('woocommerce_myaccount_page_id')); ?>" class="hover:text-charcoal transition-colors">Rejestracja</a></li>
                    <li><a href="/polityka-prywatnosci" class="hover:text-charcoal transition-colors">Polityka prywatności</a></li>
                    <li><a href="/regulamin" class="hover:text-charcoal transition-colors">Regulamin</a></li>
                </ul>
            </div>

            <!-- Column 3: Info -->
            <div>
                <h4 class="text-[10px] font-bold tracking-[0.2em] text-charcoal mb-8 uppercase">Pracownia</h4>
                <div class="text-xs text-taupe-600 space-y-4 uppercase tracking-widest">
                    <p>ul. Kaletnicza 15<br>00-001 Warszawa, PL</p>
                    <p>Pon - Pt: 09:00 — 17:00</p>
                </div>
            </div>

            <!-- Column 4: Newsletter -->
            <div>
                <h4 class="text-[10px] font-bold tracking-[0.2em] text-charcoal mb-8 uppercase">Zapisz się do newslettera</h4>
                <form class="relative group">
                    <input 
                        type="email" 
                        placeholder="EMAIL@MORETTI.COM"
                        class="w-full bg-transparent border-b border-gray-300 focus:border-charcoal py-2 text-xs focus:outline-none transition-colors"
                    >
                    <button type="submit" class="absolute right-0 top-1/2 -translate-y-1/2 text-[10px] font-bold uppercase tracking-widest hover:text-taupe-600">Zapisz</button>
                </form>
            </div>
        </div>

        <!-- Social & Copyright Bar -->
        <div class="flex flex-col md:flex-row justify-between items-center py-8 border-t border-gray-100 gap-6">
            <div class="flex gap-6">
                <a href="#" class="text-xs text-taupe-600 hover:text-charcoal uppercase tracking-widest font-bold">Facebook</a>
                <a href="#" class="text-xs text-taupe-600 hover:text-charcoal uppercase tracking-widest font-bold">Instagram</a>
                <a href="#" class="text-xs text-taupe-600 hover:text-charcoal uppercase tracking-widest font-bold">LinkedIn</a>
            </div>
            
            <p class="text-[10px] text-taupe-400 uppercase tracking-[0.2em]">
                &copy; <?php echo date('Y'); ?> MORETTI. WSZELKIE PRAWA ZASTRZEŻONE. POWERED BY MORETTI.
            </p>
            
            <p class="text-[10px] text-taupe-400 uppercase tracking-[0.2em]">
                DESIGN & DEVELOPED BY CTO AI
            </p>
        </div>
    </div>

    <!-- HUGE LOGO TEXT (Screenshot 11) -->
    <div class="footer-big-text text-center select-none">
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
