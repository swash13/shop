<?php

class ProductController extends ControllerAside {
    protected static function actionDefault() {
        $product = new ProductModel($_GET['id'], App::currentLang()->getid());

        self::templateVar('product', $product);
        return self::displayPage('product');
    }
}