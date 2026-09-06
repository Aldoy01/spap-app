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

    if ($path === '/api/integrations/whatsapp/webhook') {
        if ($method === 'GET') {
            verify_whatsapp_webhook();
            return;
        }
        if ($method === 'POST') {
            receive_whatsapp_webhook();
            return;
        }
    }

    if ($method === 'GET' && $path === '/api/public/complaints') {
        public_complaints_info();
        return;
    }

    if ($method === 'POST' && $path === '/api/public/complaints') {
        create_public_complaint();
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

    if ($method === 'GET' && $path === '/api/admin/security-events') {
        list_security_events();
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

    if ($method === 'GET' && $path === '/api/kpu/dapil') {
        list_kpu_dapil();
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
            target_name VARCHAR(160),
            region_scope VARCHAR(120),
            status VARCHAR(20) NOT NULL DEFAULT 'active',
            created_at TIMESTAMPTZ NOT NULL DEFAULT now()
        )",
        'ALTER TABLE users ADD COLUMN IF NOT EXISTS target_name VARCHAR(160)',
        'ALTER TABLE users ADD COLUMN IF NOT EXISTS region_scope VARCHAR(120)',
        'ALTER TABLE users ADD COLUMN IF NOT EXISTS last_login_at TIMESTAMPTZ',
        'ALTER TABLE users ADD COLUMN IF NOT EXISTS password_changed_at TIMESTAMPTZ',
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
            target_level VARCHAR(80),
            target_dapil VARCHAR(120),
            target_province VARCHAR(120),
            target_city VARCHAR(120),
            target_name VARCHAR(160),
            sla_due_at TIMESTAMPTZ,
            resolved_at TIMESTAMPTZ,
            created_at TIMESTAMPTZ NOT NULL DEFAULT now(),
            updated_at TIMESTAMPTZ NOT NULL DEFAULT now()
        )",
        'ALTER TABLE tickets ADD COLUMN IF NOT EXISTS target_level VARCHAR(80)',
        'ALTER TABLE tickets ADD COLUMN IF NOT EXISTS target_dapil VARCHAR(120)',
        'ALTER TABLE tickets ADD COLUMN IF NOT EXISTS target_province VARCHAR(120)',
        'ALTER TABLE tickets ADD COLUMN IF NOT EXISTS target_city VARCHAR(120)',
        'ALTER TABLE tickets ADD COLUMN IF NOT EXISTS target_name VARCHAR(160)',
        'CREATE INDEX IF NOT EXISTS tickets_target_name_idx ON tickets(target_name)',
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
        "CREATE TABLE IF NOT EXISTS security_events (
            id UUID PRIMARY KEY DEFAULT gen_random_uuid(),
            event_type VARCHAR(80) NOT NULL,
            actor_user_id UUID,
            actor_email VARCHAR(160),
            target_user_id UUID,
            target_email VARCHAR(160),
            ip_address VARCHAR(80),
            user_agent TEXT,
            success BOOLEAN NOT NULL DEFAULT true,
            metadata JSONB NOT NULL DEFAULT '{}'::jsonb,
            created_at TIMESTAMPTZ NOT NULL DEFAULT now()
        )",
        'CREATE INDEX IF NOT EXISTS tickets_type_status_idx ON tickets(type, status)',
        'CREATE INDEX IF NOT EXISTS tickets_region_category_idx ON tickets(region, category)',
        'CREATE INDEX IF NOT EXISTS ticket_events_ticket_idx ON ticket_events(ticket_id, created_at DESC)',
        'CREATE INDEX IF NOT EXISTS osint_mentions_keyword_idx ON osint_mentions(keyword, captured_at DESC)',
        'CREATE INDEX IF NOT EXISTS security_events_created_idx ON security_events(created_at DESC)',
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
    $statement = db()->prepare('UPDATE users SET password_hash = ?, status = ?, password_changed_at = COALESCE(password_changed_at, now()) WHERE email = ? AND password_hash IS NULL');
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

function raw_input(): string
{
    return file_get_contents('php://input') ?: '';
}

function webhook_token(): string
{
    return getenv_value('WHATSAPP_WEBHOOK_TOKEN', 'spap-whatsapp-secret');
}

function whatsapp_notify_enabled(): bool
{
    return strtolower(getenv_value('WHATSAPP_NOTIFY_ENABLED', 'false')) === 'true';
}

function whatsapp_access_token(): string
{
    return getenv_value('WHATSAPP_ACCESS_TOKEN', '');
}

function whatsapp_phone_number_id(): string
{
    return getenv_value('WHATSAPP_PHONE_NUMBER_ID', '');
}

function request_webhook_token(array $input = []): string
{
    return $_GET['token']
        ?? $_SERVER['HTTP_X_WEBHOOK_TOKEN']
        ?? $_SERVER['HTTP_X_SPAP_WEBHOOK_TOKEN']
        ?? ($input['token'] ?? '');
}

function turnstile_secret_key(): string
{
    return getenv_value('TURNSTILE_SECRET_KEY', '');
}

function request_ip_address(): string
{
    return $_SERVER['HTTP_CF_CONNECTING_IP']
        ?? $_SERVER['HTTP_X_FORWARDED_FOR']
        ?? $_SERVER['REMOTE_ADDR']
        ?? '';
}

