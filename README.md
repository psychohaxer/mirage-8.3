# Mirage Dev Stack 8.3

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
- `docker/php/Dockerfile` — PHP-FPM image (builds PHP 8.3 + common extensions and composer).
- `docker/php/uploads.ini` — custom PHP ini additions.
- `docker/phpmyadmin/` — phpMyAdmin build and config.
- `src/` — application source mounted into containers. Put your project files under `src/public` (the Nginx document root).

**How it works**

- The `app` service builds a PHP-FPM image and runs as a non-root `app` user.
- The `web` service runs Nginx and forwards `.php` requests to `app:9000`.
- `db` runs MariaDB and persists data in the `dbdata` volume.
- `phpmyadmin` connects to the `db` service for DB management.

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
php artisan migrate   # if using Laravel, etc.
```

If you need to run Composer as the host user, rebuild with appropriate `APP_UID`/`APP_GID` args or invoke with `--user`.

**Adding a project**

Place your app files under `src/public`. The web root is `/var/www/html/public` in the containers. Replace the sample `index.php` with your application front controller.

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

**Where to look in this repo**

- See `docker-compose.yml` for service wiring and ports.
- Nginx config: `docker/nginx/default.conf`.
- PHP Dockerfile: `docker/php/Dockerfile`.
- App entry: `src/public/index.php`.
