<?php
date_default_timezone_set('Europe/Moscow');
session_start();

// === Загрузка .env ===
$envPath = __DIR__ . '/.env.example';
if (file_exists($envPath)) {
    $env = parse_ini_file($envPath);
} else {
    $env = [];
}
$studentName = $env['STUDENT_NAME'] ?? 'ФИО не указано';

// === Динамическое управление списком приветствий ===
if (!isset($_SESSION['greetings'])) {
    $_SESSION['greetings'] = ['Hello, World!'];  // стартовое приветствие
}

$action = $_POST['action'] ?? '';
$redirect = true;

if ($action === 'add') {
    $_SESSION['greetings'][] = 'Hello, World!';
} elseif ($action === 'remove_last') {
    array_pop($_SESSION['greetings']);
} elseif ($action === 'clear') {
    $_SESSION['greetings'] = [];
} elseif ($action === 'remove_index' && isset($_POST['index'])) {
    $index = (int)$_POST['index'];
    if (isset($_SESSION['greetings'][$index])) {
        array_splice($_SESSION['greetings'], $index, 1);
    }
}

// После любой POST-операции перенаправляем, чтобы избежать повторной отправки
if ($redirect && $_SERVER['REQUEST_METHOD'] === 'POST') {
    header('Location: ' . $_SERVER['PHP_SELF']);
    exit;
}

// Данные для отображения
$currentDate = date("d.m.Y");
$currentTime = date("H:i:s");
$greetingsList = $_SESSION['greetings'];
?>

<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Hello, World! — Лабораторная работа</title>

    <style>
        * {
            box-sizing: border-box;
        }

        body {
            margin: 0;
            font-family: Arial, sans-serif;
            background: #f4f6f8;
            color: #1f2937;
            display: flex;
            flex-direction: column;
            min-height: 100vh;
        }

        header {
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 20px 40px;
            background: rgba(32, 62, 169, 0.6);
            border-bottom: 1px solid #e5e7eb;
        }

        .logo {
            height: 50px;
            width: auto;
        }

        .title {
            flex: 1;
            text-align: center;
            font-size: 22px;
            font-weight: 700;
            color: #ffffff;
        }

        main {
            flex: 1;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 40px;
        }

        .card {
            max-width: 700px;
            width: 100%;
            padding: 32px;
            background: #ffffff;
            border-radius: 20px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.08);
        }

        .card h1 {
            margin-top: 0;
            font-size: 36px;
            color: #111827;
            text-align: center;
        }

        /* Динамическая область со списком */
        .dynamic-area {
            background: #f9fafb;
            border-radius: 16px;
            padding: 16px;
            margin: 20px 0;
            border: 1px solid #e5e7eb;
        }

        .greetings-list {
            display: flex;
            flex-direction: column;
            gap: 12px;
        }

        .greeting-item {
            background: white;
            border-radius: 40px;
            padding: 10px 16px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            box-shadow: 0 1px 3px rgba(0,0,0,0.05);
            border: 1px solid #e2e8f0;
        }

        .greeting-text {
            font-size: 18px;
            font-weight: 600;
            color: #d5001c;
        }

        .delete-btn {
            background: none;
            border: none;
            font-size: 20px;
            cursor: pointer;
            color: #9ca3af;
            transition: 0.2s;
            padding: 0 8px;
        }
        .delete-btn:hover {
            color: #dc2626;
        }

        .empty-message {
            text-align: center;
            color: #6b7280;
            padding: 24px;
            font-style: italic;
        }

        /* Панель кнопок (формы) */
        .button-panel {
            display: flex;
            flex-wrap: wrap;
            gap: 12px;
            justify-content: center;
            margin: 20px 0 16px;
        }

        .btn {
            border: none;
            padding: 10px 24px;
            font-weight: 600;
            font-size: 14px;
            border-radius: 40px;
            cursor: pointer;
            font-family: inherit;
            background: #f3f4f6;
            color: #1f2937;
            transition: 0.2s;
        }
        .btn-primary {
            background: #2563eb;
            color: white;
        }
        .btn-primary:hover {
            background: #1d4ed8;
        }
        .btn-secondary {
            background: #e5e7eb;
        }
        .btn-secondary:hover {
            background: #d1d5db;
        }
        .btn-warning {
            background: #fee2e2;
            color: #b91c1c;
        }
        .btn-warning:hover {
            background: #fecaca;
        }

        .info {
            margin-top: 24px;
            padding-top: 16px;
            border-top: 1px solid #e5e7eb;
            text-align: center;
            color: #4b5563;
            line-height: 1.6;
        }

        footer {
            padding: 20px;
            text-align: center;
            background: #111827;
            color: #ffffff;
        }
    </style>
</head>
<body>

<header>
    <img class="logo" src="/logo.png" alt="Московский Политех">
    <div class="title">Домашняя работа: Hello, World!</div>
    <div></div>
</header>

<main>
    <section class="card">
        <h1>Hello, World!</h1>

        <!-- Динамический контент: список приветствий -->
        <div class="dynamic-area">
            <div class="greetings-list">
                <?php if (empty($greetingsList)): ?>
                    <div class="empty-message">
                        Список пуст. Нажмите «Добавить приветствие»
                    </div>
                <?php else: ?>
                    <?php foreach ($greetingsList as $index => $greeting): ?>
                        <div class="greeting-item">
                            <span class="greeting-text"><?= htmlspecialchars($greeting) ?></span>
                            <form method="POST" style="display: inline;">
                                <input type="hidden" name="action" value="remove_index">
                                <input type="hidden" name="index" value="<?= $index ?>">
                                <button type="submit" class="delete-btn" aria-label="Удалить">🗑️</button>
                            </form>
                        </div>
                    <?php endforeach; ?>
                <?php endif; ?>
            </div>
        </div>

        <!-- Кнопки управления -->
        <div class="button-panel">
            <form method="POST">
                <input type="hidden" name="action" value="add">
                <button type="submit" class="btn btn-primary">➕ Добавить «Hello, World!»</button>
            </form>
            <form method="POST">
                <input type="hidden" name="action" value="remove_last">
                <button type="submit" class="btn btn-secondary">⏪ Удалить последнее</button>
            </form>
            <form method="POST" onsubmit="return confirm('Очистить весь список?')">
                <input type="hidden" name="action" value="clear">
                <button type="submit" class="btn btn-warning">🧹 Очистить всё</button>
            </form>
        </div>

        <div class="info">
            <p>Студент: <?= htmlspecialchars($studentName) ?></p>
            <p>Сегодня: <?= $currentDate ?></p>
            <p>Текущее время сервера: <?= $currentTime ?></p>
        </div>
    </section>
</main>

<footer>
    Исматов Жамиль Максатович 251-3210
</footer>

</body>
</html>