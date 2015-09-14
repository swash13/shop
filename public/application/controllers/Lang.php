<?php

class LangController extends Controller {
    protected static function actionDefault() {
        if ($_GET['lang']) {
            $lang = new LangModel($_GET['lang']);
            if ($lang->getId()) {
                App::currentLang($_GET['lang']);
            }
        }

        if ($_SERVER['HTTP_REFERER']) {
            self::redirect($_SERVER['HTTP_REFERER']);
        } else {
            self::redirect('/');
        }
    }
}