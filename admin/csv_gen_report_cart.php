<?php
header("Content-type: text/csv charset=UTF-8");
header("Content-Disposition: attachment; filename=".(time()+331122).".csv");
header("Content-Transfer-Encoding: UTF-8");
header("Pragma: no-cache");
header("Expires: 0");
// echo "\xEF\xBB\xBF";
	include "./globalsMin.php";

    if ( $params->has("Filter") ){
        $barcode = $params->getString("barcode");
        $product_id = $params->getInt("product_id");
        $brand_id = $params->getInt("brand_id");
        $user_id = $params->getInt("user_id");
        $not_finalised = $params->getInt("not_finalised");
        $date_from = html_entity_decode($params->getString("date_from"));
        $date_to = html_entity_decode($params->getString("date_to"));

        $sql_where = '';

	    if ($date_from){
	        $date_tmp = explode('/', $date_from);
	        $date_from_time = mktime(0,0,0,$date_tmp[1],$date_tmp[0],$date_tmp[2]);
	        $sql_where .= " AND DATE(from_unixtime(carts.postdate)) >= '". date("Y-m-d", $date_from_time)."'";
	    }else{
	        $date_from = date("d/m/Y");
	        $sql_where .= " AND DATE(from_unixtime(carts.postdate)) >= '". date("Y-m-d")."'";
	    }
	    $sm->assign("date_from", $date_from);
	    if ($date_to){
	        $date_tmp = explode('/', $date_to);
	        $date_to_time = mktime(0,0,0,$date_tmp[1],$date_tmp[0],$date_tmp[2]);
	        $sql_where .= " AND DATE(from_unixtime(carts.postdate)) <= '".date("Y-m-d", $date_to_time)."'";
	    }else{
	        $date_to = date("d/m/Y");
	        $sql_where .= " AND DATE(from_unixtime(carts.postdate)) <= '".date("Y-m-d")."'";
	    }
	}
	$search_string = $_REQUEST["search_string"];

	$sm->assign("qqSStr", $_SERVER["QUERY_STRING"]);

    $sql = "SELECT
				cp.cart_id,
				s.name_bg AS status,
				p.name_en,
				b.name_bg AS brand,
				cp.product_price_supply,
				cp.product_price_total,
				cp.product_price_discount,
				cp.product_price,
				cp.quantity,
				o.option_text,
				FROM_UNIXTIME(carts.order_timestamp) AS order_datetime,
				(SELECT CONCAT_WS(' ', cu.billing_first_name, cu.billing_last_name) FROM arthabeauty_carts_user AS cu WHERE cu.cart_id = carts.id ORDER BY id DESC LIMIT 1) AS `carts_user_name`
			FROM
				arthabeauty_carts_products AS cp
			INNER JOIN arthabeauty_products AS p ON p.id = cp.product_id
			LEFT JOIN arthabeauty_brands as b ON p.brand_id = b.id
			LEFT JOIN arthabeauty_options AS o ON o.id = cp.option_id
			INNER JOIN arthabeauty_carts AS carts ON carts.id = cp.cart_id
			LEFT JOIN arthabeauty_order_statuses AS s ON s.id = carts.status
			WHERE
				carts.finalised = 1
			AND cp.edate = 0
			AND carts.edate = 0
			{$sql_where}
			ORDER BY cart_id
	";

	$items = $db->getAll($sql); safeCheck($items);


	$productsCount = 0;
	$productsTotalPrice = 0;
	foreach ($items as $k => $v) {
    	$productsCount += $v["product_quantity"];
    	$productsTotalPrice += $v["price_total"];
  	}

	function maybeEncodeCSVField($string) {
		$string = str_replace("&nbsp;", " ", $string);
		$string = str_replace("&amp;", " ", $string);
		$string = str_replace("&#039;", " ", $string);
    	if(strpos($string, ',') !== false || strpos($string, '"') !== false || strpos($string, "\n") !== false) {
        	$string = '"' . str_replace('"', '""', $string) . '"';
    	}

    	return $string;
	}

	// echo "Поръчка,Име потребител,Име бълг.,Име англ.,Вариант,Цена,Цена намалена,Количество,Сума,Дата\n";
	echo "Поръчка,Статус,Име потребител,Марка,Име англ.,Вариант,Доставна цена,Продажна цена,Количество,Общо,Дата и час на поръчката\n";
	foreach($items as $k => $v){
		echo
			maybeEncodeCSVField($v["cart_id"]). ",".
			maybeEncodeCSVField($v["status"]). ",".
			maybeEncodeCSVField($v["carts_user_name"])."," .
			maybeEncodeCSVField($v["brand"])."," .
			maybeEncodeCSVField($v["name_en"])."," .
			maybeEncodeCSVField($v["option_text"])."," .
			maybeEncodeCSVField($v["product_price_supply"])."," .
			(($v["product_price_discount"] > 0)? maybeEncodeCSVField($v["product_price_discount"]): maybeEncodeCSVField($v["product_price"]))."," .
			maybeEncodeCSVField($v["quantity"])."," .
			maybeEncodeCSVField($v["product_price_total"])."," .
			maybeEncodeCSVField($v["order_datetime"])."\n";
	}

?>
