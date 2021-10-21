<?php
	class Menus extends Settings{
		
		public $pagination = "";
		
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
				'icon'      => htmlspecialchars(trim($params['icon'])),
				'name_bg'	=> htmlspecialchars(trim($params['name_bg'])),
				'name_en'	=> htmlspecialchars(trim($params['name_en'])),
				'name_de'	=> htmlspecialchars(trim($params['name_de'])),
				'name_ru'	=> htmlspecialchars(trim($params['name_ru'])),
				'name_ro'	=> htmlspecialchars(trim($params['name_ro'])),
				'h1_bg'     => htmlspecialchars(trim($params['h1_bg'])),
				'h1_en'     => htmlspecialchars(trim($params['h1_en'])),
				'h1_de'     => htmlspecialchars(trim($params['h1_de'])),
				'h1_ru'     => htmlspecialchars(trim($params['h1_ru'])),
				'h1_ro'     => htmlspecialchars(trim($params['h1_ro'])),
				'link_text_bg'     => htmlspecialchars(trim($params['link_text_bg'])),
				'link_text_en'     => htmlspecialchars(trim($params['link_text_en'])),
				'link_text_de'     => htmlspecialchars(trim($params['link_text_de'])),
				'link_text_ru'     => htmlspecialchars(trim($params['link_text_ru'])),
				'link_text_ro'     => htmlspecialchars(trim($params['link_text_ro'])),
				'description_bg'	=> trim($params['description_bg']),
				'description_en'	=> trim($params['description_en']),
				'description_de'	=> trim($params['description_de']),
				'description_ru'	=> trim($params['description_ru']),
				'description_ro'	=> trim($params['description_ro']),
				'excerpt_bg'	=> trim($params['excerpt_bg']),
				'excerpt_en'	=> trim($params['excerpt_en']),
				'excerpt_de'	=> trim($params['excerpt_de']),
				'excerpt_ru'	=> trim($params['excerpt_ru']),
				'excerpt_ro'	=> trim($params['excerpt_ro']),
				'pic_1_name_bg'	=> trim($params['pic_1_name_bg']),
				'pic_1_name_en'	=> trim($params['pic_1_name_en']),
				'pic_1_name_de'	=> trim($params['pic_1_name_de']),
				'pic_1_name_ru'	=> trim($params['pic_1_name_ru']),
				'pic_1_name_ro'	=> trim($params['pic_1_name_ro']),
				'pic_2_name_bg'	=> trim($params['pic_2_name_bg']),
				'pic_2_name_en'	=> trim($params['pic_2_name_en']),
				'pic_2_name_de'	=> trim($params['pic_2_name_de']),
				'pic_2_name_ru'	=> trim($params['pic_2_name_ru']),
				'pic_2_name_ro'	=> trim($params['pic_2_name_ro']),
				'id_menu'			=> (int)$params['id_menu'],
				'meta_title_bg'		=> $params['meta_title_bg'],
				'meta_title_en'		=> $params['meta_title_en'],
				'meta_title_de'		=> $params['meta_title_de'],
				'meta_title_ru'		=> $params['meta_title_ru'],
				'meta_title_ro'		=> $params['meta_title_ro'],
				'meta_description_bg'	=> $params['meta_description_bg'],
				'meta_description_en'	=> $params['meta_description_en'],
				'meta_description_de'	=> $params['meta_description_de'],
				'meta_description_ru'	=> $params['meta_description_ru'],
				'meta_description_ro'	=> $params['meta_description_ro'],
				'meta_keywords_bg'		=> $params['meta_keywords_bg'],
				'meta_keywords_en'		=> $params['meta_keywords_en'],
				'meta_keywords_de'		=> $params['meta_keywords_de'],
				'meta_keywords_ru'		=> $params['meta_keywords_ru'],
				'meta_keywords_ro'		=> $params['meta_keywords_ro'],
				'meta_metatags_bg'		=> $params['meta_metatags_bg'],
				'meta_metatags_en'		=> $params['meta_metatags_en'],
				'meta_metatags_de'		=> $params['meta_metatags_de'],
				'meta_metatags_ru'		=> $params['meta_metatags_ru'],
				'meta_metatags_ro'		=> $params['meta_metatags_ro'],
				'file_name_en'			=> $params['file_name_en'],
				'file_name_ro'			=> $params['file_name_ro'],
				'active'                => $params['active'],
				'restricted'			=> $params['restricted'],
				'contactform'			=> $params['contactsform'],
				'url_bg'				=> $params['url_bg'],
				'url_en'				=> $params['url_en'],
				'url_de'				=> $params['url_de'],
				'url_ru'				=> $params['url_ru'],
				'url_ro'				=> $params['url_ro'],
				'htaccess_url_bg'		=> $params['htaccess_url_bg'],
				'htaccess_url_en'		=> $params['htaccess_url_en'],
				'htaccess_url_de'		=> $params['htaccess_url_de'],
				'htaccess_url_ru'		=> $params['htaccess_url_ru'],
				'htaccess_url_ro'		=> $params['htaccess_url_ro'],
				'url_target'            => $params['url_target'],
				'no_heading'			=> $params['no_heading'],
				'menu_pos'              => $params['menu_pos'],
				'check1'                => isset($params['check1']) ? 1 : 0,
				'check2'                => isset($params['check2']) ? 1 : 0,
				'dont_open'             => isset($params['dont_open']) ? 1 : 0,
				'hidden_for_logged_in'	=> isset($params['hidden_for_logged_in']) ? 1 : 0,
				'hidden_for_not_logged_in'	=> isset($params['hidden_for_not_logged_in']) ? 1 : 0,
				'html_id'               => $params['html_id'],
				'html_class'            => $params['html_class'],
				'cms_user_id'           => $_SESSION["uid"],
				"publishdate"           => time()
			);
			
			$pic_main = copyImage($_FILES['pic_main'], "../files/", "../files/tn/", "../files/tntn/", "500x");
			if(!empty($pic_main)) $fields['pic_main'] = $pic_main;
			
			$pic_1 = copyImage($_FILES['pic_1'], "../files/", "../files/tn/", "../files/tntn/", "500x");
			if(!empty($pic_1)) $fields['pic_1'] = $pic_1;
				
			$pic_2 = copyImage($_FILES['pic_2'], "../files/", "../files/tn/", "../files/tntn/", "500x");
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
			if ( $params["htaccess_url_ro"] ){
				$htaccessUpdate->updateHtaccess("ro", $params["htaccess_url_ro"], $htaccess_type, $id);
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
		
		function updateImage($params = array()){
			global $db;
			global $static_images_table;
			
			$id = (int)$params["id"];
			
			$fields = array(
								'name_bg'	=> htmlspecialchars(trim($params['name_bg'])),
								'name_en'	=> htmlspecialchars(trim($params['name_en'])),
								'name_de'	=> htmlspecialchars(trim($params['name_de'])),
								'name_ru'	=> htmlspecialchars(trim($params['name_ru'])),
								'name_ro'	=> htmlspecialchars(trim($params['name_ro'])),
							);
			$res = $db->autoExecute($static_images_table, $fields, DB_AUTOQUERY_UPDATE, " id = '".$id."' "); safeCheck($res);
			
			return $res;
		}
        
		function updateFile($params = array()){
			global $db;
			global $static_files_table;
			
			$id = (int)$params["id"];
			
			$fields = array(
								'name_bg'	=> htmlspecialchars(trim($params['name_bg'])),
								'name_en'	=> htmlspecialchars(trim($params['name_en'])),
								'name_de'	=> htmlspecialchars(trim($params['name_de'])),
								'name_ru'	=> htmlspecialchars(trim($params['name_ru'])),
								'name_ro'	=> htmlspecialchars(trim($params['name_ro'])),
							);
			$res = $db->autoExecute($static_files_table, $fields, DB_AUTOQUERY_UPDATE, " id = '".$id."' "); safeCheck($res);
			
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
		
		function postFile($file = "", $info_id = 0){
			global $db;
			global $static_files_table;
			
			$fields = array(
								"file" => $file,
								"info_id" => $info_id,
							);
			shiftPos($db, $static_files_table);
			$res = $db->autoExecute($static_files_table, $fields, DB_AUTOQUERY_INSERT); safeCheck($res);
			
			return $res;
		}
		
		function deleteFile($id){
			global $db;
			global $static_files_table;
			
			$fields = array(
								"edate" => time()
							);
			$res = $db->autoExecute($static_files_table, $fields, DB_AUTOQUERY_UPDATE, " id = '".$id."' "); safeCheck($res);
			
			return $res;
		}
		
		function deleteField($id, $field){
			global $db;
			global $lng;
			global $static_info_table;
			
			$res = $db->autoExecute($static_info_table, array( $field => "" ), DB_AUTOQUERY_UPDATE, " id = '".$id."' "); safeCheck($res);
			
			return $res;
		}
		
		function getImages($info_id){
			global $db;
			global $lng;
			global $static_images_table;
			
			$images = $db->getAll("SELECT * FROM ".$static_images_table." WHERE edate = 0 AND info_id = '".$info_id."' ORDER BY pos"); safeCheck($images);
			return $images;
		}
		
		
		function getImageForm($id){
			global $db;
			global $sm;
			global $static_images_table;
			
			$row = $db->getRow("SELECT * FROM ".$static_images_table." WHERE edate = 0 AND id = '".$id."' ORDER BY pos"); safeCheck($row);
			$sm->assign("row", $row);
			
			$sm->display("admin/menus_images.html");
		}
        
		function getFileForm($id){
			global $db;
			global $sm;
			global $static_files_table;
			
			$row = $db->getRow("SELECT * FROM ".$static_files_table." WHERE id = '".$id."' ORDER BY pos"); safeCheck($row);
			$sm->assign("row", $row);
			
			$sm->display("admin/menus_files.html");
		}
		
		
		function getFiles($info_id){
			global $db;
			global $lng;
			global $static_files_table;
			
			$files = $db->getAll("SELECT *, name_{$lng} AS name FROM ".$static_files_table." WHERE edate = 0 AND info_id = '".$info_id."' ORDER BY pos"); safeCheck($files);
			return $files;
		}
		
		
		function getMenuPosition($menu_pos){
			global $db;
			global $lng;
			global $static_info_table;
			
			$menus = $db->getAll("SELECT *, name_{$lng} AS name FROM ".$static_info_table." WHERE edate = 0 AND menu_pos = '".$menu_pos."' AND id_menu = 0 ORDER BY pos"); safeCheck($menus);
            
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
			
			$submenus = $db->getAll("SELECT *, name_{$lng} AS name FROM ".$static_info_table." WHERE edate = 0 AND id_menu = '".$id."' ORDER BY pos"); safeCheck($submenus);
			
			foreach($submenus as $k => $v){
				$v["submenus"] = $this->getSubmenus($v["id"], $level+1);
				$v["level"] = $level;
				$submenus[$k] = $v;
			}
			
			return $submenus;
		}
		
		function deleteRecord($id){
			global $db;
			global $static_info_table;
			
			$fields = array(
								"edate" => time(),
								"edate_cms_user_id" => $_SESSION["uid"]
							);
			$res = $db->autoExecute($static_info_table, $fields, DB_AUTOQUERY_UPDATE, " id = '".$id."' "); safeCheck($res);
			
			return $res;
		}
		
	}
	
?>