<?php
/**
 * Удаление мастера
 */

require_once __DIR__ . '/../config.php';

$error = null;
$master = null;
$id = (int)($_GET['id'] ?? 0);

if (!$id) {
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
    $stmt->execute([$id]);
    $master = $stmt->fetch();
    
    if (!$master) {
        header('Location: index.php?error=Мастер не найден');
        exit;
    }
    
    // Обработка подтверждения удаления
    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['confirm'])) {
        $stmt = $pdo->prepare("DELETE FROM masters WHERE id = ?");
        $stmt->execute([$id]);
        
        header('Location: index.php?success=Мастер успешно удален');
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
    <title>Удалить мастера</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <div class="container">
        <h1>Удалить мастера</h1>
        
        <?php if ($error !== null): ?>
            <div class="alert alert-error">
                <?= htmlspecialchars($error) ?>
            </div>
        <?php endif; ?>
        
        <?php if ($master): ?>
            <div class="alert alert-warning">
                Вы уверены, что хотите удалить мастера?
            </div>
            
            <div class="master-info">
                <p><strong>ФИО:</strong> 
                    <?= htmlspecialchars($master['last_name'] . ' ' . $master['first_name'] . ' ' . ($master['middle_name'] ?? '')) ?>
                </p>
                <p><strong>Специализация:</strong> <?= htmlspecialchars($master['specialization_name']) ?></p>
            </div>
            
            <form method="POST" class="form">
                <div class="form-actions">
                    <button type="submit" name="confirm" value="1" class="btn btn-delete">Да, удалить</button>
                    <a href="index.php" class="btn btn-secondary">Отмена</a>
                </div>
            </form>
        <?php endif; ?>
    </div>
</body>
</html>

