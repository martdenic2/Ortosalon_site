// services-admin-ui.js - Управление интерфейсом администратора услуг

import ServicesAPI from './services-api.js';

class ServicesAdminUI {
    constructor() {
        this.servicesContainer = document.getElementById('services-container');
        this.addServiceBtn = document.getElementById('add-service-btn');
        this.init();
    }

    /**
     * Инициализация модуля
     */
    init() {
        this.loadServices();
        this.setupEventListeners();
    }

    /**
     * Загрузка и отображение услуг
     */
    async loadServices() {
        try {
            this.showLoading();
            const services = await ServicesAPI.getAll();
            this.renderServices(services);
        } catch (error) {
            this.showError('Ошибка загрузки услуг');
            console.error(error);
        }
    }

    /**
     * Отображение состояния загрузки
     */
    showLoading() {
        this.servicesContainer.innerHTML = `
            <div class="loading-spinner">
                <div class="spinner"></div>
                <p>Загрузка услуг...</p>
            </div>
        `;
    }

    /**
     * Отображение ошибки
     * @param {String} message 
     */
    showError(message) {
        this.servicesContainer.innerHTML = `
            <div class="error-message">
                <p>${message}</p>
                <button class="btn retry-btn">Повторить попытку</button>
            </div>
        `;

        // Назначение обработчика для кнопки повтора
        this.servicesContainer.querySelector('.retry-btn')?.addEventListener('click', () => this.loadServices());
    }

    /**
     * Отображение списка услуг
     * @param {Array} services 
     */
    renderServices(services) {
        if (!services || services.length === 0) {
            this.servicesContainer.innerHTML = '<p class="no-services">Нет доступных услуг</p>';
            return;
        }

        this.servicesContainer.innerHTML = services.map(service => this.createServiceCard(service)).join('');
        this.setupServiceCardHandlers();
    }

    /**
     * Создание HTML-карточки услуги
     * @param {Object} service 
     * @returns {String}
     */
    createServiceCard(service) {
        return `
            <div class="service-editor" data-id="${service.id}">
                <div class="service-preview">
                    <div class="image-preview" style="background-image: url('${service.image || '/images/no-image.jpg'}')"></div>
                    <div class="service-info">
                        <h3>${this.escapeHtml(service.name)}</h3>
                        <p>${this.escapeHtml(service.description)}</p>
                        <div class="service-price">${service.price} руб.</div>
                    </div>
                </div>
                <div class="service-actions">
                    <button class="btn edit-btn">Редактировать</button>
                    <button class="btn delete-btn">Удалить</button>
                </div>
                <div class="edit-form">
                    <div class="form-group">
                        <label>Изображение:</label>
                        <input type="file" class="edit-image" accept="image/jpeg, image/png">
                        <div class="image-upload-preview"></div>
                    </div>
                    <div class="form-group">
                        <label>Название:</label>
                        <input type="text" class="edit-name" value="${this.escapeHtml(service.name)}" required>
                    </div>
                    <div class="form-group">
                        <label>Описание:</label>
                        <textarea class="edit-desc" required>${this.escapeHtml(service.description)}</textarea>
                    </div>
                    <div class="form-group">
                        <label>Цена (руб.):</label>
                        <input type="number" class="edit-price" min="0" step="0.01" value="${service.price}" required>
                    </div>
                    <div class="form-actions">
                        <button class="btn save-btn">Сохранить</button>
                        <button class="btn cancel-btn">Отмена</button>
                    </div>
                </div>
            </div>
        `;
    }

    /**
     * Создание формы для новой услуги
     * @returns {String}
     */
    createNewServiceForm() {
        return `
            <div class="service-editor" data-id="new">
                <div class="edit-form active">
                    <h3>Новая услуга</h3>
                    <div class="form-group">
                        <label>Изображение:</label>
                        <input type="file" class="edit-image" accept="image/jpeg, image/png">
                        <div class="image-upload-preview"></div>
                    </div>
                    <div class="form-group">
                        <label>Название:</label>
                        <input type="text" class="edit-name" placeholder="Название услуги" required>
                    </div>
                    <div class="form-group">
                        <label>Описание:</label>
                        <textarea class="edit-desc" placeholder="Описание услуги" required></textarea>
                    </div>
                    <div class="form-group">
                        <label>Цена (руб.):</label>
                        <input type="number" class="edit-price" min="0" step="0.01" placeholder="0.00" required>
                    </div>
                    <div class="form-actions">
                        <button class="btn save-btn">Создать</button>
                        <button class="btn cancel-btn">Отмена</button>
                    </div>
                </div>
            </div>
        `;
    }

    /**
     * Назначение обработчиков событий
     */
    setupEventListeners() {
        // Кнопка добавления новой услуги
        this.addServiceBtn.addEventListener('click', () => {
            this.servicesContainer.insertAdjacentHTML('afterbegin', this.createNewServiceForm());
            this.setupServiceCardHandlers();
            this.setupImagePreviewHandlers();
        });
    }

