<?php

// Шаблон который выводит заданные переменные в json формате
class TemplateJSON extends Template {

    public function display() {
        return json_encode($this->vars);
    }
}