<?php

// Шаблон выводимый на основе php файла (для HTML страниц, XML и др.)
class TemplatePage extends Template {
    // Шаблоны страниц имеют вложенную структуру
    // В этом массиве хранятся переменные которые будут доступны сразу во всех шаблонах этого класса
    protected static $globals = array();

    // Файл шаблона
    protected $file;

    public function __construct($file) {
        $this->file = $file;
    }

    /*
     * Переопределен чтобы была возможность одинаковым образом получать
     * как переменные текущего шаблона так и глобальные переменные всех шаблонов
     * в виде свойств объекта шаблона
     */
    public function __get($name) {
        $result = parent::__get($name);

        if ($result) {
            return $result;
        }

        if (!$result && isset(self::$globals[$name])) {
            return self::$globals[$name];
        }

        return null;
    }

    // Привязка одной глобальной переменной или массива
    public function assignGlobals($name, $value = null) {
        if (is_array($name)) {
            foreach ($name as $var => $val) {
                self::$globals[$var] = $val;
            }
        } else {
            self::$globals[$name] = $value;
        }
    }

    /*
     * Рендеринг файла шаблона. В файле шаблона используются переменные шаблона
     * (в том числе глобальные)
     */
    public function display() {
        ob_start();

        // Подключение переводов для шаблона
        App::setTranslation('templates/' . $this->file);

        include TPL_DIR . '/' . $this->file . '.php';

        return ob_get_clean();
    }

    // рендеринг дочернего шаблона включаемого в текущий
    public function displayTemplate($file, $params = array()) {
        $template = new TemplatePage($file);

        $template->assign($params);

        $result = $template->display();

        App::setTranslation('templates/' . $this->file);

        return $result;
    }
}