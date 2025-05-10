<?php
header('Content-Type: application/json');
require_once __DIR__ . '/../../config.php';

// 🔐 Проверка, что пользователь — админ
session_start();
if (!isset($_SESSION['is_admin']) || !$_SESSION['is_admin']) {
    http_response_code(403); // Доступ запрещён
    die(json_encode(['error' => 'Доступ только для администраторов']));
}

// Подключение к БД
$db = new mysqli(DB_HOST, DB_USER, DB_PASSWORD, DB_NAME);
if ($db->connect_error) {
    http_response_code(500);
    die(json_encode(['error' => 'Ошибка подключения к БД']));
}

// Получаем услуги
$result = $db->query("SELECT * FROM services");
if (!$result) {
    http_response_code(500);
    die(json_encode(['error' => 'Ошибка загрузки услуг']));
}

// Формируем ответ
$services = [];
while ($row = $result->fetch_assoc()) {
    $services[] = [
        'id' => $row['id'],
        'name' => $row['name'],
        'price' => $row['price'],
        'description' => $row['description'],
        'image' => $row['image'] ?? null
    ];
}

echo json_encode($services);

// Назначение: Возвращает все услуги в формате JSON php