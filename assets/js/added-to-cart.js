/**
 * Okienko „Dodano do koszyka” (HTML przygotowuje serwer: inc/added-to-cart.php).
 * - po przeładowaniu strony (formularz produktu): okienko z atrybutem data-atc-open otwiera się samo,
 * - po szybkim dodaniu (AJAX): HTML przychodzi we fragmencie „moretti_added_to_cart” zdarzenia „added_to_cart”.
 */
(function () {
	'use strict';

	var lastFocus = null;

	function close(dialog) {
		dialog.classList.remove('is-open');
		document.documentElement.classList.remove('moretti-atc-locked');
		setTimeout(function () {
			dialog.hidden = true;
		}, 250);
		if (lastFocus && typeof lastFocus.focus === 'function') {
			lastFocus.focus();
		}
	}

	function open(dialog) {
		lastFocus = document.activeElement;
		dialog.hidden = false;
		document.documentElement.classList.add('moretti-atc-locked');
		// Klatka opóźnienia, żeby przejście CSS (wysunięcie / pojawienie się) zadziałało.
		requestAnimationFrame(function () {
			requestAnimationFrame(function () {
				dialog.classList.add('is-open');
			});
		});
		// Fokus na panel (nie na przycisk), żeby czytnik ekranu ogłosił okienko, a Tab prowadził do przycisków.
		var panel = dialog.querySelector('.moretti-atc__panel');
		if (panel) {
			panel.focus({ preventScroll: true });
		}
	}

	function show(html) {
		var existing = document.getElementById('moretti-atc');
		if (existing) {
			existing.remove();
		}
		var holder = document.createElement('div');
		holder.innerHTML = html;
		var dialog = holder.firstElementChild;
		if (!dialog) {
			return;
		}
		document.body.appendChild(dialog);
		open(dialog);
	}

	document.addEventListener('click', function (event) {
		var closer = event.target.closest && event.target.closest('[data-atc-close]');
		var dialog = closer && closer.closest('.moretti-atc');
		if (dialog) {
			event.preventDefault();
			close(dialog);
		}
	});

	document.addEventListener('keydown', function (event) {
		var dialog = document.querySelector('.moretti-atc.is-open');
		if (!dialog) {
			return;
		}
		if (event.key === 'Escape') {
			close(dialog);
			return;
		}
		if (event.key === 'Tab') {
			// Fokus zostaje w okienku.
			var focusable = dialog.querySelectorAll('a[href], button:not([disabled])');
			var first = focusable[0];
			var last = focusable[focusable.length - 1];
			if (!dialog.contains(document.activeElement) || document.activeElement.classList.contains('moretti-atc__panel')) {
				event.preventDefault();
				(event.shiftKey ? last : first).focus();
			} else if (event.shiftKey && document.activeElement === first) {
				event.preventDefault();
				last.focus();
			} else if (!event.shiftKey && document.activeElement === last) {
				event.preventDefault();
				first.focus();
			}
		}
	});

	document.addEventListener('DOMContentLoaded', function () {
		var dialog = document.querySelector('.moretti-atc[data-atc-open]');
		if (dialog) {
			open(dialog);
		}

		if (window.jQuery) {
			window.jQuery(document.body).on('added_to_cart', function (event, fragments) {
				if (fragments && fragments.moretti_added_to_cart) {
					show(fragments.moretti_added_to_cart);
				}
			});
		}
	});
})();