function request_user_agent(): string
{
    return substr((string) ($_SERVER['HTTP_USER_AGENT'] ?? ''), 0, 500);
}

function allowed_roles(): array
{
    return ['admin', 'operator', 'verifikator', 'koordinator'];
}

function allowed_user_statuses(): array
{
    return ['active', 'inactive', 'suspended'];
}

function validate_user_name(string $name): ?string
{
    $length = strlen($name);
    if ($length < 3 || $length > 120) {
        return 'Nama user harus 3 sampai 120 karakter';
    }
    return null;
}

function validate_email_address(string $email): ?string
{
    if (!filter_var($email, FILTER_VALIDATE_EMAIL) || strlen($email) > 160) {
        return 'Format email tidak valid';
    }
    return null;
}

function validate_password_policy(string $password): ?string
{
    $commonPasswords = ['admin123', 'user123', 'password', 'password123', 'qwerty123', '12345678'];
    if (strlen($password) < 10) {
        return 'Password minimal 10 karakter';
    }
    if (in_array(strtolower($password), $commonPasswords, true)) {
        return 'Password terlalu umum dan mudah ditebak';
    }
    if (!preg_match('/[a-z]/', $password) || !preg_match('/[A-Z]/', $password) || !preg_match('/[0-9]/', $password) || !preg_match('/[^A-Za-z0-9]/', $password)) {
        return 'Password wajib berisi huruf besar, huruf kecil, angka, dan simbol';
    }
    return null;
}

function validate_role_and_status(string $role, string $status): ?string
{
    if (!in_array($role, allowed_roles(), true)) {
        return 'Role user tidak valid';
    }
    if (!in_array($status, allowed_user_statuses(), true)) {
        return 'Status user tidak valid';
    }
    return null;
}

function login_rate_key(string $email): string
{
    return 'login-rate:' . hash('sha256', strtolower($email) . '|' . request_ip_address());
}

function login_rate_remaining(string $email): int
{
    $state = cache_get(login_rate_key($email)) ?: [];
    $lockedUntil = (int) ($state['lockedUntil'] ?? 0);
    return max(0, $lockedUntil - time());
}

function register_failed_login(string $email): void
{
    $key = login_rate_key($email);
    $state = cache_get($key) ?: [];
    $firstAttemptAt = (int) ($state['firstAttemptAt'] ?? time());
    if ($firstAttemptAt < time() - 900) {
        $firstAttemptAt = time();
        $attempts = 0;
    } else {
        $attempts = (int) ($state['attempts'] ?? 0);
    }

    $attempts++;
    $lockedUntil = $attempts >= 5 ? time() + 900 : (int) ($state['lockedUntil'] ?? 0);
    cache_set($key, [
        'attempts' => $attempts,
        'firstAttemptAt' => $firstAttemptAt,
        'lockedUntil' => $lockedUntil,
    ], 900);
}

function clear_login_rate(string $email): void
{
    cache_delete(login_rate_key($email));
}

function record_security_event(string $eventType, ?array $actor = null, ?array $target = null, bool $success = true, array $metadata = []): void
{
    try {
        $statement = db()->prepare(
            "INSERT INTO security_events (event_type, actor_user_id, actor_email, target_user_id, target_email, ip_address, user_agent, success, metadata)
             VALUES (?, ?, ?, ?, ?, ?, ?, ?, CAST(? AS jsonb))"
        );
        $statement->execute([
            $eventType,
            $actor['id'] ?? null,
            $actor['email'] ?? ($metadata['actorEmail'] ?? null),
            $target['id'] ?? null,
            $target['email'] ?? ($metadata['targetEmail'] ?? null),
            request_ip_address(),
            request_user_agent(),
            $success,
            json_encode($metadata, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES),
        ]);
    } catch (Throwable $error) {
        error_log('Security event failed: ' . $error->getMessage());
    }
}

function verify_turnstile_token(string $token): bool
{
    $secret = turnstile_secret_key();
    if ($secret === '' || $token === '') {
        return false;
    }

    $body = http_build_query([
        'secret' => $secret,
        'response' => $token,
        'remoteip' => request_ip_address(),
    ]);

    if (function_exists('curl_init')) {
        $curl = curl_init('https://challenges.cloudflare.com/turnstile/v0/siteverify');
        curl_setopt_array($curl, [
            CURLOPT_POST => true,
            CURLOPT_POSTFIELDS => $body,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_TIMEOUT => 8,
            CURLOPT_HTTPHEADER => ['Content-Type: application/x-www-form-urlencoded'],
        ]);
        $response = curl_exec($curl);
        $error = curl_errno($curl);
        curl_close($curl);
        if ($error || !$response) {
            return false;
        }
    } else {
        $context = stream_context_create([
            'http' => [
                'method' => 'POST',
                'header' => "Content-Type: application/x-www-form-urlencoded\r\n",
                'content' => $body,
                'timeout' => 8,
            ],
        ]);
        $response = file_get_contents('https://challenges.cloudflare.com/turnstile/v0/siteverify', false, $context);
        if (!$response) {
            return false;
        }
    }

    $decoded = json_decode($response, true);
    return is_array($decoded) && ($decoded['success'] ?? false) === true;
}

