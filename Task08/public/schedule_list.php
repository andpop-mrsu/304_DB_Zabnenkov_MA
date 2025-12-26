<?php
/**
 * График работы мастера
 */

require_once __DIR__ . '/../config.php';

$error = null;
$success = null;
$master = null;
$schedule = [];
$master_id = (int)($_GET['master_id'] ?? 0);

// Обработка сообщений из GET параметров
if (isset($_GET['success'])) {
    $success = $_GET['success'];
}
if (isset($_GET['error'])) {
    $error = $_GET['error'];
}

if (!$master_id) {
    header('Location: index.php');
    exit;
}

$days_of_week = [
    1 => 'Понедельник',
    2 => 'Вторник',
    3 => 'Среда',
    4 => 'Четверг',
    5 => 'Пятница',
    6 => 'Суббота',
    7 => 'Воскресенье'
];

try {
    $pdo = getDB();
    
    // Получение данных мастера
    $stmt = $pdo->prepare("
        SELECT m.*, s.name AS specialization_name
        FROM masters m
        INNER JOIN specializations s ON m.specialization_id = s.id
        WHERE m.id = ?
    ");
    $stmt->execute([$master_id]);
    $master = $stmt->fetch();
    
    if (!$master) {
        header('Location: index.php?error=Мастер не найден');
        exit;
    }
    
    // Получение графика работы мастера
    $stmt = $pdo->prepare("
        SELECT 
            id,
            day_of_week,
            start_time,
            end_time
        FROM work_schedule
        WHERE master_id = ?
        ORDER BY day_of_week, start_time
    ");
    $stmt->execute([$master_id]);
    $schedule = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
} catch (PDOException $e) {
    $error = "Ошибка базы данных: " . $e->getMessage();
}
?>
<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>График работы</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <div class="container">
        <h1>График работы</h1>
        
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
        
        <?php if ($master): ?>
            <div class="master-info">
                <h2>
                    <?= htmlspecialchars($master['last_name'] . ' ' . $master['first_name'] . ' ' . ($master['middle_name'] ?? '')) ?>
                </h2>
                <p><strong>Специализация:</strong> <?= htmlspecialchars($master['specialization_name']) ?></p>
            </div>
        <?php endif; ?>
        
        <div class="actions-top">
            <a href="schedule_add.php?master_id=<?= $master_id ?>" class="btn btn-primary">Добавить часы работы</a>
            <a href="index.php" class="btn btn-secondary">Вернуться к списку мастеров</a>
        </div>
        
        <?php if (!empty($schedule)): ?>
            <div class="table-container">
                <table>
                    <thead>
                        <tr>
                            <th>День недели</th>
                            <th>Время начала</th>
                            <th>Время окончания</th>
                            <th>Действия</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($schedule as $item): ?>
                            <tr>
                                <td><?= htmlspecialchars($days_of_week[$item['day_of_week']] ?? '') ?></td>
                                <td><?= htmlspecialchars($item['start_time']) ?></td>
                                <td><?= htmlspecialchars($item['end_time']) ?></td>
                                <td class="actions">
                                    <a href="schedule_edit.php?id=<?= $item['id'] ?>&master_id=<?= $master_id ?>" class="btn btn-edit">Редактировать</a>
                                    <a href="schedule_delete.php?id=<?= $item['id'] ?>&master_id=<?= $master_id ?>" class="btn btn-delete">Удалить</a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        <?php else: ?>
            <div class="no-data">
                График работы не найден.
            </div>
        <?php endif; ?>
    </div>
</body>
</html>

