<?php
header('Content-Type: application/json');
require_once __DIR__ . '/../../config.php';

// Проверка авторизации администратора
session_start();
if (!isset($_SESSION['is_admin']) || !$_SESSION['is_admin']) {
    http_response_code(403);
    die(json_encode(['error' => 'Доступ запрещён']));
}

$db = new mysqli(DB_HOST, DB_USER, DB_PASSWORD, DB_NAME);

// Обработка загрузки изображения
$imagePath = null;
if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
    $uploadDir = __DIR__ . '/../../public/uploads/services/';
    
    // Создаем папку если не существует
    if (!file_exists($uploadDir)) {
        mkdir($uploadDir, 0755, true);
    }
    
    // Генерируем уникальное имя файла
    $extension = pathinfo($_FILES['image']['name'], PATHINFO_EXTENSION);
    $fileName = uniqid('service_') . '.' . $extension;
    $targetPath = $uploadDir . $fileName;
    
    // Проверяем тип файла
    $allowedTypes = ['image/jpeg', 'image/png', 'image/gif'];
    $fileType = mime_content_type($_FILES['image']['tmp_name']);
    
    if (in_array($fileType, $allowedTypes)) {
        if (move_uploaded_file($_FILES['image']['tmp_name'], $targetPath)) {
            $imagePath = '/uploads/services/' . $fileName;
        } else {
            http_response_code(500);
            die(json_encode(['error' => 'Ошибка загрузки изображения']));
        }
    } else {
        http_response_code(400);
        die(json_encode(['error' => 'Недопустимый тип файла']));
    }
}

// Получаем данные из запроса
$input = $_POST;
if (empty($_POST)) {
    $input = json_decode(file_get_contents('php://input'), true);
}

// Валидация данных
if (empty($input['name']) || !isset($input['price'])) {
    http_response_code(400);
    die(json_encode(['error' => 'Не заполнены обязательные поля']));
}

// Подготовка данных
$id = $input['id'] ?? null;
$name = trim($input['name']);
$price = (float)$input['price'];
$description = trim($input['description'] ?? '');

if ($id) {
    // Обновление существующей услуги
    if ($imagePath) {
        // Сначала получим текущее изображение для удаления
        $stmt = $db->prepare("SELECT image FROM services WHERE id = ?");
        $stmt->bind_param("i", $id);
        $stmt->execute();
        $oldImage = $stmt->get_result()->fetch_assoc()['image'];
        
        // Обновляем запись с новым изображением
        $stmt = $db->prepare("UPDATE services SET name=?, price=?, description=?, image=? WHERE id=?");
        $stmt->bind_param("sdssi", $name, $price, $description, $imagePath, $id);
        
        // Удаляем старое изображение
        if ($oldImage) {
            $oldImagePath = __DIR__ . '/../../public' . $oldImage;
            if (file_exists($oldImagePath)) {
                unlink($oldImagePath);
            }
        }
    } else {
        // Обновляем без изменения изображения
        $stmt = $db->prepare("UPDATE services SET name=?, price=?, description=? WHERE id=?");
        $stmt->bind_param("sdsi", $name, $price, $description, $id);
    }
} else {
    // Добавление новой услуги
    if ($imagePath) {
        $stmt = $db->prepare("INSERT INTO services (name, price, description, image) VALUES (?, ?, ?, ?)");
        $stmt->bind_param("sdss", $name, $price, $description, $imagePath);
    } else {
        $stmt = $db->prepare("INSERT INTO services (name, price, description) VALUES (?, ?, ?)");
        $stmt->bind_param("sds", $name, $price, $description);
    }
}

// Выполняем запрос
if ($stmt->execute()) {
    $serviceId = $id ?: $db->insert_id;
    echo json_encode([
        'success' => true,
        'id' => $serviceId,
        'image' => $imagePath
    ]);
} else {
    http_response_code(500);
    echo json_encode(['error' => 'Ошибка сохранения в базу данных']);
}