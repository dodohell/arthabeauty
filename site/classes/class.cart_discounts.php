<?php
	class CartDiscounts extends Settings{
		
		public static function getRecord(int $id){
			global $db;
			global $cart_discounts_table;
			
			$row = $db->getRow("SELECT * FROM ".$cart_discounts_table." WHERE id = $id AND edate = 0"); safeCheck($row);
			
			return $row;
		}
		
		public static function getCartDiscounts($name = '', $status = NULL){
			global $db;
			global $lng;
			global $cart_discounts_table;

			$sql_where = '';
            if($status === 1){
                $sql_where .= ' AND active = "1"';
            }
            if($status === 0){
                $sql_where .= ' AND active = "0"';
            }
            if($name){
                $sql_where .= ' AND name_bg LIKE "%'.$name .'%"';
            }
            $sql = "SELECT
                        id,
                        name_{$lng} AS name,
                        cart_discount_type,
                        order_amount_from,
                        order_amount_to,
                        order_weight_from,
                        order_weight_to,
                        value,
                        pos,
                        active
                    FROM " . $cart_discounts_table . "
                    WHERE 
                        edate = 0 ". $sql_where ."
                    ORDER BY pos";
            $items = $db->getAll($sql); safeCheck($items);
            
			return $items;
		}
        
        public static function isFreeDelivery(float $total_amount, float $weight): bool{
            
            $cartDiscounts = CartDiscounts::getCartDiscounts("", 1);
            
            foreach ($cartDiscounts as $k => $v) {
                if($v["cart_discount_type"] == 1){
                    $order_amount_from = (double)$v["order_amount_from"]; //>= 0.0 ? (double)$v["order_amount_from"] : 0;
                    $order_amount_to = (double)$v["order_amount_to"]; //>= 0.0 ? (double)$v["order_amount_to"] : 0;
                    $order_weight_from = (double)$v["order_weight_from"]; //>= 0.0 ? (double)$v["order_weight_from"] : 0;
                    $order_weight_to = (double)$v["order_weight_to"]; //>= 0.0 ? (double)$v["order_weight_to"] : 0;
                    if( $total_amount >= $order_amount_from && $order_amount_to = $order_amount_to > 0.0 ? $total_amount <= $order_amount_to : true ){
                        if ( $weight >= $order_weight_from && $order_weight_to = $order_weight_to > 0.0 ? $weight <= $order_weight_to : true ){
                            return TRUE;
                        }
                    }
                }
            }
            
            return FALSE;
        }
        
        /**
         * if delivery type is not some of the Econt types we use custom rules from cart_discounts
         * 
         * @param float $total_amount
         * @param float $weight
         * @return float delivery amount
         */
        public static function getCustomDeliveryAmount(float $total_amount, float $weight): float{
            
            $cartDiscounts = CartDiscounts::getCartDiscounts("", 1);
            $cartDiscounts = $cartDiscounts ? $cartDiscounts : [];
            
            foreach ($cartDiscounts as $k => $v) {
                if($v["cart_discount_type"] == 2){
                    $order_amount_from = (double)$v["order_amount_from"]; //>= 0.0 ? (double)$v["order_amount_from"] : 0;
                    $order_amount_to = (double)$v["order_amount_to"]; //>= 0.0 ? (double)$v["order_amount_to"] : 0;
                    $order_weight_from = (double)$v["order_weight_from"]; //>= 0.0 ? (double)$v["order_weight_from"] : 0;
                    $order_weight_to = (double)$v["order_weight_to"]; //>= 0.0 ? (double)$v["order_weight_to"] : 0;
                    $value = (double)$v["value"]; //>= 0.0 ? (double)$v["order_weight_to"] : 0;
                    if( $total_amount >= $order_amount_from && $order_amount_to = $order_amount_to > 0.0 ? $total_amount <= $order_amount_to : true ){
                        if ( $weight >= $order_weight_from && $order_weight_to = $order_weight_to > 0.0 ? $weight <= $order_weight_to : true ){
                            return $value;
                        }
                    }
                }
            }
            
            return 0.0;
        }
		
        
        
	}