function verify_whatsapp_webhook(): void
{
    $expected = webhook_token();
    $token = $_GET['hub_verify_token'] ?? $_GET['hub.verify_token'] ?? $_GET['token'] ?? '';
    $challenge = $_GET['hub_challenge'] ?? $_GET['hub.challenge'] ?? '';

    if (!hash_equals($expected, (string) $token)) {
        json_response(['error' => 'Invalid webhook token'], 403);
        return;
    }

    if ($challenge !== '') {
        header('Content-Type: text/plain; charset=utf-8');
        echo $challenge;
        return;
    }

    json_response(['data' => ['verified' => true]]);
}

function receive_whatsapp_webhook(): void
{
    $raw = raw_input();
    $input = json_decode($raw ?: '{}', true);
    $input = is_array($input) ? $input : [];

    if (!hash_equals(webhook_token(), request_webhook_token($input))) {
        json_response(['error' => 'Invalid webhook token'], 403);
        return;
    }

    $messages = extract_whatsapp_messages($input);
    if (!$messages) {
        json_response(['data' => ['received' => 0, 'tickets' => []]]);
        return;
    }

    $created = [];
    foreach ($messages as $message) {
        if (trim($message['body']) === '') {
            continue;
        }
        $ticket = whatsapp_ticket_from_message($message);
        $createdTicket = insert_ticket_record($ticket, 'Webhook WhatsApp', 'Pengaduan otomatis diterima dari WhatsApp');
        $createdTicket['whatsappNotification'] = send_whatsapp_ticket_received_notice($createdTicket);
        $created[] = $createdTicket;
    }

    json_response(['data' => ['received' => count($messages), 'tickets' => $created]], 201);
}

function extract_whatsapp_messages(array $payload): array
{
    $messages = [];

    if (isset($payload['entry']) && is_array($payload['entry'])) {
        foreach ($payload['entry'] as $entry) {
            foreach (($entry['changes'] ?? []) as $change) {
                $value = $change['value'] ?? [];
                foreach (($value['messages'] ?? []) as $message) {
                    $body = $message['text']['body'] ?? $message['button']['text'] ?? '';
                    $from = $message['from'] ?? '';
                    $name = $value['contacts'][0]['profile']['name'] ?? '';
                    $messages[] = ['from' => $from, 'name' => $name, 'body' => (string) $body];
                }
            }
        }
    }

    if (!$messages && isset($payload['messages']) && is_array($payload['messages'])) {
        foreach ($payload['messages'] as $message) {
            $body = is_array($message['text'] ?? null)
                ? ($message['text']['body'] ?? '')
                : ($message['text'] ?? ($message['body'] ?? ''));
            $messages[] = [
                'from' => (string) ($message['from'] ?? $message['phone'] ?? $message['wa_id'] ?? ''),
                'name' => (string) ($message['name'] ?? $message['profileName'] ?? ''),
                'body' => (string) $body,
            ];
        }
    }

    if (!$messages && isset($payload['body'])) {
        $messages[] = [
            'from' => (string) ($payload['from'] ?? $payload['phone'] ?? ''),
            'name' => (string) ($payload['name'] ?? ''),
            'body' => (string) $payload['body'],
        ];
    }

    return $messages;
}

function parse_spap_whatsapp_format(string $body): array
{
    $fields = [];
    foreach (preg_split('/\r\n|\r|\n/', $body) as $line) {
        if (strpos($line, ':') === false) {
            continue;
        }
        [$key, $value] = array_map('trim', explode(':', $line, 2));
        $normalized = strtolower(str_replace(['.', ' '], ['', '_'], $key));
        $fields[$normalized] = $value;
    }
    return $fields;
}

function valid_priority(string $priority): string
{
    return in_array($priority, ['Rendah', 'Sedang', 'Tinggi', 'Kritis'], true) ? $priority : 'Sedang';
}

