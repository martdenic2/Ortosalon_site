<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Ортопедический салон "Вертебра"</title>
    <link rel="stylesheet" href="/Project_Ortosalon/public/css/main.css">
    
</head>
<body>
    <!-- Шапка -->
    <header>
        <div class="container header-content">
            <div class="logo">Вертебра</div>
            <nav>
                <ul>
                    <li><a href="#services">Услуги</a></li>
                    <li><a href="#about">О нас</a></li>
                    <li><a href="#contacts">Контакты</a></li>
                    <li><a href="/Project_Ortosalon/public/login.html" class="btn">Вход</a></li>
                </ul>
            </nav>
        </div>
    </header>
    
    <!-- Баннер -->
    <section class="banner">
        <div class="container banner-content">
            <h1>Профессиональная ортопедическая помощь</h1>
            <p>Запишитесь онлайн и получите скидку 10% на первый прием</p>
            <a href="/book" class="btn">Записаться</a>
        </div>
    </section>
    
    <!-- Услуги -->
<section id="services" class="services">
    <div class="container">
        <h2 class="section-title">Наши услуги</h2>
        <div class="services-grid">
            <?php
            require_once __DIR__ . '/../config.php';
            $db = new mysqli(DB_HOST, DB_USER, DB_PASSWORD, DB_NAME);
            $services = $db->query("SELECT * FROM services");
            
            while ($service = $services->fetch_assoc()): ?>
                <div class="service-card">
                    <div class="service-img" style="background-image: url('<?= $service['image'] ?>');"></div>
                    <div class="service-info">
                        <h3><?= htmlspecialchars($service['name']) ?></h3>
                        <p><?= htmlspecialchars($service['description']) ?></p>
                        <div class="service-price"><?= $service['price'] ?> руб.</div>
                        <a href="/book?service=<?= $service['id'] ?>" class="btn">Записаться</a>
                    </div>
                </div>
            <?php endwhile; ?>
        </div>
    </div>
</section>
                
    
    <!-- Карта -->
    <section id="contacts" class="map-section">
        <div class="container">
            <h2 class="section-title">Мы находимся</h2>
            <div id="map"></div>
            <div class="footer-contacts">
                <p>г. Москва, ул. Ортопедическая, д. 15</p>
                <p>Телефон: +7 (495) 123-45-67</p>
                <p>Email: info@vertebra.ru</p>
            </div>
        </div>
    </section>
    
    <!-- Подвал -->
    <footer>
        <div class="container">
            <p>© 2023 Ортопедический салон "Вертебра". Все права защищены.</p>
        </div>
    </footer>
    
    <!-- Скрипты -->
    <script>
        // Для мобильного меню (добавим позже)
        document.addEventListener('DOMContentLoaded', function() {
            console.log('Сайт загружен!');
        });
    </script>
    <!-- Яндекс.Карты -->
    <script src="https://api-maps.yandex.ru/2.1/?apikey=ваш_API_ключ&lang=ru_RU"></script>
    <script>
        ymaps.ready(init);
        function init() {
            const map = new ymaps.Map("map", {
                center: [55.751574, 37.573856], // Координаты салона
                zoom: 15
            });
            const placemark = new ymaps.Placemark([55.751574, 37.573856], {
                hintContent: 'Ортопедический салон "Вертебра"'
            });
            map.geoObjects.add(placemark);
        }
    </script>
</body>
</html>