<?php
	class Menus extends Settings{
		
		
		function getRecord($id = 0){
			global $db;
			global $static_info_table;
			
			$id = (int)$id;
			
			$row = $db->getRow("SELECT * FROM ".$static_info_table." WHERE id = '".$id."'"); safeCheck($row);
			
			return $row;
		}
		
		function updateRow($test = ""){
			global $db;
			global $static_info_table;
			
			$row = $db->getRow("SELECT * FROM ".$static_info_table.""); safeCheck($row);
			
			return $row;
		}
		
		function addEditRow($params){
			global $db;
			global $static_info_table;
			
			$act = $params["act"];
			$id = (int)$params["id"];
			$fields = array(
				'name_bg'	=> htmlspecialchars(trim($params['name_bg'])),
				'name_en'	=> htmlspecialchars(trim($params['name_en'])),
				'name_de'	=> htmlspecialchars(trim($params['name_de'])),
				'name_ru'	=> htmlspecialchars(trim($params['name_ru'])),
				'h1_bg'	=> htmlspecialchars(trim($params['h1_bg'])),
				'h1_en'	=> htmlspecialchars(trim($params['h1_en'])),
				'h1_de'	=> htmlspecialchars(trim($params['h1_de'])),
				'h1_ru'	=> htmlspecialchars(trim($params['h1_ru'])),
				'description_bg'	=> trim($params['description_bg']),
				'description_en'	=> trim($params['description_en']),
				'description_de'	=> trim($params['description_de']),
				'description_ru'	=> trim($params['description_ru']),
				'excerpt_bg'	=> trim($params['excerpt_bg']),
				'excerpt_en'	=> trim($params['excerpt_en']),
				'excerpt_de'	=> trim($params['excerpt_de']),
				'excerpt_ru'	=> trim($params['excerpt_ru']),
				'id_menu'			=> (int)$params['id_menu'],
				'meta_title_bg'			=> $params['meta_title_bg'],
				'meta_title_en'			=> $params['meta_title_en'],
				'meta_title_de'			=> $params['meta_title_de'],
				'meta_title_ru'			=> $params['meta_title_ru'],
				'meta_description_bg'			=> $params['meta_description_bg'],
				'meta_description_en'			=> $params['meta_description_en'],
				'meta_description_de'			=> $params['meta_description_de'],
				'meta_description_ru'			=> $params['meta_description_ru'],
				'meta_keywords_bg'			=> $params['meta_keywords_bg'],
				'meta_keywords_en'			=> $params['meta_keywords_en'],
				'meta_keywords_de'			=> $params['meta_keywords_de'],
				'meta_keywords_ru'			=> $params['meta_keywords_ru'],
				'meta_metatags_bg'			=> $params['meta_metatags_bg'],
				'meta_metatags_en'			=> $params['meta_metatags_en'],
				'meta_metatags_de'			=> $params['meta_metatags_de'],
				'meta_metatags_ru'			=> $params['meta_metatags_ru'],
				'file_name_en'			=> $params['file_name_en'],
				'active'			=> $params['active'],
				'restricted'			=> $params['restricted'],
				'contactsform'			=> $params['contactsform'],
				'url_bg'				=> $params['url_bg'],
				'url_en'				=> $params['url_en'],
				'url_it'				=> $params['url_it'],
				'url_de'				=> $params['url_de'],
				'htaccess_url_bg'				=> $params['htaccess_url_bg'],
				'htaccess_url_en'				=> $params['htaccess_url_en'],
				'htaccess_url_it'				=> $params['htaccess_url_it'],
				'htaccess_url_de'				=> $params['htaccess_url_de'],
				'url_target'		=> $params['url_target'],
				'menu_pos'			=> $params['menu_pos'],
				'check1'            => (isset($params['check1']) && $params['check1'] == 1) ? 1 : 0,
				'check2'            => (isset($params['check2']) && $params['check2'] == 1) ? 1 : 0,
				'dont_open'         => (isset($params['dont_open']) && $params['dont_open'] == 1) ? 1 : 0,
                'is_faq'            => (isset($params['is_faq']) && $params['is_faq'] == 1) ? 1 : 0,
				'cms_user_id'		=> $_SESSION["uid"],
				"publishdate"		=> time()
			);
			
			$pic_left = copyImage($_FILES['pic_left'], "../files/", "../files/tn/", "../files/tntn/", "250x");
			if(!empty($pic_left)) $fields['pic_left'] = $pic_left;
			
			$pic_1 = copyImage($_FILES['pic_1'], "../files/", "../files/tn/", "../files/tntn/", "250x");
			if(!empty($pic_1)) $fields['pic_1'] = $pic_1;
				
			$pic_2 = copyImage($_FILES['pic_2'], "../files/", "../files/tn/", "../files/tntn/", "250x");
			if(!empty($pic_2)) $fields['pic_2'] = $pic_2;

			$doc = copyFile($_FILES['doc'], "../files/");
			if(!empty($doc)) $fields['doc'] = $doc;
			
		
			
			if($act == "add") {
				shiftPos($db, $static_info_table);
				$res = $db->autoExecute($static_info_table,$fields,DB_AUTOQUERY_INSERT); safeCheck($res);
				$id = mysqli_insert_id($db->connection);
				
			}
			
			if($act == "edit") {
				$id = (int)$params["id"];
				$res = $db->autoExecute($static_info_table,$fields,DB_AUTOQUERY_UPDATE,"id = " . $id);safeCheck($res);
			}
			
			$htaccessUpdate = new Settings();
			$htaccess_type = "menu";
			
			if ( $params["htaccess_url_bg"] ){
				$htaccessUpdate->updateHtaccess("bg", $params["htaccess_url_bg"], $htaccess_type, $id);
			}
			if ( $params["htaccess_url_en"] ){
				$htaccessUpdate->updateHtaccess("en", $params["htaccess_url_en"], $htaccess_type, $id);
			}
			if ( $params["htaccess_url_de"] ){
				$htaccessUpdate->updateHtaccess("de", $params["htaccess_url_de"], $htaccess_type, $id);
			}
			if ( $params["htaccess_url_ru"] ){
				$htaccessUpdate->updateHtaccess("ru", $params["htaccess_url_ru"], $htaccess_type, $id);
			}
			
		}
		
		function postImage($file = "", $info_id = 0){
			global $db;
			global $static_images_table;
			
			$fields = array(
								"file" => $file,
								"info_id" => $info_id,
							);
			shiftPos($db, $static_images_table);
			$res = $db->autoExecute($static_images_table, $fields, DB_AUTOQUERY_INSERT); safeCheck($res);
			
			return $res;
		}
		
		function deleteImage($id){
			global $db;
			global $static_images_table;
			
			$fields = array(
								"edate" => time()
							);
			$res = $db->autoExecute($static_images_table, $fields, DB_AUTOQUERY_UPDATE, " id = '".$id."' "); safeCheck($res);
			
			return $res;
		}
		
		
		function getImages($info_id){
			global $db;
			global $lng;
			global $static_images_table;
			
			$images = $db->getAll("SELECT * FROM ".$static_images_table." WHERE edate = 0 AND info_id = '".$info_id."' ORDER BY pos"); safeCheck($images);
			return $images;
		}
        
		public static function getFiles($info_id){
			global $db;
			global $lng;
			global $static_files_table;
			
			$files =  $db->getAll("SELECT file, name_{$lng} AS name FROM " . $static_files_table . " WHERE info_id = ".$info_id." AND edate = 0 ORDER BY pos"); safeCheck($files);
            
			return $files;
		}
		
		function getMenuPosition($menu_pos, $without_name = 0){
			global $db;
			global $lng;
			global $static_info_table;
			
			if ( $without_name ){
				$sql_where .= "";
			}else{
				$sql_where .= "AND name_{$lng} <> ''";
				
			}
			
			$menus = $db->getAll("SELECT *, 
										 name_{$lng} AS name,
										 url_{$lng} AS url,
										 excerpt_{$lng} AS excerpt,
										 link_text_{$lng} AS link_text,
										 url_target AS target,
										 htaccess_url_{$lng} AS htaccess_url
								  FROM ".$static_info_table." 
								  WHERE edate = 0 
								  AND menu_pos = '".$menu_pos."' 
								  AND active = 1
								  {$sql_where}
								  AND id_menu = 0 
								  ORDER BY pos"); safeCheck($menus);
			foreach($menus as $k => $v){
				$v["submenus"] = $this->getSubmenus($v["id"], 1);
				
				$menus[$k] = $v;
			}
			return $menus;
		}
		
		function getSubmenus($id, $level = 0){
			global $db;
			global $lng;
			global $static_info_table;
			
			$submenus = $db->getAll("SELECT *, 
											name_{$lng} AS name,
											excerpt_{$lng} AS excerpt,
											url_{$lng} AS url,
											url_target AS target,
											htaccess_url_{$lng} AS htaccess_url
									 FROM ".$static_info_table." 
									 WHERE edate = 0 
									 AND id_menu = '".$id."' 
									 AND name_{$lng} <> ''
									 ORDER BY pos"); safeCheck($submenus);
			
			foreach($submenus as $k => $v){
				$v["submenus"] = $this->getSubmenus($v["id"], $level+1);
				$v["level"] = $level;
				$submenus[$k] = $v;
			}
			
			return $submenus;
		}
		
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
			
			$sm->configLoad($language_file);
			$configVars = $sm->getConfigVars();
			$sm->configLoad($htaccess_file);
			$htaccessVars = $sm->getConfigVars();
			
			$row = $db->getRow("SELECT id, id_menu, name_{$lng} AS name, url_{$lng} AS url, url_target AS target, htaccess_url_{$lng} AS htaccess_url FROM ".$static_info_table." WHERE id = '".$id."'"); safeCheck($row);
			$row['link_title'] = str_replace($link_find, $link_repl, $row['name']);
			
			$htaccess_prefix = $htaccessVars["htaccess_info"];
			if ( $row["url"] ){
				$tmp_breadcrumbs = '<a href="'.$row["url"].'" target="'.$row["target"].'">'.$row["name"].'</a> <span>|</span>'.$tmp_breadcrumbs;
			}elseif( $row["htaccess_url"] ){
				$tmp_breadcrumbs = '<a href="'.$row["htaccess_url"].'" target="'.$row["target"].'">'.$row["name"].'</a> <span>|</span>'.$tmp_breadcrumbs;
			}else{
				$tmp_breadcrumbs = '<a href="/'.$htaccess_prefix.'/'.$row["id"].'" target="'.$row["target"].'">'.$row["name"].'</a> <span>|</span>'.$tmp_breadcrumbs;
			}
			if ($row["id_menu"] != 0){
				return self::generateBreadcrumbs($row["id_menu"], $tmp_breadcrumbs);
			}else{
				return '<a href="'.$host.'">'.$configVars["home_breadcrumbs"].'</a> <span>|</span>'.$tmp_breadcrumbs;
			}
		}
		
		function getPage($id){
			global $static_info_table;
			global $static_images_table;
			global $static_files_table;
			global $db;
			global $sm;
			global $lng;
			global $host;
			global $language_file;
			global $htaccess_file;
			global $htaccess_file_bg;
			global $htaccess_file_en;
			global $htaccess_file_de;
			global $htaccess_file_ru;
			global $htaccess_file_ro;
			
			$row = $db->getRow("SELECT *,
										  name_{$lng} AS name,
										  description_{$lng} AS description,
										  pic_1_name_{$lng} AS pic_1_name,
										  pic_2_name_{$lng} AS pic_2_name,
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
            
			$breadcrumbs = self::generateBreadcrumbs($id);
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
											htaccess_url_{$lng} AS htaccess_url,
											url_{$lng} AS url,
											url_target AS target
									FROM ". $static_info_table ."
									WHERE id_menu = '".$row["id"]."'
									AND active = 1
									AND name_{$lng} <> ''
									AND edate = 0
									ORDER BY pos
									"); safeCheck($subMenus);
													
			foreach($subMenus as $k=>$v){
				$v['link_title'] = str_replace($link_find, $link_repl, $v['name']);
				$subMenus[$k] = $v;
			}
			$sm->assign("subMenus", $subMenus);
			
			$images =  $db->getAll("SELECT file, name_{$lng} AS name FROM " . $static_images_table . " as st1 WHERE info_id = ".$id." AND edate = 0 ORDER BY pos"); safeCheck($images);
			$sm->assign("images", $images);
			
			$files =  $db->getAll("SELECT file, name_{$lng} AS name FROM " . $static_files_table . " as st1 WHERE info_id = ".$id." AND edate = 0 ORDER BY pos"); safeCheck($files);
			$sm->assign("files", $files);
			
			
			$sm->configLoad($htaccess_file_en);
			$htaccess_en = $sm->getConfigVars();
			if ( $row["htaccess_url_en"] ){
				$link_en = $row["htaccess_url_en"];
			}else{
				if ( $row["url_en"] ){
					$link_en = $row["url_en"];
				}else{
					$link_en = "/".$htaccess_en["htaccess_info"]."/".$row["id"];
				}
			}
			
			$sm->configLoad($htaccess_file_ro);
			$htaccess_ro = $sm->getConfigVars();
			if ( $row["htaccess_url_ro"] ){
				$link_ro = $row["htaccess_url_ro"];
			}else{
				if ( $row["url_ro"] ){
					$link_ro = $row["url_ro"];
				}else{
					$link_ro = "/".$htaccess_ro["htaccess_info"]."/".$row["id"];
				}
			}/**/
			
			$sm->configLoad($htaccess_file_bg);
			$htaccess_bg = $sm->getConfigVars();
			if ( $row["htaccess_url_bg"] ){
				$link_bg = $row["htaccess_url_bg"];
			}else{
				if ( $row["url_bg"] ){
					$link_bg = $row["url_bg"];
				}else{
					$link_bg = "/".$htaccess_bg["htaccess_info"]."/".$row["id"];
				}
			}
			
			
			$sm->configLoad($htaccess_file_de);
			$htaccess_de = $sm->getConfigVars();
			if ( $row["htaccess_url_de"] ){
				$link_de = "/".$row["htaccess_url_de"];
			}else{
				if ( $row["url_de"] ){
					$link_de = $row["url_de"];
				}else{
					$link_de = "/".$htaccess_de["htaccess_info"]."/".$row["id"];
				}
			}
			
			$sm->configLoad($htaccess_file_ru);
			$htaccess_ru = $sm->getConfigVars();
			if ( $row["htaccess_url_ru"] ){
				$link_ru = "/".$row["htaccess_url_ru"];
			}else{
				if ( $row["url_ru"] ){
					$link_ru = $row["url_ru"];
				}else{
					$link_ru = "/".$htaccess_ru["htaccess_info"]."/".$row["id"];
				}
			}
            
            $sm->configLoad($language_file);
            $sm->configLoad($htaccess_file);

			$sm->assign("link_bg", $link_bg);
			$sm->assign("link_en", $link_en);
			$sm->assign("link_de", $link_de);
			$sm->assign("link_ru", $link_ru);
			$sm->assign("link_ro", $link_ro);
			
			
			$sm->assign("s", $_REQUEST["s"]);
			$sm->assign("breadcrumbs", $breadcrumbs);
            if($row["is_faq"] == 1){
                $sm->display("faq.html");
            }else{
                $sm->display("info.html");
            }
		}
		
		function getPageGifts($id){
			global $static_info_table;
			global $static_images_table;
			global $static_files_table;
			global $db;
			global $sm;
			global $lng;
			global $host;
			global $language_file;
			global $htaccess_file;
			global $htaccess_file_bg;
			global $htaccess_file_en;
			global $htaccess_file_de;
			global $htaccess_file_ru;
			global $htaccess_file_ro;
			
			$partners = $this->getMenuPosition('4_partners', 1);
			$sm->assign("partners", $partners);
			
			$partners_right = $this->getMenuPosition('4_partners_right', 1);
			$sm->assign("partners_right", $partners_right);
			
			$sm->assign("page_gift_partners", 1);
			
			$sm->display("gifts.html");
		}
		
	}
	
?>