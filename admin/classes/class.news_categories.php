<?php
	class NewsCategories extends Settings{
		
		function getRecord($id = 0){
			global $db;
			global $lng;
			global $news_categories_table;
			
			$id = (int)$id;
			
			$row = $db->getRow("SELECT *, name_{$lng} AS name FROM ".$news_categories_table." WHERE id = '".$id."'"); safeCheck($row);
			
			return $row;
		}
		
		function updateRow($test = ""){
			global $db;
			global $news_categories_table;
			
			$row = $db->getRow("SELECT * FROM ".$news_categories_table.""); safeCheck($row);
			
			return $row;
		}
		
		function addEditRow($params){
			global $db;
			global $news_categories_table;
			
			$act = $params["act"];
			$id = (int)$params["id"];
			$fields = array(
				'name_bg'	=> htmlspecialchars(trim($params['name_bg'])),
				'name_en'	=> htmlspecialchars(trim($params['name_en'])),
				'name_de'	=> htmlspecialchars(trim($params['name_de'])),
				'name_ru'	=> htmlspecialchars(trim($params['name_ru'])),
				'name_ro'	=> htmlspecialchars(trim($params['name_ro'])),
				'description_bg'	=> trim($params['description_bg']),
				'description_en'	=> trim($params['description_en']),
				'description_de'	=> trim($params['description_de']),
				'description_ru'	=> trim($params['description_ru']),
				'description_ro'	=> trim($params['description_ro']),
				'htaccess_url_bg'	=> $params['htaccess_url_bg'],
				'htaccess_url_en'	=> $params['htaccess_url_en'],
				'htaccess_url_de'	=> $params['htaccess_url_de'],
				'htaccess_url_ru'	=> $params['htaccess_url_ru'],
				'htaccess_url_ro'	=> $params['htaccess_url_ro'],
				'meta_title_bg'			=> $params['meta_title_bg'],
				'meta_title_en'			=> $params['meta_title_en'],
				'meta_title_de'			=> $params['meta_title_de'],
				'meta_title_ru'			=> $params['meta_title_ru'],
				'meta_title_ro'			=> $params['meta_title_ro'],
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
				'active'			=> $params['active'],
				'cms_user_id'		=> $_SESSION["uid"],
			);
			
			if($act == "add") {
				shiftPos($db, $news_categories_table);
				$res = $db->autoExecute($news_categories_table,$fields,DB_AUTOQUERY_INSERT); safeCheck($res);
				$id = mysqli_insert_id($db->connection);
			}
			
			if($act == "edit") {
				$id = (int)$params["id"];
				$res = $db->autoExecute($news_categories_table,$fields,DB_AUTOQUERY_UPDATE,"id = " . $id);safeCheck($res);
			}
			
			$htaccessUpdate = new Settings();
			$htaccess_type = "news_categories";
			
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
		
		function getNewsCategories($page = 0){
			global $db;
			global $lng;
			global $news_categories_table;
			
			$news_categories = $db->getAll("SELECT *, name_{$lng} AS name FROM ".$news_categories_table." WHERE edate = 0 ORDER BY pos"); safeCheck($news_categories);
			foreach($news_categories as $k => $v){
				
				$news_categories[$k] = $v;
			}
			return $news_categories;
		}
		
		function deleteRecord($id){
			global $db;
			global $news_categories_table;
			
			$fields = array(
								"edate" => time()
							);
			$res = $db->autoExecute($news_categories_table, $fields, DB_AUTOQUERY_UPDATE, " id = '".$id."' "); safeCheck($res);
			
			return $res;
		}
		
	}
	
?>