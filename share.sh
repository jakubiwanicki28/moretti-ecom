#!/bin/bash

# 1. Pobierz publiczne IP (to jest hasło)
echo "🔍 Pobieranie publicznego IP (będzie potrzebne jako hasło)..."
IP=$(curl -s https://ipv4.icanhazip.com)

echo ""
echo "========================================================"
echo "🔑 HASŁO DO TUNELU: $IP"
echo "========================================================"
echo "(Wpisz to IP na stronie, która się otworzy)"
echo ""

# 2. Uruchom localtunnel w tle i zapisz URL
echo "🚀 Uruchamiam tunel..."
# Używamy npx localtunnel
# Zapisujemy output do pliku tymczasowego żeby wyciągnąć URL
npx localtunnel --port 8080 > .tunnel_url 2>&1 &
LT_PID=$!

# Czekamy chwilę aż URL się wygeneruje
sleep 3

# 3. Odczytaj URL i wygeneruj QR kod
URL=$(grep -o "https://.*loca.lt" .tunnel_url | head -n 1)

if [ -z "$URL" ]; then
    echo "❌ Nie udało się uruchomić tunelu. Sprawdź logi."
    cat .tunnel_url
    kill $LT_PID
    exit 1
fi

echo "✅ Twój link: $URL"
echo ""
echo "📱 Zeskanuj ten kod QR telefonem:"
npx qrcode-terminal "$URL"

echo ""
echo "💡 PAMIĘTAJ: Na telefonie zostaniesz poproszony o hasło."
echo "👉 Wpisz: $IP"
echo ""
echo "Naciśnij Ctrl+C aby zatrzymać."

# Czekaj na zakończenie procesu
wait $LT_PID
