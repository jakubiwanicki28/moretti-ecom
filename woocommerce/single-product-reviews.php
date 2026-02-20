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

<details id="reviews" class="woocommerce-Reviews border-t border-gray-200 pt-4 pb-4 border-b group">
    <summary class="cursor-pointer text-charcoal font-medium flex items-center justify-between text-xs uppercase tracking-[0.2em] list-none marker:content-none [&::-webkit-details-marker]:hidden">
        <span>Opinie (<?php echo $review_count; ?>)</span>
        <svg class="w-5 h-5 transition-transform duration-300 group-open:rotate-45" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
        </svg>
    </summary>

    <div class="mt-8 text-taupe-700 text-sm">
        <?php if (have_comments()) : ?>
            <ol class="commentlist space-y-8 mb-10">
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
                echo '<nav class="woocommerce-pagination mb-8">';
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
            <p class="woocommerce-noreviews text-gray-500 mb-8 italic">Na razie nie ma opinii o produkcie.</p>
        <?php endif; ?>

        <div id="review_form_wrapper" class="bg-gray-50 p-6 rounded-lg">
            <div id="review_form">
                <?php
                $review_verification_required = get_option('woocommerce_review_rating_verification_required');
                if (
                    'yes' === $review_verification_required
                    && $product
                    && !wc_customer_bought_product('', get_current_user_id(), $product->get_id())
                ) :
                    ?>
                    <p class="woocommerce-verification-required text-red-600">Tylko zalogowani klienci, którzy kupili ten produkt, mogą dodać opinię.</p>
                <?php else : ?>
                    <?php
                    $commenter = wp_get_current_commenter();
                    
                    $comment_form = array(
                        'title_reply'          => have_comments() ? 'Dodaj opinię' : sprintf('Napisz pierwszą opinię o „%s”', get_the_title()),
                        'title_reply_to'       => 'Odpowiedz',
                        'title_reply_before'   => '<h3 id="reply-title" class="comment-reply-title text-lg font-medium text-gray-900 mb-4">',
                        'title_reply_after'    => '</h3>',
                        'comment_notes_before' => '',
                        'comment_notes_after'  => '',
                        'label_submit'         => 'Wyślij',
                        'class_submit'         => 'submit bg-black text-white px-8 py-3 uppercase text-xs font-bold tracking-widest hover:bg-gray-800 transition-colors cursor-pointer mt-4',
                        'logged_in_as'         => '',
                        'fields'               => array(
                            'author' => sprintf(
                                '<div class="mb-4"><label for="author" class="block text-xs font-bold uppercase tracking-wide text-gray-700 mb-2">%s&nbsp;<span class="required text-red-500">*</span></label><input id="author" name="author" type="text" value="%s" class="w-full border-gray-300 focus:border-black focus:ring-0 p-3 text-sm" required placeholder="Wpisz swoje imię" /></div>',
                                esc_html__('Imię', 'moretti-theme'),
                                esc_attr($commenter['comment_author'])
                            ),
                            'email'  => sprintf(
                                '<div class="mb-4"><label for="email" class="block text-xs font-bold uppercase tracking-wide text-gray-700 mb-2">%s&nbsp;<span class="required text-red-500">*</span></label><input id="email" name="email" type="email" value="%s" class="w-full border-gray-300 focus:border-black focus:ring-0 p-3 text-sm" required placeholder="twoj@email.com" /></div>',
                                esc_html__('E-mail', 'moretti-theme'),
                                esc_attr($commenter['comment_author_email'])
                            ),
                        ),
                        'comment_field'        => '',
                    );

                    if (wc_review_ratings_enabled()) {
                        $comment_form['comment_field'] .= '<div class="comment-form-rating mb-6"><label for="rating" class="block text-xs font-bold uppercase tracking-wide text-gray-700 mb-2">' . esc_html__('Twoja ocena', 'moretti-theme') . '&nbsp;<span class="required text-red-500">*</span></label>';
                        
                        // Custom Star Rating HTML
                        $comment_form['comment_field'] .= '
                        <div class="star-rating-widget mb-2">
                            <input type="radio" id="star5" name="rating" value="5" required />
                            <label for="star5" title="5 gwiazdek">★</label>
                            
                            <input type="radio" id="star4" name="rating" value="4" />
                            <label for="star4" title="4 gwiazdki">★</label>
                            
                            <input type="radio" id="star3" name="rating" value="3" />
                            <label for="star3" title="3 gwiazdki">★</label>
                            
                            <input type="radio" id="star2" name="rating" value="2" />
                            <label for="star2" title="2 gwiazdki">★</label>
                            
                            <input type="radio" id="star1" name="rating" value="1" />
                            <label for="star1" title="1 gwiazdka">★</label>
                        </div>';
                        
                        $comment_form['comment_field'] .= '</div>';
                    }

                    $comment_form['comment_field'] .= '<div class="comment-form-comment mb-6"><label for="comment" class="block text-xs font-bold uppercase tracking-wide text-gray-700 mb-2">' . esc_html__('Twoja opinia', 'moretti-theme') . '&nbsp;<span class="required text-red-500">*</span></label><textarea id="comment" name="comment" cols="45" rows="8" class="w-full border-gray-300 focus:border-black focus:ring-0 p-3 text-sm" required placeholder="Napisz co myślisz o tym produkcie..."></textarea></div>';

                    comment_form(apply_filters('woocommerce_product_review_comment_form_args', $comment_form));
                    ?>
                <?php endif; ?>
            </div>
        </div>
    </div>
