<?php

class CartsProducts extends Settings {

    public static function getRecord(int $id = 0) {
        global $db;
        global $carts_products_table;

        $row = $db->getRow("SELECT * FROM " . $carts_products_table . " WHERE id = '" . $id . "'"); safeCheck($row);

        return $row;
    }

    public static function getCartProductsSimple(int $cart_id) {
        global $db;
        global $carts_products_table;

        $res = $db->getAll("SELECT * FROM " . $carts_products_table . " WHERE cart_id = '" . $cart_id . "' AND edate = 0"); safeCheck($res);

        return $res;
    }

}
