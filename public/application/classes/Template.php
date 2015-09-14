<?php

// Базовый класс шаблонов
class Template {
    // Переменные шаблона
    protected $vars = array();

    // автоматическое получение переменной шаблона как свойства объекта
    public function __get($name) {
        if (isset($this->vars[$name])) {
            return $this->vars[$name];
        }

        return null;
    }

    public function __isset($name) {
        return !empty($this->vars[$name]);
    }

    // Привязка одной переменной к шаблону либо массива переменных
    public function assign($name, $value = null) {
        if (is_array($name)) {
            foreach ($name as $var => $val) {
                $this->vars[$var] = $val;
            }
        } else {
            $this->vars[$name] = $value;
        }
    }

    // Рендеринг шаблона (Каждый класс шаблона рендерится по своему)
    public function display() {}
}
