<?php
/**
 * Cart Page
 *
 * @package Moretti
 */

defined('ABSPATH') || exit;

do_action('woocommerce_before_cart'); ?>

<div class="cart-page-wrapper bg-white py-12 md:py-24">
    <div class="container mx-auto px-4">
        
        <!-- Cart Header -->
        <header class="mb-12 md:mb-20 text-center">
            <h1 class="text-4xl md:text-6xl lg:text-7xl font-bold text-charcoal uppercase tracking-tighter leading-none">
                TWÓJ KOSZYK
            </h1>
        </header>

        <form class="woocommerce-cart-form" action="<?php echo esc_url(wc_get_cart_url()); ?>" method="post">
            <div class="flex flex-col lg:flex-row gap-16">
                
                <!-- Cart Items Table -->
                <div class="lg:w-2/3">
                    <table class="shop_table shop_table_responsive cart woocommerce-cart-form__contents w-full border-collapse" cellspacing="0">
                        <thead class="hidden md:table-header-group">
                            <tr class="border-b border-gray-200">
                                <th class="product-thumbnail py-4 text-[10px] font-bold uppercase tracking-widest text-left">Produkt</th>
                                <th class="product-name py-4 text-[10px] font-bold uppercase tracking-widest text-left"></th>
                                <th class="product-price py-4 text-[10px] font-bold uppercase tracking-widest text-left">Cena</th>
                                <th class="product-quantity py-4 text-[10px] font-bold uppercase tracking-widest text-left">Ilość</th>
                                <th class="product-subtotal py-4 text-[10px] font-bold uppercase tracking-widest text-right">Suma</th>
                                <th class="product-remove py-4 text-[10px] font-bold uppercase tracking-widest text-right"></th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php do_action('woocommerce_before_cart_contents'); ?>

                            <?php
                            foreach (WC()->cart->get_cart() as $cart_item_key => $cart_item) {
                                $_product   = apply_filters('woocommerce_cart_item_product', $cart_item['data'], $cart_item, $cart_item_key);
                                $product_id = apply_filters('woocommerce_cart_item_product_id', $cart_item['product_id'], $cart_item, $cart_item_key);

                                if ($_product && $_product->exists() && $cart_item['quantity'] > 0 && apply_filters('woocommerce_cart_item_visible', true, $cart_item, $cart_item_key)) {
                                    $product_permalink = apply_filters('woocommerce_cart_item_permalink', $_product->is_visible() ? $_product->get_permalink($cart_item) : '', $cart_item, $cart_item_key);
                                    ?>
                                    <tr class="woocommerce-cart-form__cart-item <?php echo esc_attr(apply_filters('woocommerce_cart_item_class', 'cart_item', $cart_item, $cart_item_key)); ?> border-b border-gray-100">
                                        
                                        <td class="product-thumbnail py-8 w-24">
                                            <?php
                                            $thumbnail = apply_filters('woocommerce_cart_item_thumbnail', $_product->get_image(), $cart_item, $cart_item_key);
                                            if (!$product_permalink) {
                                                echo $thumbnail; // PHPCS: XSS ok.
                                            } else {
                                                printf('<a href="%s">%s</a>', esc_url($product_permalink), $thumbnail); // PHPCS: XSS ok.
                                            }
                                            ?>
                                        </td>

                                        <td class="product-name py-8 pl-6" data-title="<?php esc_attr_e('Product', 'woocommerce'); ?>">
                                            <?php
                                            if (!$product_permalink) {
                                                echo wp_kses_post(apply_filters('woocommerce_cart_item_name', $_product->get_name(), $cart_item, $cart_item_key) . '&nbsp;');
                                            } else {
                                                echo wp_kses_post(apply_filters('woocommerce_cart_item_name', sprintf('<a href="%s" class="text-sm font-bold uppercase tracking-wider text-charcoal hover:text-taupe-600 transition-colors">%s</a>', esc_url($product_permalink), $_product->get_name()), $cart_item, $cart_item_key));
                                            }

                                            do_action('woocommerce_after_cart_item_name', $cart_item, $cart_item_key);

                                            // Meta data.
                                            echo wc_get_formatted_cart_item_data($cart_item); // PHPCS: XSS ok.

                                            // Backorder notification.
                                            if ($_product->backorders_require_notification() && $_product->is_on_backorder($cart_item['quantity'])) {
                                                echo wp_kses_post(apply_filters('woocommerce_cart_item_backorder_notification', '<p class="backorder_notification">' . esc_html__('Available on backorder', 'woocommerce') . '</p>', $product_id));
                                            }
                                            ?>
                                        </td>

                                        <td class="product-price py-8 text-sm text-taupe-600" data-title="<?php esc_attr_e('Price', 'woocommerce'); ?>">
                                            <?php
                                                echo apply_filters('woocommerce_cart_item_price', WC()->cart->get_product_price($_product), $cart_item, $cart_item_key); // PHPCS: XSS ok.
                                            ?>
                                        </td>

                                        <td class="product-quantity py-8" data-title="<?php esc_attr_e('Quantity', 'woocommerce'); ?>">
                                            <?php
                                            if ($_product->is_sold_individually()) {
                                                $product_quantity = sprintf('1 <input type="hidden" name="cart[%s][qty]" value="1" />', $cart_item_key);
                                            } else {
                                                $product_quantity = woocommerce_quantity_input(
                                                    array(
                                                        'input_name'   => "cart[{$cart_item_key}][qty]",
                                                        'input_value'  => $cart_item['quantity'],
                                                        'max_value'    => $_product->get_max_purchase_quantity(),
                                                        'min_value'    => '0',
                                                        'product_name' => $_product->get_name(),
                                                    ),
                                                    $_product,
                                                    false
                                                );
                                            }

                                            echo apply_filters('woocommerce_cart_item_quantity', $product_quantity, $cart_item_key, $cart_item); // PHPCS: XSS ok.
                                            ?>
                                        </td>

                                        <td class="product-subtotal py-8 text-right text-sm font-bold text-charcoal" data-title="<?php esc_attr_e('Subtotal', 'woocommerce'); ?>">
                                            <?php
                                                echo apply_filters('woocommerce_cart_item_subtotal', WC()->cart->get_product_subtotal($_product, $cart_item['quantity']), $cart_item, $cart_item_key); // PHPCS: XSS ok.
                                            ?>
                                        </td>

                                        <td class="product-remove py-8 text-right pl-4">
                                            <?php
                                                echo apply_filters( // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
                                                    'woocommerce_cart_item_remove_link',
                                                    sprintf(
                                                        '<a href="%s" class="remove text-taupe-400 hover:text-charcoal transition-colors" aria-label="%s" data-product_id="%s" data-product_sku="%s">&times;</a>',
                                                        esc_url(wc_get_cart_remove_url($cart_item_key)),
                                                        esc_html__('Remove this item', 'woocommerce'),
                                                        esc_attr($product_id),
                                                        esc_attr($_product->get_sku())
                                                    ),
                                                    $cart_item_key
                                                );
                                            ?>
                                        </td>
                                    </tr>
                                    <?php
                                }
                            }
                            ?>

                            <?php do_action('woocommerce_cart_contents'); ?>

                            <tr>
                                <td colspan="6" class="actions pt-8">

                                    <div class="flex flex-col md:flex-row justify-between items-center gap-6">
                                        <?php if (wc_coupons_enabled()) { ?>
                                            <div class="coupon flex items-center gap-4 w-full md:w-auto">
                                                <input type="text" name="coupon_code" class="input-text bg-transparent border-b border-gray-300 py-2 focus:border-charcoal focus:outline-none text-xs uppercase tracking-widest w-full md:w-48" id="coupon_code" value="" placeholder="KOD KUPONU" />
                                                <button type="submit" class="button px-6 py-2 border border-charcoal text-[10px] font-bold uppercase tracking-widest hover:bg-charcoal hover:text-white transition-all" name="apply_coupon" value="Zastosuj">Zastosuj</button>
                                                <?php do_action('woocommerce_cart_coupon'); ?>
                                            </div>
                                        <?php } ?>

                                        <button type="submit" class="button px-10 py-3 bg-gray-100 text-charcoal text-[10px] font-bold uppercase tracking-widest hover:bg-gray-200 transition-all ml-auto" name="update_cart" value="Aktualizuj koszyk">Aktualizuj koszyk</button>
                                    </div>

                                    <?php do_action('woocommerce_cart_actions'); ?>

                                    <?php wp_nonce_field('woocommerce-cart', 'woocommerce-cart-nonce'); ?>
                                </td>
                            </tr>

                            <?php do_action('woocommerce_after_cart_contents'); ?>
                        </tbody>
                    </table>
                </div>

                <!-- Cart Summary (Totals) -->
                <div class="lg:w-1/3">
                    <div class="cart-collaterals sticky top-24 bg-gray-50 p-8 md:p-12">
                        <?php
                            /**
                             * Cart collaterals hook.
                             *
                             * @hooked woocommerce_cross_sell_display
                             * @hooked woocommerce_cart_totals - 10
                             */
                            do_action('woocommerce_cart_collaterals');
                        ?>
                    </div>
                </div>
            </div>
        </form>

        <?php do_action('woocommerce_after_cart_table'); ?>

        <!-- Recommended Products Section -->
        <div class="mt-32 pt-20 border-t border-gray-100">
            <h2 class="text-3xl md:text-4xl font-bold uppercase tracking-tighter mb-12 text-center">NOWOŚCI W SKLEPIE</h2>
            <?php
            echo do_shortcode('[products limit="4" columns="4" orderby="date" order="DESC"]');
            ?>
        </div>
    </div>
</div>

<?php do_action('woocommerce_after_cart'); ?>
