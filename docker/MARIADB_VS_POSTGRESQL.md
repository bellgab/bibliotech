# BiblioTech - MariaDB vs PostgreSQL Összehasonlítás

## 🎯 Célja

Ez a dokumentum bemutatja a BiblioTech alkalmazás **párhuzamos adatbázis setupját**, ahol mind MariaDB, mind PostgreSQL elérhető fejlesztési és oktatási célokra.

## 🏗️ Architektúra

```
BiblioTech Docker Stack
├── nginx (proxy)
├── php-fpm (Laravel)
├── mariadb (MySQL kompatibilis)
├── postgresql (PostgreSQL)
├── phpmyadmin (MariaDB admin)
└── pgadmin (PostgreSQL admin - opcionális)
```

## 🚀 Gyors Indítás

### Teljes Stack (Mindkét Adatbázis)
```bash
docker-compose up -d
```

### Csak MariaDB
```bash
docker-compose up -d nginx php mariadb phpmyadmin
```

### Csak PostgreSQL
```bash
docker-compose up -d nginx php postgresql
```

## 📊 Technikai Összehasonlítás

### Alapvető Különbségek

| Aspektus | MariaDB | PostgreSQL |
|----------|---------|------------|
| **Típus** | MySQL fork | Objektum-relációs |
| **Licenc** | GPL v2 | PostgreSQL License |
| **ACID** | Igen (InnoDB) | Igen (natív) |
| **JSON** | JSON típus | JSON + JSONB |
| **Replikáció** | Master-Slave | Streaming + Logical |
| **Teljesítmény** | Gyors OLTP | Komplex lekérdezések |

### Docker Konfiguráció Különbségek

#### MariaDB Setup
```yaml
mariadb:
  image: mariadb:10.11
  environment:
    MYSQL_DATABASE: bibliotech
    MYSQL_USER: bibliotech_user
    MYSQL_PASSWORD: bibliotech_pass
  ports:
    - "3307:3306"
  volumes:
    - mariadb_data:/var/lib/mysql
```

#### PostgreSQL Setup
```yaml
postgresql:
  image: postgres:15
  environment:
    POSTGRES_DB: bibliotech_pg
    POSTGRES_USER: bibliotech_user
    POSTGRES_PASSWORD: bibliotech_pass
  ports:
    - "5433:5432"
  volumes:
    - postgresql_data:/var/lib/postgresql/data
```

### Laravel .env Konfiguráció

#### MariaDB
```env
DB_CONNECTION=mysql
DB_HOST=mariadb
DB_PORT=3306
DB_DATABASE=bibliotech
```

#### PostgreSQL
```env
DB_CONNECTION=pgsql
DB_HOST=postgresql
DB_PORT=5432
DB_DATABASE=bibliotech_pg
```

## 🗃️ Adatbázis Séma Különbségek

### Auto Increment
```sql
-- MariaDB
id INT AUTO_INCREMENT PRIMARY KEY

-- PostgreSQL
id SERIAL PRIMARY KEY
```

### Enum Típusok
```sql
-- MariaDB (inline)
allapot ENUM('uj', 'jo', 'kopott', 'rossz')

-- PostgreSQL (custom type)
CREATE TYPE konyv_allapot_enum AS ENUM ('uj', 'jo', 'kopott', 'rossz');
allapot konyv_allapot_enum
```

### Timestamp Kezelés
```sql
-- MariaDB
created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP

-- PostgreSQL (trigger szükséges)
created_at TIMESTAMPTZ DEFAULT CURRENT_TIMESTAMP,
updated_at TIMESTAMPTZ DEFAULT CURRENT_TIMESTAMP
-- + trigger function
```

### Boolean Típus
```sql
-- MariaDB
aktiv BOOLEAN DEFAULT TRUE  -- TINYINT(1) alias

-- PostgreSQL
aktiv BOOLEAN DEFAULT TRUE  -- natív boolean
```

## 📈 Teljesítmény Összehasonlítás

### MariaDB Előnyei
- **Gyors INSERT/UPDATE műveletek**
- **Egyszerűbb konfiguráció**
- **Kisebb memória igény**
- **MyISAM motor (read-heavy workload)**

### PostgreSQL Előnyei
- **Komplex JOIN műveletek**
- **JSON lekérdezések**
- **Window functions**
- **Partial indexek**
- **Better query optimizer**

## 🛠️ Fejlesztői Eszközök

### MariaDB
- **phpMyAdmin**: http://localhost:8081
- **MySQL Workbench** (külső)
- **Adminer** (alternatíva)

### PostgreSQL
- **pgAdmin**: http://localhost:5050 (opcionális)
- **psql CLI**: `docker-compose exec postgresql psql`
- **DBeaver** (külső)

## 🔧 Használati Esetek

