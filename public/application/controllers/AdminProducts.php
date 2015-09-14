<?php

class AdminProductsController extends Controller {
    protected static function actionDefault() {
        // Получение списка товаров
        $products = new Collection('Product', 1);
        $products->link('category');
        $products->link('brand');
        $products->link('cover');
        $products->order('`add` DESC');

        $list = new TemplateList();
        $list->fields = array(
            'id' => array(
                'type' => 'id',
                'title' => 'Номер'
            ),
            'cover->file' => array(
                'type' => 'image',
                'title' => 'Изображение',
                'directory' => 'products',
                'size' => '85x84'
            ),
            'name' => array(
                'type' => 'text',
                'title' => 'Название'
            ),
            'category->name' => array(
                'type' => 'text',
                'title' => 'Категория'
            ),
            'brand->name' => array(
                'type' => 'text',
                'title' => 'Производитель'
            ),
            'price' => array(
                'type' => 'text',
                'title' => 'Цена'
            ),
            'active' => array(
                'type' => 'active',
                'title' => 'Вкл/Выкл',
                'action' => 'active',
                'controller' => 'admin-products'
            )
        );
        $list->itemActions = array(
            'edit' => array(
                'hint' => 'Изменить',
                'icon' => 'edit',
                'controller' => 'admin-products'
            ),
            'delete' => array(
                'hint' => 'Удалить',
                'icon' => 'delete',
                'controller' => 'admin-products'
            )
        );
        $list->actions = array(
            'new' => array(
                'hint' => 'Добавить',
                'icon' => 'add',
                'controller' => 'AdminProducts'
            )
        );
        $list->items = Model::setModelsAutoload($products->items(), false, true);
        $list->classes = 'center-list';

        self::templateVar('title', App::t('Товары'));
        self::templateVar('content', $list);

        return self::displayPage('admin');
    }

    protected static function actionActive() {
        $product = new ProductModel($_GET['id']);
        $product->active = $product->active ? '0' : '1';
        $product->save();

        self::redirect(App::getLink('admin-products'));
    }

    protected static function actionEdit() {
        $product = new ProductModel($_GET['id'], 0);
        $errors = self::processPost($product);

        self::assignForm($product, $errors);

        self::templateVar('title', App::t('Редактирование товара'));
        return self::displayPage('admin');
    }

    protected static function actionNew() {
        $product = new ProductModel(null, 0);
        $errors = self::processPost($product);

        self::assignForm($product, $errors);

        self::templateVar('title', App::t('Создание товара'));
        return self::displayPage('admin');
    }

    protected static function actionDelete() {
        $product = new ProductModel($_GET['id']);
        $product->delete();

        self::redirect(App::getLink('AdminProducts'));
    }

