<?php
/**
 * Cart totals
 *
 * @package Moretti
 */

defined('ABSPATH') || exit;

?>
<div class="cart_totals <?php echo (WC()->customer->has_calculated_shipping()) ? 'calculated_shipping' : ''; ?>">

    <?php do_action('woocommerce_before_cart_totals'); ?>

    <h2 class="text-xl font-bold uppercase tracking-widest mb-8 pb-4 border-b border-gray-200">Podsumowanie</h2>

    <table cellspacing="0" class="shop_table shop_table_responsive w-full mb-8">

        <tr class="cart-subtotal flex justify-between items-center mb-4">
            <th class="text-[10px] font-bold uppercase tracking-widest text-taupe-500">Suma częściowa</th>
            <td data-title="Suma częściowa" class="text-sm font-bold text-charcoal"><?php wc_cart_totals_subtotal_html(); ?></td>
        </tr>

        <?php foreach (WC()->cart->get_coupons() as $code => $coupon) : ?>
            <tr class="cart-discount coupon-<?php echo esc_attr(sanitize_title($code)); ?> flex justify-between items-center mb-4">
                <th class="text-[10px] font-bold uppercase tracking-widest text-taupe-500">Kupon: <?php echo esc_html($code); ?></th>
                <td data-title="Kupon" class="text-sm font-bold text-green-600"><?php wc_cart_totals_coupon_html($coupon); ?></td>
            </tr>
        <?php endforeach; ?>

        <?php if (WC()->cart->needs_shipping() && WC()->cart->show_shipping()) : ?>

            <?php do_action('woocommerce_before_cart_totals_shipping'); ?>

            <tr class="flex flex-col mb-6">
                <th class="text-[10px] font-bold uppercase tracking-widest text-taupe-500 mb-4 text-left">Wysyłka</th>
                <td data-title="Wysyłka" class="text-sm">
                    <?php woocommerce_cart_totals_shipping_html(); ?>
                </td>
            </tr>

            <?php do_action('woocommerce_after_cart_totals_shipping'); ?>

        <?php elseif (WC()->cart->needs_shipping() && 'yes' === get_option('woocommerce_enable_shipping_calc')) : ?>

            <tr class="shipping flex justify-between items-center mb-4">
                <th class="text-[10px] font-bold uppercase tracking-widest text-taupe-500">Wysyłka</th>
                <td data-title="Wysyłka"><?php woocommerce_shipping_calculator(); ?></td>
            </tr>

        <?php endif; ?>

        <?php foreach (WC()->cart->get_fees() as $fee) : ?>
            <tr class="fee flex justify-between items-center mb-4">
                <th class="text-[10px] font-bold uppercase tracking-widest text-taupe-500"><?php echo esc_html($fee->name); ?></th>
                <td data-title="<?php echo esc_attr($fee->name); ?>" class="text-sm font-bold text-charcoal"><?php wc_cart_totals_fee_html($fee); ?></td>
            </tr>
        <?php endforeach; ?>

        <?php
        if (wc_tax_enabled() && !WC()->cart->display_prices_including_tax()) {
            $taxable_address = WC()->customer->get_taxable_address();
            $estimated_text  = '';

            if (WC()->customer->is_customer_outside_base() && !WC()->customer->has_calculated_shipping()) {
                /* translators: %s location. */
                $estimated_text = sprintf(' <small>' . esc_html__('(estimated for %s)', 'woocommerce') . '</small>', WC()->countries->estimated_for_prefix($taxable_address[0]) . WC()->countries->countries[$taxable_address[0]]);
            }

            if ('itemized' === get_option('woocommerce_tax_total_display')) {
                foreach (WC()->cart->get_tax_totals() as $code => $tax) { // phpcs:ignore WordPress.WP.GlobalVariablesOverride.Prohibited
                    ?>
                    <tr class="tax-rate tax-rate-<?php echo esc_attr(sanitize_title($code)); ?> flex justify-between items-center mb-4">
                        <th class="text-[10px] font-bold uppercase tracking-widest text-taupe-500"><?php echo esc_html($tax->label) . $estimated_text; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?></th>
                        <td data-title="<?php echo esc_attr($tax->label); ?>" class="text-sm font-bold text-charcoal"><?php echo wp_kses_post($tax->formatted_amount); ?></td>
                    </tr>
                    <?php
                }
            } else {
                ?>
                <tr class="tax-total flex justify-between items-center mb-4">
                    <th class="text-[10px] font-bold uppercase tracking-widest text-taupe-500"><?php echo esc_html(WC()->countries->tax_or_vat()) . $estimated_text; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?></th>
                    <td data-title="<?php echo esc_attr(WC()->countries->tax_or_vat()); ?>" class="text-sm font-bold text-charcoal"><?php wc_cart_totals_taxes_total_html(); ?></td>
                </tr>
                <?php
            }
        }
        ?>

        <?php do_action('woocommerce_cart_totals_before_order_total'); ?>

        <tr class="order-total flex justify-between items-center mt-8 pt-8 border-t-2 border-charcoal">
            <th class="text-sm font-bold uppercase tracking-widest text-charcoal">RAZEM</th>
            <td data-title="Suma" class="text-xl font-bold text-charcoal"><?php wc_cart_totals_order_total_html(); ?></td>
        </tr>

        <?php do_action('woocommerce_cart_totals_after_order_total'); ?>

    </table>

    <div class="wc-proceed-to-checkout mt-12">
        <?php do_action('woocommerce_proceed_to_checkout'); ?>
    </div>

    <?php do_action('woocommerce_after_cart_totals'); ?>

</div>
