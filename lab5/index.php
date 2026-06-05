<?php
declare(strict_types=1);

$dbDir = __DIR__ . '/data';

if (!is_dir($dbDir)) {
    mkdir($dbDir, 0777, true);
}

$pdo = new PDO('sqlite:' . $dbDir . '/notebook.sqlite');
$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
$pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);

$pdo->exec("
    CREATE TABLE IF NOT EXISTS contacts (
        id INTEGER PRIMARY KEY AUTOINCREMENT,
        surname TEXT NOT NULL,
        name TEXT NOT NULL,
        lastname TEXT,
        gender TEXT,
        birth_date TEXT,
        phone TEXT,
        address TEXT,
        email TEXT,
        comment TEXT,
        created_at TEXT DEFAULT CURRENT_TIMESTAMP
    )
");

function e(string $value): string
{
    return htmlspecialchars($value, ENT_QUOTES, 'UTF-8');
}

require_once __DIR__ . '/menu.php';
require_once __DIR__ . '/viewer.php';
require_once __DIR__ . '/add.php';
require_once __DIR__ . '/edit.php';
require_once __DIR__ . '/delete.php';

$allowedActions = ['view', 'add', 'edit', 'delete'];
$action = $_GET['action'] ?? 'view';

if (!in_array($action, $allowedActions, true)) {
    $action = 'view';
}

$sort = $_GET['sort'] ?? 'created';
$page = (int)($_GET['page'] ?? 1);

if ($page < 1) {
    $page = 1;
}

$content = '';

if ($action === 'view') {
    $content = renderViewer($pdo, $sort, $page);
}

if ($action === 'add') {
    $content = renderAdd($pdo);
}

if ($action === 'edit') {
    $content = renderEdit($pdo);
}

if ($action === 'delete') {
    $content = renderDelete($pdo);
}
?>

<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <title>Notebook</title>
    <link rel="stylesheet" href="./style.css">
</head>
<body>
    <header class="header">
        <img class="logo" src="/logo.png" alt="logo">
        <div class="title">Домашняя работа: Notebook</div>
        <div class="header-spacer"></div>
    </header>

    <main class="main">
        <?= getMenu() ?>

        <section class="content">
            <?= $content ?>
        </section>
    </main>

    <footer class="footer">
        задание для самостоятельно работы
    </footer>
</body>
</html>