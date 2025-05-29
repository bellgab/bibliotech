# Bibliotech Projekt Indítási Útmutató

Ez a szkript automatikusan létrehozza és beállítja a teljes Bibliotech Laravel alkalmazást.

## Előfeltételek
- Docker és Docker Compose telepítve
- Git telepítve

## Automatikus telepítés

### 1. Docker konténerek indítása
echo "🐳 Docker konténerek indítása..."
docker-compose up -d

### 2. Várakozás az adatbázis elindulására
echo "⏳ Várakozás az adatbázis elindulására..."
sleep 30

### 3. Laravel alkalmazás létrehozása
echo "🚀 Laravel alkalmazás létrehozása..."
docker-compose exec php composer create-project laravel/laravel . --prefer-dist

### 4. Laravel konfigurációs fájl beállítása
echo "⚙️ Laravel környezeti változók beállítása..."
docker-compose exec php cp .env.example .env
docker-compose exec php php artisan key:generate

### 5. Adatbázis konfiguráció frissítése
docker-compose exec php sed -i 's/DB_HOST=127.0.0.1/DB_HOST=mariadb/' .env
docker-compose exec php sed -i 's/DB_DATABASE=laravel/DB_DATABASE=bibliotech/' .env
docker-compose exec php sed -i 's/DB_USERNAME=root/DB_USERNAME=bibliotech_user/' .env
docker-compose exec php sed -i 's/DB_PASSWORD=/DB_PASSWORD=bibliotech_pass/' .env

### 6. Laravel migráció és seederek futtatása
echo "📊 Adatbázis migrációk futtatása..."
docker-compose exec php php artisan migrate

echo "🌱 Mintaadatok betöltése..."
docker-compose exec php php artisan db:seed

### 7. Composer telepítések
echo "📦 Composer csomagok telepítése..."
docker-compose exec php composer install

### 8. Jogosultságok beállítása
echo "🔐 Fájl jogosultságok beállítása..."
docker-compose exec php chown -R www:www /var/www/html
docker-compose exec php chmod -R 755 /var/www/html/storage
docker-compose exec php chmod -R 755 /var/www/html/bootstrap/cache

echo "✅ Bibliotech alkalmazás sikeresen telepítve!"
echo ""
echo "🌐 Alkalmazás elérhetősége:"
echo "   Weboldal: http://localhost:8080"
echo "   phpMyAdmin: http://localhost:8081"
echo ""
echo "📚 További parancsok:"
echo "   docker-compose logs     # Naplók megtekintése"
echo "   docker-compose down     # Leállítás"
echo "   docker-compose up -d    # Újraindítás"
