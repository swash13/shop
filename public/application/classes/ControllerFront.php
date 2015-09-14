<?php

// Базовый класс для front-end контроллеров
class ControllerFront extends Controller {
    protected static function init() {
        parent::init();

        /*
         * Если текущий запрос не ajax
         * Происходит привязка текущего пользователя к шаблонам
         */
        if (!App::isAjax()) {
            // Привязка списка категорий
            static::templateGlobal('customer', App::currentUser());
        }
    }
}