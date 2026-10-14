/**
 * Checkout (blokowy WooCommerce) — uzupełnienia do wtyczek, których kodu nie zmieniamy.
 * Część serwerowa: inc/checkout.php.
 *
 * 1. Jedna zgoda: stan checkboxa „Warunki” trafia do serwera (extension „moretti”), który bez niej
 *    odrzuca zamówienie. Ukryty checkbox regulaminu Przelewy24 trzymamy stale zaznaczony — P24 dostaje
 *    zamówienie tylko po przejściu naszej zgody, a jego odznaczony checkbox przy nieudanej próbie
 *    zostawiał płatność w stanie błędu („Wystąpił problem z metodą płatności” przy kolejnym kliknięciu).
 * 2. Mapa Apaczki: widget wysyła wpisany tekst do wyszukiwarki OpenStreetMap bez ograniczenia kraju
 *    i bierze pierwszy wynik („Rudna” → Mołdawia, „00-001 wwa” → Alaska; brak wyniku wywraca mapę).
 *    Startujemy ze współrzędnych kodu pocztowego z naszego serwera, a wyszukiwarkę w mapie
 *    ograniczamy do Polski i uczymy kodów pocztowych.
 * 3. Brak punktu odbioru: zamiast okienka wtyczki — komunikat pod przyciskiem wyboru punktu.
 */
