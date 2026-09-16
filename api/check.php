<?php
header('Content-Type: application/json');

// Collect database environment variables (masked)
$rawDbUrl = $_ENV['DATABASE_URL'] ?? getenv('DATABASE_URL') ?? $_SERVER['DATABASE_URL'] ?? '';
$rawPostgresUrl = $_ENV['POSTGRES_URL'] ?? getenv('POSTGRES_URL') ?? $_SERVER['POSTGRES_URL'] ?? '';
$rawHost = $_ENV['DB_HOST'] ?? getenv('DB_HOST') ?? $_SERVER['DB_HOST'] ?? '';
$rawUser = $_ENV['DB_USERNAME'] ?? getenv('DB_USERNAME') ?? $_SERVER['DB_USERNAME'] ?? 'neondb_owner';
$rawPass = $_ENV['DB_PASSWORD'] ?? getenv('DB_PASSWORD') ?? $_SERVER['DB_PASSWORD'] ?? '';
$rawDb = $_ENV['DB_DATABASE'] ?? getenv('DB_DATABASE') ?? $_SERVER['DB_DATABASE'] ?? 'neondb';

$envInfo = [
    'has_DATABASE_URL' => !empty($rawDbUrl),
    'has_POSTGRES_URL' => !empty($rawPostgresUrl),
    'has_DB_PASSWORD' => !empty($rawPass),
    'DB_PASSWORD_length' => strlen($rawPass),
    'DB_HOST' => $rawHost ?: '(not set)',
    'DB_USERNAME' => $rawUser ?: '(not set)',
    'DB_DATABASE' => $rawDb ?: '(not set)',
];

// Determine host, user, password, db
$targetUrl = $rawDbUrl ?: $rawPostgresUrl;
if ($targetUrl) {
    // Strip quotes and channel_binding if present
    $cleanUrl = trim($targetUrl, " \t\n\r\0\x0B\"'");
    $cleanUrl = preg_replace('/([?&])channel_binding=[^&]*(&?)/', '$1', $cleanUrl);
    $cleanUrl = rtrim($cleanUrl, '?&');
    $cleanUrl = str_replace('-pooler', '', $cleanUrl);
    
    $parsed = parse_url($cleanUrl);
    $host = $parsed['host'] ?? '';
    $user = $parsed['user'] ?? $rawUser;
    $pass = isset($parsed['pass']) ? rawurldecode($parsed['pass']) : $rawPass;
    $db = isset($parsed['path']) ? ltrim($parsed['path'], '/') : $rawDb;
} else {
    $host = str_replace('-pooler', '', $rawHost);
    $user = $rawUser;
    $pass = trim($rawPass, " \t\n\r\0\x0B\"'");
    $db = $rawDb;
}

if (!$host || !$pass) {
    http_response_code(500);
    echo json_encode([
        'status' => 'CONFIG_ERROR',
        'message' => 'DATABASE_URL atau DB_PASSWORD belum disetel di Environment Variables Vercel!',
        'env_detection' => $envInfo,
    ], JSON_PRETTY_PRINT);
    exit;
}

// In Neon, if host starts with ep-..., we must pass endpoint= in password
$endpoint = '';
if (preg_match('/^(ep-[a-z0-9-]+)/', $host, $m)) {
    $endpoint = str_replace('-pooler', '', $m[1]);
}

$testPasswords = [];
if ($endpoint && !str_starts_with($pass, 'endpoint=')) {
    $testPasswords['with_endpoint'] = "endpoint={$endpoint}\${$pass}";
}
$testPasswords['raw'] = $pass;

$connected = false;
$connResult = [];
$lastError = '';

foreach ($testPasswords as $type => $pwd) {
    try {
        $dsn = "pgsql:host={$host};port=5432;dbname={$db};sslmode=require";
        $pdo = new PDO($dsn, $user, $pwd, [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_TIMEOUT => 5,
        ]);
        $connected = true;
        $usersCount = $pdo->query("SELECT count(*) FROM users")->fetchColumn();
        $connResult = [
            'connection_type' => $type,
            'host' => $host,
            'database' => $db,
            'user' => $user,
            'users_table_count' => (int) $usersCount,
        ];
        break;
    } catch (Exception $e) {
        $lastError = $e->getMessage();
    }
}

if ($connected) {
    echo json_encode([
        'status' => 'CONNECTED',
        'message' => 'Koneksi ke Neon PostgreSQL BERHASIL dari Vercel!',
        'connection' => $connResult,
        'env_detection' => $envInfo,
    ], JSON_PRETTY_PRINT);
} else {
    http_response_code(500);
    echo json_encode([
        'status' => 'FAILED',
        'error' => $lastError,
        'tried_host' => $host,
        'tried_user' => $user,
        'tried_db' => $db,
        'env_detection' => $envInfo,
    ], JSON_PRETTY_PRINT);
}
