<?php
	class OrderStatuses extends Settings{

		public $pagination = "";

		public function getRecord(int $id){
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

		function addEditRow($params){
			global $db;
			global $order_statuses_table;

			$act = $params->getString("act");
			$id = $params->getInt("id");
			$fields = array(
				'name_bg'           => $params->getString("name_bg"),
				'name_en'           => $params->getString('name_en'),
				'name_de'           => $params->getString('name_de'),
				'name_ru'           => $params->getString('name_ru'),
				'name_ro'           => $params->getString('name_ro'),
				'subject_bg'        => $params->getString('subject_bg'),
				'subject_en'        => $params->getString('subject_en'),
				'subject_de'        => $params->getString('subject_de'),
				'subject_ru'        => $params->getString('subject_ru'),
				'subject_ro'        => $params->getString('subject_ro'),
				'email_text_bg'		=> $params->getString('email_text_bg', false),
				'email_text_en'		=> $params->getString('email_text_en', false),
				'email_text_de'		=> $params->getString('email_text_de', false),
				'email_text_ru'		=> $params->getString('email_text_ru', false),
				'email_text_ro'		=> $params->getString('email_text_ro', false),
				'bonus_points_actions'          => $params->getInt("bonus_points_actions") === 1 ? 1 : 0,
				'bonus_points_actions_revert'   => $params->getInt("bonus_points_actions_revert") === 1 ? 1 : 0,
				'reduce_products_quantity'      => $params->getInt("reduce_products_quantity") === 1 ? 1 : 0,
				'revert_products_quantity'      => $params->getInt("revert_products_quantity") === 1 ? 1 : 0,
				'active'			=> $params->getInt("active") === 1 ? 1 : 0,
				'cms_user_id'		=> $_SESSION["uid"],
			);

			if($act == "add") {
				shiftPos($db, $order_statuses_table);
				$res = $db->autoExecute($order_statuses_table,$fields,DB_AUTOQUERY_INSERT); safeCheck($res);
				$id = mysqli_insert_id($db->connection);
			}

			if($act == "edit") {
				$id = $params->getInt("id");
				$res = $db->autoExecute($order_statuses_table,$fields,DB_AUTOQUERY_UPDATE,"id = " . $id);safeCheck($res);
			}
		}

		function deleteField($id, $field){
			global $db;
			global $order_statuses_table;

			$res = $db->autoExecute($order_statuses_table, array( $field => "" ), DB_AUTOQUERY_UPDATE, " id = '".$id."' "); safeCheck($res);

			return $res;
		}

		function deleteRecord($id){
			global $db;
			global $order_statuses_table;

			$fields = array(
								"edate" => time()
							);
			$res = $db->autoExecute($order_statuses_table, $fields, DB_AUTOQUERY_UPDATE, " id = '".$id."' "); safeCheck($res);

			return $res;
		}

		function getOrderStatuses(){
			global $db;
			global $lng;
			global $order_statuses_table;

			$order_statuses = $db->getAll("SELECT *, name_{$lng} AS name FROM ".$order_statuses_table." WHERE edate = 0 ORDER BY pos"); safeCheck($order_statuses);

			return $order_statuses;
		}

		function getOrderStatusesActive(){
			global $db;
			global $lng;
			global $order_statuses_table;

			$order_statuses = $db->getAll("SELECT *, name_{$lng} AS name FROM ".$order_statuses_table." WHERE edate = 0 AND active = 1 ORDER BY pos"); safeCheck($order_statuses);

			return $order_statuses;
		}

		function getOrderStatusesSale(){
			global $db;
			global $lng;
			global $order_statuses_table;

			$order_statuses = $db->getAll("SELECT *, name_{$lng} AS name FROM ".$order_statuses_table." WHERE edate = 0 AND active = 1 AND is_sale = 1 ORDER BY pos"); safeCheck($order_statuses);

			return $order_statuses;
		}

        public function changeStatus($status, $cartId, $finaliseOrder = false) {
            global $db;
            global $carts_table;
            global $users_table;
            global $products_table;
            global $variants_table;

            $statusRow = $this->getRecord($status);

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

			if($statusRow["revert_products_quantity"] == 1){
                $cartProducts = CartsProducts::getCartProductsSimple((int)$cartId);

                foreach ($cartProducts as $v) {
                    $product_id = $v["product_id"];
                    $quantity = $v["quantity"] > 0 ? (int)$v["quantity"] : 0;
                    if($v["option_id"] > 0){
                        $option_id = (int)$v["option_id"];
                        $res = $db->query("UPDATE {$variants_table} SET quantity = quantity + {$quantity} WHERE product_id = $product_id AND option_id = {$option_id} AND quantity > 0 AND edate = 0"); safeCheck($res);
                    }
                    $res = $db->query("UPDATE {$products_table} SET quantity = quantity + {$quantity} WHERE id = $product_id AND quantity > 0 AND edate = 0"); safeCheck($res);
                }
            }

            $sql_update = "status = '".$status."' ";
            if($finaliseOrder){
                $sql_update .= ", finalised = 1 ";
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

?>
