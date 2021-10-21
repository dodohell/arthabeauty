<?php
	include("globals.php");

	$id = (int)$_REQUEST["id"];
	$page = (int)$_REQUEST["page"];
	$act = htmlspecialchars(trim($_REQUEST['act']), ENT_QUOTES);
	$page_heading = $configVars["products_sales_report"];

	$php_self = "products_sales_report.php";

	$sm->assign("php_self", $php_self);

	$sm->assign("id", $id);
	$sm->assign("act", $act);
	$sm->assign("page_heading", $page_heading);

	$limit = 20;
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
    $sm->assign("date_to", $date_to);

    $orderStatusesObj = new OrderStatuses();

    $order_statuses = $orderStatusesObj->getOrderStatusesActive();
    foreach ($order_statuses as $k => $v) {
        $v["selected"] = "";
        if($order_statuses_selected && in_array($v["id"], $order_statuses_selected)){
            $v["selected"] = "checked";
        }
        $order_statuses[$k] = $v;
    }
    $sm->assign("order_statuses", $order_statuses);

    $brands = Brands::getBrandsAll();
    $sm->assign("brands", $brands);

	$search_string = $_REQUEST["search_string"];
    $sql = "SELECT
                cp.product_id,
                cp.option_id,
                p.barcode,
                p.name_en,
                p.name_bg,
                -- o.option_text,
                SUM(cp.quantity) AS product_quantity,
                p.price,
                p.price_supply,
                p.price_profit,
                -- v.code AS variant_code,
                -- v.price AS variant_price,
                -- v.price_supply AS variant_price_supply,
                -- v.price_profit AS variant_price_profit,
                Sum(cp.product_price_total) AS price_total,
                (SELECT name_{$lng} FROM {$brands_table} WHERE id = p.brand_id) AS brand_name,
                (SELECT name_{$lng} FROM {$collections_table} WHERE id = p.collection_id) AS coll_name
            FROM
                {$carts_products_table} AS cp
            INNER JOIN {$carts_table} AS c ON c.id = cp.cart_id
            INNER JOIN {$products_table} AS p ON p.id = cp.product_id
            -- LEFT JOIN {$options_table} AS o ON o.id = cp.option_id
            -- LEFT JOIN {$variants_table} AS v ON v.product_id = cp.product_id AND v.option_id = cp.option_id
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
//    var_dump($sql);
//    die;
    $items = $db->getAll($sql); safeCheck($items);
    // echo '<pre>';
    // var_export($items);
    // echo '</pre>';
    // die;
    $productsCount = 0;
    $productsTotalPrice = 0;
    foreach ($items as $k => $v) {
		if(isset($v["product_quantity"])) {
			$productsCount += $v["product_quantity"];
		}
		if(isset($v["price_total"])) {
			$productsTotalPrice += $v["price_total"];
		}
    }

	$sm->assign("productsCount", $productsCount);
	$sm->assign("productsTotalPrice", $productsTotalPrice);
	$sm->assign("items", $items);

  $sm->assign("time_now", time());
  $sm->assign("qqSStr", $_SERVER["QUERY_STRING"]);
	$sm->display("admin/products_sales_report.html");
?>
