<?php
/**
 * Выполненные работы мастера
 */

require_once __DIR__ . '/../config.php';

$error = null;
$success = null;
$master = null;
$services = [];
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
    
    // Получение выполненных работ мастера
    $stmt = $pdo->prepare("
        SELECT 
            id,
            service_name,
            service_date,
            price
        FROM completed_services
        WHERE master_id = ?
        ORDER BY service_date DESC
    ");
    $stmt->execute([$master_id]);
    $services = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
} catch (PDOException $e) {
    $error = "Ошибка базы данных: " . $e->getMessage();
}
?>
<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Выполненные работы</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <div class="container">
        <h1>Выполненные работы</h1>
        
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
            <a href="service_add.php?master_id=<?= $master_id ?>" class="btn btn-primary">Добавить выполненную работу</a>
            <a href="index.php" class="btn btn-secondary">Вернуться к списку мастеров</a>
        </div>
        
        <?php if (!empty($services)): ?>
            <div class="table-container">
                <table>
                    <thead>
                        <tr>
                            <th>Дата</th>
                            <th>Услуга</th>
                            <th>Стоимость</th>
                            <th>Действия</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($services as $service): ?>
                            <tr>
                                <td><?= htmlspecialchars($service['service_date']) ?></td>
                                <td><?= htmlspecialchars($service['service_name']) ?></td>
                                <td><?= number_format($service['price'], 2, '.', ' ') ?> ₽</td>
                                <td class="actions">
                                    <a href="service_edit.php?id=<?= $service['id'] ?>&master_id=<?= $master_id ?>" class="btn btn-edit">Редактировать</a>
                                    <a href="service_delete.php?id=<?= $service['id'] ?>&master_id=<?= $master_id ?>" class="btn btn-delete">Удалить</a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        <?php else: ?>
            <div class="no-data">
                Выполненные работы не найдены.
            </div>
        <?php endif; ?>
    </div>
</body>
</html>

