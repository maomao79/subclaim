<?php
declare(strict_types=1);
session_start();

$configFile = dirname(__DIR__) . '/config.php';
if (!file_exists($configFile)) {
    exit('Missing config.php. Copy config.example.php to config.php first.');
}
$config = require $configFile;

function db(): PDO {
    static $pdo = null;
    global $config;
    if ($pdo) return $pdo;

    $d = $config['db'];
    $dsn = "mysql:host={$d['host']};port={$d['port']};dbname={$d['name']};charset=utf8";
    $pdo = new PDO($dsn,$d['user'],$d['pass'],[
        PDO::ATTR_ERRMODE=>PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE=>PDO::FETCH_ASSOC,
        PDO::ATTR_EMULATE_PREPARES=>false
    ]);
    return $pdo;
}

function rows(string $sql,array $p=[]): array {
    $s=db()->prepare($sql); $s->execute($p); return $s->fetchAll();
}
function row(string $sql,array $p=[]): ?array {
    $s=db()->prepare($sql); $s->execute($p); $r=$s->fetch(); return $r?:null;
}
function val(string $sql,array $p=[]): mixed {
    $s=db()->prepare($sql); $s->execute($p); $r=$s->fetchColumn(); return $r===false?null:$r;
}
function execsql(string $sql,array $p=[]): bool {
    $s=db()->prepare($sql); return $s->execute($p);
}
function insertid(string $sql,array $p=[]): int {
    execsql($sql,$p); return (int)db()->lastInsertId();
}
function e(mixed $v): string {
    return htmlspecialchars((string)$v,ENT_QUOTES,'UTF-8');
}
function money(mixed $v): string {
    return 'GBP '.number_format((float)$v,2);
}
function user(): ?array { return $_SESSION['user'] ?? null; }
function companyId(): int { return (int)(user()['company_id'] ?? 0); }
function requireAuth(): void {
    if (!user()) { header('Location: /login.php'); exit; }
}
function csrf(): string {
    if(empty($_SESSION['csrf'])) $_SESSION['csrf']=bin2hex(random_bytes(32));
    return $_SESSION['csrf'];
}
function csrfField(): string {
    return '<input type="hidden" name="csrf" value="'.e(csrf()).'">';
}
function verifyCsrf(): void {
    if(!hash_equals($_SESSION['csrf']??'',$_POST['csrf']??'')) {
        http_response_code(419); exit('Invalid CSRF token');
    }
}
function headerHtml(string $title): void {
    $u=user(); ?>
<!doctype html><html><head>
<meta charset="utf-8"><meta name="viewport" content="width=device-width,initial-scale=1">
<title><?=e($title)?> · SubClaim</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
<style>
body{background:#f6f7f9}.card{border:0}.table td,.table th{vertical-align:middle}
@media(max-width:576px){.container{padding-left:.75rem;padding-right:.75rem}}
</style></head><body>
<?php if($u): ?>
<nav class="navbar navbar-expand-lg navbar-dark bg-dark">
<div class="container"><a class="navbar-brand" href="/">SubClaim</a>
<div class="navbar-nav me-auto">
<a class="nav-link" href="/customers.php">Customers</a>
<a class="nav-link" href="/projects.php">Projects</a>
<a class="nav-link" href="/contracts.php">Contracts</a>
<a class="nav-link" href="/claims.php">Claims</a>
</div>
<span class="navbar-text me-3"><?=e($u['company_name'])?></span>
<a class="btn btn-sm btn-outline-light" href="/logout.php">Logout</a>
</div></nav>
<?php endif; ?><main class="container py-4"><?php
}
function footerHtml(): void { ?>
</main>
<script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body></html><?php
}
