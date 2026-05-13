const seed = {
  aspirasi: [
    { id: "ASP-2026-001", tanggal: "2026-05-09", nama: "Ahmad Rizki", kategori: "Infrastruktur", prioritas: "Tinggi", status: "Baru", wilayah: "DKI Jakarta", judul: "Perbaikan jalan rusak di Cengkareng", deskripsi: "Jalan utama rusak dan membahayakan pengendara saat jam padat.", kanal: "WhatsApp", pic: "DPC Jakarta Barat" },
    { id: "ASP-2026-002", tanggal: "2026-05-08", nama: "Siti Nurhaliza", kategori: "Pendidikan", prioritas: "Sedang", status: "Diproses", wilayah: "Jawa Barat", judul: "Kekurangan guru SD negeri", deskripsi: "Orang tua meminta advokasi penambahan guru kelas dan fasilitas belajar.", kanal: "Form Web", pic: "DPD Bandung" },
    { id: "ASP-2026-003", tanggal: "2026-05-07", nama: "Rahmat Hidayat", kategori: "Kesehatan", prioritas: "Kritis", status: "Eskalasi", wilayah: "Jawa Timur", judul: "Antrian BPJS terlalu panjang", deskripsi: "Warga lansia sulit mendapat pelayanan cepat di fasilitas kesehatan setempat.", kanal: "Hotline", pic: "DPW Jawa Timur" },
    { id: "ASP-2026-004", tanggal: "2026-05-06", nama: "Dewi Lestari", kategori: "Ekonomi", prioritas: "Sedang", status: "Selesai", wilayah: "Jawa Tengah", judul: "Pendampingan UMKM pasar tradisional", deskripsi: "Pelaku UMKM meminta pelatihan pemasaran digital dan akses pembiayaan.", kanal: "Media Sosial", pic: "Bidang Ekonomi" }
  ],
  pengaduan: [
    { id: "PEN-2026-001", tanggal: "2026-05-10", nama: "Budi Santoso", kategori: "Hukum", prioritas: "Kritis", status: "Baru", wilayah: "Jawa Barat", judul: "Dugaan penyimpangan proyek jembatan", deskripsi: "Pelapor meminta investigasi awal dan perlindungan identitas.", kanal: "Email", pic: "Tim Advokasi Hukum", lokasi: "Kabupaten Bogor" },
    { id: "PEN-2026-002", tanggal: "2026-05-09", nama: "Maria Magdalena", kategori: "Pelayanan Publik", prioritas: "Tinggi", status: "Diproses", wilayah: "DKI Jakarta", judul: "Pungutan liar administrasi warga", deskripsi: "Warga mengeluhkan pungutan tambahan untuk layanan administrasi.", kanal: "WhatsApp", pic: "DPC Jakarta Timur", lokasi: "Kecamatan Kramat Jati" },
    { id: "PEN-2026-003", tanggal: "2026-05-07", nama: "Taufik Akbar", kategori: "Infrastruktur", prioritas: "Sedang", status: "Eskalasi", wilayah: "Sumatera Utara", judul: "Drainase menyebabkan banjir", deskripsi: "Drainase tersumbat selama dua bulan dan belum ada tindak lanjut.", kanal: "Aplikasi Mobile", pic: "DPW Sumatera Utara", lokasi: "Kota Medan" }
  ],
  osint: {
    topics: [
      { tag: "#HargaSembako", mentions: 15420, sentiment: "Negatif", rekomendasi: "Susun respon kebijakan dan advokasi pasar murah." },
      { tag: "#PendidikanGratis", mentions: 5430, sentiment: "Positif", rekomendasi: "Amplifikasi program dan kumpulkan testimoni." },
      { tag: "#Infrastruktur", mentions: 6720, sentiment: "Netral", rekomendasi: "Petakan wilayah keluhan untuk koordinasi DPC." },
      { tag: "#Kesehatan", mentions: 4210, sentiment: "Negatif", rekomendasi: "Eskalasi isu fasilitas kesehatan ke struktur wilayah." }
    ],
    feed: [
      { sumber: "X/Twitter", cluster: "Ekonomi", teks: "Harga bahan pokok naik, butuh kanal aspirasi yang cepat.", waktu: "12 menit lalu" },
      { sumber: "Facebook", cluster: "Pendidikan", teks: "Program bantuan pendidikan mendapat respon positif dari orang tua.", waktu: "21 menit lalu" },
      { sumber: "Instagram", cluster: "Infrastruktur", teks: "Warga menandai kondisi jalan rusak dan meminta advokasi.", waktu: "38 menit lalu" }
    ]
  }
};

