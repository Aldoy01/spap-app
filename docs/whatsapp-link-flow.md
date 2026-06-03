# Alur Link Pengaduan dari WhatsApp Business

Gunakan alur ini jika nomor WhatsApp bisnis hanya mengarahkan masyarakat ke link form pengaduan.

## Link Form Publik

Link yang dikirim ke masyarakat:

```text
https://DOMAIN-FRONTEND-KAMU.up.railway.app/?aduan=wa
```

Contoh template pesan WhatsApp:

```text
Terima kasih sudah menghubungi SPAP.
Silakan isi pengaduan melalui link berikut:
https://DOMAIN-FRONTEND-KAMU.up.railway.app/?aduan=wa

Setelah formulir dikirim, pengaduan akan tercatat otomatis dan diteruskan ke admin wilayah atau pusat.
```

## Cara Kerja

1. Masyarakat menghubungi nomor WhatsApp bisnis.
2. Admin/auto-reply WhatsApp mengirim link `?aduan=wa`.
3. Masyarakat membuka link dan mengisi form pengaduan tanpa login.
4. Frontend mengirim data ke endpoint:

```text
POST /api/public/complaints
```

5. Backend membuat tiket `Pengaduan` dengan kanal `WhatsApp Link`.
6. Jika tujuan `Admin Wilayah`, tiket masuk ke `Admin Wilayah - [Wilayah]`.
7. Jika tujuan `Admin Pusat`, tiket masuk ke `Admin Pusat SPAP`.

## Endpoint Backend

```text
https://DOMAIN-BACKEND-KAMU.up.railway.app/api/public/complaints
```

Payload contoh:

```json
{
  "reporterName": "Budi Santoso",
  "reporterContact": "081234567890",
  "region": "Jawa Barat",
  "targetScope": "wilayah",
  "category": "Pelayanan Publik",
  "priority": "Sedang",
  "subject": "Keluhan layanan administrasi",
  "description": "Saya mengalami kendala pelayanan administrasi di kecamatan."
}
```
