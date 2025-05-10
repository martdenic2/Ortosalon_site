<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: /login.html");
    exit;
}

require_once __DIR__ . '/../config.php';
$db = new mysqli(DB_HOST, DB_USER, DB_PASSWORD, DB_NAME);

// Получаем данные клиента
$user = $db->query("SELECT name, email, phone FROM clients WHERE id = {$_SESSION['user_id']}")->fetch_assoc();

// Получаем записи клиента
$bookings = $db->query("
    SELECT a.date, a.time, s.name 
    FROM appointments a
    JOIN services s ON a.service_id = s.id
    WHERE a.client_id = {$_SESSION['user_id']}
");
?>
<!DOCTYPE html>
<html>
<body>
    <h1>Привет, <?= htmlspecialchars($user['name']) ?>!</h1>
    
    <!-- Форма записи (только для авторизованных) -->
    <form action="/api/book.php" method="POST">
        <input type="hidden" name="client_id" value="<?= $_SESSION['user_id'] ?>">
        <!-- Поля для записи -->
        <button>Записаться</button>
    </form>

    <!-- История записей -->
    <h2>Ваши записи:</h2>
    <?php while ($booking = $bookings->fetch_assoc()): ?>
        <p><?= $booking['date'] ?>: <?= $booking['name'] ?></p>
    <?php endwhile; ?>
</body>
</html>