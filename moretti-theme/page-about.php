<?php
/**
 * Template Name: About Page
 * 
 * @package Moretti
 */

get_header(); ?>

<div class="about-page-wrapper bg-white">
    <!-- Hero Section -->
    <section class="relative pt-20 pb-20 md:pt-32 md:pb-32 border-b border-gray-100">
        <div class="container mx-auto px-4">
            <div class="max-w-6xl mx-auto">
                <span class="text-[10px] uppercase tracking-[0.4em] text-taupe-500 mb-8 block font-medium">O NASZEJ MARCE</span>
                <h1 class="text-5xl md:text-7xl lg:text-9xl font-bold text-charcoal uppercase tracking-tighter leading-[0.85] mb-20">
                    POLSKIE RZEMIOSŁO<br/>
                    <span class="text-taupe-300">NOWOCZESNY DESIGN</span>
                </h1>
                <div class="grid md:grid-cols-2 gap-16 md:gap-24 items-start">
                    <div class="text-xl md:text-2xl text-charcoal leading-tight font-bold uppercase tracking-tight">
                        Wierzymy, że portfel to coś więcej niż przedmiot. To towarzysz codzienności, świadek sukcesów i strażnik Twoich najważniejszych rzeczy.
                    </div>
                    <div class="text-taupe-600 text-base leading-relaxed">
                        Moretti narodziło się z pasji do tradycyjnego kaletnictwa. Łączymy dekady doświadczenia w obróbce skóry z minimalistyczną estetyką jutra. Każdy produkt opuszczający naszą pracownię to obietnica trwałości, która przetrwa próbę czasu i trendów.
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Values & Philosophy -->
    <section class="py-24 md:py-48 bg-white">
        <div class="container mx-auto px-4">
            <div class="max-w-6xl mx-auto">
                <div class="grid lg:grid-cols-3 gap-16 md:gap-32">
                    <div class="space-y-6">
                        <span class="text-xs font-bold uppercase tracking-[0.3em] text-taupe-400">01 / JAKOŚĆ</span>
                        <h3 class="text-2xl md:text-3xl font-bold uppercase tracking-tight leading-none">SKÓRA LICOWA</h3>
                        <p class="text-taupe-600 text-base leading-relaxed">
                            Wykorzystujemy wyłącznie najwyższej klasy skórę licową z polskich i włoskich garbarni. Materiał ten z czasem nabiera unikalnej patyny, stając się jeszcze szlachetniejszym z każdym rokiem użytkowania.
                        </p>
                    </div>
                    <div class="space-y-6">
                        <span class="text-xs font-bold uppercase tracking-[0.3em] text-taupe-400">02 / TECHNOLOGIA</span>
                        <h3 class="text-2xl md:text-3xl font-bold uppercase tracking-tight leading-none">OCHRONA RFID</h3>
                        <p class="text-taupe-600 text-base leading-relaxed">
                            Współczesność wymaga bezpieczeństwa. Każdy nasz portfel wyposażony jest w ultra-cienkie membrany blokujące sygnały RFID, chroniąc Twoje dane i środki przed nieautoryzowanym skanowaniem.
                        </p>
                    </div>
                    <div class="space-y-6">
                        <span class="text-xs font-bold uppercase tracking-[0.3em] text-taupe-400">03 / ETYKA</span>
                        <h3 class="text-2xl md:text-3xl font-bold uppercase tracking-tight leading-none">LOCAL FIRST</h3>
                        <p class="text-taupe-600 text-base leading-relaxed">
                            Cały proces projektowania i produkcji odbywa się w Polsce. Wspieramy lokalnych rzemieślników i dbamy o to, by nasz ślad węglowy był jak najmniejszy, stawiając na transparentność i uczciwość.
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Stats Section -->
    <section style="padding: 100px 0; background-color: #2a2826; color: #ffffff; overflow: hidden; margin: 40px 0;">
        <div style="max-width: 1280px; margin: 0 auto; padding: 0 48px;">
            <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(180px, 1fr)); gap: 40px; text-align: center;">
                <div style="display: flex; flex-direction: column; gap: 12px;">
                    <div style="font-size: 48px; font-weight: 700; letter-spacing: -0.02em; line-height: 1;">15+</div>
                    <div style="font-size: 9px; text-transform: uppercase; letter-spacing: 0.3em; color: #8f8275; font-weight: 700;">Lat Tradycji</div>
                </div>
                <div style="display: flex; flex-direction: column; gap: 12px;">
                    <div style="font-size: 48px; font-weight: 700; letter-spacing: -0.02em; line-height: 1;">48h</div>
                    <div style="font-size: 9px; text-transform: uppercase; letter-spacing: 0.3em; color: #8f8275; font-weight: 700;">Czas Produkcji</div>
                </div>
                <div style="display: flex; flex-direction: column; gap: 12px;">
                    <div style="font-size: 48px; font-weight: 700; letter-spacing: -0.02em; line-height: 1;">10k+</div>
                    <div style="font-size: 9px; text-transform: uppercase; letter-spacing: 0.3em; color: #8f8275; font-weight: 700;">Uszytych Portfeli</div>
                </div>
                <div style="display: flex; flex-direction: column; gap: 12px;">
                    <div style="font-size: 48px; font-weight: 700; letter-spacing: -0.02em; line-height: 1;">100%</div>
                    <div style="font-size: 9px; text-transform: uppercase; letter-spacing: 0.3em; color: #8f8275; font-weight: 700;">Gwarancja Jakości</div>
                </div>
            </div>
        </div>
    </section>

    <!-- Final CTA -->
    <section class="py-48 md:py-72 text-center bg-white">
        <div class="container mx-auto px-4">
            <div class="max-w-4xl mx-auto">
                <h2 class="text-5xl md:text-8xl font-bold uppercase tracking-tighter leading-none mb-16">GOTOWY NA ZMIANĘ?</h2>
                <a href="<?php echo esc_url(get_permalink(wc_get_page_id('shop'))); ?>" class="inline-block bg-charcoal text-white px-16 py-6 text-xs font-bold uppercase tracking-[0.3em] hover:bg-taupe-800 transition-all transform hover:-translate-y-1">
                    ODKRYJ KOLEKCJĘ
                </a>
            </div>
        </div>
    </section>
</div>

<?php get_footer(); ?>
