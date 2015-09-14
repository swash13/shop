<?php

class App {
    private static $controller; // текущий контроллер
    private static $action; // текущий action метод контроллера

    private static $activeLangs;
    private static $defaultLang;
    private static $langs;
    private static $currentLang;
    private static $translations;
    private static $translationFile;

    private static $currentUser;

    public static function init() {
        if (APP_MODE == 'develop') {
            // Когда сайт в разработке показываются все ошибки и предупреждения
            ini_set('display_errors', '1');
            error_reporting(E_ALL);
        } else {
            ini_set('display_error', '0');
        }

        // Регистрация метода в качестве автозагрузчика классов
        spl_autoload_register('App::loadClass');

        // Соединение с базой данных
        Database::connect(DB_HOST, DB_USER, DB_PASS, DB_NAME, DB_CHARSET);

        // Запуск механизма сессий
        session_start();

        if (!isset($_SESSION['current_lang_id'])) {
            $_SESSION['current_lang_id'] = DEF_LANG;
        }

        self::$translations = array();
        self::$translationFile = '';
    }

    public static function run()
    {
        // Определение контроллера для обработки запроса
        if (empty($_GET['controller'])) {
            $_GET['controller'] = 'index';
        }

        self::$controller = explode('-', $_GET['controller']);
        foreach (self::$controller as &$part) {
            $part = ucfirst($part);
        }
        self::$controller = implode('', self::$controller);
        $controller = self::$controller . 'Controller';


        // Определение action - метода для обработки запроса
        if (empty($_GET['action'])) {
            $_GET['action'] = 'default';
        }

        self::$action = explode('-', $_GET['action']);
        foreach (self::$action as &$part) {
            $part = ucfirst($part);
        }
        self::$action = implode('', self::$action);

        // Запуск контроллера
        return $controller::run();
    }

    // Автозагрузка классов
    public static function loadClass($class_name) {
        switch (true) {
            // Название класса заканчивается на 'Controller' - загрузка из папки controllers
            case preg_match('/^.+Controller$/', $class_name) :
                $dir  = 'controllers';
                $file_name = preg_replace('/Controller$/', '', $class_name);
                break;

            // Название класса заканчивается на 'Model' - загрузка класса из папки models
            case preg_match('/^.+Model$/', $class_name) :
                $dir = 'models';
                $file_name = preg_replace('/Model$/', '', $class_name);
                break;

            // Остальные классы в папке classes
            default :
                $dir = 'classes';
                $file_name = $class_name;
                break;
        }

        // Если файла с классом нет, генерируется исключение
        if (!file_exists(APP_DIR . "/$dir/$file_name.php")) {
            throw new Exception("Class not found ($class_name).");
        }

        // Подключение файла с классом
        include_once APP_DIR . "/$dir/$file_name.php";

        // Если в файле вдруг небыло класса, генерируется исключение
        if (!class_exists($class_name)) {
            throw new Exception("Class not found ($class_name).");
        }
    }

    /*
     *  Формирование ссылок ведущих на контроллеры
     *  $controller - название контроллера
     *  $params - массив дополнительных GET параметров (action и др.)
     */
    public static function getLink($controller, $params = array()) {
        if (preg_match('/[A-Z]/', $controller)) {
            $controller = preg_match_all('/[A-Z][^A-Z]*/', preg_replace('/(.+)Controller$/', '$1', $controller), $result);
            $controller = strtolower(implode('-', $result[0]));
        }

        $link = 'index.php?controller=' . $controller;

        foreach ($params as $key => $value) {
            $link .= '&' . urlencode($key) . '=' . urlencode($value);
        }

        return BASE_PATH . $link;
    }

    // Формирование простых ссылок (ск стилям, скриптам, картинкам и т.д.)
    public static function siteURL($path = '') {
        return BASE_PATH . $path;
    }

    // Получение текущего контроллера
    public static function getController() {
        return self::$controller;
    }

    // Получение текущего action - метода
    public static function getAction() {
        return self::$action;
    }

    // Получение списка языков
    public static function getLangs($active = true) {
        if ($active) {
            if (!self::$activeLangs) {
                self::$activeLangs = new Collection('Lang', 1);
                self::$activeLangs->where("`active` = 1");
                self::$activeLangs = self::$activeLangs->items();
            }

            return self::$activeLangs;
        } else {
            if (!self::$langs) {
                self::$langs = new Collection('Lang');
                self::$langs = self::$langs->items();
            }

            return self::$langs;
        }
    }

    public static function getDefaultLang() {
        if (!self::$defaultLang) {
            self::$defaultLang = new LangModel(DEF_LANG, 1);
        }

        return self::$defaultLang;
    }

    // Является ли текущий запрос аяксовым
    public static function isAjax() {
        return
            isset($_SERVER['HTTP_X_REQUESTED_WITH']) &&
            (
                $_SERVER['HTTP_X_REQUESTED_WITH'] == 'XMLHttpRequest' || // запрос от яваскрипта
                strpos( $_SERVER['HTTP_X_REQUESTED_WITH'], 'ShockwaveFlash') !== false // запрос от flash
            );
    }

    // Запись/получение текущего пользователя
    public static function currentUser($user = null) {
        if ($user) {
            if (is_object($user)) {
                $_SESSION['current_user_id'] = $user->getId();
                self::$currentUser = $user;
            } else {
                $_SESSION['current_user_id'] = $user;
                self::$currentUser = new UserModel($user, 1);
            }
        }

        if (!isset($_SESSION['current_user_id'])) {
            $_SESSION['current_user_id'] = 0;
            return null;
        }

        if (!self::$currentUser && $_SESSION['current_user_id']) {
            self::$currentUser = new UserModel($_SESSION['current_user_id']);
        }

        return self::$currentUser;
    }

    // Запись/получение ткущего языка
    public static function currentLang($lang = null) {
        if ($lang) {
            if (is_object($lang)) {
                $_SESSION['current_lang_id'] = $lang->getId();
            } else {
                $_SESSION['current_lang_id'] = $lang;
            }
        }

        if (!self::$currentLang && $_SESSION['current_lang_id']) {
            self::$currentLang = new LangModel($_SESSION['current_lang_id'], $_SESSION['current_lang_id']);
        }

        return self::$currentLang;
    }

    public static function setTranslation($file) {
        $file = LANG_DIR . '/' . $file . '_' . self::currentLang()->code . '.php';
        if (file_exists($file)) {
            self::$translations = include $file;
            self::$translationFile = $file;
        } else {
            self::$translations = array();
            self::$translationFile = $file;
            $dir = dirname($file);
            if (!is_dir($dir)) {
                mkdir($dir, 0777, true);
            }
            file_put_contents($file, '<?php return array();');
        }
    }

    public static function t($frase) {
        if (array_key_exists($frase, self::$translations)) {
            return self::$translations[$frase];
        }

        self::$translations[$frase] = $frase;
        file_put_contents(self::$translationFile, '<?php return ' . var_export(self::$translations, true) . ';');

        return $frase;
    }
}