# Mirage Dev Stack 8.3

[🇮🇩 Versi Indonesia di bawah](#versi-indonesia)

[![License](https://img.shields.io/badge/license-MIT-green.svg)](LICENSE) [![Docker Compose](https://img.shields.io/badge/Docker%20Compose-ready-blue.svg)](https://docs.docker.com/compose/)

A lightweight, XAMPP-like developer environment based on Docker Compose. Provides PHP-FPM (PHP 8.3), Nginx, MariaDB and phpMyAdmin for local development and simple deployment workflows.

**Quick Start**

Requirements: Docker and Docker Compose.

1. Build and start the stack:

```bash
docker compose up -d --build
```

2. Open the app in your browser:

- Web site: http://localhost:8080 (controlled by `PORT_WEB`)
- phpMyAdmin: http://localhost:8081 (controlled by `PORT_PMA`)

3. Stop the stack:

```bash
docker compose down
```

**Repository layout**

- `docker-compose.yml` — Compose services (`app`, `web`, `db`, `phpmyadmin`).
- `docker/nginx/default.conf` — Nginx vhost; document root is `/var/www/html/public`.
- `docker/php/Dockerfile` — PHP-FPM image (builds PHP 8.3 Alpine + Node.js + NPM + common extensions and composer).
- `docker/php/uploads.ini` — custom PHP ini additions.
- `src/` — application source mounted into containers. Put your project files under `src/public` (the Nginx document root).

**How it works**

- The `app` service builds a PHP-FPM image and runs as a non-root `app` user.
- The `web` service runs Nginx and forwards `.php` requests to `app:9000`.
- `db` runs official MariaDB 10.11 and persists data in the `dbdata` volume.
- `phpmyadmin` connects to the `db` service for DB management (using the official lightweight image).

**Optional services (profiles)**

This stack supports optional developer services (Postgres, Redis) that are defined in `docker-compose.yml` but are inactive by default. They can be enabled with Compose profiles so you don't need to comment/uncomment services.

- Start default stack only:

```bash
docker compose up -d
```

- Start with Postgres:

```bash
docker compose --profile postgres up -d
```

- Start with Redis:

```bash
docker compose --profile redis up -d
```

Or enable multiple profiles with the `COMPOSE_PROFILES` env var:

```bash
export COMPOSE_PROFILES=postgres,redis
docker compose up -d
```

**Production compose**

Use the production override file to run a production-friendly configuration together with the main compose file:

```bash
docker compose -f docker-compose.yml -f docker-compose.prod.yml up -d
```

The production override adjusts ports and restart policies and relies on profiles in the main file to avoid starting dev-only services like phpMyAdmin.

**Environment / Configuration**

- Environment variables are read by `docker-compose.yml`. Useful variables:
  - `PORT_WEB` (default `8080`) — host port for Nginx
  - `PORT_PMA` (default `8081`) — host port for phpMyAdmin
  - `MYSQL_ROOT_PASSWORD`, `MYSQL_DATABASE`, `MYSQL_USER`, `MYSQL_PASSWORD`
  - `APP_CONTAINER_NAME`, `WEB_CONTAINER_NAME`, `DB_CONTAINER_NAME`, `PMA_CONTAINER_NAME`

You can create a `.env` file in the repo root to override defaults.

Example `.env`:

```env
PORT_WEB=8080
PORT_PMA=8081
MYSQL_ROOT_PASSWORD=root
MYSQL_DATABASE=mirage_db
MYSQL_USER=mirage_user
MYSQL_PASSWORD=mirage_pass
```

**Using Composer / Artisan / CLI**

To run commands inside the PHP container:

```bash
docker compose exec app bash
# then from inside container (as `app` user):
composer install
npm install
npm run dev           # if compiling frontend assets (Tailwind/Vite)
php artisan migrate   # if using Laravel, etc.
```

If you need to run Composer as the host user, rebuild with appropriate `APP_UID`/`APP_GID` args or invoke with `--user`.

**Adding a project**

Place your app files under `src/public`. The web root is `/var/www/html/public` in the containers. Replace the sample `index.php` with your application front controller.

**Project directory examples**

Below are common layouts for placing framework projects under `src`. Nginx is configured to serve from `src/public` by default; adjust if your framework uses a different public folder.

- Laravel

```
src/
└─ your-laravel-app/
  ├─ app/
  ├─ bootstrap/
  ├─ config/
  ├─ public/    <-- web root (map to `src/public` or symlink)
  ├─ resources/
  ├─ routes/
  ├─ storage/
  └─ artisan
```

Place the contents of `your-laravel-app/public` into `src/public` or set Nginx `root` to `src/your-laravel-app/public`.

- Symfony

```
src/
└─ your-symfony-app/
  ├─ bin/
  ├─ config/
  ├─ public/    <-- web root (index.php)
  ├─ src/
  ├─ var/
  └─ vendor/
```

Point Nginx to `src/your-symfony-app/public` or copy the `public` contents into `src/public`.

- CodeIgniter (4)

```
src/
└─ your-ci-app/
  ├─ app/
  ├─ public/    <-- web root (index.php)
  ├─ system/
  └─ writable/
```

Copy `public` into `src/public` or point Nginx to `src/your-ci-app/public`.

- CakePHP

```
src/
└─ your-cake-app/
  ├─ bin/
  ├─ config/
  ├─ logs/
  ├─ plugins/
  ├─ templates/
  ├─ tmp/
  ├─ vendor/
  └─ webroot/    <-- web root (index.php)
```

Map `webroot` to `src/public` or update Nginx `root` to `src/your-cake-app/webroot`.

- Other / plain PHP

For simple PHP apps, place `index.php` and assets into `src/public` and put other PHP classes under `src/src` or a preferred structure.

If you prefer to keep each project self-contained under `src/<project>`, update Nginx `root` in `docker/nginx/default.conf` to the chosen public folder.

**Customization & Common edits**

- Change the PHP version by editing `docker/php/Dockerfile` base image tag.
- Update Nginx rules in `docker/nginx/default.conf`.
- Persist additional data by adding volumes under `volumes:` in `docker-compose.yml`.

**Troubleshooting**

- Port already in use: set `PORT_WEB`/`PORT_PMA` in `.env` to free ports.
- Build issues: `docker compose up --build --force-recreate`.
- Permission issues: ensure `APP_UID`/`APP_GID` match your host user (compose supports passing `UID`/`GID`).
- Logs: `docker compose logs -f web`, `docker compose logs -f app`, `docker compose logs -f db`.

**Deployment notes**

This stack is optimized for local development. For production use:

- Use production-ready images (e.g., full Debian/Alpine production images) and tune PHP-FPM/Nginx configs.
- Secure database credentials using secrets or environment management.
- Add TLS (reverse proxy or load balancer) and restrict phpMyAdmin access.

**License**

This project is released under the MIT License — see the `LICENSE` file for details. Feel free to use, modify and share.

**Where to look in this repo**

- See `docker-compose.yml` for service wiring and ports.
- Nginx config: `docker/nginx/default.conf`.
- PHP Dockerfile: `docker/php/Dockerfile`.
- App entry: `src/public/index.php`.

---

<a name="versi-indonesia"></a>
# 🇮🇩 Versi Indonesia

Lingkungan pengembangan (*developer environment*) yang ringan layaknya XAMPP berbasis Docker Compose. Menyediakan PHP-FPM (PHP 8.3), Nginx, MariaDB, dan phpMyAdmin untuk pengembangan lokal dan *deployment* sederhana.

**Mulai Cepat**

Persyaratan: Docker dan Docker Compose.

1. Build dan jalankan stack:
```bash
docker compose up -d --build
```

2. Buka aplikasi di browsermu:
- Web site: http://localhost:8080 (diatur oleh `PORT_WEB`)
- phpMyAdmin: http://localhost:8081 (diatur oleh `PORT_PMA`)

3. Matikan stack:
```bash
docker compose down
```

**Struktur Repositori**
- `docker-compose.yml` — Service Compose (`app`, `web`, `db`, `phpmyadmin`).
- `docker/nginx/default.conf` — Nginx vhost; document root berada di `/var/www/html/public`.
- `docker/php/Dockerfile` — Image PHP-FPM (membangun PHP 8.3 Alpine + Node.js + NPM + ekstensi umum dan composer).
- `docker/php/uploads.ini` — Konfigurasi tambahan PHP.
- `src/` — Source code aplikasi yang di-*mount* ke dalam kontainer. Letakkan kodemu di dalam `src/public` (Nginx document root).

**Cara Kerjanya**
- Service `app` membangun image PHP-FPM dan berjalan sebagai user non-root `app`.
- Service `web` menjalankan Nginx dan meneruskan request `.php` ke `app:9000`.
- `db` menjalankan MariaDB 10.11 *official* dan menyimpan data secara persisten di volume `dbdata`.
- `phpmyadmin` terhubung ke service `db` untuk manajemen database (menggunakan image ringan *official*).

**Service Opsional (Profiles)**
Stack ini mendukung service tambahan untuk developer (Postgres, Redis) yang didefinisikan di `docker-compose.yml` tetapi tidak aktif secara *default*. Mereka bisa diaktifkan menggunakan *Compose profiles*.

- Hanya jalankan stack bawaan:
```bash
docker compose up -d
```
- Jalankan beserta Postgres:
```bash
docker compose --profile postgres up -d
```

**Compose Production**
Gunakan file *override* production untuk menjalankan konfigurasi production secara bersamaan dengan file compose utama:
```bash
docker compose -f docker-compose.yml -f docker-compose.prod.yml up -d
```

**Konfigurasi / Environment**
Buat file `.env` di *root* repositori untuk menimpa konfigurasi *default*:
```env
PORT_WEB=8080
PORT_PMA=8081
MYSQL_ROOT_PASSWORD=root
MYSQL_DATABASE=mirage_db
MYSQL_USER=mirage_user
MYSQL_PASSWORD=mirage_pass
```

**Menggunakan Composer / NPM / Artisan**
Untuk menjalankan perintah di dalam kontainer PHP:
```bash
docker compose exec app bash
# Kemudian dari dalam kontainer:
composer install
npm install
npm run dev           # untuk kompilasi frontend
php artisan migrate
```
