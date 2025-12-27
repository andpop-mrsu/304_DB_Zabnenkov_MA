<?php
/**
 * Редактирование мастера
 */

require_once __DIR__ . '/../config.php';

$error = null;
$master = null;
$specializations = [];
$id = (int)($_GET['id'] ?? 0);

if (!$id) {
    header('Location: index.php');
    exit;
}

try {
    $pdo = getDB();
    
    // Получение данных мастера
    $stmt = $pdo->prepare("SELECT * FROM masters WHERE id = ?");
    $stmt->execute([$id]);
    $master = $stmt->fetch();
    
    if (!$master) {
        header('Location: index.php?error=Мастер не найден');
        exit;
    }
    
    // Получение списка специализаций
    $stmt = $pdo->query("SELECT id, name FROM specializations ORDER BY name");
    $specializations = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    // Обработка формы
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $first_name = trim($_POST['first_name'] ?? '');
        $last_name = trim($_POST['last_name'] ?? '');
        $middle_name = trim($_POST['middle_name'] ?? '');
        $specialization_id = (int)($_POST['specialization_id'] ?? 0);
        
        // Валидация
        if (empty($first_name) || empty($last_name) || empty($specialization_id)) {
            $error = "Заполните все обязательные поля.";
        } else {
            // Проверка существования специализации
            $stmt = $pdo->prepare("SELECT id FROM specializations WHERE id = ?");
            $stmt->execute([$specialization_id]);
            if (!$stmt->fetch()) {
                $error = "Выбранная специализация не существует.";
            } else {
                // Обновление данных
                $stmt = $pdo->prepare("
                    UPDATE masters 
                    SET first_name = ?, last_name = ?, middle_name = ?, specialization_id = ?
                    WHERE id = ?
                ");
                $stmt->execute([
                    $first_name,
                    $last_name,
                    $middle_name ?: null,
                    $specialization_id,
                    $id
                ]);
                
                header('Location: index.php?success=Данные мастера обновлены');
                exit;
            }
        }
        
        // Обновить данные для отображения в форме
        $master = [
            'first_name' => $first_name,
            'last_name' => $last_name,
            'middle_name' => $middle_name,
            'specialization_id' => $specialization_id
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
    <title>Редактировать мастера</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <div class="container">
        <h1>Редактировать мастера</h1>
        
        <?php if ($error !== null): ?>
            <div class="alert alert-error">
                <?= htmlspecialchars($error) ?>
            </div>
        <?php endif; ?>
        
        <?php if ($master): ?>
            <form method="POST" class="form">
                <div class="form-group">
                    <label for="last_name">Фамилия *</label>
                    <input type="text" id="last_name" name="last_name" required 
                           value="<?= htmlspecialchars($master['last_name']) ?>">
                </div>
                
                <div class="form-group">
                    <label for="first_name">Имя *</label>
                    <input type="text" id="first_name" name="first_name" required 
                           value="<?= htmlspecialchars($master['first_name']) ?>">
                </div>
                
                <div class="form-group">
                    <label for="middle_name">Отчество</label>
                    <input type="text" id="middle_name" name="middle_name" 
                           value="<?= htmlspecialchars($master['middle_name'] ?? '') ?>">
                </div>
                
                <div class="form-group">
                    <label for="specialization_id">Специализация *</label>
                    <select id="specialization_id" name="specialization_id" required>
                        <option value="">Выберите специализацию</option>
                        <?php foreach ($specializations as $spec): ?>
                            <option value="<?= $spec['id'] ?>" 
                                    <?= ($master['specialization_id'] == $spec['id']) ? 'selected' : '' ?>>
                                <?= htmlspecialchars($spec['name']) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                
                <div class="form-actions">
                    <button type="submit" class="btn btn-primary">Сохранить</button>
                    <a href="index.php" class="btn btn-secondary">Отмена</a>
                </div>
            </form>
        <?php endif; ?>
    </div>
</body>
</html>

