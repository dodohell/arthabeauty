<?php
	class News extends Settings{
		
		public $pagination = "";
		
		function getRecord($id = 0){
			global $db;
			global $lng;
			global $news_table;
			
			$id = (int)$id;
			
			$row = $db->getRow("SELECT *, name_{$lng} AS name FROM ".$news_table." WHERE id = '".$id."'"); safeCheck($row);
			
			return $row;
		}
		
		function updateRow($test = ""){
			global $db;
			global $news_table;
			
			$row = $db->getRow("SELECT * FROM ".$news_table.""); safeCheck($row);
			
			return $row;
		}
		
		function addEditRow($params){
			global $db;
			global $news_table;
			global $news_to_news_categories_table;
            
            $act = $params->getString("act");
			$id = $params->getInt("id");
            
			$fields = array(
				'active'	=> $params->getInt("active"),
				'news_author_id'	=> $params->getInt("news_author_id"),
				'accent'	=> $params->getInt("accent"),
                'name_bg'	=> $params->getString('name_bg'),
                'name_en'	=> $params->getString('name_en'),
                'name_de'	=> $params->getString('name_de'),
                'name_ru'	=> $params->getString('name_ru'),
                'name_ro'	=> $params->getString('name_ro'),
				
				'htaccess_url_bg'	=> $params->getString('htaccess_url_bg'),
                'htaccess_url_en'	=> $params->getString('htaccess_url_en'),
                'htaccess_url_de'	=> $params->getString('htaccess_url_de'),
                'htaccess_url_ru'	=> $params->getString('htaccess_url_ru'),
                'htaccess_url_ro'	=> $params->getString('htaccess_url_ro'),
                
                'excerpt_bg'	=> $params->getString('excerpt_bg', false),
                'excerpt_en'	=> $params->getString('excerpt_en', false),
                'excerpt_de'	=> $params->getString('excerpt_de', false),
                'excerpt_ru'	=> $params->getString('excerpt_ru', false),
                'excerpt_ro'	=> $params->getString('excerpt_ro', false),
                
                'description_bg'	=> $params->getString('description_bg', false),
                'description_en'	=> $params->getString('description_en', false),
                'description_de'	=> $params->getString('description_de', false),
                'description_ru'	=> $params->getString('description_ru', false),
                'description_ro'	=> $params->getString('description_ro', false),
                
                'meta_title_bg'			=> $params->getString('meta_title_bg'),
                'meta_title_en'			=> $params->getString('meta_title_en'),
                'meta_title_de'			=> $params->getString('meta_title_de'),
                'meta_title_ru'			=> $params->getString('meta_title_ru'),
                'meta_title_ro'			=> $params->getString('meta_title_ro'),
                
                'meta_description_bg'	=> $params->getString('meta_description_bg'),
                'meta_description_en'	=> $params->getString('meta_description_en'),
                'meta_description_de'	=> $params->getString('meta_description_de'),
                'meta_description_ru'	=> $params->getString('meta_description_ru'),
                'meta_description_ro'	=> $params->getString('meta_description_ro'),
                
                'meta_keywords_bg'		=> $params->getString('meta_keywords_bg'),
                'meta_keywords_en'		=> $params->getString('meta_keywords_en'),
                'meta_keywords_de'		=> $params->getString('meta_keywords_de'),
                'meta_keywords_ru'		=> $params->getString('meta_keywords_ru'),
                'meta_keywords_ro'		=> $params->getString('meta_keywords_ro'),
                
                'meta_metatags_bg'		=> $params->getString('meta_metatags_bg'),
                'meta_metatags_en'		=> $params->getString('meta_metatags_en'),
                'meta_metatags_de'		=> $params->getString('meta_metatags_de'),
                'meta_metatags_ru'		=> $params->getString('meta_metatags_ru'),
                'meta_metatags_ro'		=> $params->getString('meta_metatags_ro'),
                
                'publishdate'	=> strtotime($params->getString("publishdate")),
				'publishdate_2'	=> date("Y-m-d H:i:s", strtotime($params->getString("publishdate"))),
                
                'firstpage'	=> $params->getInt("firstpage"),
			);
			
			$pic_header = copyImage($_FILES['pic_header'], "../files/", "../files/tn/", "../files/tntn/", "450x");
			if(!empty($pic_header)) $fields['pic_header'] = $pic_header;
			
			$pic = copyImage($_FILES['pic'], "../files/", "../files/tn/", "../files/tntn/", "450x");
			if(!empty($pic)) $fields['pic'] = $pic;
			
			if($act == "add") {
                $fields["postdate"] = time();
				shiftPos($db, $news_table);
				$res = $db->autoExecute($news_table,$fields,DB_AUTOQUERY_INSERT); safeCheck($res);
				$id = mysqli_insert_id($db->connection);
			}
			
			if($act == "edit") {
				$res = $db->autoExecute($news_table,$fields,DB_AUTOQUERY_UPDATE,"id = " . $id);safeCheck($res);
			}
			
			$res = $db->Query("DELETE FROM ".$news_to_news_categories_table." WHERE news_id = '{$id}'"); safeCheck($res);
			$news_categoriesRequest = $params->get("news_categories");
			if ($news_categoriesRequest && sizeof($news_categoriesRequest)){
				foreach($news_categoriesRequest as $k=>$v){
					$res = $db->Query("INSERT INTO ".$news_to_news_categories_table." (news_id, news_category_id) VALUES ('".$id."','".$v."')"); safeCheck($res);
				}
			}
			
			$htaccessUpdate = new Settings();
			$htaccess_type = "news";
			
			if ( $params->has("htaccess_url_bg") ){
				$htaccessUpdate->updateHtaccess("bg", $params->getString("htaccess_url_bg"), $htaccess_type, $id);
			}
			if ( $params->has("htaccess_url_en") ){
				$htaccessUpdate->updateHtaccess("en", $params->getString("htaccess_url_en"), $htaccess_type, $id);
			}
			if ( $params->has("htaccess_url_de") ){
				$htaccessUpdate->updateHtaccess("de", $params->getString("htaccess_url_de"), $htaccess_type, $id);
			}
			if ( $params->has("htaccess_url_ru") ){
				$htaccessUpdate->updateHtaccess("ru", $params->getString("htaccess_url_ru"), $htaccess_type, $id);
			}
			if ( $params->has("htaccess_url_ro") ){
				$htaccessUpdate->updateHtaccess("ro", $params->getString("htaccess_url_ro"), $htaccess_type, $id);
			}
            
            return $id;
		}
		
		function deleteFile($id){
			global $db;
			global $news_files_table;
			
			$fields = array(
								"edate" => time()
							);
			$res = $db->autoExecute($news_files_table, $fields, DB_AUTOQUERY_UPDATE, " id = '".$id."' "); safeCheck($res);
			
			return $res;
		}
		
		function deleteField($id, $field){
			global $db;
			global $lng;
			global $news_table;
			
			$res = $db->autoExecute($news_table, array( $field => "" ), DB_AUTOQUERY_UPDATE, " id = '".$id."' "); safeCheck($res);
			
			return $res;
		}
		
		function postImage($file = "", $news_id = 0){
			global $db;
			global $news_images_table;
			
			$fields = array(
								"file" => $file,
								"news_id" => $news_id,
							);
			shiftPos($db, $news_images_table);
			$res = $db->autoExecute($news_images_table, $fields, DB_AUTOQUERY_INSERT); safeCheck($res);
			
			return $res;
		}
        
        function updateImage(FilteredMap $params){
			global $db;
			global $news_images_table;
			
			$id = $params->getInt("id");
			
			$fields = array(
								'name_bg'	=> $params->getString("name_bg"),
								'name_en'	=> $params->getString("name_en"),
								'name_de'	=> $params->getString("name_de"),
								'name_ru'	=> $params->getString("name_ru"),
								'name_ro'	=> $params->getString("name_ro"),
							);
			$res = $db->autoExecute($news_images_table, $fields, DB_AUTOQUERY_UPDATE, " id = '".$id."' "); safeCheck($res);
			
			return $res;
		}
		
		function deleteImage($id){
			global $db;
			global $news_images_table;
			
			$fields = array(
								"edate" => time()
							);
			$res = $db->autoExecute($news_images_table, $fields, DB_AUTOQUERY_UPDATE, " id = '".$id."' "); safeCheck($res);
			
			return $res;
		}
		
		function deleteRecord($id){
			global $db;
			global $news_table;
			
			$fields = array(
								"edate" => time(),
                                "edate_cms_user_id" => $_SESSION["uid"]
							);
			$res = $db->autoExecute($news_table, $fields, DB_AUTOQUERY_UPDATE, " id = '".$id."' "); safeCheck($res);
			
			return $res;
		}
		
		function getImages($news_id){
			global $db;
			global $news_images_table;
			
			$images = $db->getAll("SELECT * FROM ".$news_images_table." WHERE edate = 0 AND news_id = '".$news_id."' ORDER BY pos"); safeCheck($images);
			return $images;
		}
        
        function getImageForm($id){
			global $db;
			global $sm;
			global $news_images_table;
			
			$row = $db->getRow("SELECT * FROM ".$news_images_table." WHERE edate = 0 AND id = '".$id."' ORDER BY pos"); safeCheck($row);
			$sm->assign("row", $row);
			
			$sm->display("admin/news_images.html");
		}
		
		function getSelectedCategories($id){
			global $db;
			global $news_to_news_categories_table;
			
			$all = $db->getAll("SELECT * FROM ".$news_to_news_categories_table." WHERE news_id = '".$id."'"); safeCheck($all);
			
			return $all;
		}
		
		function getNews($page = 0, $limit = 50, $search_string = ""){
			global $db;
			global $lng;
			global $news_table;
            global $params;


            $search_string = $params->getString("search_string");
			if ( $search_string ){
				$search_string = strtolower($search_string);
				$sql_search_string = " AND (LOWER(name_{$lng}) LIKE '%".$search_string."%' OR LOWER(excerpt_{$lng}) LIKE '%".$search_string."%' OR LOWER(description_{$lng}) LIKE '%".$search_string."%')";
			}
			
			
			$start = $page * $limit;
			$pages = $db->getRow("SELECT count(id) AS cntr FROM ".$news_table." WHERE edate = 0 {$sql_search_string}"); safeCheck($pages);
			$total_pages = ceil($pages["cntr"]/$limit);
			$generate_pages = '';
			
			if ( $page > 0 ){
				$generate_pages .= '<a href="news.php?'.$_SERVER["QUERY_STRING"].'&page=0" class="first paginate_button paginate_button_enabled" tabindex="0">First</a>';
			}else{
				$generate_pages .= '<a href="#" class="first paginate_button paginate_button_disabled" tabindex="0">First</a>';
			}
			if ( $page > 0 ){
				$generate_pages .= '<a href="news.php?'.$_SERVER["QUERY_STRING"].'&page='.($page-1).'" class="previous paginate_button paginate_button_enabled" tabindex="0">Previous</a>';
			}else{
				$generate_pages .= '<a href="#" class="previous paginate_button paginate_button_disabled" tabindex="0">Previous</a>';
			}
			
			$generate_pages .= '<span>';
			for ( $i = 0 ; $i < $total_pages; $i++ ){
				if ( $page == $i ){
					$generate_pages .= '<a href="news.php?'.$_SERVER["QUERY_STRING"].'&page='.$i.'" class="paginate_active" tabindex="0">'.($i+1).'</a>';
				}else{
					$generate_pages .= '<a href="news.php?'.$_SERVER["QUERY_STRING"].'&page='.$i.'" class="paginate_button" tabindex="0">'.($i+1).'</a>';
				}
			}
			$generate_pages .= '</span>';
			
			if ( $page < $total_pages-1 ){
				$generate_pages .= '<a href="news.php?'.$_SERVER["QUERY_STRING"].'&page='.($page+1).'" class="next paginate_button paginate_button_enabled" tabindex="0">Next</a>';
			}else{
				$generate_pages .= '<a href="#" class="next paginate_button paginate_button_disabled" tabindex="0">Next</a>';
			}
			
			if ( $page < $total_pages-1 ){
				$generate_pages .= '<a href="news.php?'.$_SERVER["QUERY_STRING"].'&page='.($total_pages-1).'" class="last paginate_button paginate_button_enabled" tabindex="0">Last</a>';
			}else{
				$generate_pages .= '<a href="#" class="last paginate_button paginate_button_disabled" tabindex="0">Last</a>';
			}
			
			$this->pagination = $generate_pages;
			
			$news = $db->getAll("SELECT *, name_{$lng} AS name FROM ".$news_table." WHERE edate = 0 {$sql_search_string} ORDER BY publishdate DESC, pos LIMIT {$start}, {$limit}"); safeCheck($news);
			foreach($news as $k => $v){
				
				$news[$k] = $v;
			}
			return $news;
		}
		
		function getNewsPagination(){
			return $this->pagination;
		}
		
	}
	
?>