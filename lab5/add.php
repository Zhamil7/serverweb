<?php
if (basename($_SERVER['SCRIPT_NAME']) !== 'index.php') {
    http_response_code(404);
    exit;
}

function renderAdd(PDO $pdo): string
{
    $message = '';
    $messageClass = '';

    $values = [
        'surname' => '',
        'name' => '',
        'lastname' => '',
        'gender' => '',
        'date' => '',
        'phone' => '',
        'location' => '',
        'email' => '',
        'comment' => '',
    ];

    if ($_SERVER['REQUEST_METHOD'] === 'POST' && ($_POST['form_action'] ?? '') === 'add') {
        foreach ($values as $key => $value) {
            $values[$key] = trim($_POST[$key] ?? '');
        }

        if ($values['surname'] === '' || $values['name'] === '') {
            $message = 'Ошибка: запись не добавлена';
            $messageClass = 'error';
        } else {
            try {
                $stmt = $pdo->prepare("
                    INSERT INTO contacts (
                        surname,
                        name,
                        lastname,
                        gender,
                        birth_date,
                        phone,
                        address,
                        email,
                        comment
                    )
                    VALUES (
                        :surname,
                        :name,
                        :lastname,
                        :gender,
                        :birth_date,
                        :phone,
                        :address,
                        :email,
                        :comment
                    )
                ");

                $stmt->execute([
                    ':surname' => $values['surname'],
                    ':name' => $values['name'],
                    ':lastname' => $values['lastname'],
                    ':gender' => $values['gender'],
                    ':birth_date' => $values['date'],
                    ':phone' => $values['phone'],
                    ':address' => $values['location'],
                    ':email' => $values['email'],
                    ':comment' => $values['comment'],
                ]);

                $message = 'Запись добавлена';
                $messageClass = 'success';

                foreach ($values as $key => $value) {
                    $values[$key] = '';
                }
            } catch (Throwable $error) {
                $message = 'Ошибка: запись не добавлена';
                $messageClass = 'error';
            }
        }
    }

    ob_start();
    ?>

    <h1>Добавление записи</h1>

    <?php if ($message !== ''): ?>
        <p class="message <?= e($messageClass) ?>">
            <?= e($message) ?>
        </p>
    <?php endif; ?>

    <form class="contact-form" method="post" action="./index.php?action=add">
        <input type="hidden" name="form_action" value="add">

        <div class="form-row">
            <label>Фамилия</label>
            <input type="text" name="surname" value="<?= e($values['surname']) ?>" required>
        </div>

        <div class="form-row">
            <label>Имя</label>
            <input type="text" name="name" value="<?= e($values['name']) ?>" required>
        </div>

        <div class="form-row">
            <label>Отчество</label>
            <input type="text" name="lastname" value="<?= e($values['lastname']) ?>">
        </div>

        <div class="form-row">
            <label>Пол</label>
            <select name="gender">
                <option value="">Выберите пол</option>
                <option value="мужской" <?= $values['gender'] === 'мужской' ? 'selected' : '' ?>>мужской</option>
                <option value="женский" <?= $values['gender'] === 'женский' ? 'selected' : '' ?>>женский</option>
            </select>
        </div>

        <div class="form-row">
            <label>Дата рождения</label>
            <input type="date" name="date" value="<?= e($values['date']) ?>">
        </div>

        <div class="form-row">
            <label>Телефон</label>
            <input type="text" name="phone" value="<?= e($values['phone']) ?>">
        </div>

        <div class="form-row">
            <label>Адрес</label>
            <input type="text" name="location" value="<?= e($values['location']) ?>">
        </div>

        <div class="form-row">
            <label>E-mail</label>
            <input type="email" name="email" value="<?= e($values['email']) ?>">
        </div>

        <div class="form-row">
            <label>Комментарий</label>
            <textarea name="comment"><?= e($values['comment']) ?></textarea>
        </div>

        <button class="form-btn" type="submit">Добавить</button>
    </form>

    <?php
    return ob_get_clean();
}