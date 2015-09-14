<?php

class CartModel {
    public $customer;

    private $products = array();

    public function addProduct($id_product, $quantity) {
        $product = new ProductModel($id_product);

        if (array_key_exists($id_product, $this->products)) {
            $this->products[$id_product]->cart_quantity += $quantity;
        } else {
            $this->products[$id_product] = $product;
            $this->products[$id_product]->cart_quantity = $quantity;
        }

        $this->products[$id_product]->total_price = $this->products[$id_product]->price * $this->products[$id_product]->cart_quantity;


        if ($this->products[$id_product]->cart_quantity < 1) {
            unset($this->products[$id_product]);
        }
    }

    public function deleteProduct($id_product) {
        unset($this->products[$id_product]);
    }

    public function getProducts() {
        return $this->products;
    }

    public function getProduct($id_product) {
        return $this->products[$id_product];
    }

    public function getTotalPrice() {
        $total_price = 0;

        foreach ($this->products as $product) {
            $total_price += $product->total_price;
        }

        return $total_price;
    }

}