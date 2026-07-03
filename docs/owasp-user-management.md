# User Management SPAP - Standar OWASP Top 10

Dokumen ini merangkum kontrol keamanan user management SPAP App yang mengikuti praktik OWASP Top 10.

## Kontrol yang diterapkan

- Password policy: minimal 10 karakter, huruf besar, huruf kecil, angka, dan simbol.
- Password umum seperti `admin123`, `user123`, `password`, dan `qwerty123` ditolak untuk akun baru dan reset password.
- Login rate limit: 5 kali gagal dalam 15 menit akan mengunci percobaan login sementara untuk kombinasi email dan IP.
- Error login dibuat umum agar tidak membocorkan apakah email terdaftar atau tidak.
- Role hanya menerima daftar resmi: `admin`, `operator`, `verifikator`, `koordinator`.
- Status hanya menerima daftar resmi: `active`, `inactive`, `suspended`.
- Admin tidak bisa menonaktifkan atau menurunkan role akun sendiri.
- Sistem mencegah penghapusan admin aktif terakhir.
- Aktivitas keamanan dicatat ke tabel `security_events`.
- Password seed bawaan tidak lagi menimpa password yang sudah ada.

## Audit keamanan

Event yang dicatat:

- `auth.login_success`
- `auth.login_failed`
- `auth.login_rate_limited`
- `auth.logout`
- `user.created_or_updated`
- `user.updated`
- `user.password_reset`

Endpoint audit admin:

```text
GET /api/admin/security-events
Authorization: Bearer <token-admin>
```

## Panduan operasional

- Jangan memakai password default untuk user produksi.
- Buat minimal dua akun admin aktif agar tidak terkunci saat satu akun bermasalah.
- Reset password user hanya lewat admin dan gunakan password sementara yang kuat.
- Setelah reset password, minta user mengganti password sesuai kebijakan internal organisasi.
- Review audit keamanan secara berkala, terutama event login gagal berulang.

## Catatan Railway

Pastikan service backend sudah menjalankan migration otomatis melalui `ensure_schema()`. Setelah deploy, endpoint `/health` harus mengembalikan status database `ok`.
