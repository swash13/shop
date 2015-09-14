<?php

class CategoryModel extends Model {
    const TABLE = 'category';

    protected static $fields = array('active');
    protected static $fieldsLang = array('name');

    protected static $links = array(
        'products' => array(
            'model' => 'Product',
            'type' => LinkType::PRIMARY_KEY,
            'field' => 'id_category'
        )
    );
}