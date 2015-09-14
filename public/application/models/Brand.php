<?php

class BrandModel extends Model {
    const TABLE = 'brand';

    protected static $fields = array('name', 'active');
    protected static $links = array(
        'products' => array(
            'model' => 'Product',
            'type' => LinkType::PRIMARY_KEY,
            'field' => 'id_brand'
        )
    );
}