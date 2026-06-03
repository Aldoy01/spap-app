<?php
header('Cache-Control: no-store, no-cache, must-revalidate, max-age=0');
header('Pragma: no-cache');
header('Expires: 0');

$appTitle = 'SPAP App - Sistem Pelayanan dan Advokasi Publik';
$brandName = 'SPAP PKS';
$brandSubtitle = 'Sistem Pelayanan & Advokasi Publik';
$apiBaseUrl = getenv('SPAP_API_BASE_URL') ?: 'http://localhost:3000';
$assetVersion = getenv('RAILWAY_GIT_COMMIT_SHA') ?: (string) time();
$menuItems = [
  ['page' => 'dashboard', 'icon' => 'dashboard', 'label' => 'Dashboard', 'active' => true],
  ['page' => 'aspirasi', 'icon' => 'aspirasi', 'label' => 'Aspirasi Masyarakat'],
  ['page' => 'pengaduan', 'icon' => 'pengaduan', 'label' => 'Pengaduan'],
  ['page' => 'osint', 'icon' => 'osint', 'label' => 'OSINT Monitoring'],
  ['page' => 'analytics', 'icon' => 'analytics', 'label' => 'Analytics'],
  ['page' => 'laporan', 'icon' => 'laporan', 'label' => 'Laporan'],
  ['page' => 'infra', 'icon' => 'infra', 'label' => 'Konsep Infra', 'hidden' => true],
  ['page' => 'workflow', 'icon' => 'workflow', 'label' => 'Proses Bisnis', 'hidden' => true],
  ['page' => 'settings', 'icon' => 'settings', 'label' => 'Pengaturan'],
];

function pks_logo(): string
{
  return '<div class="pks-logo" aria-label="Logo PKS"><img src="assets/logo-pks.svg" alt="Logo PKS"><strong>PKS</strong></div>';
}

