# CAPTCHA Form Publik SPAP

Form publik `?aduan=wa` mendukung Cloudflare Turnstile untuk mencegah submit otomatis.

## Alur

1. User memilih `Pengaduan` atau `Aspirasi`.
2. User mengisi form.
3. User menyelesaikan CAPTCHA.
4. Frontend mengirim `captchaToken` ke backend.
5. Backend memverifikasi token ke Cloudflare Turnstile.
6. Jika valid, data disimpan.
7. Jika tidak valid, submit ditolak.

## Variable Railway

Tambahkan di service frontend:

```env
TURNSTILE_SITE_KEY=site-key-dari-cloudflare
```

Tambahkan di service backend:

```env
TURNSTILE_SECRET_KEY=secret-key-dari-cloudflare
```

Jika variable belum diisi, aplikasi tetap bisa dites lokal memakai checkbox fallback `Saya bukan robot`. Untuk produksi, isi kedua variable agar verifikasi token aktif.
