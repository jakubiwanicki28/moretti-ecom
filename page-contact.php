<?php
/**
 * Template Name: Contact Page
 * 
 * @package Moretti
 */

get_header(); ?>

<div class="contact-page-wrapper bg-white">
    <!-- Hero Section -->
    <section class="relative pt-20 pb-20 md:pt-32 md:pb-32 border-b border-gray-100">
        <div class="container mx-auto px-4">
            <div class="max-w-6xl mx-auto">
                <span class="text-[10px] uppercase tracking-[0.4em] text-taupe-500 mb-8 block font-medium">ZOSTAŃMY W KONTAKCIE</span>
                <h1 class="text-5xl md:text-7xl lg:text-9xl font-bold text-charcoal uppercase tracking-tighter leading-[0.85] mb-20">
                    CHĘTNIE<br/>
                    <span class="text-taupe-300">POMOŻEMY</span>
                </h1>
                <div class="grid md:grid-cols-2 gap-16 md:gap-24 items-start">
                    <div class="text-xl md:text-2xl text-charcoal leading-tight font-bold uppercase tracking-tight">
                        Masz pytania dotyczące naszych produktów, zamówienia lub personalizacji? Nasz zespół jest do Twojej dyspozycji.
                    </div>
                    <div class="text-taupe-600 text-base leading-relaxed">
                        W Moretti wierzymy w relacje oparte na zaufaniu. Każde zapytanie traktujemy indywidualnie, dbając o to, byś otrzymał wsparcie na najwyższym poziomie – dokładnie takim, jak nasze produkty.
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Contact Content -->
    <section class="py-24 md:py-48 bg-white">
        <div class="container mx-auto px-4">
            <div class="max-w-3xl mx-auto">
                
                <p style="color: #766a5d; font-size: 20px; text-align: center; margin-bottom: 48px; line-height: 1.6; max-width: 700px; margin-left: auto; margin-right: auto;">
                    Masz pytania dotyczące naszych produktów,<br/>zamówienia lub personalizacji? Napisz do nas.
                </p>

                <!-- Contact Form -->
                <div style="background-color: #faf9f7; padding: 60px; margin-bottom: 80px;">
                    
                    <?php
                    if (isset($_POST['moretti_contact_submit'])) {
                        if (wp_verify_nonce($_POST['moretti_contact_nonce'], 'moretti_contact_form')) {
                            $name = sanitize_text_field($_POST['contact_name']);
                            $email = sanitize_email($_POST['contact_email']);
                            $phone = sanitize_text_field($_POST['contact_phone']);
                            $message = sanitize_textarea_field($_POST['contact_message']);
                            
                            $to = get_option('admin_email');
                            $subject = 'Nowa wiadomość ze strony od ' . $name;
                            $body = "Imię: $name\nEmail: $email\nTelefon: $phone\n\nWiadomość:\n$message";
                            $headers = array('Reply-To: ' . $email);
                            
                            if (wp_mail($to, $subject, $body, $headers)) {
                                echo '<div style="background-color: #2a2826; color: #ffffff; padding: 20px; margin-bottom: 32px; font-size: 12px; font-weight: 700; text-transform: uppercase; letter-spacing: 0.1em; text-align: center;">Dziękujemy za wiadomość. Odpowiemy wkrótce.</div>';
                            } else {
                                echo '<div style="background-color: #7f1d1d; color: #ffffff; padding: 20px; margin-bottom: 32px; font-size: 12px; font-weight: 700; text-transform: uppercase; letter-spacing: 0.1em; text-align: center;">Wystąpił błąd. Spróbuj ponownie.</div>';
                            }
                        }
                    }
                    ?>
                    
                    <form method="post" action="">
                        <?php wp_nonce_field('moretti_contact_form', 'moretti_contact_nonce'); ?>
                        
                        <div style="margin-bottom: 28px;">
                            <label for="contact_name" style="display: block; font-size: 11px; font-weight: 700; text-transform: uppercase; letter-spacing: 0.15em; color: #2a2826; margin-bottom: 12px;">Imię i Nazwisko *</label>
                            <input type="text" id="contact_name" name="contact_name" required 
                                   style="width: 100%; background-color: #ffffff; border: 1px solid #e5e7eb; padding: 16px 20px; font-size: 15px; color: #2a2826; font-family: inherit; box-sizing: border-box; transition: border-color 0.2s;" 
                                   placeholder="Jan Kowalski"
                                   onfocus="this.style.borderColor='#2a2826'; this.style.outline='none';"
                                   onblur="this.style.borderColor='#e5e7eb';">
                        </div>
                        
                        <div style="margin-bottom: 28px;">
                            <label for="contact_email" style="display: block; font-size: 11px; font-weight: 700; text-transform: uppercase; letter-spacing: 0.15em; color: #2a2826; margin-bottom: 12px;">Email *</label>
                            <input type="email" id="contact_email" name="contact_email" required 
                                   style="width: 100%; background-color: #ffffff; border: 1px solid #e5e7eb; padding: 16px 20px; font-size: 15px; color: #2a2826; font-family: inherit; box-sizing: border-box; transition: border-color 0.2s;" 
                                   placeholder="email@przyklad.pl"
                                   onfocus="this.style.borderColor='#2a2826'; this.style.outline='none';"
                                   onblur="this.style.borderColor='#e5e7eb';">
                        </div>
                        
                        <div style="margin-bottom: 28px;">
                            <label for="contact_phone" style="display: block; font-size: 11px; font-weight: 700; text-transform: uppercase; letter-spacing: 0.15em; color: #2a2826; margin-bottom: 12px;">Telefon</label>
                            <input type="tel" id="contact_phone" name="contact_phone" 
                                   style="width: 100%; background-color: #ffffff; border: 1px solid #e5e7eb; padding: 16px 20px; font-size: 15px; color: #2a2826; font-family: inherit; box-sizing: border-box; transition: border-color 0.2s;" 
                                   placeholder="+48 000 000 000"
                                   onfocus="this.style.borderColor='#2a2826'; this.style.outline='none';"
                                   onblur="this.style.borderColor='#e5e7eb';">
                        </div>
                        
                        <div style="margin-bottom: 32px;">
                            <label for="contact_message" style="display: block; font-size: 11px; font-weight: 700; text-transform: uppercase; letter-spacing: 0.15em; color: #2a2826; margin-bottom: 12px;">Wiadomość *</label>
                            <textarea id="contact_message" name="contact_message" rows="6" required 
                                      style="width: 100%; background-color: #ffffff; border: 1px solid #e5e7eb; padding: 16px 20px; font-size: 15px; color: #2a2826; font-family: inherit; resize: none; box-sizing: border-box; line-height: 1.5; transition: border-color 0.2s;" 
                                      placeholder="W czym możemy pomóc?"
                                      onfocus="this.style.borderColor='#2a2826'; this.style.outline='none';"
                                      onblur="this.style.borderColor='#e5e7eb';"></textarea>
                        </div>
                        
                        <button type="submit" name="moretti_contact_submit" 
                                style="width: 100%; background-color: #2a2826; color: #ffffff; padding: 22px 40px; font-size: 12px; font-weight: 700; text-transform: uppercase; letter-spacing: 0.15em; border: none; cursor: pointer; font-family: inherit; transition: background-color 0.2s; margin-top: 8px;"
                                onmouseover="this.style.backgroundColor='#4a423a';"
                                onmouseout="this.style.backgroundColor='#2a2826';">
                            Wyślij wiadomość
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </section>
</div>

<?php get_footer(); ?>
