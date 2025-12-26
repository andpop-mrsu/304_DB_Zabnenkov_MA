<?php
/**
 * Редактирование часов работы в графике
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
    $stmt = $pdo->prepare("SELECT * FROM work_schedule WHERE id = ?");
    $stmt->execute([$id]);
    $schedule_item = $stmt->fetch();
    
    if (!$schedule_item) {
        header('Location: index.php?error=Запись графика не найдена');
        exit;
    }
    
    if (!$master_id) {
        $master_id = $schedule_item['master_id'];
    }
    
    // Получение данных мастера
    $stmt = $pdo->prepare("
        SELECT m.*, s.name AS specialization_name
        FROM masters m
        INNER JOIN specializations s ON m.specialization_id = s.id
        WHERE m.id = ?
    ");
    $stmt->execute([$master_id]);
    $master = $stmt->fetch();
    
    // Обработка формы
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $day_of_week = (int)($_POST['day_of_week'] ?? 0);
        $start_time = trim($_POST['start_time'] ?? '');
        $end_time = trim($_POST['end_time'] ?? '');
        
        // Валидация
        if (empty($day_of_week) || empty($start_time) || empty($end_time)) {
            $error = "Заполните все обязательные поля.";
        } elseif ($day_of_week < 1 || $day_of_week > 7) {
            $error = "Неверный день недели.";
        } elseif ($start_time >= $end_time) {
            $error = "Время начала должно быть меньше времени окончания.";
        } else {
            // Обновление данных
            $stmt = $pdo->prepare("
                UPDATE work_schedule 
                SET day_of_week = ?, start_time = ?, end_time = ?
                WHERE id = ?
            ");
            $stmt->execute([
                $day_of_week,
                $start_time,
                $end_time,
                $id
            ]);
            
            header('Location: schedule_list.php?master_id=' . $master_id . '&success=График работы обновлен');
            exit;
        }
        
        // Обновить данные для отображения в форме
        $schedule_item = [
            'day_of_week' => $day_of_week,
            'start_time' => $start_time,
            'end_time' => $end_time
        ];
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
    <title>Редактировать график работы</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <div class="container">
        <h1>Редактировать график работы</h1>
        
        <?php if ($error !== null): ?>
            <div class="alert alert-error">
                <?= htmlspecialchars($error) ?>
            </div>
        <?php endif; ?>
        
        <?php if ($schedule_item): ?>
            <form method="POST" class="form">
                <div class="form-group">
                    <label for="day_of_week">День недели *</label>
                    <select id="day_of_week" name="day_of_week" required>
                        <option value="">Выберите день недели</option>
                        <?php foreach ($days_of_week as $day_num => $day_name): ?>
                            <option value="<?= $day_num ?>" 
                                    <?= ($schedule_item['day_of_week'] == $day_num) ? 'selected' : '' ?>>
                                <?= htmlspecialchars($day_name) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                
                <div class="form-group">
                    <label for="start_time">Время начала *</label>
                    <input type="time" id="start_time" name="start_time" required 
                           value="<?= htmlspecialchars($schedule_item['start_time']) ?>">
                </div>
                
                <div class="form-group">
                    <label for="end_time">Время окончания *</label>
                    <input type="time" id="end_time" name="end_time" required 
                           value="<?= htmlspecialchars($schedule_item['end_time']) ?>">
                </div>
                
                <div class="form-actions">
                    <button type="submit" class="btn btn-primary">Сохранить</button>
                    <a href="schedule_list.php?master_id=<?= $master_id ?>" class="btn btn-secondary">Отмена</a>
                </div>
            </form>
        <?php endif; ?>
    </div>
</body>
</html>

