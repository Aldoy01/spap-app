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
4. Masyarakat memilih wilayah, struktur dewan, dapil, dan nama anggota/tujuan yang dituju.
5. Frontend mengirim data ke endpoint:

```text
POST /api/public/complaints
```

6. Backend membuat tiket `Pengaduan` dengan kanal `WhatsApp Link`.
7. Jika tujuan `Admin Wilayah`, tiket masuk ke `Admin Wilayah - [Wilayah] - [Nama Tujuan]`.
8. Jika tujuan `Admin Pusat`, tiket masuk ke `Admin Pusat SPAP - [Nama Tujuan]`.
9. Kategori dan prioritas tidak dipilih oleh masyarakat. Tiket masuk dengan kategori awal `Belum Diklasifikasi` dan prioritas awal `Sedang`, lalu diklasifikasi oleh admin/operator.

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
  "targetLevel": "DPR RI",
  "targetDapil": "Jawa Barat I",
  "targetName": "Nama Anggota PKS - PKS Jawa Barat I",
  "subject": "Keluhan layanan administrasi",
  "description": "Saya mengalami kendala pelayanan administrasi di kecamatan."
}
```
