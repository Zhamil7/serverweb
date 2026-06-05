<?php
if (basename($_SERVER['SCRIPT_NAME']) !== 'index.php') {
    http_response_code(404);
    exit;
}

function getInitials(string $name, string $lastname): string
{
    $firstInitial = $name !== '' ? mb_substr($name, 0, 1) . '.' : '';
    $secondInitial = $lastname !== '' ? mb_substr($lastname, 0, 1) . '.' : '';

    return trim($firstInitial . ' ' . $secondInitial);
}

function renderDelete(PDO $pdo): string
{
    $message = '';

    if (isset($_GET['id'])) {
        $id = (int)$_GET['id'];

        $stmt = $pdo->prepare("SELECT * FROM contacts WHERE id = :id");
        $stmt->execute([':id' => $id]);
        $contact = $stmt->fetch();

        if ($contact) {
            $deleteStmt = $pdo->prepare("DELETE FROM contacts WHERE id = :id");
            $deleteStmt->execute([':id' => $id]);

            $message = 'Запись с фамилией ' . e($contact['surname']) . ' удалена';
        }
    }

    $contacts = $pdo
        ->query("SELECT * FROM contacts ORDER BY surname ASC, name ASC")
        ->fetchAll();

    ob_start();
    ?>

    <h1>Удаление записи</h1>

    <?php if ($message !== ''): ?>
        <p class="message success"><?= $message ?></p>
    <?php endif; ?>

    <?php if (count($contacts) === 0): ?>
        <p class="empty">Записей пока нет.</p>
    <?php else: ?>
        <div class="delete-list">
            <?php foreach ($contacts as $contact): ?>
                <a
                    class="delete-link"
                    href="./index.php?action=delete&id=<?= (int)$contact['id'] ?>"
                >
                    <?= e($contact['surname'] . ' ' . getInitials($contact['name'], $contact['lastname'] ?? '')) ?>
                </a>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>

    <?php
    return ob_get_clean();
}