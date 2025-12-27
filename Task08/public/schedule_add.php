<?php
/**
 * Добавление часов работы в график
 */

require_once __DIR__ . '/../config.php';

$error = null;
$master = null;
$master_id = (int)($_GET['master_id'] ?? 0);

if (!$master_id) {
    header('Location: index.php');
    exit;
}

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
            // Вставка данных
            $stmt = $pdo->prepare("
                INSERT INTO work_schedule (master_id, day_of_week, start_time, end_time)
                VALUES (?, ?, ?, ?)
            ");
            $stmt->execute([
                $master_id,
                $day_of_week,
                $start_time,
                $end_time
            ]);
            
            header('Location: schedule_list.php?master_id=' . $master_id . '&success=Часы работы успешно добавлены');
            exit;
        }
    }
} catch (PDOException $e) {
    $error = "Ошибка базы данных: " . $e->getMessage();
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
?>
<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Добавить часы работы</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <div class="container">
        <h1>Добавить часы работы</h1>
        
        <?php if ($error !== null): ?>
            <div class="alert alert-error">
                <?= htmlspecialchars($error) ?>
            </div>
        <?php endif; ?>
        
        <?php if ($master): ?>
            <div class="master-info">
                <p><strong>Мастер:</strong> 
                    <?= htmlspecialchars($master['last_name'] . ' ' . $master['first_name'] . ' ' . ($master['middle_name'] ?? '')) ?>
                </p>
            </div>
        <?php endif; ?>
        
        <form method="POST" class="form">
            <div class="form-group">
                <label for="day_of_week">День недели *</label>
                <select id="day_of_week" name="day_of_week" required>
                    <option value="">Выберите день недели</option>
                    <?php foreach ($days_of_week as $day_num => $day_name): ?>
                        <option value="<?= $day_num ?>" 
                                <?= (($_POST['day_of_week'] ?? '') == $day_num) ? 'selected' : '' ?>>
                            <?= htmlspecialchars($day_name) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
            
            <div class="form-group">
                <label for="start_time">Время начала *</label>
                <input type="time" id="start_time" name="start_time" required 
                       value="<?= htmlspecialchars($_POST['start_time'] ?? '') ?>">
            </div>
            
            <div class="form-group">
                <label for="end_time">Время окончания *</label>
                <input type="time" id="end_time" name="end_time" required 
                       value="<?= htmlspecialchars($_POST['end_time'] ?? '') ?>">
            </div>
            
            <div class="form-actions">
                <button type="submit" class="btn btn-primary">Сохранить</button>
                <a href="schedule_list.php?master_id=<?= $master_id ?>" class="btn btn-secondary">Отмена</a>
            </div>
        </form>
    </div>
</body>
</html>

