<?php
	include("globals.php");
	
//	$settingsObj->checkLogin();
	
	$id = $params->getInt("id");
	$act = $params->getString("act");
	$menu_pos = $params->getString("menu_pos");
	$page_heading = $configVars["category_types"];
	
	$php_self = "category_types.php";
	$php_edit = "category_types_ae.php";
	$sm->assign("php_self", $php_self);
	$sm->assign("php_edit", $php_edit);
	
	$sm->assign("id", $id);
	$sm->assign("act", $act);
	$sm->assign("menu_pos", $menu_pos);
	$sm->assign("page_heading", $page_heading);
    
	$category_types = new CategoryTypes();
	$settings = new Settings();
	
	if( $params->has("Submit") ){
		$category_types->addEditRow($params);
		header("Location: category_types.php");
		die();
	}
    
	if ( $id ){
		$row = $category_types->getRecord($id);
		$sm->assign("row", $row);
	}
	if ( $act == "delete" ){
		$field = $params->getString("field");
		$category_types->deleteField($id, $field);
		header("Location: ".$php_edit."?id=".$id."&act=edit");
		die();
	}
	
	$editor = $settings->generateEditor($languages,"description", $row);
	$sm->assign("editor", $editor);
	
    $categories = $db->getAll("SELECT id, name_{$lng} AS name FROM ".$categories_table." WHERE edate = 0 AND category_id = 0 ORDER BY pos"); safeCheck($categories);
	$categoriesSelected = $db->getAll("SELECT * FROM ".$category_to_category_type_table." WHERE category_type_id = '".$id."'"); safeCheck($categoriesSelected);
	foreach($categories as $k => $v){
		$subcategories = $db->getAll("SELECT id, name_{$lng} AS name FROM ".$categories_table." WHERE edate = 0 AND category_id = '".$v["id"]."' ORDER BY pos"); safeCheck($subcategories);
		foreach( $subcategories as $kk => $vv ){
			foreach($categoriesSelected as $kkk => $vvv){
				if ( $vv["id"] == $vvv["category_id"] ){
					$vv["selected"] = "checked";
				}
			}
			$subcategories[$kk] = $vv;
		}
		$v["subcategories"] = $subcategories;
		foreach($categoriesSelected as $kk => $vv){
			if ( $v["id"] == $vv["category_id"] ){
				$v["selected"] = "checked";
			}
		}
		$categories[$k] = $v;
	}
	$sm->assign("categories", $categories);
    
//	$editorExcerpt = $settings->generateEditor($languages,"excerpt", $row);
//	$sm->assign("editorExcerpt", $editorExcerpt);
	
	$sm->display("admin/category_types_ae.html");
?>