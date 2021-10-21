<?
	include("globals.php");
	
//	$settingsObj->checkLogin();
	
	$id = (int)$_REQUEST["id"];
	$customer_id = (int)$_REQUEST["customer_id"];
	$act = htmlspecialchars(trim($_REQUEST['act']), ENT_QUOTES);
	$menu_pos = htmlspecialchars(trim($_REQUEST['menu_pos']), ENT_QUOTES);
	$page_heading = $configVars["faqs"];
	
	$php_self = "faqs.php?customer_id=$customer_id";
	$php_edit = "faqs_ae.php";
	$sm->assign("php_self", $php_self);
	$sm->assign("php_edit", $php_edit);
	
	$sm->assign("id", $id);
	$sm->assign("customer_id", $customer_id);
	$sm->assign("act", $act);
	$sm->assign("page_heading", $page_heading);
	
	$faqs = new Faqs();
	
	if ( $act == "delete" ){
		$faqs->deleteRecord($id);
		header("Location: ".$php_self);
		die();
	}
	
	$items = $faqs->getFaqs(array("customer_id" => $customer_id));
	$sm->assign("items", $items);
	
	
	
	
	$sm->display("admin/faqs.html");
?>