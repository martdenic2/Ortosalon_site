<?php
// Данные для подключения к БД
define('DB_HOST', 'localhost'); // Адрес сервера
define('DB_USER', 'root');      // Имя пользователя (по умолчанию в XAMPP)
define('DB_PASSWORD', '');      // Пароль (по умолчанию пустой)
define('DB_NAME', 'ortosalon'); // Название вашей БД

// Режим разработки (включить при тестировании)
define('DEBUG_MODE', true);

// Базовый URL сайта (для ссылок)
define('BASE_URL', 'http://localhost/Project_Ortosalon/public'); // Или твой домен

// Настройки сессии
session_set_cookie_params([
    'lifetime' => 86400,
    'path' => '/',
    'secure' => false, // На реальном сервере поменяй на true (HTTPS)
    'httponly' => true
]);

// Автозагрузка классов (если будешь использовать ООП)
spl_autoload_register(function ($class_name) {
    include __DIR__ . '/classes/' . $class_name . '.php';
});

// Старт сессии (лучше запускать в отдельных файлах при необходимости)
// session_start();
?>