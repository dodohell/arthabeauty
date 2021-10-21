<?php
header("Content-type: text/csv charset=UTF-8");
header("Content-Disposition: attachment; filename=".(time()+331122).".csv");
header("Content-Transfer-Encoding: UTF-8");
header("Pragma: no-cache");
header("Expires: 0");
//	echo "\xEF\xBB\xBF";
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
        
        if ($barcode){
            $sql_barcode = " AND (p.barcode = '{$barcode}' OR v.`code` = '{$barcode}' ) ";
            $sm->assign("barcode", $barcode);
        }
        
        if ( $user_id ){
            $sql_where .= " AND c.user_id = '".$user_id."'"; 
            $sm->assign("user_id", $user_id);
        }
        
        if ($product_id){
            $sql_product_id = " AND cp.product_id = {$product_id}"; 
            $sm->assign("product_id", $product_id);
        }
        if ($brand_id){
            $sql_brands = " AND cp.brand_id = {$brand_id}"; 
            $sm->assign("brand_id", $brand_id);
        }
        if ($not_finalised){
            $sql_finalised = " AND c.finalised = 0"; 
        }else{
            $sql_finalised = " AND c.finalised = 1"; 
        }
        $sm->assign("not_finalised", $not_finalised);
        
        $order_statuses_selected = $params->get("order_statuses_selected");
        if ( is_array($order_statuses_selected) && sizeof($order_statuses_selected) > 0 ){
            $sql_order_status = "";
            foreach($order_statuses_selected as $k => $v){
                if ( $k == 0 ){
                    $sql_order_status .= " c.status = '".$v."' ";
                }else{
                    $sql_order_status .= " OR c.status = '".$v."' ";
                }
            }

            $sql_order_status = " AND (". $sql_order_status .") ";
        }
    }
    
    if ($date_from){
        $date_tmp = explode('/', $date_from);
        $date_from_time = mktime(0,0,0,$date_tmp[1],$date_tmp[0],$date_tmp[2]);
        $sql_where .= " AND DATE(from_unixtime(c.postdate)) >= '". date("Y-m-d", $date_from_time)."'";
    }else{
        $date_from = date("d/m/Y");
        $sql_where .= " AND DATE(from_unixtime(c.postdate)) >= '". date("Y-m-d")."'";
    }
    $sm->assign("date_from", $date_from);
    if ($date_to){
        $date_tmp = explode('/', $date_to);
        $date_to_time = mktime(0,0,0,$date_tmp[1],$date_tmp[0],$date_tmp[2]);
        $sql_where .= " AND DATE(from_unixtime(c.postdate)) <= '".date("Y-m-d", $date_to_time)."'"; 
    }else{
        $date_to = date("d/m/Y");
        $sql_where .= " AND DATE(from_unixtime(c.postdate)) <= '".date("Y-m-d")."'";
    }

	$search_string = $_REQUEST["search_string"];
	
	$sm->assign("qqSStr", $_SERVER["QUERY_STRING"]);

    $sql = "SELECT
                cp.product_id,
                cp.option_id,
                p.barcode,
                p.name_en,
                p.name_bg,
                o.option_text,
                Sum(cp.quantity) AS product_quantity,
                p.price,
                p.price_supply,
                p.price_profit,
                v.code AS variant_code,
                v.price AS variant_price,
                v.price_supply AS variant_price_supply,
                v.price_profit AS variant_price_profit,
                Sum(cp.product_price_total) AS price_total,
                (SELECT name_{$lng} FROM {$brands_table} WHERE id = p.brand_id) AS brand_name,
                (SELECT name_{$lng} FROM {$collections_table} WHERE id = p.collection_id) AS coll_name
            FROM
                {$carts_products_table} AS cp
            INNER JOIN {$carts_table} AS c ON c.id = cp.cart_id
            INNER JOIN {$products_table} AS p ON p.id = cp.product_id
            LEFT JOIN {$options_table} AS o ON o.id = cp.option_id
            LEFT JOIN {$variants_table} AS v ON v.product_id = cp.product_id AND v.option_id = cp.option_id
            WHERE
                c.edate = 0
            AND cp.edate = 0
            {$sql_finalised}
            {$sql_barcode}
            {$sql_where}
            {$sql_product_id}
            {$sql_brands}
            {$sql_order_status}
            GROUP BY
                cp.product_id,
                cp.option_id";

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
    if(strpos($string, ',') !== false || strpos($string, '"') !== false || strpos($string, "\n") !== false) {
        $string = '"' . str_replace('"', '""', $string) . '"';
    }
    return $string;
	}
	
	echo "Баркод,Марка,Колекция,Име англ.,Вариант,Цена,Доставна цена,Печалба,Брой,Сума\n";
	foreach($items as $k => $v){

		echo maybeEncodeCSVField(($v["variant_code"] ? $v["variant_code"] : $v["barcode"])). ",".maybeEncodeCSVField($v["brand_name"])."," . maybeEncodeCSVField(htmlspecialchars_decode($v["coll_name"])) . "," . maybeEncodeCSVField($v["name_en"])."," . maybeEncodeCSVField($v["option_text"])."," .maybeEncodeCSVField(strip_tags(($v["variant_price"]?$v["variant_price"]:$v["price"]))	)."," .maybeEncodeCSVField(strip_tags(($v["variant_price_supply"]?$v["variant_price_supply"]:$v["price_supply"])))."," . maybeEncodeCSVField(strip_tags(($v["variant_price_profit"]?$v["variant_price_profit"]:$v["price_profit"])))."," .maybeEncodeCSVField($v["product_quantity"])."," .maybeEncodeCSVField($v["price_total"])."\n";
	}
?>

