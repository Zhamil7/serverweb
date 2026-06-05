<?php
function addNumbers(float $a, float $b): float
{
    return $a + $b;
}

function subtractNumbers(float $a, float $b): float
{
    return $a - $b;
}

function multiplyNumbers(float $a, float $b): float
{
    return $a * $b;
}

function divideNumbers(float $a, float $b): float
{
    if (abs($b) < 0.0000001) {
        throw new Exception('Деление на ноль невозможно');
    }

    return $a / $b;
}

function powerNumbers(float $a, float $b): float
{
    $result = $a ** $b;

    if (!is_finite($result)) {
        throw new Exception('Некорректное возведение в степень');
    }

    return $result;
}

function squareRootNumber(float $number): float
{
    if ($number < 0) {
        throw new Exception('Корень из отрицательного числа невозможен');
    }

    return sqrt($number);
}

function naturalLogNumber(float $number): float
{
    if ($number <= 0) {
        throw new Exception('ln можно вычислить только для положительного числа');
    }

    return log($number);
}

function decimalLogNumber(float $number): float
{
    if ($number <= 0) {
        throw new Exception('log можно вычислить только для положительного числа');
    }

    return log10($number);
}

function factorialNumber(float $number): float
{
    if ($number < 0) {
        throw new Exception('Факториал отрицательного числа невозможен');
    }

    if (abs($number - round($number)) > 0.0000001) {
        throw new Exception('Факториал можно вычислить только для целого числа');
    }

    if ($number > 170) {
        throw new Exception('Слишком большое число для факториала');
    }

    $result = 1;

    for ($i = 2; $i <= (int)$number; $i++) {
        $result *= $i;
    }

    return $result;
}

function formatResult(float $number): string
{
    if (abs($number - round($number)) < 0.0000001) {
        return (string)round($number);
    }

    return rtrim(rtrim(number_format($number, 10, '.', ''), '0'), '.');
}

class CalculatorParser
{
    private string $expression = '';
    private int $position = 0;

    public function calculate(string $expression): float
    {
        $this->expression = strtolower(trim($expression));
        $this->expression = str_replace(',', '.', $this->expression);
        $this->expression = preg_replace('/\s+/', '', $this->expression);
        $this->position = 0;

        if ($this->expression === '') {
            throw new Exception('Введите выражение');
        }

        if (!preg_match('/^[0-9+\-*\/^().!a-z]+$/', $this->expression)) {
            throw new Exception('Выражение содержит недопустимые символы');
        }

        $result = $this->parseExpression();

        if ($this->position !== strlen($this->expression)) {
            throw new Exception('Некорректное выражение');
        }

        if (!is_finite($result)) {
            throw new Exception('Результат невозможно вычислить');
        }

        return $result;
    }

    private function parseExpression(): float
    {
        $result = $this->parseTerm();

        while (!$this->isEnd()) {
            if ($this->match('+')) {
                $result = addNumbers($result, $this->parseTerm());
            } elseif ($this->match('-')) {
                $result = subtractNumbers($result, $this->parseTerm());
            } else {
                break;
            }
        }

        return $result;
    }

    private function parseTerm(): float
    {
        $result = $this->parsePower();

        while (!$this->isEnd()) {
            if ($this->match('*')) {
                $result = multiplyNumbers($result, $this->parsePower());
            } elseif ($this->match('/')) {
                $result = divideNumbers($result, $this->parsePower());
            } else {
                break;
            }
        }

        return $result;
    }

    private function parsePower(): float
    {
        $result = $this->parseUnary();

        if ($this->match('^')) {
            $result = powerNumbers($result, $this->parsePower());
        }

        return $result;
    }

    private function parseUnary(): float
    {
        if ($this->match('+')) {
            return $this->parseUnary();
        }

        if ($this->match('-')) {
            return -$this->parseUnary();
        }

        return $this->parsePostfix();
    }

    private function parsePostfix(): float
    {
        $result = $this->parsePrimary();

        while ($this->match('!')) {
            $result = factorialNumber($result);
        }

        return $result;
    }

    private function parsePrimary(): float
    {
        if ($this->match('(')) {
            $result = $this->parseExpression();

            if (!$this->match(')')) {
                throw new Exception('Не закрыта скобка');
            }

            return $result;
        }

        if ($this->startsWith('sqrt')) {
            return $this->parseFunction('sqrt');
        }

        if ($this->startsWith('ln')) {
            return $this->parseFunction('ln');
        }

        if ($this->startsWith('log')) {
            return $this->parseFunction('log');
        }

        if ($this->startsWith('pi')) {
            $this->position += 2;
            return pi();
        }

        if ($this->startsWith('e')) {
            $this->position += 1;
            return exp(1);
        }

        return $this->parseNumber();
    }