const provinces = [
  "Aceh",
  "Sumatera Utara",
  "Sumatera Barat",
  "Riau",
  "Kepulauan Riau",
  "Jambi",
  "Sumatera Selatan",
  "Kepulauan Bangka Belitung",
  "Bengkulu",
  "Lampung",
  "Banten",
  "DKI Jakarta",
  "Jawa Barat",
  "Jawa Tengah",
  "Daerah Istimewa Yogyakarta",
  "Jawa Timur",
  "Bali",
  "Nusa Tenggara Barat",
  "Nusa Tenggara Timur",
  "Kalimantan Barat",
  "Kalimantan Tengah",
  "Kalimantan Selatan",
  "Kalimantan Timur",
  "Kalimantan Utara",
  "Sulawesi Utara",
  "Gorontalo",
  "Sulawesi Tengah",
  "Sulawesi Barat",
  "Sulawesi Selatan",
  "Sulawesi Tenggara",
  "Maluku",
  "Maluku Utara",
  "Papua",
  "Papua Barat",
  "Papua Tengah",
  "Papua Pegunungan",
  "Papua Selatan",
  "Papua Barat Daya"
];

const workflow = [
  { step: "01", title: "Data Ingestion", text: "Aspirasi, pengaduan, WhatsApp, email, form web, mobile app, dan OSINT masuk ke antrean terpadu." },
  { step: "02", title: "Normalisasi & QC", text: "Data dibersihkan, dilengkapi wilayah, kategori, sumber, kredibilitas, dan duplikasi." },
  { step: "03", title: "AI Processing", text: "Sistem melakukan clustering topik, klasifikasi kategori, sentiment, urgensi, dan estimasi dampak." },
  { step: "04", title: "Triage & SLA", text: "Tiket diberi prioritas, PIC, tenggat waktu, dan jalur eskalasi sesuai level struktur." },
  { step: "05", title: "Advokasi & Layanan", text: "DPC, DPD, DPW, DPP, atau legislator melakukan tindak lanjut dengan bukti kerja." },
  { step: "06", title: "Feedback & Policy", text: "Status dikirim ke pelapor, data menjadi rekomendasi kebijakan dan laporan eksekutif." }
];

const escalation = [
  ["Lokal", "Isu satu kelurahan/kecamatan, dampak rendah-sedang", "DPC/DPRa", "2x24 jam"],
  ["Kab/Kota", "Isu lintas kecamatan atau butuh koordinasi OPD", "DPD/Legislator daerah", "3x24 jam"],
  ["Provinsi", "Isu lintas kab/kota atau sentimen publik meningkat", "DPW", "5 hari kerja"],
  ["Nasional", "Isu strategis, hukum, krisis reputasi, atau kebijakan pusat", "DPP/Fraksi", "7 hari kerja"]
];

let state = loadState();
let currentPage = "dashboard";
let apiAvailable = false;
let authToken = localStorage.getItem("spap-auth-token") || "";
let currentUser = null;
const API_BASE = window.SPAP_CONFIG?.apiBaseUrl || (window.location.port === "3000" ? "" : "http://localhost:3000");

function populateProvinceOptions() {
  const regionFilter = document.getElementById("regionFilter");
  const ticketRegion = document.getElementById("ticketRegion");
  const reportRegion = document.getElementById("reportRegion");

  provinces.forEach(province => {
    regionFilter.appendChild(new Option(province, province));
    ticketRegion.appendChild(new Option(province, province));
    reportRegion.appendChild(new Option(province, province));
  });

  ticketRegion.value = "";
}

function setConnectionStatus(text, online) {
  const label = document.getElementById("connectionStatus");
  const dot = document.querySelector(".status-dot");
  if (!label || !dot) return;
  label.textContent = text;
  dot.style.background = online ? "#22c55e" : "#facc15";
}

function loadState() {
  const saved = localStorage.getItem("spap-state");
  if (!saved) return structuredClone(seed);
  try {
    return JSON.parse(saved);
  } catch {
    return structuredClone(seed);
  }
}

function saveState() {
  localStorage.setItem("spap-state", JSON.stringify(state));
}

async function apiRequest(path, options = {}) {
  const headers = { "Content-Type": "application/json", ...(options.headers || {}) };
  if (authToken) headers.Authorization = `Bearer ${authToken}`;
  const response = await fetch(`${API_BASE}${path}`, {
    headers,
    ...options
  });
  if (!response.ok) {
    throw new Error(`API ${response.status}: ${path}`);
  }
  return response.json();
}

