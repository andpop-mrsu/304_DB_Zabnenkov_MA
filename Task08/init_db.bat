@echo off
chcp 65001 >nul

echo Инициализация базы данных...

if not exist "data" mkdir data

cmd /c "C:\sqllite\sqlite3.exe data\salon.db < data\db_init.sql"

if %ERRORLEVEL% EQU 0 (
    echo База данных успешно создана!
) else (
    echo Ошибка при создании базы данных!
    exit /b 1
)

pause

