// admin-ui.js - Управление интерфейсом администратора услуг

import ServicesAPI from './services-api.js';

class ServicesAdminUI {
    constructor() {
        this.servicesContainer = document.getElementById('services-container');
        this.addServiceBtn = document.getElementById('add-service-btn');
        
        if (this.servicesContainer && this.addServiceBtn) {
            this.init();
        } else {
            console.error('Не найдены необходимые элементы DOM');
        }
    }

    init() {
        this.loadServices();
        this.setupEventListeners();
        console.log('Админ-панель инициализирована!');
    }

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

    showLoading() {
        this.servicesContainer.innerHTML = '<p>Загрузка услуг...</p>';
    }

    showError(message) {
        this.servicesContainer.innerHTML = `
            <div class="error">
                <p>${message}</p>
                <button class="btn retry-btn">Повторить</button>
            </div>
        `;
        this.servicesContainer.querySelector('.retry-btn').addEventListener('click', () => this.loadServices());
    }

    renderServices(services) {
        if (!services.length) {
            this.servicesContainer.innerHTML = '<p>Нет доступных услуг</p>';
            return;
        }

        this.servicesContainer.innerHTML = services.map(service => `
            <div class="service-editor" data-id="${service.id}">
                <h3>${service.name}</h3>
                <p>${service.description}</p>
                <div class="price">${service.price} руб.</div>
                <button class="btn edit-btn">Редактировать</button>
                <button class="btn delete-btn">Удалить</button>
            </div>
        `).join('');

        this.setupEventHandlers();
    }

    setupEventListeners() {
        this.addServiceBtn.addEventListener('click', () => {
            this.servicesContainer.insertAdjacentHTML('afterbegin', `
                <div class="service-editor" data-id="new">
                    <h3>Новая услуга</h3>
                    <input type="text" class="edit-name" placeholder="Название">
                    <textarea class="edit-desc" placeholder="Описание"></textarea>
                    <input type="number" class="edit-price" placeholder="Цена">
                    <button class="btn save-btn">Сохранить</button>
                    <button class="btn cancel-btn">Отмена</button>
                </div>
            `);
            this.setupEventHandlers();
        });
    }

    setupEventHandlers() {
        // Обработчики для кнопок редактирования, сохранения, удаления...
        document.querySelectorAll('.edit-btn').forEach(btn => {
            btn.addEventListener('click', function() {
                const serviceId = this.closest('.service-editor').dataset.id;
                console.log(`Редактируем услугу с ID: ${serviceId}`);
                // Здесь будет больше логики
            });
        });
    }
}

// Инициализация
document.addEventListener('DOMContentLoaded', () => {
    new ServicesAdminUI();
});