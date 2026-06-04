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

Jika variable belum diisi, aplikasi memakai test key resmi Cloudflare:

```env
TURNSTILE_SITE_KEY=1x00000000000000000000AA
TURNSTILE_SECRET_KEY=1x0000000000000000000000000000000AA
```

Test key ini membuat widget tetap tampil dan validasi selalu berhasil untuk pengujian. Untuk produksi, ganti kedua variable dengan key asli dari Cloudflare.