function applyAuthState() {
  const loggedIn = Boolean(currentUser);
  document.getElementById("loginScreen").classList.toggle("hidden", loggedIn);
  document.getElementById("appShell").classList.toggle("app-locked", !loggedIn);
  document.getElementById("currentUserName").textContent = currentUser?.name || "-";
  document.getElementById("currentUserRole").textContent = currentUser ? (currentUser.role === "admin" ? "Admin" : "User") : "-";

  document.querySelectorAll('[data-page="settings"]').forEach(item => {
    item.classList.toggle("nav-hidden", currentUser?.role !== "admin");
  });

  if (loggedIn && currentPage === "settings" && currentUser.role !== "admin") {
    setPage("dashboard");
  }
}

async function restoreSession() {
  if (!authToken) {
    applyAuthState();
    return;
  }
  try {
    const payload = await apiRequest("/api/auth/me");
    currentUser = payload.data;
  } catch (error) {
    authToken = "";
    localStorage.removeItem("spap-auth-token");
    currentUser = null;
  }
  applyAuthState();
}

async function login(event) {
  event.preventDefault();
  try {
    const payload = await apiRequest("/api/auth/login", {
      method: "POST",
      body: JSON.stringify({
        email: document.getElementById("loginEmail").value,
        password: document.getElementById("loginPassword").value
      })
    });
    authToken = payload.data.token;
    currentUser = payload.data.user;
    localStorage.setItem("spap-auth-token", authToken);
    applyAuthState();
    await loadData();
    toast(`Selamat datang, ${currentUser.name}`);
  } catch (error) {
    toast("Login gagal. Periksa email dan password.");
  }
}

async function logout() {
  try {
    await apiRequest("/api/auth/logout", { method: "POST", body: "{}" });
  } catch (error) {
    // Session tetap dibersihkan di sisi browser.
  }
  authToken = "";
  currentUser = null;
  localStorage.removeItem("spap-auth-token");
  applyAuthState();
}

function normalizeTicket(row) {
  return {
    id: row.public_id || row.id,
    tanggal: (row.created_at || row.tanggal || new Date().toISOString()).slice(0, 10),
    nama: row.reporter_name || row.nama,
    kategori: row.category || row.kategori,
    prioritas: row.priority || row.prioritas,
    status: row.status,
    wilayah: row.region || row.wilayah,
    judul: row.subject || row.judul,
    deskripsi: row.description || row.deskripsi,
    kanal: row.channel || row.kanal || "API",
    pic: row.assigned_unit || row.pic || "Triage SPAP",
    lokasi: row.region || row.lokasi || row.wilayah
  };
}

function normalizeOsint(row) {
  return {
    tag: row.keyword || row.tag,
    mentions: row.mention_count || row.mentions || 0,
    sentiment: row.sentiment || "Netral",
    rekomendasi: row.recommendation || row.rekomendasi || "Pantau dan lakukan triage isu."
  };
}

async function loadRemoteState() {
  const [ticketsPayload, osintPayload] = await Promise.all([
    apiRequest("/api/tickets"),
    apiRequest("/api/osint/mentions")
  ]);

  const tickets = ticketsPayload.data || [];
  state.aspirasi = tickets.filter(item => item.type === "aspirasi").map(normalizeTicket);
  state.pengaduan = tickets.filter(item => item.type === "pengaduan").map(normalizeTicket);

  const osintRows = osintPayload.data || [];
  state.osint.topics = osintRows.map(normalizeOsint);
  state.osint.feed = osintRows.map(row => ({
    sumber: row.source,
    cluster: row.cluster_name || "Umum",
    teks: row.sample_text || row.recommendation || "Sinyal ruang publik terdeteksi.",
    waktu: "baru saja"
  }));

  apiAvailable = true;
  setConnectionStatus("API backend aktif", true);
  saveState();
}

async function loadData() {
  try {
    await loadRemoteState();
  } catch (error) {
    apiAvailable = false;
    setConnectionStatus("Mode lokal aktif", false);
  }
  renderAll();
}

function allTickets() {
  return [
    ...state.aspirasi.map(item => ({ ...item, tipe: "Aspirasi" })),
    ...state.pengaduan.map(item => ({ ...item, tipe: "Pengaduan" }))
  ];
}

function scopedTickets() {
  const region = document.getElementById("regionFilter").value;
  return allTickets().filter(item => region === "Semua" || item.wilayah === region);
}

function badge(text) {
  return `<span class="badge ${text}">${text}</span>`;
}

