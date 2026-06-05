<?php
if (basename($_SERVER['SCRIPT_NAME']) !== 'index.php') {
    http_response_code(404);
    exit;
}

function getMenu(): string
{
    $action = $_GET['action'] ?? 'view';
    $sort = $_GET['sort'] ?? 'created';

    $allowedActions = ['view', 'add', 'edit', 'delete'];

    if (!in_array($action, $allowedActions, true)) {
        $action = 'view';
    }

    $items = [
        'view' => 'Просмотр',
        'add' => 'Добавление записи',
        'edit' => 'Редактирование записи',
        'delete' => 'Удаление записи',
    ];

    $sortItems = [
        'created' => 'По порядку добавления',
        'surname' => 'По фамилии',
        'birth_date' => 'По дате рождения',
    ];

    ob_start();
    ?>

    <nav class="menu">
        <div class="main-menu">
            <?php foreach ($items as $key => $label): ?>
                <a
                    class="menu-link <?= $action === $key ? 'active' : '' ?>"
                    href="./index.php?action=<?= e($key) ?>"
                >
                    <?= e($label) ?>
                </a>
            <?php endforeach; ?>
        </div>

        <?php if ($action === 'view'): ?>
            <div class="submenu">
                <?php foreach ($sortItems as $key => $label): ?>
                    <a
                        class="menu-link small <?= $sort === $key ? 'active' : '' ?>"
                        href="./index.php?action=view&sort=<?= e($key) ?>&page=1"
                    >
                        <?= e($label) ?>
                    </a>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </nav>

    <?php
    return ob_get_clean();
}