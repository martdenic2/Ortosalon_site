<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    die("Доступ запрещён");
}

$client_id = $_SESSION['user_id'];
$service_id = $_POST['service_id'];
// ... остальной код
?>