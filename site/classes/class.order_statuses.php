<?php
	class OrderStatuses extends Settings{

		public static function getRecord(int $id){
			global $db;
			global $lng;
			global $order_statuses_table;
			
			$row = $db->getRow("SELECT
                                    *,
                                    name_{$lng} AS name
                                FROM 
                                    ".$order_statuses_table."
                                WHERE 
                                    id = {$id}
                                AND edate = 0"); safeCheck($row);
			return $row;
		}
		
		public function getOrderStatuses(){
			global $db;
			global $lng;
			global $order_statuses_table;
			
			$order_statuses = $db->getAll("SELECT *, name_{$lng} AS name FROM ".$order_statuses_table." WHERE edate = 0 ORDER BY pos"); safeCheck($order_statuses);
			
			return $order_statuses;
		}
        
		public function getOrderStatusesActive(){
			global $db;
			global $lng;
			global $order_statuses_table;
			
			$order_statuses = $db->getAll("SELECT *, name_{$lng} AS name FROM ".$order_statuses_table." WHERE edate = 0 AND active = 1 ORDER BY pos"); safeCheck($order_statuses);
			
			return $order_statuses;
		}
		
        public function changeStatus($status, $cartId, $finaliseOrder = false) {
            global $db;
            global $carts_table;
            global $users_table;
            global $products_table;
            global $variants_table;

            $statusRow = self::getRecord($status);

            if($statusRow["reduce_products_quantity"] == 1){
                $cartProducts = CartsProducts::getCartProductsSimple((int)$cartId);
                foreach ($cartProducts as $v) {
                    $product_id = $v["product_id"];
                    $quantity = $v["quantity"] > 0 ? (int)$v["quantity"] : 0;
                    if($v["option_id"] > 0){
                        $option_id = (int)$v["option_id"];
                        $res = $db->query("UPDATE {$variants_table} SET quantity = quantity - {$quantity} WHERE product_id = $product_id AND option_id = {$option_id} AND quantity > 0 AND edate = 0"); safeCheck($res);
                    }
                    $res = $db->query("UPDATE {$products_table} SET quantity = quantity - {$quantity} WHERE id = $product_id AND quantity > 0 AND edate = 0"); safeCheck($res);
                }
            }
            
            $sql_update = "status = '".$status."' ";
            if($finaliseOrder){
                $sql_update .= ", finalised = 1 ";
            }
            if ($status == 11) {
                $sql_update .= ", card_paid = 1 ";
            }
            if ( $statusRow["active"] && $statusRow["bonus_points_actions"] ){
                $row = $db->getRow("SELECT * FROM ".$carts_table." WHERE id = '".$cartId."'");
                if (  $row["user_id"] ){
                    $tmp_user = $db->getRow("SELECT * FROM ".$users_table." WHERE edate = 0 AND id = '".$row["user_id"]."'"); safeCheck($tmp_user);
                    if ( $row["bonus_points_number"] > 0.0 ){
                        if ( $tmp_user["bonus_points"] >= $row["bonus_points_number"] ){
                            $res = $db->autoExecute($users_table, array("bonus_points" => $tmp_user["bonus_points"] - $row["bonus_points_number"]), DB_AUTOQUERY_UPDATE, " id = '".$row["user_id"]."' "); safeCheck($res);
                        }else{
                            $res = $db->autoExecute($users_table, array("bonus_points" => 0), DB_AUTOQUERY_UPDATE, " id = '".$row["user_id"]."' "); safeCheck($res);
                        }
                    }
                    if ( $row["bonus_points_won_number"] > 0.0 && $row["bonus_points_calculated"] == 0 ){
                        $tmp_user = $db->getRow("SELECT * FROM ".$users_table." WHERE edate = 0 AND id = '".$row["user_id"]."'"); safeCheck($tmp_user);
                        $res = $db->autoExecute($users_table, array("bonus_points" => $tmp_user["bonus_points"] + $row["bonus_points_won_number"]), DB_AUTOQUERY_UPDATE, " id = '".$row["user_id"]."' "); safeCheck($res);
                        $res = $db->autoExecute($carts_table, array("bonus_points_calculated" => 1), DB_AUTOQUERY_UPDATE, " id = '".$cartId."' "); safeCheck($res);
                    }
                }
            }else if ( $statusRow["active"] && $statusRow["bonus_points_actions_revert"] ){
                $row = $db->getRow("SELECT * FROM ".$carts_table." WHERE id = '".$cartId."'");
                $tmp_user = $db->getRow("SELECT * FROM ".$users_table." WHERE edate = 0 AND id = '".$row["user_id"]."'"); safeCheck($tmp_user);
                if ( $tmp_user["bonus_points_tmp"] > 0 ){
                    $res = $db->autoExecute($users_table, array("bonus_points" => $tmp_user["bonus_points_tmp"], "bonus_points_tmp" => 0), DB_AUTOQUERY_UPDATE, " id = '".$row["user_id"]."' "); safeCheck($res);
                }
            }
            
            $res = $db->query("UPDATE " . $carts_table . " SET {$sql_update} WHERE id = " . $cartId); safeCheck($res);
        }
		
	}
