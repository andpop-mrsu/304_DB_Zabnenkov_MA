<?php
/**
 * Удаление записи графика работы
 */

require_once __DIR__ . '/../config.php';

$error = null;
$schedule_item = null;
$master = null;
$id = (int)($_GET['id'] ?? 0);
$master_id = (int)($_GET['master_id'] ?? 0);

if (!$id) {
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
    
    // Получение данных графика
    $stmt = $pdo->prepare("
        SELECT ws.*, m.last_name, m.first_name, m.middle_name
        FROM work_schedule ws
        INNER JOIN masters m ON ws.master_id = m.id
        WHERE ws.id = ?
    ");
    $stmt->execute([$id]);
    $schedule_item = $stmt->fetch();
    
    if (!$schedule_item) {
        header('Location: index.php?error=Запись графика не найдена');
        exit;
    }
    
    if (!$master_id) {
        $master_id = $schedule_item['master_id'];
    }
    
    // Обработка подтверждения удаления
    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['confirm'])) {
        $stmt = $pdo->prepare("DELETE FROM work_schedule WHERE id = ?");
        $stmt->execute([$id]);
        
        header('Location: schedule_list.php?master_id=' . $master_id . '&success=Запись графика успешно удалена');
        exit;
    }
} catch (PDOException $e) {
    $error = "Ошибка базы данных: " . $e->getMessage();
}
?>
<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Удалить запись графика</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <div class="container">
        <h1>Удалить запись графика</h1>
        
        <?php if ($error !== null): ?>
            <div class="alert alert-error">
                <?= htmlspecialchars($error) ?>
            </div>
        <?php endif; ?>
        
        <?php if ($schedule_item): ?>
            <div class="alert alert-warning">
                Вы уверены, что хотите удалить эту запись графика?
            </div>
            
            <div class="schedule-info">
                <p><strong>Мастер:</strong> 
                    <?= htmlspecialchars($schedule_item['last_name'] . ' ' . $schedule_item['first_name'] . ' ' . ($schedule_item['middle_name'] ?? '')) ?>
                </p>
                <p><strong>День недели:</strong> <?= htmlspecialchars($days_of_week[$schedule_item['day_of_week']] ?? '') ?></p>
                <p><strong>Время:</strong> <?= htmlspecialchars($schedule_item['start_time'] . ' - ' . $schedule_item['end_time']) ?></p>
            </div>
            
            <form method="POST" class="form">
                <div class="form-actions">
                    <button type="submit" name="confirm" value="1" class="btn btn-delete">Да, удалить</button>
                    <a href="schedule_list.php?master_id=<?= $master_id ?>" class="btn btn-secondary">Отмена</a>
                </div>
            </form>
        <?php endif; ?>
    </div>
</body>
</html>

