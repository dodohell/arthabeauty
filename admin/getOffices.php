<?php
	include("globals.php");
    
	$delivery_type = (int)$_REQUEST["delivery_type"];
	$region_id = (int)$_REQUEST["region_id"];
	$city_id = (int)$_REQUEST["city_id"];
    
    if($delivery_type && $region_id && $city_id){
        try {
            $offices = Delivery::getOfficesSimple($delivery_type, $region_id, $city_id, 3);
        }catch (Exception $e) {
            $file = __FILE__; $line = __LINE__;
            $speedyError = $e->getMessage();
            $res = $db->autoExecute($error_log_delivery_table, array("carts_id" => $_SESSION["cart_id"], "file" => $file, "line" => $line, "message" => $speedyError, "server_info" => serialize($_SERVER), "error_datetime" => date("Y-m-d H:i")), DB_AUTOQUERY_INSERT); safeCheck($res);
            //header("Location: /message.php?code=403");
            die("Възникна проблем. Проверете валидността на данните в полетата и опитайте отново!");
        }
    }
?>