<?php
	include("globals.php");
	
	$id = (int)$_REQUEST["id"];
	$page = (int)$_REQUEST["page"];
	$act = htmlspecialchars(trim($_REQUEST['act']), ENT_QUOTES);
	$menu_pos = htmlspecialchars(trim($_REQUEST['menu_pos']), ENT_QUOTES);
	$page_heading = $configVars["carts"];
	
	$php_self = "carts.php";
	$php_edit = "carts_ae.php";
	$sm->assign("php_self", $php_self);
	$sm->assign("php_edit", $php_edit);
	
	$sm->assign("id", $id);
	$sm->assign("act", $act);
	$sm->assign("menu_pos", $menu_pos);
	$sm->assign("page_heading", $page_heading);
	
	$limit = 20;
	
    $carts = new Carts();
    $orderStatusesObj = new OrderStatuses();
    
    if ( $params->has("mDelete") ){
		$ids = $params->get("ids");
		foreach($ids as $key => $value) {
			$carts->deleteRecord($value);
		}
	}
    
	if ( $params->has("sendStatusEmail") ){
		$cart_ids = $params->get("ids");
		foreach( $cart_ids as $k => $v ){
			$row = $carts->getRecordSimple($v);
			$carts->sendOrderTemplate($row["status"], $row["id"]);
		}
	}
    
	if ( $params->has("changeStatuses") ){
		$order_status_ids = $params->get("order_status_ids");
		$cart_ids = $params->get("cart_ids");
		
		foreach($order_status_ids as $k => $v){
			if ( (int)$v ){
				$status = (int)$v;
				$cartId = $cart_ids[$k];
                
                $orderStatusesObj->changeStatus($status, $cartId);
			}
		}
	}
    
    $order_statuses_selected = $params->get("order_statuses_selected");
    
    $order_statuses = $orderStatusesObj->getOrderStatusesActive();
    foreach ($order_statuses as $k => $v) {
        $v["selected"] = "";
        if($order_statuses_selected && in_array($v["id"], $order_statuses_selected)){
            $v["selected"] = "checked";
        }
        $order_statuses[$k] = $v;
    }
    $sm->assign("order_statuses", $order_statuses);
    
	if ( $act == "delete" ){
        $suid = $_SESSION["uid"];
        if($suid == 1 || $suid == 2){
            $carts->deleteRecord($id);
            header("Location: ".$php_self);
            die();
        }else{
            die("Функцията за изтриване на поръчка е забранена. При необходимост моля свържете се с администраторите на системата.");
        }
	}
	
	$brands = Brands::getBrandsAll();
    $sm->assign("brands", $brands);
	
	$search_string = $_REQUEST["search_string"];
	$items = $carts->getCarts($page, $limit, $params);
	$pagination = $carts->getCartsPagination();
	
	$sm->assign("items", $items);
	$sm->assign("pagination", $pagination);
	
    $sm->assign("time_now", time());
    
	$sm->display("admin/carts.html");
?>