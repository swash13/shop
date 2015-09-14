<?php

class AddressModel extends Model {
    const TABLE = 'addresses';

    protected static $fields = array('id_customer', 'name', 'phone', 'country', 'state', 'city', 'address', 'date_add');

    protected static $links = array(
        'customer' => array(
            'model' => 'UserModel',
            'type' => LinkType::FOREIGN_KEY,
            'field' => 'id_customer'
        ),
    );
}