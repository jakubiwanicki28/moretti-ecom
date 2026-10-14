#!/usr/bin/env bash
# Stawia lokalną kopię sklepu w stanie odpowiadającym produkcji (wersje, strony koszyka/kasy,
# metody dostawy i płatności). Bezpieczne do ponownego uruchomienia.
#
#   docker compose up -d && scripts/local-env/setup.sh
#
# Konfigurację checkoutu (które dostawy/płatności i w jakiej kolejności) nakłada osobno
# scripts/checkout-config.py — ten sam skrypt, którym zmienia się produkcję.
set -euo pipefail
cd "$(dirname "$0")/../.."

URL="http://localhost:8080"
WC_VERSION="11.0.1"
APACZKA_VERSION="1.4.4"
ACF_VERSION="6.8.10"
# P24 nie publikuje wersji na wordpress.org; ten adres zawsze daje najnowszą (2026-10-14: 1.2.0, jak produkcja).
P24_URL="https://www.przelewy24.pl/do-pobrania/woocommerce-aktualizacja"

wp() { docker compose exec -T cli wp "$@"; }

echo "→ czekam na bazę"
until wp db check >/dev/null 2>&1; do sleep 2; done

if ! wp core is-installed 2>/dev/null; then
  wp core install --url="$URL" --title="Moretti (local)" --admin_user=admin --admin_password=admin \
    --admin_email=admin@moretti.local --skip-email
fi
wp language core install pl_PL --activate >/dev/null
wp rewrite structure '/%postname%/' --hard >/dev/null

echo "→ wtyczki (wersje z produkcji)"
wp plugin is-installed woocommerce || wp plugin install woocommerce --version="$WC_VERSION"
wp plugin is-installed apaczka-pl || wp plugin install apaczka-pl --version="$APACZKA_VERSION"
wp plugin is-installed advanced-custom-fields || wp plugin install advanced-custom-fields --version="$ACF_VERSION"
wp plugin is-installed woo-przelewy24 || wp plugin install "$P24_URL"
wp plugin activate woocommerce apaczka-pl advanced-custom-fields woo-przelewy24
wp language plugin install --all pl_PL >/dev/null 2>&1 || true
wp theme activate moretti-ecom

echo "→ ustawienia sklepu"
wp option update woocommerce_default_country 'PL'
wp option update woocommerce_currency 'PLN'
wp option update woocommerce_price_thousand_sep ' '
wp option update woocommerce_price_decimal_sep ','
wp option update woocommerce_coming_soon 'no'
wp option update woocommerce_store_address 'Nadręczna 14'
wp option update woocommerce_store_city 'Gdańsk'
wp option update woocommerce_store_postcode '80-000'
wp option update woocommerce_enable_guest_checkout 'yes'
wp option update woocommerce_enable_signup_and_login_from_checkout 'yes'

echo "→ strony koszyka i kasy (treść bloków z produkcji)"
wp eval-file /local-env/setup-pages-and-shipping.php

echo "→ Przelewy24 (sandbox + atrapa listy metod, stan jak na produkcji)"
wp option update p24_mode 'sandbox'
wp option update p24_merchant_id '100000'
wp option update p24_crc_key 'local-crc'
wp option update p24_reports_key 'local-reports'
wp option update woocommerce_p24-online-payments_settings --format=json \
  '{"enabled":"yes","title":"Szybki przelew online / BLIK (Przelewy24)","description":"Po wybraniu tej płatności zostaniesz przekierowany do płatności Przelewy24","show_available_methods":"yes","feature_as_gateway":"yes","gateway_methods":"303,154:","fee_enabled":"no"}'
wp option update woocommerce_p24-card_settings --format=json '{"enabled":"yes","title":"Płatność kartą"}'
wp option update woocommerce_p24-blik_settings --format=json '{"enabled":"yes"}'
wp transient delete --all >/dev/null

echo "→ produkty z produkcji"
if [ "$(wp post list --post_type=product --format=count)" -lt 5 ]; then
  wp eval-file /local-env/import-products.php
fi

echo "→ hasło aplikacji dla scripts/checkout-config.py"
if [ ! -f scripts/local-env/.app-password ]; then
  wp user application-password create admin checkout-config --porcelain > scripts/local-env/.app-password
fi

echo "✓ gotowe: $URL  (wp-admin: admin / admin)"
