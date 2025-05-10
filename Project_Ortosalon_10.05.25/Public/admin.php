<?php
require_once __DIR__ . '/../config.php';
session_start();
if (!isset($_SESSION['is_admin']) || !$_SESSION['is_admin']) {
    header("Location: " . BASE_URL . "/login.html");
    exit;
}
?>
<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Админ-панель | Управление услугами</title>
    <link rel="stylesheet" href="<?= BASE_URL ?>/css/main.css">
    <style>
        .service-editor {
            border: 1px solid #ddd;
            padding: 15px;
            margin-bottom: 20px;
            border-radius: 5px;
        }
        .edit-form {
            display: none;
            margin-top: 15px;
        }
        .edit-form.active {
            display: block;
        }
    </style>
</head>
<body>
    <header>
        <div class="container header-content">
            <div class="logo">Вертебра | Админ-панель</div>
            <nav>
                <ul>
                    <li><a href="<?= BASE_URL ?>/index.php">На сайт</a></li>
                    <li><a href="<?= BASE_URL ?>/logout.php">Выйти</a></li>
                </ul>
            </nav>
        </div>
    </header>

    <main class="container">
        <h1>Управление услугами</h1>
        <button id="add-service-btn" class="btn">Добавить услугу</button>
        
        <div id="services-container">
            <!-- Сюда будет загружаться список услуг -->
            <p>Загрузка услуг...</p>
        </div>
    </main>

    <script>
        // Функция для загрузки услуг
        async function loadServices() {
            try {
                const response = await fetch('<?= BASE_URL ?>/api/get_services.php');
                const services = await response.json();
                renderServices(services);
            } catch (error) {
                console.error("Ошибка загрузки услуг:", error);
                document.getElementById('services-container').innerHTML = 
                    '<p class="error">Ошибка загрузки услуг. Проверьте консоль.</p>';
            }
        }

        // Функция для отображения услуг
        function renderServices(services) {
            const container = document.getElementById('services-container');
            
            if (services.length === 0) {
                container.innerHTML = '<p>Нет доступных услуг</p>';
                return;
            }

        // В функции renderServices обновите форму:
        container.innerHTML = services.map(service => `
            <div class="service-editor" data-id="${service.id}">
             <div class="image-preview" style="background-image: url('${service.image || ''}')"></div>
             <h3>${service.name}</h3>
              <p>${service.description}</p>
              <div class="service-price">${service.price} руб.</div>
                <button class="edit-btn">Редактировать</button>
        
            <div class="edit-form">
              <input type="file" class="edit-image" accept="image/*">
              <input type="text" class="edit-name" value="${service.name}">
             <textarea class="edit-desc">${service.description}</textarea>
             <input type="number" class="edit-price" value="${service.price}">
              <button class="save-btn">Сохранить</button>
             </div>
            </div>
         `).join('');
            // Назначаем обработчики
            setupEventHandlers();
        }

        // Настройка обработчиков событий
        function setupEventHandlers() {
            // Кнопки "Редактировать"
            document.querySelectorAll('.edit-btn').forEach(btn => {
                btn.addEventListener('click', function() {
                    const editor = this.closest('.service-editor');
                    editor.querySelector('.edit-form').classList.add('active');
                });
            });

            // Кнопки "Отмена"
            document.querySelectorAll('.cancel-btn').forEach(btn => {
                btn.addEventListener('click', function() {
                    this.closest('.edit-form').classList.remove('active');
                });
            });

            // Кнопки "Сохранить"
            document.querySelectorAll('.save-btn').forEach(btn => {
                btn.addEventListener('click', async function() {
                    const editor = this.closest('.service-editor');
                    const serviceId = editor.dataset.id;
                    
                    const updatedData = {
                        id: serviceId,
                        name: editor.querySelector('.edit-name').value,
                        description: editor.querySelector('.edit-desc').value,
                        price: editor.querySelector('.edit-price').value
                    };

                    try {
                        const response = await fetch('<?= BASE_URL ?>/api/update_service.php', {
                            method: 'POST',
                            headers: { 'Content-Type': 'application/json' },
                            body: JSON.stringify(updatedData)
                        });
                        
                        const result = await response.json();
                        if (result.success) {
                            alert('Изменения сохранены!');
                            loadServices(); // Перезагружаем список
                        } else {
                            alert('Ошибка сохранения: ' + (result.error || ''));
                        }
                    } catch (error) {
                        console.error("Ошибка:", error);
                        alert('Ошибка сети при сохранении');
                    }
                });
            });

            // Кнопки "Удалить"
            document.querySelectorAll('.delete-btn').forEach(btn => {
                btn.addEventListener('click', async function() {
                    if (!confirm('Удалить эту услугу?')) return;
                    
                    const serviceId = this.closest('.service-editor').dataset.id;
                    
                    try {
                        const response = await fetch('<?= BASE_URL ?>/api/delete_service.php', {
                            method: 'POST',
                            headers: { 'Content-Type': 'application/json' },
                            body: JSON.stringify({ id: serviceId })
                        });
                        
                        const result = await response.json();
                        if (result.success) {
                            loadServices(); // Перезагружаем список
                        } else {
                            alert('Ошибка удаления');
                        }
                    } catch (error) {
                        console.error("Ошибка:", error);
                        alert('Ошибка сети при удалении');
                    }
                });
            });
        }

        // Кнопка "Добавить услугу"
        document.getElementById('add-service-btn').addEventListener('click', function() {
            const container = document.getElementById('services-container');
            container.insertAdjacentHTML('afterbegin', `
                <div class="service-editor" data-id="new">
                    <div class="edit-form active">
                        <h3>Новая услуга</h3>
                        <input type="text" class="edit-name" placeholder="Название">
                        <textarea class="edit-desc" placeholder="Описание"></textarea>
                        <input type="number" class="edit-price" placeholder="Цена">
                        <button class="save-btn">Создать</button>
                        <button class="cancel-btn">Отмена</button>
                    </div>
                </div>
            `);
            
            // Назначаем обработчики для новой формы
            setupEventHandlers();
        });

        // Загружаем услуги при старте
        document.addEventListener('DOMContentLoaded', loadServices);
    </script>
        <!-- ... остальной HTML-код админ-панели ... -->

    <!-- Только эти два скрипта для админки -->
    <script type="module" src="<?= BASE_URL ?>/js/services-api.js"></script>
    <script type="module" src="<?= BASE_URL ?>/js/admin-ui.js"></script>
    
</body>
</html>
</body>
</html>