<?php
/**
 * Добавление выполненной работы
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
        $service_name = trim($_POST['service_name'] ?? '');
        $service_date = trim($_POST['service_date'] ?? '');
        $price = trim($_POST['price'] ?? '');
        
        // Валидация
        if (empty($service_name) || empty($service_date) || empty($price)) {
            $error = "Заполните все обязательные поля.";
        } elseif (!is_numeric($price) || (float)$price < 0) {
            $error = "Стоимость должна быть положительным числом.";
        } else {
            // Вставка данных
            $stmt = $pdo->prepare("
                INSERT INTO completed_services (master_id, service_name, service_date, price)
                VALUES (?, ?, ?, ?)
            ");
            $stmt->execute([
                $master_id,
                $service_name,
                $service_date,
                (float)$price
            ]);
            
            header('Location: services_list.php?master_id=' . $master_id . '&success=Выполненная работа успешно добавлена');
            exit;
        }
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
    <title>Добавить выполненную работу</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <div class="container">
        <h1>Добавить выполненную работу</h1>
        
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
                <label for="service_name">Название услуги *</label>
                <input type="text" id="service_name" name="service_name" required 
                       value="<?= htmlspecialchars($_POST['service_name'] ?? '') ?>">
            </div>
            
            <div class="form-group">
                <label for="service_date">Дата выполнения *</label>
                <input type="date" id="service_date" name="service_date" required 
                       value="<?= htmlspecialchars($_POST['service_date'] ?? '') ?>">
            </div>
            
            <div class="form-group">
                <label for="price">Стоимость (₽) *</label>
                <input type="number" id="price" name="price" step="0.01" min="0" required 
                       value="<?= htmlspecialchars($_POST['price'] ?? '') ?>">
            </div>
            
            <div class="form-actions">
                <button type="submit" class="btn btn-primary">Сохранить</button>
                <a href="services_list.php?master_id=<?= $master_id ?>" class="btn btn-secondary">Отмена</a>
            </div>
        </form>
    </div>
</body>
</html>

