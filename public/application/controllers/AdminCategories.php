<?php

class AdminCategoriesController extends Controller {
    protected static function actionDefault() {
        $categories = new Collection('Category', App::currentLang()->getId());

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
                'controller' => 'admin-categories'
            )
        );
        $list->itemActions = array(
            'edit' => array(
                'hint' => 'Изменить',
                'icon' => 'edit',
                'controller' => 'admin-categories'
            ),
            'delete' => array(
                'hint' => 'Удалить',
                'icon' => 'delete',
                'controller' => 'admin-categories'
            )
        );
        $list->actions = array(
            'new' => array(
                'hint' => 'Добавить',
                'icon' => 'add',
                'controller' => 'admin-categories'
            )
        );
        $list->items = $categories->items();
        $list->classes = 'center-list';

        self::templateVar('title', App::t('Категории'));
        self::templateVar('content', $list);

        return self::displayPage('admin');
    }

    protected static function actionActive() {
        $category = new CategoryModel($_GET['id']);
        $category->active = $category->active ? '0' : '1';
        $category->save();

        self::redirect(App::getLink('AdminCategories'));
    }

    protected static function actionNew() {
        $category = new CategoryModel(null, 0);
        $errors = self::processPost($category);

        self::assignForm($category, $errors);

        self::templateVar('title', 'Создание категории');
        return self::displayPage('admin');
    }

    protected static function actionEdit() {
        $category = new CategoryModel($_GET['id'], 0);
        $errors = self::processPost($category);

        self::assignForm($category, $errors);

        self::templateVar('title', 'Редактирование категории');
        return self::displayPage('admin');
    }

    private static function processPost($category) {
        $errors = array();

        if (!empty($_POST)) {
            if (empty($_POST['name'][DEF_LANG])) {
                $errors['name'] = 'Название должно быть указано хотя бы на языке по умолчанию';
            } else {
                $langs = App::getLangs();

                foreach ($langs as $lang) {
                    if (empty($_POST['name'][$lang->getId()])) {
                        $_POST['name'][$lang->getId()] = $_POST['name'][DEF_LANG];
                    }
                }

                $category->name = $_POST['name'];
            }

            $category->active = isset($_POST['active']) ? 1 : 0;

            if (empty($errors)) {
                $category->save();
                self::redirect(App::getLink('AdminCategories'));
            }
        }

        return $errors;
    }

    private static function assignForm($category, $errors) {
        $form = new TemplateForm();
        $form->addTextField('name', 'Название', $category->name, isset($errors['name']) ? $errors['name'] : null, true);
        $form->addCheckboxField('active', 'Вкл/Выкл', $category->active);
        $form->classes = 'center-form';

        self::templateVar('content', $form);
    }
}