(function () {
	'use strict';

	var data = window.wp && window.wp.data;
	if (!data) {
		return;
	}

	var TERMS_INPUT = '.wc-block-checkout__terms input[type="checkbox"]';
	var P24_INPUT = '.p24-checkbox input[type="checkbox"]';
	var termsAccepted = false;

	/* 1. Zgoda ------------------------------------------------------------------------------- */

	function acceptP24Regulation() {
		document.querySelectorAll(P24_INPUT).forEach(function (input) {
			if (!input.checked) {
				input.click(); // kontrolowany przez React — click() przechodzi przez jego onChange
			}
		});
	}

	function setTermsAccepted(accepted) {
		termsAccepted = accepted;
		data.dispatch('wc/store/checkout').setExtensionData('moretti', { terms_accepted: accepted });
	}

	document.addEventListener('change', function (event) {
		if (event.target.matches && event.target.matches(TERMS_INPUT)) {
			setTermsAccepted(event.target.checked);
		}
	});

	/* 2. Mapa punktów ------------------------------------------------------------------------ */

	function shippingAddress() {
		try {
			return data.select('wc/store/cart').getCustomerData().shippingAddress || {};
		} catch (e) {
			return {};
		}
	}

	var POLAND_VIEW = { center: { lat: 52.07, lng: 19.48 }, zoom: 6 };
	var POSTCODE = /^\d{2}-?\d{3}$/;
	var postcodeCache = {};

	function lookupPostcode(code) {
		var config = window.morettiCheckout || {};
		code = String(code || '').trim();
		if (!config.postcodeUrl || !POSTCODE.test(code)) {
			return Promise.resolve(null);
		}
		if (!postcodeCache[code]) {
			postcodeCache[code] = fetch(config.postcodeUrl + encodeURIComponent(code))
				.then(function (response) { return response.ok ? response.json() : null; })
				.catch(function () { return null; });
		}
		return postcodeCache[code];
	}

	function viewForPostcode(hit) {
		if (!hit) {
			return POLAND_VIEW;
		}
		var zoom = { code: 13, area: 12, region: 9 }[hit.precision] || 12;
		return { center: { lat: hit.lat, lng: hit.lng }, zoom: zoom };
	}

	function patchPointMap() {
		var widget = window.BPWidget;
		if (!widget || typeof widget.init !== 'function' || widget.__morettiPatched) {
			return;
		}
		var init = widget.init;
		widget.init = function (element, options) {
			var self = this;
			var address = shippingAddress();
			var country = (address.country || 'PL').toUpperCase();
			var hit = country === 'PL' ? lookupPostcode(address.postcode) : Promise.resolve(null);
			hit.then(function (point) {
				init.call(self, element, Object.assign({}, options, {
					countryCodes: country,
					initialAddress: '', // pusty = widget nie geokoduje sam, tylko bierze mapOptions.center
					mapOptions: Object.assign({}, options.mapOptions, viewForPostcode(point))
				}));
			});
		};
		widget.__morettiPatched = true;
	}

	// Pole „Szukaj punktu” w mapie: tylko Polska, a kod pocztowy z naszych danych (OSM słabo zna polskie kody).
	function patchMapSearch() {
		var Provider = window.GeoSearch && window.GeoSearch.OpenStreetMapProvider;
		if (!Provider || Provider.prototype.__morettiPatched) {
			return;
		}
		var endpoint = Provider.prototype.endpoint;
		var search = Provider.prototype.search;
		Provider.prototype.endpoint = function (args) {
			var url = endpoint.call(this, args);
			return url + (url.indexOf('?') === -1 ? '?' : '&') + 'countrycodes=pl&accept-language=pl';
		};
		Provider.prototype.search = function (args) {
			var self = this;
			var query = String((args && args.query) || '').trim();
			if (!POSTCODE.test(query)) {
				return search.call(self, args);
			}
			return lookupPostcode(query).then(function (point) {
				if (!point) {
					return search.call(self, args);
				}
				return [{
					x: point.lng,
					y: point.lat,
					label: query,
					bounds: [[point.lat - 0.02, point.lng - 0.03], [point.lat + 0.02, point.lng + 0.03]],
					raw: point
				}];
			});
		};
		Provider.prototype.__morettiPatched = true;
	}

	/* 3. Wymagany punkt odbioru ---------------------------------------------------------------- */

	var POINT_ERROR_ID = 'moretti-point-error';

	function selectedRateNeedsPoint() {
		var config = (window.apaczka_block && window.apaczka_block.map_config) || {};
		var packages;
		try {
			packages = data.select('wc/store/cart').getShippingRates() || [];
		} catch (e) {
			return false;
		}
		var selected = packages[0] && (packages[0].shipping_rates || []).find(function (rate) {
			return rate.selected;
		});
		if (!selected) {
			return false;
		}
		return Object.prototype.hasOwnProperty.call(config, String(selected.rate_id).split(':').pop());
	}

	function hasPoint() {
		var input = document.getElementById('apaczka-point');
		return !!(input && input.value);
	}

	function clearPointError() {
		var error = document.getElementById(POINT_ERROR_ID);
		if (error) {
			error.remove();
		}
		document.querySelectorAll('.moretti-point-missing').forEach(function (el) {
			el.classList.remove('moretti-point-missing');
		});
	}

	function showPointError() {
		var button = document.getElementById('apaczka_pl_geowidget_block');
		if (!button) {
			return;
		}
		button.classList.add('moretti-point-missing');
		if (!document.getElementById(POINT_ERROR_ID)) {
			var error = document.createElement('p');
			error.id = POINT_ERROR_ID;
			error.className = 'moretti-point-error';
			error.setAttribute('role', 'alert');
			error.textContent = 'Wybierz punkt odbioru, aby złożyć zamówienie.';
			button.insertAdjacentElement('afterend', error);
		}
		button.scrollIntoView({ behavior: 'smooth', block: 'center' });
	}

	// Faza capture: zatrzymujemy kliknięcie, zanim dojdzie do Reacta i do okienka Apaczki.
	document.addEventListener('click', function (event) {
		var target = event.target;
		if (!target.closest || !target.closest('.wc-block-components-checkout-place-order-button')) {
			return;
		}
		if (selectedRateNeedsPoint() && !hasPoint()) {
			event.preventDefault();
			event.stopPropagation();
			showPointError();
		}
	}, true);

	/* Wspólny obserwator — bloki renderują się asynchronicznie i przerysowują przy zmianach. ---- */

	new MutationObserver(function () {
		var terms = document.querySelector(TERMS_INPUT);
		if (terms && terms.checked !== termsAccepted) {
			setTermsAccepted(terms.checked);
		}
		acceptP24Regulation();
		// Apaczka podmienia etykietę na „Telefon (wymagany)” po wyborze punktu — telefon jest wymagany zawsze.
		var phoneLabel = document.querySelector('label[for="shipping-phone"]');
		if (phoneLabel && phoneLabel.textContent === 'Telefon (wymagany)') {
			phoneLabel.textContent = 'Numer telefonu';
		}
		patchPointMap();
		patchMapSearch();
		if (hasPoint() || !selectedRateNeedsPoint()) {
			clearPointError();
		}
	}).observe(document.body, { childList: true, subtree: true, attributes: true, attributeFilter: ['style'] });

	setTermsAccepted(false);
})();
