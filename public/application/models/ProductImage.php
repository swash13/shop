<?php

class ProductImageModel extends Model {
    const TABLE = 'product_image';

    protected static $fields = array('id_product', 'file', 'position');

    protected static $links = array(
        'product' => array(
            'model' => 'Product',
            'type' => LinkType::FOREIGN_KEY,
            'field' => 'id_product',
        )
    );
}