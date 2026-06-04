<?php
$url = 'https://httpbin.org/get';   // адрес для проверки

$headers = get_headers($url);

if ($headers === false) {
    $output = "Не удалось получить заголовки с $url";
} else {
    $output = print_r($headers, true);
}
?>

<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Заголовки ответа сервера</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <header class="header">
        <img class="logo" src="logo.png" alt="МосПолитех">
        <div class="title">Результат работы get_headers()</div>
        <div class="header-spacer"></div>
    </header>

    <main class="main">
        <section class="card">
            <h1>HTTP-заголовки</h1>
            <p class="description">URL: <?= htmlspecialchars($url) ?></p>
            <textarea class="headers-result" readonly><?= htmlspecialchars($output) ?></textarea>
            <a class="link-button" href="index.php">← Вернуться к форме</a>
        </section>
    </main>

    <footer class="footer">
        задание для самостоятельной работы
    </footer>
</body>
</html>