<?php
require_once __DIR__ . '/../config.php';

$email = $_POST['email'];
$password = $_POST['password'];

$db = new mysqli(DB_HOST, DB_USER, DB_PASSWORD, DB_NAME);
// Добавляем выборку is_admin
$stmt = $db->prepare("SELECT id, password, is_admin FROM clients WHERE email = ?");
$stmt->bind_param("s", $email);
$stmt->execute();
$user = $stmt->get_result()->fetch_assoc();

if ($user && password_verify($password, $user['password'])) {
    session_start();
    $_SESSION['user_id'] = $user['id'];
    $_SESSION['is_admin'] = $user['is_admin'];
    
    // Перенаправляем в зависимости от роли
    if ($user['is_admin']) {
        header("Location: /Project_Ortosalon/public/admin.php");
    } else {
        header("Location: /Project_Ortosalon/public/index.php");
    }
    exit;
} else {
    header("Location: /Project_Ortosalon/public/login.html?error=1");
    exit;
}
?>