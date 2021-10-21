<?php
	include("globals.php");
    include("./classes/class.econt.php");
    
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
    $sm->assign("econtSender", $econtSender);
    
	$carts = new Carts();
	$settings = new Settings();
	
//    $econtDelivery = new econt($econt_user, $econt_pass);
//    $regions = $econtDelivery->getRegions();
    
//	$sm->assign("regions", $regions);
    
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
		$row["postdate_value"] = date("m/d/Y", $row["postdate"]);
        
        $delivery_cities = $carts->getCitiesByDistrict($row["delivery_region_id"]);
        $billing_cities = $carts->getCitiesByDistrict($row["billing_region_id"]);
        
        $sm->assign("delivery_cities", $delivery_cities);
        $sm->assign("billing_cities", $billing_cities);
        
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
        
        header("Location: ".$php_edit."?act=edit&id=".$id);
		die();
    }
    if ($params->has("buttonUpdateQuantities")){
		$cart_products = CartsProducts::getCartProductsSimple($id);
		foreach($cart_products as $k => $v){
			if ($v["variant_id"]){
				$option_id = $v["variant_id"];
			}else{
				$option_id = "";
			}
			
			$v["quantity"] = $params->getInt("quantity".$v["product_id"].$option_id);
			$v["product_price_total"] = $v["quantity"]*$v["product_price"];
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
		$fields["delivery_type_id"] = $params->getInt("delivery_type");
		$fields["delivery_office_econt"] = $params->getInt("delivery_office_econt");
		
		$receiver["clientCompany"] = $row["delivery_first_name"]." ".$row["delivery_last_name"]; //фирма получател;
		$receiver["clientName"] = $row["delivery_first_name"]." ".$row["delivery_last_name"]; //име на получателя;
		$receiver["clientMail"] = $row["email"]; // email на получателя
        
        $deliveryCity = $db->getRow("SELECT c.name_bg AS name FROM $cities_table AS c WHERE c.id = ".$row["delivery_city_id"]." AND c.edate = 0");
        
		$receiver["deliveryCityBG"] = $deliveryCity["name"]; //фирма получател;
		$receiver["deliveryCityPostCode"] = $row["delivery_postcode"]; //фирма получател;
		$receiver["deliveryOfficeSelected"] = ""; //фирма получател;
		$receiver["deliveryQuarter"] = ""; //квартал;
		$receiver["deliveryStreet"] = $row["delivery_address_1"]; //улица;
		$receiver["deliveryStreetNum"] = ""; //номер на улицата;
		$receiver["deliveryBlock"] = ""; //блок;
		$receiver["deliveryEntrance"] = "";  //вход;
		$receiver["deliveryFloor"] = ""; //етаж;
		$receiver["deliveryApartment"] = ""; //апартамент;
		$receiver["streetOther"] = ""; //друга информация;
		$receiver["clientPhone"] = $row["delivery_phone"]; // телефон на получателя;
        
		$econtDelivery = new econt($econt_user, $econt_pass);
		
		$delivery_office_econt = $params->getInt("delivery_office_econt");
		if ( $delivery_office_econt ){
			$office_tmp = $econtDelivery->getOfficeById($delivery_office_econt);
			$receiver["deliveryOfficeSelected"] = (string)$office_tmp["office_code"]; //фирма получател;
			$receiver["deliveryQuarter"] = ""; //квартал;
			
			$receiver["deliveryCityPostCode"] = (string)$office_tmp["post_code"]; //фирма получател;
			$receiver["deliveryQuarter"] = (string)$office_tmp["address_details"]->quarter_name; //квартал;
			$receiver["deliveryStreet"] = (string)$office_tmp["address_details"]->street_name; //улица;
			$receiver["deliveryStreetNum"] = (string)$office_tmp["address_details"]->num; //номер на улицата;
			$receiver["deliveryBlock"] = (string)$office_tmp["address_details"]->bl; //блок;
			$receiver["deliveryEntrance"] = (string)$office_tmp["address_details"]->vh;  //вход;
			$receiver["deliveryFloor"] = (string)$office_tmp["address_details"]->et; //етаж;
			$receiver["deliveryApartment"] = (string)$office_tmp["address_details"]->ap; //апартамент;
			$receiver["streetOther"] = (string)$office_tmp["address_details"]->other; //друга информация;
            
			$delivery_postcode = (string)$office_tmp["post_code"];
		}
		if ( $fields["delivery_type_id"] == 1 ){
			$deliveryTypeText = "DOOR";
		}
		if ( $fields["delivery_type_id"] == 2 ){
			$deliveryTypeText = "OFFICE";
		}
		//dbg($row);
		$xml_delivery = $econtDelivery->createShippingRequest($id, $receiver, $deliveryTypeText, $row["total_amount"], $row["weight"], 1, "", $row["discount_free_delivery"], $row["payment_type_id"]);
        
		$xml_delivery_content = $econtDelivery->readXML($xml_delivery);
		$xml_delivery_prices = $xml_delivery_content->result->e->loading_price;
        
		$cod_amount = $xml_delivery_prices->CD;
		$delivery_amount = $xml_delivery_prices->total;
        
//        echo "<pre>";
//        var_dump($id);
//        var_dump($receiver);
//        var_dump($deliveryTypeText);
//        var_dump($row["total_amount"]);
//        var_dump($row["weight"]);
//        var_dump(1);
//        var_dump('""');
//        var_dump($row["discount_free_delivery"]);
//        var_dump("8604");
//        var_dump($row["payment_type_id"]);
//        var_dump("-------------------");
//        var_dump($cod_amount);
//        var_dump($delivery_amount);
//        print_r($xml_delivery_content);
//        echo "------------";
//        var_dump($xml_delivery);
//        var_dump($xml_delivery_prices);
//        var_dump($id);
//        echo "</pre>";
//        exit();
        
		$fields_delivery["delivery_type_id"] = $params->getInt("delivery_type");
		$fields_delivery["cod_amount"] = (double)$cod_amount;
		$fields_delivery["delivery_amount"] = (double)$delivery_amount;
		//$fields_delivery["delivery_postcode"] = $delivery_postcode; //commented because $delivery_postcode is NULL here and it deletes the field after buttonUpdateDeliveryType is clicked
		$fields_delivery["delivery_office_econt"] = $delivery_office_econt;
		
		$res = $db->autoExecute($carts_table, $fields_delivery, DB_AUTOQUERY_UPDATE, " id = ".$id." "); safeCheck($res);
		//$res = $db->autoExecute($carts_user_table, $fields_delivery, DB_AUTOQUERY_UPDATE, " cart_id = '".$id."' "); safeCheck($res);
		
		$row = $carts->getRecord($id);
		
		$total_amount = $carts->cartCalculateTotal($id, $row["user_id"]);
		
		header("Location: ".$php_edit."?act=edit&id=".$id);
		die();
	}
    
    if ( $row["discount_free_delivery"] ){
		// $total_amount = $row["total_amount"]-$row["bonus_points_amount"] - $row["discount_amount"];
		$total_amount = $row["total_amount"]-$row["bonus_points_amount"] - $row["discount_amount_delivery"] ;
	}else{
		// $total_amount = $row["total_amount"]+$row["delivery_amount"]-$row["bonus_points_amount"]- $row["discount_amount"];
		$total_amount = $row["total_amount"]+$row["delivery_amount"]-$row["bonus_points_amount"];
	}
    
	$sm->assign("total_amount", number_format($total_amount,2));
	
	$row = $carts->getRecord($id);
    
	$sm->assign("row", $row);
    
    
    if ( $params->has("generateEcontDelete") ){
		$carts->generateEcontDelete($id, $row["delivery_tracking_number"]);
		
		header("Location: ".$php_edit."?act=edit&id=".$id);
		die();
	}
    
    if ( $params->has("generateEcont") ){
		$carts->generateEcont($id, $row);
		header("Location: ".$php_edit."?act=edit&id=".$id);
		die();
	}
    
    
    $cart = $carts->getCartProducts($id);
    $sm->assign("cart", $cart);
    $sm->assign("carts_ae_page", 1);
    
	$sm->display("admin/carts_ae.html");
?>