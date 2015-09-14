<?php

class ProductModel extends Model {
    const TABLE = 'product';

    protected static $fields = array('id_category', 'id_brand', 'price', 'articul', 'active', 'add');
    protected static $fieldsLang = array('name', 'description');

    private $images_count = null;

    protected static $links = array(
        'category' => array(
            'model' => 'Category',
            'type' => LinkType::FOREIGN_KEY,
            'field' => 'id_category'
        ),
        'brand' => array(
            'model' => 'Brand',
            'type' => LinkType::FOREIGN_KEY,
            'field' => 'id_brand'
        ),
        'images' => array(
            'model' => 'ProductImage',
            'type' => LinkType::PRIMARY_KEY,
            'field' => 'id_product',
            'order' => "`position` ASC"

        ),
        'cover' => array(
            'model' => 'ProductImage',
            'type' => LinkType::PRIMARY_KEY,
            'field' => 'id_product',
            'where' => "`position` = 1",
            'limit' => 1,
            'virtual' => true
        )
    );

    public function getImagesCount() {
        if ($this->images_count === null) {
            $this->images_count = Database::getValue("
                SELECT COUNT(*)
                FROM `products_image`
                WHERE `id_product` = {$this->id}
            ");
        };

        return $this->images_count;
    }
}