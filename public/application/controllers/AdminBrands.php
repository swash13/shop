<?php

class AdminBrandsController extends Controller {
    protected static function actionDefault() {
        $brands = new Collection('Brand', App::currentLang()->getId());

        $list = new TemplateList();
        $list->fields = array(
            'id' => array(
                'type' => 'id',
                'title' => 'Номер'
            ),
            'name' => array(
                'type' => 'text',
                'title' => 'Название'
            ),
            'active' => array(
                'type' => 'active',
                'title' => 'Вкл/Выкл',
                'action' => 'active',
                'controller' => 'AdminBrands'
            )
        );
        $list->itemActions = array(
            'edit' => array(
                'hint' => 'Изменить',
                'icon' => 'edit',
                'controller' => 'AdminBrands'
            ),
            'delete' => array(
                'hint' => 'Удалить',
                'icon' => 'delete',
                'controller' => 'AdminBrands'
            )
        );
        $list->actions = array(
            'new' => array(
                'hint' => 'Добавить',
                'icon' => 'add',
                'controller' => 'AdminBrands'
            )
        );
        $list->items = $brands->items();
        $list->classes = 'center-list';

        self::templateVar('title', App::t('Производители'));
        self::templateVar('content', $list);

        return self::displayPage('admin');
    }

    protected static function actionActive() {
        $brand = new BrandModel($_GET['id']);
        $brand->active = $brand->active ? '0' : '1';
        $brand->save();

        self::redirect(App::getLink('AdminBrands'));
    }



    protected static function actionNew() {
        $brand = new BrandModel(null);
        $errors = self::processPost($brand);

        self::assignForm($brand, $errors);

        self::templateVar('title', 'Создание производителя');
        return self::displayPage('admin');
    }

    protected static function actionEdit() {
        $brand = new BrandModel($_GET['id']);
        $errors = self::processPost($brand);

        self::assignForm($brand, $errors);

        self::templateVar('title', 'Редактирование производителя');
        return self::displayPage('admin');
    }

    private static function processPost($brand) {
        $errors = array();

        if (!empty($_POST)) {
            if (empty($_POST['name'][DEF_LANG])) {
                $errors['name'] = 'Введите название';
            } else {
                $brand->name = $_POST['name'];
            }

            $brand->active = isset($_POST['active']) ? 1 : 0;

            if (empty($errors)) {
                $brand->save();
                self::redirect(App::getLink('AdminBrands'));
            }
        }

        return $errors;
    }

    private static function assignForm($brand, $errors) {
        $form = new TemplateForm();
        $form->addTextField('name', 'Название', $brand->name, isset($errors['name']) ? $errors['name'] : null);
        $form->addCheckboxField('active', 'Вкл/Выкл', $brand->active);
        $form->classes = 'center-form';

        self::templateVar('content', $form);
    }
}