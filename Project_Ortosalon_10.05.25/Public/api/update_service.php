<?php
require_once __DIR__ . '/../../config.php';
header('Content-Type: application/json');

session_start();
if (!isset($_SESSION['is_admin'])) {
    die(json_encode(['success' => false, 'error' => 'Доступ запрещен']));
}

$input = json_decode(file_get_contents('php://input'), true);
$db = new mysqli(DB_HOST, DB_USER, DB_PASSWORD, DB_NAME);

// Обработка изображения
$imagePath = null;
if (!empty($input['image'])) {
    $uploadDir = __DIR__ . '/../../public/uploads/';
    $fileName = uniqid() . '.jpg';
    file_put_contents($uploadDir . $fileName, base64_decode($input['image']));
    $imagePath = 'uploads/' . $fileName;
}

if ($input['id'] === 'new') {
    // Создание новой услуги
    $stmt = $db->prepare("INSERT INTO services (name, description, price, image) VALUES (?, ?, ?, ?)");
    $stmt->bind_param("ssds", $input['name'], $input['description'], $input['price'], $imagePath);
} else {
    // Обновление существующей
    $stmt = $db->prepare("UPDATE services SET name=?, description=?, price=?, image=COALESCE(?, image) WHERE id=?");
    $stmt->bind_param("ssdsi", $input['name'], $input['description'], $input['price'], $imagePath, $input['id']);
}

echo json_encode(['success' => $stmt->execute()]);
?>