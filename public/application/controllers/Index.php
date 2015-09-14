<?php

// Контроллер для главной страницы
class IndexController extends ControllerAside
{

    // Действие по умолчанию - вывод главной страницы
    protected static function actionDefault()
    {
        /*
         * Если выводиться вся страница то списко товаров передается шаблону products-list таким образом
         * Controller -> index -> products-list
         * Если запрос аяксовый то переменные передаются напрямую шаблону products-list
         * Controller -> products-list
         * Но при создании этих переменных мы не думаем о том какой шаблон будет выводиться.
         */

        // Заголовок списка товаров
        self::templateVar('product_list_title', 'Последние товары');

        // товары в списке (6 последних добавленных в соотвествии с фильтром цен)
        $collection = new Collection('Product', App::currentLang()->getId());
        $collection->link('cover');
        $collection->order("`add` DESC");

        $where = "`active` = 1";

        if (isset($_POST['submitPriceRange'])) {
            $where .= " AND `price` >= {$_POST['min']} AND `price` <= {$_POST['max']}";
        }

        $collection->where($where);
        $collection->limit(6);

        self::templateVar('product_list_items', $collection->items());

        if (App::isAjax()) {
            // Если это ajax выводим чанк списка товаров
            return self::displayPage('chunks/product-list');
        }

        // Привязка категорий для табов под списком товаров

        // Получаем две категории из базы
        $collection = new Collection('Category', App::currentLang()->getId());
        $collection->where("`active` = 1");
        $collection->limit(2);
        $categories = $collection->items();

        foreach ($categories as $category) {
            $collection = new Collection('Product', App::currentLang()->getId());
            $collection->where("`id_category` = " . $category->getId() . " AND `active` = 1");
            $collection->limit(4);

            $category->setLink('products', $collection->items());
        }

        foreach ($categories as $index => $category) {
            if (empty($category->products)) {
                unset($categories[$index]);
            }
        }

        // Привязываем к шаблону
        static::templateVar('tab_categories', $categories);

        return self::displayPage('index');
    }
}