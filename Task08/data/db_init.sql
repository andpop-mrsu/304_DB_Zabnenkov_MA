-- Database initialization script for hairdressing salon management system
-- SQLite database

PRAGMA foreign_keys = ON;

-- Drop existing tables if they exist
DROP TABLE IF EXISTS completed_services;
DROP TABLE IF EXISTS work_schedule;
DROP TABLE IF EXISTS masters;
DROP TABLE IF EXISTS specializations;

-- Create specializations table (специализации мастеров)
CREATE TABLE specializations (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    name TEXT NOT NULL UNIQUE
);

-- Create masters table (мастера)
CREATE TABLE masters (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    first_name TEXT NOT NULL,
    last_name TEXT NOT NULL,
    middle_name TEXT,
    specialization_id INTEGER NOT NULL,
    FOREIGN KEY (specialization_id) REFERENCES specializations(id)
);

-- Create work_schedule table (график работы)
CREATE TABLE work_schedule (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    master_id INTEGER NOT NULL,
    day_of_week INTEGER NOT NULL CHECK(day_of_week >= 1 AND day_of_week <= 7),
    start_time TEXT NOT NULL,
    end_time TEXT NOT NULL,
    FOREIGN KEY (master_id) REFERENCES masters(id) ON DELETE CASCADE
);

-- Create completed_services table (выполненные работы)
CREATE TABLE completed_services (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    master_id INTEGER NOT NULL,
    service_name TEXT NOT NULL,
    service_date TEXT NOT NULL,
    price REAL NOT NULL CHECK(price >= 0),
    FOREIGN KEY (master_id) REFERENCES masters(id) ON DELETE CASCADE
);

-- Insert test data

-- Insert specializations
INSERT INTO specializations (name) VALUES 
    ('Парикмахер-стилист'),
    ('Колорист'),
    ('Мастер маникюра'),
    ('Мастер педикюра'),
    ('Визажист'),
    ('Барбер');

-- Insert masters
INSERT INTO masters (first_name, last_name, middle_name, specialization_id) VALUES
    ('Анна', 'Иванова', 'Сергеевна', 1),
    ('Мария', 'Петрова', 'Александровна', 1),
    ('Елена', 'Сидорова', 'Владимировна', 2),
    ('Ольга', 'Козлова', 'Игоревна', 2),
    ('Дмитрий', 'Новиков', 'Дмитриевич', 6),
    ('Сергей', 'Волков', 'Сергеевич', 6),
    ('Татьяна', 'Морозова', 'Андреевна', 3),
    ('Екатерина', 'Соколова', 'Павловна', 4);

-- Insert work schedule
INSERT INTO work_schedule (master_id, day_of_week, start_time, end_time) VALUES
    (1, 1, '09:00', '18:00'),
    (1, 2, '09:00', '18:00'),
    (1, 3, '09:00', '18:00'),
    (1, 4, '09:00', '18:00'),
    (1, 5, '09:00', '18:00'),
    (2, 1, '10:00', '19:00'),
    (2, 2, '10:00', '19:00'),
    (2, 3, '10:00', '19:00'),
    (2, 4, '10:00', '19:00'),
    (2, 5, '10:00', '19:00'),
    (3, 1, '09:00', '17:00'),
    (3, 3, '09:00', '17:00'),
    (3, 5, '09:00', '17:00'),
    (4, 2, '10:00', '18:00'),
    (4, 4, '10:00', '18:00'),
    (4, 6, '10:00', '18:00'),
    (5, 1, '08:00', '20:00'),
    (5, 2, '08:00', '20:00'),
    (5, 3, '08:00', '20:00'),
    (5, 4, '08:00', '20:00'),
    (5, 5, '08:00', '20:00'),
    (6, 1, '09:00', '19:00'),
    (6, 2, '09:00', '19:00'),
    (6, 3, '09:00', '19:00'),
    (6, 4, '09:00', '19:00'),
    (6, 5, '09:00', '19:00'),
    (7, 1, '10:00', '18:00'),
    (7, 2, '10:00', '18:00'),
    (7, 3, '10:00', '18:00'),
    (7, 4, '10:00', '18:00'),
    (7, 5, '10:00', '18:00'),
    (8, 1, '11:00', '19:00'),
    (8, 2, '11:00', '19:00'),
    (8, 3, '11:00', '19:00'),
    (8, 4, '11:00', '19:00'),
    (8, 5, '11:00', '19:00');

-- Insert completed services
INSERT INTO completed_services (master_id, service_name, service_date, price) VALUES
    (1, 'Стрижка женская', '2024-01-15', 1500.00),
    (1, 'Укладка', '2024-01-16', 800.00),
    (1, 'Стрижка женская', '2024-01-18', 1500.00),
    (2, 'Стрижка женская', '2024-01-15', 1500.00),
    (2, 'Окрашивание', '2024-01-17', 3500.00),
    (3, 'Окрашивание', '2024-01-16', 4000.00),
    (3, 'Мелирование', '2024-01-20', 4500.00),
    (4, 'Окрашивание', '2024-01-18', 3800.00),
    (5, 'Стрижка мужская', '2024-01-15', 800.00),
    (5, 'Стрижка мужская', '2024-01-16', 800.00),
    (5, 'Борода', '2024-01-17', 500.00),
    (6, 'Стрижка мужская', '2024-01-15', 900.00),
    (6, 'Стрижка + борода', '2024-01-19', 1200.00),
    (7, 'Маникюр классический', '2024-01-15', 1200.00),
    (7, 'Маникюр + покрытие', '2024-01-17', 2000.00),
    (8, 'Педикюр классический', '2024-01-16', 1500.00),
    (8, 'Педикюр + покрытие', '2024-01-20', 2500.00);

-- Create indexes for better performance
CREATE INDEX idx_masters_last_name ON masters(last_name);
CREATE INDEX idx_masters_specialization ON masters(specialization_id);
CREATE INDEX idx_work_schedule_master_id ON work_schedule(master_id);
CREATE INDEX idx_work_schedule_day ON work_schedule(day_of_week);
CREATE INDEX idx_completed_services_master_id ON completed_services(master_id);
CREATE INDEX idx_completed_services_date ON completed_services(service_date);
