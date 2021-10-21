<?php
	include "./globals.php";
    if ($_SERVER["REMOTE_ADDR"] == "93.123.82.30"){
    	ini_set("display_errors", 1);
        error_reporting(E_ALL & ~(E_STRICT|E_NOTICE));
        //error_reporting(E_ALL);
	}
	$id = $params->getInt("id");
	$act = $params->getString("act");
	$page_heading = $configVars["carts_page"];

	$php_self = "carts.php";
	$php_edit = "carts_ae.php";
	$sm->assign("php_self", $php_self);
	$sm->assign("php_edit", $php_edit);

	$sm->assign("id", $id);
	$sm->assign("act", $act);
	$sm->assign("page_heading", $page_heading);

    $sm->assign("default_office_code", $default_office_code);

	$carts = new Carts();
	$settings = new Settings();

    $districtsObj = new Districts();
    $districts = $districtsObj->getDistricts();
    $sm->assign("districts", $districts);

	if( $params->has("Submit") ){
		$carts->addEditRow($params);
		header("Location: ".$php_self);
		die();
	}

    if($params->has("saveTrackingNumber")) {
        $res = $carts->saveTrackingNumber($params, $id);
	}
    
    $orderStatusesObj = new OrderStatuses();
    $order_statuses = $orderStatusesObj->getOrderStatusesActive();
    $sm->assign("order_statuses", $order_statuses);

	$row["publishdate_value"] = date("m/d/Y");
	if ( $id ){
		$row = $carts->getRecord($id);

        if( $params->has("order_discount_button") ){
            $order_discount_percent = $params->getNumber("order_discount_percent");
            $order_discount_amount = $params->getNumber("order_discount_amount");
            $total_amount_current = $row["order_discount_amount"] > 0.0 ? (float)$row["total_amount"] + (float)$row["order_discount_amount"] : (float)$row["total_amount"];

            if($order_discount_percent > 0.0){
                $order_discount_amount = $total_amount_current * $order_discount_percent / 100;
                $total_amount = $total_amount_current - $order_discount_amount;
            }else if($order_discount_amount > 0.0){
                $total_amount = $total_amount_current - $order_discount_amount;
            }else{
                $total_amount = $total_amount_current; //$row["order_discount_amount"] > 0.0 ? $total_amount_current + (float)$row["order_discount_amount"] : $total_amount_current;
            }

            $fields = array(
                "order_discount_percent" => $order_discount_percent > 0.0 ? $order_discount_percent : 0.0,
                "order_discount_amount" => $order_discount_amount > 0.0 ? $order_discount_amount : 0.0,
                "total_amount" => $total_amount
            );

            $res = $db->autoExecute($carts_table, $fields, DB_AUTOQUERY_UPDATE, " id = '".$id."' "); safeCheck($res);

            header("Location: ".$php_edit."?act=edit&id=".$id."#orderDiscount");
            die();
        }

		$row["postdate_value"] = date("m/d/Y", $row["postdate"]);

        $delivery_cities = $carts->getCitiesByDistrict($row["delivery_region_id"]);
        $billing_cities = $carts->getCitiesByDistrict($row["billing_region_id"]);

        $sm->assign("delivery_cities", $delivery_cities);
        $sm->assign("billing_cities", $billing_cities);

//----------------- Get shipment Info ------------------------------------------
        $deliveryError = "";
        $shipmentInfoArr = array();
        if((int)$row["delivery_tracking_number"]){
            try {
                $shipmentInfo = Delivery::getShipmentInfo((int)$row["delivery_type_id"], (int)$row["delivery_tracking_number"]); // test ot afya: 1051815866298
            }catch (Exception $e) {
                $file = __FILE__; $line = __LINE__;
                $speedyError = $e->getMessage();
                $res = $db->autoExecute($error_log_delivery_table, array("carts_id" => $id, "file" => $file, "line" => $line, "message" => $speedyError, "server_info" => serialize($_SERVER), "error_datetime" => date("Y-m-d H:i")), DB_AUTOQUERY_INSERT); safeCheck($res);
                //header("Location: /message.php?code=403");
                //die("Възникна проблем. Проверете валидността на данните в полетата и опитайте отново!1");
                $deliveryError .= $speedyError;
            }
            if($shipmentInfo){
                if((int)$row["delivery_type_id"] === 1 || (int)$row["delivery_type_id"] === 2){
                    foreach ($shipmentInfo as $status){
                        $elem = array();
                        $elem["moment"] = date("d.m.Y H:m", strtotime($status->getMoment()));
                        $elem["operationDescription"] = $status->getOperationDescription();
                        $elem["operationComment"] = $status->getOperationComment();
                        $shipmentInfoArr[] = $elem;
                    }
                }else if((int)$row["delivery_type_id"] === 3 || (int)$row["delivery_type_id"] === 4){
                    foreach ($shipmentInfo as $status){
                        if($status["destinationType"] == "office"){
                            $destinationType = "пристигнала в офис";
                        }else if($status["destinationType"] == "courier_direction"){
                            $destinationType = "натоварена на линия";
                        }else if($status["destinationType"] == "client"){
                            $destinationType = "доставена";
                        }else{
                            $destinationType = $status["destinationType"];
                        }
                        $elem = array();
                        $elem["moment"] = date("d.m.Y H:m", substr($status["time"],0,10));
                        $elem["operationDescription"] = $destinationType;
                        $elem["operationComment"] = $status["destinationDetails"];
                        $shipmentInfoArr[] = $elem;
                    }
                }
            }
        }

        $sm->assign("shipmentInfoArr", $shipmentInfoArr);
//----------------- END Get shipment Info --------------------------------------

        if($act == "change_status") {
            $status = $params->getInt("status");

            $orderStatusesObj->changeStatus($status, $id);

            header("Location: ".$php_edit."?id=".$id."&act=edit");
            die();
        }

        if($params->has("sendStatusEmail")) {
            $carts->sendOrderTemplate($row["status"], $id);
            header("Location: ".$php_edit."?id=".$id."&act=edit");
            die();
        }

	}
    if($act === "delete_from_cart"){
        $product_id = $params->getInt("product_id");
        $carts->deleteCartProductByCartIdAndProductId($id, $product_id);
        $carts->cartCalculateTotal($id, $row["user_id"]);

        header("Location: ".$php_edit."?act=edit&id=".$id);
		die();
    }
    if ($params->has("buttonUpdateQuantities")){
		$cart_products = CartsProducts::getCartProductsSimple($id);

		foreach($cart_products as $k => $v){
			if ($v["option_id"]){
				$option_id = $v["option_id"];
			}else{
				$option_id = "";
			}

			$v["quantity"] = $params->getInt("quantity".$v["product_id"].$option_id);
			$v["product_price_total"] = $v["product_price_discount"] > 0.0 ? $v["quantity"]*$v["product_price_discount"] : $v["quantity"]*$v["product_price"];
			$v["product_weight_total"] = $v["quantity"]*$v["product_weight"];

			$fields = array(
								"quantity" => $v["quantity"],
								"product_price_total" => $v["product_price_total"] ,
								"product_weight_total" => $v["product_weight_total"]
							);

			$res = $db->autoExecute($carts_products_table, $fields, DB_AUTOQUERY_UPDATE, " id = '".$v["id"]."' "); safeCheck($res);

			$cart_products[$k] = $v;
		}

		$tmp = $db->getAll("SELECT * FROM ".$carts_products_table." WHERE cart_id = '".$id."'"); safeCheck($tmp);

		$carts->cartCalculateTotal($id, $row["user_id"]);

		header("Location: ".$php_edit."?act=edit&id=".$id);
		die();
	}

    if ($params->has("buttonUpdateDeliveryType")){
		$delivery_office_id = $params->getInt("delivery_office_id");

		$fields["delivery_type"] = $params->getInt("delivery_type");
		$fields["delivery_type_id"] = $fields["delivery_type"];
		$fields["delivery_office_id"] = $delivery_office_id;

        if($useShippingApis && $cart_products["total_weight"] > 0.0){
            $districtsObj = new Districts();
            $citiesObj = new Cities();
            $districtName = $districtsObj->getRecord($row["delivery_region_id"])["name"];
            $cityName = $citiesObj->getRecord($row["delivery_city_id"])["name"];

            try {
                $receiverSiteId = Delivery::getCourierSiteId($fields["delivery_type"], $districtName, $cityName);
                if ( $fields["delivery_type"] == 1 || $fields["delivery_type"] == 3){
                    $delivery_info = Delivery::getDeliveryAmountAndCodBase($fields["delivery_type"], $row["total_amount"], $row["weight"], $receiverSiteId);
                }else if($fields["delivery_type"] == 2 || $fields["delivery_type"] == 4){
                    $delivery_info = Delivery::getDeliveryAmountAndCodBase($fields["delivery_type"], $row["total_amount"], $row["weight"], $receiverSiteId, $delivery_office_id);
                }
            }catch (Exception $e) {
                $file = __FILE__; $line = __LINE__;
                $speedyError = $e->getMessage();
                $res = $db->autoExecute($error_log_delivery_table, array("delivery_type_id" => $fields["delivery_type"], "carts_id" => $id, "file" => $file, "line" => $line, "message" => $speedyError, "server_info" => serialize($_SERVER), "error_datetime" => date("Y-m-d H:i")), DB_AUTOQUERY_INSERT); safeCheck($res);
                //header("Location: /message.php?code=403");
                die("Възникна проблем. Проверете валидността на данните в полетата и опитайте отново!2");
            }

            $cod_amount = $delivery_info["codBase"];
            $delivery_amount = $delivery_info["deliveryAmount"];
        }else{
            $cod_amount = $row["total_amount"];
            $delivery_amount = CartDiscounts::getCustomDeliveryAmount($row["total_amount"], $row["weight"]);
        }


		$fields_delivery["delivery_type_id"] = $fields["delivery_type"];
		$fields_delivery["cod_amount"] = (double)$cod_amount;
		$fields_delivery["delivery_amount"] = (double)$delivery_amount;
		//$fields_delivery["delivery_postcode"] = $delivery_postcode; //commented because $delivery_postcode is NULL here and it deletes the field after buttonUpdateDeliveryType is clicked
		$fields_delivery["delivery_office_id"] = $fields["delivery_type"] == 2 || $fields["delivery_type"] == 4 ? $delivery_office_id : null;
		//$row["delivery_amount"] = (double)$delivery_amount;

        $res = $db->autoExecute($carts_table, $fields_delivery, DB_AUTOQUERY_UPDATE, " id = '".$id."' "); safeCheck($res);
		//$res = $db->autoExecute($carts_user_table, $fields_delivery, DB_AUTOQUERY_UPDATE, " cart_id = '".$id."' "); safeCheck($res);

		$row = $db->getRow("SELECT * FROM ".$carts_table." WHERE id = '".$id."'"); safeCheck($row);

		//$total_amount = cartCalculateTotal($id, $row["user_id"]);

		header("Location: carts_ae.php?act=edit&id=".$id);
		die();
	}

    $bonusPointsAmount = $useBonusPoints ? $row["bonus_points_amount"] : 0.0;
    if ( $row["discount_free_delivery"] ){
		$total_amount = $row["total_amount"] - $bonusPointsAmount - $row["discount_amount_delivery"];
	}else{
		$total_amount = $row["total_amount"] + $row["delivery_amount"] - $bonusPointsAmount - $row["discount_amount_delivery"];
	}
    $total_amount_customer_currency = 0;
    $abb_before_amount = null;
    $currency_abb = "";
    if($row["customer_currency_code"] && $row["customer_currency_code"] != "BGN"){
        $currency = Currencies::getRecordByCode($row["customer_currency_code"]);
        $abb_before_amount = $currency["abb_before_amount"];
        $currency_abb = $currency["abbreviation"];
        $convertor = new Convert();
        $total_amount_customer_currency = $convertor->convert($total_amount, false, $row["customer_currency_code"]);
    }
    $sm->assign("abb_before_amount", $abb_before_amount);
    $sm->assign("currency_abb", $currency_abb);
    $sm->assign("total_amount_customer_currency", $total_amount_customer_currency);
    $sm->assign("order_discount_amount", $row["order_discount_amount"]);
	$sm->assign("total_amount", $total_amount);

	$row = $carts->getRecord($id);

	$sm->assign("row", $row);


    if ( $params->has("generateBOLDelete") ){
		$carts->generateBOLDelete($row);

		header("Location: ".$php_edit."?act=edit&id=".$id);
		die();
	}
    $errorMessage = "";
    $message = "";
    if ( $params->has("generateBOL") ){
		$result = $carts->generateBOL($row);
        
        if(isset($result["errorMessage"])){
           // var_dump($result);
            $errorMessage = $result["errorMessage"];
        }

        if(isset($result["message"])) {
            $message = $result["message"];
        }

		header("Location: ".$php_edit."?act=edit&id=".$id."&errorMessage=".$errorMessage."&message=".$message);
		die();
	}
    $present_product = array();
    if($row["present_product_id"] > 0){
        $productsObj = new Products();
        $present_product = $productsObj->getRecord((int)$row["present_product_id"]);
    }
    $sm->assign("present_product", $present_product);

    $sm->assign("errorMessage", $params->getString("errorMessage"));

    $sm->assign("message", $params->getString("message"));

    $cart = $carts->getCartProducts($id);
    $sm->assign("cart", $cart);
    $sm->assign("carts_ae_page", 1);

	$sm->display("admin/carts_ae.html");
?>
