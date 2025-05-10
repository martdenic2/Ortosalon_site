<?php
require_once __DIR__ . '/../../config.php';
header('Content-Type: application/json');

session_start();
if (!isset($_SESSION['is_admin']) || !$_SESSION['is_admin']) {
    echo json_encode(['success' => false, 'error' => 'Доступ запрещен']);
    exit;
}

$input = json_decode(file_get_contents('php://input'), true);

$db = new mysqli(DB_HOST, DB_USER, DB_PASSWORD, DB_NAME);
$stmt = $db->prepare("DELETE FROM services WHERE id=?");
$stmt->bind_param("i", $input['id']);
$success = $stmt->execute();

echo json_encode(['success' => $success]);
?>

// Назначение: Удаляет услугу по ID