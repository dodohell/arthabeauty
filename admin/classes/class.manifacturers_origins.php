<?php
	class ManifacturersOrigins extends Settings{
		
		public $pagination = "";
		
		function getRecord($id = 0){
			global $db;
			global $manifacturers_origins_table;
			
			$id = (int)$id;
			
			$row = $db->getRow("SELECT * FROM ".$manifacturers_origins_table." WHERE id = '".$id."'"); safeCheck($row);
			
			return $row;
		}
		
		function updateRow($test = ""){
			global $db;
			global $manifacturers_origins_table;
			
			$row = $db->getRow("SELECT * FROM ".$manifacturers_origins_table.""); safeCheck($row);
			
			return $row;
		}
		
		function addEditRow($params){
			global $db;
			global $manifacturers_origins_table;
			
			$act = $params["act"];
			$id = (int)$params["id"];
			$fields = array(
				'name_bg'	=> htmlspecialchars(trim($params['name_bg'])),
				'name_en'	=> htmlspecialchars(trim($params['name_en'])),
				'name_de'	=> htmlspecialchars(trim($params['name_de'])),
				'name_ru'	=> htmlspecialchars(trim($params['name_ru'])),
				'name_ro'	=> htmlspecialchars(trim($params['name_ro'])),
				'meta_title_bg'			=> $params['meta_title_bg'],
				'meta_title_en'			=> $params['meta_title_en'],
				'meta_title_de'			=> $params['meta_title_de'],
				'meta_title_ru'			=> $params['meta_title_ru'],
				'meta_title_ro'			=> $params['meta_title_ro'],
				'meta_description_bg'			=> $params['meta_description_bg'],
				'meta_description_en'			=> $params['meta_description_en'],
				'meta_description_de'			=> $params['meta_description_de'],
				'meta_description_ru'			=> $params['meta_description_ru'],
				'meta_description_ro'			=> $params['meta_description_ro'],
				'meta_keywords_bg'			=> $params['meta_keywords_bg'],
				'meta_keywords_en'			=> $params['meta_keywords_en'],
				'meta_keywords_de'			=> $params['meta_keywords_de'],
				'meta_keywords_ru'			=> $params['meta_keywords_ru'],
				'meta_keywords_ro'			=> $params['meta_keywords_ro'],
				'meta_metatags_bg'			=> $params['meta_metatags_bg'],
				'meta_metatags_en'			=> $params['meta_metatags_en'],
				'meta_metatags_de'			=> $params['meta_metatags_de'],
				'meta_metatags_ru'			=> $params['meta_metatags_ru'],
				'meta_metatags_ro'			=> $params['meta_metatags_ro'],
				'active'			=> $params['active'],
				'cms_user_id'		=> $_SESSION["uid"],
			);
			
			if($act == "add") {
				shiftPos($db, $manifacturers_origins_table);
				$res = $db->autoExecute($manifacturers_origins_table,$fields,DB_AUTOQUERY_INSERT); safeCheck($res);
				$id = mysqli_insert_id($db->connection);
			}
			
			if($act == "edit") {
				$id = (int)$params["id"];
				$res = $db->autoExecute($manifacturers_origins_table,$fields,DB_AUTOQUERY_UPDATE,"id = " . $id);safeCheck($res);
			}
			
			$htaccessUpdate = new Settings();
			$htaccess_type = "manifacturers_origins";
			
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
		
		function postImage($file = "", $city_id = 0){
			global $db;
			global $manifacturers_origins_images_table;
			
			$fields = array(
								"file" => $file,
								"city_id" => $city_id,
							);
			shiftPos($db, $manifacturers_origins_images_table);
			$res = $db->autoExecute($manifacturers_origins_images_table, $fields, DB_AUTOQUERY_INSERT); safeCheck($res);
			
			return $res;
		}
		
		function deleteImage($id){
			global $db;
			global $manifacturers_origins_images_table;
			
			$fields = array(
								"edate" => time()
							);
			$res = $db->autoExecute($manifacturers_origins_images_table, $fields, DB_AUTOQUERY_UPDATE, " id = '".$id."' "); safeCheck($res);
			
			return $res;
		}
		
		function postFile($file = "", $city_id = 0){
			global $db;
			global $manifacturers_origins_files_table;
			
			$fields = array(
								"file" => $file,
								"city_id" => $city_id,
							);
			shiftPos($db, $manifacturers_origins_files_table);
			$res = $db->autoExecute($manifacturers_origins_files_table, $fields, DB_AUTOQUERY_INSERT); safeCheck($res);
			
			return $res;
		}
		
		function deleteFile($id){
			global $db;
			global $manifacturers_origins_files_table;
			
			$fields = array(
								"edate" => time()
							);
			$res = $db->autoExecute($manifacturers_origins_files_table, $fields, DB_AUTOQUERY_UPDATE, " id = '".$id."' "); safeCheck($res);
			
			return $res;
		}
		
		function deleteField($id, $field){
			global $db;
			global $lng;
			global $manifacturers_origins_table;
			
			$res = $db->autoExecute($manifacturers_origins_table, array( $field => "" ), DB_AUTOQUERY_UPDATE, " id = '".$id."' "); safeCheck($res);
			
			return $res;
		}
		
		function getManifacturersOrigins($options = array()){
			global $db;
			global $lng;
			global $manifacturers_origins_table;
			global $customers_to_manifacturers_origins_table;
			
			$manifacturers_origins = $db->getAll("SELECT *, name_{$lng} AS name FROM ".$manifacturers_origins_table." WHERE edate = 0 ORDER BY pos"); safeCheck($manifacturers_origins);
			if ( $options["selected"] && $options["customer_id"] ){
				$manifacturers_originsSelected = $db->getAll("SELECT * FROM ".$customers_to_manifacturers_origins_table." WHERE customer_id = '".$options["customer_id"]."'"); safeCheck($manifacturers_originsSelected);
				foreach($manifacturers_origins as $k => $v){
					foreach($manifacturers_originsSelected as $kk => $vv){
						if ( $vv["manifacturer_origin_id"] == $v["id"] ){
							$v["selected"] = "checked";
						}
					}
					$manifacturers_origins[$k] = $v;
				}
			}
			return $manifacturers_origins;
		}
		
		function deleteRecord($id){
			global $db;
			global $manifacturers_origins_table;
			
			$fields = array(
								"edate" => time()
							);
			$res = $db->autoExecute($manifacturers_origins_table, $fields, DB_AUTOQUERY_UPDATE, " id = '".$id."' "); safeCheck($res);
			
			return $res;
		}
		
	}
	
?>