    /**
     * Назначение обработчиков для карточек услуг
     */
    setupServiceCardHandlers() {
        // Кнопки редактирования
        document.querySelectorAll('.edit-btn').forEach(btn => {
            btn.addEventListener('click', (e) => {
                const card = e.target.closest('.service-editor');
                card.querySelector('.edit-form').classList.add('active');
                this.setupImagePreviewHandlers();
            });
        });

        // Кнопки отмены
        document.querySelectorAll('.cancel-btn').forEach(btn => {
            btn.addEventListener('click', (e) => {
                const card = e.target.closest('.service-editor');
                if (card.dataset.id === 'new') {
                    card.remove();
                } else {
                    card.querySelector('.edit-form').classList.remove('active');
                }
            });
        });

        // Кнопки сохранения
        document.querySelectorAll('.save-btn').forEach(btn => {
            btn.addEventListener('click', async (e) => {
                await this.handleSave(e);
            });
        });

        // Кнопки удаления
        document.querySelectorAll('.delete-btn').forEach(btn => {
            btn.addEventListener('click', async (e) => {
                await this.handleDelete(e);
            });
        });
    }

    /**
     * Настройка превью загружаемых изображений
     */
    setupImagePreviewHandlers() {
        document.querySelectorAll('.edit-image').forEach(input => {
            input.addEventListener('change', (e) => {
                const file = e.target.files[0];
                if (!file) return;

                const preview = e.target.closest('.form-group').querySelector('.image-upload-preview');
                const reader = new FileReader();

                reader.onload = (event) => {
                    preview.innerHTML = `<img src="${event.target.result}" alt="Превью">`;
                };

                reader.readAsDataURL(file);
            });
        });
    }

    /**
     * Обработка сохранения услуги
     * @param {Event} e 
     */
    async handleSave(e) {
        const card = e.target.closest('.service-editor');
        const form = card.querySelector('.edit-form');
        const isNew = card.dataset.id === 'new';

        // Валидация формы
        if (!this.validateForm(form)) return;

        try {
            // Сбор данных
            const serviceData = {
                id: isNew ? null : card.dataset.id,
                name: form.querySelector('.edit-name').value.trim(),
                description: form.querySelector('.edit-desc').value.trim(),
                price: parseFloat(form.querySelector('.edit-price').value)
            };

            // Загрузка изображения, если есть
            const imageInput = form.querySelector('.edit-image');
            let imageResult = null;

            if (imageInput.files[0]) {
                imageResult = await ServicesAPI.uploadImage(
                    isNew ? 'new' : card.dataset.id, 
                    imageInput.files[0]
                );
            }

            // Сохранение данных услуги
            const saveResult = await ServicesAPI.save(serviceData);

            // Обновление интерфейса
            if (isNew) {
                this.showSuccess('Услуга успешно создана');
            } else {
                this.showSuccess('Изменения сохранены');
            }
            
            this.loadServices();
        } catch (error) {
            this.showError('Ошибка сохранения: ' + error.message);
            console.error(error);
        }
    }

    /**
     * Обработка удаления услуги
     * @param {Event} e 
     */
    async handleDelete(e) {
        const card = e.target.closest('.service-editor');
        const serviceId = card.dataset.id;

        if (!confirm('Вы уверены, что хотите удалить эту услугу?')) return;

        try {
            await ServicesAPI.delete(serviceId);
            this.showSuccess('Услуга удалена');
            this.loadServices();
        } catch (error) {
            this.showError('Ошибка удаления: ' + error.message);
            console.error(error);
        }
    }

    /**
     * Валидация формы
     * @param {HTMLElement} form 
     * @returns {Boolean}
     */
    validateForm(form) {
        let isValid = true;

        // Проверка обязательных полей
        form.querySelectorAll('[required]').forEach(input => {
            if (!input.value.trim()) {
                input.classList.add('error');
                isValid = false;
            } else {
                input.classList.remove('error');
            }
        });

        // Проверка цены
        // Улучшенная проверка цены
const validatePrice = (inputElement) => {
    if (!inputElement) {
        console.error('Элемент для проверки цены не найден');
        return false;
    }

    const value = inputElement.value.trim();
    if (value === '') {
        inputElement.classList.add('error');
        return false;
    }

    const price = parseFloat(value);
    if (isNaN(price) || price < 0 || !isFinite(price)) {
        inputElement.classList.add('error');
        return false;
    }

    inputElement.classList.remove('error');
    return true;
};

// Использование:
const priceInput = form.querySelector('.edit-price');
if (!validatePrice(priceInput)) {
    isValid = false;
}

        if (!isValid) {
            this.showError('Заполните все обязательные поля корректно');
        }

        return isValid;
    }

    /**
     * Отображение сообщения об успехе
     * @param {String} message 
     */
    showSuccess(message) {
        const notification = document.createElement('div');
        notification.className = 'notification success';
        notification.textContent = message;
        document.body.appendChild(notification);

        setTimeout(() => {
            notification.remove();
        }, 3000);
    }

    /**
     * Экранирование HTML-символов
     * @param {String} str 
     * @returns {String}
     */
    escapeHtml(str) {
        const div = document.createElement('div');
        div.textContent = str;
        return div.innerHTML;
    }
}

// Инициализация при загрузке страницы
document.addEventListener('DOMContentLoaded', () => {
    new ServicesAdminUI();
});