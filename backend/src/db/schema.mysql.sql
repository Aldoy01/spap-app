CREATE TABLE IF NOT EXISTS users (
  id CHAR(36) PRIMARY KEY DEFAULT (UUID()),
  name VARCHAR(120) NOT NULL,
  email VARCHAR(160) UNIQUE NOT NULL,
  password_hash VARCHAR(255),
  role VARCHAR(40) NOT NULL DEFAULT 'operator',
  organization_unit VARCHAR(120),
  status VARCHAR(20) NOT NULL DEFAULT 'active',
  created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE IF NOT EXISTS tickets (
  id CHAR(36) PRIMARY KEY DEFAULT (UUID()),
  public_id VARCHAR(32) UNIQUE NOT NULL,
  type ENUM('aspirasi', 'pengaduan') NOT NULL,
  reporter_name VARCHAR(140) NOT NULL,
  reporter_contact VARCHAR(120),
  channel VARCHAR(40) NOT NULL,
  region VARCHAR(120) NOT NULL,
  category VARCHAR(80) NOT NULL,
  priority ENUM('Rendah', 'Sedang', 'Tinggi', 'Kritis') NOT NULL,
  status ENUM('Baru', 'Diproses', 'Eskalasi', 'Selesai') NOT NULL,
  subject VARCHAR(180) NOT NULL,
  description TEXT NOT NULL,
  assigned_unit VARCHAR(120),
  assigned_user_id CHAR(36),
  sla_due_at TIMESTAMP NULL,
  resolved_at TIMESTAMP NULL,
  created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  updated_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  CONSTRAINT tickets_assigned_user_fk FOREIGN KEY (assigned_user_id) REFERENCES users(id)
);

CREATE INDEX tickets_type_status_idx ON tickets(type, status);
CREATE INDEX tickets_region_category_idx ON tickets(region, category);
CREATE INDEX tickets_priority_idx ON tickets(priority);

CREATE TABLE IF NOT EXISTS ticket_events (
  id CHAR(36) PRIMARY KEY DEFAULT (UUID()),
  ticket_id CHAR(36) NOT NULL,
  event_type VARCHAR(60) NOT NULL,
  note TEXT,
  actor_name VARCHAR(120) NOT NULL DEFAULT 'Sistem',
  created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  CONSTRAINT ticket_events_ticket_fk FOREIGN KEY (ticket_id) REFERENCES tickets(id) ON DELETE CASCADE
);

CREATE INDEX ticket_events_ticket_idx ON ticket_events(ticket_id, created_at);

CREATE TABLE IF NOT EXISTS osint_mentions (
  id CHAR(36) PRIMARY KEY DEFAULT (UUID()),
  source VARCHAR(60) NOT NULL,
  keyword VARCHAR(120) NOT NULL,
  cluster_name VARCHAR(80),
  sentiment ENUM('Positif', 'Netral', 'Negatif') NOT NULL,
  mention_count INT NOT NULL DEFAULT 0,
  sample_text TEXT,
  recommendation TEXT,
  captured_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP
);

CREATE INDEX osint_mentions_keyword_idx ON osint_mentions(keyword, captured_at);

CREATE TABLE IF NOT EXISTS report_jobs (
  id CHAR(36) PRIMARY KEY DEFAULT (UUID()),
  report_type VARCHAR(80) NOT NULL,
  period VARCHAR(40) NOT NULL,
  region VARCHAR(120) NOT NULL,
  output_format VARCHAR(20) NOT NULL,
  status VARCHAR(20) NOT NULL DEFAULT 'queued',
  payload JSON NOT NULL,
  created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  completed_at TIMESTAMP NULL
);
