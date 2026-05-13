# Publikasi SPAP App ke GitHub dan Railway

Dokumen ini menjelaskan mekanisme publikasi SPAP App memakai GitHub sebagai source code repository dan Railway sebagai platform hosting.

## Arsitektur Deployment

```text
GitHub Repository
  |
  | auto deploy
  v
Railway Project
  |-- frontend service: PHP 7 Apache, root repo, Dockerfile frontend/Dockerfile
  |-- backend service: PHP 7 Apache, root php-backend, Dockerfile php-backend/Dockerfile
  |-- Postgres service
  |-- Redis service
```

Frontend dan backend dipisah menjadi dua service agar domain publiknya jelas dan scaling-nya bisa dipisah. PostgreSQL dan Redis memakai service bawaan Railway.

## 1. Siapkan GitHub Repository

Jalankan dari root project:

```bash
git init
git add .
git commit -m "Initial SPAP app"
git branch -M main
git remote add origin https://github.com/USERNAME/spap-app.git
git push -u origin main
```

Jika repository sudah ada:

```bash
git status
git add .
git commit -m "Prepare Railway deployment"
git push
```

## 2. Buat Project Railway

1. Buka Railway.
2. Buat project baru.
3. Pilih `Deploy from GitHub repo`.
4. Pilih repository `spap-app`.

Railway mendukung monorepo dengan mengatur root directory per service. Dokumentasi Railway menyarankan root directory untuk service yang terisolasi di monorepo, dan Dockerfile harus berada di root source directory atau ditentukan lewat variable `RAILWAY_DOCKERFILE_PATH`.

## 3. Tambahkan Database dan Redis

Di project Railway:

1. Add service `Postgres`.
2. Add service `Redis`.

Railway akan menyediakan variable koneksi seperti `DATABASE_URL` dan `REDIS_URL`.

## 4. Service Backend PHP 7

Buat service dari GitHub repo yang sama.

Pengaturan service:

- Service name: `spap-api`
- Root Directory: `/php-backend`
- Dockerfile: otomatis memakai `/php-backend/Dockerfile`
- Public Networking: aktifkan domain publik

Variables backend:

```env
CORS_ORIGIN=https://DOMAIN-FRONTEND-RAILWAY
DATABASE_URL=${{Postgres.DATABASE_URL}}
REDIS_URL=${{Redis.REDIS_URL}}
```

Setelah deploy, cek:

```text
https://DOMAIN-BACKEND-RAILWAY/health
```

Response sehat:

```json
{
  "status": "ok",
  "runtime": "php7",
  "services": {
    "db": "ok",
    "cache": "ok"
  }
}
```

## 5. Service Frontend PHP 7

Buat service kedua dari GitHub repo yang sama.

Pengaturan service:

- Service name: `spap-frontend`
- Root Directory: `/`
- Variable `RAILWAY_DOCKERFILE_PATH`: `frontend/Dockerfile`
- Public Networking: aktifkan domain publik

Variables frontend:

```env
SPAP_API_BASE_URL=https://DOMAIN-BACKEND-RAILWAY
```

Frontend akan membaca `SPAP_API_BASE_URL` dari PHP dan mengirim request API ke backend.

## 6. Migrasi Database

Untuk deployment pertama, schema PostgreSQL perlu dijalankan ke database Railway.

Cara paling sederhana:

1. Buka service Postgres di Railway.
2. Buka query console atau gunakan koneksi eksternal.
3. Jalankan isi file:

```text
backend/src/db/schema.postgres.sql
```

Alternatif via lokal:

```bash
psql "RAILWAY_POSTGRES_DATABASE_URL" -f backend/src/db/schema.postgres.sql
```

## 7. Urutan Deploy yang Disarankan

1. Deploy Postgres dan Redis.
2. Jalankan schema PostgreSQL.
3. Deploy backend.
4. Ambil domain backend.
5. Set `SPAP_API_BASE_URL` di frontend.
6. Set `CORS_ORIGIN` di backend dengan domain frontend.
7. Deploy frontend dan redeploy backend.

## 8. Local vs Production

Local Docker:

```bash
docker compose up -d --build
```

URL lokal:

- Frontend: `http://localhost:8080`
- Backend: `http://localhost:3000`

Production Railway:

- Frontend: domain public service frontend.
- Backend: domain public service backend.
- Database dan Redis tidak perlu expose publik.

## Catatan Penting

- Jangan commit `.env` asli ke GitHub.
- Gunakan `.env.railway.frontend.example` dan `.env.railway.backend.example` sebagai template.
- Railway menyediakan `PORT`; Dockerfile sudah menyesuaikan Apache agar listen ke `${PORT:-80}`.
- Jika frontend gagal mengambil API, pastikan `SPAP_API_BASE_URL` benar dan `CORS_ORIGIN` backend sesuai domain frontend.
