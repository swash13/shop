<?php

class AdminController extends Controller {

    public static function actionDefault() {
        return self::displayPage('admin');
    }
}