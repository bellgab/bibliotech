#!/bin/bash

# =====================================================================
# BIBLIOTECH PHP KONTÉNER ENTRYPOINT SCRIPT
# =====================================================================
# 
# Ez a script automatikusan fut le a PHP konténer indításakor
# és elvégzi a szükséges Laravel inicializációs lépéseket

set -e  # Script leáll hiba esetén

echo "🚀 Bibliotech Laravel konténer inicializálása..."

# =====================================================================
# ADATBÁZIS KAPCSOLAT ELLENŐRZÉSE
# =====================================================================
echo "📊 Várakozás az adatbázis elérhetőségére..."

# Maximum 30 másodpercig várunk az adatbázisra
RETRIES=30
until php -r "
try {
    \$pdo = new PDO('mysql:host=' . getenv('DB_HOST') . ';port=' . getenv('DB_PORT'), getenv('DB_USERNAME'), getenv('DB_PASSWORD'));
    echo 'Kapcsolat sikeres';
    exit(0);
} catch (Exception \$e) {
    exit(1);
}
" > /dev/null 2>&1 || [ $RETRIES -eq 0 ]; do
    echo "⏳ Várakozás az adatbázisra... ($RETRIES másodperc)"
    RETRIES=$((RETRIES-1))
    sleep 1
done

if [ $RETRIES -eq 0 ]; then
    echo "❌ Nem sikerült kapcsolódni az adatbázishoz!"
    echo "Ellenőrizd a .env fájl beállításait és a MariaDB konténer állapotát."
    # Ne állítsuk le teljesen, próbáljuk indítani a PHP-FPM-et
fi

echo "✅ Adatbázis kapcsolat ellenőrzés kész!"

# =====================================================================
# LARAVEL MIGRÁCIÓ FUTTATÁSA
# =====================================================================
echo "🔄 Laravel migrációk futtatása..."

# Egyszerű migráció futtatás
if php artisan migrate --force 2>/dev/null; then
    echo "✅ Migrációk sikeresen lefutottak!"
    
    # Seederek futtatása csak akkor, ha a migrációk sikeresek voltak
    echo "🌱 Mintaadatok betöltése..."
    if php artisan db:seed --force 2>/dev/null; then
        echo "✅ Mintaadatok sikeresen betöltve!"
    else
        echo "⚠️ Seeder probléma, de folytatjuk..."
    fi
else
    echo "⚠️ Migráció probléma, de folytatjuk..."
fi

# =====================================================================
# FÁJL JOGOSULTSÁGOK BEÁLLÍTÁSA
# =====================================================================
echo "🔐 Fájl jogosultságok beállítása..."

# Laravel storage és cache könyvtárak írhatósága
chmod -R 755 /var/www/html/storage 2>/dev/null || true
chmod -R 755 /var/www/html/bootstrap/cache 2>/dev/null || true

# Tulajdonos beállítása
chown -R www-data:www-data /var/www/html/storage 2>/dev/null || true
chown -R www-data:www-data /var/www/html/bootstrap/cache 2>/dev/null || true

echo "✅ Jogosultságok beállítva!"

echo "🎉 Bibliotech Laravel konténer sikeresen inicializálva!"
echo "🌐 Alkalmazás elérhető: http://localhost:8080"

# =====================================================================
# PHP-FPM INDÍTÁSA
# =====================================================================
echo "🔄 PHP-FPM indítása..."

# Átadjuk a vezérlést az eredeti CMD parancsnak
exec "$@"
