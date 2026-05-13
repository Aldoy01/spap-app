<?php
declare(strict_types=1);

header('Content-Type: application/json; charset=utf-8');
header('Access-Control-Allow-Origin: ' . getenv_value('CORS_ORIGIN', '*'));
header('Access-Control-Allow-Methods: GET, POST, PATCH, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type, Authorization');

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(204);
    exit;
}

try {
    route_request();
} catch (Throwable $error) {
    error_log($error->getMessage());
    json_response(['error' => 'Internal server error'], 500);
}

function route_request(): void
{
    $method = $_SERVER['REQUEST_METHOD'];
    $path = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH) ?: '/';

    ensure_schema();

    if ($method === 'GET' && $path === '/health') {
        health();
        return;
    }

    if ($method === 'POST' && $path === '/api/auth/login') {
        login();
        return;
    }

    if ($method === 'GET' && $path === '/api/auth/me') {
        current_user();
        return;
    }

    if ($method === 'POST' && $path === '/api/auth/logout') {
        logout();
        return;
    }

    if ($method === 'GET' && $path === '/api/admin/users') {
        list_users();
        return;
    }

    if ($method === 'POST' && $path === '/api/admin/users') {
        create_user();
        return;
    }

    if ($method === 'PATCH' && preg_match('#^/api/admin/users/([^/]+)$#', $path, $matches)) {
        update_user($matches[1]);
        return;
    }

    if ($method === 'PATCH' && preg_match('#^/api/admin/users/([^/]+)/password$#', $path, $matches)) {
        reset_user_password($matches[1]);
        return;
    }

    if ($method === 'GET' && $path === '/api/admin/menu-permissions') {
        list_menu_permissions();
        return;
    }

    if ($method === 'POST' && $path === '/api/admin/menu-permissions') {
        save_menu_permissions();
        return;
    }

    if ($path === '/api/tickets') {
        if ($method === 'GET') {
            list_tickets();
            return;
        }
        if ($method === 'POST') {
            create_ticket();
            return;
        }
    }

    if ($method === 'PATCH' && preg_match('#^/api/tickets/([^/]+)/status$#', $path, $matches)) {
        update_ticket_status($matches[1]);
        return;
    }

    if ($method === 'GET' && preg_match('#^/api/tickets/([^/]+)/events$#', $path, $matches)) {
        list_ticket_events($matches[1]);
        return;
    }

    if ($method === 'POST' && preg_match('#^/api/tickets/([^/]+)/events$#', $path, $matches)) {
        create_ticket_event($matches[1]);
        return;
    }

    if ($method === 'GET' && $path === '/api/osint/mentions') {
        list_osint_mentions();
        return;
    }

    if ($method === 'GET' && $path === '/api/notifications') {
        list_notifications();
        return;
    }

    if ($method === 'POST' && preg_match('#^/api/notifications/([^/]+)/ack$#', $path, $matches)) {
        acknowledge_notification($matches[1]);
        return;
    }

    if ($method === 'GET' && $path === '/api/reports/summary') {
        report_summary();
        return;
    }

    if ($method === 'POST' && $path === '/api/reports/jobs') {
        create_report_job();
        return;
    }

    json_response(['error' => 'Route not found: ' . $method . ' ' . $path], 404);
}

function db(): PDO
{
    static $pdo = null;

    if ($pdo instanceof PDO) {
        return $pdo;
    }

    $url = parse_url(getenv_value('DATABASE_URL', 'postgres://spap:spap_password@postgres:5432/spap'));
    $host = $url['host'] ?? 'postgres';
    $port = $url['port'] ?? 5432;
    $user = $url['user'] ?? 'spap';
    $password = $url['pass'] ?? 'spap_password';
    $database = isset($url['path']) ? ltrim($url['path'], '/') : 'spap';

    $pdo = new PDO(
        "pgsql:host={$host};port={$port};dbname={$database}",
        $user,
        $password,
        [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        ]
    );

    return $pdo;
}

