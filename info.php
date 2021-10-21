<?
	include("globals.php");
	
	
	$id = (int)$_REQUEST["id"];
	$row = $db->getRow("SELECT id,
											  name_{$lng} AS name,
											  description_{$lng} AS description,
											  pic_1,
											  pic_2,
											  contactform,
											  id_menu,
											  restricted,
											  menu_pos, 
											  meta_keywords_{$lng} AS meta_keywords,
											  meta_title_{$lng} AS meta_title,
											  meta_description_{$lng} AS meta_description
									FROM ".$static_info_table."
									WHERE id = '{$id}'
									"); safeCheck($row);
	
	$sm->assign("infoKeys", 	$row['meta_keywords']);
	if ( $row["meta_title"] ){
		$sm->assign("infoTitle", $row['meta_title']);
	}else{
		$sm->assign("infoTitle", $row['name']);
	}
	$sm->assign("infoDescr", $row['meta_description']);
	
	
	if ($row["restricted"] == 1 && !isset($_SESSION["uid"])){
		header("Location: restricted.php");
		die();
	}
	
	$row["description"] = $row["description"];	
	
	$sm->assign("row", $row);
	
	$var = $sm->configLoad($language_file);
	$configVars = $sm->getConfigVars();
	
	function generateBreadcrumbs($id, $tmp_breadcrumbs = ""){
		global $db;
		global $sm;
		global $lng;
		global $link_find;
		global $link_repl;
		global $host;
		global $language_file;
		global $htaccess_file;
		global $static_info_table;
		global $left;
		$sm->configLoad($language_file);
		$configVars = $sm->getConfigVars();
		$sm->configLoad($htaccess_file);
		$htaccessVars = $sm->getConfigVars();
		
		$row = $db->getRow("SELECT id, id_menu, name_{$lng} AS name FROM ".$static_info_table." WHERE id = '".$id."'"); safeCheck($row);
		$row['link_title'] = str_replace($link_find, $link_repl, $row['name']);
		
		$htaccess_prefix = $htaccessVars["htaccess_info"];
		
		$tmp_breadcrumbs = '<div class="leftBreadcrumbs">&raquo;</div><div class="leftBreadcrumbs"><a href="/'.$htaccess_prefix.'/'.$row["id"].'" class="linkBreadcrumbs">'.$row["name"].'</a></div>'.$tmp_breadcrumbs;
		if ($row["id_menu"] != 0){
			return generateBreadcrumbs($row["id_menu"], $tmp_breadcrumbs);
		}else{
			return '<div class="leftBreadcrumbs"><a href="'.$host.'" class="linkBreadcrumbs">'.$configVars["home_breadcrumbs"].'</a></div>'.$tmp_breadcrumbs;
		}
	}
	
	$breadcrumbs = generateBreadcrumbs($id);
	
	$sm->assign("breadcrumbs", $breadcrumbs);
	
	
	$s = $row["id_menu"];
	if ($s){
		$id_menu = $s;
	}else{
		$id_menu = $row["id"];
	}
	if ($id_menu == 0){
		$row = $db->getRow("SELECT id,
											  name_{$lng} AS name,
											  description_{$lng} AS description,
											  pic_1,
											  id_menu
									FROM ".$static_info_table."
									WHERE id = '{$id}'
									"); safeCheck($row);
		$id_menu = $row["id"];
	}
	$subMenus = $db->getAll("SELECT id,
													name_{$lng} AS name,
													excerpt_{$lng} AS excerpt,
													description_{$lng} AS description,
													id_menu,
													pic_1,
													url_{$lng} AS url,
													url_target AS target
											FROM ". $static_info_table ."
											WHERE id_menu = '".$row["id"]."'
											AND active = 'checked'
											AND edate = 0
											ORDER BY pos
											"); safeCheck($subMenus);
											
	//print_r($subMenus);
	foreach($subMenus as $k=>$v){
		$v['link_title'] = str_replace($link_find, $link_repl, $v['name']);
		$subMenus[$k] = $v;
	}
	$sm->assign("subMenus", $subMenus);
	
	$images =  $db->getAll("SELECT pic, alt_{$lng} AS alt
													FROM " . $static_images_table . " as st1 WHERE  static_info_id = ".$id." AND edate = 0 ORDER BY pos");
	
	$sm->assign("images", $images);
	$sm->assign("projects", $projects);
	$sm->assign("s", $_REQUEST["s"]);
	$sm->assign("breadcrumbs", $breadcrumbs);
	$sm->display("./info.html");
?>
