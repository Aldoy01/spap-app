# SPAP App

SPAP App adalah konsep aplikasi Sistem Pelayanan dan Advokasi Publik dengan pemisahan frontend, backend API PHP 7, Redis, dan database relasional. Frontend mockup tetap bisa dibuka langsung, tetapi struktur baru sudah disiapkan untuk dijalankan sebagai aplikasi berbasis container.

## Arsitektur

```text
Browser
  |
  v
Frontend PHP 7 Apache :8080
  |
  | /api
  v
Backend PHP 7 Apache :3000
  |              |
  v              v
PostgreSQL      Redis
:5432           :6379
```

## Komponen

- `frontend`: menyajikan UI dari `index.php` dan folder `assets`.
- `backend`: API PHP 7 untuk tiket aspirasi, pengaduan, OSINT, dan laporan.
- `postgres`: database utama untuk tiket, event, user, OSINT mention, dan report job.
- `redis`: cache query, calon antrean notifikasi, rate limit, dan job ringan.
- `mysql`: opsi service database alternatif melalui Docker profile `mysql`; default aplikasi memakai PostgreSQL. Schema awal tersedia di `backend/src/db/schema.mysql.sql`.

## Menjalankan Dengan Docker

```bash
docker compose up --build
```

Setelah container aktif:

- Frontend: `http://localhost:8080`
- Backend health check: `http://localhost:3000/health`
- API tiket: `http://localhost:3000/api/tickets`

PostgreSQL akan otomatis membuat schema dan data mockup dari:

```text
backend/src/db/schema.postgres.sql
```

## Menjalankan Backend Lokal

Backend utama berjalan di PHP 7 lewat Docker. Untuk development lokal tanpa Docker penuh, jalankan PostgreSQL dan Redis lebih dulu, lalu gunakan PHP 7 dengan extension `pdo_pgsql` dan `redis`.

Jika memakai Docker hanya untuk dependency:

```bash
docker compose up postgres redis
```

Lalu jalankan PHP development server dari folder `php-backend/public`:

```bash
cd php-backend/public
php -S localhost:3000 index.php
```

## Endpoint Awal

- `GET /health`: status backend, PostgreSQL, dan Redis.
- `GET /api/tickets`: daftar tiket, mendukung query `type`, `status`, `region`, `q`.
- `POST /api/tickets`: membuat tiket aspirasi/pengaduan.
- `PATCH /api/tickets/:publicId/status`: update status tiket.
- `GET /api/osint/mentions`: data monitoring OSINT.
- `GET /api/reports/summary`: ringkasan status, kategori, dan OSINT.
- `POST /api/reports/jobs`: membuat job laporan.

## Catatan Database

Default yang disarankan adalah PostgreSQL karena mendukung `JSONB`, constraint yang kuat, indexing fleksibel, dan cocok untuk audit trail. MySQL tetap tersedia sebagai opsi infrastruktur jika deployment wajib mengikuti ekosistem MySQL, tetapi backend PHP 7 saat ini disiapkan untuk PostgreSQL.

Untuk menyalakan MySQL opsional:

```bash
docker compose --profile mysql up mysql
```

## Deploy ke GitHub dan Railway

Panduan deployment ada di:

```text
docs/deploy-github-railway.md
```

Ringkasnya:

- Push project ke GitHub.
- Buat Railway project dari GitHub repo.
- Buat service `spap-api` dari folder `/php-backend`.
- Buat service `spap-frontend` dari root repo dengan `RAILWAY_DOCKERFILE_PATH=frontend/Dockerfile`.
- Tambahkan Railway Postgres dan Redis.
- Set variable `SPAP_API_BASE_URL`, `DATABASE_URL`, `REDIS_URL`, dan `CORS_ORIGIN`.

## Materi Repost / Publikasi

Materi siap salin untuk caption, GitHub description, release notes, dan checklist publikasi tersedia di:

```text
docs/repost-spap-app.md
```
