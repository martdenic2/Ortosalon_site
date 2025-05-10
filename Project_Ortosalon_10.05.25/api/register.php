<?php
require_once __DIR__ . '/../config.php';

// Получаем данные
$name = $_POST['name'];
$email = $_POST['email'];
$password = password_hash($_POST['password'], PASSWORD_DEFAULT);
$phone = $_POST['phone'] ?? '';

// Сохраняем в БД
$db = new mysqli(DB_HOST, DB_USER, DB_PASSWORD, DB_NAME);
$stmt = $db->prepare("INSERT INTO clients (name, email, password, phone) VALUES (?, ?, ?, ?)");
$stmt->bind_param("ssss", $name, $email, $password, $phone);
$stmt->execute();

// Сразу авторизуем
session_start();
$_SESSION['user_id'] = $db->insert_id;
header("Location: /profile.php");
?>