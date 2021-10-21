<?php
	class CategoryTypes extends Settings{
		
		public $pagination = "";
		
		function getRecord($id = 0){
			global $db;
			global $category_types_table;
			
			$id = (int)$id;
			
			$row = $db->getRow("SELECT * FROM ".$category_types_table." WHERE id = '".$id."'"); safeCheck($row);
			
			return $row;
		}
		
		function updateRow($test = ""){
			global $db;
			global $category_types_table;
			
			$row = $db->getRow("SELECT * FROM ".$category_types_table.""); safeCheck($row);
			
			return $row;
		}
		
		function addEditRow(FilteredMap $params){
			global $db;
			global $category_types_table;
			global $category_to_category_type_table;
			
			$act = $params->getString("act");
			$id = $params->getInt("id");
			$fields = array(
				'name_bg'               => $params->getString("name_bg"),
				'name_en'               => $params->getString("name_en"),
				'name_de'               => $params->getString("name_de"),
				'name_ru'               => $params->getString("name_ru"),
				'name_ro'               => $params->getString("name_ro"),
				'description_bg'        => $params->getString("description_bg"),
				'description_en'        => $params->getString("description_en"),
				'description_de'        => $params->getString("description_de"),
				'description_ro'        => $params->getString("description_ro"),
                'url_bg'				=> $params->getString('url_bg'),
                'url_en'				=> $params->getString('url_en'),
                'url_de'				=> $params->getString('url_de'),
                'url_ru'				=> $params->getString('url_ru'),
                'url_ro'				=> $params->getString('url_ro'),
                'htaccess_url_bg'		=> $params->getString('htaccess_url_bg'),
                'htaccess_url_en'		=> $params->getString('htaccess_url_en'),
                'htaccess_url_de'		=> $params->getString('htaccess_url_de'),
                'htaccess_url_ru'		=> $params->getString('htaccess_url_ru'),
                'htaccess_url_ro'		=> $params->getString('htaccess_url_ro'),
				'meta_title_bg'			=> $params->getString("meta_title_bg"),
				'meta_title_en'			=> $params->getString("meta_title_en"),
				'meta_title_de'			=> $params->getString("meta_title_de"),
				'meta_title_ru'			=> $params->getString("meta_title_ru"),
				'meta_title_ro'			=> $params->getString("meta_title_ro"),
				'meta_description_bg'	=> $params->getString("meta_description_bg"),
				'meta_description_en'	=> $params->getString("meta_description_en"),
				'meta_description_de'	=> $params->getString("meta_description_de"),
				'meta_description_ru'	=> $params->getString("meta_description_ru"),
				'meta_description_ro'	=> $params->getString("meta_description_ro"),
				'meta_keywords_bg'		=> $params->getString("meta_keywords_bg"),
				'meta_keywords_en'		=> $params->getString("meta_keywords_en"),
				'meta_keywords_de'		=> $params->getString("meta_keywords_de"),
				'meta_keywords_ru'		=> $params->getString("meta_keywords_ru"),
				'meta_keywords_ro'		=> $params->getString("meta_keywords_ro"),
				'meta_metatags_bg'		=> $params->getString("meta_metatags_bg"),
				'meta_metatags_en'		=> $params->getString("meta_metatags_en"),
				'meta_metatags_de'		=> $params->getString("meta_metatags_de"),
				'meta_metatags_ru'		=> $params->getString("meta_metatags_ru"),
				'meta_metatags_ro'		=> $params->getString("meta_metatags_ro"),
				'active'                => $params->getInt("active"),
			);
            
            $pic_1 = copyImage($_FILES['pic_1'], "../files/", "../files/tn/", "../files/tntn/", "250x");
			if(!empty($pic_1)) $fields['pic_1'] = $pic_1;
            
			if($act == "add") {
				shiftPos($db, $category_types_table);
                $fields["postdate"] = time();
                $fields["cms_user_id"] = $_SESSION["uid"];
				$res = $db->autoExecute($category_types_table,$fields,DB_AUTOQUERY_INSERT); safeCheck($res);
				$id = mysqli_insert_id($db->connection);
			}
			
			if($act == "edit") {
				$id = $params->getInt("id");
                $fields["updated"] = time();
                $fields["cms_user_id_updated"] = $_SESSION["uid"];
				$res = $db->autoExecute($category_types_table,$fields,DB_AUTOQUERY_UPDATE,"id = " . $id);safeCheck($res);
			}
            
            $categoriesRequest = $_REQUEST["categories"];
            $res = $db->Query("DELETE FROM ".$category_to_category_type_table." WHERE category_type_id = '".$id."'"); safeCheck($res);
            if ($categoriesRequest){
                foreach($categoriesRequest as $k=>$v){
                    $res = $db->Query("INSERT INTO ".$category_to_category_type_table." (category_type_id, category_id) VALUES ('".$id."','".$v."')"); safeCheck($res);
                }
            }
			
			$htaccessUpdate = new Settings();
			$htaccess_type = "category_types";
			
			if ( $params->getString("htaccess_url_bg") ){
				$htaccessUpdate->updateHtaccess("bg",  $params->getString("htaccess_url_bg"), $htaccess_type, $id);
			}
			if (  $params->getString("htaccess_url_en") ){
				$htaccessUpdate->updateHtaccess("en", $params->getString("htaccess_url_en"), $htaccess_type, $id);
			}
			if ( $params->getString("htaccess_url_de") ){
				$htaccessUpdate->updateHtaccess("de", $params->getString("htaccess_url_de"), $htaccess_type, $id);
			}
			if ( $params->getString("htaccess_url_ru") ){
				$htaccessUpdate->updateHtaccess("ru", $params->getString("htaccess_url_ru"), $htaccess_type, $id);
			}
			if ( $params->getString("htaccess_url_ro") ){
				$htaccessUpdate->updateHtaccess("ro", $params->getString("htaccess_url_ro"), $htaccess_type, $id);
			}
			
		}
		
		function postImage($file = "", $city_id = 0){
			global $db;
			global $category_types_images_table;
			
			$fields = array(
								"file" => $file,
								"city_id" => $city_id,
							);
			shiftPos($db, $category_types_images_table);
			$res = $db->autoExecute($category_types_images_table, $fields, DB_AUTOQUERY_INSERT); safeCheck($res);
			
			return $res;
		}
		
		function deleteImage($id){
			global $db;
			global $category_types_images_table;
			
			$fields = array(
								"edate" => time()
							);
			$res = $db->autoExecute($category_types_images_table, $fields, DB_AUTOQUERY_UPDATE, " id = '".$id."' "); safeCheck($res);
			
			return $res;
		}
		
		function postFile($file = "", $city_id = 0){
			global $db;
			global $category_types_files_table;
			
			$fields = array(
								"file" => $file,
								"city_id" => $city_id,
							);
			shiftPos($db, $category_types_files_table);
			$res = $db->autoExecute($category_types_files_table, $fields, DB_AUTOQUERY_INSERT); safeCheck($res);
			
			return $res;
		}
		
		function deleteFile($id){
			global $db;
			global $category_types_files_table;
			
			$fields = array(
								"edate" => time()
							);
			$res = $db->autoExecute($category_types_files_table, $fields, DB_AUTOQUERY_UPDATE, " id = '".$id."' "); safeCheck($res);
			
			return $res;
		}
		
		function deleteField($id, $field){
			global $db;
			global $lng;
			global $category_types_table;
			
			$res = $db->autoExecute($category_types_table, array( $field => "" ), DB_AUTOQUERY_UPDATE, " id = '".$id."' "); safeCheck($res);
			
			return $res;
		}
		
		function getCategoryTypes($options = array()){
			global $db;
			global $lng;
			global $category_types_table;
			global $customers_to_category_types_table;
			
			$category_types = $db->getAll("SELECT *, name_{$lng} AS name FROM ".$category_types_table." WHERE edate = 0 ORDER BY pos"); safeCheck($category_types);
			if ( is_array($options) && $options["selected"] && $options["customer_id"] ){
				$category_typesSelected = $db->getAll("SELECT * FROM ".$customers_to_category_types_table." WHERE customer_id = '".$options["customer_id"]."'"); safeCheck($category_typesSelected);
				foreach($category_types as $k => $v){
					foreach($category_typesSelected as $kk => $vv){
						if ( $vv["category_type_id"] == $v["id"] ){
							$v["selected"] = "checked";
						}
					}
					$category_types[$k] = $v;
				}
			}
			return $category_types;
		}
		
		function deleteRecord($id){
			global $db;
			global $category_types_table;
			
			$fields = array(
								"edate" => time()
							);
			$res = $db->autoExecute($category_types_table, $fields, DB_AUTOQUERY_UPDATE, " id = '".$id."' "); safeCheck($res);
			
			return $res;
		}
		
	}
	
?>