function whatsapp_ticket_from_message(array $message): array
{
    $fields = parse_spap_whatsapp_format($message['body']);
    $region = $fields['wilayah'] ?? 'Nasional';
    $scope = strtolower($fields['tujuan'] ?? 'Admin Wilayah');
    $isPusat = strpos($scope, 'pusat') !== false;
    $assignedUnit = $isPusat ? 'Admin Pusat SPAP' : 'Admin Wilayah - ' . $region;
    $reporterName = $fields['nama'] ?? ($message['name'] ?: 'Pelapor WhatsApp');
    $contact = $fields['no_whatsapp'] ?? $fields['whatsapp'] ?? $message['from'];
    $subject = $fields['judul'] ?? ('Pengaduan WhatsApp dari ' . $reporterName);
    $description = $fields['kronologi'] ?? $fields['isi_pesan'] ?? $message['body'];

    return [
        'type' => 'pengaduan',
        'reporterName' => $reporterName,
        'reporterContact' => $contact,
        'channel' => 'WhatsApp',
        'region' => $region,
        'category' => 'Belum Diklasifikasi',
        'priority' => 'Sedang',
        'subject' => $subject,
        'description' => $description,
        'assignedUnit' => $assignedUnit,
        'targetLevel' => $isPusat ? 'Admin Pusat' : 'Admin Wilayah',
        'targetProvince' => $region,
        'targetName' => $assignedUnit,
    ];
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
        'targetName' => $user['target_name'] ?? null,
        'regionScope' => $user['region_scope'] ?? null,
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
    if (!is_array($session) || empty($session['user']['id'])) {
        return null;
    }

    $statement = db()->prepare('SELECT * FROM users WHERE id = ? AND status = ? LIMIT 1');
    $statement->execute([$session['user']['id'], 'active']);
    $user = $statement->fetch();
    if (!$user) {
        cache_delete(token_key($token));
        return null;
    }

    $freshUser = public_user($user);
    cache_set(token_key($token), ['user' => $freshUser], 86400);
    return $freshUser;
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

function apply_target_name_scope(array $user, array &$where, array &$params): void
{
    $targetName = trim((string) ($user['targetName'] ?? $user['target_name'] ?? ''));
    $regionScope = trim((string) ($user['regionScope'] ?? $user['region_scope'] ?? ''));
    if (($user['role'] ?? '') === 'admin') {
        return;
    }
    if ($regionScope !== '') {
        $where[] = '(region = ? OR target_province = ?)';
        $params[] = $regionScope;
        $params[] = $regionScope;
    }
    if ($targetName === '') {
        return;
    }

    $where[] = '(target_name ILIKE ? OR ? ILIKE target_name || ?)';
    $params[] = $targetName . '%';
    $params[] = $targetName;
    $params[] = '%';
}

function can_access_ticket_target(array $user, array $ticket): bool
{
    $targetName = trim((string) ($user['targetName'] ?? $user['target_name'] ?? ''));
    $regionScope = trim((string) ($user['regionScope'] ?? $user['region_scope'] ?? ''));
    if (($user['role'] ?? '') === 'admin') {
        return true;
    }
    if ($regionScope !== '') {
        $ticketRegion = trim((string) ($ticket['region'] ?? ''));
        $ticketTargetProvince = trim((string) ($ticket['target_province'] ?? ''));
        if ($ticketRegion !== $regionScope && $ticketTargetProvince !== $regionScope) {
            return false;
        }
    }
    if ($targetName === '') {
        return true;
    }

    $ticketTarget = trim((string) ($ticket['target_name'] ?? ''));
    return $ticketTarget !== ''
        && (stripos($ticketTarget, $targetName) === 0 || stripos($targetName, $ticketTarget) === 0);
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
    if ($message = validate_email_address($email)) {
        json_response(['error' => $message], 422);
        return;
    }

    $rateRemaining = login_rate_remaining($email);
    if ($rateRemaining > 0) {
        record_security_event('auth.login_rate_limited', null, null, false, ['actorEmail' => $email]);
        json_response(['error' => 'Terlalu banyak percobaan login. Coba lagi dalam ' . $rateRemaining . ' detik'], 429);
        return;
    }

    $statement = db()->prepare('SELECT * FROM users WHERE lower(email) = ? AND status = ? LIMIT 1');
    $statement->execute([$email, 'active']);
    $user = $statement->fetch();

    if (!$user || empty($user['password_hash']) || !password_verify($password, $user['password_hash'])) {
        register_failed_login($email);
        record_security_event('auth.login_failed', null, null, false, ['actorEmail' => $email]);
        json_response(['error' => 'Email atau password tidak sesuai'], 401);
        return;
    }

    clear_login_rate($email);
    db()->prepare('UPDATE users SET last_login_at = now() WHERE id = ?')->execute([$user['id']]);
    $token = bin2hex(random_bytes(32));
    cache_set(token_key($token), ['user' => public_user($user)], 86400);
    record_security_event('auth.login_success', $user, $user, true);

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
        'SELECT id, name, email, role, organization_unit, target_name, region_scope, status, created_at
         FROM users
         ORDER BY created_at DESC, name ASC'
    );
    json_response(['data' => $statement->fetchAll()]);
}

function find_user_by_id(string $id): ?array
{
    $statement = db()->prepare('SELECT * FROM users WHERE id = ? LIMIT 1');
    $statement->execute([$id]);
    $user = $statement->fetch();
    return $user ?: null;
}

function active_admin_count(): int
{
    $statement = db()->query("SELECT count(*)::int AS total FROM users WHERE role = 'admin' AND status = 'active'");
    $row = $statement->fetch();
    return (int) ($row['total'] ?? 0);
}

function would_remove_last_active_admin(array $target, string $nextRole, string $nextStatus): bool
{
    if (($target['role'] ?? '') !== 'admin' || ($target['status'] ?? '') !== 'active') {
        return false;
    }
    if ($nextRole === 'admin' && $nextStatus === 'active') {
        return false;
    }
    return active_admin_count() <= 1;
}

