# =====================================================================
# BIBLIOTECH PHP-FPM DOCKERFILE
# =====================================================================
#
# Ez a Dockerfile egy speciálisan konfigurált PHP-FPM konténert épít fel
# a Bibliotech Laravel alkalmazás futtatásához.
#
# FŐBB JELLEMZŐK:
# ==============
# ✅ PHP 8.2-FPM alapkép (hivatalos Docker image)
# ✅ Laravel-hez szükséges PHP bővítmények
# ✅ Composer dependency manager
# ✅ MariaDB/MySQL kapcsolat támogatás
# ✅ Optimalizált PHP konfigurációk
# ✅ Biztonságos felhasználói jogosultságok
# ✅ Fejlesztői eszközök (git, curl)
#
# BUILD PARANCS:
# =============
# docker build -t bibliotech-php ./docker/php
#
# MANUAL RUN (teszteléshez):
# =========================
# docker run -d --name test-php -v $(pwd):/var/www/html bibliotech-php
#
# MÉRET OPTIMALIZÁLÁS:
# ===================
# - Multi-stage build nem szükséges (fejlesztői környezet)
# - Alpine verzió használható kisebb mérethez (php:8.2-fpm-alpine)
# - Apt cache tisztítása csökkenti a képfájl méretet

# =====================================================================
# ALAPKÉP VÁLASZTÁSA
# =====================================================================
# PHP 8.2-FPM hivatalos Docker image
# 
# MIÉRT FPM? 
# - FastCGI Process Manager: jobb teljesítmény
# - NGINX-szel való együttműködésre optimalizált
# - Többszálú feldolgozás támogatása
# - Memória hatékonyság
# 
# ALTERNATÍVÁK:
# - php:8.2-apache (beépített Apache webszerver)
# - php:8.2-cli (csak parancssori használatra)
# - php:8.2-fpm-alpine (kisebb méret, de kevesebb előtelepített csomag)
FROM php:8.2-fpm

# =====================================================================
# MUNKAMAPPA BEÁLLÍTÁSA
# =====================================================================
# A konténeren belüli alapértelmezett munkamappa
# Itt fognak elhelyezkedni a Laravel fájlok
WORKDIR /var/www/html

# =====================================================================
# RENDSZER FÜGGŐSÉGEK TELEPÍTÉSE
# =====================================================================
# Ubuntu/Debian csomagok telepítése apt-get-tel
# 
# FONTOS: Egyetlen RUN utasításban csoportosítjuk a telepítést
# Ez csökkenti a Docker layer-ek számát és a képfájl méretet

