-- Добавляем услуги
INSERT INTO services (name, price, duration) VALUES
('Консультация ортопеда', 1500, 30),
('Лечебный массаж', 2500, 60),
('Изготовление стелек', 4000, 45);

-- Тестовый клиент (пароль: 123456)
INSERT INTO clients (name, phone, email, password) VALUES
('Иванов Иван', '+79161234567', 'test@example.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi');