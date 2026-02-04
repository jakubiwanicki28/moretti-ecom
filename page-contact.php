<?php
/**
 * Template Name: Contact Page
 * 
 * @package Moretti
 */

get_header(); ?>

<!-- Hero Section -->
<section class="bg-cream py-12 md:py-16">
    <div class="container mx-auto px-4">
        <div class="max-w-3xl mx-auto text-center">
            <h1 class="text-4xl md:text-5xl font-light text-charcoal mb-4 uppercase tracking-widest">
                Kontakt
            </h1>
            <p class="text-lg text-taupe-700">
                Chętnie odpowiemy na Twoje pytania. Skontaktuj się z naszym zespołem.
            </p>
        </div>
    </div>
</section>

<!-- Contact Content -->
<section class="container mx-auto px-4 py-12 md:py-20">
    <div class="max-w-6xl mx-auto">
        <div class="grid md:grid-cols-2 gap-12 md:gap-16">
            
            <!-- Contact Form -->
            <div class="order-2 md:order-1">
                <h2 class="text-2xl font-light text-charcoal mb-6 uppercase tracking-widest">Napisz do nas</h2>
                
                <?php
                // Check if form was submitted
                if (isset($_POST['moretti_contact_submit'])) {
                    // Verify nonce
                    if (wp_verify_nonce($_POST['moretti_contact_nonce'], 'moretti_contact_form')) {
                        // Sanitize inputs
                        $name = sanitize_text_field($_POST['contact_name']);
                        $email = sanitize_email($_POST['contact_email']);
                        $phone = sanitize_text_field($_POST['contact_phone']);
                        $message = sanitize_textarea_field($_POST['contact_message']);
                        
                        // Send email
                        $to = get_option('admin_email');
                        $subject = 'Nowa wiadomość ze strony od ' . $name;
                        $body = "Imię: $name\n";
                        $body .= "Email: $email\n";
                        $body .= "Telefon: $phone\n\n";
                        $body .= "Wiadomość:\n$message";
                        $headers = array('Reply-To: ' . $email);
                        
                        if (wp_mail($to, $subject, $body, $headers)) {
                            echo '<div class="bg-green-50 border-l-4 border-green-600 p-4 mb-6 text-green-900 text-sm">Dziękujemy za wiadomość! Odpowiemy tak szybko, jak to możliwe.</div>';
                        } else {
                            echo '<div class="bg-red-50 border-l-4 border-red-600 p-4 mb-6 text-red-900 text-sm">Przepraszamy, wystąpił błąd podczas wysyłania wiadomości. Spróbuj ponownie później.</div>';
                        }
                    }
                }
                ?>
                
                <form method="post" action="" class="space-y-6">
                    <?php wp_nonce_field('moretti_contact_form', 'moretti_contact_nonce'); ?>
                    
                    <div>
                        <label for="contact_name" class="block text-xs font-semibold text-charcoal mb-2 uppercase tracking-widest">
                            Imię i Nazwisko *
                        </label>
                        <input 
                            type="text" 
                            id="contact_name" 
                            name="contact_name" 
                            required
                            class="w-full px-4 py-3 border border-gray-300 focus:border-charcoal focus:outline-none transition-colors text-sm"
                            placeholder="Twoje imię"
                        >
                    </div>
                    
                    <div>
                        <label for="contact_email" class="block text-xs font-semibold text-charcoal mb-2 uppercase tracking-widest">
                            Email *
                        </label>
                        <input 
                            type="email" 
                            id="contact_email" 
                            name="contact_email" 
                            required
                            class="w-full px-4 py-3 border border-gray-300 focus:border-charcoal focus:outline-none transition-colors text-sm"
                            placeholder="twoj@email.pl"
                        >
                    </div>
                    
                    <div>
                        <label for="contact_phone" class="block text-xs font-semibold text-charcoal mb-2 uppercase tracking-widest">
                            Telefon
                        </label>
                        <input 
                            type="tel" 
                            id="contact_phone" 
                            name="contact_phone"
                            class="w-full px-4 py-3 border border-gray-300 focus:border-charcoal focus:outline-none transition-colors text-sm"
                            placeholder="+48 000 000 000"
                        >
                    </div>
                    
                    <div>
                        <label for="contact_message" class="block text-xs font-semibold text-charcoal mb-2 uppercase tracking-widest">
                            Wiadomość *
                        </label>
                        <textarea 
                            id="contact_message" 
                            name="contact_message" 
                            rows="6"
                            required
                            class="w-full px-4 py-3 border border-gray-300 focus:border-charcoal focus:outline-none transition-colors resize-none text-sm"
                            placeholder="W czym możemy pomóc?"
                        ></textarea>
                    </div>
                    
                    <button 
                        type="submit" 
                        name="moretti_contact_submit"
                        class="w-full md:w-auto px-10 py-4 bg-charcoal text-white hover:bg-taupe-800 transition-colors font-bold text-xs uppercase tracking-widest"
                    >
                        Wyślij wiadomość
                    </button>
                </form>
            </div>
            
            <!-- Contact Information -->
            <div class="order-1 md:order-2">
                <h2 class="text-2xl font-light text-charcoal mb-6 uppercase tracking-widest text-center md:text-left">Dane Kontaktowe</h2>
                
                <div class="space-y-8">
                    <!-- Office Address -->
                    <div>
                        <h3 class="text-[10px] font-bold text-charcoal mb-3 uppercase tracking-[0.2em]">Adres</h3>
                        <div class="text-taupe-700 text-sm">
                            <p>ul. Kaletnicza 15</p>
                            <p>00-001 Warszawa</p>
                            <p>Polska</p>
                        </div>
                    </div>
                    
                    <!-- Contact Details -->
                    <div>
                        <h3 class="text-[10px] font-bold text-charcoal mb-3 uppercase tracking-[0.2em]">Kontakt</h3>
                        <div class="space-y-2 text-taupe-700 text-sm">
                            <div class="flex items-center gap-3">
                                <svg class="w-4 h-4 text-taupe-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"></path>
                                </svg>
                                <a href="tel:+48123456789" class="hover:text-charcoal transition-colors">
                                    +48 123 456 789
                                </a>
                            </div>
                            <div class="flex items-center gap-3">
                                <svg class="w-4 h-4 text-taupe-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path>
                                </svg>
                                <a href="mailto:biuro@moretti.com" class="hover:text-charcoal transition-colors">
                                    biuro@moretti.com
                                </a>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Business Hours -->
                    <div>
                        <h3 class="text-[10px] font-bold text-charcoal mb-3 uppercase tracking-[0.2em]">Godziny Otwarcia</h3>
                        <div class="space-y-2 text-taupe-700 text-xs">
                            <div class="flex justify-between max-w-[200px]">
                                <span>Poniedziałek - Piątek</span>
                                <span>9:00 - 17:00</span>
                            </div>
                            <div class="flex justify-between max-w-[200px]">
                                <span>Sobota</span>
                                <span>10:00 - 14:00</span>
                            </div>
                            <div class="flex justify-between max-w-[200px]">
                                <span>Niedziela</span>
                                <span class="text-taupe-400 italic">Zamknięte</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Map Section (Placeholder) -->
<section class="bg-sand-50 py-12">
    <div class="container mx-auto px-4 text-center">
        <div class="relative h-[300px] bg-taupe-100 rounded-none overflow-hidden flex items-center justify-center border border-gray-100">
            <div class="text-center">
                <svg class="w-10 h-10 text-taupe-300 mx-auto mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path>
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path>
                </svg>
                <p class="text-taupe-600 text-[10px] uppercase tracking-widest">Lokalizacja naszej pracowni</p>
                <p class="text-taupe-500 text-xs mt-1">ul. Kaletnicza 15, Warszawa</p>
            </div>
        </div>
    </div>
</section>

<?php get_footer(); ?>