</details>

<style>
/* Custom Star Rating CSS */
.star-rating-widget {
    display: flex;
    flex-direction: row-reverse;
    justify-content: flex-end;
    gap: 0.25rem;
}
.star-rating-widget input {
    position: absolute;
    opacity: 0;
    width: 1px;
    height: 1px;
    overflow: hidden;
    clip: rect(1px, 1px, 1px, 1px);
    pointer-events: none;
}
.star-rating-widget label {
    font-size: 1.5rem;
    line-height: 2rem;
    color: #d1d5db !important; /* gray-300 */
    cursor: pointer;
    transition: color 0.15s ease-in-out;
}
/* When hovering the container, keep unhovered stars gray */
.star-rating-widget:hover label {
    color: #d1d5db !important; 
}
/* Highlight hovered star and all following siblings (previous in visual order) */
.star-rating-widget label:hover,
.star-rating-widget label:hover ~ label {
    color: #facc15 !important; /* yellow-400 */
}
/* Keep selected stars highlighted */
.star-rating-widget input:checked ~ label {
    color: #facc15 !important; /* yellow-400 */
}

/* Reviews List Styling */
.commentlist {
    list-style: none;
    margin: 0;
    padding: 0;
}
.commentlist li {
    margin-bottom: 2rem;
    border-bottom: 1px solid #e5e7eb;
    padding-bottom: 2rem;
}
.commentlist li:last-child {
    border-bottom: none;
}
.comment_container {
    display: flex;
    gap: 1.5rem;
}
.comment_container img.avatar {
    width: 3rem;
    height: 3rem;
    border-radius: 50%;
    object-fit: cover;
}
.comment-text {
    flex: 1;
}
.comment-text .star-rating {
    float: right;
    color: #facc15;
    font-size: 0.875rem;
    overflow: hidden;
    position: relative;
    height: 1.2em;
    line-height: 1.2em;
    width: 5.5em;
    font-family: sans-serif;
}
.comment-text .star-rating::before {
    content: '★★★★★';
    color: #e5e7eb;
    float: left;
    top: 0;
    left: 0;
    position: absolute;
    letter-spacing: 2px;
}
.comment-text .star-rating span {
    overflow: hidden;
    float: left;
    top: 0;
    left: 0;
    position: absolute;
    padding-top: 1.5em;
}
.comment-text .star-rating span::before {
    content: '★★★★★';
    top: 0;
    position: absolute;
    left: 0;
    color: #facc15;
    letter-spacing: 2px;
}
.comment-text .meta {
    font-size: 0.75rem;
    color: #6b7280;
    margin-bottom: 0.5rem;
    display: block;
}
.comment-text .description {
    font-size: 0.875rem;
    line-height: 1.5;
    color: #374151;
}

/* Details Summary styling fixes */
details > summary {
    list-style: none;
}
details > summary::-webkit-details-marker {
    display: none;
}
</style>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const form = document.getElementById('commentform');
    if (form) {
        form.addEventListener('submit', function(e) {
            // Check if rating is selected
            const ratingInputs = form.querySelectorAll('input[name="rating"]');
            if (ratingInputs.length > 0) {
                const checked = form.querySelector('input[name="rating"]:checked');
                if (!checked) {
                    e.preventDefault();
                    alert('Proszę wybrać ocenę (gwiazdki).');
                    const widget = document.querySelector('.star-rating-widget');
                    if (widget) {
                        widget.scrollIntoView({ behavior: 'smooth', block: 'center' });
                        // Highlight widget
                        widget.style.outline = '2px solid #ef4444';
                        widget.style.borderRadius = '4px';
                        setTimeout(() => { widget.style.outline = 'none'; }, 2000);
                    }
                }
            }
        });
    }
});
</script>
