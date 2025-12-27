<?php
/**
 * Главная страница - список мастеров
 */

require_once __DIR__ . '/../config.php';

// Инициализация переменных
$masters = [];
$error = null;
$success = null;

// Обработка сообщений из GET параметров
if (isset($_GET['success'])) {
    $success = $_GET['success'];
}
if (isset($_GET['error'])) {
    $error = $_GET['error'];
}

try {
    // Подключение к базе данных
    $pdo = getDB();
    
    // Получение списка всех мастеров с их специализациями, отсортированных по фамилии
    $stmt = $pdo->query("
        SELECT 
            m.id,
            m.first_name,
            m.last_name,
            m.middle_name,
            s.name AS specialization_name
        FROM masters m
        INNER JOIN specializations s ON m.specialization_id = s.id
        ORDER BY m.last_name, m.first_name
    ");
    $masters = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
} catch (PDOException $e) {
    $error = "Ошибка базы данных: " . $e->getMessage();
}
?>
<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Список мастеров</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <div class="container">
        <h1>Список мастеров парикмахерской</h1>
        
        <?php if ($error !== null): ?>
            <div class="alert alert-error">
                <?= htmlspecialchars($error) ?>
            </div>
        <?php endif; ?>
        
        <?php if ($success !== null): ?>
            <div class="alert alert-success">
                <?= htmlspecialchars($success) ?>
            </div>
        <?php endif; ?>
        
        <?php if (!empty($masters)): ?>
            <div class="table-container">
                <table>
                    <thead>
                        <tr>
                            <th>Фамилия</th>
                            <th>Имя</th>
                            <th>Отчество</th>
                            <th>Специализация</th>
                            <th>Действия</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($masters as $master): ?>
                            <tr>
                                <td><?= htmlspecialchars($master['last_name']) ?></td>
                                <td><?= htmlspecialchars($master['first_name']) ?></td>
                                <td><?= htmlspecialchars($master['middle_name'] ?? '') ?></td>
                                <td><?= htmlspecialchars($master['specialization_name']) ?></td>
                                <td class="actions">
                                    <a href="master_edit.php?id=<?= $master['id'] ?>" class="btn btn-edit">Редактировать</a>
                                    <a href="master_delete.php?id=<?= $master['id'] ?>" class="btn btn-delete">Удалить</a>
                                    <a href="schedule_list.php?master_id=<?= $master['id'] ?>" class="btn btn-schedule">График</a>
                                    <a href="services_list.php?master_id=<?= $master['id'] ?>" class="btn btn-services">Выполненные работы</a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        <?php else: ?>
            <div class="no-data">
                Мастера не найдены.
            </div>
        <?php endif; ?>
        
        <div class="actions-bottom">
            <a href="master_add.php" class="btn btn-primary">Добавить</a>
        </div>
    </div>
</body>
</html>
