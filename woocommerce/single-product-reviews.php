<?php
/**
 * Custom single product reviews template.
 *
 * @package Moretti
 */

defined('ABSPATH') || exit;

global $product;

if (!comments_open()) {
    return;
}

$review_count = $product ? (int) $product->get_review_count() : 0;
?>

<div id="reviews" class="woocommerce-Reviews">
    <h2 class="woocommerce-Reviews-title">
        <?php printf('Opinie (%d)', $review_count); ?>
    </h2>

    <?php if (have_comments()) : ?>
        <ol class="commentlist">
            <?php
            wp_list_comments(
                apply_filters(
                    'woocommerce_product_review_list_args',
                    array(
                        'callback' => 'woocommerce_comments',
                        'style' => 'ol',
                    )
                )
            );
            ?>
        </ol>

        <?php
        if (get_comment_pages_count() > 1 && get_option('page_comments')) :
            echo '<nav class="woocommerce-pagination">';
            paginate_comments_links(
                apply_filters(
                    'woocommerce_comment_pagination_args',
                    array(
                        'prev_text' => '&larr;',
                        'next_text' => '&rarr;',
                        'type'      => 'list',
                    )
                )
            );
            echo '</nav>';
        endif;
        ?>
    <?php else : ?>
        <p class="woocommerce-noreviews">Na razie nie ma opinii o produkcie.</p>
    <?php endif; ?>

    <div id="review_form_wrapper">
        <div id="review_form">
            <?php
            $review_verification_required = get_option('woocommerce_review_rating_verification_required');
            if (
                'yes' === $review_verification_required
                && $product
                && !wc_customer_bought_product('', get_current_user_id(), $product->get_id())
            ) :
                ?>
                <p class="woocommerce-verification-required">Tylko zalogowani klienci, którzy kupili ten produkt, mogą dodać opinię.</p>
            <?php else : ?>
                <?php
                $commenter = wp_get_current_commenter();
                $comment_form = array(
                    'title_reply'          => have_comments() ? 'Dodaj opinię' : sprintf('Napisz pierwszą opinię o „%s”', get_the_title()),
                    'title_reply_to'       => 'Odpowiedz',
                    'title_reply_before'   => '<h3 id="reply-title" class="comment-reply-title">',
                    'title_reply_after'    => '</h3>',
                    'comment_notes_before' => '',
                    'comment_notes_after'  => '',
                    'label_submit'         => 'Wyślij',
                    'logged_in_as'         => '',
                    'fields'               => array(
                        'author' => sprintf(
                            '<p class="comment-form-author"><label for="author">%s&nbsp;<span class="required">*</span></label><input id="author" name="author" type="text" value="%s" size="30" required /></p>',
                            esc_html__('Imię/Nick', 'moretti-theme'),
                            esc_attr($commenter['comment_author'])
                        ),
                        'email'  => sprintf(
                            '<p class="comment-form-email"><label for="email">%s&nbsp;<span class="required">*</span></label><input id="email" name="email" type="email" value="%s" size="30" required /></p>',
                            esc_html__('E-mail', 'moretti-theme'),
                            esc_attr($commenter['comment_author_email'])
                        ),
                    ),
                    'comment_field'        => '',
                );

                if (wc_review_ratings_enabled()) {
                    $comment_form['comment_field'] .= '<p class="comment-form-rating"><label for="rating">' . esc_html__('Twoja ocena', 'moretti-theme') . '&nbsp;<span class="required">*</span></label><select name="rating" id="rating" required><option value="">' . esc_html__('Wybierz ocenę', 'moretti-theme') . '</option><option value="5">' . esc_html__('5 / 5', 'moretti-theme') . '</option><option value="4">' . esc_html__('4 / 5', 'moretti-theme') . '</option><option value="3">' . esc_html__('3 / 5', 'moretti-theme') . '</option><option value="2">' . esc_html__('2 / 5', 'moretti-theme') . '</option><option value="1">' . esc_html__('1 / 5', 'moretti-theme') . '</option></select></p>';
                }

                $comment_form['comment_field'] .= '<p class="comment-form-comment"><label for="comment">' . esc_html__('Twoja opinia', 'moretti-theme') . '&nbsp;<span class="required">*</span></label><textarea id="comment" name="comment" cols="45" rows="8" required></textarea></p>';

                comment_form(apply_filters('woocommerce_product_review_comment_form_args', $comment_form));
                ?>
            <?php endif; ?>
        </div>
    </div>
</div>
