<?php

class CartController extends ControllerFront {
    protected static function init() {
        if (!isset($_SESSION['cart'])) {
            $_SESSION['cart'] = array();
        }
    }

    protected static function actionDefault() {
        $products = new Collection('Product', App::currentLang()->getId());
        $products->link('cover');
        $products->where("`id` IN (" . implode(',', array_keys($_SESSION['cart'])) . ")");
        $products = $products->items();

        foreach ($products as $product) {
            $product->quantity = $_SESSION['cart'][$product->getId()];
        }

        self::templateVar('cart_items', $products);
        return self::displayPage('cart');
    }

    protected static function actionAdd() {
        if (!empty($_GET['product'])) {
            if (isset($_SESSION['cart'][$_GET['product']])) {
                $_SESSION['cart'][$_GET['product']] += $_GET['quantity'];
            } else {
                $_SESSION['cart'][$_GET['product']] = $_GET['quantity'];
            }

            if ($_SESSION['cart'][$_GET['product']] == 0) {
                unset($_SESSION['cart'][$_GET['products']]);
            }
        }

        if (App::isAjax()) {
            return App::t('Товар успешно добавлен');
        }
    }

    protected static function actionDelete () {
        if (!empty($_GET['product'])) {
            unset($_SESSION['cart'][$_GET['product']]);
        }

        if (App::isAjax()) {
            return 'success';
        }
    }
}