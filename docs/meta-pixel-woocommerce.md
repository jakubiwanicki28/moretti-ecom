# Meta Pixel + WooCommerce Runbook

This runbook standardizes Meta tracking for the following events:

- `PageView`
- `ViewContent`
- `AddToCart`
- `InitiateCheckout`
- `Purchase`

## 1) Plugin setup (official integration)

1. Install and activate `Facebook for WooCommerce` in WordPress admin.
2. Connect the store to the correct Meta Business Manager, Pixel, and Catalog.
3. Enable Conversions API in plugin settings.
4. Confirm catalog sync uses Woo product IDs/SKUs used by this store.

Recommended: keep only one primary Meta integration active to avoid duplicate browser events.

## 2) Theme compatibility rules in this project

- Keep `wp_head()` in `header.php` and `wp_footer()` in `footer.php`.
- Keep product pages on standard Woo single-product flow.
- Keep cart to checkout transition on Woo default flow (`woocommerce_proceed_to_checkout`).
- Keep quick-add using Woo native AJAX add-to-cart endpoint (`?wc-ajax=add_to_cart`).

These rules are required for plugin-level Woo event hooks to remain reliable.

## 3) Event validation checklist (Meta Test Events)

Use Meta Test Events and run the scenarios below in one browser session.

1. `PageView`
   - Open homepage, category page, product page.
   - Expected: `PageView` each time.

2. `ViewContent`
   - Open a simple product page.
   - Open a variable product page.
   - Expected: `ViewContent` with valid product identifiers.

3. `AddToCart`
   - Add from PDP button.
   - Add from quick-add button on listing/home cards.
   - Expected: `AddToCart` in both flows.

4. `InitiateCheckout`
   - Go to cart and click proceed to checkout.
   - Expected: `InitiateCheckout` exactly once per checkout start.

5. `Purchase`
   - Complete a test order end-to-end.
   - Expected: `Purchase` with correct `value`, `currency`, and content IDs.

## 4) Regression guardrails

Before merging frontend/cart changes:

1. Confirm quick-add still posts to Woo native AJAX endpoint, not custom `admin-ajax` action.
2. Confirm quick-add still triggers Woo `added_to_cart` JS event.
3. Confirm cart badge updates after both PDP add and quick-add.
4. Confirm no second Pixel integration/plugin duplicates events.
5. Re-run the 5-event validation checklist in Meta Test Events.

## 5) Troubleshooting

- Missing `AddToCart` from quick-add:
  - Check browser network request uses `wc-ajax=add_to_cart`.
  - Check response includes `fragments` and `cart_hash`.
  - Check `added_to_cart` is triggered in JS.

- Missing `Purchase`:
  - Verify thank-you page is standard Woo flow.
  - Verify plugin connection to the correct Pixel and dataset.

- Duplicate events:
  - Disable overlapping Meta/GTM integrations emitting the same event.
