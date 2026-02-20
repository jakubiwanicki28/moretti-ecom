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

<details id="reviews" class="woocommerce-Reviews border-t border-gray-200 py-4 border-b group">
    <summary class="cursor-pointer text-charcoal font-medium flex items-center justify-between text-xs uppercase tracking-[0.2em] list-none marker:content-none [&::-webkit-details-marker]:hidden">
        <span>Opinie (<?php echo $review_count; ?>)</span>
        <div class="flex items-center gap-4">
            <div id="open-review-modal" 
                 class="text-xl font-light hover:text-black transition-colors px-2 cursor-pointer"
                 onclick="event.preventDefault(); event.stopPropagation(); document.getElementById('review-modal').classList.add('flex'); document.getElementById('review-modal').classList.remove('hidden'); document.body.style.overflow = 'hidden';">
                +
            </div>
            <svg class="w-5 h-5 transition-transform duration-300 group-open:rotate-180" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
            </svg>
        </div>
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
    </div>
</details>

<!-- Review Modal -->
<div id="review-modal" class="hidden fixed inset-0 z-[9999] items-center justify-center p-0 md:p-8 lg:p-12">
    <div class="absolute inset-0 bg-black/70 backdrop-blur-sm" onclick="closeReviewModal()"></div>
    <div class="relative bg-white w-full h-full md:h-auto md:max-h-[90vh] md:max-w-2xl md:rounded-xl shadow-2xl overflow-y-auto flex flex-col">
        <button type="button" 
                class="absolute top-4 right-4 md:top-6 md:right-6 text-3xl font-light text-gray-400 hover:text-black transition-colors z-10"
                onclick="closeReviewModal()">
            ×
        </button>
        
        <div class="p-6 md:p-12 mt-12 md:mt-0">
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
                        <p class="woocommerce-verification-required text-red-600">Tylko zalogowani klienci, którzy kupili ten produkt, mogą dodać opinię.</p>
                    <?php else : ?>
                        <?php
                        $commenter = wp_get_current_commenter();
                        
                        $comment_form = array(
                            'title_reply'          => 'Dodaj opinię',
                            'title_reply_to'       => 'Odpowiedz',
                            'title_reply_before'   => '<h3 id="reply-title" class="comment-reply-title text-2xl font-bold text-charcoal uppercase tracking-wider mb-8">',
                            'title_reply_after'    => '</h3>',
                            'comment_notes_before' => '',
                            'comment_notes_after'  => '',
                            'label_submit'         => 'Wyślij',
                            'class_submit'         => 'submit bg-black text-white w-full py-4 uppercase text-xs font-bold tracking-[0.2em] hover:bg-gray-800 transition-colors cursor-pointer mt-4',
                            'logged_in_as'         => '',
                            'fields'               => array(
                                'author' => sprintf(
                                    '<div class="mb-6"><label for="author" class="block text-[10px] font-bold uppercase tracking-[0.2em] text-gray-500 mb-2">%s&nbsp;<span class="required text-red-500">*</span></label><input id="author" name="author" type="text" value="%s" class="w-full border-gray-200 focus:border-black focus:ring-0 p-4 text-sm bg-gray-50" required placeholder="Wpisz swoje imię" /></div>',
                                    esc_html__('Imię', 'moretti-theme'),
                                    esc_attr($commenter['comment_author'])
                                ),
                                'email'  => sprintf(
                                    '<div class="mb-6"><label for="email" class="block text-[10px] font-bold uppercase tracking-[0.2em] text-gray-500 mb-2">%s&nbsp;<span class="required text-red-500">*</span></label><input id="email" name="email" type="email" value="%s" class="w-full border-gray-200 focus:border-black focus:ring-0 p-4 text-sm bg-gray-50" required placeholder="twoj@email.com" /></div>',
                                    esc_html__('E-mail', 'moretti-theme'),
                                    esc_attr($commenter['comment_author_email'])
                                ),
                            ),
                            'comment_field'        => '',
                        );

                        if (wc_review_ratings_enabled()) {
                            $comment_form['comment_field'] .= '<div class="comment-form-rating mb-8"><label for="rating" class="block text-[10px] font-bold uppercase tracking-[0.2em] text-gray-500 mb-3">' . esc_html__('Twoja ocena', 'moretti-theme') . '&nbsp;<span class="required text-red-500">*</span></label>';
                            
                            $comment_form['comment_field'] .= '
                            <div class="star-rating-widget">
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

                        $comment_form['comment_field'] .= '<div class="comment-form-comment mb-8"><label for="comment" class="block text-[10px] font-bold uppercase tracking-[0.2em] text-gray-500 mb-2">' . esc_html__('Twoja opinia', 'moretti-theme') . '&nbsp;<span class="required text-red-500">*</span></label><textarea id="comment" name="comment" cols="45" rows="6" class="w-full border-gray-200 focus:border-black focus:ring-0 p-4 text-sm bg-gray-50" required placeholder="Napisz co myślisz o tym produkcie..."></textarea></div>';

                        comment_form(apply_filters('woocommerce_product_review_comment_form_args', $comment_form));
                        ?>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
/* Custom Star Rating CSS */
.star-rating-widget {
    display: flex;
    flex-direction: row-reverse;
    justify-content: flex-end;
    gap: 0.5rem;
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
    font-size: 2rem;
    color: #e5e7eb !important; /* gray-200 */
    cursor: pointer;
    transition: color 0.15s ease-in-out;
}
.star-rating-widget:hover label {
    color: #e5e7eb !important; 
}
.star-rating-widget label:hover,
.star-rating-widget label:hover ~ label {
    color: #000000 !important; /* Elegant black stars */
}
.star-rating-widget input:checked ~ label {
    color: #000000 !important;
}

/* Reviews List Styling */
.commentlist {
    list-style: none;
    margin: 0;
    padding: 0;
}
.commentlist li {
    margin-bottom: 2rem;
    border-bottom: 1px solid #f3f4f6;
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
    width: 2.5rem;
    height: 2.5rem;
    border-radius: 50%;
    background: #f3f4f6;
}
.comment-text {
    flex: 1;
}
.comment-text .star-rating {
    float: right;
    color: #000;
    font-size: 0.75rem;
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
    color: #000;
    letter-spacing: 2px;
}
.comment-text .meta {
    font-size: 11px;
    text-transform: uppercase;
    letter-spacing: 0.1em;
    color: #9ca3af;
    margin-bottom: 0.75rem;
    display: block;
}
.comment-text .description {
    font-size: 14px;
    line-height: 1.6;
    color: #4b5563;
}

/* Form Styling Fixes */
#commentform .submit {
    background-color: #000000 !important;
    color: #ffffff !important;
    width: 100% !important;
    padding: 1rem !important;
    text-transform: uppercase !important;
    font-size: 11px !important;
    font-weight: 700 !important;
    letter-spacing: 0.2em !important;
    border: none !important;
    cursor: pointer !important;
    transition: background-color 0.2s !important;
}
#commentform .submit:hover {
    background-color: #333333 !important;
}

