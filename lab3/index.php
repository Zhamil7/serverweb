<?php
$equation = 'X + 3 = 7';

$operator = '+';
$position = 'X находится слева от оператора';

$number = 3;
$result = 7;

$x = $result - $number;
?>

<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Solve the equation</title>
    <link rel="stylesheet" href="./style.css">
</head>
<body>
    <header class="header">
        <img class="logo" src="/logo.png" alt="logo">
        <div class="title">Домашняя работа: Solve the equation</div>
        <div class="header-spacer"></div>
    </header>

    <main class="main">
        <section class="card">
            <h1>Решение уравнения</h1>

            <div class="equation">
                <?= $equation ?>
            </div>

            <div class="result">
                <p>
                    <strong>Оператор:</strong>
                    <?= $operator ?>
                </p>

                <p>
                    <strong>Расположение неизвестной переменной:</strong>
                    <?= $position ?>
                </p>

                <p>
                    <strong>Решение:</strong>
                    X = <?= $result ?> - <?= $number ?>
                </p>

                <p class="answer">
                    X = <?= $x ?>
                </p>
            </div>
        </section>
    </main>

    <footer class="footer">
        Исматов Жамиль Максатович 251-3210
    </footer>
</body>
</html>