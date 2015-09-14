<?php

class UserModel extends Model{
	const TABLE = 'user';

    protected static $fields = array('name', 'password', 'email', 'add');

    protected static $links = array(
        'addresses' => array(
            'model' => 'AddressModel',
            'type' => LinkType::PRIMARY_KEY,
            'field' => 'id_user'
        ),
    );

	public static function getByEmail($email){
        $row = Database::getRow("
            SELECT *
            FROM `user`
            WHERE `email` = '$email'
        ");

        if (!empty($row)) {
            $customer = new UserModel();
            $customer->id = $row['id'];
            $customer->name = $row['name'];
            $customer->email = $email;
            $customer->password = $row['password'];

            return $customer;
        }

        return null;
	}
}