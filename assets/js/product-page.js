/**
 * Pasek „cena · Dodaj do koszyka” na telefonie (inc/product-page.php).
 * Widoczny, gdy właściwy przycisk formularza nie jest na ekranie; kliknięcie wysyła ten sam formularz.
 */
(function () {
	'use strict';

	var bar = document.getElementById('moretti-sticky-atc');
	var target = document.querySelector('.product-actions-row');
	var formButton = document.querySelector('form.cart .single_add_to_cart_button');
	if (!bar || !target || !formButton || !('IntersectionObserver' in window)) {
		return;
	}

	bar.hidden = false;
	document.body.classList.add('has-sticky-atc');

	new IntersectionObserver(function (entries) {
		var visible = entries[0].isIntersecting;
		bar.classList.toggle('is-visible', !visible);
		bar.setAttribute('aria-hidden', visible ? 'true' : 'false');
	}, { threshold: 0, rootMargin: '0px 0px -80px 0px' }).observe(target); // znika, gdy przycisk wychodzi spod paska

	bar.querySelector('button').addEventListener('click', function () {
		this.disabled = true;
		formButton.click();
	});
})();
