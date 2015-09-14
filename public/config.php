<?php
define('BASE_PATH', '/'); // Основной путь в URL после домена

define('APP_MODE', 'develop'); // Режим работы сайта (разработка, продакшн)
//define('App_MODE', 'production');

define('ROOT_DIR', __DIR__);                    // Корневая папка
define('APP_DIR', ROOT_DIR . '/application');   // Папка с классами, контроллерами, моделями
define('TPL_DIR', ROOT_DIR . '/templates');     // Папка с шаблонами
define('LANG_DIR', ROOT_DIR . '/langs');        // Папка с переводами
define('DEF_LANG', 1); // язык по умолчанию

define('DB_HOST', 'localhost');     // Адрес Mysql сервера
define('DB_USER', 'root');          // Пользователь
define('DB_PASS', '');              // Пароль
define('DB_NAME', 'shop');          // Название базы даных
define('DB_CHARSET', 'UTF8');       // Кодировка базы данных