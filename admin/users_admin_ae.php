<?php
	include("globals.php");
	
//	$settingsObj->checkLogin();
	
	$id = $params->getInt("id");
	$act = $params->getString("act");
	$page_heading = $configVars["users_admin"];
	
	$php_self = "users_admin.php";
	$php_edit = "users_admin_ae.php";
	$sm->assign("php_self", $php_self);
	$sm->assign("php_edit", $php_edit);
	
	$sm->assign("id", $id);
	$sm->assign("act", $act);
	$sm->assign("page_heading", $page_heading);
    
	$users_admin = new UsersAdmin();
	$settings = new Settings();
	
	if($params->has("Submit")){
		$users_admin->addEditRow($params);
		header("Location: ".$php_self);
		die();
	}

	if ($id){
		$row = $users_admin->getRecord($id);
        $sm->assign("row", $row);
        
        $userLevels = $db->getAll("SELECT * FROM " . $users_admin_to_menus_table . " WHERE user_id = " . $id  . " ORDER BY menu_id ASC ");
        foreach( $userLevels as $v ){
            $accessLevels[$v['menu_id']] = (int) $v['permission_level'];
        }
        $sm->assign("accessLevels", $accessLevels);
        
        foreach($fullmenu as $k=>$v){
            $fullmenu[$k]['access'] = $accessLevels[$v['menu_id']];
            if($v["submenu"]){
                foreach($v["submenu"] as $kk=>$vv){
                    $fullmenu[$k]["submenu"][$kk]['access'] = $accessLevels[$vv['menu_id']];
                }
            }
        }
        $sm->assign("fullmenu", $fullmenu);
	}
	
	$sm->display("admin/users_admin_ae.html");
?>