RUN apt-get update && apt-get install -y \
    # ================================================================
    # ALAPVETŐ FEJLESZTŐI ESZKÖZÖK
    # ================================================================
    # Git verziókezelő - Composer függőségekhez és fejlesztéshez
    git \
    # cURL - HTTP kérések küldéséhez, API teszteléshez
    curl \
    # ================================================================
    # GRAFIKAI ÉS KÉPFELDOLGOZÁSI KÖNYVTÁRAK  
    # ================================================================
    # PNG képformátum támogatás (GD extension-höz)
    libpng-dev \
    # ================================================================
    # SZÖVEG ÉS KARAKTERKÓDOLÁSI KÖNYVTÁRAK
    # ================================================================
    # Oniguruma regex könyvtár (mbstring extension-höz)
    libonig-dev \
    # XML feldolgozási könyvtár (xml extension-höz)
    libxml2-dev \
    # ================================================================
    # FÁJLKEZELÉSI ESZKÖZÖK
    # ================================================================
    # ZIP fájlok létrehozása és kicsomagolása
    zip \
    unzip \
    # ================================================================
    # ADATBÁZIS KAPCSOLÓDÁSI ESZKÖZÖK
    # ================================================================
    # MySQL/MariaDB parancssori kliens (fejlesztéshez és debugginghoz)
    default-mysql-client \
    # ================================================================
    # RENDSZER KONFIGURÁCIÓS ESZKÖZÖK
    # ================================================================
    # Időzóna adatok és beállítások
    tzdata \
    # ================================================================
    # TISZTÍTÁS - FONTOS A KÉPFÁJL MÉRET CSÖKKENTÉSÉHEZ
    # ================================================================
    # Apt cache törlése csökkenti a végső image méretet
    && rm -rf /var/lib/apt/lists/*

# =====================================================================
# PHP BŐVÍTMÉNYEK TELEPÍTÉSE
# =====================================================================
# Laravel alkalmazásokhoz szükséges PHP extension-ök telepítése
# A docker-php-ext-install egy speciális segédprogram a PHP Docker image-ekben
# 
# MIÉRT SZÜKSÉGESEK EZEK A BŐVÍTMÉNYEK?
# ====================================

RUN docker-php-ext-install \
    # ================================================================
    # ADATBÁZIS KAPCSOLAT (KÖTELEZŐ LARAVEL-HEZ)
    # ================================================================
    # PDO MySQL driver - Laravel Eloquent ORM-hez szükséges
    # Ezt használja a Laravel a MariaDB/MySQL adatbázishoz való kapcsolódáshoz
    pdo_mysql \
    # ================================================================
    # KÉPFELDOLGOZÁS ÉS GRAFIKA
    # ================================================================
    # GD könyvtár - képek létrehozása, átméretezése, vágása
    # Hasznos profilképekhez, thumbnail-ekhez, captcha-khoz
    gd \
    # ================================================================
    # TÖBBBÁJTOS KARAKTEREK (UTF-8 TÁMOGATÁS)
    # ================================================================
    # Multibyte String - UTF-8, unicode karakterek kezelése
    # Elengedhetetlen nemzetközi alkalmazásokhoz (magyar ékezetek!)
    mbstring \
    # ================================================================
    # XML FELDOLGOZÁS
    # ================================================================
    # XML parser és generator - API-k, RSS feed-ek, sitemap-ek
    # Laravel config cache-hez is szükséges
    xml \
    # ================================================================
    # MATEMATIKAI MŰVELETEK NAGY PONTOSSÁGGAL
    # ================================================================
    # BCMath - nagy pontosságú matematikai műveletek
    # Pénzügyi számításokhoz, bírságok kalkulációjához hasznos
    bcmath

# ================================================================
# TOVÁBBI HASZNOS EXTENSION-ÖK (opcionálisan hozzáadhatók):
# ================================================================
# 
# Redis cache-hez:
# RUN pecl install redis && docker-php-ext-enable redis
#
# ImageMagick fejlett képfeldolgozáshoz:
# RUN apt-get install -y libmagickwand-dev && \
#     pecl install imagick && docker-php-ext-enable imagick
#
# ZIP fájlkezeléshez:
# RUN docker-php-ext-install zip
#
# Nemzetköziesítéshez:
# RUN docker-php-ext-install intl
#
# Socket kapcsolatokhoz:
# RUN docker-php-ext-install sockets

# =====================================================================
# COMPOSER TELEPÍTÉSE
# =====================================================================
# Composer: PHP dependency manager (mint npm Node.js-hez)
# 
# MULTI-STAGE COPY TECHNIKA:
# ===========================
# A --from=composer:latest azt jelenti, hogy a hivatalos Composer image-ből
# másoljuk ki a composer binary-t a mi image-ünkbe
# 
# ELŐNYÖK:
# - Nincs szükség külön Composer telepítésre
# - Mindig a legfrissebb verziót kapjuk
# - Kisebb végső image méret (csak a binary másolódik)
# 
# ALTERNATÍV TELEPÍTÉSI MÓDOK:
# ============================
# Kézi letöltés:
# RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer
# 
# Apt repository-ból:
# RUN apt-get install composer
# 
# Specifikus verzió:
# COPY --from=composer:2.5 /usr/bin/composer /usr/bin/composer

COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# =====================================================================
# EGYÉNI PHP KONFIGURÁCIÓ
# =====================================================================
# A php.ini fájl testreszabott beállításokat tartalmaz a Laravel optimalizálásához
# 
# FONTOS BEÁLLÍTÁSOK A php.ini-ben:
# =================================
# - memory_limit: memória korlát növelése
# - upload_max_filesize: fájlfeltöltési limit
# - post_max_size: POST kérések mérete  
# - max_execution_time: script futási idő limit
# - display_errors: hibák megjelenítése fejlesztéskor
# - error_reporting: hibajelentési szint
# - date.timezone: időzóna beállítás
# 
# A conf.d könyvtárba helyezett .ini fájlok automatikusan betöltődnek

COPY php.ini /usr/local/etc/php/conf.d/bibliotech.ini

# =====================================================================
# FELHASZNÁLÓI FIÓK ÉS JOGOSULTSÁGOK BEÁLLÍTÁSA
# =====================================================================
# 
# BIZTONSÁGI MEGFONTOLÁSOK:
# ========================
# Konténerekben SOHA ne futtassunk alkalmazásokat root felhasználóként!
# Ez súlyos biztonsági kockázat lenne.
# 
# MIÉRT FONTOS A MEGFELELŐ UID/GID?
# =================================
# - Host és konténer közötti fájljogosultságok kompatibilitása
# - Docker volume mount-ok megfelelő működése
# - Fejlesztői környezetben fájlok szerkeszthetősége
# 
# UID/GID 1000: A legtöbb Linux disztribúcióban az első normál felhasználó

# 1. CSOPORT LÉTREHOZÁSA
# ======================
# Új 'www' csoport létrehozása GID 1000-rel
# -g 1000: csoport ID megadása
RUN groupadd -g 1000 www && \
    # 2. FELHASZNÁLÓ LÉTREHOZÁSA
    # ==========================
    # Új 'www' felhasználó létrehozása UID 1000-rel
    # -u 1000: felhasználó ID megadása
    # -m: home könyvtár létrehozása
    # -s /bin/bash: alapértelmezett shell beállítása
    # -g www: hozzáadás a 'www' csoporthoz
    useradd -u 1000 -ms /bin/bash -g www www

# =====================================================================
# FÁJLJOGOSULTSÁGOK BEÁLLÍTÁSA
# =====================================================================
# 
# MUNKAMAPPA TULAJDONJOGA:
# ========================
# A /var/www/html könyvtár (és alkönyvtárai) tulajdonosának beállítása
# 
# CHMOD 755 JELENTÉSE:
# ===================
# 7 (rwx) - tulajdonos: olvasás, írás, futtatás
# 5 (r-x) - csoport: olvasás, futtatás  
# 5 (r-x) - egyéb: olvasás, futtatás
# 
# Ez biztosítja, hogy:
# - A www felhasználó mindent csinálhat a fájlokkal
# - Mások csak olvashatják és futtathatják őket
# - Webszerver (NGINX) hozzáfér a fájlokhoz

RUN chown -R www:www /var/www/html && \
    chmod -R 755 /var/www/html

# =====================================================================
# AKTÍV FELHASZNÁLÓ VÁLTÁSA
# =====================================================================
# 
# Innentől kezdve minden parancs a 'www' felhasználóként fut
# Ez azt jelenti, hogy:
# - A Laravel fájlok a megfelelő tulajdonossal jönnek létre
# - Nincs root jogosultság a futó alkalmazásnak
# - Biztonsági szempontból optimális
# 
# FONTOS: Ez a sor után már nem tudunk root jogosultságot igénylő
# műveleteket végezni (pl. apt-get install)

USER www

# =====================================================================
# ALTERNATIVE APPROACHES:
# =====================================================================
# 
# Dinamikus UID/GID beállítás build argumentumokkal:
# ARG USER_ID=1000
# ARG GROUP_ID=1000
# RUN groupadd -g $GROUP_ID www && useradd -u $USER_ID -g www www
# 
# Futásidejű jogosultság beállítás:
# ENTRYPOINT ["entrypoint.sh"] (script-ben állítja be a jogokat)
# 
# Alpine Linux esetén:
# RUN addgroup -g 1000 www && adduser -u 1000 -G www -D www

# =====================================================================
# HÁLÓZATI KONFIGURÁCIÓ
# =====================================================================
# 
# PHP-FPM PORT MEGHATÁROZÁSA:
# ===========================
# Port 9000: PHP-FPM alapértelmezett portja
# 
# FONTOS TUDNIVALÓK:
# ==================
# - Ez NEM HTTP port! FastCGI protokoll használja
# - NGINX proxy_pass-al kapcsolódik ehhez a porthoz
# - Külső hozzáférésre nem alkalmas (csak NGINX-en keresztül)
# - TCP socket helyett UNIX socket is használható (jobb teljesítmény)
# 
# UNIX SOCKET ALTERNATÍVA:
# ========================
# EXPOSE helyett php-fpm.conf-ban:
# listen = /var/run/php-fpm.sock
# listen.owner = www
# listen.group = www
# listen.mode = 0660

EXPOSE 9000

# =====================================================================
# KONTÉNER INDÍTÁSI PARANCS
# =====================================================================
# 
# PHP-FPM DAEMON INDÍTÁSA:
# ========================
# A CMD utasítás meghatározza, mi fusson a konténer indításakor
# 
# "php-fpm" PARANCS RÉSZLETEI:
# ============================
# - Foreground módban fut (nem daemon-ként)
# - Docker számára fontos, hogy ne fork-oljon
# - Alapértelmezetten a /usr/local/etc/php-fpm.conf config-ot használja
# - Több worker process-t indít (pm.max_children beállítás alapján)
# 
# ALTERNATIVE COMMANDS:
# ====================
# Fejlesztői módban részletes logging-gal:
# CMD ["php-fpm", "--nodaemonize", "--force-stderr"]
# 
# Custom config fájl használata:
# CMD ["php-fpm", "--fpm-config", "/usr/local/etc/php-fpm.d/custom.conf"]
# 
# Debug módban:
# CMD ["php-fpm", "--allow-to-run-as-root", "--nodaemonize"]
# 
# ENTRYPOINT vs CMD:
# ==================
# CMD: könnyen felülírható docker run-nál
# ENTRYPOINT: mindig futni fog, CMD csak paraméterként lesz hozzáadva

CMD ["php-fpm"]

# =====================================================================
# DODATEK INFORMÁCIÓK
# =====================================================================
# 
# IMAGE OPTIMALIZÁLÁSI TIPPEK:
# ============================
# 
# 1. Multi-stage build használata:
#    FROM php:8.2-fpm as base
#    # ... telepítések ...
#    FROM base as production
#    # ... production specifikus beállítások ...
# 
# 2. .dockerignore fájl használata:
#    node_modules/
#    vendor/
#    .git/
#    storage/logs/
# 
# 3. Layer cache optimalizálás:
#    COPY composer.json composer.lock ./
#    RUN composer install --no-dev --optimize-autoloader
#    COPY . .
# 
# 4. Health check hozzáadása:
#    HEALTHCHECK --interval=30s --timeout=3s --start-period=5s --retries=3 \
#    CMD php artisan tinker --execute="echo 'OK';" || exit 1
# 
# BUILD BEST PRACTICES:
# ====================
# 
# Production build:
# docker build --target production -t bibliotech-php:prod .
# 
# Development build with cache:
# docker build --cache-from bibliotech-php:latest -t bibliotech-php:dev .
# 
# Multi-platform build:
# docker buildx build --platform linux/amd64,linux/arm64 -t bibliotech-php .
# 
# Build argumentumokkal:
# docker build --build-arg PHP_VERSION=8.2 --build-arg USER_ID=1001 .
# 
# DEBUGGING TIPPEK:
# =================
# 
# Konténerbe belépés teszteléshez:
# docker run -it --rm bibliotech-php bash
# 
# PHP verzió ellenőrzése:
# docker run --rm bibliotech-php php -v
# 
# Telepített extension-ök listázása:
# docker run --rm bibliotech-php php -m
# 
# Composer verzió:
# docker run --rm bibliotech-php composer --version
