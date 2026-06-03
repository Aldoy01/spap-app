# Otomatisasi Pengaduan WhatsApp SPAP

Fitur ini membuat pengaduan otomatis dari pesan WhatsApp yang masuk melalui webhook provider WhatsApp Business API.

## Endpoint Webhook

Gunakan endpoint backend:

```text
https://DOMAIN-BACKEND-KAMU.up.railway.app/api/integrations/whatsapp/webhook
```

Tambahkan variable Railway di service backend:

```env
WHATSAPP_WEBHOOK_TOKEN=isi-token-rahasia-kamu
```

Untuk Meta WhatsApp Cloud API, masukkan token yang sama sebagai `Verify Token`.

## Format Pesan Masyarakat

```text
[FORMAT PENGADUAN SPAP]
Nama: Budi Santoso
No. WhatsApp: 6281234567890
Wilayah: Jawa Barat
Tujuan: Admin Wilayah
Judul: Keluhan layanan administrasi
Kronologi: Saya mengalami kendala pelayanan administrasi di kecamatan.
```

Jika `Tujuan` berisi `Admin Pusat`, tiket masuk ke `Admin Pusat SPAP`.
Jika selain itu, tiket masuk ke `Admin Wilayah - [Wilayah]`.
Kategori dan prioritas tidak diisi oleh masyarakat. Admin pusat, wilayah, daerah, atau operator menentukan klasifikasi lanjutan di sistem.

## Hasil di Sistem

Pesan yang diterima webhook akan otomatis dibuat sebagai:

- tipe: `pengaduan`
- kanal: `WhatsApp`
- status: `Baru`
- kategori awal: `Belum Diklasifikasi`
- prioritas awal: `Sedang`
- PIC/target: `Admin Wilayah - [Wilayah]` atau `Admin Pusat SPAP`

## Tes Manual

Contoh tes memakai curl:

```bash
curl -X POST "https://DOMAIN-BACKEND-KAMU.up.railway.app/api/integrations/whatsapp/webhook?token=isi-token-rahasia-kamu" \
  -H "Content-Type: application/json" \
  -d "{\"from\":\"6281234567890\",\"name\":\"Budi Santoso\",\"body\":\"[FORMAT PENGADUAN SPAP]\nNama: Budi Santoso\nNo. WhatsApp: 6281234567890\nWilayah: Jawa Barat\nTujuan: Admin Wilayah\nJudul: Keluhan layanan administrasi\nKronologi: Saya mengalami kendala pelayanan administrasi di kecamatan.\"}"
```

Setelah sukses, buka menu **Pengaduan** di SPAP. Tiket baru akan muncul sebagai pengaduan WhatsApp.