function toast(message) {
  const el = document.getElementById("toast");
  el.textContent = message;
  el.classList.add("show");
  setTimeout(() => el.classList.remove("show"), 2200);
}

function renderMetrics() {
  const tickets = scopedTickets();
  const selesai = tickets.filter(t => t.status === "Selesai").length;
  const aspirasi = tickets.filter(t => t.tipe === "Aspirasi").length;
  const pengaduan = tickets.filter(t => t.tipe === "Pengaduan").length;
  const responseTime = Math.max(1.8, (4.6 - selesai * 0.35)).toFixed(1);
  const metrics = [
    ["Total Aspirasi", aspirasi.toLocaleString("id-ID"), "+12% dari minggu lalu"],
    ["Total Pengaduan", pengaduan.toLocaleString("id-ID"), "-5% dari minggu lalu"],
    ["Issues Terselesaikan", selesai.toLocaleString("id-ID"), "+18% dari minggu lalu"],
    ["Rata-rata Response Time", responseTime, "-0.5 jam dari minggu lalu"]
  ];
  const icons = ["A", "P", "S", "T"];
  document.getElementById("metricGrid").innerHTML = metrics.map(([label, value, trend], index) => `
    <article class="metric dashboard-kpi"><div class="kpi-icon">${icons[index]}</div><strong>${value}</strong><span>${label}</span><em class="${trend.startsWith("-") ? "down" : ""}">${trend}</em></article>
  `).join("");
}

function renderTrendChart() {
  const labels = ["Sen", "Sel", "Rab", "Kam", "Jum"];
  const aspirasi = [44, 51, 38, 67, 89];
  const pengaduan = [32, 28, 45, 38, 52];
  const max = 100;
  const width = 900;
  const height = 250;
  const padding = 28;
  const xStep = (width - padding * 2) / (labels.length - 1);
  const y = value => height - padding - (value / max) * (height - padding * 2);
  const x = index => padding + index * xStep;
  const points = values => values.map((value, index) => `${x(index)},${y(value)}`).join(" ");
  const circles = (values, className) => values.map((value, index) => `<circle class="${className}" cx="${x(index)}" cy="${y(value)}" r="4" />`).join("");
  const grid = [0, 20, 40, 60, 80, 100].map(value => {
    const yy = y(value);
    return `<line class="chart-grid" x1="${padding}" y1="${yy}" x2="${width - padding}" y2="${yy}" /><text class="chart-axis" x="4" y="${yy + 4}">${value}</text>`;
  }).join("");
  const axis = labels.map((label, index) => `<text class="chart-axis" x="${x(index) - 8}" y="${height - 6}">${label}</text>`).join("");

  document.getElementById("trendChart").innerHTML = `
    <svg viewBox="0 0 ${width} ${height}" role="img" aria-label="Trend aspirasi dan pengaduan">
      ${grid}
      <polyline class="line-aspirasi" points="${points(aspirasi)}" />
      <polyline class="line-pengaduan" points="${points(pengaduan)}" />
      ${circles(aspirasi, "dot-aspirasi")}
      ${circles(pengaduan, "dot-pengaduan")}
      ${axis}
    </svg>
  `;
}

function renderGeoDistribution() {
  const tickets = scopedTickets();
  const base = {
    "DKI Jakarta": 245,
    "Jawa Barat": 189,
    "Jawa Tengah": 156,
    "Jawa Timur": 134,
    "Sumatera Utara": 98,
    "Sumatera Selatan": 87,
    "Kalimantan Timur": 76,
    "Sulawesi Selatan": 65
  };

  tickets.forEach(ticket => {
    base[ticket.wilayah] = (base[ticket.wilayah] || 20) + 1;
  });

  document.getElementById("geoDistribution").innerHTML = Object.entries(base)
    .sort((a, b) => b[1] - a[1])
    .slice(0, 8)
    .map(([province, total], index) => `<article class="geo-card tone-${index % 4}"><span>${province}</span><strong>${total}</strong></article>`)
    .join("");
}

