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
  },
  notifications: []
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

const recipientDirectory = {
  default: {
    dprRi: ["Dapil DPR RI Nasional"],
    dprdProvinsi: ["Dapil Provinsi Utama"],
    cities: ["Kabupaten/Kota Utama"],
    names: {
      "DPR RI": ["Fraksi PKS DPR RI - Koordinator Aspirasi"],
      "DPRD Provinsi": ["Fraksi PKS DPRD Provinsi - Koordinator Wilayah"],
      "DPRD Kab/Kota": ["Fraksi PKS DPRD Kab/Kota - Koordinator Pengaduan"]
    }
  },
  "DKI Jakarta": {
    dprRi: ["DKI Jakarta I", "DKI Jakarta II", "DKI Jakarta III"],
    dprdProvinsi: ["DKI Jakarta 1", "DKI Jakarta 2", "DKI Jakarta 3", "DKI Jakarta 4"],
    cities: ["Jakarta Pusat", "Jakarta Utara", "Jakarta Barat", "Jakarta Selatan", "Jakarta Timur", "Kepulauan Seribu"],
    names: {
      "DPR RI": ["Anggota DPR RI PKS Dapil DKI Jakarta I", "Anggota DPR RI PKS Dapil DKI Jakarta II", "Anggota DPR RI PKS Dapil DKI Jakarta III"],
      "DPRD Provinsi": ["Fraksi PKS DPRD DKI Jakarta", "Ketua Fraksi PKS DPRD DKI"],
      "DPRD Kab/Kota": ["Koordinator DPD PKS Jakarta Pusat", "Koordinator DPD PKS Jakarta Selatan", "Koordinator DPD PKS Jakarta Timur"]
    }
  },
  "Jawa Barat": {
    dprRi: ["Jawa Barat I", "Jawa Barat II", "Jawa Barat III", "Jawa Barat IV", "Jawa Barat V"],
    dprdProvinsi: ["Jabar 1", "Jabar 2", "Jabar 3", "Jabar 4", "Jabar 5"],
    cities: ["Kota Bandung", "Kabupaten Bandung", "Kota Bekasi", "Kabupaten Bekasi", "Kota Depok", "Kabupaten Bogor", "Kota Bogor", "Kota Cimahi"],
    names: {
      "DPR RI": ["Anggota DPR RI PKS Dapil Jawa Barat I", "Anggota DPR RI PKS Dapil Jawa Barat II", "Anggota DPR RI PKS Dapil Jawa Barat V"],
      "DPRD Provinsi": ["Fraksi PKS DPRD Jawa Barat", "Koordinator Fraksi PKS DPRD Jabar"],
      "DPRD Kab/Kota": ["Fraksi PKS DPRD Kota Bandung", "Fraksi PKS DPRD Kota Bekasi", "Fraksi PKS DPRD Kabupaten Bogor"]
    }
  },
  "Jawa Tengah": {
    dprRi: ["Jawa Tengah I", "Jawa Tengah II", "Jawa Tengah III", "Jawa Tengah IV"],
    dprdProvinsi: ["Jateng 1", "Jateng 2", "Jateng 3", "Jateng 4"],
    cities: ["Kota Semarang", "Kabupaten Semarang", "Kota Surakarta", "Kabupaten Banyumas", "Kabupaten Kudus", "Kabupaten Klaten"],
    names: {
      "DPR RI": ["Anggota DPR RI PKS Dapil Jawa Tengah I", "Anggota DPR RI PKS Dapil Jawa Tengah V"],
      "DPRD Provinsi": ["Fraksi PKS DPRD Jawa Tengah"],
      "DPRD Kab/Kota": ["Fraksi PKS DPRD Kota Semarang", "Fraksi PKS DPRD Kota Surakarta"]
    }
  },
  "Jawa Timur": {
    dprRi: ["Jawa Timur I", "Jawa Timur II", "Jawa Timur III", "Jawa Timur IV"],
    dprdProvinsi: ["Jatim 1", "Jatim 2", "Jatim 3", "Jatim 4"],
    cities: ["Kota Surabaya", "Kabupaten Sidoarjo", "Kabupaten Malang", "Kota Malang", "Kabupaten Gresik", "Kabupaten Jember"],
    names: {
      "DPR RI": ["Anggota DPR RI PKS Dapil Jawa Timur I", "Anggota DPR RI PKS Dapil Jawa Timur V"],
      "DPRD Provinsi": ["Fraksi PKS DPRD Jawa Timur"],
      "DPRD Kab/Kota": ["Fraksi PKS DPRD Kota Surabaya", "Fraksi PKS DPRD Kabupaten Sidoarjo"]
    }
  },
  "Sumatera Utara": {
    dprRi: ["Sumatera Utara I", "Sumatera Utara II", "Sumatera Utara III"],
    dprdProvinsi: ["Sumut 1", "Sumut 2", "Sumut 3"],
    cities: ["Kota Medan", "Kota Binjai", "Kabupaten Deli Serdang", "Kabupaten Langkat", "Kota Pematangsiantar"],
    names: {
      "DPR RI": ["Anggota DPR RI PKS Dapil Sumatera Utara I", "Anggota DPR RI PKS Dapil Sumatera Utara III"],
      "DPRD Provinsi": ["Fraksi PKS DPRD Sumatera Utara"],
      "DPRD Kab/Kota": ["Fraksi PKS DPRD Kota Medan", "Fraksi PKS DPRD Deli Serdang"]
    }
  },
  "Sulawesi Selatan": {
    dprRi: ["Sulawesi Selatan I", "Sulawesi Selatan II", "Sulawesi Selatan III"],
    dprdProvinsi: ["Sulsel 1", "Sulsel 2", "Sulsel 3"],
    cities: ["Kota Makassar", "Kabupaten Gowa", "Kabupaten Maros", "Kota Parepare", "Kabupaten Bone"],
    names: {
      "DPR RI": ["Anggota DPR RI PKS Dapil Sulawesi Selatan I", "Anggota DPR RI PKS Dapil Sulawesi Selatan II"],
      "DPRD Provinsi": ["Fraksi PKS DPRD Sulawesi Selatan"],
      "DPRD Kab/Kota": ["Fraksi PKS DPRD Kota Makassar", "Fraksi PKS DPRD Kabupaten Gowa"]
    }
  }
};

let state = loadState();
let currentPage = "dashboard";
let apiAvailable = false;
let authToken = localStorage.getItem("spap-auth-token") || "";
let currentUser = null;
let adminData = { users: [], permissions: [] };
let ticketEvents = {};
let acknowledgedNotifications = new Set(JSON.parse(localStorage.getItem("spap-ack-notifications") || "[]"));
let kpuDapilCache = {};
let kpuTargetNameOptions = [];
const API_BASE = window.SPAP_CONFIG?.apiBaseUrl || (window.location.port === "3000" ? "" : "http://localhost:3000");
const manageableMenus = ["dashboard", "aspirasi", "pengaduan", "osint", "analytics", "laporan", "settings"];
const manageableRoles = ["admin", "operator", "verifikator", "koordinator"];
const roleLabels = { admin: "Admin", operator: "Operator", verifikator: "Verifikator", koordinator: "Koordinator" };
const menuLabels = { dashboard: "Dashboard", aspirasi: "Aspirasi", pengaduan: "Pengaduan", osint: "OSINT", analytics: "Analytics", laporan: "Laporan", settings: "Pengaturan" };