    private function parseFunction(string $functionName): float
    {
        $this->position += strlen($functionName);

        if (!$this->match('(')) {
            throw new Exception('После функции должна быть открывающая скобка');
        }

        $value = $this->parseExpression();

        if (!$this->match(')')) {
            throw new Exception('После аргумента функции должна быть закрывающая скобка');
        }

        if ($functionName === 'sqrt') {
            return squareRootNumber($value);
        }

        if ($functionName === 'ln') {
            return naturalLogNumber($value);
        }

        if ($functionName === 'log') {
            return decimalLogNumber($value);
        }

        throw new Exception('Неизвестная функция');
    }

    private function parseNumber(): float
    {
        $number = '';
        $hasDigit = false;
        $hasDot = false;

        while (!$this->isEnd()) {
            $char = $this->current();

            if (ctype_digit($char)) {
                $number .= $char;
                $hasDigit = true;
                $this->position++;
                continue;
            }

            if ($char === '.' && !$hasDot) {
                $number .= $char;
                $hasDot = true;
                $this->position++;
                continue;
            }

            break;
        }

        if (!$hasDigit) {
            throw new Exception('Ожидалось число');
        }

        return (float)$number;
    }

    private function match(string $expected): bool
    {
        if ($this->current() === $expected) {
            $this->position++;
            return true;
        }

        return false;
    }

    private function startsWith(string $value): bool
    {
        return substr($this->expression, $this->position, strlen($value)) === $value;
    }

    private function current(): ?string
    {
        if ($this->isEnd()) {
            return null;
        }

        return $this->expression[$this->position];
    }

    private function isEnd(): bool
    {
        return $this->position >= strlen($this->expression);
    }
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $expression = $_POST['expression'] ?? '';

    try {
        $parser = new CalculatorParser();
        $result = $parser->calculate($expression);

        header(
            'Location: ./index.php?expression=' . urlencode($expression) .
            '&result=' . urlencode(formatResult($result))
        );
        exit;
    } catch (Exception $error) {
        header(
            'Location: ./index.php?expression=' . urlencode($expression) .
            '&error=' . urlencode($error->getMessage())
        );
        exit;
    }
}

$expressionFromGet = $_GET['expression'] ?? '';
$resultFromGet = $_GET['result'] ?? '';
$errorFromGet = $_GET['error'] ?? '';

$displayValue = $resultFromGet !== '' ? $resultFromGet : $expressionFromGet;
?>

<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Calculator</title>
    <link rel="stylesheet" href="./style.css">
</head>
<body>
    <header class="header">
        <img class="logo" src="/logo.png" alt="logo">
        <div class="title">Домашняя работа: Calculator</div>
        <div class="header-spacer"></div>
    </header>

    <main class="main">
        <section class="calculator">
            <h1>Калькулятор</h1>

            <form action="./index.php" method="POST" class="calculator-form" id="calculatorForm">
                <input
                    class="display"
                    id="display"
                    type="text"
                    name="expression"
                    value="<?= htmlspecialchars($displayValue) ?>"
                    placeholder="Введите выражение"
                    autocomplete="off"
                >

                <?php if ($errorFromGet !== ''): ?>
                    <p class="error">
                        <?= htmlspecialchars($errorFromGet) ?>
                    </p>
                <?php endif; ?>

                <div class="buttons">
                    <button type="button" id="clear">C</button>
                    <button type="button" id="backspace">←</button>
                    <button type="button" data-value="(">(</button>
                    <button type="button" data-value=")">)</button>

                    <button type="button" data-value="sqrt(">√</button>
                    <button type="button" data-value="ln(">ln</button>
                    <button type="button" data-value="log(">log</button>
                    <button type="button" data-value="!">!</button>

                    <button type="button" data-value="pi">π</button>
                    <button type="button" data-value="e">e</button>
                    <button type="button" data-value="^">^</button>
                    <button type="button" data-value="/">/</button>

                    <button type="button" data-value="7">7</button>
                    <button type="button" data-value="8">8</button>
                    <button type="button" data-value="9">9</button>
                    <button type="button" data-value="*">x</button>

                    <button type="button" data-value="4">4</button>
                    <button type="button" data-value="5">5</button>
                    <button type="button" data-value="6">6</button>
                    <button type="button" data-value="-">-</button>

                    <button type="button" data-value="1">1</button>
                    <button type="button" data-value="2">2</button>
                    <button type="button" data-value="3">3</button>
                    <button type="button" data-value="+">+</button>

                    <button type="button" data-value="0">0</button>
                    <button type="button" data-value=".">.</button>
                    <button type="submit" class="equals">=</button>
                </div>
            </form>

            <div class="help">
                <p>Примеры: <code>sqrt(9)</code>, <code>2^3</code>, <code>5!</code>, <code>ln(e)</code>, <code>log(100)</code>, <code>-(2+3)</code></p>
            </div>
        </section>
    </main>

    <footer class="footer">
        Исматов Жамиль Максатович 251-3210
    </footer>

    <script src="./script.js"></script>
</body>
</html>