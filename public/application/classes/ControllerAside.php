<?php

/*
 * Промежуточный класс контроллеров
 * которые используют шаблоны с левым меню
 */
class ControllerAside extends ControllerFront {
    protected static function init() {
        parent::init();

        /*
         * Если текущий запрос не ajax
         * Происходит привязка глобальных данных для левого меню
         */
        if (!App::isAjax()) {
            // Привязка списка категорий

            $collection = new Collection('Category', App::currentLang()->getId());
            $collection->where("`active` = 1");

            static::templateGlobal('aside_categories', $collection->items());

            // Привязка списка производителей
            $collection = new Collection('Brand', App::currentLang()->getId());
            $collection->where("`active` = 1");
            static::templateGlobal('aside_brands', $collection->items());

            // Привязка данных для фильтра цен
            static::templateGlobal('filter_price', Database::getRow("
                SELECT
                    MIN(`price`) AS `min`,
                    MAX(`price`) AS `max`
                FROM `product`
                WHERE `active` = 1
            "));
        }
    }
}