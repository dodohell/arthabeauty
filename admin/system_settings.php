<?php
	include("globals.php");
	
	$settingsObj->checkLogin();
	
	$page_heading = $configVars["system_settings"];
	
	$php_self = "system_settings.php";

	$sm->assign("php_self", $php_self);
	$sm->assign("php_edit", $php_edit);
	
	$sm->assign("id", $id);
	$sm->assign("act", $act);
	$sm->assign("page_heading", $page_heading);
	
	
	
	if(isset($_POST['Submit'])){
		$fields = array(
					"view_customers_contact_global" => $_POST['view_customers_contact_global'],
					"hide_customers_contact_global" => $_POST['hide_customers_contact_global'],
					"cms_user" => $_SESSION['userAdmin']['id'],
					'last_update' => time()
				);
		$res = $db->autoExecute($system_settings_table, $fields, DB_AUTOQUERY_UPDATE, " id = 1"); safeCheck($res);
	}
	
	$row = $db->getRow("SELECT * FROM ".$system_settings_table." WHERE id = '1'"); 
	safeCheck($row);
	$sm->assign("row", $row);
	
	$sm->display("admin/system_settings.html");
