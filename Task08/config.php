<?php
/**
 * Конфигурация приложения
 */

// Путь к базе данных
define('DB_PATH', __DIR__ . '/data/salon.db');

// Функция для получения подключения к БД
function getDB() {
    static $pdo = null;
    if ($pdo === null) {
        $pdo = new PDO('sqlite:' . DB_PATH);
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        $pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
    }
    return $pdo;
}

