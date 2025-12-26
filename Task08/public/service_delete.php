<?php
/**
 * Удаление выполненной работы
 */

require_once __DIR__ . '/../config.php';

$error = null;
$service = null;
$master = null;
$id = (int)($_GET['id'] ?? 0);
$master_id = (int)($_GET['master_id'] ?? 0);

if (!$id) {
    header('Location: index.php');
    exit;
}

try {
    $pdo = getDB();
    
    // Получение данных выполненной работы
    $stmt = $pdo->prepare("
        SELECT cs.*, m.last_name, m.first_name, m.middle_name
        FROM completed_services cs
        INNER JOIN masters m ON cs.master_id = m.id
        WHERE cs.id = ?
    ");
    $stmt->execute([$id]);
    $service = $stmt->fetch();
    
    if (!$service) {
        header('Location: index.php?error=Выполненная работа не найдена');
        exit;
    }
    
    if (!$master_id) {
        $master_id = $service['master_id'];
    }
    
    // Обработка подтверждения удаления
    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['confirm'])) {
        $stmt = $pdo->prepare("DELETE FROM completed_services WHERE id = ?");
        $stmt->execute([$id]);
        
        header('Location: services_list.php?master_id=' . $master_id . '&success=Выполненная работа успешно удалена');
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
    <title>Удалить выполненную работу</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <div class="container">
        <h1>Удалить выполненную работу</h1>
        
        <?php if ($error !== null): ?>
            <div class="alert alert-error">
                <?= htmlspecialchars($error) ?>
            </div>
        <?php endif; ?>
        
        <?php if ($service): ?>
            <div class="alert alert-warning">
                Вы уверены, что хотите удалить эту выполненную работу?
            </div>
            
            <div class="service-info">
                <p><strong>Мастер:</strong> 
                    <?= htmlspecialchars($service['last_name'] . ' ' . $service['first_name'] . ' ' . ($service['middle_name'] ?? '')) ?>
                </p>
                <p><strong>Услуга:</strong> <?= htmlspecialchars($service['service_name']) ?></p>
                <p><strong>Дата:</strong> <?= htmlspecialchars($service['service_date']) ?></p>
                <p><strong>Стоимость:</strong> <?= number_format($service['price'], 2, '.', ' ') ?> ₽</p>
            </div>
            
            <form method="POST" class="form">
                <div class="form-actions">
                    <button type="submit" name="confirm" value="1" class="btn btn-delete">Да, удалить</button>
                    <a href="services_list.php?master_id=<?= $master_id ?>" class="btn btn-secondary">Отмена</a>
                </div>
            </form>
        <?php endif; ?>
    </div>
</body>
</html>

