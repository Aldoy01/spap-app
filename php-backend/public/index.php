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

    if ($method === 'GET' && $path === '/api/osint/mentions') {
        list_osint_mentions();
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

function cache(): ?Redis
{
    static $redis = false;

    if ($redis instanceof Redis) {
        return $redis;
    }
    if ($redis === null) {
        return null;
    }

    $url = parse_url(getenv_value('REDIS_URL', 'redis://redis:6379'));
    $host = $url['host'] ?? 'redis';
    $port = $url['port'] ?? 6379;

    try {
        $client = new Redis();
        $client->connect($host, (int) $port, 1.5);
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
    ];
}

function cache_get(string $key): ?array
{
    $redis = cache();
    if (!$redis) {
        return null;
    }
    $value = $redis->get($key);
    if (!$value) {
        return null;
    }
    $decoded = json_decode($value, true);
    return is_array($decoded) ? $decoded : null;
}

function cache_set(string $key, array $payload, int $ttl = 60): void
{
    $redis = cache();
    if ($redis) {
        $redis->setex($key, $ttl, json_encode($payload, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES));
    }
}

function cache_delete(string $key): void
{
    $redis = cache();
    if ($redis) {
        $redis->del($key);
    }
}

function cache_invalidate(string $prefix): void
{
    $redis = cache();
    if (!$redis) {
        return;
    }
    foreach ($redis->keys($prefix . '*') as $key) {
        $redis->del($key);
    }
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
    $type = $_GET['type'] ?? null;
    $status = $_GET['status'] ?? null;
    $region = $_GET['region'] ?? null;
    $q = $_GET['q'] ?? null;
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

    $sql = 'SELECT public_id, type, reporter_name, channel, region, category, priority, status,
                   subject, description, assigned_unit, sla_due_at, created_at, updated_at
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
    $prefix = $type === 'pengaduan' ? 'PEN' : 'ASP';

    $countStatement = db()->prepare('SELECT count(*)::int + 1 AS next FROM tickets WHERE type = ?');
    $countStatement->execute([$type]);
    $next = (int) $countStatement->fetchColumn();
    $publicId = $prefix . '-2026-' . str_pad((string) $next, 3, '0', STR_PAD_LEFT);

    $statement = db()->prepare(
        "INSERT INTO tickets
          (public_id, type, reporter_name, reporter_contact, channel, region, category, priority, status, subject, description, assigned_unit, sla_due_at)
         VALUES
          (?, ?, ?, ?, ?, ?, ?, ?, 'Baru', ?, ?, ?, now() + interval '2 days')
         RETURNING *"
    );
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
    ]);
    $created = $statement->fetch();

    $event = db()->prepare("INSERT INTO ticket_events (ticket_id, event_type, note) VALUES (?, 'created', 'Tiket dibuat dari API PHP 7')");
    $event->execute([$created['id']]);

    cache_invalidate('tickets:');
    json_response(['data' => $created], 201);
}

function update_ticket_status(string $publicId): void
{
    $input = input_json();
    $status = $input['status'] ?? 'Diproses';

    $statement = db()->prepare(
        "UPDATE tickets
         SET status = ?,
             resolved_at = CASE WHEN ? = 'Selesai' THEN now() ELSE resolved_at END,
             updated_at = now()
         WHERE public_id = ?
         RETURNING *"
    );
    $statement->execute([$status, $status, $publicId]);
    $ticket = $statement->fetch();

    if (!$ticket) {
        json_response(['error' => 'Ticket not found'], 404);
        return;
    }

    $event = db()->prepare("INSERT INTO ticket_events (ticket_id, event_type, note, actor_name) VALUES (?, 'status_changed', ?, ?)");
    $event->execute([
        $ticket['id'],
        $input['note'] ?? 'Status diubah ke ' . $status,
        $input['actorName'] ?? 'Operator SPAP',
    ]);

    cache_invalidate('tickets:');
    json_response(['data' => $ticket]);
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
