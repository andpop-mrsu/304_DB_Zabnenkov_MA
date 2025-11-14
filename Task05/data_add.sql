INSERT INTO users (name, email, gender, register_date, occupation)
VALUES ('Забненков Максим', 'maxim.zab@student.university.edu', 'male', date('now'), 'student');

INSERT INTO users (name, email, gender, register_date, occupation)
VALUES ('Исаков Владимир', 'vladimir.is@university.edu', 'male', date('now'), 'student');

INSERT INTO users (name, email, gender, register_date, occupation)
VALUES ('Кижаев Роман', 'rom.kij@university.edu', 'male', date('now'), 'student');

INSERT INTO users (name, email, gender, register_date, occupation)
VALUES ('Киселев Никита', 'nikit.kis@university.edu', 'male', date('now'), 'student');

INSERT INTO users (name, email, gender, register_date, occupation)
VALUES ('Кочетов Владислав', 'vladislav.koch@university.edu', 'male', date('now'), 'student');

INSERT INTO movies (title, year)
VALUES ('Крутой Боевик 2026', 2026);

INSERT INTO movie_genres (movie_id, genre_id)
SELECT m.id, g.id 
FROM movies m, genres g 
WHERE m.title = 'Крутой Боевик 2026' AND g.name = 'Action';

INSERT INTO movies (title, year)
VALUES ('Веселая Комедия 2026', 2026);

INSERT INTO movie_genres (movie_id, genre_id)
SELECT m.id, g.id 
FROM movies m, genres g 
WHERE m.title = 'Веселая Комедия 2026' AND g.name = 'Comedy';

INSERT INTO movies (title, year)
VALUES ('Фантастическое Будущее 2026', 2026);

INSERT INTO movie_genres (movie_id, genre_id)
SELECT m.id, g.id 
FROM movies m, genres g 
WHERE m.title = 'Фантастическое Будущее 2026' AND g.name = 'Sci-Fi';

INSERT INTO ratings (user_id, movie_id, rating, timestamp)
SELECT 
    (SELECT id FROM users WHERE email = 'maxim.zab@student.university.edu'),
    (SELECT id FROM movies WHERE title = 'Крутой Боевик 2026'),
    4.5,
    strftime('%s', 'now');

INSERT INTO ratings (user_id, movie_id, rating, timestamp)
SELECT 
    (SELECT id FROM users WHERE email = 'maxim.zab@student.university.edu'),
    (SELECT id FROM movies WHERE title = 'Веселая Комедия 2026'),
    5.0,
    strftime('%s', 'now');

INSERT INTO ratings (user_id, movie_id, rating, timestamp)
SELECT 
    (SELECT id FROM users WHERE email = 'maxim.zab@student.university.edu'),
    (SELECT id FROM movies WHERE title = 'Фантастическое Будущее 2026'),
    4.0,
    strftime('%s', 'now');