function create_user(): void
{
    $actor = require_admin();
    if (!$actor) {
        return;
    }

    $input = input_json();
    $name = trim($input['name'] ?? '');
    $email = strtolower(trim($input['email'] ?? ''));
    $role = trim((string) ($input['role'] ?? 'operator'));
    $unit = trim((string) ($input['organizationUnit'] ?? 'SPAP'));
    $targetName = trim($input['targetName'] ?? '');
    $regionScope = trim($input['regionScope'] ?? '');
    $status = trim((string) ($input['status'] ?? 'active'));
    $password = (string) ($input['password'] ?? '');

    if (!$name || !$email) {
        json_response(['error' => 'Nama dan email wajib diisi'], 422);
        return;
    }
    foreach ([validate_user_name($name), validate_email_address($email), validate_role_and_status($role, $status), validate_password_policy($password)] as $message) {
        if ($message) {
            json_response(['error' => $message], 422);
            return;
        }
    }

    $statement = db()->prepare(
        'INSERT INTO users (name, email, password_hash, role, organization_unit, target_name, region_scope, status, password_changed_at)
         VALUES (?, ?, ?, ?, ?, ?, ?, ?, now())
         ON CONFLICT (email) DO UPDATE
         SET name = EXCLUDED.name, role = EXCLUDED.role, organization_unit = EXCLUDED.organization_unit, target_name = EXCLUDED.target_name, region_scope = EXCLUDED.region_scope, status = EXCLUDED.status
         RETURNING id, name, email, role, organization_unit, target_name, region_scope, status, created_at'
    );
    $statement->execute([$name, $email, password_hash($password, PASSWORD_BCRYPT), $role, $unit, $targetName ?: null, $regionScope ?: null, $status]);
    $user = $statement->fetch();
    record_security_event('user.created_or_updated', $actor, $user, true, ['role' => $role, 'status' => $status]);
    json_response(['data' => $user], 201);
}

function update_user(string $id): void
{
    $actor = require_admin();
    if (!$actor) {
        return;
    }

    $input = input_json();
    $current = find_user_by_id($id);
    if (!$current) {
        json_response(['error' => 'User not found'], 404);
        return;
    }

    $nextRole = array_key_exists('role', $input) ? trim((string) $input['role']) : (string) $current['role'];
    $nextStatus = array_key_exists('status', $input) ? trim((string) $input['status']) : (string) $current['status'];
    if ($message = validate_role_and_status($nextRole, $nextStatus)) {
        json_response(['error' => $message], 422);
        return;
    }
    if (array_key_exists('name', $input) && ($message = validate_user_name(trim((string) $input['name'])))) {
        json_response(['error' => $message], 422);
        return;
    }
    if (($actor['id'] ?? '') === $id && ($nextRole !== 'admin' || $nextStatus !== 'active')) {
        json_response(['error' => 'Admin tidak dapat menonaktifkan atau menurunkan role akun sendiri'], 422);
        return;
    }
    if (would_remove_last_active_admin($current, $nextRole, $nextStatus)) {
        json_response(['error' => 'Minimal harus ada satu admin aktif'], 422);
        return;
    }

    $targetNameProvided = array_key_exists('targetName', $input);
    $targetName = $targetNameProvided ? (trim((string) $input['targetName']) ?: null) : null;
    $regionScopeProvided = array_key_exists('regionScope', $input);
    $regionScope = $regionScopeProvided ? (trim((string) $input['regionScope']) ?: null) : null;
    $statement = db()->prepare(
        'UPDATE users
         SET name = COALESCE(?, name),
             role = COALESCE(?, role),
             organization_unit = COALESCE(?, organization_unit),
             target_name = CASE WHEN ? = 1 THEN ? ELSE target_name END,
             region_scope = CASE WHEN ? = 1 THEN ? ELSE region_scope END,
             status = COALESCE(?, status)
         WHERE id = ?
         RETURNING id, name, email, role, organization_unit, target_name, region_scope, status, created_at'
    );
    $statement->execute([
        $input['name'] ?? null,
        $input['role'] ?? null,
        $input['organizationUnit'] ?? null,
        $targetNameProvided ? 1 : 0,
        $targetName,
        $regionScopeProvided ? 1 : 0,
        $regionScope,
        $input['status'] ?? null,
        $id,
    ]);
    $user = $statement->fetch();
    record_security_event('user.updated', $actor, $user, true, ['changedFields' => array_keys($input)]);
    json_response(['data' => $user]);
}

function reset_user_password(string $id): void
{
    $actor = require_admin();
    if (!$actor) {
        return;
    }

    $input = input_json();
    $password = trim($input['password'] ?? '');
    if ($message = validate_password_policy($password)) {
        json_response(['error' => $message], 422);
        return;
    }

    $statement = db()->prepare(
        'UPDATE users
         SET password_hash = ?, password_changed_at = now()
         WHERE id = ?
         RETURNING id, name, email, role, organization_unit, target_name, region_scope, status, created_at'
    );
    $statement->execute([password_hash($password, PASSWORD_BCRYPT), $id]);
    $user = $statement->fetch();
    if (!$user) {
        json_response(['error' => 'User not found'], 404);
        return;
    }
    record_security_event('user.password_reset', $actor, $user, true);
    json_response(['data' => $user]);
}