function renderCategoryDonut() {
  const categories = [
    ["Infrastruktur", 35, "#ff8a00"],
    ["Pendidikan", 25, "#ff6537"],
    ["Kesehatan", 20, "#e9552a"],
    ["Ekonomi", 15, "#ffa600"],
    ["Sosial", 5, "#ffb94d"]
  ];
  let offset = 0;
  const segments = categories.map(([label, value, color]) => {
    const dash = `${value} ${100 - value}`;
    const segment = `<circle class="donut-segment" r="45" cx="60" cy="60" stroke="${color}" stroke-dasharray="${dash}" stroke-dashoffset="${-offset}" />`;
    offset += value;
    return segment;
  }).join("");

  document.getElementById("categoryDonut").innerHTML = `
    <svg viewBox="0 0 120 120" role="img" aria-label="Kategori aspirasi">
      <circle class="donut-track" r="45" cx="60" cy="60" />
      ${segments}
      <circle class="donut-hole" r="27" cx="60" cy="60" />
    </svg>
    <div class="category-legend">
      ${categories.map(([label, value, color]) => `<span><i style="background:${color}"></i>${label} <strong>${value}%</strong></span>`).join("")}
    </div>
  `;
}

function renderDashboardActivities() {
  const items = [
    ["Aspirasi baru diterima", "Infrastruktur jalan di Depok", "5 menit lalu", "A"],
    ["Pengaduan terselesaikan", "Masalah air bersih di Bekasi", "15 menit lalu", "S"],
    ["Laporan mingguan generated", "Statistik periode 1-7 Jan 2024", "1 jam lalu", "L"],
    ["OSINT alert detected", "Trending topic: #HargaSembako", "2 jam lalu", "O"],
    ["Pengaduan baru", "Fasilitas kesehatan di Bandung", "3 jam lalu", "P"]
  ];

  document.getElementById("dashboardActivityList").innerHTML = items.map(([title, desc, time, icon]) => `
    <div class="timeline-item"><span class="timeline-icon">${icon}</span><div><strong>${title}</strong><p>${desc}</p></div><time>${time}</time></div>
  `).join("");
}

function renderDashboardAlerts() {
  const urgent = scopedTickets().filter(ticket => ticket.prioritas === "Kritis" || ticket.status === "Eskalasi");
  const alerts = urgent.length
    ? urgent.slice(0, 3).map(ticket => [ticket.prioritas === "Kritis" ? "Aspirasi Kritis" : "Perlu Eskalasi", `${ticket.judul} - ${ticket.wilayah}`])
    : [["Aspirasi Kritis", "15 aspirasi infrastruktur belum ditindaklanjuti > 48 jam"]];

  document.getElementById("dashboardAlerts").innerHTML = alerts.map(([title, desc]) => `
    <div class="dashboard-alert"><strong>${title}</strong><p>${desc}</p></div>
  `).join("");
}

function renderPipeline() {
  const tickets = scopedTickets();
  const statuses = ["Baru", "Diproses", "Eskalasi", "Selesai"];
  document.getElementById("pipeline").innerHTML = statuses.map(status => {
    const count = tickets.filter(t => t.status === status).length;
    return `<div class="stage"><span>${status}</span><strong>${count}</strong><small>${Math.max(1, count * 2)} aktivitas</small></div>`;
  }).join("");
}

function renderCategoryBars() {
  const tickets = scopedTickets();
  const counts = tickets.reduce((acc, item) => {
    acc[item.kategori] = (acc[item.kategori] || 0) + 1;
    return acc;
  }, {});
  const max = Math.max(1, ...Object.values(counts));
  document.getElementById("categoryBars").innerHTML = Object.entries(counts).map(([name, count]) => `
    <div class="bar-row"><span>${name}</span><div class="bar-track"><div class="bar-fill" style="width:${(count / max) * 100}%"></div></div><strong>${count}</strong></div>
  `).join("");
}

function renderWarnings() {
  const urgent = scopedTickets()
    .filter(item => item.prioritas === "Kritis" || item.status === "Eskalasi")
    .slice(0, 5);
  document.getElementById("warningList").innerHTML = urgent.map(item => `
    <div class="warning ${item.prioritas.toLowerCase()}"><strong>${item.judul}</strong><p>${item.wilayah} - ${item.pic}</p>${badge(item.prioritas)}</div>
  `).join("") || `<div class="warning positif"><strong>Tidak ada krisis aktif</strong><p>Semua tiket kritis sudah terkendali.</p></div>`;
}

function renderActivities() {
  const items = scopedTickets().sort((a, b) => b.tanggal.localeCompare(a.tanggal)).slice(0, 8);
  document.getElementById("activityList").innerHTML = items.map(item => `
    <div class="activity-item"><strong>${item.tipe} - ${item.judul}</strong><br><span>${item.tanggal} - ${item.kanal} - ${item.pic}</span></div>
  `).join("");
}

function renderDashboard() {
  renderMetrics();
  renderTrendChart();
  renderGeoDistribution();
  renderCategoryDonut();
  renderDashboardActivities();
  renderDashboardAlerts();
  renderPipeline();
  renderCategoryBars();
  renderWarnings();
  renderActivities();
}

