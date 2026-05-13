# Konsep Infrastruktur SPAP

## Tujuan

Infrastruktur SPAP dirancang untuk menerima aspirasi dan pengaduan dari kanal pasif, membaca sinyal isu dari kanal aktif OSINT, lalu mengubahnya menjadi tiket pelayanan yang bisa ditriage, dieskalasi, ditindaklanjuti, dan dilaporkan.

## Lapisan Sistem

### 1. Frontend

Frontend adalah antarmuka operator dan pimpinan. Modul utama:

- Dashboard operasional.
- Manajemen aspirasi.
- Manajemen pengaduan.
- OSINT monitoring.
- Proses bisnis dan matriks eskalasi.
- Laporan.

Dalam container, frontend dijalankan oleh Nginx. Nginx juga menjadi reverse proxy untuk request `/api` ke backend.

### 2. Backend API

Backend PHP 7 menjadi pusat aturan bisnis:

- Membuat tiket aspirasi/pengaduan.
- Filter dan pencarian tiket.
- Update status dan event tiket.
- Menyajikan data OSINT.
- Membuat ringkasan laporan.
- Menulis audit trail ke `ticket_events`.

Backend tidak menyimpan state penting di memori agar aman ketika diskalakan menjadi beberapa instance. Implementasi saat ini memakai PHP 7 Apache, PDO PostgreSQL, dan extension Redis.

### 3. PostgreSQL

PostgreSQL menjadi database utama. Tabel inti:

- `users`: operator, admin, struktur organisasi, dan PIC.
- `tickets`: tiket aspirasi dan pengaduan.
- `ticket_events`: riwayat status, eskalasi, catatan, dan bukti tindak lanjut.
- `osint_mentions`: hasil monitoring keyword, sentimen, cluster, dan rekomendasi.
- `report_jobs`: antrean pembuatan laporan.

### 4. Redis

Redis dipakai untuk data cepat dan sementara:

- Cache daftar tiket dan OSINT.
- Session/token store jika nanti login ditambahkan.
- Queue job ringan seperti generate laporan, notifikasi WhatsApp/email, dan sinkronisasi OSINT.
- Rate limit API publik.

### 5. MySQL Opsional

MySQL disediakan sebagai opsi service di Docker Compose, tetapi bukan engine default. Schema awal ada di `backend/src/db/schema.mysql.sql`. Jika ingin pindah ke MySQL, backend PHP perlu mengganti DSN PDO dari `pgsql` ke `mysql` dan menyesuaikan query yang memakai fitur PostgreSQL seperti `RETURNING`, `ILIKE`, dan `JSONB`.

## Alur Proses Bisnis

```text
Kanal Masuk
  -> Normalisasi Data
  -> Klasifikasi Kategori dan Prioritas
  -> Triage PIC dan SLA
  -> Tindak Lanjut / Advokasi
  -> Update Status dan Audit Trail
  -> Feedback ke Pelapor
  -> Laporan dan Rekomendasi Kebijakan
```

## Rekomendasi Deployment

### Development

- `docker compose up --build`
- Semua service berjalan dalam satu mesin.
- Port dibuka untuk debugging: frontend `8080`, backend `3000`, PostgreSQL `5432`, Redis `6379`.

### Staging

- Frontend dan backend dipisah container.
- PostgreSQL memakai volume persistent.
- Redis memakai AOF.
- Jalankan migrasi sebelum deploy backend.

### Production

- Frontend di CDN atau Nginx.
- Backend minimal 2 replica di belakang load balancer.
- PostgreSQL managed database dengan backup harian dan point-in-time recovery.
- Redis managed atau cluster dengan password dan network private.
- Observability: log terstruktur, metrics, alert SLA, dan audit trail.

## Keamanan

- Gunakan role-based access control.
- Simpan data pelapor sensitif secara terbatas.
- Audit semua perubahan status tiket.
- Terapkan rate limit untuk endpoint publik.
- Enkripsi koneksi database pada production.
- Gunakan secret manager untuk password database dan Redis.
