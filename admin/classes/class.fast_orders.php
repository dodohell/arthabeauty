<?php
	class FastOrders extends Settings{
		
		public $pagination = "";
		
		public static function getRecord(int $id = 0){
			global $db;
			global $fast_orders_table;
			
			$row = $db->getRow("SELECT * FROM ".$fast_orders_table." WHERE id = $id"); safeCheck($row);
			
			return $row;
		}
		
		function addEditRow($params){
			global $db;
			global $fast_orders_table;
            
            $act = $params->getString("act");
			$id = $params->getInt("id");
            
			$fields = array(
				'active'	=> $params->getInt("active"),
                'name_bg'	=> $params->getString('name_bg'),
                'name_en'	=> $params->getString('name_en'),
                'name_de'	=> $params->getString('name_de'),
                'name_ru'	=> $params->getString('name_ru'),
                'name_ro'	=> $params->getString('name_ro'),
                
                'excerpt_bg'	=> $params->getString('excerpt_bg'),
                'excerpt_en'	=> $params->getString('excerpt_en'),
                'excerpt_de'	=> $params->getString('excerpt_de'),
                'excerpt_ru'	=> $params->getString('excerpt_ru'),
                'excerpt_ro'	=> $params->getString('excerpt_ro'),
                
                'description_bg'	=> $params->getString('description_bg'),
                'description_en'	=> $params->getString('description_en'),
                'description_de'	=> $params->getString('description_de'),
                'description_ru'	=> $params->getString('description_ru'),
                'description_ro'	=> $params->getString('description_ro'),
                
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
			
			if($act == "add") {
                $fields["postdate"] = time();
				shiftPos($db, $fast_orders_table);
				$res = $db->autoExecute($fast_orders_table,$fields,DB_AUTOQUERY_INSERT); safeCheck($res);
				$id = mysqli_insert_id($db->connection);
			}
			
			if($act == "edit") {
				$res = $db->autoExecute($fast_orders_table,$fields,DB_AUTOQUERY_UPDATE,"id = " . $id);safeCheck($res);
			}

            return $id;
		}
		
		function deleteRecord($id){
			global $db;
			global $fast_orders_table;
			
			$fields = array(
								"edate" => time(),
                                "edate_cms_user_id" => $_SESSION["uid"]
							);
			$res = $db->autoExecute($fast_orders_table, $fields, DB_AUTOQUERY_UPDATE, " id = '".$id."' "); safeCheck($res);
			
			return $res;
		}

		function getFastOrders($page = 0, $limit = 50, $search_string = ""){
			global $db;
			global $lng;
			global $fast_orders_table;
            global $params;

            $search_string = $params->getString("search_string");
			if ( $search_string ){
				$search_string = strtolower($search_string);
				$sql_search_string = " AND (LOWER(name) LIKE '%".$search_string."%' OR LOWER(phone) LIKE '%".$search_string."%' OR LOWER(email) LIKE '%".$search_string."%')";
			}
			
			$start = $page * $limit;
			$pages = $db->getRow("SELECT count(id) AS cntr FROM ".$fast_orders_table." WHERE edate = 0 {$sql_search_string}"); safeCheck($pages);
			$total_pages = ceil($pages["cntr"]/$limit);
			$generate_pages = '';
			
			if ( $page > 0 ){
				$generate_pages .= '<a href="fast_orders.php?'.$_SERVER["QUERY_STRING"].'&page=0" class="first paginate_button paginate_button_enabled" tabindex="0">First</a>';
			}else{
				$generate_pages .= '<a href="#" class="first paginate_button paginate_button_disabled" tabindex="0">First</a>';
			}
			if ( $page > 0 ){
				$generate_pages .= '<a href="fast_orders.php?'.$_SERVER["QUERY_STRING"].'&page='.($page-1).'" class="previous paginate_button paginate_button_enabled" tabindex="0">Previous</a>';
			}else{
				$generate_pages .= '<a href="#" class="previous paginate_button paginate_button_disabled" tabindex="0">Previous</a>';
			}
			
			$generate_pages .= '<span>';
			for ( $i = 0 ; $i < $total_pages; $i++ ){
				if ( $page == $i ){
					$generate_pages .= '<a href="fast_orders.php?'.$_SERVER["QUERY_STRING"].'&page='.$i.'" class="paginate_active" tabindex="0">'.($i+1).'</a>';
				}else{
					$generate_pages .= '<a href="fast_orders.php?'.$_SERVER["QUERY_STRING"].'&page='.$i.'" class="paginate_button" tabindex="0">'.($i+1).'</a>';
				}
			}
			$generate_pages .= '</span>';
			
			if ( $page < $total_pages-1 ){
				$generate_pages .= '<a href="fast_orders.php?'.$_SERVER["QUERY_STRING"].'&page='.($page+1).'" class="next paginate_button paginate_button_enabled" tabindex="0">Next</a>';
			}else{
				$generate_pages .= '<a href="#" class="next paginate_button paginate_button_disabled" tabindex="0">Next</a>';
			}
			
			if ( $page < $total_pages-1 ){
				$generate_pages .= '<a href="fast_orders.php?'.$_SERVER["QUERY_STRING"].'&page='.($total_pages-1).'" class="last paginate_button paginate_button_enabled" tabindex="0">Last</a>';
			}else{
				$generate_pages .= '<a href="#" class="last paginate_button paginate_button_disabled" tabindex="0">Last</a>';
			}
			
			$this->pagination = $generate_pages;
			
			$fast_orders = $db->getAll("SELECT * FROM ".$fast_orders_table." WHERE edate = 0 {$sql_search_string} ORDER BY postdate DESC LIMIT {$start}, {$limit}"); safeCheck($fast_orders);
//			foreach($fast_orders as $k => $v){
//				
//				$fast_orders[$k] = $v;
//			}
			return $fast_orders;
		}
		
		function getFastOrdersPagination(){
			return $this->pagination;
		}
	
	
    public static function getCartProduct($product_id) {
        global $db;
        global $lng;
        global $products_table;
        global $products_images_table;
        global $brands_table;
        global $collections_table;
        
        $product = $db->getRow("SELECT 
        													id, name_{$lng} AS name, barcode, name_en, barcode,
																	(SELECT name_{$lng} FROM {$brands_table} WHERE id = brand_id) AS brand_name,
																	(SELECT name_{$lng} FROM {$collections_table} WHERE id = collection_id) AS col_name,
																	(SELECT images.pic FROM ".$products_images_table." AS images WHERE images.product_id = products.id AND edate=0 ORDER BY pos LIMIT 1) AS pic
        											  FROM ".$products_table." AS products WHERE edate = 0 AND id = '".$product_id."'"); safeCheck($product);

        return $product;
    }
		
	}
	
?>