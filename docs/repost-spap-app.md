# Materi Repost / Publikasi SPAP App

## Versi Singkat

SPAP App adalah dashboard Sistem Pelayanan dan Advokasi Publik untuk mengelola aspirasi masyarakat, pengaduan, OSINT monitoring, laporan, dan tindak lanjut pelayanan berbasis role admin/user.

Stack: PHP 7, PostgreSQL, Redis, Docker, Railway, dan GitHub.

## Caption Publikasi

SPAP App - Sistem Pelayanan dan Advokasi Publik

Saya membuat prototype aplikasi SPAP untuk membantu pengelolaan aspirasi masyarakat dan pengaduan publik secara lebih terstruktur.

Fitur utama:
- Dashboard operasional aspirasi dan pengaduan
- Manajemen aspirasi masyarakat
- Manajemen pengaduan publik
- OSINT monitoring
- Grafik tren dan kategori aspirasi
- Distribusi wilayah/provinsi Indonesia
- Login admin dan user/operator
- Backend API PHP 7
- PostgreSQL untuk database utama
- Redis untuk session dan cache
- Siap deploy ke Railway via GitHub

Tech stack:
PHP 7, Apache, JavaScript, CSS, PostgreSQL, Redis, Docker Compose, Railway.

Demo lokal:
Frontend: http://localhost:8080
Backend: http://localhost:3000

Akun demo:
Admin: admin@spap.local / admin123
User: operator@spap.local / user123

## Deskripsi GitHub Repository

SPAP App adalah prototype Sistem Pelayanan dan Advokasi Publik berbasis PHP 7, PostgreSQL, Redis, dan Docker. Aplikasi ini menyediakan dashboard aspirasi, pengaduan, OSINT monitoring, laporan, login role admin/user, serta panduan deploy ke Railway.

## Short Repository About

Prototype dashboard SPAP untuk aspirasi, pengaduan, OSINT, laporan, dan login admin/user berbasis PHP 7, PostgreSQL, Redis, Docker, dan Railway.

## Tags / Topics GitHub

```text
php
php7
dashboard
public-service
postgresql
redis
docker
railway
osint
aspirasi
pengaduan
indonesia
```

## Release Notes

### SPAP App v0.1.0

Initial prototype:
- Frontend PHP 7 dengan UI dashboard modern
- Backend API PHP 7
- Database PostgreSQL
- Redis session dan cache
- Login admin/user
- Dashboard KPI, trend, kategori aspirasi, distribusi geografis, aktivitas, dan alert
- Modul aspirasi, pengaduan, OSINT, analytics, laporan, dan pengaturan
- Docker Compose local development
- Panduan deploy GitHub dan Railway

## Checklist Sebelum Diposting

- Pastikan `.env` tidak ikut commit.
- Jalankan `docker compose up -d --build`.
- Cek frontend `http://localhost:8080`.
- Cek backend `http://localhost:3000/health`.
- Screenshot dashboard, login, aspirasi, dan kategori aspirasi.
- Push ke GitHub.
- Deploy ke Railway.

## Command GitHub

```bash
git init
git add .
git commit -m "Initial SPAP app prototype"
git branch -M main
git remote add origin https://github.com/USERNAME/spap-app.git
git push -u origin main
```

## Command Update Setelah Ada Perubahan

```bash
git status
git add .
git commit -m "Update SPAP app"
git push
```