function ensure_schema(): void
{
    static $ready = false;

    if ($ready) {
        return;
    }

    $statements = [
        'CREATE EXTENSION IF NOT EXISTS pgcrypto',
        "CREATE TABLE IF NOT EXISTS users (
            id UUID PRIMARY KEY DEFAULT gen_random_uuid(),
            name VARCHAR(120) NOT NULL,
            email VARCHAR(160) UNIQUE NOT NULL,
            password_hash VARCHAR(255),
            role VARCHAR(40) NOT NULL DEFAULT 'operator',
            organization_unit VARCHAR(120),
            status VARCHAR(20) NOT NULL DEFAULT 'active',
            created_at TIMESTAMPTZ NOT NULL DEFAULT now()
        )",
        "CREATE TABLE IF NOT EXISTS tickets (
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
        )",
        "CREATE TABLE IF NOT EXISTS ticket_events (
            id UUID PRIMARY KEY DEFAULT gen_random_uuid(),
            ticket_id UUID NOT NULL REFERENCES tickets(id) ON DELETE CASCADE,
            event_type VARCHAR(60) NOT NULL,
            note TEXT,
            actor_name VARCHAR(120) NOT NULL DEFAULT 'Sistem',
            created_at TIMESTAMPTZ NOT NULL DEFAULT now()
        )",
        "CREATE TABLE IF NOT EXISTS osint_mentions (
            id UUID PRIMARY KEY DEFAULT gen_random_uuid(),
            source VARCHAR(60) NOT NULL,
            keyword VARCHAR(120) NOT NULL,
            cluster_name VARCHAR(80),
            sentiment VARCHAR(20) NOT NULL CHECK (sentiment IN ('Positif', 'Netral', 'Negatif')),
            mention_count INTEGER NOT NULL DEFAULT 0,
            sample_text TEXT,
            recommendation TEXT,
            captured_at TIMESTAMPTZ NOT NULL DEFAULT now()
        )",
        "CREATE TABLE IF NOT EXISTS report_jobs (
            id UUID PRIMARY KEY DEFAULT gen_random_uuid(),
            report_type VARCHAR(80) NOT NULL,
            period VARCHAR(40) NOT NULL,
            region VARCHAR(120) NOT NULL,
            output_format VARCHAR(20) NOT NULL,
            status VARCHAR(20) NOT NULL DEFAULT 'queued',
            payload JSONB NOT NULL DEFAULT '{}'::jsonb,
            created_at TIMESTAMPTZ NOT NULL DEFAULT now(),
            completed_at TIMESTAMPTZ
        )",
        "CREATE TABLE IF NOT EXISTS app_cache (
            cache_key VARCHAR(180) PRIMARY KEY,
            payload JSONB NOT NULL,
            expires_at TIMESTAMPTZ NOT NULL
        )",
        "CREATE TABLE IF NOT EXISTS menu_permissions (
            role VARCHAR(40) NOT NULL,
            menu_key VARCHAR(80) NOT NULL,
            can_view BOOLEAN NOT NULL DEFAULT true,
            can_create BOOLEAN NOT NULL DEFAULT false,
            can_update BOOLEAN NOT NULL DEFAULT false,
            can_delete BOOLEAN NOT NULL DEFAULT false,
            PRIMARY KEY (role, menu_key)
        )",
        'CREATE INDEX IF NOT EXISTS tickets_type_status_idx ON tickets(type, status)',
        'CREATE INDEX IF NOT EXISTS tickets_region_category_idx ON tickets(region, category)',
        'CREATE INDEX IF NOT EXISTS ticket_events_ticket_idx ON ticket_events(ticket_id, created_at DESC)',
        'CREATE INDEX IF NOT EXISTS osint_mentions_keyword_idx ON osint_mentions(keyword, captured_at DESC)',
        "INSERT INTO users (name, email, role, organization_unit)
         VALUES
            ('Admin SPAP', 'admin@spap.local', 'admin', 'DPP'),
            ('Operator SPAP', 'operator@spap.local', 'operator', 'Triage SPAP'),
            ('Verifikator SPAP', 'verifikator@spap.local', 'verifikator', 'Validasi Data'),
            ('Koordinator Pengaduan', 'koordinator@spap.local', 'koordinator', 'Unit Pengaduan')
         ON CONFLICT (email) DO NOTHING",
        "INSERT INTO menu_permissions (role, menu_key, can_view, can_create, can_update, can_delete)
         VALUES
            ('admin', 'dashboard', true, true, true, true),
            ('admin', 'aspirasi', true, true, true, true),
            ('admin', 'pengaduan', true, true, true, true),
            ('admin', 'osint', true, true, true, true),
            ('admin', 'analytics', true, true, true, true),
            ('admin', 'laporan', true, true, true, true),
            ('admin', 'settings', true, true, true, true),
            ('operator', 'dashboard', true, false, false, false),
            ('operator', 'aspirasi', true, true, true, false),
            ('operator', 'pengaduan', true, true, true, false),
            ('operator', 'laporan', true, false, false, false),
            ('verifikator', 'dashboard', true, false, false, false),
            ('verifikator', 'aspirasi', true, false, true, false),
            ('verifikator', 'pengaduan', true, false, true, false),
            ('koordinator', 'dashboard', true, false, false, false),
            ('koordinator', 'pengaduan', true, false, true, false),
            ('koordinator', 'laporan', true, false, false, false)
         ON CONFLICT (role, menu_key) DO NOTHING",
        "INSERT INTO tickets (public_id, type, reporter_name, channel, region, category, priority, status, subject, description, assigned_unit, sla_due_at)
         VALUES
            ('ASP-2026-001', 'aspirasi', 'Ahmad Rizki', 'WhatsApp', 'DKI Jakarta', 'Infrastruktur', 'Tinggi', 'Baru', 'Perbaikan jalan rusak di Cengkareng', 'Jalan utama rusak dan membahayakan pengendara saat jam padat.', 'DPC Jakarta Barat', now() + interval '2 days'),
            ('ASP-2026-002', 'aspirasi', 'Siti Nurhaliza', 'Form Web', 'Jawa Barat', 'Pendidikan', 'Sedang', 'Diproses', 'Kekurangan guru SD negeri', 'Orang tua meminta advokasi penambahan guru kelas dan fasilitas belajar.', 'DPD Bandung', now() + interval '4 days'),
            ('PEN-2026-001', 'pengaduan', 'Budi Santoso', 'Email', 'Jawa Barat', 'Hukum', 'Kritis', 'Baru', 'Dugaan penyimpangan proyek jembatan', 'Pelapor meminta investigasi awal dan perlindungan identitas.', 'Tim Advokasi Hukum', now() + interval '1 day')
         ON CONFLICT (public_id) DO NOTHING",
        "INSERT INTO osint_mentions (source, keyword, cluster_name, sentiment, mention_count, sample_text, recommendation)
         VALUES
            ('X/Twitter', '#HargaSembako', 'Ekonomi', 'Negatif', 15420, 'Harga bahan pokok naik, butuh kanal aspirasi yang cepat.', 'Susun respon kebijakan dan advokasi pasar murah.'),
            ('Facebook', '#PendidikanGratis', 'Pendidikan', 'Positif', 5430, 'Program bantuan pendidikan mendapat respon positif dari orang tua.', 'Amplifikasi program dan kumpulkan testimoni.'),
            ('Instagram', '#Infrastruktur', 'Infrastruktur', 'Netral', 6720, 'Warga menandai kondisi jalan rusak dan meminta advokasi.', 'Petakan wilayah keluhan untuk koordinasi DPC.')
         ON CONFLICT DO NOTHING",
    ];

    foreach ($statements as $statement) {
        db()->exec($statement);
    }

    seed_user_password('admin@spap.local', '$2y$10$XjdRzaG9nJAORl4ek5m3LuLXJpCaSW29f3niYRrSH2ObViR8rIqa2');
    seed_user_password('operator@spap.local', '$2y$10$CynqGjenPLMJnhh33m.nLepJRQvpo/BuJZr60ZWo5dj5WERsIUXqe');
    seed_user_password('verifikator@spap.local', '$2y$10$CynqGjenPLMJnhh33m.nLepJRQvpo/BuJZr60ZWo5dj5WERsIUXqe');
    seed_user_password('koordinator@spap.local', '$2y$10$CynqGjenPLMJnhh33m.nLepJRQvpo/BuJZr60ZWo5dj5WERsIUXqe');

    $ready = true;
}