### MariaDB Választása Ekkor:
- **WordPress, Drupal** projektekhez
- **Egyszerű CRUD** műveletekhez  
- **MySQL kompatibilitás** szükséges
- **Gyors prototípus** fejlesztéshez
- **Kisebb csapat** kevesebb PostgreSQL tapasztalattal

### PostgreSQL Választása Ekkor:
- **JSON/NoSQL** hibrid megoldás
- **Komplex analitikai** lekérdezések
- **Geo-spatial** adatok (PostGIS)
- **Full-text search** szükséges
- **Enterprise szintű** alkalmazás

## 📚 Laravel Specifikus Különbségek

### Migration Különbségek
```php
// MariaDB migration
Schema::create('konyvek', function (Blueprint $table) {
    $table->id();
    $table->enum('allapot', ['uj', 'jo', 'kopott', 'rossz']);
    $table->timestamps();
});

// PostgreSQL migration
Schema::create('konyvek', function (Blueprint $table) {
    $table->id();
    $table->enum('allapot', ['uj', 'jo', 'kopott', 'rossz']);
    $table->timestampsTz(); // Timezone aware
});
```

### Query Builder Különbségek
```php
// JSON lekérdezések
// MariaDB
User::whereRaw("JSON_EXTRACT(meta, '$.age') > ?", [18]);

// PostgreSQL (natív operátorok)
User::whereRaw("meta->>'age'::int > ?", [18]);
```

## 🔄 Adatbázis Váltás Lépései

1. **Konfiguráció módosítása**
   ```bash
   cp docker/postgresql/.env.postgresql.example .env
   ```

2. **Konténerek újraindítása**
   ```bash
   docker-compose down
   docker-compose up -d nginx php postgresql
   ```

3. **Migrációk futtatása**
   ```bash
   docker-compose exec php php artisan migrate:fresh --seed
   ```

## 📊 Benchmark Példák

### Egyszerű Lekérdezések
```sql
-- SELECT * FROM konyvek WHERE kategoria = 'Fantasy'
-- MariaDB: ~0.1ms (index)
-- PostgreSQL: ~0.15ms (index)
```

### Komplex JOIN-ok
```sql
-- Multi-table joins with aggregation
-- MariaDB: ~2-5ms
-- PostgreSQL: ~1-3ms (better optimizer)
```

### JSON Műveletek
```sql
-- JSON field queries
-- MariaDB: ~1-2ms
-- PostgreSQL: ~0.5-1ms (JSONB optimized)
```

## 🚨 Gyakori Hibák és Megoldások

### MariaDB Hibák
```bash
# Port foglaltság
ERROR: Port 3306 already in use
# Megoldás: Állítsd le a lokális MySQL/MariaDB szolgáltatást

# Karakterkódolás probléma
SQLSTATE[HY000]: General error: 1366 Incorrect string value
# Megoldás: utf8mb4 charset használata
```

### PostgreSQL Hibák
```bash
# Kapcsolati hiba
SQLSTATE[08006]: Connection failure
# Megoldás: Ellenőrizd a DB_HOST=postgresql beállítást

# Séma nem található
SQLSTATE[3F000]: Invalid schema name
# Megoldás: search_path beállítás vagy public séma használat
```

## 🎓 Oktatási Célok

### Mit Tanulhatsz
1. **Adatbázis architektúra** különbségek
2. **SQL dialektus** eltérések
3. **Teljesítmény tuning** stratégiák
4. **Laravel ORM** adatbázis specifikus funkciók
5. **Docker** multi-service orchestration

### Gyakorlati Feladatok
1. **Migráld át** a MariaDB sémát PostgreSQL-re
2. **Hasonlítsd össze** a lekérdezési terveket
3. **Implementálj** JSON alapú funkciót PostgreSQL-ben
4. **Tesztelj** teljesítményt mindkét rendszerben
5. **Készíts** backup/restore scriptet mindkettőhöz

## 📈 Következő Lépések

### Fejlesztési Irányok
- [ ] **Redis cache** layer hozzáadása
- [ ] **Elasticsearch** full-text search
- [ ] **Read replica** setup mindkét adatbázishoz
- [ ] **Monitoring** (Prometheus, Grafana)
- [ ] **Backup automation** mindkét rendszerhez

### Éles Környezet Felkészítés
- [ ] **SSL/TLS** konfiguráció
- [ ] **Connection pooling** (PgBouncer, ProxySQL)
- [ ] **High availability** setup
- [ ] **Disaster recovery** tervezés
- [ ] **Security hardening**

---

**💡 Tipp**: Kezd a MariaDB-vel ha MySQL háttérrel rendelkezel, vagy PostgreSQL-lel ha fejlettebb funkciókra van szükséged. Mindkét rendszer kiválóan működik a BiblioTech alkalmazással!
