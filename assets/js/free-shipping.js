/**
 * Pasek „Brakuje Ci X zł do darmowej dostawy” w koszyku i kasie (bloki WooCommerce).
 * Dane liczy serwer (inc/free-shipping.php → rozszerzenie koszyka „moretti_free_shipping” w Store API),
 * tu tylko je pokazujemy i odświeżamy przy każdej zmianie koszyka (ilość, usunięcie, kupon).
 *
 * Pasek stoi nad blokiem koszyka/kasy, poza drzewem Reacta, żeby WooCommerce go nie przerysowywał.
 * Przy okazji licznik koszyka w nagłówku (header.php, [data-cart-count]) nadąża za zmianą ilości w koszyku.
 */
(function () {
	'use strict';

	var data = window.wp && window.wp.data;
	var root = document.querySelector('.wp-block-woocommerce-cart, .wp-block-woocommerce-checkout');
	if (!data || !root) {
		return;
	}

	var bar = document.createElement('div');
	bar.className = 'moretti-fs moretti-fs--page';
	bar.setAttribute('role', 'status');
	bar.hidden = true;
	bar.innerHTML = '<p class="moretti-fs__text"></p><div class="moretti-fs__track" aria-hidden="true"><span></span></div>';
	root.parentNode.insertBefore(bar, root);

	var text = bar.querySelector('.moretti-fs__text');
	var fill = bar.querySelector('.moretti-fs__track span');
	var last = '';

	function syncHeaderCount(count) {
		document.querySelectorAll('[data-cart-count]').forEach(function (el) {
			el.textContent = count;
			el.style.display = count > 0 ? 'flex' : 'none';
		});
	}

	function render() {
		var cart = data.select('wc/store/cart').getCartData();
		if (cart && typeof cart.itemsCount === 'number' && data.select('wc/store/cart').hasFinishedResolution('getCartData')) {
			syncHeaderCount(cart.itemsCount);
		}
		var state = cart && cart.extensions && cart.extensions.moretti_free_shipping;
		var hasItems = cart && cart.itemsCount > 0;

		var key = state && hasItems ? state.message + '|' + state.percent : '';
		if (key === last) {
			return;
		}
		last = key;

		bar.hidden = !key;
		if (!key) {
			return;
		}
		text.textContent = state.message;
		fill.style.width = state.percent + '%';
		bar.classList.toggle('is-free', !!state.qualifies);
	}

	render();
	data.subscribe(render, 'wc/store/cart');
})();
