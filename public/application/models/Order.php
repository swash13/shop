<?php

class OrderModel extends Model {
    const TABLE = 'orders';

    protected static $fields = array('id_customer', 'id_address', 'delivery', 'paing', 'date_add');

    protected static $links = array(
        'customer' => array(
            'model' => 'UserModel',
            'type' => LinkType::FOREIGN_KEY,
            'field' => 'id_customer'
        ),

        'address' => array(
            'model' => 'AddressModel',
            'type' => LinkType::FOREIGN_KEY,
            'field' => 'id_address'
        )
    );

    private $products;

    public function setProducts($products) {
        if ($this->id) {
            $this->products = $products;

            Database::query("
                DELETE FROM `order_items` WHERE `id_order` = {$this->id}
            ");

            $query = "INSERT INTO `order_items` (`id_order`, `id_product`, `quantity`) VALUES ";
            foreach ($products as $product) {
                $query .= "({$this->id}, {$product->getId()}, {$product->cart_quantity}), ";
            }
            $query = rtrim($query, ', ');

            Database::query($query);
        }
    }

    public function getProducts() {
        if ($this->id && empty($this->products)) {
            $rows = Database::getTable("
                SELECT `id_product`, `quantity`
                FROM `order_items`
                WHERE `id_order` = {$this->id}
            ");

            $products = array();

            foreach ($rows as $row) {
                $product = new ProductModel($row['id_product']);
                $product->cart_quantity = $row['quantity'];
                $product->total_price = $product->price * $product->cart_quantity;

                $products[] = $product;
            }

            $this->products = $products;
        }

        return $this->products;
    }

    public function getTotalPrice() {
        $total_price = 0;

        foreach ($this->getProducts() as $product) {
            $total_price += $product->total_price;
        }

        return $total_price;
    }
}