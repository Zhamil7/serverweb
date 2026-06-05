<?php
if (basename($_SERVER['SCRIPT_NAME']) !== 'index.php') {
    http_response_code(404);
    exit;
}

function renderViewer(PDO $pdo, string $sort, int $page): string
{
    $sortMap = [
        'created' => 'id ASC',
        'surname' => 'surname ASC, name ASC',
        'birth_date' => 'birth_date ASC',
    ];

    if (!array_key_exists($sort, $sortMap)) {
        $sort = 'created';
    }

    $limit = 10;
    $offset = ($page - 1) * $limit;

    $total = (int)$pdo->query("SELECT COUNT(*) FROM contacts")->fetchColumn();
    $totalPages = max(1, (int)ceil($total / $limit));

    if ($page > $totalPages) {
        $page = $totalPages;
        $offset = ($page - 1) * $limit;
    }

    $sql = "
        SELECT *
        FROM contacts
        ORDER BY {$sortMap[$sort]}
        LIMIT :limit OFFSET :offset
    ";

    $stmt = $pdo->prepare($sql);
    $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
    $stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
    $stmt->execute();

    $contacts = $stmt->fetchAll();

    ob_start();
    ?>

    <h1>Записная книжка</h1>

    <?php if (count($contacts) === 0): ?>
        <p class="empty">Записей пока нет.</p>
    <?php else: ?>
        <table class="contacts-table">
            <thead>
                <tr>
                    <th>№</th>
                    <th>Фамилия</th>
                    <th>Имя</th>
                    <th>Отчество</th>
                    <th>Пол</th>
                    <th>Дата рождения</th>
                    <th>Телефон</th>
                    <th>Адрес</th>
                    <th>E-mail</th>
                    <th>Комментарий</th>
                </tr>
            </thead>

            <tbody>
                <?php foreach ($contacts as $index => $contact): ?>
                    <tr>
                        <td><?= $offset + $index + 1 ?></td>
                        <td><?= e($contact['surname']) ?></td>
                        <td><?= e($contact['name']) ?></td>
                        <td><?= e($contact['lastname'] ?? '') ?></td>
                        <td><?= e($contact['gender'] ?? '') ?></td>
                        <td><?= e($contact['birth_date'] ?? '') ?></td>
                        <td><?= e($contact['phone'] ?? '') ?></td>
                        <td><?= e($contact['address'] ?? '') ?></td>
                        <td><?= e($contact['email'] ?? '') ?></td>
                        <td><?= e($contact['comment'] ?? '') ?></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>

        <?php if ($totalPages > 1): ?>
            <div class="pagination">
                <?php for ($i = 1; $i <= $totalPages; $i++): ?>
                    <a
                        class="page-link <?= $page === $i ? 'active' : '' ?>"
                        href="./index.php?action=view&sort=<?= e($sort) ?>&page=<?= $i ?>"
                    >
                        <?= $i ?>
                    </a>
                <?php endfor; ?>
            </div>
        <?php endif; ?>
    <?php endif; ?>

    <?php
    return ob_get_clean();
}