function escapeHtml(value) {
  return String(value ?? "")
    .replace(/&/g, "&amp;")
    .replace(/</g, "&lt;")
    .replace(/>/g, "&gt;")
    .replace(/"/g, "&quot;")
    .replace(/'/g, "&#39;");
}

function populateProvinceOptions() {
  const regionFilter = document.getElementById("regionFilter");
  const ticketRegion = document.getElementById("ticketRegion");
  const reportRegion = document.getElementById("reportRegion");
  const waRegion = document.getElementById("waRegion");
  const userRegionScope = document.getElementById("userRegionScope");

  provinces.forEach(province => {
    regionFilter.appendChild(new Option(province, province));
    ticketRegion.appendChild(new Option(province, province));
    reportRegion.appendChild(new Option(province, province));
    if (waRegion) waRegion.appendChild(new Option(province, province));
    if (userRegionScope) userRegionScope.appendChild(new Option(province, province));
  });

  ticketRegion.value = "";
  updateRecipientFields();
}

function setSelectOptions(select, values, placeholder) {
  select.innerHTML = `<option value="">${placeholder}</option>`;
  (values || []).forEach(value => select.appendChild(new Option(value, value)));
}

function currentRecipientDirectory() {
  const province = document.getElementById("ticketRegion")?.value || "";
  return kpuDapilCache[province] || recipientDirectory[province] || recipientDirectory.default;
}

function updateRecipientFields() {
  const level = document.getElementById("ticketTargetLevel")?.value || "DPR RI";
  const province = document.getElementById("ticketRegion")?.value || "";
  const directory = currentRecipientDirectory();
  const provinceInput = document.getElementById("ticketTargetProvince");
  const dapilSelect = document.getElementById("ticketTargetDapil");
  const citySelect = document.getElementById("ticketTargetCity");
  if (!provinceInput || !dapilSelect || !citySelect) return;

  provinceInput.value = province;
  const dapils = level === "DPR RI" ? directory.dprRi : directory.dprdProvinsi;
  setSelectOptions(dapilSelect, dapils, "Pilih Dapil");
  setSelectOptions(citySelect, directory.cities, "Pilih Kota/Kabupaten");
  updateRecipientNameOptions();

  citySelect.disabled = level !== "DPRD Kab/Kota";
  if (citySelect.disabled) citySelect.value = "";
}

function buildPksDprRiNames(directory, selectedDapil) {
  const byDapil = directory.pksDprRi || {};
  if (selectedDapil && byDapil[selectedDapil]?.length) {
    return byDapil[selectedDapil].map(name => `${name} - PKS ${selectedDapil}`);
  }

  return Object.entries(byDapil).flatMap(([dapil, names]) =>
    (names || []).map(name => `${name} - PKS ${dapil}`)
  );
}

function updateRecipientNameOptions() {
  const level = document.getElementById("ticketTargetLevel")?.value || "DPR RI";
  const selectedDapil = document.getElementById("ticketTargetDapil")?.value || "";
  const nameSelect = document.getElementById("ticketTargetName");
  if (!nameSelect) return;

  const directory = currentRecipientDirectory();
  const pksDprRiNames = level === "DPR RI" ? buildPksDprRiNames(directory, selectedDapil) : [];
  const fallbackNames = directory.names?.[level] || recipientDirectory.default.names[level];
  setSelectOptions(nameSelect, pksDprRiNames.length ? pksDprRiNames : fallbackNames, "Pilih Nama Tujuan");
}

function buildRecipientNames(level, dapils, province) {
  const suffix = province ? ` ${province}` : "";
  return dapils.length
    ? dapils.map(dapil => `Fraksi PKS ${level} - ${dapil}`)
    : [`${level}${suffix}`];
}

async function loadKpuDapilForProvince(province) {
  if (!province || kpuDapilCache[province]) return;
  try {
    const payload = await apiRequest(`/api/kpu/dapil?province=${encodeURIComponent(province)}`);
    const data = payload.data || {};
    const dprRi = data.dprRi || data.dpr_ri || [];
    const dprdProvinsi = data.dprdProvinsi || data.dprd_provinsi || [];
    kpuDapilCache[province] = {
      dprRi,
      dprdProvinsi,
      cities: data.cities || [],
      pksDprRi: data.pksDprRi || {},
      names: {
        "DPR RI": data.names?.["DPR RI"]?.length ? data.names["DPR RI"] : buildRecipientNames("DPR RI", dprRi, province),
        "DPRD Provinsi": data.names?.["DPRD Provinsi"]?.length ? data.names["DPRD Provinsi"] : buildRecipientNames("DPRD Provinsi", dprdProvinsi, province),
        "DPRD Kab/Kota": data.names?.["DPRD Kab/Kota"]?.length ? data.names["DPRD Kab/Kota"] : buildRecipientNames("DPRD Kab/Kota", data.cities || [], province)
      }
    };
  } catch (error) {
    console.error("KPU dapil data load failed", error);
  }
}

async function loadKpuTargetNameOptions() {
  if (kpuTargetNameOptions.length || !apiAvailable) return;
  try {
    const payload = await apiRequest("/api/kpu/dapil");
    const provincesData = payload.data?.provinces || {};
    kpuTargetNameOptions = [...new Set(Object.values(provincesData).flatMap(province =>
      Object.values(province.pksDprRi || {}).flat()
    ))].sort((a, b) => a.localeCompare(b, "id-ID"));
  } catch (error) {
    console.error("KPU target name options load failed", error);
  }
}

async function refreshRecipientFieldsFromKpu() {
  const province = document.getElementById("ticketRegion")?.value || "";
  await loadKpuDapilForProvince(province);
  updateRecipientFields();
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
  const method = (options.method || "GET").toUpperCase();
  const endpoint = method === "GET"
    ? `${path}${path.includes("?") ? "&" : "?"}_=${Date.now()}`
    : path;
  const response = await fetch(`${API_BASE}${endpoint}`, {
    headers,
    cache: "no-store",
    ...options
  });
  if (!response.ok) {
    throw new Error(`API ${response.status}: ${path}`);
  }
  return response.json();
}

function applyAuthState() {
  const loggedIn = Boolean(currentUser);
  const loginScreen = document.getElementById("loginScreen");
  const appShell = document.getElementById("appShell");

  loginScreen.classList.toggle("hidden", loggedIn);
  loginScreen.style.display = loggedIn ? "none" : "grid";
  appShell.classList.toggle("app-locked", !loggedIn);
  appShell.style.display = loggedIn ? "flex" : "none";

  document.getElementById("currentUserName").textContent = currentUser?.name || "-";
  const targetScope = currentUser?.targetName ? ` - ${currentUser.targetName}` : "";
  document.getElementById("currentUserRole").textContent = currentUser ? `${currentUser.role === "admin" ? "Admin" : "User"}${targetScope}` : "-";

  document.querySelectorAll(".nav-item").forEach(item => {
    const page = item.dataset.page;
    const permission = currentUser?.permissions?.[page];
    const hiddenByPermission = currentUser && currentUser.role !== "admin" && permission && permission.view === false;
    item.classList.toggle("nav-hidden", page === "settings" ? currentUser?.role !== "admin" : Boolean(hiddenByPermission));
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
  let payload;

  try {
    payload = await apiRequest("/api/auth/login", {
      method: "POST",
      body: JSON.stringify({
        email: document.getElementById("loginEmail").value,
        password: document.getElementById("loginPassword").value
      })
    });
  } catch (error) {
    console.error("Login request failed", error);
    toast("Login gagal. Periksa email dan password.");
    return;
  }

  authToken = payload.data.token;
  currentUser = payload.data.user;
  localStorage.setItem("spap-auth-token", authToken);
  currentPage = "dashboard";
  applyAuthState();
  setPage(currentPage);

  try {
    await loadData();
  } catch (error) {
    console.error("Dashboard load after login failed", error);
  }

  toast(`Selamat datang, ${currentUser.name}`);
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
  const createdAt = row.created_at || row.createdAt || row.tanggal || new Date().toISOString();
  return {
    id: row.public_id || row.id,
    tanggal: createdAt.slice(0, 10),
    nama: row.reporter_name || row.nama,
    kategori: row.category || row.kategori,
    prioritas: row.priority || row.prioritas,
    status: row.status,
    wilayah: row.region || row.wilayah,
    judul: row.subject || row.judul,
    deskripsi: row.description || row.deskripsi,
    kanal: row.channel || row.kanal || "API",
    pic: row.assigned_unit || row.pic || "Triage SPAP",
    lokasi: row.region || row.lokasi || row.wilayah,
    targetLevel: row.target_level || row.targetLevel || "",
    targetDapil: row.target_dapil || row.targetDapil || "",
    targetProvince: row.target_province || row.targetProvince || row.region || row.wilayah || "",
    targetCity: row.target_city || row.targetCity || "",
    targetName: row.target_name || row.targetName || "",
    createdAt,
    slaDueAt: row.sla_due_at || row.slaDueAt || null,
    resolvedAt: row.resolved_at || row.resolvedAt || null,
    updatedAt: row.updated_at || row.updatedAt || null
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
  const [ticketsPayload, osintPayload, notificationsPayload] = await Promise.all([
    apiRequest("/api/tickets"),
    apiRequest("/api/osint/mentions"),
    apiRequest("/api/notifications")
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
  state.notifications = notificationsPayload.data || [];

  apiAvailable = true;
  const currentTicketRegion = document.getElementById("ticketRegion")?.value;
  if (currentTicketRegion) {
    await loadKpuDapilForProvince(currentTicketRegion);
  }
  setConnectionStatus("API backend aktif", true);
  saveState();
}

async function loadAdminData() {
  if (currentUser?.role !== "admin" || !apiAvailable) {
    return;
  }
  try {
    await loadKpuTargetNameOptions();
    const [usersPayload, permissionsPayload] = await Promise.all([
      apiRequest("/api/admin/users"),
      apiRequest("/api/admin/menu-permissions")
    ]);
    adminData.users = usersPayload.data || [];
    adminData.permissions = permissionsPayload.data || [];
  } catch (error) {
    console.error("Admin data load failed", error);
  }
}

async function loadData() {
  try {
    await loadRemoteState();
    await loadAdminData();
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

function mergeTicketIntoState(item, type) {
  const collection = type === "aspirasi" ? state.aspirasi : state.pengaduan;
  const existingIndex = collection.findIndex(ticket => ticket.id === item.id);
  if (existingIndex >= 0) {
    collection[existingIndex] = { ...collection[existingIndex], ...item };
  } else {
    collection.unshift(item);
  }
}

function ticketTypeFromItem(item, fallback = "aspirasi") {
  return (item?.type || item?.tipe || fallback).toString().toLowerCase() === "pengaduan" ? "pengaduan" : "aspirasi";
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

function activeNotifications() {
  const selectedRegion = document.getElementById("regionFilter")?.value || "Semua";
  const byTicket = new Map();
  [...(state.notifications || []), ...localNotifications()].forEach(item => {
    byTicket.set(`${item.type}:${item.id}`, { ...(byTicket.get(`${item.type}:${item.id}`) || {}), ...item });
  });

  return [...byTicket.values()].filter(item => {
    const matchesRegion = selectedRegion === "Semua" || item.region === selectedRegion || item.description?.includes(selectedRegion);
    return matchesRegion && !acknowledgedNotifications.has(item.id);
  });
}

function dashboardTickets() {
  const category = document.getElementById("dashboardCategoryFilter")?.value || "Semua Kategori";
  const period = document.getElementById("periodFilter")?.value || "7 Hari Terakhir";
  const now = new Date();
  const periodDays = {
    "Hari Ini": 1,
    "7 Hari Terakhir": 7,
    "Bulan Ini": 31,
    "Triwulan Ini": 92
  }[period] || 7;

  const categoryTickets = scopedTickets().filter(ticket => category === "Semua Kategori" || ticket.kategori === category);
  const periodTickets = categoryTickets.filter(ticket => {
    const matchesCategory = category === "Semua Kategori" || ticket.kategori === category;
    const ticketDate = new Date(ticket.createdAt || ticket.tanggal || now);
    const ageDays = Math.floor((now - ticketDate) / 86400000);
    return matchesCategory && ageDays < periodDays;
  });
  return periodTickets.length ? periodTickets : categoryTickets;
}

function countBy(items, keyFn) {
  return items.reduce((acc, item) => {
    const key = keyFn(item) || "Tidak Terkategori";
    acc[key] = (acc[key] || 0) + 1;
    return acc;
  }, {});
}

function percent(value, total) {
  return total ? Math.round((value / total) * 100) : 0;
}

function renderMetrics() {
  const tickets = dashboardTickets();
  const total = tickets.length;
  const selesai = tickets.filter(t => t.status === "Selesai").length;
  const aktif = tickets.filter(t => t.status !== "Selesai").length;
  const kritis = tickets.filter(t => t.prioritas === "Kritis" || t.status === "Eskalasi").length;
  const whatsapp = tickets.filter(t => t.kanal === "WhatsApp").length;
  const resolutionRate = percent(selesai, total);
  const criticalRate = percent(kritis, total);
  const metrics = [
    ["Total Tiket", total.toLocaleString("id-ID"), `${aktif} masih aktif`],
    ["Rasio Selesai", `${resolutionRate}%`, `${selesai} tiket selesai`],
    ["Prioritas Kritis", kritis.toLocaleString("id-ID"), `${criticalRate}% dari total`],
    ["Kanal WhatsApp", whatsapp.toLocaleString("id-ID"), `${percent(whatsapp, total)}% tiket masuk`]
  ];
  const icons = ["T", "S", "K", "WA"];
  document.getElementById("metricGrid").innerHTML = metrics.map(([label, value, trend], index) => `
    <article class="metric dashboard-kpi"><div class="kpi-icon">${icons[index]}</div><strong>${value}</strong><span>${label}</span><em class="${label === "Prioritas Kritis" && kritis ? "down" : ""}">${trend}</em></article>
  `).join("");
}

function renderTrendChart() {
  const tickets = dashboardTickets();
  let days = Array.from({ length: 7 }, (_, index) => {
    const date = new Date();
    date.setDate(date.getDate() - (6 - index));
    const key = date.toISOString().slice(0, 10);
    return { key, label: date.toLocaleDateString("id-ID", { weekday: "short" }) };
  });
  let aspirasi = days.map(day => tickets.filter(ticket => ticket.tipe === "Aspirasi" && (ticket.createdAt || ticket.tanggal || "").slice(0, 10) === day.key).length);
  let pengaduan = days.map(day => tickets.filter(ticket => ticket.tipe === "Pengaduan" && (ticket.createdAt || ticket.tanggal || "").slice(0, 10) === day.key).length);
  if (tickets.length && [...aspirasi, ...pengaduan].every(value => value === 0)) {
    days = [...new Set(tickets.map(ticket => (ticket.createdAt || ticket.tanggal || "").slice(0, 10)).filter(Boolean))]
      .sort()
      .slice(-7)
      .map(key => ({ key, label: new Date(key).toLocaleDateString("id-ID", { day: "2-digit", month: "short" }) }));
    aspirasi = days.map(day => tickets.filter(ticket => ticket.tipe === "Aspirasi" && (ticket.createdAt || ticket.tanggal || "").slice(0, 10) === day.key).length);
    pengaduan = days.map(day => tickets.filter(ticket => ticket.tipe === "Pengaduan" && (ticket.createdAt || ticket.tanggal || "").slice(0, 10) === day.key).length);
  }
  const max = Math.max(5, ...aspirasi, ...pengaduan);
  const width = 900;
  const height = 250;
  const padding = 28;
  const xStep = (width - padding * 2) / Math.max(1, days.length - 1);
  const y = value => height - padding - (value / max) * (height - padding * 2);
  const x = index => padding + index * xStep;
  const points = values => values.map((value, index) => `${x(index)},${y(value)}`).join(" ");
  const circles = (values, className) => values.map((value, index) => `<circle class="${className}" cx="${x(index)}" cy="${y(value)}" r="4" />`).join("");
  const grid = [0, 20, 40, 60, 80, 100].map(value => {
    const chartValue = Math.round((value / 100) * max);
    const yy = y(chartValue);
    return `<line class="chart-grid" x1="${padding}" y1="${yy}" x2="${width - padding}" y2="${yy}" /><text class="chart-axis" x="4" y="${yy + 4}">${chartValue}</text>`;
  }).join("");
  const axis = days.map((day, index) => `<text class="chart-axis" x="${x(index) - 8}" y="${height - 6}">${day.label}</text>`).join("");

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
  const tickets = dashboardTickets();
  const counts = countBy(tickets, ticket => ticket.wilayah);

  document.getElementById("geoDistribution").innerHTML = Object.entries(counts)
    .sort((a, b) => b[1] - a[1])
    .slice(0, 8)
    .map(([province, total], index) => `<article class="geo-card tone-${index % 4}"><span>${province}</span><strong>${total}</strong></article>`)
    .join("") || '<div class="warning positif"><strong>Belum ada sebaran wilayah</strong><p>Data akan muncul setelah tiket masuk.</p></div>';
}

function renderCategoryDonut() {
  const tickets = dashboardTickets();
  const colors = ["#ff8a00", "#ff6537", "#0f766e", "#2563eb", "#eab308", "#7c3aed", "#64748b"];
  const total = Math.max(1, tickets.length);
  const categories = Object.entries(countBy(tickets, ticket => ticket.kategori))
    .sort((a, b) => b[1] - a[1])
    .slice(0, 7)
    .map(([label, count], index) => [label, percent(count, total), colors[index], count]);
  const top = categories[0];
  document.getElementById("categoryInsightTitle").textContent = top ? `${top[0]} paling dominan` : "Belum ada kategori dominan";
  document.getElementById("categoryInsightText").textContent = top
    ? `${top[3]} tiket (${top[1]}%) berada pada kategori ${top[0]}. Gunakan ini untuk mengatur prioritas PIC dan komunikasi wilayah.`
    : "Data kategori akan terbaca setelah pengaduan atau aspirasi masuk.";
  let offset = 0;
  const segments = categories.map(([, value, color]) => {
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
      ${categories.length ? categories.map(([label, value, color, count]) => `<span><i style="background:${color}"></i>${label} <strong>${count} / ${value}%</strong></span>`).join("") : '<span>Belum ada data kategori</span>'}
    </div>
  `;
}

function renderStatusDashboardChart() {
  const tickets = dashboardTickets();
  const total = Math.max(1, tickets.length);
  const statuses = ["Baru", "Diproses", "Eskalasi", "Selesai"];
  const colors = { Baru: "#2563eb", Diproses: "#38bdf8", Eskalasi: "#dc2626", Selesai: "#16a34a" };
  document.getElementById("statusDashboardChart").innerHTML = statuses.map(status => {
    const count = tickets.filter(ticket => ticket.status === status).length;
    return `
      <div class="measure-row">
        <div><strong>${status}</strong><span>${count} tiket</span></div>
        <div class="measure-track"><i style="width:${percent(count, total)}%; background:${colors[status]}"></i></div>
        <em>${percent(count, total)}%</em>
      </div>
    `;
  }).join("");
}

function renderChannelDashboardChart() {
  const tickets = dashboardTickets();
  const total = Math.max(1, tickets.length);
  const channels = Object.entries(countBy(tickets, ticket => ticket.kanal))
    .sort((a, b) => b[1] - a[1])
    .slice(0, 5);
  document.getElementById("channelDashboardChart").innerHTML = channels.length
    ? channels.map(([channel, count]) => `
      <div class="channel-row">
        <span>${channel}</span>
        <strong>${count}</strong>
        <small>${percent(count, total)}%</small>
      </div>
    `).join("")
    : '<div class="warning positif"><strong>Belum ada kanal masuk</strong><p>Kanal akan muncul setelah data masuk.</p></div>';
}

function renderDashboardActivities() {
  const items = scopedTickets()
    .sort((a, b) => `${b.tanggal} ${b.id}`.localeCompare(`${a.tanggal} ${a.id}`))
    .slice(0, 6)
    .map(item => {
      const isPengaduan = item.tipe === "Pengaduan";
      const statusText = item.status === "Selesai" ? "terselesaikan" : item.status === "Baru" ? "baru diterima" : item.status.toLowerCase();
      return [
        `${item.tipe} ${statusText}`,
        `${item.judul} - ${item.wilayah}`,
        item.tanggal,
        isPengaduan ? "P" : "A"
      ];
    });

  document.getElementById("dashboardActivityList").innerHTML = items.length
    ? items.map(([title, desc, time, icon]) => `
    <div class="timeline-item"><span class="timeline-icon">${icon}</span><div><strong>${title}</strong><p>${desc}</p></div><time>${time}</time></div>
  `).join("")
    : '<div class="warning positif"><strong>Belum ada aktivitas</strong><p>Data terbaru akan muncul setelah aspirasi atau pengaduan dibuat.</p></div>';
}

function renderDashboardAlerts() {
  const urgent = dashboardTickets().filter(ticket => ticket.prioritas === "Kritis" || ticket.status === "Eskalasi");
  const alerts = urgent.length
    ? urgent.slice(0, 3).map(ticket => [ticket.prioritas === "Kritis" ? "Prioritas Kritis" : "Perlu Eskalasi", `${ticket.judul} - ${ticket.wilayah}`])
    : [];

  document.getElementById("dashboardAlerts").innerHTML = alerts.length ? alerts.map(([title, desc]) => `
    <div class="dashboard-alert"><strong>${title}</strong><p>${desc}</p></div>
  `).join("") : '<div class="warning positif"><strong>Tidak ada alert berat</strong><p>Tiket dalam filter saat ini tidak memiliki eskalasi atau prioritas kritis.</p></div>';
}

function hoursUntil(dateValue) {
  if (!dateValue) return null;
  return Math.round((new Date(dateValue).getTime() - Date.now()) / 36e5);
}

function slaSeverityMeta(severity) {
  return {
    overdue: { label: "Terlambat", color: "#dc2626", weight: 100 },
    critical: { label: "Kritis", color: "#f97316", weight: 78 },
    escalation: { label: "Eskalasi", color: "#7c3aed", weight: 62 },
    new: { label: "Baru", color: "#16a34a", weight: 36 },
    info: { label: "Monitoring", color: "#64748b", weight: 24 },
  }[severity] || { label: "Monitoring", color: "#64748b", weight: 24 };
}

function localNotifications() {
  return scopedTickets()
    .filter(ticket => ticket.status !== "Selesai")
    .map(ticket => {
      const hours = hoursUntil(ticket.slaDueAt);
      let severity = "info";
      let title = "Perlu tindak lanjut";
      if (hours !== null && hours < 0) {
        severity = "overdue";
        title = "SLA terlewati";
      } else if (ticket.prioritas === "Kritis") {
        severity = "critical";
        title = "Tiket kritis aktif";
      } else if (ticket.status === "Eskalasi") {
        severity = "escalation";
        title = "Butuh eskalasi";
      } else if (ticket.status === "Baru") {
        severity = "new";
        title = `${ticket.tipe} baru diajukan`;
      }
      return {
        id: ticket.id,
        type: ticket.tipe === "Pengaduan" ? "pengaduan" : "aspirasi",
        severity,
        title,
        description: `${ticket.judul} - ${ticket.wilayah}`,
        region: ticket.wilayah,
        assignedUnit: ticket.pic,
        slaDueAt: ticket.slaDueAt,
        createdAt: ticket.createdAt || ticket.tanggal
      };
    })
    .slice(0, 8);
}

function renderSlaNotifications() {
  const notifications = activeNotifications().slice(0, 8);
  const target = document.getElementById("slaNotificationList");
  if (!target) return;

  if (!notifications.length) {
    target.innerHTML = '<div class="warning positif"><strong>SLA aman</strong><p>Tidak ada tiket kritis atau terlambat saat ini.</p></div>';
    return;
  }

  const total = notifications.length;
  const counts = countBy(notifications, item => slaSeverityMeta(item.severity).label);
  const riskScore = Math.round(notifications.reduce((sum, item) => sum + slaSeverityMeta(item.severity).weight, 0) / total);
  const summary = [
    ["Terlambat", counts.Terlambat || 0, "#dc2626"],
    ["Kritis", counts.Kritis || 0, "#f97316"],
    ["Eskalasi", counts.Eskalasi || 0, "#7c3aed"],
    ["Baru", counts.Baru || 0, "#16a34a"],
  ];

  target.innerHTML = `
    <div class="sla-graphic">
      <div class="sla-score">
        <svg viewBox="0 0 120 120" role="img" aria-label="Skor risiko SLA">
          <circle class="sla-score-track" cx="60" cy="60" r="48" pathLength="100"></circle>
          <circle class="sla-score-fill" cx="60" cy="60" r="48" pathLength="100" style="stroke-dasharray:${riskScore} ${100 - riskScore}"></circle>
        </svg>
        <div><strong>${riskScore}</strong><span>Skor Risiko</span></div>
      </div>
      <div class="sla-summary-grid">
        ${summary.map(([label, count, color]) => `
          <article>
            <i style="background:${color}"></i>
            <span>${label}</span>
            <strong>${count}</strong>
            <div class="sla-mini-track"><b style="width:${percent(count, total)}%; background:${color}"></b></div>
          </article>
        `).join("")}
      </div>
    </div>
    <div class="sla-identity-grid">
      ${notifications.map(item => {
      const hours = hoursUntil(item.slaDueAt);
      const eta = hours === null
        ? "SLA belum ditentukan"
        : hours < 0
          ? `Terlambat ${Math.abs(hours)} jam`
          : `Sisa ${hours} jam`;
      const meta = slaSeverityMeta(item.severity);
      return `
        <article class="sla-card ${item.severity}" style="--sla-color:${meta.color}">
          <div class="sla-card-head">
            <span>${item.id}</span>
            <strong>${meta.label}</strong>
          </div>
          <div class="sla-card-body">
            <small>${item.type}</small>
            <h4>${item.title}</h4>
            <p>${item.description}</p>
          </div>
          <div class="sla-card-meta">
            <span>PIC<br><strong>${item.assignedUnit || "Triage SPAP"}</strong></span>
            <span>Wilayah<br><strong>${item.region || "-"}</strong></span>
            <span>Waktu<br><strong>${eta}</strong></span>
          </div>
          <button class="btn ghost" onclick="openDetail('${item.type}','${item.id}')">Tindak Lanjut</button>
        </article>
      `;
    }).join("")}
    </div>
  `;
}

function renderNotificationCenter() {
  const notifications = activeNotifications();
  const count = document.getElementById("notificationCount");
  const list = document.getElementById("notificationCenterList");
  if (count) count.textContent = notifications.length;
  if (!list) return;

  list.innerHTML = notifications.length
    ? notifications.map(item => {
      const hours = hoursUntil(item.slaDueAt);
      const eta = hours === null
        ? "SLA belum ditentukan"
        : hours < 0
          ? `Terlambat ${Math.abs(hours)} jam`
          : `Sisa ${hours} jam`;
      return `
        <article class="notification-card ${item.severity}">
          <div>
            <span class="eyebrow">${item.id} - ${item.type}</span>
            <strong>${item.title}</strong>
            <p>${item.description}</p>
            <small>${item.assignedUnit || "Triage SPAP"} - ${eta}</small>
          </div>
          <div class="notification-actions">
            <button class="btn ghost" onclick="openDetail('${item.type}','${item.id}')">Detail</button>
            <button class="btn primary" onclick="acknowledgeNotification('${item.id}', '${item.type}')">Tandai Ditindaklanjuti</button>
          </div>
        </article>
      `;
    }).join("")
    : '<div class="warning positif"><strong>Tidak ada notifikasi aktif</strong><p>Semua notifikasi sudah ditindaklanjuti.</p></div>';
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
  renderStatusDashboardChart();
  renderChannelDashboardChart();
  renderDashboardActivities();
  renderDashboardAlerts();
  renderSlaNotifications();
  renderNotificationCenter();
  renderPipeline();
  renderCategoryBars();
  renderWarnings();
  renderActivities();
}

function selectedRegionFilter() {
  return document.getElementById("regionFilter")?.value || "Semua";
}

function ticketMatchesRegion(item, region) {
  if (region === "Semua") return true;
  return item.wilayah === region
    || item.targetProvince === region
    || item.lokasi === region;
}

function ticketMatches(item, search, status, priority, region = selectedRegionFilter()) {
  const haystack = `${item.id} ${item.nama} ${item.judul} ${item.kategori} ${item.wilayah} ${item.deskripsi}`.toLowerCase();
  return (!search || haystack.includes(search.toLowerCase()))
    && (status === "Semua" || item.status === status)
    && (priority === "Semua" || item.prioritas === priority)
    && ticketMatchesRegion(item, region);
}

function renderAspirasi() {
  const search = document.getElementById("aspirasiSearch").value;
  const status = document.getElementById("aspirasiStatus").value;
  const priority = document.getElementById("aspirasiPriority").value;
  const items = state.aspirasi.filter(item => ticketMatches(item, search, status, priority));
  document.getElementById("aspirasiTable").innerHTML = items.length
    ? items
    .map(item => `
      <tr>
        <td>${item.id}</td><td>${item.nama}</td><td><strong>${item.judul}</strong><br><small>${item.kanal}</small></td>
        <td>${item.kategori}</td><td>${item.wilayah}</td><td>${badge(item.prioritas)}</td><td>${badge(item.status)}</td>
        <td>
          <div class="table-actions">
            <button class="btn ghost" onclick="openDetail('aspirasi','${item.id}')">Detail</button>
            ${item.status === "Baru" ? `<button class="btn" onclick="updateTicketStatus('aspirasi','${item.id}','Diproses')">Proses</button>` : ""}
            ${item.status !== "Selesai" ? `<button class="btn primary" onclick="updateTicketStatus('aspirasi','${item.id}','Selesai')">Selesai</button>` : ""}
          </div>
        </td>
      </tr>
    `).join("")
    : '<tr><td colspan="8">Tidak ada aspirasi untuk filter wilayah/status/prioritas ini.</td></tr>';
}

function renderPengaduan() {
  const search = document.getElementById("pengaduanSearch").value;
  const status = document.getElementById("pengaduanStatus").value;
  const priority = document.getElementById("pengaduanPriority").value;
  const items = state.pengaduan.filter(item => ticketMatches(item, search, status, priority));
  document.getElementById("pengaduanGrid").innerHTML = items.length
    ? items
    .map(item => `
      <article class="ticket-card">
        <div class="ticket-meta"><span>${item.id}</span><span>${item.tanggal}</span></div>
        <h3>${item.judul}</h3>
        <p>${item.deskripsi}</p>
        <div class="ticket-meta"><span>${item.lokasi}</span><span>${item.pic}</span></div>
        <div class="card-actions">${badge(item.prioritas)} ${badge(item.status)} <button class="btn ghost" onclick="openDetail('pengaduan','${item.id}')">Detail</button></div>
      </article>
    `).join("")
    : '<div class="warning positif"><strong>Data tidak ditemukan</strong><p>Tidak ada pengaduan untuk filter wilayah/status/prioritas ini.</p></div>';
}

function renderDetailContent(type, item) {
  const events = ticketEvents[item.id] || [];
  const targetName = item.targetName || item.target_name || "-";
  const targetLevel = item.targetLevel || item.target_level || "-";
  const targetDapil = item.targetDapil || item.target_dapil || "-";
  const targetArea = [item.targetProvince, item.targetCity].filter(Boolean).join(" - ") || "-";
  const eventRows = events.length
    ? events.map(event => `
      <div class="event-item">
        <strong>${event.actor_name || "Sistem"}</strong>
        <span>${event.note || event.event_type}</span>
        <time>${new Date(event.created_at).toLocaleString("id-ID")}</time>
      </div>
    `).join("")
    : '<div class="event-empty">Belum ada riwayat tindak lanjut.</div>';

  return `
    <div class="detail-grid">
      <div class="detail-box"><strong>Pelapor</strong><br>${item.nama}</div>
      <div class="detail-box"><strong>Wilayah</strong><br>${item.wilayah}</div>
      <div class="detail-box"><strong>Kategori</strong><br>${item.kategori}</div>
      <div class="detail-box"><strong>PIC</strong><br>${item.pic}</div>
      <div class="detail-box"><strong>Prioritas</strong><br>${badge(item.prioritas)}</div>
      <div class="detail-box"><strong>Status</strong><br>${badge(item.status)}</div>
      <div class="detail-box target-recipient"><strong>Ditujukan Kepada</strong><br><span>${targetName}</span><br><small>${targetLevel}</small></div>
      <div class="detail-box"><strong>Wilayah Tujuan</strong><br>${targetDapil}<br><small>${targetArea}</small></div>
    </div>
    <h4>${item.judul}</h4>
    <p>${item.deskripsi}</p>
    <div class="card-actions">
      <button class="btn" onclick="updateTicketStatus('${type}','${item.id}','Diproses')">Proses</button>
      <button class="btn" onclick="updateTicketStatus('${type}','${item.id}','Eskalasi')">Eskalasi</button>
      <button class="btn primary" onclick="updateTicketStatus('${type}','${item.id}','Selesai')">Selesaikan</button>
    </div>
    <div class="event-panel">
      <div class="panel-head compact">
        <div>
          <h3>Riwayat Tindak Lanjut</h3>
          <p>Catatan audit dan perubahan status tiket</p>
        </div>
      </div>
      <div id="ticketEventList" class="event-list">${eventRows}</div>
      <div class="note-compose">
        <textarea id="ticketNoteInput" rows="3" placeholder="Tulis catatan tindak lanjut..."></textarea>
        <button class="btn primary" onclick="addTicketNote('${type}','${item.id}')">Tambah Catatan</button>
      </div>
    </div>
  `;
}

async function loadTicketEvents(type, id) {
  if (!apiAvailable) return;
  try {
    const payload = await apiRequest(`/api/tickets/${id}/events`);
    ticketEvents[id] = payload.data || [];
    const collection = type === "aspirasi" ? state.aspirasi : state.pengaduan;
    const item = collection.find(ticket => ticket.id === id);
    if (item && document.getElementById("detailDialog").open) {
      document.getElementById("detailContent").innerHTML = renderDetailContent(type, item);
    }
  } catch (error) {
    console.error("Ticket events load failed", error);
  }
}

function openDetail(type, id) {
  const collection = type === "aspirasi" ? state.aspirasi : state.pengaduan;
  const item = collection.find(ticket => ticket.id === id);
  if (!item) return;
  document.getElementById("detailTitle").textContent = `${type === "aspirasi" ? "Aspirasi" : "Pengaduan"} ${item.id}`;
  document.getElementById("detailContent").innerHTML = renderDetailContent(type, item);
  const detailDialog = document.getElementById("detailDialog");
  if (!detailDialog.open) detailDialog.showModal();
  loadTicketEvents(type, id);
}

async function updateTicketStatus(type, id, status) {
  const collection = type === "aspirasi" ? state.aspirasi : state.pengaduan;
  const item = collection.find(ticket => ticket.id === id);
  if (!item) return;

  if (apiAvailable) {
    try {
      const payload = await apiRequest(`/api/tickets/${id}/status`, {
        method: "PATCH",
        body: JSON.stringify({ status, note: `Status diubah melalui UI ke ${status}` })
      });
      Object.assign(item, normalizeTicket(payload.data || item), { status });
      await loadData();
      await loadTicketEvents(type, id);
    } catch (error) {
      apiAvailable = false;
      setConnectionStatus("Mode lokal aktif", false);
      toast("API tidak merespon, perubahan disimpan lokal");
    }
  }

  item.status = status;
  mergeTicketIntoState(item, type);
  saveState();
  renderAll();
  openDetail(type, id);
  toast(`Status ${id} diubah ke ${status}`);
}

async function addTicketNote(type, id) {
  const noteInput = document.getElementById("ticketNoteInput");
  const note = noteInput?.value.trim();
  if (!note) {
    toast("Catatan tindak lanjut belum diisi");
    return;
  }
  if (!apiAvailable) {
    ticketEvents[id] = [
      { actor_name: currentUser?.name || "Operator SPAP", note, event_type: "note_added", created_at: new Date().toISOString() },
      ...(ticketEvents[id] || [])
    ];
    noteInput.value = "";
    openDetail(type, id);
    toast("Catatan disimpan lokal");
    return;
  }

  try {
    await apiRequest(`/api/tickets/${id}/events`, {
      method: "POST",
      body: JSON.stringify({ note, actorName: currentUser?.name || "Operator SPAP" })
    });
    noteInput.value = "";
    await loadTicketEvents(type, id);
    toast("Catatan tindak lanjut ditambahkan");
  } catch (error) {
    toast("Catatan gagal disimpan");
  }
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
  const overdue = localNotifications().filter(item => item.severity === "overdue").length;
  const critical = tickets.filter(item => item.prioritas === "Kritis" && item.status !== "Selesai").length;
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
    <p><strong>SLA terlewati:</strong> ${overdue}</p>
    <p><strong>Tiket kritis aktif:</strong> ${critical}</p>
    <p><strong>Kategori dominan:</strong> ${topCategory}</p>
    <p><strong>Isu OSINT utama:</strong> ${(state.osint.topics[0]?.tag || "-")} dengan ${(state.osint.topics[0]?.mentions || 0).toLocaleString("id-ID")} mentions.</p>
    <p><strong>Rekomendasi:</strong> Perkuat triage tiket prioritas, siapkan respon isu publik, dan kirim ringkasan ke struktur wilayah terkait.</p>
  `;
}

function renderSettings() {
  const userRows = document.getElementById("userManagementRows");
  const permissionRows = document.getElementById("permissionRows");
  const complaintBox = document.getElementById("complaintManagement");
  if (!userRows || !permissionRows || !complaintBox) return;

  populateUserTargetNameOptions();

  userRows.innerHTML = adminData.users.length
    ? adminData.users.map(user => {
      const targetName = user.target_name || user.targetName || "";
      const regionScope = user.region_scope || user.regionScope || "";
      return `
      <tr>
        <td><strong>${user.name}</strong></td>
        <td>${user.email}</td>
        <td><span class="role-pill">${roleLabels[user.role] || user.role}</span></td>
        <td>${user.organization_unit || "-"}</td>
        <td>
          <select class="compact-select" id="region-${user.id}">
            <option value="">Semua Wilayah</option>
            ${provinces.map(province => `<option value="${province}" ${regionScope === province ? "selected" : ""}>${province}</option>`).join("")}
          </select>
          <button class="btn ghost compact-btn" onclick="updateUserRegionScope('${user.id}')">Simpan</button>
        </td>
        <td>
          <input class="compact-input" id="target-${user.id}" value="${escapeHtml(targetName)}" placeholder="Nama yang dituju">
          <button class="btn ghost compact-btn" onclick="updateUserTargetName('${user.id}')">Simpan</button>
        </td>
        <td>
          <select class="compact-select" onchange="updateUserStatus('${user.id}', this.value)">
            <option value="active" ${user.status === "active" ? "selected" : ""}>Aktif</option>
            <option value="inactive" ${user.status !== "active" ? "selected" : ""}>Nonaktif</option>
          </select>
          <button class="btn ghost compact-btn" onclick="resetUserPassword('${user.id}', '${user.email}')">Reset</button>
        </td>
      </tr>
    `;
    }).join("")
    : '<tr><td colspan="7">Data user akan tampil setelah API admin aktif.</td></tr>';

  const permissionMap = new Map(adminData.permissions.map(item => [`${item.role}:${item.menu_key}`, item]));
  permissionRows.innerHTML = manageableRoles.flatMap(role => manageableMenus.map(menu => {
    const item = permissionMap.get(`${role}:${menu}`) || {};
    const adminLock = role === "admin";
    return `
      <tr data-role="${role}" data-menu="${menu}">
        <td>${roleLabels[role]}</td>
        <td>${menuLabels[menu]}</td>
        <td><input type="checkbox" data-perm="canView" ${adminLock || item.can_view ? "checked" : ""} ${adminLock ? "disabled" : ""}></td>
        <td><input type="checkbox" data-perm="canCreate" ${adminLock || item.can_create ? "checked" : ""} ${adminLock ? "disabled" : ""}></td>
        <td><input type="checkbox" data-perm="canUpdate" ${adminLock || item.can_update ? "checked" : ""} ${adminLock ? "disabled" : ""}></td>
        <td><input type="checkbox" data-perm="canDelete" ${adminLock || item.can_delete ? "checked" : ""} ${adminLock ? "disabled" : ""}></td>
      </tr>
    `;
  })).join("");

  const activeComplaints = state.pengaduan.filter(item => item.status !== "Selesai");
  complaintBox.innerHTML = activeComplaints.length
    ? activeComplaints.map(item => `
      <article class="management-card">
        <div>
          <span class="eyebrow">${item.id} - ${item.wilayah}</span>
          <h4>${item.judul}</h4>
          <p>${item.deskripsi}</p>
          <small>PIC saat ini: ${item.pic || "Triage SPAP"}</small>
        </div>
        <div class="management-actions">
          ${badge(item.prioritas)}
          ${badge(item.status)}
          <select id="assign-${item.id}">
            <option value="Triage SPAP" ${item.pic === "Triage SPAP" ? "selected" : ""}>Triage SPAP</option>
            <option value="Unit Pengaduan" ${item.pic === "Unit Pengaduan" ? "selected" : ""}>Unit Pengaduan</option>
            <option value="Tim Advokasi Hukum" ${item.pic === "Tim Advokasi Hukum" ? "selected" : ""}>Tim Advokasi Hukum</option>
            <option value="Koordinator Wilayah" ${item.pic === "Koordinator Wilayah" ? "selected" : ""}>Koordinator Wilayah</option>
          </select>
          <button class="btn" onclick="assignComplaint('${item.id}', 'Diproses')">Disposisi</button>
          <button class="btn" onclick="assignComplaint('${item.id}', 'Eskalasi')">Eskalasi</button>
          <button class="btn primary" onclick="assignComplaint('${item.id}', 'Selesai')">Selesai</button>
        </div>
      </article>
    `).join("")
    : '<div class="warning positif"><strong>Semua pengaduan selesai</strong><p>Tidak ada pengaduan aktif yang perlu ditindaklanjuti.</p></div>';
}

function collectUserTargetNameOptions() {
  const ticketNames = [...state.aspirasi, ...state.pengaduan].map(item => item.targetName).filter(Boolean);
  const userNames = adminData.users.map(user => user.target_name || user.targetName).filter(Boolean);
  return [...new Set([...kpuTargetNameOptions, ...ticketNames, ...userNames])].sort((a, b) => a.localeCompare(b, "id-ID"));
}

function populateUserTargetNameOptions() {
  const datalist = document.getElementById("userTargetNameOptions");
  if (!datalist) return;
  datalist.innerHTML = collectUserTargetNameOptions()
    .map(name => `<option value="${escapeHtml(name)}"></option>`)
    .join("");
}

function renderAll() {
  renderDashboard();
  renderAspirasi();
  renderPengaduan();
  renderOsint();
  renderWorkflow();
  renderReport();
  renderSettings();
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
  button.textContent = "Tambah Pengaduan";
}

async function addTicket(event) {
  event.preventDefault();
  const type = document.getElementById("ticketType").value;
  const email = document.getElementById("ticketEmail").value;
  const phone = document.getElementById("ticketPhone").value;
  const region = document.getElementById("ticketRegion").value;
  const category = document.getElementById("ticketCategory").value;
  const priority = document.getElementById("ticketPriority").value;
  const targetLevel = document.getElementById("ticketTargetLevel").value;
  const targetDapil = document.getElementById("ticketTargetDapil").value;
  const targetProvince = document.getElementById("ticketTargetProvince").value;
  const targetCity = document.getElementById("ticketTargetCity").value;
  const targetName = document.getElementById("ticketTargetName").value;
  if (!region || !category || !priority) {
    toast("Lengkapi wilayah, kategori, dan prioritas");
    return;
  }
  if (type === "pengaduan" && (!targetLevel || !targetDapil || !targetName)) {
    toast("Lengkapi tujuan pengaduan, dapil, dan nama tujuan");
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
    targetLevel,
    targetDapil,
    targetProvince,
    targetCity,
    targetName,
    createdAt: new Date().toISOString(),
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
          assignedUnit: item.pic,
          targetLevel,
          targetDapil,
          targetProvince,
          targetCity,
          targetName
        })
      });
      Object.assign(item, normalizeTicket(payload.data));
    } catch (error) {
      apiAvailable = false;
      setConnectionStatus("Mode lokal aktif", false);
      toast("API tidak merespon, tiket disimpan lokal");
    }
  }

  mergeTicketIntoState(item, type);
  saveState();
  if (apiAvailable) {
    await loadData();
    mergeTicketIntoState(item, type);
    saveState();
  }
  document.getElementById("ticketDialog").close();
  document.getElementById("ticketForm").reset();
  renderAll();
  setPage(type);
  toast(`${item.id} berhasil dibuat`);
}

function normalizeWhatsappPhone(phone) {
  const digits = phone.replace(/\D/g, "");
  if (!digits) return "";
  if (digits.startsWith("62")) return digits;
  if (digits.startsWith("0")) return `62${digits.slice(1)}`;
  return digits;
}

function whatsappTemplateMessage() {
  const name = document.getElementById("waReporterName")?.value || "[Nama]";
  const phone = document.getElementById("waReporterPhone")?.value || "[No. WhatsApp]";
  const region = document.getElementById("waRegion")?.value || "[Wilayah]";
  const category = document.getElementById("waCategory")?.value || "[Kategori]";
  const priority = document.getElementById("waPriority")?.value || "Sedang";
  const adminScope = document.getElementById("waAdminScope")?.value === "pusat" ? "Admin Pusat" : "Admin Wilayah";
  const subject = document.getElementById("waSubject")?.value || "[Judul pengaduan]";
  const message = document.getElementById("waMessage")?.value || "[Tulis kronologi singkat]";
  return [
    "[FORMAT PENGADUAN SPAP]",
    "Assalamu'alaikum, saya ingin menyampaikan pengaduan melalui SPAP.",
    `Nama: ${name}`,
    `No. WhatsApp: ${phone}`,
    `Wilayah: ${region}`,
    `Tujuan: ${adminScope}`,
    `Kategori: ${category}`,
    `Prioritas: ${priority}`,
    `Judul: ${subject}`,
    `Kronologi: ${message}`,
  ].join("\n");
}

function whatsappAssignedUnit(region, scope) {
  return scope === "pusat" ? "Admin Pusat SPAP" : `Admin Wilayah - ${region || "Belum ditentukan"}`;
}

function updateWhatsappPreview() {
  const preview = document.getElementById("waTemplatePreview");
  if (preview) preview.value = whatsappTemplateMessage();
}

async function copyWhatsappFormat() {
  const text = whatsappTemplateMessage();
  try {
    await navigator.clipboard.writeText(text);
    toast("Format WhatsApp disalin");
  } catch (error) {
    const preview = document.getElementById("waTemplatePreview");
    preview?.select();
    document.execCommand("copy");
    toast("Format WhatsApp disalin");
  }
}

function openWhatsappComposer() {
  const phone = normalizeWhatsappPhone(document.getElementById("waReporterPhone")?.value || "");
  if (!phone) {
    toast("Isi nomor WhatsApp terlebih dahulu");
    return;
  }
  const url = `https://wa.me/${phone}?text=${encodeURIComponent(whatsappTemplateMessage())}`;
  window.open(url, "_blank", "noopener");
}

async function submitWhatsappComplaint(event) {
  event.preventDefault();
  const phone = normalizeWhatsappPhone(document.getElementById("waReporterPhone").value);
  const adminScope = document.getElementById("waAdminScope").value;
  const assignedUnit = whatsappAssignedUnit(document.getElementById("waRegion").value, adminScope);
  const item = {
    id: `PEN-2026-${String(state.pengaduan.length + 1).padStart(3, "0")}`,
    tanggal: new Date().toISOString().slice(0, 10),
    nama: document.getElementById("waReporterName").value,
    wilayah: document.getElementById("waRegion").value,
    kategori: document.getElementById("waCategory").value,
    prioritas: document.getElementById("waPriority").value,
    status: "Baru",
    judul: document.getElementById("waSubject").value,
    deskripsi: document.getElementById("waMessage").value,
    kanal: "WhatsApp",
    pic: assignedUnit,
    lokasi: document.getElementById("waRegion").value,
    targetLevel: adminScope === "pusat" ? "Admin Pusat" : "Admin Wilayah",
    targetProvince: document.getElementById("waRegion").value,
    targetName: assignedUnit,
    email: "",
    phone,
    createdAt: new Date().toISOString()
  };

  if (apiAvailable) {
    try {
      const payload = await apiRequest("/api/tickets", {
        method: "POST",
        body: JSON.stringify({
          type: "pengaduan",
          reporterName: item.nama,
          reporterContact: phone,
          channel: "WhatsApp",
          region: item.wilayah,
          category: item.kategori,
          priority: item.prioritas,
          subject: item.judul,
          description: item.deskripsi,
          assignedUnit,
          targetLevel: item.targetLevel,
          targetProvince: item.targetProvince,
          targetName: item.targetName
        })
      });
      Object.assign(item, normalizeTicket(payload.data));
    } catch (error) {
      apiAvailable = false;
      setConnectionStatus("Mode lokal aktif", false);
      toast("API tidak merespon, pengaduan WhatsApp disimpan lokal");
    }
  }

  mergeTicketIntoState(item, "pengaduan");
  saveState();
  if (apiAvailable) {
    await loadData();
    mergeTicketIntoState(item, "pengaduan");
    saveState();
  }
  document.getElementById("whatsappComplaintForm").reset();
  document.getElementById("waPriority").value = "Sedang";
  document.getElementById("waAdminScope").value = "wilayah";
  updateWhatsappPreview();
  renderAll();
  setPage("pengaduan");
  toast(`${item.id} dicatat dari WhatsApp`);
}

function closeTicketDialog() {
  const dialog = document.getElementById("ticketDialog");
  document.getElementById("ticketForm").reset();
  if (dialog.open) dialog.close();
}

function openTicketDialog() {
  const type = "pengaduan";
  document.getElementById("ticketType").value = type;
  updateRecipientFields();
  document.getElementById("dialogTitle").innerHTML = type === "pengaduan"
    ? '<span class="modal-title-icon">+</span> Tambah Pengaduan Baru'
    : '<span class="modal-title-icon">+</span> Tambah Aspirasi Baru';
  document.getElementById("saveTicketBtn").textContent = type === "pengaduan" ? "Simpan Pengaduan" : "Simpan Aspirasi";
  document.getElementById("ticketDialog").showModal();
}

async function assignComplaint(id, status) {
  const item = state.pengaduan.find(ticket => ticket.id === id);
  const select = document.getElementById(`assign-${id}`);
  const assignedUnit = select?.value || item?.pic || "Unit Pengaduan";
  if (!item) return;

  if (apiAvailable) {
    try {
      const payload = await apiRequest(`/api/tickets/${id}/status`, {
        method: "PATCH",
        body: JSON.stringify({
          status,
          assignedUnit,
          targetLevel: item.targetLevel,
          targetDapil: item.targetDapil,
          targetProvince: item.targetProvince,
          targetCity: item.targetCity,
          targetName: item.targetName,
          note: `Pengaduan didisposisikan ke ${assignedUnit} dengan status ${status}`,
          actorName: currentUser?.name || "Admin SPAP"
        })
      });
      Object.assign(item, normalizeTicket(payload.data || item), { status, pic: assignedUnit });
      await loadData();
    } catch (error) {
      toast("API tidak merespon, perubahan disimpan lokal");
    }
  }

  item.status = status;
  item.pic = assignedUnit;
  mergeTicketIntoState(item, "pengaduan");
  saveState();
  renderAll();
  toast(`Pengaduan ${id} diperbarui`);
}

async function acknowledgeNotification(id, type) {
  const note = "Notifikasi SLA/eskalasi sudah ditindaklanjuti dari pusat notifikasi";
  if (apiAvailable) {
    try {
      const payload = await apiRequest(`/api/notifications/${id}/ack`, {
        method: "POST",
        body: JSON.stringify({
          note,
          status: "Diproses",
          actorName: currentUser?.name || "Operator SPAP"
        })
      });
      if (payload?.data) {
        const updated = normalizeTicket(payload.data);
        mergeTicketIntoState(updated, ticketTypeFromItem(payload.data, type));
      }
      await loadTicketEvents(type, id);
    } catch (error) {
      toast("Notifikasi disimpan lokal karena API tidak merespon");
    }
  }

  acknowledgedNotifications.add(id);
  localStorage.setItem("spap-ack-notifications", JSON.stringify([...acknowledgedNotifications]));

  const collection = type === "aspirasi" ? state.aspirasi : state.pengaduan;
  const item = collection.find(ticket => ticket.id === id);
  if (item && item.status === "Baru") {
    item.status = "Diproses";
    mergeTicketIntoState(item, type);
  }

  saveState();
  renderAll();
  toast(`Notifikasi ${id} ditandai ditindaklanjuti`);
}

async function createUser(event) {
  event.preventDefault();
  if (!apiAvailable || currentUser?.role !== "admin") {
    toast("Manajemen user membutuhkan akses admin dan API aktif");
    return;
  }

  const payload = await apiRequest("/api/admin/users", {
    method: "POST",
    body: JSON.stringify({
      name: document.getElementById("userName").value,
      email: document.getElementById("userEmail").value,
      role: document.getElementById("userRole").value,
      organizationUnit: document.getElementById("userUnit").value,
      targetName: document.getElementById("userTargetName").value,
      regionScope: document.getElementById("userRegionScope").value,
      password: document.getElementById("userPassword").value,
      status: document.getElementById("userStatus").value
    })
  });
  adminData.users = [payload.data, ...adminData.users.filter(user => user.email !== payload.data.email)];
  document.getElementById("userForm").reset();
  document.getElementById("userPassword").value = "user123";
  document.getElementById("userRegionScope").value = "";
  renderSettings();
  toast("User berhasil disimpan");
}

async function updateUserStatus(id, status) {
  if (!apiAvailable || currentUser?.role !== "admin") return;
  const payload = await apiRequest(`/api/admin/users/${id}`, {
    method: "PATCH",
    body: JSON.stringify({ status })
  });
  adminData.users = adminData.users.map(user => user.id === id ? payload.data : user);
  renderSettings();
  toast("Status user diperbarui");
}

async function updateUserTargetName(id) {
  if (!apiAvailable || currentUser?.role !== "admin") return;
  const targetName = document.getElementById(`target-${id}`)?.value || "";
  const payload = await apiRequest(`/api/admin/users/${id}`, {
    method: "PATCH",
    body: JSON.stringify({ targetName })
  });
  adminData.users = adminData.users.map(user => user.id === id ? payload.data : user);
  renderSettings();
  toast("Nama tujuan user diperbarui. User perlu login ulang agar filter aktif.");
}

async function updateUserRegionScope(id) {
  if (!apiAvailable || currentUser?.role !== "admin") return;
  const regionScope = document.getElementById(`region-${id}`)?.value || "";
  const payload = await apiRequest(`/api/admin/users/${id}`, {
    method: "PATCH",
    body: JSON.stringify({ regionScope })
  });
  adminData.users = adminData.users.map(user => user.id === id ? payload.data : user);
  renderSettings();
  toast("Wilayah akses user diperbarui. User perlu login ulang agar filter aktif.");
}

async function resetUserPassword(id, email) {
  if (!apiAvailable || currentUser?.role !== "admin") return;
  const password = prompt(`Password baru untuk ${email}`, "user123");
  if (!password) return;

  await apiRequest(`/api/admin/users/${id}/password`, {
    method: "PATCH",
    body: JSON.stringify({ password })
  });
  toast("Password user diperbarui");
}

async function savePermissions() {
  if (!apiAvailable || currentUser?.role !== "admin") {
    toast("Hanya admin yang dapat menyimpan akses menu");
    return;
  }

  const permissions = [...document.querySelectorAll("#permissionRows tr")].map(row => ({
    role: row.dataset.role,
    menuKey: row.dataset.menu,
    canView: row.querySelector('[data-perm="canView"]').checked,
    canCreate: row.querySelector('[data-perm="canCreate"]').checked,
    canUpdate: row.querySelector('[data-perm="canUpdate"]').checked,
    canDelete: row.querySelector('[data-perm="canDelete"]').checked
  }));

  await apiRequest("/api/admin/menu-permissions", {
    method: "POST",
    body: JSON.stringify({ permissions })
  });
  adminData.permissions = permissions.map(item => ({
    role: item.role,
    menu_key: item.menuKey,
    can_view: item.canView,
    can_create: item.canCreate,
    can_update: item.canUpdate,
    can_delete: item.canDelete
  }));
  toast("Hak akses menu tersimpan");
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
  document.getElementById("ticketRegion").addEventListener("change", refreshRecipientFieldsFromKpu);
  document.getElementById("ticketTargetLevel").addEventListener("change", updateRecipientFields);
  document.getElementById("ticketTargetDapil").addEventListener("change", updateRecipientNameOptions);
  document.getElementById("newTicketBtn").addEventListener("click", openTicketDialog);
  document.getElementById("ticketForm").addEventListener("submit", addTicket);
  document.getElementById("whatsappComplaintForm").addEventListener("submit", submitWhatsappComplaint);
  document.getElementById("openWhatsappBtn").addEventListener("click", openWhatsappComposer);
  document.getElementById("copyWhatsappFormatBtn").addEventListener("click", copyWhatsappFormat);
  ["waReporterName", "waReporterPhone", "waRegion", "waAdminScope", "waCategory", "waPriority", "waSubject", "waMessage"].forEach(id => {
    document.getElementById(id).addEventListener("input", updateWhatsappPreview);
    document.getElementById(id).addEventListener("change", updateWhatsappPreview);
  });
  document.getElementById("closeTicketBtn").addEventListener("click", closeTicketDialog);
  document.getElementById("cancelTicketBtn").addEventListener("click", closeTicketDialog);
  document.getElementById("ticketDialog").addEventListener("click", event => {
    if (event.target.id === "ticketDialog") closeTicketDialog();
  });
  document.getElementById("closeDetailBtn").addEventListener("click", () => document.getElementById("detailDialog").close());
  document.getElementById("notificationBtn").addEventListener("click", () => document.getElementById("notificationDialog").showModal());
  document.getElementById("closeNotificationBtn").addEventListener("click", () => document.getElementById("notificationDialog").close());
  document.getElementById("refreshBtn").addEventListener("click", async () => { await loadData(); toast("Data dashboard diperbarui"); });
  document.getElementById("refreshDashboardBtn").addEventListener("click", async () => { await loadData(); toast("Data dashboard diperbarui"); });
  ["periodFilter", "dashboardCategoryFilter"].forEach(id => document.getElementById(id).addEventListener("change", renderDashboard));
  ["aspirasiSearch", "aspirasiStatus", "aspirasiPriority"].forEach(id => document.getElementById(id).addEventListener("input", renderAspirasi));
  ["pengaduanSearch", "pengaduanStatus", "pengaduanPriority"].forEach(id => document.getElementById(id).addEventListener("input", renderPengaduan));
  ["reportType", "reportPeriod", "reportRegion", "reportFormat"].forEach(id => document.getElementById(id).addEventListener("change", renderReport));
  document.getElementById("reportForm").addEventListener("submit", event => { event.preventDefault(); renderReport(); toast("Preview laporan dibuat"); });
  document.getElementById("userForm").addEventListener("submit", createUser);
  document.getElementById("savePermissionBtn").addEventListener("click", savePermissions);
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
updateWhatsappPreview();
restoreSession().then(loadData);
setPage(currentPage);