    protected static function actionNewImage() {
        $file = self::processImage($_FILES['image']['tmp_name'], isset($_POST['product']) ? ROOT_DIR . '/assets/images/products' : ROOT_DIR . '/assets/images/temp', array('110x110', '255x237', '184x162', '255x128', '329x380', '85x84'));

        if (isset($_POST['product'])) {
            $image = new ProductImageModel();
            $image->file = $file;
            $image->id_product = $_POST['product'];
            $image->position = 1 + Database::getValue("
                SELECT MAX(`position`)
                FROM `product_image`
                WHERE `id_product` = {$_POST['product']}
            ");
            $image->save();

            $product = new ProductModel($_POST['product']);
            $list = new TemplateList();
            $list->fields = array(
                'file' => array(
                    'type' => 'image',
                    'title' => '',
                    'directory' => 'products',
                    'size' => '85x84'
                )
            );
            $list->itemActions = array(
                'down-image' => array(
                    'hint' => 'Опустить',
                    'icon' => 'down',
                    'controller' => 'AdminProducts'
                ),
                'up-image' => array(
                    'hint' => 'Поднять',
                    'icon' => 'up',
                    'controller' => 'AdminProducts'
                ),
                'delete-image' => array(
                    'hint' => 'Удалить',
                    'icon' => 'delete',
                    'controller' => 'AdminProducts'
                )
            );
            $list->items = $product->images;

            return $list->display();
        } else {
            $images = array();

            $_POST['images'] = isset($_POST['images']) ? explode(',', $_POST['images']) : array();
            $_POST['images'][] = $file;
            foreach ($_POST['images'] as $image) {
                $images[] = array('id' => $image);
            }

            $list = new TemplateList();
            $list->fields = array(
                'id' => array(
                    'type' => 'image',
                    'title' => '',
                    'directory' => 'temp',
                    'size' => '85x84'
                )
            );
            $list->items = $images;

            return $list->display() . '<input type="hidden" id="image_post_params" name="image_post_params" value=\'' . json_encode(array('images' => implode(',', $_POST['images']))) . '\' />';
        }
    }

    protected static function actionDeleteImage() {
        $image = Database::getRow("
            SELECT *
            FROM `product_image`
            WHERE `id` = {$_GET['id']}
        ");

        unlink(ROOT_DIR . '/assets/images/products/' . $image['file'] . '.png');
        unlink(ROOT_DIR . '/assets/images/products/' . $image['file'] . '_85x84.png');
        unlink(ROOT_DIR . '/assets/images/products/' . $image['file'] . '_110x110.png');
        unlink(ROOT_DIR . '/assets/images/products/' . $image['file'] . '_184x162.png');
        unlink(ROOT_DIR . '/assets/images/products/' . $image['file'] . '_255x128.png');
        unlink(ROOT_DIR . '/assets/images/products/' . $image['file'] . '_255x237.png');
        unlink(ROOT_DIR . '/assets/images/products/' . $image['file'] . '_329x380.png');

        Database::query("
            DELETE FROM `product_image`
            WHERE `id` = {$_GET['id']}
        ");

        Database::query("
            UPDATE `product_image`
            SET `position` = `position` - 1
            WHERE `position` > {$image['position']}
        ");

        self::redirect(App::getLink('AdminProducts', array('action' => 'edit', 'id' => $image['id_product'])));
    }

    protected static function actionUpImage() {
        $image = Database::getRow("
            SELECT *
            FROM `product_image`
            WHERE `id` = {$_GET['id']}
        ");

        Database::query("
            UPDATE `product_image`
            SET `position` = `position` + 1
            WHERE `id_product` = {$image['id_product']} AND `position` = {$image['position']} - 1
        ");

        Database::query("
            UPDATE `product_image`
            SET `position` = `position` - 1
            WHERE `id` = {$image['id']}
        ");

        self::redirect(App::getLink('AdminProducts', array('action' => 'edit', 'id' => $image['id_product'])));
    }

    protected static function actionDownImage() {
        $image = Database::getRow("
            SELECT *
            FROM `product_image`
            WHERE `id` = {$_GET['id']}
        ");

        Database::query("
            UPDATE `product_image`
            SET `position` = `position` - 1
            WHERE `id_product` = {$image['id_product']} AND `position` = {$image['position']} + 1
        ");

        Database::query("
            UPDATE `product_image`
            SET `position` = `position` + 1
            WHERE `id` = {$image['id']}
        ");

        self::redirect(App::getLink('AdminProducts', array('action' => 'edit', 'id' => $image['id_product'])));
    }

    private static function processPost($product) {
        $errors = array();

        if (!empty($_POST)) {
            if (empty($_POST['name'][DEF_LANG])) {
                $errors['name'] = 'Укажите название на языке по умолчанию';
            } else {
                $langs = App::getLangs();

                foreach ($langs as $lang) {
                    if (empty($_POST['name'][$lang->getId()])) {
                        $_POST['name'][$lang->getId()] = $_POST['name'][DEF_LANG];
                    }
                }

                $product->name = $_POST['name'];
            }

            if (empty($_POST['id_brand'])) {
                $errors['id_brand'] = 'Не выбран производитель';
            } else {
                $product->id_brand = $_POST['id_brand'];
            }

            if (empty($_POST['id_category'])) {
                $errors['id_category'] = 'Не выбрана категория';
            } else {
                $product->id_category = $_POST['id_category'];
            }

            if (empty($_POST['articul'])) {
                $errors['articul'] = 'Введите артикул';
            } else {
                $product->articul = $_POST['articul'];
            }

            if ($_POST['price'] < 0) {
                $errors['price'] = 'Цена некорректна';
            } else {
                $product->price = $_POST['price'];
            }

            $product->active = isset($_POST['active']) ? 1 : 0;

            $langs = App::getLangs();
            foreach ($langs as $lang) {
                if (empty($_POST['description'][$lang->getId()])) {
                    $_POST['description'][$lang->getId()] = $_POST['description'][DEF_LANG];
                }
            }

            $product->description = $_POST['description'];

            if (empty($errors)) {
                $is_new = $product->getId() ? false : true;
                $product->save();

                if ($is_new) {
                    $images = json_decode($_POST['image_post_params'], true);
                    $images = !empty($images['images']) ? explode(',', $images['images']) : array();
                    foreach ($images as $index => $image) {
                        rename(ROOT_DIR . '/assets/images/temp/' . $image . '.png', ROOT_DIR . '/assets/images/products/' . $image . '.png');
                        rename(ROOT_DIR . '/assets/images/temp/' . $image . '_85x84.png', ROOT_DIR . '/assets/images/products/' . $image . '_85x84.png');
                        rename(ROOT_DIR . '/assets/images/temp/' . $image . '_110x110.png', ROOT_DIR . '/assets/images/products/' . $image . '_110x110.png');
                        rename(ROOT_DIR . '/assets/images/temp/' . $image . '_184x162.png', ROOT_DIR . '/assets/images/products/' . $image . '_184x162.png');
                        rename(ROOT_DIR . '/assets/images/temp/' . $image . '_255x128.png', ROOT_DIR . '/assets/images/products/' . $image . '_255x128.png');
                        rename(ROOT_DIR . '/assets/images/temp/' . $image . '_255x237.png', ROOT_DIR . '/assets/images/products/' . $image . '_255x237.png');
                        rename(ROOT_DIR . '/assets/images/temp/' . $image . '_329x380.png', ROOT_DIR . '/assets/images/products/' . $image . '_329x380.png');

                        $productImage = new ProductImageModel();
                        $productImage->id_product = $product->getId();
                        $productImage->file = $image;
                        $productImage->position = $index + 1;
                        $productImage->save();
                    }
                }

                self::redirect(App::getLink('AdminProducts'));
            }
        }

        return $errors;
    }

    private static function assignForm($product, $errors) {
        $categories = Database::getTable("
            SELECT
                `c`.`id` AS `value`,
                `cl`.`name` AS `text`
            FROM `category` AS `c`
            LEFT JOIN `category_lang` AS `cl` ON `cl`.`id_object` = `c`.`id`
            WHERE `c`.`active` = 1 AND `cl`.`id_lang` = 1
            ORDER BY `cl`.`name`
        ");

        $brands = Database::getTable("
            SELECT
                `id` AS `value`,
                `name` AS `text`
            FROM `brand`
            WHERE `active` = 1
            ORDER BY `name`
        ");

        $form = new TemplateForm();
        $form->addTextField('name', 'Название', $product->name, isset($errors['name']) ? $errors['name'] : null, true);
        $form->addTextField('articul', 'Артикул', $product->articul, isset($errors['articul']) ? $errors['articul'] : null);
        $form->addTextField('price', 'Цена', $product->price, isset($errors['price']) ? $errors['price'] : null);
        $form->addSelectField('id_category', 'Категория', $categories, $product->id_category, isset($errors['id_category']) ? $errors['id_category'] : null, 'Выберите категорию');
        $form->addSelectField('id_brand', 'Производитель', $brands, $product->id_brand, isset($errors['id_brand']) ? $errors['id_brand'] : null, 'Выберите произвотеля');
        $form->addTextareaField('description', 'Описание', $product->description, null, true, true);
        $form->addCheckboxField('active', 'Вкл/Выкл', $product->active);

        if ($product->getId()) {
            $list = new TemplateList();
            $list->fields = array(
                'file' => array(
                    'type' => 'image',
                    'title' => '',
                    'directory' => 'products',
                    'size' => '85x84'
                )
            );
            $list->itemActions = array(
                'down-image' => array(
                    'hint' => 'Опустить',
                    'icon' => 'down',
                    'controller' => 'AdminProducts'
                ),
                'up-image' => array(
                    'hint' => 'Поднять',
                    'icon' => 'up',
                    'controller' => 'AdminProducts'
                ),
                'delete-image' => array(
                    'hint' => 'Удалить',
                    'icon' => 'delete',
                    'controller' => 'AdminProducts'
                )
            );
            $list->items = $product->images;
            $form->addUploaderField('image', 'Добавить картинку', App::getLink('AdminProducts', array('action' => 'new-image')), '*.jpg;*.jpeg;*.png', 'Изображения', '3MB', array('product' => $product->getId()), $list);
        } else {
            $form->addUploaderField('image', 'Добавить картинку', App::getLink('AdminProducts', array('action' => 'new-image')), '*.jpg;*.jpeg;*.png', 'Изображения', '3MB');
        }

        $form->classes = 'center-form';

        self::templateVar('content', $form);
    }

    private static function processImage($source, $dir, $sizes){
        $file = md5(microtime() . rand(1000, 9999));
        $info=getimagesize($source);
        $image=imagecreatefromstring(file_get_contents($source));
        imagepng($image, $dir.'/'.$file.'.png');

        foreach($sizes as $size){
            $dest_size=explode('x',$size); // $dest_size[0] - ширина $desct_size[1] - высота

            if(($info[0]/$info[1]) > ($dest_size[0])/$dest_size[1]) {
                $d = $dest_size[0] * $info[1] / $dest_size[1];
                $dd = ($info[0] - $d) / 2;
                $new_image = imagecreatetruecolor($dest_size[0],$dest_size[1]);
                imagecopyresampled($new_image, $image, 0, 0, $dd, 0, $dest_size[0], $dest_size[1], $d, $info[1]);
            }else{
                $d = $info[0] * $dest_size[1] / $dest_size[0];
                $dd = ($info[1] - $d) / 2;
                $new_image=imagecreatetruecolor($dest_size[0], $dest_size[1]);
                imagecopyresampled($new_image, $image, 0, 0, 0, $dd, $dest_size[0], $dest_size[1], $info[0], $d);
            }

            imagepng($new_image, $dir.'/'.$file.'_'.$size.'.png');
        }

        return $file;
    }
}