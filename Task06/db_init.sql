DROP TABLE IF EXISTS completed_works;
DROP TABLE IF EXISTS bookings;
DROP TABLE IF EXISTS services;
DROP TABLE IF EXISTS employee_specializations;
DROP TABLE IF EXISTS employees;

-- Создаем таблицу сотрудников (Кадровый учет)
CREATE TABLE employees (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    name TEXT NOT NULL,
    -- Специализация: 'Мужчины', 'Женщины', 'Универсал'
    specialization TEXT NOT NULL CHECK(specialization IN ('Мужчины', 'Женщины', 'Универсал')),
    salary_percentage REAL NOT NULL CHECK(salary_percentage >= 0 AND salary_percentage <= 100),
    hire_date DATE NOT NULL DEFAULT (date('now')),
    dismissal_date DATE,
    phone TEXT,
    email TEXT,
    CHECK(dismissal_date IS NULL OR dismissal_date >= hire_date)
);

-- Создаем таблицу услуг (Справочник услуг)
CREATE TABLE services (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    name TEXT NOT NULL,
    duration_minutes INTEGER NOT NULL CHECK(duration_minutes > 0),
    price REAL NOT NULL CHECK(price >= 0),
    -- Категория клиента: 'Мужчины', 'Женщины'
    client_category TEXT NOT NULL CHECK(client_category IN ('Мужчины', 'Женщины')),
    description TEXT,
    UNIQUE(name, client_category) -- Услуга с таким названием может быть только для одной категории
);

-- Создаем таблицу предварительных записей
CREATE TABLE bookings (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    client_name TEXT NOT NULL,
    client_phone TEXT NOT NULL,
    employee_id INTEGER NOT NULL,
    service_id INTEGER NOT NULL,
    booking_date DATE NOT NULL,
    booking_time TIME NOT NULL,
    status TEXT NOT NULL DEFAULT 'pending' CHECK(status IN ('pending', 'completed', 'cancelled')),
    created_at DATETIME NOT NULL DEFAULT (datetime('now')),
    notes TEXT,
    FOREIGN KEY (employee_id) REFERENCES employees(id) ON DELETE RESTRICT,
    FOREIGN KEY (service_id) REFERENCES services(id) ON DELETE RESTRICT
);

-- Создаем таблицу выполненных работ
CREATE TABLE completed_works (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    booking_id INTEGER, -- Может быть NULL, если работа была без записи
    employee_id INTEGER NOT NULL,
    service_id INTEGER NOT NULL,
    work_date DATE NOT NULL DEFAULT (date('now')),
    work_time TIME NOT NULL,
    actual_duration_minutes INTEGER NOT NULL CHECK(actual_duration_minutes > 0),
    actual_price REAL NOT NULL CHECK(actual_price >= 0),
    notes TEXT,
    FOREIGN KEY (booking_id) REFERENCES bookings(id) ON DELETE SET NULL,
    FOREIGN KEY (employee_id) REFERENCES employees(id) ON DELETE RESTRICT,
    FOREIGN KEY (service_id) REFERENCES services(id) ON DELETE RESTRICT
);

-- Заполняем тестовыми данными

-- Сотрудники (Мастера)
INSERT INTO employees (name, specialization, salary_percentage, hire_date, dismissal_date, phone, email) VALUES
('Анна Петрова', 'Женщины', 25.0, '2023-01-15', NULL, '+7-900-123-45-67', 'anna@example.com'),
('Иван Сидоров', 'Мужчины', 30.0, '2023-02-01', NULL, '+7-900-234-56-78', 'ivan@example.com'),
('Елена Козлова', 'Универсал', 28.0, '2023-03-10', NULL, '+7-900-345-67-89', 'elena@example.com'),
('Дмитрий Михайлов', 'Мужчины', 27.0, '2022-11-20', '2024-06-30', '+7-900-456-78-90', 'dmitry@example.com');

-- Услуги
-- Для мужчин
INSERT INTO services (name, duration_minutes, price, client_category, description) VALUES
('Стрижка мужская', 30, 800.0, 'Мужчины', 'Классическая стрижка'),
('Бритье бороды', 20, 500.0, 'Мужчины', 'Чистое бритье'),
('Мужская укладка', 25, 600.0, 'Мужчины', 'Укладка волос феном и средствами');
-- Для женщин
INSERT INTO services (name, duration_minutes, price, client_category, description) VALUES
('Стрижка женская', 45, 1200.0, 'Женщины', 'Стрижка по индивидуальному заказу'),
('Окрашивание волос', 90, 2500.0, 'Женщины', 'Окрашивание в один цвет'),
('Укладка женская', 30, 700.0, 'Женщины', 'Укладка феном и средствами');

-- Предварительные записи
INSERT INTO bookings (client_name, client_phone, employee_id, service_id, booking_date, booking_time, status, notes) VALUES
('Сергей Иванов', '+7-911-111-11-11', 2, 1, '2024-12-20', '10:00', 'pending', 'Нужна стрижка и бритье'),
('Мария Петрова', '+7-911-222-22-22', 1, 4, '2024-12-20', '11:00', 'pending', 'Хочу новую стрижку'),
('Александр Смирнов', '+7-911-333-33-33', 3, 2, '2024-12-20', '14:00', 'pending', 'Бритье бороды'),
('Елена Федорова', '+7-911-444-44-44', 1, 5, '2024-12-21', '09:00', 'pending', 'Окрашивание волос'),
('Николай Кузнецов', '+7-911-555-55-55', 2, 1, '2024-12-19', '15:00', 'completed', 'Работа выполнена'),
('Ольга Волкова', '+7-911-666-66-66', 3, 4, '2024-12-18', '12:00', 'cancelled', 'Клиент отменил запись');

-- Выполненные работы
INSERT INTO completed_works (booking_id, employee_id, service_id, work_date, work_time, actual_duration_minutes, actual_price, notes) VALUES
(5, 2, 1, '2024-12-19', '15:00', 35, 800.0, 'Клиент доволен'),
(NULL, 1, 4, '2024-12-18', '10:30', 50, 1200.0, 'Работа без предварительной записи'),
(NULL, 3, 2, '2024-12-18', '11:00', 22, 500.0, NULL),
(NULL, 2, 1, '2024-12-17', '14:00', 32, 800.0, 'Клиент доволен'),
(NULL, 1, 5, '2024-12-17', '16:00', 95, 2500.0, 'Окрашивание выполнено'),
(NULL, 3, 3, '2024-12-16', '10:00', 28, 600.0, 'Укладка'),
(NULL, 2, 3, '2024-12-16', '13:00', 27, 600.0, 'Укладка'),
(NULL, 1, 4, '2024-12-15', '11:30', 48, 1200.0, NULL),
(NULL, 3, 1, '2024-12-15', '14:00', 33, 800.0, 'Стрижка'),
(NULL, 2, 1, '2024-12-14', '09:00', 30, 800.0, NULL),
(NULL, 4, 1, '2024-06-15', '10:00', 30, 800.0, NULL),
(NULL, 4, 2, '2024-06-14', '14:00', 20, 500.0, 'Бритье'),
(NULL, 4, 3, '2024-06-10', '11:00', 25, 600.0, 'Укладка');