function ticketMatches(item, search, status, priority) {
  const haystack = `${item.id} ${item.nama} ${item.judul} ${item.kategori} ${item.wilayah} ${item.deskripsi}`.toLowerCase();
  return (!search || haystack.includes(search.toLowerCase()))
    && (status === "Semua" || item.status === status)
    && (priority === "Semua" || item.prioritas === priority);
}

function renderAspirasi() {
  const search = document.getElementById("aspirasiSearch").value;
  const status = document.getElementById("aspirasiStatus").value;
  const priority = document.getElementById("aspirasiPriority").value;
  document.getElementById("aspirasiTable").innerHTML = state.aspirasi
    .filter(item => ticketMatches(item, search, status, priority))
    .map(item => `
      <tr>
        <td>${item.id}</td><td>${item.nama}</td><td><strong>${item.judul}</strong><br><small>${item.kanal}</small></td>
        <td>${item.kategori}</td><td>${item.wilayah}</td><td>${badge(item.prioritas)}</td><td>${badge(item.status)}</td>
        <td><button class="btn ghost" onclick="openDetail('aspirasi','${item.id}')">Detail</button></td>
      </tr>
    `).join("");
}

function renderPengaduan() {
  const search = document.getElementById("pengaduanSearch").value;
  const status = document.getElementById("pengaduanStatus").value;
  const priority = document.getElementById("pengaduanPriority").value;
  document.getElementById("pengaduanGrid").innerHTML = state.pengaduan
    .filter(item => ticketMatches(item, search, status, priority))
    .map(item => `
      <article class="ticket-card">
        <div class="ticket-meta"><span>${item.id}</span><span>${item.tanggal}</span></div>
        <h3>${item.judul}</h3>
        <p>${item.deskripsi}</p>
        <div class="ticket-meta"><span>${item.lokasi}</span><span>${item.pic}</span></div>
        <div class="card-actions">${badge(item.prioritas)} ${badge(item.status)} <button class="btn ghost" onclick="openDetail('pengaduan','${item.id}')">Detail</button></div>
      </article>
    `).join("");
}

function openDetail(type, id) {
  const collection = type === "aspirasi" ? state.aspirasi : state.pengaduan;
  const item = collection.find(ticket => ticket.id === id);
  if (!item) return;
  document.getElementById("detailTitle").textContent = `${type === "aspirasi" ? "Aspirasi" : "Pengaduan"} ${item.id}`;
  document.getElementById("detailContent").innerHTML = `
    <div class="detail-grid">
      <div class="detail-box"><strong>Pelapor</strong><br>${item.nama}</div>
      <div class="detail-box"><strong>Wilayah</strong><br>${item.wilayah}</div>
      <div class="detail-box"><strong>Kategori</strong><br>${item.kategori}</div>
      <div class="detail-box"><strong>PIC</strong><br>${item.pic}</div>
      <div class="detail-box"><strong>Prioritas</strong><br>${badge(item.prioritas)}</div>
      <div class="detail-box"><strong>Status</strong><br>${badge(item.status)}</div>
    </div>
    <h4>${item.judul}</h4>
    <p>${item.deskripsi}</p>
    <div class="card-actions">
      <button class="btn" onclick="updateTicketStatus('${type}','${item.id}','Diproses')">Proses</button>
      <button class="btn" onclick="updateTicketStatus('${type}','${item.id}','Eskalasi')">Eskalasi</button>
      <button class="btn primary" onclick="updateTicketStatus('${type}','${item.id}','Selesai')">Selesaikan</button>
    </div>
  `;
  const detailDialog = document.getElementById("detailDialog");
  if (!detailDialog.open) detailDialog.showModal();
}

async function updateTicketStatus(type, id, status) {
  const collection = type === "aspirasi" ? state.aspirasi : state.pengaduan;
  const item = collection.find(ticket => ticket.id === id);
  if (!item) return;

  if (apiAvailable) {
    try {
      await apiRequest(`/api/tickets/${id}/status`, {
        method: "PATCH",
        body: JSON.stringify({ status, note: `Status diubah melalui UI ke ${status}` })
      });
    } catch (error) {
      apiAvailable = false;
      setConnectionStatus("Mode lokal aktif", false);
      toast("API tidak merespon, perubahan disimpan lokal");
    }
  }

  item.status = status;
  saveState();
  renderAll();
  openDetail(type, id);
  toast(`Status ${id} diubah ke ${status}`);
}

function renderOsint() {
  document.getElementById("topicGrid").innerHTML = state.osint.topics.map(topic => `
    <article class="topic-card">
      <span>${topic.tag}</span>
      <strong>${topic.mentions.toLocaleString("id-ID")}</strong>
      ${badge(topic.sentiment === "Negatif" ? "Kritis" : topic.sentiment === "Positif" ? "Rendah" : "Sedang")}
      <p>${topic.rekomendasi}</p>
    </article>
  `).join("");
  document.getElementById("osintAlerts").innerHTML = state.osint.topics
    .filter(topic => topic.sentiment === "Negatif")
    .map(topic => `<div class="warning negatif"><strong>${topic.tag}</strong><p>${topic.rekomendasi}</p></div>`).join("");
  document.getElementById("feedList").innerHTML = state.osint.feed.map(item => `
    <div class="feed-item"><strong>${item.cluster} - ${item.sumber}</strong><p>${item.teks}</p><span>${item.waktu}</span></div>
  `).join("");
}

function renderWorkflow() {
  document.getElementById("workflowGrid").innerHTML = workflow.map(item => `
    <article class="workflow-card"><span>${item.step}</span><h3>${item.title}</h3><p>${item.text}</p></article>
  `).join("");
  document.getElementById("escalationTable").innerHTML = escalation.map(row => `
    <tr><td>${row[0]}</td><td>${row[1]}</td><td>${row[2]}</td><td>${row[3]}</td></tr>
  `).join("");
}

function renderReport() {
  const tickets = scopedTickets();
  const unresolved = tickets.filter(item => item.status !== "Selesai").length;
  const topCategory = Object.entries(tickets.reduce((acc, item) => {
    acc[item.kategori] = (acc[item.kategori] || 0) + 1;
    return acc;
  }, {})).sort((a, b) => b[1] - a[1])[0]?.[0] || "-";
  document.getElementById("reportPreview").innerHTML = `
    <h3>${document.getElementById("reportType").value}</h3>
    <p>Periode: ${document.getElementById("reportPeriod").value} - Wilayah: ${document.getElementById("reportRegion").value}</p>
    <hr>
    <p><strong>Total tiket:</strong> ${tickets.length}</p>
    <p><strong>Belum selesai:</strong> ${unresolved}</p>
    <p><strong>Kategori dominan:</strong> ${topCategory}</p>
    <p><strong>Isu OSINT utama:</strong> ${(state.osint.topics[0]?.tag || "-")} dengan ${(state.osint.topics[0]?.mentions || 0).toLocaleString("id-ID")} mentions.</p>
    <p><strong>Rekomendasi:</strong> Perkuat triage tiket prioritas, siapkan respon isu publik, dan kirim ringkasan ke struktur wilayah terkait.</p>
  `;
}

function renderAll() {
  renderDashboard();
  renderAspirasi();
  renderPengaduan();
  renderOsint();
  renderWorkflow();
  renderReport();
}

function setPage(page) {
  currentPage = page;
  document.querySelectorAll(".page").forEach(el => el.classList.toggle("active", el.id === page));
  document.querySelectorAll(".nav-item").forEach(el => el.classList.toggle("active", el.dataset.page === page));
  const titles = {
    dashboard: "Dashboard Operasional",
    aspirasi: "Manajemen Aspirasi",
    pengaduan: "Manajemen Pengaduan",
    osint: "OSINT Monitoring",
    analytics: "Analytics SPAP",
    laporan: "Laporan",
    infra: "Konsep Infrastruktur",
    workflow: "Proses Bisnis SPAP",
    settings: "Pengaturan"
  };
  document.getElementById("pageTitle").textContent = titles[page];
  const button = document.getElementById("newTicketBtn");
  if (page === "pengaduan") {
    button.textContent = "Tambah Pengaduan";
  } else if (page === "aspirasi" || page === "dashboard") {
    button.textContent = "Tambah Aspirasi";
  } else {
    button.textContent = "Tambah Tiket";
  }
}