function list_security_events(): void
{
    if (!require_admin()) {
        return;
    }

    $statement = db()->query(
        'SELECT id, event_type, actor_email, target_email, ip_address, success, metadata, created_at
         FROM security_events
         ORDER BY created_at DESC
         LIMIT 100'
    );
    json_response(['data' => $statement->fetchAll()]);
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
    $user = session_user();
    $token = bearer_token();
    if ($token) {
        cache_delete(token_key($token));
    }
    if ($user) {
        record_security_event('auth.logout', $user, $user, true);
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
    apply_target_name_scope($user, $where, $params);

    $scope = ($user['role'] ?? '') === 'admin'
        ? 'admin'
        : ('target:' . md5((string) ($user['targetName'] ?? $user['target_name'] ?? 'all') . '|region:' . (string) ($user['regionScope'] ?? $user['region_scope'] ?? 'all')));
    $cacheKey = 'tickets:' . $scope . ':' . ($type ?: 'all') . ':' . ($status ?: 'all') . ':' . ($region ?: 'all') . ':' . ($q ?: '');

    $cached = cache_get($cacheKey);
    if ($cached) {
        json_response($cached);
        return;
    }

    $sql = 'SELECT public_id, type, reporter_name, reporter_contact, channel, region, category, priority, status,
                   subject, description, assigned_unit, target_level, target_dapil, target_province, target_city, target_name,
                   sla_due_at, resolved_at, created_at, updated_at
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

    $created = insert_ticket_record($ticket, $actor['name'] ?? 'Operator SPAP', 'Tiket dibuat dari aplikasi SPAP');
    json_response(['data' => $created], 201);
}

function create_public_complaint(): void
{
    $input = input_json();
    $type = ($input['type'] ?? 'pengaduan') === 'aspirasi' ? 'aspirasi' : 'pengaduan';
    $name = trim((string) ($input['reporterName'] ?? ''));
    $phone = trim((string) ($input['reporterContact'] ?? ''));
    $region = trim((string) ($input['region'] ?? ''));
    $targetLevel = trim((string) ($input['targetLevel'] ?? ''));
    $targetDapil = trim((string) ($input['targetDapil'] ?? ''));
    $targetName = trim((string) ($input['targetName'] ?? ''));
    $subject = trim((string) ($input['subject'] ?? ''));
    $description = trim((string) ($input['description'] ?? ''));
    $targetScope = ($input['targetScope'] ?? 'wilayah') === 'pusat' ? 'pusat' : 'wilayah';

    if (!verify_turnstile_token(trim((string) ($input['captchaToken'] ?? '')))) {
        json_response(['error' => 'Verifikasi CAPTCHA gagal'], 403);
        return;
    }

    if (!$name || !$phone || !$region || !$subject || !$description) {
        json_response(['error' => 'Data pelapor dan isi pengaduan wajib dilengkapi'], 422);
        return;
    }

    $defaultTarget = $targetScope === 'pusat' ? 'Admin Pusat SPAP' : 'Admin Wilayah - ' . $region;
    $assignedUnit = $targetName !== ''
        ? ($targetScope === 'pusat' ? 'Admin Pusat SPAP - ' . $targetName : 'Admin Wilayah - ' . $region . ' - ' . $targetName)
        : $defaultTarget;
    $ticket = [
        'type' => $type,
        'reporterName' => $name,
        'reporterContact' => $phone,
        'channel' => 'WhatsApp Link',
        'region' => $region,
        'category' => 'Belum Diklasifikasi',
        'priority' => 'Sedang',
        'subject' => $subject,
        'description' => $description,
        'assignedUnit' => $assignedUnit,
        'targetLevel' => $targetLevel ?: null,
        'targetDapil' => $targetDapil ?: null,
        'targetProvince' => $region,
        'targetName' => $targetName ?: null,
    ];

    $eventNote = $type === 'aspirasi'
        ? 'Aspirasi dibuat dari link WhatsApp Business'
        : 'Pengaduan dibuat dari link WhatsApp Business';
    $created = insert_ticket_record($ticket, 'Form Publik WhatsApp', $eventNote);
    $whatsappNotification = send_whatsapp_ticket_received_notice($created);
    json_response([
        'data' => [
            'id' => $created['public_id'],
            'type' => $created['type'],
            'status' => $created['status'],
            'assignedUnit' => $created['assigned_unit'],
            'targetName' => $created['target_name'],
            'targetDapil' => $created['target_dapil'],
            'whatsappNotification' => $whatsappNotification,
        ],
    ], 201);
}

function public_complaints_info(): void
{
    json_response([
        'status' => 'ok',
        'message' => 'Endpoint ini menerima pengaduan publik melalui metode POST.',
        'method' => 'POST',
        'frontendForm' => '/?aduan=wa',
        'requiredFields' => [
            'type',
            'reporterName',
            'reporterContact',
            'region',
            'subject',
            'description',
        ],
        'optionalFields' => ['targetScope', 'targetLevel', 'targetDapil', 'targetName'],
        'note' => 'Tujuan penanganan bersifat opsional. Kategori dan prioritas ditentukan oleh admin/operator setelah pengaduan masuk.',
    ]);
}

function insert_ticket_record(array $ticket, string $actorName, string $eventNote): array
{
    $type = ($ticket['type'] ?? 'aspirasi') === 'pengaduan' ? 'pengaduan' : 'aspirasi';
    $prefix = $type === 'pengaduan' ? 'PEN' : 'ASP';

    $countStatement = db()->prepare('SELECT count(*)::int + 1 AS next FROM tickets WHERE type = ?');
    $countStatement->execute([$type]);
    $next = (int) $countStatement->fetchColumn();
    $publicId = $prefix . '-2026-' . str_pad((string) $next, 3, '0', STR_PAD_LEFT);

    $statement = db()->prepare(
        "INSERT INTO tickets
          (public_id, type, reporter_name, reporter_contact, channel, region, category, priority, status, subject, description, assigned_unit,
           target_level, target_dapil, target_province, target_city, target_name, sla_due_at)
         VALUES
          (?, ?, ?, ?, ?, ?, ?, ?, 'Baru', ?, ?, ?, ?, ?, ?, ?, ?, now() + (? * interval '1 hour'))
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
        $ticket['targetLevel'] ?? null,
        $ticket['targetDapil'] ?? null,
        $ticket['targetProvince'] ?? ($ticket['region'] ?? null),
        $ticket['targetCity'] ?? null,
        $ticket['targetName'] ?? null,
        $slaHours,
    ]);
    $created = $statement->fetch();

    $event = db()->prepare("INSERT INTO ticket_events (ticket_id, event_type, note, actor_name) VALUES (?, 'created', ?, ?)");
    $event->execute([$created['id'], $eventNote, $actorName]);

    cache_invalidate('tickets:');
    return $created;
}


