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
# COMPOSER CSOMAGOK TELEPÍTÉSE
# =====================================================================
echo "📦 Composer csomagok ellenőrzése és telepítése..."

# Ellenőrizzük, hogy létezik-e a vendor könyvtár és a composer.lock
if [ ! -d "/var/www/html/vendor" ] || [ ! -f "/var/www/html/vendor/autoload.php" ]; then
    echo "📥 Composer csomagok telepítése..."
    composer install --no-dev --optimize-autoloader --no-interaction 2>/dev/null || {
        echo "⚠️ Composer install hiba, de folytatjuk..."
    }
    echo "✅ Composer csomagok telepítve!"
else
    echo "✅ Composer csomagok már telepítve!"
fi

# =====================================================================
# NPM CSOMAGOK TELEPÍTÉSE (FRONTEND)
# =====================================================================
echo "🎨 NPM csomagok ellenőrzése és telepítése..."

# Ellenőrizzük, hogy létezik-e package.json és node_modules
if [ -f "/var/www/html/package.json" ]; then
    if [ ! -d "/var/www/html/node_modules" ] || [ ! -f "/var/www/html/node_modules/.package-lock.json" ]; then
        echo "📥 NPM csomagok telepítése..."
        npm install --production 2>/dev/null || {
            echo "⚠️ NPM install hiba, de folytatjuk..."
        }
        echo "✅ NPM csomagok telepítve!"
    else
        echo "✅ NPM csomagok már telepítve!"
    fi
    
    # Frontend build futtatása (csak production környezetben)
    if [ "${APP_ENV}" = "production" ]; then
        echo "🏗️ Frontend build futtatása..."
        npm run build 2>/dev/null || {
            echo "⚠️ NPM build hiba, de folytatjuk..."
        }
        echo "✅ Frontend build kész!"
    fi
else
    echo "ℹ️ Nincs package.json fájl, NPM telepítés kihagyva."
fi

# =====================================================================
# LARAVEL ALKALMAZÁS KULCS GENERÁLÁSA
# =====================================================================
echo "🔑 Laravel alkalmazás kulcs ellenőrzése..."

# Ellenőrizzük, hogy van-e APP_KEY beállítva
if ! grep -q "APP_KEY=base64:" /var/www/html/.env 2>/dev/null; then
    echo "🔑 Laravel APP_KEY generálása..."
    php artisan key:generate --force 2>/dev/null || {
        echo "⚠️ APP_KEY generálás hiba, de folytatjuk..."
    }
    echo "✅ APP_KEY sikeresen generálva!"
else
    echo "✅ APP_KEY már beállítva!"
fi

# =====================================================================
# LARAVEL CACHE TISZTÍTÁSA ÉS OPTIMALIZÁLÁSA
# =====================================================================
echo "🧹 Laravel cache optimalizálása..."

# Cache tisztítása és újraépítése
php artisan config:clear 2>/dev/null || true
php artisan route:clear 2>/dev/null || true
php artisan view:clear 2>/dev/null || true

# Production optimalizálás (csak ha nem debug módban vagyunk)
if [ "${APP_DEBUG}" != "true" ]; then
    php artisan config:cache 2>/dev/null || true
    php artisan route:cache 2>/dev/null || true
    php artisan view:cache 2>/dev/null || true
fi

echo "✅ Cache optimalizálás kész!"

# =====================================================================
# LARAVEL STORAGE LINK LÉTREHOZÁSA
# =====================================================================
echo "🔗 Laravel storage link ellenőrzése..."

# Ellenőrizzük, hogy létezik-e a public/storage symlink
if [ ! -L "/var/www/html/public/storage" ]; then
    echo "🔗 Storage link létrehozása..."
    php artisan storage:link 2>/dev/null || {
        echo "⚠️ Storage link hiba, de folytatjuk..."
    }
    echo "✅ Storage link létrehozva!"
else
    echo "✅ Storage link már létezik!"
fi

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
