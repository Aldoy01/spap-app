CREATE EXTENSION IF NOT EXISTS pgcrypto;

CREATE TABLE IF NOT EXISTS users (
  id UUID PRIMARY KEY DEFAULT gen_random_uuid(),
  name VARCHAR(120) NOT NULL,
  email VARCHAR(160) UNIQUE NOT NULL,
  password_hash VARCHAR(255),
  role VARCHAR(40) NOT NULL DEFAULT 'operator',
  organization_unit VARCHAR(120),
  status VARCHAR(20) NOT NULL DEFAULT 'active',
  created_at TIMESTAMPTZ NOT NULL DEFAULT now()
);

ALTER TABLE users ADD COLUMN IF NOT EXISTS password_hash VARCHAR(255);
ALTER TABLE users ADD COLUMN IF NOT EXISTS status VARCHAR(20) NOT NULL DEFAULT 'active';

CREATE TABLE IF NOT EXISTS tickets (
  id UUID PRIMARY KEY DEFAULT gen_random_uuid(),
  public_id VARCHAR(32) UNIQUE NOT NULL,
  type VARCHAR(20) NOT NULL CHECK (type IN ('aspirasi', 'pengaduan')),
  reporter_name VARCHAR(140) NOT NULL,
  reporter_contact VARCHAR(120),
  channel VARCHAR(40) NOT NULL,
  region VARCHAR(120) NOT NULL,
  category VARCHAR(80) NOT NULL,
  priority VARCHAR(20) NOT NULL CHECK (priority IN ('Rendah', 'Sedang', 'Tinggi', 'Kritis')),
  status VARCHAR(20) NOT NULL CHECK (status IN ('Baru', 'Diproses', 'Eskalasi', 'Selesai')),
  subject VARCHAR(180) NOT NULL,
  description TEXT NOT NULL,
  assigned_unit VARCHAR(120),
  assigned_user_id UUID REFERENCES users(id),
  sla_due_at TIMESTAMPTZ,
  resolved_at TIMESTAMPTZ,
  created_at TIMESTAMPTZ NOT NULL DEFAULT now(),
  updated_at TIMESTAMPTZ NOT NULL DEFAULT now()
);

CREATE INDEX IF NOT EXISTS tickets_type_status_idx ON tickets(type, status);
CREATE INDEX IF NOT EXISTS tickets_region_category_idx ON tickets(region, category);
CREATE INDEX IF NOT EXISTS tickets_priority_idx ON tickets(priority);

CREATE TABLE IF NOT EXISTS ticket_events (
  id UUID PRIMARY KEY DEFAULT gen_random_uuid(),
  ticket_id UUID NOT NULL REFERENCES tickets(id) ON DELETE CASCADE,
  event_type VARCHAR(60) NOT NULL,
  note TEXT,
  actor_name VARCHAR(120) NOT NULL DEFAULT 'Sistem',
  created_at TIMESTAMPTZ NOT NULL DEFAULT now()
);

CREATE INDEX IF NOT EXISTS ticket_events_ticket_idx ON ticket_events(ticket_id, created_at DESC);

CREATE TABLE IF NOT EXISTS osint_mentions (
  id UUID PRIMARY KEY DEFAULT gen_random_uuid(),
  source VARCHAR(60) NOT NULL,
  keyword VARCHAR(120) NOT NULL,
  cluster_name VARCHAR(80),
  sentiment VARCHAR(20) NOT NULL CHECK (sentiment IN ('Positif', 'Netral', 'Negatif')),
  mention_count INTEGER NOT NULL DEFAULT 0,
  sample_text TEXT,
  recommendation TEXT,
  captured_at TIMESTAMPTZ NOT NULL DEFAULT now()
);

CREATE INDEX IF NOT EXISTS osint_mentions_keyword_idx ON osint_mentions(keyword, captured_at DESC);

CREATE TABLE IF NOT EXISTS report_jobs (
  id UUID PRIMARY KEY DEFAULT gen_random_uuid(),
  report_type VARCHAR(80) NOT NULL,
  period VARCHAR(40) NOT NULL,
  region VARCHAR(120) NOT NULL,
  output_format VARCHAR(20) NOT NULL,
  status VARCHAR(20) NOT NULL DEFAULT 'queued',
  payload JSONB NOT NULL DEFAULT '{}'::jsonb,
  created_at TIMESTAMPTZ NOT NULL DEFAULT now(),
  completed_at TIMESTAMPTZ
);

INSERT INTO users (name, email, role, organization_unit)
VALUES
  ('Admin SPAP', 'admin@spap.local', 'admin', 'DPP'),
  ('Operator SPAP', 'operator@spap.local', 'operator', 'Triage SPAP')
ON CONFLICT (email) DO NOTHING;

UPDATE users
SET password_hash = '$2y$10$XjdRzaG9nJAORl4ek5m3LuLXJpCaSW29f3niYRrSH2ObViR8rIqa2',
    status = 'active'
WHERE email = 'admin@spap.local';

UPDATE users
SET password_hash = '$2y$10$CynqGjenPLMJnhh33m.nLepJRQvpo/BuJZr60ZWo5dj5WERsIUXqe',
    status = 'active'
WHERE email = 'operator@spap.local';

INSERT INTO tickets (public_id, type, reporter_name, channel, region, category, priority, status, subject, description, assigned_unit, sla_due_at)
VALUES
  ('ASP-2026-001', 'aspirasi', 'Ahmad Rizki', 'WhatsApp', 'DKI Jakarta', 'Infrastruktur', 'Tinggi', 'Baru', 'Perbaikan jalan rusak di Cengkareng', 'Jalan utama rusak dan membahayakan pengendara saat jam padat.', 'DPC Jakarta Barat', now() + interval '2 days'),
  ('ASP-2026-002', 'aspirasi', 'Siti Nurhaliza', 'Form Web', 'Jawa Barat', 'Pendidikan', 'Sedang', 'Diproses', 'Kekurangan guru SD negeri', 'Orang tua meminta advokasi penambahan guru kelas dan fasilitas belajar.', 'DPD Bandung', now() + interval '4 days'),
  ('PEN-2026-001', 'pengaduan', 'Budi Santoso', 'Email', 'Jawa Barat', 'Hukum', 'Kritis', 'Baru', 'Dugaan penyimpangan proyek jembatan', 'Pelapor meminta investigasi awal dan perlindungan identitas.', 'Tim Advokasi Hukum', now() + interval '1 day')
ON CONFLICT (public_id) DO NOTHING;

INSERT INTO osint_mentions (source, keyword, cluster_name, sentiment, mention_count, sample_text, recommendation)
VALUES
  ('X/Twitter', '#HargaSembako', 'Ekonomi', 'Negatif', 15420, 'Harga bahan pokok naik, butuh kanal aspirasi yang cepat.', 'Susun respon kebijakan dan advokasi pasar murah.'),
  ('Facebook', '#PendidikanGratis', 'Pendidikan', 'Positif', 5430, 'Program bantuan pendidikan mendapat respon positif dari orang tua.', 'Amplifikasi program dan kumpulkan testimoni.'),
  ('Instagram', '#Infrastruktur', 'Infrastruktur', 'Netral', 6720, 'Warga menandai kondisi jalan rusak dan meminta advokasi.', 'Petakan wilayah keluhan untuk koordinasi DPC.')
ON CONFLICT DO NOTHING;