/* Checkbox Styling */
.comment-form-cookies-consent {
    display: flex !important;
    align-items: center !important;
    gap: 10px !important;
    margin-top: 1rem !important;
}
.comment-form-cookies-consent input {
    margin: 0 !important;
}
.comment-form-cookies-consent label {
    font-size: 10px !important;
    text-transform: none !important;
    letter-spacing: normal !important;
    color: #6b7280 !important;
    line-height: 1.2 !important;
}

/* Details Summary styling */
details > summary {
    list-style: none;
}
details > summary::-webkit-details-marker {
    display: none;
}

/* Modal styles */
#review-modal {
    display: none;
}
#review-modal.flex {
    display: flex !important;
}
@media (min-width: 768px) {
    #review-modal.flex {
        align-items: center;
        justify-content: center;
    }
    #review-modal .relative {
        height: auto !important;
        max-height: 85vh !important;
        max-width: 672px !important;
        margin: auto !important;
        border-radius: 1.5rem !important;
    }
}
@media (max-width: 767px) {
    #review-modal .relative {
        padding-top: 2rem;
    }
}
</style>

<script>
function sendReviewDebugLog(payload) {
    // #region agent log
    fetch('http://127.0.0.1:7891/ingest/dcf13279-3f4d-467c-82df-cbaa05cc56de',{method:'POST',headers:{'Content-Type':'application/json','X-Debug-Session-Id':'d2e860'},body:JSON.stringify(payload)}).catch(()=>{});
    // #endregion
}

