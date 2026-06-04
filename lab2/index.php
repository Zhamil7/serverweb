<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Форма обратной связи</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <header class="header">
        <img class="logo" src="logo.png" alt="МосПолитех">
        <div class="title">Форма обратной связи</div>
        <div class="header-spacer"></div>
    </header>

    <main class="main">
        <section class="card">
            <h1>Обратная связь</h1>

            <form class="form" action="https://httpbin.org/post" method="POST">
                <label class="form-field">
                    <span>Имя пользователя</span>
                    <input type="text" name="username" placeholder="Иван Иванов" required>
                </label>

                <label class="form-field">
                    <span>E-mail пользователя</span>
                    <input type="email" name="email" placeholder="ivan@example.com" required>
                </label>

                <label class="form-field">
                    <span>Тип обращения</span>
                    <select name="request_type" required>
                        <option value="">-- Выберите --</option>
                        <option value="complaint">Жалоба</option>
                        <option value="suggestion">Предложение</option>
                        <option value="thanks">Благодарность</option>
                    </select>
                </label>

                <label class="form-field">
                    <span>Текст обращения</span>
                    <textarea name="message" rows="5" placeholder="Опишите ваше обращение..." required></textarea>
                </label>

                <fieldset class="checkbox-group">
                    <legend>Вариант ответа</legend>
                    <label><input type="checkbox" name="response_type[]" value="sms"> СМС</label>
                    <label><input type="checkbox" name="response_type[]" value="email"> E-mail</label>
                </fieldset>

                <button class="button" type="submit">Отправить</button>
            </form>

            <a class="link-button" href="headers.php">Перейти на 2 страницу (заголовки)</a>
        </section>
    </main>

    <footer class="footer">
        задание для самостоятельной работы
    </footer>
</body>
</html>