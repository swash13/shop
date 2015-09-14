<?php

class CatalogController extends ControllerAside {
    protected static function actionDefault()
    {
        // Заголовок над списком твоаров
        self::templateVar('product_list_title', 'Каталог');

        // Если выбрана категория или бренд, перезаписываем заголовок
        if (isset($_GET['category'])) {
            $category = new CategoryModel($_GET['category']);
            self::templateVar('product_list_title', 'Категория ' . $category->name);
        }

        if (isset($_GET['brand'])) {
            $brand = new BrandModel($_GET['brand']);
            self::templateVar('product_list_title', 'Производитель ' . $brand->name);
        }

        // Товары
        $products = new Collection('Product', App::currentLang()->getId());
        $products->link('cover');
        $products->where("`active` = 1" .
                (isset($_GET['category']) ? " AND `id_category` = {$_GET['category']}" : '') .
                (isset($_GET['brand']) ? " AND `id_brand` = {$_GET['brand']}" : '') .
                (isset($_POST['submitPriceRange']) ? " AND `price` >= {$_POST['min']} AND `price` <= {$_POST['max']}" : '')
        );
        $products->order("`add` DESC");
        $products->limit(12);

        self::templateVar('product_list_items', $products->items());

        // Если это ajax выводим только чанк product-list
        if (App::isAjax()) {
            return self::displayPage('chunks/product-list');
        }

        return self::displayPage('catalog');
    }

    // action - поиск товаров
    protected static function actionSearch() {
        // Заголовок над списком твоаров
        self::templateVar('product_list_title', 'Результаты поиска');

        // Товары
        $products = new Collection('Product', App::currentLang()->getId());
        $products->link('cover');
        $products->where("`active` = 1 AND `name` LIKE '%{$_GET['q']}%'" .
            (isset($_POST['submitPriceRange']) ? " AND `price` >= {$_POST['min']} AND `price` <= {$_POST['max']}" : '')
        );
        $products->order("`add` DESC");
        $products->limit(12);

        self::templateVar('product_list_items', $products->items());

        // Если это ajax выводим только чанк product-list
        if (App::isAjax()) {
            return self::displayPage('chunks/product-list');
        }

        return self::displayPage('catalog');
    }
}