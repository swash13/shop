<?php

abstract class Controller {
    // временное хранилище переменных для вывода (TemplatePage и TemplateJSON)
    private static $templateVars = array();

    // временное хранилище глобальных переменноых для вывода (только для TemplatePage)
    private static $templateGlobals = array();

    /*
     * Запись/получение переменной для вывода
     * Если значение переменной не передано, функция вернет текущее значение
     * Если передано функция обновит переменную
     */
    protected static function templateVar($name, $value = null) {
        if (isset($value)) {
            self::$templateVars[$name] = $value;
        } else if (isset(self::$templateVars[$name])){
            return self::$templateVars[$name];
        } else {
            return null;
        }
    }

    // Удаление переменной для вывода
    protected static function unsetVar($name) {
        unset(self::$templateVars[$name]);
    }

    /*
     * Запись/получение глобальной переменной для вывода
     * Если значение переменной не передано, функция вернет текущее значение
     * Если передано функция обновит переменную
     */
    protected static function templateGlobal($name, $value = null) {
        if (isset($value)) {
            self::$templateGlobals[$name] = $value;
        } else if (isset(self::$templateGlobals[$name])){
            return self::$templateGlobals[$name];
        } else {
            return null;
        }
    }

    // Удаление глобальной переменной для вывода
    protected static function unsetGlobal($name) {
        unset(self::$templateGlobals[$name]);
    }

    // Подготовка к обработке апроса
    protected static function init() {}

    // action - метод по умолчанию
    protected static function actionDefault() {}

    /*
     * Выводит указанный шаблон страницы в виде строки
     * Используется класс TemplatePage
     * В шаблоне выполняется php код
     * перед выводом все переменные подготовленные на вывод передаются шаблону
     */
    protected static function displayPage($file) {
        $template = new TemplatePage($file);
        $template->assignGlobals(self::$templateGlobals);
        $template->assign(self::$templateVars);
        $result = $template->display();

        App::setTranslation('controllers/' . App::getController());

        return $result;
    }

    /*
     * Выводит строку в формате json на основе подготовленных на вывод переменных
     * Используется TemplateJSON
     */
    protected static function displayJSON() {
        $template = new TemplateJSON();
        $template->assign(self::$templateVars);

        return $template->display();
    }

    /*
     * Полный сброс текущего выполнения обработки запроса
     * и переадресация на указанный адрес
     */
    protected static function redirect($url) {
        header("Location: $url");
        exit;
    }


    /*
     * Запуск всего процесса обработки запроса контроллером
     * Этот метод вызывается в App::run()
     */
    public static function run() {
        // Подключение переводов
        App::setTranslation('controllers/' . App::getController());

        // Подготовка контроллера
        static::init();

        // Запуск необходимого для обработки запроса action - метода
        $method = 'action' . App::getAction();

        if (method_exists(get_called_class(), $method)) {
            return static::$method();
        } else {
            return static::actionDefault();
        }
    }
}