function send_whatsapp_ticket_received_notice(array $ticket): array
{
    $phone = normalize_whatsapp_recipient((string) ($ticket['reporter_contact'] ?? ''));
    if ($phone === '') {
        return ['status' => 'skipped', 'reason' => 'Nomor WhatsApp pelapor kosong'];
    }

    if (!whatsapp_notify_enabled()) {
        return ['status' => 'skipped', 'reason' => 'WHATSAPP_NOTIFY_ENABLED belum aktif'];
    }

    $token = whatsapp_access_token();
    $phoneNumberId = whatsapp_phone_number_id();
    if ($token === '' || $phoneNumberId === '') {
        return ['status' => 'skipped', 'reason' => 'WHATSAPP_ACCESS_TOKEN atau WHATSAPP_PHONE_NUMBER_ID belum diisi'];
    }

    $typeLabel = ($ticket['type'] ?? 'pengaduan') === 'aspirasi' ? 'aspirasi' : 'pengaduan';
    $message = implode("\n", [
        'Assalamu alaikum, ' . ($ticket['reporter_name'] ?? 'Bapak/Ibu') . '.',
        '',
        'Terima kasih. ' . ucfirst($typeLabel) . ' Anda sudah diterima oleh SPAP.',
        'Nomor tiket: ' . ($ticket['public_id'] ?? '-'),
        'Status: diterima dan menunggu proses penanganan.',
        'Wilayah: ' . ($ticket['region'] ?? '-'),
        'Tujuan: ' . ($ticket['assigned_unit'] ?? 'Admin SPAP'),
        '',
        'Petugas akan melakukan verifikasi dan tindak lanjut sesuai antrean layanan.'
    ]);

    $payload = [
        'messaging_product' => 'whatsapp',
        'recipient_type' => 'individual',
        'to' => $phone,
        'type' => 'text',
        'text' => [
            'preview_url' => false,
            'body' => $message,
        ],
    ];

    $result = post_json_to_whatsapp('https://graph.facebook.com/v20.0/' . $phoneNumberId . '/messages', $payload, $token);
    if (($result['ok'] ?? false) === true) {
        log_ticket_event_by_uuid(
            (string) ($ticket['id'] ?? ''),
            'whatsapp_ack_sent',
            'Pesan WhatsApp penerimaan terkirim ke pelapor',
            'Sistem WhatsApp'
        );
        return ['status' => 'sent', 'to' => $phone];
    }

    error_log('WhatsApp notification failed: ' . json_encode($result));
    return ['status' => 'error', 'to' => $phone, 'reason' => $result['error'] ?? 'Gagal mengirim WhatsApp'];
}

function normalize_whatsapp_recipient(string $phone): string
{
    $digits = preg_replace('/\D+/', '', $phone) ?? '';
    if ($digits === '') {
        return '';
    }
    if (substr($digits, 0, 1) === '0') {
        return '62' . substr($digits, 1);
    }
    if (substr($digits, 0, 1) === '8') {
        return '62' . $digits;
    }
    return $digits;
}

function post_json_to_whatsapp(string $url, array $payload, string $token): array
{
    $body = json_encode($payload);
    if ($body === false) {
        return ['ok' => false, 'error' => 'Payload WhatsApp tidak valid'];
    }

    if (function_exists('curl_init')) {
        $ch = curl_init($url);
        curl_setopt_array($ch, [
            CURLOPT_POST => true,
            CURLOPT_HTTPHEADER => [
                'Authorization: Bearer ' . $token,
                'Content-Type: application/json',
            ],
            CURLOPT_POSTFIELDS => $body,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_TIMEOUT => 10,
        ]);
        $response = curl_exec($ch);
        $status = (int) curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $error = curl_error($ch);
        curl_close($ch);
    } else {
        $context = stream_context_create([
            'http' => [
                'method' => 'POST',
                'header' => "Authorization: Bearer {$token}\r\nContent-Type: application/json\r\n",
                'content' => $body,
                'timeout' => 10,
                'ignore_errors' => true,
            ],
        ]);
        $response = file_get_contents($url, false, $context);
        $status = 0;
        if (isset($http_response_header[0]) && preg_match('/\s(\d{3})\s/', $http_response_header[0], $matches)) {
            $status = (int) $matches[1];
        }
        $error = $response === false ? 'HTTP request failed' : '';
    }

    $decoded = is_string($response) ? json_decode($response, true) : null;
    if ($status >= 200 && $status < 300) {
        return ['ok' => true, 'status' => $status, 'response' => $decoded ?: $response];
    }

    return [
        'ok' => false,
        'status' => $status,
        'error' => $error ?: (($decoded['error']['message'] ?? null) ?: 'WhatsApp API menolak request'),
        'response' => $decoded ?: $response,
    ];
}