function closeReviewModal() {
    document.getElementById('review-modal').classList.add('hidden');
    document.getElementById('review-modal').classList.remove('flex');
    document.body.style.overflow = '';
}

document.addEventListener('DOMContentLoaded', function() {
    // MOVE MODAL TO BODY TO FIX STACKING CONTEXT ISSUES
    const modal = document.getElementById('review-modal');
    if (modal) {
        document.body.appendChild(modal);
        sendReviewDebugLog({
            sessionId: 'd2e860',
            runId: 'pre-fix',
            hypothesisId: 'H3',
            location: 'woocommerce/single-product-reviews.php:356',
            message: 'Modal moved to body',
            data: {
                parentTag: modal.parentElement ? modal.parentElement.tagName : null,
                modalClass: modal.className,
                viewportWidth: window.innerWidth
            },
            timestamp: Date.now()
        });
    }

    const openTrigger = document.getElementById('open-review-modal');
    if (openTrigger && modal) {
        openTrigger.addEventListener('click', function() {
            const centerX = Math.floor(window.innerWidth / 2);
            const centerY = Math.floor(window.innerHeight / 2);
            const stacked = document.elementsFromPoint(centerX, centerY).slice(0, 6).map((el) => ({
                tag: el.tagName,
                id: el.id || null,
                className: (el.className || '').toString().slice(0, 120),
                position: window.getComputedStyle(el).position,
                zIndex: window.getComputedStyle(el).zIndex
            }));
            const relatedVisibleImages = Array.from(document.querySelectorAll('.related-products .slider-image')).slice(0, 6).map((el) => {
                const style = window.getComputedStyle(el);
                return {
                    active: el.classList.contains('active'),
                    display: style.display,
                    visibility: style.visibility,
                    opacity: style.opacity
                };
            });
            sendReviewDebugLog({
                sessionId: 'd2e860',
                runId: 'pre-fix',
                hypothesisId: 'H1',
                location: 'woocommerce/single-product-reviews.php:385',
                message: 'Modal opened snapshot',
                data: {
                    modalClass: modal.className,
                    modalDisplay: window.getComputedStyle(modal).display,
                    modalZ: window.getComputedStyle(modal).zIndex,
                    relatedVisibleImages,
                    stacked
                },
                timestamp: Date.now()
            });
        });
    }

    const form = document.getElementById('commentform');
    if (form) {
        form.addEventListener('submit', function(e) {
            const checked = form.querySelector('input[name="rating"]:checked');
            if (!checked) {
                e.preventDefault();
                alert('Proszę wybrać ocenę (gwiazdki).');
            }
        });
    }
    
    // Close modal on ESC
    document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape') closeReviewModal();
    });

    let loggedScroll = false;
    document.addEventListener('scroll', function() {
        if (loggedScroll) {
            return;
        }
        if (!modal || !modal.classList.contains('flex') || window.innerWidth >= 768) {
            return;
        }
        loggedScroll = true;
        const controls = Array.from(document.querySelectorAll('.related-products .wishlist-toggle, .related-products .slider-arrow')).slice(0, 8).map((el) => {
            const rect = el.getBoundingClientRect();
            return {
                className: (el.className || '').toString().slice(0, 120),
                top: Math.round(rect.top),
                left: Math.round(rect.left),
                width: Math.round(rect.width),
                height: Math.round(rect.height),
                position: window.getComputedStyle(el).position,
                zIndex: window.getComputedStyle(el).zIndex
            };
        });
        const activeRelatedImage = document.querySelector('.related-products .slider-image.active img');
        const activeImageStyle = activeRelatedImage ? window.getComputedStyle(activeRelatedImage) : null;
        sendReviewDebugLog({
            sessionId: 'd2e860',
            runId: 'pre-fix',
            hypothesisId: 'H2',
            location: 'woocommerce/single-product-reviews.php:442',
            message: 'Mobile scroll with open modal',
            data: {
                modalClass: modal.className,
                modalContainerBg: window.getComputedStyle(modal.querySelector('.relative')).backgroundColor,
                controls,
                activeImage: activeImageStyle ? {
                    display: activeImageStyle.display,
                    visibility: activeImageStyle.visibility,
                    opacity: activeImageStyle.opacity
                } : null
            },
            timestamp: Date.now()
        });
    }, { passive: true });
});
</script>