function nav_icon(string $name): string
{
  $icons = [
    'dashboard' => '<svg viewBox="0 0 24 24"><path d="M4 13h6V4H4v9Zm10 7h6V4h-6v16ZM4 20h6v-4H4v4Z"/></svg>',
    'aspirasi' => '<svg viewBox="0 0 24 24"><path d="M4 6.5A3.5 3.5 0 0 1 7.5 3h9A3.5 3.5 0 0 1 20 6.5v5A3.5 3.5 0 0 1 16.5 15H11l-5 4v-4.2A3.5 3.5 0 0 1 4 11.5v-5Z"/></svg>',
    'pengaduan' => '<svg viewBox="0 0 24 24"><path d="M12 3 3 20h18L12 3Zm0 5v6m0 3h.01"/></svg>',
    'osint' => '<svg viewBox="0 0 24 24"><path d="M12 20a8 8 0 1 0 0-16 8 8 0 0 0 0 16Zm0-12v4l3 2M4 12h3m10 0h3m-8-8v3m0 10v3"/></svg>',
    'analytics' => '<svg viewBox="0 0 24 24"><path d="M4 19V5m0 14h16M7 16l3-4 3 2 5-7"/></svg>',
    'laporan' => '<svg viewBox="0 0 24 24"><path d="M7 3h7l4 4v14H7V3Zm7 0v5h5M9 13h6M9 17h6M9 9h2"/></svg>',
    'infra' => '<svg viewBox="0 0 24 24"><path d="M5 5h14v5H5V5Zm0 9h14v5H5v-5Zm3-6h.01M8 17h.01M12 10v4"/></svg>',
    'workflow' => '<svg viewBox="0 0 24 24"><path d="M6 6h5v5H6V6Zm7 7h5v5h-5v-5ZM8.5 11v2A2.5 2.5 0 0 0 11 15.5h2"/></svg>',
    'settings' => '<svg viewBox="0 0 24 24"><path d="M12 15.5A3.5 3.5 0 1 0 12 8a3.5 3.5 0 0 0 0 7.5Zm7-3.5 2-1.4-2-3.5-2.4 1a7.8 7.8 0 0 0-1.5-.9L14.8 4h-4l-.3 3.2c-.5.2-1 .5-1.5.9l-2.4-1-2 3.5 2 1.4a8 8 0 0 0 0 1.8l-2 1.4 2 3.5 2.4-1c.5.4 1 .7 1.5.9l.3 3.2h4l.3-3.2c.5-.2 1-.5 1.5-.9l2.4 1 2-3.5-2-1.4a8 8 0 0 0 0-1.8Z"/></svg>',
  ];

  return $icons[$name] ?? $icons['dashboard'];
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title><?= htmlspecialchars($appTitle, ENT_QUOTES, 'UTF-8') ?></title>
  <link rel="stylesheet" href="assets/app.css?v=<?= htmlspecialchars($assetVersion, ENT_QUOTES, 'UTF-8') ?>">
  <script>
    window.SPAP_CONFIG = {
      apiBaseUrl: <?= json_encode($apiBaseUrl, JSON_UNESCAPED_SLASHES) ?>
    };
  </script>
</head>
<body>
  <section class="login-screen" id="loginScreen">
    <div class="login-card">
      <?= pks_logo() ?>
      <p class="eyebrow">Login SPAP App</p>
      <h1>Masuk ke Sistem</h1>
      <p class="login-copy">Gunakan akun admin atau user untuk mengakses dashboard pelayanan dan advokasi publik.</p>
      <form id="loginForm" class="login-form">
        <label>Email
          <input id="loginEmail" type="email" value="admin@spap.local" required>
        </label>
        <label>Password
          <input id="loginPassword" type="password" value="admin123" required>
        </label>
        <button class="btn primary" type="submit">Masuk</button>
      </form>
      <div class="login-hint">
        <span>Admin: admin@spap.local / admin123</span>
        <span>User: operator@spap.local / user123</span>
      </div>
    </div>
  </section>

  <div class="app-shell app-locked" id="appShell">
    <aside class="sidebar" aria-label="Navigasi utama">
      <div class="brand">
        <?= pks_logo() ?>
        <div>
          <h1><?= htmlspecialchars($brandName, ENT_QUOTES, 'UTF-8') ?></h1>
          <p><?= htmlspecialchars($brandSubtitle, ENT_QUOTES, 'UTF-8') ?></p>
        </div>
      </div>
      <nav class="nav-list">
        <?php foreach ($menuItems as $item): ?>
          <?php
            $classes = 'nav-item';
            if (!empty($item['active'])) $classes .= ' active';
            if (!empty($item['hidden'])) $classes .= ' nav-hidden';
          ?>
          <button class="<?= htmlspecialchars($classes, ENT_QUOTES, 'UTF-8') ?>" data-page="<?= htmlspecialchars($item['page'], ENT_QUOTES, 'UTF-8') ?>" title="<?= htmlspecialchars($item['label'], ENT_QUOTES, 'UTF-8') ?>">
            <span class="nav-icon" aria-hidden="true"><?= nav_icon($item['icon']) ?></span>
            <span><?= htmlspecialchars($item['label'], ENT_QUOTES, 'UTF-8') ?></span>
          </button>
        <?php endforeach; ?>
      </nav>
      <div class="sidebar-footer">
        <span class="status-dot"></span>
        <span id="connectionStatus">Menghubungkan API</span>
      </div>
      <div class="user-panel">
        <strong id="currentUserName">-</strong>
        <span id="currentUserRole">-</span>
        <button class="btn ghost" id="logoutBtn" type="button">Logout</button>
      </div>
    </aside>

    <main class="main">
      <header class="topbar">
        <div>
          <p class="eyebrow">Sistem pelayanan terpadu</p>
          <h2 id="pageTitle">Dashboard Operasional</h2>
          <p class="topbar-subtitle">Pantau aspirasi, pengaduan, OSINT, dan tindak lanjut pelayanan publik dalam satu ruang kerja.</p>
        </div>
        <div class="top-actions">
          <select id="regionFilter" aria-label="Filter wilayah">
            <option value="Semua">Semua Wilayah</option>
          </select>
          <button class="notification-btn" id="notificationBtn" type="button" aria-label="Buka notifikasi">
            <span>Notifikasi</span>
            <strong id="notificationCount">0</strong>
          </button>
          <button class="btn primary" id="newTicketBtn">Tambah Aspirasi</button>
        </div>
      </header>

      <section class="page active" id="dashboard">
        <section class="dashboard-filterbar">
          <select id="periodFilter" aria-label="Filter periode">
            <option>Hari Ini</option>
            <option>7 Hari Terakhir</option>
            <option>Bulan Ini</option>
            <option>Triwulan Ini</option>
          </select>
          <select id="dashboardCategoryFilter" aria-label="Filter kategori">
            <option>Semua Kategori</option>
            <option>Infrastruktur</option>
            <option>Pendidikan</option>
            <option>Kesehatan</option>
            <option>Ekonomi</option>
            <option>Pelayanan Publik</option>
            <option>Hukum</option>
          </select>
          <button class="btn primary" id="refreshDashboardBtn">Refresh Data</button>
        </section>
        <div class="metric-grid" id="metricGrid"></div>
        <div class="dashboard-grid dashboard-rich">
          <section class="panel span-3">
            <div class="panel-head">
              <div>
                <h3>Trend Aspirasi & Pengaduan</h3>
                <p>Pergerakan volume layanan dalam 5 hari operasional terakhir</p>
              </div>
              <div class="chart-legend">
                <span><i class="legend-aspirasi"></i>Aspirasi</span>
                <span><i class="legend-pengaduan"></i>Pengaduan</span>
              </div>
            </div>
            <div id="trendChart" class="trend-chart"></div>
          </section>
          <section class="panel span-3">
            <div class="panel-head">
              <div>
                <h3>Distribusi Geografis Aspirasi</h3>
                <p>Provinsi dengan volume aspirasi/pengaduan tertinggi</p>
              </div>
            </div>
            <div id="geoDistribution" class="geo-strip"></div>
          </section>
          <section class="panel span-3 category-panel">
            <div class="panel-head">
              <div>
                <h3>Kategori Aspirasi</h3>
                <p>Komposisi aspirasi berdasarkan isu utama masyarakat</p>
              </div>
            </div>
            <div class="category-chart-layout">
              <div class="category-copy">
                <strong>Prioritas tema layanan</strong>
                <p>Gunakan komposisi kategori untuk menentukan fokus advokasi dan alokasi PIC wilayah.</p>
              </div>
              <div id="categoryDonut" class="category-donut"></div>
            </div>
          </section>
          <section class="panel span-3">
            <div class="panel-head">
              <div>
                <h3>Aktivitas Terbaru</h3>
                <p>Jejak layanan terbaru dari kanal pasif dan aktif</p>
              </div>
            </div>
            <div class="activity-timeline" id="dashboardActivityList"></div>
          </section>
          <section class="panel span-3">
            <div class="panel-head">
              <div>
                <h3>Alert & Notifikasi</h3>
                <p>Isu prioritas yang perlu perhatian operator</p>
              </div>
            </div>
            <div id="dashboardAlerts" class="dashboard-alerts"></div>
          </section>
          <section class="panel span-3">
            <div class="panel-head">
              <div>
                <h3>SLA & Eskalasi Otomatis</h3>
                <p>Tiket yang melewati batas waktu, kritis, atau menunggu tindakan</p>
              </div>
            </div>
            <div id="slaNotificationList" class="sla-list"></div>
          </section>
          <section class="panel span-2">
            <div class="panel-head">
              <div>
                <h3>Pipeline Pelayanan</h3>
                <p>Ringkasan status aspirasi dan pengaduan</p>
              </div>
              <button class="btn ghost" id="refreshBtn">Refresh</button>
            </div>
            <div class="pipeline" id="pipeline"></div>
          </section>
          <section class="panel">
            <div class="panel-head">
              <div>
                <h3>Early Warning</h3>
                <p>Prioritas tindak lanjut hari ini</p>
              </div>
            </div>
            <div id="warningList" class="warning-list"></div>
          </section>
          <section class="panel">
            <div class="panel-head">
              <div>
                <h3>Sebaran Isu</h3>
                <p>Volume berdasarkan kategori</p>
              </div>
            </div>
            <div id="categoryBars" class="bar-list"></div>
          </section>
          <section class="panel span-2">
            <div class="panel-head">
              <div>
                <h3>Aktivitas Terakhir</h3>
                <p>Jejak layanan dari kanal pasif dan aktif</p>
              </div>
            </div>
            <div class="activity-list" id="activityList"></div>
          </section>
        </div>
      </section>

      <section class="page" id="aspirasi">
        <div class="toolbar">
          <input id="aspirasiSearch" type="search" placeholder="Cari aspirasi, nama, wilayah...">
          <select id="aspirasiStatus">
            <option value="Semua">Semua Status</option>
            <option value="Baru">Baru</option>
            <option value="Diproses">Diproses</option>
            <option value="Eskalasi">Eskalasi</option>
            <option value="Selesai">Selesai</option>
          </select>
          <select id="aspirasiPriority">
            <option value="Semua">Semua Prioritas</option>
            <option value="Kritis">Kritis</option>
            <option value="Tinggi">Tinggi</option>
            <option value="Sedang">Sedang</option>
            <option value="Rendah">Rendah</option>
          </select>
        </div>
        <div class="table-wrap">
          <table>
            <thead>
              <tr>
                <th>ID</th>
                <th>Pelapor</th>
                <th>Judul</th>
                <th>Kategori</th>
                <th>Wilayah</th>
                <th>Prioritas</th>
                <th>Status</th>
                <th>Aksi</th>
              </tr>
            </thead>
            <tbody id="aspirasiTable"></tbody>
          </table>
        </div>
      </section>

      <section class="page" id="pengaduan">
        <section class="panel whatsapp-intake-panel">
          <div class="panel-head">
            <div>
              <h3>Pencatatan WhatsApp</h3>
              <p>Catat pengaduan masyarakat dari chat WhatsApp langsung menjadi tiket SPAP</p>
            </div>
            <button class="btn ghost" id="openWhatsappBtn" type="button">Buka WhatsApp</button>
          </div>
          <form id="whatsappComplaintForm" class="whatsapp-intake-form">
            <label>Nama Pelapor
              <input id="waReporterName" placeholder="Nama masyarakat" required>
            </label>
            <label>No. WhatsApp
              <input id="waReporterPhone" type="tel" placeholder="08xxxxxxxxxx" required>
            </label>
            <label>Wilayah
              <select id="waRegion" required>
                <option value="">Pilih Wilayah</option>
              </select>
            </label>
            <label>Kategori
              <select id="waCategory" required>
                <option value="">Pilih Kategori</option>
                <option>Infrastruktur</option>
                <option>Pendidikan</option>
                <option>Kesehatan</option>
                <option>Ekonomi</option>
                <option>Sosial</option>
                <option>Pelayanan Publik</option>
                <option>Hukum</option>
              </select>
            </label>
            <label>Prioritas
              <select id="waPriority" required>
                <option>Sedang</option>
                <option>Rendah</option>
                <option>Tinggi</option>
                <option>Kritis</option>
              </select>
            </label>
            <label class="span-2">Judul Pengaduan
              <input id="waSubject" placeholder="Ringkasan singkat pengaduan" required>
            </label>
            <label class="span-3">Isi Pesan WhatsApp
              <textarea id="waMessage" rows="4" placeholder="Tempel isi chat atau kronologi dari WhatsApp..." required></textarea>
            </label>
            <div class="whatsapp-actions span-3">
              <button class="btn primary" type="submit">Catat sebagai Pengaduan</button>
              <small>Nomor WhatsApp disimpan sebagai kontak pelapor dan kanal tiket otomatis menjadi WhatsApp.</small>
            </div>
          </form>
        </section>
        <div class="toolbar">
          <input id="pengaduanSearch" type="search" placeholder="Cari pengaduan, lokasi, pelapor...">
          <select id="pengaduanStatus">
            <option value="Semua">Semua Status</option>
            <option value="Baru">Baru</option>
            <option value="Diproses">Diproses</option>
            <option value="Eskalasi">Eskalasi</option>
            <option value="Selesai">Selesai</option>
          </select>
          <select id="pengaduanPriority">
            <option value="Semua">Semua Prioritas</option>
            <option value="Kritis">Kritis</option>
            <option value="Tinggi">Tinggi</option>
            <option value="Sedang">Sedang</option>
            <option value="Rendah">Rendah</option>
          </select>
        </div>
        <div class="ticket-grid" id="pengaduanGrid"></div>
      </section>

      <section class="page" id="osint">
        <div class="dashboard-grid">
          <section class="panel span-2">
            <div class="panel-head">
              <div>
                <h3>Monitoring Ruang Publik</h3>
                <p>Kanal aktif untuk sinyal isu, sentimen, dan rekomendasi respon</p>
              </div>
              <button class="btn ghost" id="simulateOsintBtn">Simulasi Update</button>
            </div>
            <div class="topic-grid" id="topicGrid"></div>
          </section>
          <section class="panel">
            <div class="panel-head">
              <div>
                <h3>Alert OSINT</h3>
                <p>Sinyal yang perlu ditindaklanjuti</p>
              </div>
            </div>
            <div class="warning-list" id="osintAlerts"></div>
          </section>
          <section class="panel span-3">
            <div class="panel-head">
              <div>
                <h3>Feed Terklaster</h3>
                <p>Contoh data hasil clustering dan qualification</p>
              </div>
            </div>
            <div class="feed-list" id="feedList"></div>
          </section>
        </div>
      </section>

      <section class="page" id="workflow">
        <div class="process-hero">
          <div>
            <p class="eyebrow">Konsep bisnis SPAP</p>
            <h3>Dual-kanal: masyarakat mengirim aspirasi, sistem aktif membaca ruang publik.</h3>
            <p>Alur ini diringkas dari konsep mockup: data masuk, dinormalisasi, diklasifikasi, diprioritaskan, ditugaskan ke struktur terkait, lalu ditutup dengan bukti tindak lanjut.</p>
          </div>
          <div class="service-map" aria-label="Peta layanan konseptual">
            <span>DPP</span><span>DPW</span><span>DPD</span><span>DPC</span><span>Legislator</span><span>Masyarakat</span>
          </div>
        </div>
        <div class="workflow-grid" id="workflowGrid"></div>
        <section class="panel">
          <div class="panel-head">
            <div>
              <h3>Matriks Eskalasi</h3>
              <p>Penentuan pemilik kerja berdasarkan dampak dan wilayah</p>
            </div>
          </div>
          <div class="table-wrap compact">
            <table>
              <thead>
                <tr><th>Level</th><th>Kriteria</th><th>PIC</th><th>SLA</th></tr>
              </thead>
              <tbody id="escalationTable"></tbody>
            </table>
          </div>
        </section>
      </section>

      <section class="page" id="analytics">
        <div class="metric-grid" id="analyticsMetricGrid">
          <article class="metric"><span>Sentimen Positif</span><strong>64%</strong><em>+8% dari pekan lalu</em></article>
          <article class="metric"><span>Rata-rata Respon</span><strong>4.2j</strong><em>Target SLA 6 jam</em></article>
          <article class="metric"><span>Isu Dominan</span><strong>4</strong><em>Ekonomi, infrastruktur, kesehatan, pendidikan</em></article>
          <article class="metric"><span>Kualitas Data</span><strong>91%</strong><em>Data siap analitik</em></article>
        </div>
        <div class="dashboard-grid">
          <section class="panel span-2">
            <div class="panel-head">
              <div>
                <h3>Trend Aspirasi dan Pengaduan</h3>
                <p>Model analitik mockup untuk memantau volume layanan</p>
              </div>
            </div>
            <div class="mock-chart">
              <span style="height:42%"></span><span style="height:66%"></span><span style="height:51%"></span><span style="height:78%"></span><span style="height:62%"></span><span style="height:88%"></span><span style="height:72%"></span>
            </div>
          </section>
          <section class="panel">
            <div class="panel-head">
              <div>
                <h3>Insight Utama</h3>
                <p>Ringkasan cepat untuk operator</p>
              </div>
            </div>
            <div class="warning-list">
              <div class="warning positif"><strong>Respons cepat meningkat</strong><p>Mayoritas tiket baru masuk fase diproses di bawah SLA.</p></div>
              <div class="warning"><strong>Perlu perhatian wilayah</strong><p>Isu infrastruktur dan kesehatan paling sering dieskalasi.</p></div>
              <div class="warning negatif"><strong>Sentimen ekonomi negatif</strong><p>OSINT merekam kenaikan percakapan harga sembako.</p></div>
            </div>
          </section>
        </div>
      </section>

      <section class="page" id="infra">
        <div class="process-hero">
          <div>
            <p class="eyebrow">Konsep Infrastruktur</p>
            <h3>Frontend Nginx, backend PHP 7, PostgreSQL, Redis, dan opsi MySQL.</h3>
            <p>Halaman ini merangkum konsep infrastruktur SPAP agar bisa dibaca langsung dari aplikasi, sesuai bahan arsitektur di dokumentasi.</p>
          </div>
          <div class="service-map infra-map" aria-label="Komponen infrastruktur SPAP">
            <span>Frontend</span><span>PHP 7 API</span><span>PostgreSQL</span><span>Redis</span><span>MySQL Opsional</span><span>Docker</span>
          </div>
        </div>
        <div class="workflow-grid">
          <article class="workflow-card"><span>01</span><h3>Frontend</h3><p>Nginx menyajikan UI SPAP dan meneruskan request <strong>/api</strong> ke backend.</p></article>
          <article class="workflow-card"><span>02</span><h3>Backend PHP 7</h3><p>Apache + PHP 7.4 menjalankan API tiket, OSINT, laporan, dan health check.</p></article>
          <article class="workflow-card"><span>03</span><h3>PostgreSQL</h3><p>Database utama untuk tiket, event, user, report job, dan data OSINT.</p></article>
          <article class="workflow-card"><span>04</span><h3>Redis</h3><p>Cache query tiket/OSINT dan fondasi antrean notifikasi atau job laporan.</p></article>
          <article class="workflow-card"><span>05</span><h3>MySQL Opsional</h3><p>Disediakan schema alternatif jika deployment wajib memakai ekosistem MySQL.</p></article>
          <article class="workflow-card"><span>06</span><h3>Docker Compose</h3><p>Menyatukan frontend, backend, database, dan cache dalam satu stack development.</p></article>
        </div>
        <section class="panel infra-flow">
          <div class="panel-head">
            <div>
              <h3>Alur Infrastruktur</h3>
              <p>Urutan komunikasi antar komponen saat operator membuka dashboard</p>
            </div>
          </div>
          <div class="infra-steps">
            <span>Browser</span><span>Frontend Nginx</span><span>PHP 7 API</span><span>Redis Cache</span><span>PostgreSQL</span><span>Response JSON</span>
          </div>
        </section>
      </section>

      <section class="page" id="laporan">
        <div class="report-layout">
          <section class="panel">
            <div class="panel-head">
              <div>
                <h3>Buat Laporan</h3>
                <p>Template laporan berbasis data mockup</p>
              </div>
            </div>
            <form id="reportForm" class="form-grid">
              <label>Jenis Laporan
                <select id="reportType">
                  <option>Ringkasan Eksekutif</option>
                  <option>Aspirasi Masyarakat</option>
                  <option>Pengaduan Publik</option>
                  <option>OSINT & Sentimen</option>
                </select>
              </label>
              <label>Periode
                <select id="reportPeriod">
                  <option>Harian</option>
                  <option>Mingguan</option>
                  <option>Bulanan</option>
                  <option>Triwulan</option>
                </select>
              </label>
              <label>Wilayah
          <select id="reportRegion">
            <option>Nasional</option>
          </select>
              </label>
              <label>Format
                <select id="reportFormat">
                  <option>PDF</option>
                  <option>Excel</option>
                  <option>PowerPoint</option>
                </select>
              </label>
              <button class="btn primary" type="submit">Generate Preview</button>
            </form>
          </section>
          <section class="panel span-2">
            <div class="panel-head">
              <div>
                <h3>Preview Laporan</h3>
                <p>Ringkasan otomatis dari kondisi terkini</p>
              </div>
            </div>
            <div id="reportPreview" class="report-preview"></div>
          </section>
        </div>
      </section>

      <section class="page" id="settings">
        <div class="dashboard-grid">
          <section class="panel">
            <div class="panel-head">
              <div>
                <h3>Status Sistem</h3>
                <p>Kondisi layanan dan database Railway</p>
              </div>
            </div>
            <div class="warning-list">
              <div class="warning positif"><strong>Backend PHP 7 aktif</strong><p>Endpoint health tersedia di port 3000.</p></div>
              <div class="warning positif"><strong>PostgreSQL sehat</strong><p>Data tiket dan OSINT tersimpan di database utama.</p></div>
              <div class="warning positif"><strong>Session fallback aktif</strong><p>Redis opsional; session bisa memakai PostgreSQL.</p></div>
            </div>
          </section>
          <section class="panel span-2">
            <div class="panel-head">
              <div>
                <h3>Tambah User</h3>
                <p>Admin dapat membuat akun operator, verifikator, koordinator, atau admin</p>
              </div>
            </div>
            <form id="userForm" class="form-grid">
              <label>Nama<input id="userName" required></label>
              <label>Email<input id="userEmail" type="email" required></label>
              <label class="full user-target-field">Nama Tujuan / Anggota yang Diakses
                <input id="userTargetName" list="userTargetNameOptions" placeholder="Pilih atau ketik nama anggota yang dituju">
                <datalist id="userTargetNameOptions"></datalist>
                <small class="field-hint">Kosongkan untuk akses semua data sesuai role. Isi nama anggota agar user hanya melihat aspirasi/pengaduan yang ditujukan ke nama tersebut.</small>
              </label>
              <label>Role
                <select id="userRole">
                  <option value="operator">Operator</option>
                  <option value="verifikator">Verifikator</option>
                  <option value="koordinator">Koordinator</option>
                  <option value="admin">Admin</option>
                </select>
              </label>
              <label>Unit/Struktur<input id="userUnit" value="Unit SPAP"></label>
              <label>Password awal<input id="userPassword" value="user123"></label>
              <label>Status
                <select id="userStatus">
                  <option value="active">Aktif</option>
                  <option value="inactive">Nonaktif</option>
                </select>
              </label>
              <button class="btn primary" type="submit">Simpan User</button>
            </form>
          </section>
          <section class="panel span-3">
            <div class="panel-head">
              <div>
                <h3>Manajemen User</h3>
                <p>Daftar akun dan role operasional SPAP</p>
              </div>
            </div>
            <div class="table-wrap">
              <table>
                <thead><tr><th>Nama</th><th>Email</th><th>Role</th><th>Unit</th><th>Nama Tujuan</th><th>Status</th></tr></thead>
                <tbody id="userManagementRows"></tbody>
              </table>
            </div>
          </section>
          <section class="panel span-3">
            <div class="panel-head">
              <div>
                <h3>Manajemen Menu & Hak Akses</h3>
                <p>Atur menu yang bisa dilihat dan dikelola setiap role</p>
              </div>
              <button class="btn primary" id="savePermissionBtn" type="button">Simpan Akses</button>
            </div>
            <div class="table-wrap">
              <table>
                <thead><tr><th>Role</th><th>Menu</th><th>Lihat</th><th>Tambah</th><th>Ubah</th><th>Hapus</th></tr></thead>
                <tbody id="permissionRows"></tbody>
              </table>
            </div>
          </section>
          <section class="panel span-3">
            <div class="panel-head">
              <div>
                <h3>Pengelolaan Pengaduan</h3>
                <p>Disposisi, proses, eskalasi, dan penyelesaian pengaduan publik</p>
              </div>
            </div>
            <div id="complaintManagement" class="management-list"></div>
          </section>
        </div>
      </section>
    </main>
  </div>

  <dialog id="ticketDialog">
    <form id="ticketForm" class="modal">
      <div class="modal-head">
        <h3 id="dialogTitle"><span class="modal-title-icon">+</span> Tambah Aspirasi Baru</h3>
        <button class="icon-btn close-btn" id="closeTicketBtn" type="button" aria-label="Tutup"></button>
      </div>
      <div class="form-grid">
        <label class="full hidden-field">Tipe
          <select id="ticketType">
            <option value="aspirasi">Aspirasi</option>
            <option value="pengaduan">Pengaduan</option>
          </select>
        </label>
        <label class="full">Nama Pengirim:
          <input id="ticketName" required>
        </label>
        <label class="full">Email:
          <input id="ticketEmail" type="email">
        </label>
        <label class="full">No. Telepon:
          <input id="ticketPhone" type="tel">
        </label>
        <label class="full">Wilayah:
          <select id="ticketRegion">
            <option value="">Pilih Wilayah</option>
          </select>
        </label>
        <label>Ditujukan Kepada:
          <select id="ticketTargetLevel">
            <option value="DPR RI">DPR RI</option>
            <option value="DPRD Provinsi">DPRD Provinsi</option>
            <option value="DPRD Kab/Kota">DPRD Kab/Kota</option>
          </select>
        </label>
        <label>Provinsi Tujuan:
          <input id="ticketTargetProvince" readonly>
        </label>
        <label>Dapil:
          <select id="ticketTargetDapil">
            <option value="">Pilih Dapil</option>
          </select>
        </label>
        <label>Kota/Kabupaten:
          <select id="ticketTargetCity">
            <option value="">Pilih Kota/Kabupaten</option>
          </select>
        </label>
        <label class="full">Nama Tujuan:
          <select id="ticketTargetName">
            <option value="">Pilih Nama Tujuan</option>
          </select>
        </label>
        <label class="full">Kategori:
          <select id="ticketCategory">
            <option value="">Pilih Kategori</option>
            <option>Infrastruktur</option>
            <option>Pendidikan</option>
            <option>Kesehatan</option>
            <option>Ekonomi</option>
            <option>Sosial</option>
          </select>
        </label>
        <label class="full">Prioritas:
          <select id="ticketPriority">
            <option value="">Pilih Prioritas</option>
            <option>Rendah</option>
            <option>Sedang</option>
            <option>Tinggi</option>
            <option>Kritis</option>
          </select>
        </label>
        <label class="full">Judul Aspirasi:
          <input id="ticketSubject" required>
        </label>
        <label class="full">Deskripsi Aspirasi:
          <textarea id="ticketDescription" rows="4" required></textarea>
        </label>
        <label class="full">Lampiran:
          <input id="ticketAttachment" type="file" multiple>
        </label>
      </div>
      <menu class="modal-actions">
        <button class="btn ghost" id="cancelTicketBtn" type="button">Batal</button>
        <button class="btn primary" id="saveTicketBtn" type="submit">Simpan Aspirasi</button>
      </menu>
    </form>
  </dialog>

  <dialog id="detailDialog">
    <div class="modal">
      <div class="modal-head">
        <h3 id="detailTitle">Detail Tiket</h3>
        <button class="icon-btn close-btn" id="closeDetailBtn" type="button" aria-label="Tutup"></button>
      </div>
      <div id="detailContent"></div>
    </div>
  </dialog>

  <dialog id="notificationDialog">
    <div class="modal compact-modal">
      <div class="modal-head">
        <h3>Notifikasi SLA & Eskalasi</h3>
        <button class="icon-btn close-btn" id="closeNotificationBtn" type="button" aria-label="Tutup"></button>
      </div>
      <div id="notificationCenterList" class="notification-center"></div>
    </div>
  </dialog>

  <div id="toast" class="toast" role="status" aria-live="polite"></div>
  <script src="assets/app.js?v=<?= htmlspecialchars($assetVersion, ENT_QUOTES, 'UTF-8') ?>"></script>
</body>
</html>
