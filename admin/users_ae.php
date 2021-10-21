<?php
	include("globals.php");
	
	//$settingsObj->checkLogin();
	
	$id = $params->getInt("id");
	$act = $params->getString("act");
	$page_heading = $configVars["users"];
	
	$php_self = "users.php";
	$php_edit = "users_ae.php";
	$sm->assign("php_self", $php_self);
	$sm->assign("php_edit", $php_edit);
	
	$sm->assign("id", $id);
	$sm->assign("act", $act);
	$sm->assign("page_heading", $page_heading);

	$users = new Users();
	$settings = new Settings();
	$orderStatusesObj = new OrderStatuses();
	
	if( isset($_REQUEST["Submit"]) ){
		$users->addEditRow();
		header("Location: users.php");
		die();
	}
	
	if ( $id ){
		$row = $users->getRecord($id);
	}
	$sm->assign("row", $row);
    
    if($useUserGroups){
        $userGroupsObj = new UserGroups();
        $userGroups = $userGroupsObj->getUserGroupsAllActive();
        $sm->assign("userGroups", $userGroups);
    }
    
	
	$cartsObj = new Carts();
	$carts = $cartsObj->getCarts($page, 1000, $params, $id);
	$total_amount = 0;
	foreach( $carts as $k => $v ){
		$total_amount += $v['total_amount'];
	}
	$sm->assign('items', $carts);
	$sm->assign('total_amount', $total_amount);
	
	$order_statuses = $orderStatusesObj->getOrderStatusesActive();
    foreach ($order_statuses as $k => $v) {
        $v["selected"] = "";
        if($order_statuses_selected && in_array($v["id"], $order_statuses_selected)){
            $v["selected"] = "checked";
        }
        $order_statuses[$k] = $v;
    }
    $sm->assign("order_statuses", $order_statuses);
	
	$sm->display("admin/users_ae.html");
?>