function log_ticket_event_by_uuid(string $ticketId, string $eventType, string $note, string $actorName): void
{
    if ($ticketId === '') {
        return;
    }
    $event = db()->prepare('INSERT INTO ticket_events (ticket_id, event_type, note, actor_name) VALUES (?, ?, ?, ?)');
    $event->execute([$ticketId, $eventType, $note, $actorName]);
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
    $targetLevel = $input['targetLevel'] ?? null;
    $targetDapil = $input['targetDapil'] ?? null;
    $targetProvince = $input['targetProvince'] ?? null;
    $targetCity = $input['targetCity'] ?? null;
    $targetName = $input['targetName'] ?? null;
    $typeStatement = db()->prepare('SELECT type, region, target_province, target_name FROM tickets WHERE public_id = ? LIMIT 1');
    $typeStatement->execute([$publicId]);
    $existingTicket = $typeStatement->fetch();
    if (!$existingTicket) {
        json_response(['error' => 'Ticket not found'], 404);
        return;
    }

    $actor = require_permission($existingTicket['type'] === 'pengaduan' ? 'pengaduan' : 'aspirasi', 'update');
    if (!$actor) {
        return;
    }
    if (!can_access_ticket_target($actor, $existingTicket)) {
        json_response(['error' => 'Forbidden'], 403);
        return;
    }

    $statement = db()->prepare(
        "UPDATE tickets
         SET status = ?,
             assigned_unit = COALESCE(?, assigned_unit),
             target_level = COALESCE(?, target_level),
             target_dapil = COALESCE(?, target_dapil),
             target_province = COALESCE(?, target_province),
             target_city = COALESCE(?, target_city),
             target_name = COALESCE(?, target_name),
             resolved_at = CASE WHEN ? = 'Selesai' THEN now() ELSE resolved_at END,
             updated_at = now()
         WHERE public_id = ?
         RETURNING *"
    );
    $statement->execute([$status, $assignedUnit, $targetLevel, $targetDapil, $targetProvince, $targetCity, $targetName, $status, $publicId]);
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
    $statement = db()->prepare('SELECT id, public_id, type, region, target_province, target_name FROM tickets WHERE public_id = ? LIMIT 1');
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
    if (!can_access_ticket_target($actor, $ticket)) {
        json_response(['error' => 'Forbidden'], 403);
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
    if (!can_access_ticket_target($actor, $ticket)) {
        json_response(['error' => 'Forbidden'], 403);
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

    $where = ["status <> 'Selesai'"];
    $params = [];
    apply_target_name_scope($user, $where, $params);

    $statement = db()->prepare(
        "SELECT public_id, type, priority, status, subject, region, assigned_unit, sla_due_at, created_at,
                CASE
                  WHEN status <> 'Selesai' AND sla_due_at < now() THEN 'overdue'
                  WHEN priority = 'Kritis' AND status <> 'Selesai' THEN 'critical'
                  WHEN status = 'Eskalasi' THEN 'escalation'
                  WHEN status = 'Baru' THEN 'new'
                  ELSE 'info'
                END AS severity
         FROM tickets
         WHERE " . implode(' AND ', $where) . "
         ORDER BY
           CASE
             WHEN sla_due_at < now() THEN 1
             WHEN priority = 'Kritis' THEN 2
             WHEN status = 'Eskalasi' THEN 3
             WHEN status = 'Baru' THEN 4
             ELSE 5
           END,
           created_at DESC,
           sla_due_at ASC
         LIMIT 20"
    );
    $statement->execute($params);

    $items = array_map(static function (array $row): array {
        $title = [
            'overdue' => 'SLA terlewati',
            'critical' => 'Tiket kritis aktif',
            'escalation' => 'Butuh eskalasi',
            'new' => $row['type'] === 'pengaduan' ? 'Pengaduan baru diajukan' : 'Aspirasi baru diajukan',
            'info' => 'Perlu tindak lanjut',
        ][$row['severity']] ?? 'Perlu tindak lanjut';

        return [
            'id' => $row['public_id'],
            'type' => $row['type'],
            'region' => $row['region'],
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
    if (!can_access_ticket_target($actor, $ticket)) {
        json_response(['error' => 'Forbidden'], 403);
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

function list_kpu_dapil(): void
{
    $path = __DIR__ . '/data/kpu-dapil-2024.json';
    if (!is_file($path)) {
        $path = dirname(__DIR__, 2) . '/data/kpu-dapil-2024.json';
    }
    if (!is_file($path)) {
        json_response(['error' => 'KPU dapil data not found'], 404);
        return;
    }

    $data = json_decode(file_get_contents($path) ?: '{}', true);
    if (!is_array($data)) {
        json_response(['error' => 'Invalid KPU dapil data'], 500);
        return;
    }

    $province = $_GET['province'] ?? null;
    if ($province) {
        $fallback = $data['default'] ?? [];
        $item = $data['provinces'][$province] ?? $fallback;
        $item['source'] = $data['source'] ?? null;
        json_response(['data' => $item]);
        return;
    }

    json_response(['data' => $data]);
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