function seed_user_password(string $email, string $passwordHash): void
{
    $statement = db()->prepare('UPDATE users SET password_hash = ?, status = ? WHERE email = ?');
    $statement->execute([$passwordHash, 'active', $email]);
}

function cache(): ?Redis
{
    static $redis = false;

    if ($redis instanceof Redis) {
        return $redis;
    }
    if ($redis === null) {
        return null;
    }

    $redisUrl = getenv('REDIS_URL');
    if ($redisUrl === false || trim($redisUrl) === '') {
        $redis = null;
        return null;
    }

    $url = parse_url($redisUrl);
    $host = $url['host'] ?? '';
    $port = $url['port'] ?? 6379;

    if (!$host) {
        $redis = null;
        return null;
    }

    try {
        $client = new Redis();
        @$client->connect($host, (int) $port, 1.5);
        $redis = $client;
        return $redis;
    } catch (Throwable $error) {
        error_log('Redis unavailable: ' . $error->getMessage());
        $redis = null;
        return null;
    }
}

function getenv_value(string $name, string $default): string
{
    $value = getenv($name);
    return $value === false || $value === '' ? $default : $value;
}

function json_response(array $payload, int $status = 200): void
{
    http_response_code($status);
    echo json_encode($payload, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
}

function input_json(): array
{
    $raw = file_get_contents('php://input') ?: '{}';
    $decoded = json_decode($raw, true);
    return is_array($decoded) ? $decoded : [];
}

function bearer_token(): ?string
{
    $header = $_SERVER['HTTP_AUTHORIZATION'] ?? '';
    if (preg_match('/Bearer\s+(.+)/', $header, $matches)) {
        return trim($matches[1]);
    }
    return null;
}

function token_key(string $token): string
{
    return 'session:' . hash('sha256', $token);
}

function public_user(array $user): array
{
    return [
        'id' => $user['id'],
        'name' => $user['name'],
        'email' => $user['email'],
        'role' => $user['role'],
        'organizationUnit' => $user['organization_unit'] ?? null,
        'permissions' => permissions_for_role($user['role']),
    ];
}

function permissions_for_role(string $role): array
{
    $statement = db()->prepare(
        'SELECT menu_key, can_view, can_create, can_update, can_delete
         FROM menu_permissions
         WHERE role = ?
         ORDER BY menu_key'
    );
    $statement->execute([$role]);
    $permissions = [];
    foreach ($statement->fetchAll() as $row) {
        $permissions[$row['menu_key']] = [
            'view' => filter_var($row['can_view'], FILTER_VALIDATE_BOOLEAN),
            'create' => filter_var($row['can_create'], FILTER_VALIDATE_BOOLEAN),
            'update' => filter_var($row['can_update'], FILTER_VALIDATE_BOOLEAN),
            'delete' => filter_var($row['can_delete'], FILTER_VALIDATE_BOOLEAN),
        ];
    }
    return $permissions;
}

function session_user(): ?array
{
    $token = bearer_token();
    if (!$token) {
        return null;
    }
    $session = cache_get(token_key($token));
    return is_array($session) && !empty($session['user']) ? $session['user'] : null;
}

function require_admin(): ?array
{
    $user = session_user();
    if (!$user) {
        json_response(['error' => 'Unauthorized'], 401);
        return null;
    }
    if (($user['role'] ?? '') !== 'admin') {
        json_response(['error' => 'Forbidden'], 403);
        return null;
    }
    return $user;
}

function require_permission(string $menuKey, string $action): ?array
{
    $user = session_user();
    if (!$user) {
        json_response(['error' => 'Unauthorized'], 401);
        return null;
    }
    if (($user['role'] ?? '') === 'admin') {
        return $user;
    }

    $permission = $user['permissions'][$menuKey] ?? null;
    if (!$permission || empty($permission[$action])) {
        json_response(['error' => 'Forbidden'], 403);
        return null;
    }

    return $user;
}

function cache_get(string $key): ?array
{
    $redis = cache();
    if ($redis) {
        $value = $redis->get($key);
        if (!$value) {
            return null;
        }
        $decoded = json_decode($value, true);
        return is_array($decoded) ? $decoded : null;
    }

    $statement = db()->prepare('SELECT payload FROM app_cache WHERE cache_key = ? AND expires_at > now() LIMIT 1');
    $statement->execute([$key]);
    $row = $statement->fetch();
    return $row ? json_decode($row['payload'], true) : null;
}

function cache_set(string $key, array $payload, int $ttl = 60): void
{
    $redis = cache();
    if ($redis) {
        $redis->setex($key, $ttl, json_encode($payload, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES));
        return;
    }

    $expiresAt = gmdate('c', time() + $ttl);
    $statement = db()->prepare(
        "INSERT INTO app_cache (cache_key, payload, expires_at)
         VALUES (?, CAST(? AS jsonb), CAST(? AS timestamptz))
         ON CONFLICT (cache_key) DO UPDATE
         SET payload = EXCLUDED.payload, expires_at = EXCLUDED.expires_at"
    );
    $statement->execute([$key, json_encode($payload, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES), $expiresAt]);
}

function cache_delete(string $key): void
{
    $redis = cache();
    if ($redis) {
        $redis->del($key);
        return;
    }

    $statement = db()->prepare('DELETE FROM app_cache WHERE cache_key = ?');
    $statement->execute([$key]);
}

function cache_invalidate(string $prefix): void
{
    $redis = cache();
    if ($redis) {
        foreach ($redis->keys($prefix . '*') as $key) {
            $redis->del($key);
        }
        return;
    }

    $statement = db()->prepare('DELETE FROM app_cache WHERE cache_key LIKE ?');
    $statement->execute([$prefix . '%']);
}

function health(): void
{
    $dbStatus = 'error';
    $cacheStatus = 'error';

    try {
        db()->query('SELECT 1');
        $dbStatus = 'ok';
    } catch (Throwable $error) {
        error_log($error->getMessage());
    }

    try {
        $redis = cache();
        if ($redis && $redis->ping()) {
            $cacheStatus = 'ok';
        }
    } catch (Throwable $error) {
        error_log($error->getMessage());
    }

    json_response(['status' => 'ok', 'runtime' => 'php7', 'services' => ['db' => $dbStatus, 'cache' => $cacheStatus]]);
}

function login(): void
{
    $input = input_json();
    $email = strtolower(trim($input['email'] ?? ''));
    $password = (string) ($input['password'] ?? '');

    if (!$email || !$password) {
        json_response(['error' => 'Email dan password wajib diisi'], 422);
        return;
    }

    $statement = db()->prepare('SELECT * FROM users WHERE lower(email) = ? AND status = ? LIMIT 1');
    $statement->execute([$email, 'active']);
    $user = $statement->fetch();

    if (!$user || empty($user['password_hash']) || !password_verify($password, $user['password_hash'])) {
        json_response(['error' => 'Email atau password tidak sesuai'], 401);
        return;
    }

    $token = bin2hex(random_bytes(32));
    cache_set(token_key($token), ['user' => public_user($user)], 86400);

    json_response([
        'data' => [
            'token' => $token,
            'user' => public_user($user),
        ],
    ]);
}

function current_user(): void
{
    $token = bearer_token();
    if (!$token) {
        json_response(['error' => 'Unauthorized'], 401);
        return;
    }

    $session = cache_get(token_key($token));
    if (!$session || empty($session['user'])) {
        json_response(['error' => 'Session expired'], 401);
        return;
    }

    json_response(['data' => $session['user']]);
}

function list_users(): void
{
    if (!require_admin()) {
        return;
    }

    $statement = db()->query(
        'SELECT id, name, email, role, organization_unit, status, created_at
         FROM users
         ORDER BY created_at DESC, name ASC'
    );
    json_response(['data' => $statement->fetchAll()]);
}

function create_user(): void
{
    if (!require_admin()) {
        return;
    }

    $input = input_json();
    $name = trim($input['name'] ?? '');
    $email = strtolower(trim($input['email'] ?? ''));
    $role = $input['role'] ?? 'operator';
    $unit = $input['organizationUnit'] ?? 'SPAP';
    $status = $input['status'] ?? 'active';
    $password = $input['password'] ?? 'user123';

    if (!$name || !$email) {
        json_response(['error' => 'Nama dan email wajib diisi'], 422);
        return;
    }

    $statement = db()->prepare(
        'INSERT INTO users (name, email, password_hash, role, organization_unit, status)
         VALUES (?, ?, ?, ?, ?, ?)
         ON CONFLICT (email) DO UPDATE
         SET name = EXCLUDED.name, role = EXCLUDED.role, organization_unit = EXCLUDED.organization_unit, status = EXCLUDED.status
         RETURNING id, name, email, role, organization_unit, status, created_at'
    );
    $statement->execute([$name, $email, password_hash($password, PASSWORD_BCRYPT), $role, $unit, $status]);
    json_response(['data' => $statement->fetch()], 201);
}

function update_user(string $id): void
{
    if (!require_admin()) {
        return;
    }

    $input = input_json();
    $statement = db()->prepare(
        'UPDATE users
         SET name = COALESCE(?, name),
             role = COALESCE(?, role),
             organization_unit = COALESCE(?, organization_unit),
             status = COALESCE(?, status)
         WHERE id = ?
         RETURNING id, name, email, role, organization_unit, status, created_at'
    );
    $statement->execute([
        $input['name'] ?? null,
        $input['role'] ?? null,
        $input['organizationUnit'] ?? null,
        $input['status'] ?? null,
        $id,
    ]);
    $user = $statement->fetch();
    if (!$user) {
        json_response(['error' => 'User not found'], 404);
        return;
    }
    json_response(['data' => $user]);
}

function reset_user_password(string $id): void
{
    if (!require_admin()) {
        return;
    }

    $input = input_json();
    $password = trim($input['password'] ?? '');
    if (strlen($password) < 6) {
        json_response(['error' => 'Password minimal 6 karakter'], 422);
        return;
    }

    $statement = db()->prepare(
        'UPDATE users
         SET password_hash = ?
         WHERE id = ?
         RETURNING id, name, email, role, organization_unit, status, created_at'
    );
    $statement->execute([password_hash($password, PASSWORD_BCRYPT), $id]);
    $user = $statement->fetch();
    if (!$user) {
        json_response(['error' => 'User not found'], 404);
        return;
    }
    json_response(['data' => $user]);
}

function list_menu_permissions(): void
{
    if (!require_admin()) {
        return;
    }

    $statement = db()->query(
        'SELECT role, menu_key, can_view, can_create, can_update, can_delete
         FROM menu_permissions
         ORDER BY role, menu_key'
    );
    $rows = array_map(static function (array $row): array {
        return [
            'role' => $row['role'],
            'menu_key' => $row['menu_key'],
            'can_view' => filter_var($row['can_view'], FILTER_VALIDATE_BOOLEAN),
            'can_create' => filter_var($row['can_create'], FILTER_VALIDATE_BOOLEAN),
            'can_update' => filter_var($row['can_update'], FILTER_VALIDATE_BOOLEAN),
            'can_delete' => filter_var($row['can_delete'], FILTER_VALIDATE_BOOLEAN),
        ];
    }, $statement->fetchAll());

    json_response(['data' => $rows]);
}

function save_menu_permissions(): void
{
    if (!require_admin()) {
        return;
    }

    $input = input_json();
    $items = $input['permissions'] ?? [];
    $statement = db()->prepare(
        'INSERT INTO menu_permissions (role, menu_key, can_view, can_create, can_update, can_delete)
         VALUES (?, ?, ?, ?, ?, ?)
         ON CONFLICT (role, menu_key) DO UPDATE
         SET can_view = EXCLUDED.can_view,
             can_create = EXCLUDED.can_create,
             can_update = EXCLUDED.can_update,
             can_delete = EXCLUDED.can_delete'
    );

    foreach ($items as $item) {
        $statement->execute([
            $item['role'] ?? 'operator',
            $item['menuKey'] ?? 'dashboard',
            !empty($item['canView']),
            !empty($item['canCreate']),
            !empty($item['canUpdate']),
            !empty($item['canDelete']),
        ]);
    }

    cache_invalidate('permissions:');
    json_response(['data' => ['saved' => count($items)]]);
}

function logout(): void
{
    $token = bearer_token();
    if ($token) {
        cache_delete(token_key($token));
    }
    json_response(['data' => ['message' => 'Logout berhasil']]);
}

function list_tickets(): void
{
    $user = session_user();
    if (!$user) {
        json_response(['error' => 'Unauthorized'], 401);
        return;
    }

    $type = $_GET['type'] ?? null;
    $status = $_GET['status'] ?? null;
    $region = $_GET['region'] ?? null;
    $q = $_GET['q'] ?? null;

    if ($type && ($user['role'] ?? '') !== 'admin') {
        $permission = $user['permissions'][$type] ?? null;
        if (!$permission || empty($permission['view'])) {
            json_response(['error' => 'Forbidden'], 403);
            return;
        }
    }

    $cacheKey = 'tickets:' . ($type ?: 'all') . ':' . ($status ?: 'all') . ':' . ($region ?: 'all') . ':' . ($q ?: '');

    $cached = cache_get($cacheKey);
    if ($cached) {
        json_response($cached);
        return;
    }

    $where = [];
    $params = [];

    if ($type) {
        $where[] = 'type = ?';
        $params[] = $type;
    }
    if ($status) {
        $where[] = 'status = ?';
        $params[] = $status;
    }
    if ($region) {
        $where[] = 'region = ?';
        $params[] = $region;
    }
    if ($q) {
        $where[] = '(subject ILIKE ? OR description ILIKE ? OR reporter_name ILIKE ?)';
        $params[] = '%' . $q . '%';
        $params[] = '%' . $q . '%';
        $params[] = '%' . $q . '%';
    }

    $sql = 'SELECT public_id, type, reporter_name, reporter_contact, channel, region, category, priority, status,
                   subject, description, assigned_unit, sla_due_at, resolved_at, created_at, updated_at
            FROM tickets';
    if ($where) {
        $sql .= ' WHERE ' . implode(' AND ', $where);
    }
    $sql .= ' ORDER BY created_at DESC LIMIT 100';

    $statement = db()->prepare($sql);
    $statement->execute($params);
    $payload = ['data' => $statement->fetchAll()];
    cache_set($cacheKey, $payload, 30);
    json_response($payload);
}

function create_ticket(): void
{
    $ticket = input_json();
    $type = $ticket['type'] ?? 'aspirasi';
    $actor = require_permission($type === 'pengaduan' ? 'pengaduan' : 'aspirasi', 'create');
    if (!$actor) {
        return;
    }
    $prefix = $type === 'pengaduan' ? 'PEN' : 'ASP';

    $countStatement = db()->prepare('SELECT count(*)::int + 1 AS next FROM tickets WHERE type = ?');
    $countStatement->execute([$type]);
    $next = (int) $countStatement->fetchColumn();
    $publicId = $prefix . '-2026-' . str_pad((string) $next, 3, '0', STR_PAD_LEFT);

    $statement = db()->prepare(
        "INSERT INTO tickets
          (public_id, type, reporter_name, reporter_contact, channel, region, category, priority, status, subject, description, assigned_unit, sla_due_at)
         VALUES
          (?, ?, ?, ?, ?, ?, ?, ?, 'Baru', ?, ?, ?, now() + (? * interval '1 hour'))
         RETURNING *"
    );
    $slaHours = sla_hours_for_priority($ticket['priority'] ?? 'Sedang');
    $statement->execute([
        $publicId,
        $type,
        $ticket['reporterName'] ?? 'Pelapor',
        $ticket['reporterContact'] ?? null,
        $ticket['channel'] ?? 'Input Operator',
        $ticket['region'] ?? 'Nasional',
        $ticket['category'] ?? 'Umum',
        $ticket['priority'] ?? 'Sedang',
        $ticket['subject'] ?? 'Tiket baru',
        $ticket['description'] ?? '-',
        $ticket['assignedUnit'] ?? 'Triage SPAP',
        $slaHours,
    ]);
    $created = $statement->fetch();

    $event = db()->prepare("INSERT INTO ticket_events (ticket_id, event_type, note, actor_name) VALUES (?, 'created', ?, ?)");
    $event->execute([$created['id'], 'Tiket dibuat dari aplikasi SPAP', $actor['name'] ?? 'Operator SPAP']);

    cache_invalidate('tickets:');
    json_response(['data' => $created], 201);
}

function sla_hours_for_priority(string $priority): int
{
    $map = [
        'Kritis' => 12,
        'Tinggi' => 24,
        'Sedang' => 48,
        'Rendah' => 72,
    ];
    return $map[$priority] ?? 48;
}

function update_ticket_status(string $publicId): void
{
    $input = input_json();
    $status = $input['status'] ?? 'Diproses';
    $assignedUnit = $input['assignedUnit'] ?? null;
    $typeStatement = db()->prepare('SELECT type FROM tickets WHERE public_id = ? LIMIT 1');
    $typeStatement->execute([$publicId]);
    $type = $typeStatement->fetchColumn();
    if (!$type) {
        json_response(['error' => 'Ticket not found'], 404);
        return;
    }

    $actor = require_permission($type === 'pengaduan' ? 'pengaduan' : 'aspirasi', 'update');
    if (!$actor) {
        return;
    }

    $statement = db()->prepare(
        "UPDATE tickets
         SET status = ?,
             assigned_unit = COALESCE(?, assigned_unit),
             resolved_at = CASE WHEN ? = 'Selesai' THEN now() ELSE resolved_at END,
             updated_at = now()
         WHERE public_id = ?
         RETURNING *"
    );
    $statement->execute([$status, $assignedUnit, $status, $publicId]);
    $ticket = $statement->fetch();

    if (!$ticket) {
        json_response(['error' => 'Ticket not found'], 404);
        return;
    }

    $event = db()->prepare("INSERT INTO ticket_events (ticket_id, event_type, note, actor_name) VALUES (?, 'status_changed', ?, ?)");
    $event->execute([
        $ticket['id'],
        $input['note'] ?? 'Status diubah ke ' . $status,
        $input['actorName'] ?? ($actor['name'] ?? 'Operator SPAP'),
    ]);

    cache_invalidate('tickets:');
    json_response(['data' => $ticket]);
}

function ticket_by_public_id(string $publicId): ?array
{
    $statement = db()->prepare('SELECT id, public_id, type FROM tickets WHERE public_id = ? LIMIT 1');
    $statement->execute([$publicId]);
    $ticket = $statement->fetch();
    return $ticket ?: null;
}

function list_ticket_events(string $publicId): void
{
    $ticket = ticket_by_public_id($publicId);
    if (!$ticket) {
        json_response(['error' => 'Ticket not found'], 404);
        return;
    }

    $actor = require_permission($ticket['type'] === 'pengaduan' ? 'pengaduan' : 'aspirasi', 'view');
    if (!$actor) {
        return;
    }

    $statement = db()->prepare(
        'SELECT event_type, note, actor_name, created_at
         FROM ticket_events
         WHERE ticket_id = ?
         ORDER BY created_at DESC'
    );
    $statement->execute([$ticket['id']]);
    json_response(['data' => $statement->fetchAll()]);
}

function create_ticket_event(string $publicId): void
{
    $ticket = ticket_by_public_id($publicId);
    if (!$ticket) {
        json_response(['error' => 'Ticket not found'], 404);
        return;
    }

    $actor = require_permission($ticket['type'] === 'pengaduan' ? 'pengaduan' : 'aspirasi', 'update');
    if (!$actor) {
        return;
    }

    $input = input_json();
    $note = trim($input['note'] ?? '');
    if (!$note) {
        json_response(['error' => 'Catatan wajib diisi'], 422);
        return;
    }

    $statement = db()->prepare(
        "INSERT INTO ticket_events (ticket_id, event_type, note, actor_name)
         VALUES (?, 'note_added', ?, ?)
         RETURNING event_type, note, actor_name, created_at"
    );
    $statement->execute([
        $ticket['id'],
        $note,
        $input['actorName'] ?? ($actor['name'] ?? 'Operator SPAP'),
    ]);

    json_response(['data' => $statement->fetch()], 201);
}

function list_osint_mentions(): void
{
    $cached = cache_get('osint:mentions');
    if ($cached) {
        json_response($cached);
        return;
    }

    $statement = db()->query(
        'SELECT source, keyword, cluster_name, sentiment, mention_count, sample_text, recommendation, captured_at
         FROM osint_mentions
         ORDER BY captured_at DESC, mention_count DESC
         LIMIT 50'
    );
    $payload = ['data' => $statement->fetchAll()];
    cache_set('osint:mentions', $payload, 60);
    json_response($payload);
}

function list_notifications(): void
{
    $user = session_user();
    if (!$user) {
        json_response(['error' => 'Unauthorized'], 401);
        return;
    }

    $statement = db()->query(
        "SELECT public_id, type, priority, status, subject, region, assigned_unit, sla_due_at, created_at,
                CASE
                  WHEN status <> 'Selesai' AND sla_due_at < now() THEN 'overdue'
                  WHEN priority = 'Kritis' AND status <> 'Selesai' THEN 'critical'
                  WHEN status = 'Eskalasi' THEN 'escalation'
                  WHEN status = 'Baru' AND created_at < now() - interval '24 hours' THEN 'waiting'
                  ELSE 'info'
                END AS severity
         FROM tickets
         WHERE status <> 'Selesai'
           AND (
             sla_due_at < now()
             OR priority = 'Kritis'
             OR status = 'Eskalasi'
             OR (status = 'Baru' AND created_at < now() - interval '24 hours')
           )
         ORDER BY
           CASE
             WHEN sla_due_at < now() THEN 1
             WHEN priority = 'Kritis' THEN 2
             WHEN status = 'Eskalasi' THEN 3
             ELSE 4
           END,
           sla_due_at ASC
         LIMIT 20"
    );

    $items = array_map(static function (array $row): array {
        $title = [
            'overdue' => 'SLA terlewati',
            'critical' => 'Tiket kritis aktif',
            'escalation' => 'Butuh eskalasi',
            'waiting' => 'Menunggu verifikasi',
            'info' => 'Perlu tindak lanjut',
        ][$row['severity']] ?? 'Perlu tindak lanjut';

        return [
            'id' => $row['public_id'],
            'type' => $row['type'],
            'severity' => $row['severity'],
            'title' => $title,
            'description' => $row['subject'] . ' - ' . $row['region'],
            'assignedUnit' => $row['assigned_unit'],
            'slaDueAt' => $row['sla_due_at'],
            'createdAt' => $row['created_at'],
        ];
    }, $statement->fetchAll());

    json_response(['data' => $items]);
}

function acknowledge_notification(string $publicId): void
{
    $ticket = ticket_by_public_id($publicId);
    if (!$ticket) {
        json_response(['error' => 'Ticket not found'], 404);
        return;
    }

    $actor = require_permission($ticket['type'] === 'pengaduan' ? 'pengaduan' : 'aspirasi', 'update');
    if (!$actor) {
        return;
    }

    $input = input_json();
    $note = trim($input['note'] ?? 'Notifikasi SLA ditandai sudah ditindaklanjuti');

    $event = db()->prepare(
        "INSERT INTO ticket_events (ticket_id, event_type, note, actor_name)
         VALUES (?, 'notification_acknowledged', ?, ?)"
    );
    $event->execute([
        $ticket['id'],
        $note,
        $input['actorName'] ?? ($actor['name'] ?? 'Operator SPAP'),
    ]);

    $status = $input['status'] ?? null;
    if ($status) {
        $update = db()->prepare(
            "UPDATE tickets
             SET status = ?,
                 updated_at = now()
             WHERE id = ?"
        );
        $update->execute([$status, $ticket['id']]);
        cache_invalidate('tickets:');
    }

    json_response(['data' => ['id' => $publicId, 'acknowledged' => true]]);
}

function report_summary(): void
{
    $region = $_GET['region'] ?? null;
    $where = $region ? 'WHERE region = ?' : '';
    $params = $region ? [$region] : [];

    $status = db()->prepare("SELECT status, count(*)::int AS total FROM tickets {$where} GROUP BY status");
    $status->execute($params);

    $categories = db()->prepare("SELECT category, count(*)::int AS total FROM tickets {$where} GROUP BY category ORDER BY total DESC");
    $categories->execute($params);

    $osint = db()->query('SELECT keyword, sentiment, mention_count FROM osint_mentions ORDER BY mention_count DESC LIMIT 5');

    json_response([
        'data' => [
            'status' => $status->fetchAll(),
            'categories' => $categories->fetchAll(),
            'osint' => $osint->fetchAll(),
        ],
    ]);
}

function create_report_job(): void
{
    $input = input_json();
    $statement = db()->prepare(
        'INSERT INTO report_jobs (report_type, period, region, output_format, payload)
         VALUES (?, ?, ?, ?, ?::jsonb)
         RETURNING *'
    );
    $statement->execute([
        $input['reportType'] ?? 'Ringkasan Eksekutif',
        $input['period'] ?? 'Harian',
        $input['region'] ?? 'Nasional',
        $input['outputFormat'] ?? 'PDF',
        json_encode($input['payload'] ?? new stdClass(), JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES),
    ]);

    json_response(['data' => $statement->fetch()], 201);
}
