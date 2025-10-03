# Лабораторная работа 2. Подготовка скриптов для создания таблиц и добавления данных

## Описание

Данный проект содержит утилиты для автоматического создания базы данных SQLite `db_init.db` с таблицами и данными из исходных текстовых файлов.

## Структура проекта

- `make_db_init.py` - Python-скрипт для генерации SQL-скрипта
- `make_db_init.ps1` - PowerShell-скрипт для генерации SQL-скрипта
- `db_init.bat` - Batch-скрипт для запуска процесса создания базы данных
- `README.md` - данный файл с описанием

## Требования к окружению

Для корректной работы скрипта `db_init.bat` на компьютере должны быть установлены:

### Обязательные компоненты:

1. **Python 3.x**
   - Версия: Python 3.6 или выше
   - Проверка установки: `python3 --version` или `python --version`
   - Скачать можно с [python.org](https://www.python.org/downloads/)

2. **SQLite3**
   - Версия: SQLite 3.x
   - Проверка установки: `sqlite3 --version`
   - Обычно входит в состав Python, но может потребоваться отдельная установка
   - Скачать можно с [sqlite.org](https://www.sqlite.org/download.html)

### Дополнительные требования:

3. **Исходные данные**
   - Каталог `../dataset/` должен содержать файлы:
     - `movies.csv` - данные о фильмах
     - `ratings.csv` - рейтинги фильмов
     - `tags.csv` - теги фильмов
     - `users.txt` - данные пользователей

## Использование

### Запуск на Windows:
```cmd
db_init.bat
```

### Запуск на Linux/macOS:
```bash
chmod +x db_init.bat
./db_init.bat
```

## Что происходит при выполнении

1. **Генерация SQL-скрипта**: `make_db_init.ps1` создает файл `db_init.sql` с командами:
   - Удаление существующих таблиц (если есть)
   - Создание таблиц: `movies`, `ratings`, `tags`, `users`
   - Загрузка данных из исходных файлов
   - Создание индексов для улучшения производительности

2. **Создание базы данных**: `sqlite3` выполняет SQL-скрипт и создает базу `db_init.db`

## Структура базы данных

### Таблица `movies`
- `id` (INTEGER PRIMARY KEY) - идентификатор фильма
- `title` (TEXT NOT NULL) - название фильма
- `year` (INTEGER) - год выпуска (извлекается из названия)
- `genres` (TEXT) - жанры через символ |

### Таблица `ratings`
- `id` (INTEGER PRIMARY KEY AUTOINCREMENT) - автоинкрементный ключ
- `user_id` (INTEGER NOT NULL) - идентификатор пользователя
- `movie_id` (INTEGER NOT NULL) - идентификатор фильма
- `rating` (REAL NOT NULL) - оценка
- `timestamp` (INTEGER NOT NULL) - временная метка

### Таблица `tags`
- `id` (INTEGER PRIMARY KEY AUTOINCREMENT) - автоинкрементный ключ
- `user_id` (INTEGER NOT NULL) - идентификатор пользователя
- `movie_id` (INTEGER NOT NULL) - идентификатор фильма
- `tag` (TEXT NOT NULL) - тег
- `timestamp` (INTEGER NOT NULL) - временная метка

### Таблица `users`
- `id` (INTEGER PRIMARY KEY) - идентификатор пользователя
- `name` (TEXT NOT NULL) - имя пользователя
- `email` (TEXT NOT NULL) - электронная почта
- `gender` (TEXT NOT NULL) - пол
- `register_date` (TEXT NOT NULL) - дата регистрации
- `occupation` (TEXT NOT NULL) - профессия

## Результат выполнения

После успешного выполнения скрипта `db_init.bat` будет создана заполненная база данных `db_init.db` со всеми таблицами и данными.

## Устранение неполадок

### Ошибка "python3: command not found"
- Убедитесь, что Python 3 установлен и добавлен в PATH
- Попробуйте использовать `python` вместо `python3`

### Ошибка "sqlite3: command not found"
- Установите SQLite3 или убедитесь, что он добавлен в PATH
- На Windows можно использовать полный путь к sqlite3.exe

### Ошибка "No such file or directory: '../dataset/'"
- Убедитесь, что каталог `dataset` находится в родительской директории относительно `Task02`
- Проверьте наличие всех необходимых файлов данных

## Автор

Создано в рамках лабораторной работы по курсу "Базы данных"