async function addTicket(event) {
  event.preventDefault();
  const type = document.getElementById("ticketType").value;
  const email = document.getElementById("ticketEmail").value;
  const phone = document.getElementById("ticketPhone").value;
  const region = document.getElementById("ticketRegion").value;
  const category = document.getElementById("ticketCategory").value;
  const priority = document.getElementById("ticketPriority").value;
  if (!region || !category || !priority) {
    toast("Lengkapi wilayah, kategori, dan prioritas");
    return;
  }
  const prefix = type === "aspirasi" ? "ASP" : "PEN";
  const collection = type === "aspirasi" ? state.aspirasi : state.pengaduan;
  const next = String(collection.length + 1).padStart(3, "0");
  const item = {
    id: `${prefix}-2026-${next}`,
    tanggal: new Date().toISOString().slice(0, 10),
    nama: document.getElementById("ticketName").value,
    wilayah: region,
    kategori: category,
    prioritas: priority,
    status: "Baru",
    judul: document.getElementById("ticketSubject").value,
    deskripsi: document.getElementById("ticketDescription").value,
    kanal: "Input Operator",
    pic: "Triage SPAP",
    lokasi: region,
    email,
    phone
  };

  if (apiAvailable) {
    try {
      const payload = await apiRequest("/api/tickets", {
        method: "POST",
        body: JSON.stringify({
          type,
          reporterName: item.nama,
          reporterContact: phone || email,
          channel: item.kanal,
          region: item.wilayah,
          category: item.kategori,
          priority: item.prioritas,
          subject: item.judul,
          description: item.deskripsi,
          assignedUnit: item.pic
        })
      });
      Object.assign(item, normalizeTicket(payload.data));
    } catch (error) {
      apiAvailable = false;
      setConnectionStatus("Mode lokal aktif", false);
      toast("API tidak merespon, tiket disimpan lokal");
    }
  }

  collection.unshift(item);
  saveState();
  document.getElementById("ticketDialog").close();
  document.getElementById("ticketForm").reset();
  renderAll();
  setPage(type);
  toast(`${item.id} berhasil dibuat`);
}

function closeTicketDialog() {
  const dialog = document.getElementById("ticketDialog");
  document.getElementById("ticketForm").reset();
  if (dialog.open) dialog.close();
}

function openTicketDialog() {
  const type = currentPage === "pengaduan" ? "pengaduan" : "aspirasi";
  document.getElementById("ticketType").value = type;
  document.getElementById("dialogTitle").innerHTML = type === "pengaduan"
    ? '<span class="modal-title-icon">+</span> Tambah Pengaduan Baru'
    : '<span class="modal-title-icon">+</span> Tambah Aspirasi Baru';
  document.getElementById("saveTicketBtn").textContent = type === "pengaduan" ? "Simpan Pengaduan" : "Simpan Aspirasi";
  document.getElementById("ticketDialog").showModal();
}

function bindEvents() {
  document.getElementById("loginForm").addEventListener("submit", login);
  document.getElementById("logoutBtn").addEventListener("click", logout);
  document.querySelectorAll(".nav-item").forEach(button => {
    button.addEventListener("click", () => {
      if (button.dataset.page === "settings" && currentUser?.role !== "admin") {
        toast("Menu Pengaturan hanya untuk admin");
        return;
      }
      setPage(button.dataset.page);
    });
  });
  document.getElementById("regionFilter").addEventListener("change", renderAll);
  document.getElementById("newTicketBtn").addEventListener("click", openTicketDialog);
  document.getElementById("ticketForm").addEventListener("submit", addTicket);
  document.getElementById("closeTicketBtn").addEventListener("click", closeTicketDialog);
  document.getElementById("cancelTicketBtn").addEventListener("click", closeTicketDialog);
  document.getElementById("ticketDialog").addEventListener("click", event => {
    if (event.target.id === "ticketDialog") closeTicketDialog();
  });
  document.getElementById("closeDetailBtn").addEventListener("click", () => document.getElementById("detailDialog").close());
  document.getElementById("refreshBtn").addEventListener("click", async () => { await loadData(); toast("Data dashboard diperbarui"); });
  ["aspirasiSearch", "aspirasiStatus", "aspirasiPriority"].forEach(id => document.getElementById(id).addEventListener("input", renderAspirasi));
  ["pengaduanSearch", "pengaduanStatus", "pengaduanPriority"].forEach(id => document.getElementById(id).addEventListener("input", renderPengaduan));
  ["reportType", "reportPeriod", "reportRegion", "reportFormat"].forEach(id => document.getElementById(id).addEventListener("change", renderReport));
  document.getElementById("reportForm").addEventListener("submit", event => { event.preventDefault(); renderReport(); toast("Preview laporan dibuat"); });
  document.getElementById("simulateOsintBtn").addEventListener("click", () => {
    state.osint.topics.forEach(topic => topic.mentions += Math.floor(Math.random() * 900));
    saveState();
    renderOsint();
    renderReport();
    toast("Sinyal OSINT disimulasikan");
  });
}

populateProvinceOptions();
bindEvents();
restoreSession().then(loadData);
setPage(currentPage);
