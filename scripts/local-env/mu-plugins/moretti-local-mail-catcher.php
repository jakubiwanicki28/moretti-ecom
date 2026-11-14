<?php
/**
 * Plugin Name: Moretti — lokalny przechwytywacz maili
 * Description: TYLKO środowisko lokalne. Zamiast wysyłać, zapisuje każdy mail jako plik HTML w wp-content/uploads/local-mails/
 *              (podgląd: http://localhost:8080/wp-content/uploads/local-mails/). Pozwala testować maile sklepu bez SMTP.
 */

if (!defined('ABSPATH') || wp_get_environment_type() !== 'local') {
    return;
}

add_filter('pre_wp_mail', function ($return, $atts) {
    $dir = WP_CONTENT_DIR . '/uploads/local-mails';
    wp_mkdir_p($dir);

    $to      = is_array($atts['to']) ? implode(', ', $atts['to']) : (string) $atts['to'];
    $subject = (string) $atts['subject'];
    $file    = sprintf('%s/%s-%s.html', $dir, gmdate('Ymd-His'), substr(sanitize_title($subject), 0, 60));
    $header  = sprintf("<!-- To: %s -->\n<!-- Subject: %s -->\n", esc_html($to), esc_html($subject));

    file_put_contents($file, $header . $atts['message']);

    return true;
}, 10, 2);
