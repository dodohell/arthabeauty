<?php
	class CategoryTypes extends Settings{
		
		
		public static function getRecord(int $id){
			global $db;
			global $lng;
			global $category_types_table;
			
			$row = $db->getRow("SELECT *,
                                    name_{$lng} AS name,
                                    description_{$lng} AS description,
                                    htaccess_url_{$lng} AS htaccess_url,
                                    meta_title_{$lng} AS meta_title,
                                    meta_description_{$lng} AS meta_description,
                                    meta_keywords_{$lng} AS meta_keywords,
                                    meta_metatags_{$lng} AS meta_metatags
								FROM
                                    ".$category_types_table."
								WHERE 
                                    id = {$id}
                                AND edate = 0
								"); safeCheck($row);
			return $row;
		}
        
		public static function getActiveRecord(int $id){
			global $db;
			global $lng;
			global $category_types_table;
			
			$row = $db->getRow("SELECT *,
                                    name_{$lng} AS name,
                                    description_{$lng} AS description,
                                    htaccess_url_{$lng} AS htaccess_url,
                                    meta_title_{$lng} AS meta_title,
                                    meta_description_{$lng} AS meta_description,
                                    meta_keywords_{$lng} AS meta_keywords,
                                    meta_metatags_{$lng} AS meta_metatags
								FROM
                                    ".$category_types_table."
								WHERE 
                                    id = {$id}
                                AND active = 1
                                AND edate = 0
								"); safeCheck($row);
			return $row;
		}
		
		public static function getCategoryTypes(FilteredMap $params){
			global $db;
			global $lng;
			global $category_types_table;
			
			$sql = "SELECT 
                        category_types.*, 
                        category_types.name_{$lng} AS name,
                        category_types.htaccess_url_{$lng} AS htaccess_url
				    FROM ".$category_types_table." AS category_types
				    WHERE 
                        category_types.edate = 0 
				    AND category_types.name_{$lng} <> ''
				    AND category_types.active = 1
				    ORDER BY category_types.pos";
			$category_types = $db->getAll($sql); safeCheck($category_types);
			
			return $category_types;
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
			global $category_types_table;
			
			$sm->configLoad($language_file);
			$configVars = $sm->getConfigVars();
			$sm->configLoad($htaccess_file);
			$htaccessVars = $sm->getConfigVars();
			
			$row = $db->getRow("SELECT id, name_{$lng} AS name, url_{$lng} AS url, htaccess_url_{$lng} AS htaccess_url FROM ".$category_types_table." WHERE id = '".$id."' AND edate = 0"); safeCheck($row);
			$row['link_title'] = str_replace($link_find, $link_repl, $row['name']);
			
			$htaccess_prefix = $htaccessVars["htaccess_categorytypes"];
			if ( $row["url"] ){
				$tmp_breadcrumbs = '<li class="breadcrumb-item"><a href="'.$row["url"].'" target="'.$row["target"].'">'.$row["name"].'</a></li>'.$tmp_breadcrumbs;
			}elseif( $row["htaccess_url"] ){
				$tmp_breadcrumbs = '<li class="breadcrumb-item"><a href="'.$row["htaccess_url"].'" target="'.$row["target"].'">'.$row["name"].'</a></li>'.$tmp_breadcrumbs;
			}else{
				$tmp_breadcrumbs = '<li class="breadcrumb-item"><a href="/'.$htaccess_prefix.'/'.$row["id"].'" target="'.$row["target"].'">'.$row["name"].'</a></li>'.$tmp_breadcrumbs;
			}
			if ($row["category_id"] != 0){
				return self::generateBreadcrumbs($row["category_id"], $tmp_breadcrumbs);
			}else{
				return '<li class="breadcrumb-item"><a href="'.$host.'">'.$configVars["home_breadcrumbs"].'</a></li>'.$tmp_breadcrumbs;
			}
		}
		
		public function getPage($id, $page = 0, $params = null){
			global $db;
			global $sm;
			global $lng;
			global $htaccess_file;
			global $htaccess_file_bg;
			global $htaccess_file_en;
			global $htaccess_file_de;
			global $htaccess_file_ru;
			global $htaccess_file_ro;
			global $category_types_table;
			global $category_to_category_type_table;
            global $products_table;
            global $product_to_category_table;
            global $products_images_table;
            global $product_types_table;
            global $products_comments_table;
            global $limit;
            
			$row = $this->getActiveRecord($id);
            $sm->assign("row", $row);            
            
            if ( $params->getString("old_link") && $row["htaccess_url"] ){
				header ('HTTP/1.1 301 Moved Permanently');
				if ( $params->getInt("sort_by_type") ){
					header('Location: '.$row["htaccess_url"]."/".strtolower($params->getInt("sort_by_type")));
				}else{
					header('Location: '.$row["htaccess_url"]);
				}
                die();
			}                    
            
            $sm->assign("infoKeys", $row['meta_keywords']);
			if ( $row["meta_title"] ){
				$sm->assign("infoTitle", $row['meta_title']);
			}else{
				$sm->assign("infoTitle", $row['name']);
			}
			$sm->assign("infoDescr", $row['meta_description']);
            
			$checkedCategories = $db->getAll("SELECT
                                                    ctct.category_id
                                                FROM
                                                    {$category_types_table} AS ct
                                                INNER JOIN {$category_to_category_type_table} AS ctct ON ct.id = ctct.category_type_id
                                                WHERE
                                                    ctct.category_type_id = {$id}
                                                AND ct.active = 1
                                                AND ct.edate = 0"); safeCheck($checkedCategories);
            $checkedCategories = array_values(array_unique(array_column($checkedCategories, 'category_id')));
            $checkedCategoriesStr = $checkedCategories ? implode(",", $checkedCategories) : "''";
            
            $sm->configLoad($htaccess_file);
			$htaccessVars = $sm->getConfigVars();
            
            $sort_by_price = $params->has("sort_by_price") ? $params->getString("sort_by_price") : "";
            $sql_order_by = " ORDER BY p.id DESC ";
            if($sort_by_price == "asc"){
                $sql_order_by = " ORDER BY p.price ASC";
            }else if($sort_by_price == "desc"){
                $sql_order_by = " ORDER BY p.price DESC ";
            }
            
            if($sort_by_price == "asc"){
                $page_url = isset($row["htaccess_url"]) && trim($row["htaccess_url"]) ? $row["htaccess_url"]."/price-asc" : "/".$htaccessVars["htaccess_categorytypes"]."/".$id."/price-asc";
            }else if($sort_by_price == "desc"){
                $page_url = isset($row["htaccess_url"]) && trim($row["htaccess_url"]) ? $row["htaccess_url"]."/price-desc" : "/".$htaccessVars["htaccess_categorytypes"]."/".$id."/price-desc";
            }else{
                $page_url = isset($row["htaccess_url"]) && trim($row["htaccess_url"]) ? $row["htaccess_url"] : "/".$htaccessVars["htaccess_categorytypes"]."/".$id;
            }
            
            $current_page = is_numeric($page) && $page > 0 ? (int)$page : 1;
            $limit = (int)$limit;
            
            $results = $db->getRow("SELECT 
                                        COUNT(DISTINCT p.id) AS cntr
                                    FROM
                                        {$products_table} AS p
                                    INNER JOIN {$product_to_category_table} AS ptc ON p.id = ptc.product_id
                                    WHERE
                                        ptc.category_id IN ({$checkedCategoriesStr})
                                    AND p.edate = 0
                                    AND p.active = 1"); safeCheck($results);
            
            $total_pages = (int)ceil((int)$results["cntr"]/$limit); //break records into pages
            $start = (($current_page-1) * $limit); //get starting position
            $sql_limit = " LIMIT {$start}, {$limit}";
            
            $pages = Helpers::paginate($current_page, $total_pages, $page_url);
            $sm->assign("pages", $pages);
            
            $items = $db->getAll("SELECT DISTINCT
                                    p.*,
                                    p.name_{$lng} AS name,
                                    p.excerpt_{$lng} AS excerpt,
                                    p.description_{$lng} AS description,
                                    p.meta_title_{$lng} AS meta_title,
                                    p.meta_keywords_{$lng} AS meta_keywords,
                                    p.meta_description_{$lng} AS meta_description,
                                    p.meta_metatags_{$lng} AS meta_metatags, 
                                    (SELECT pi.pic FROM " . $products_images_table . " AS pi WHERE pi.product_id = p.id ORDER BY pi.pos LIMIT 1) as mainPic,
                                    (SELECT product_types.name_{$lng} FROM ".$product_types_table." AS product_types WHERE p.product_type_id = product_types.id) AS product_type_name,
                                    (SELECT SUM(rating.rating)/COUNT(rating.id) FROM ".$products_comments_table." AS rating WHERE rating.product_id = p.id AND rating.edate = 0) AS rating,
                                    (SELECT COUNT(rating2.id) FROM ".$products_comments_table." AS rating2 WHERE rating2.product_id = p.id  AND rating2.edate = 0) AS reviews_count
                                    FROM
                                        {$products_table} AS p
                                    INNER JOIN {$product_to_category_table} AS ptc ON p.id = ptc.product_id
                                    WHERE
                                        ptc.category_id IN ({$checkedCategoriesStr})
                                    AND p.edate = 0
                                    AND p.active = 1
                                    {$sql_order_by}
                                    {$sql_limit}"); safeCheck($items);
            foreach ($items as $k => $v) {
                $items[$k]["excerpt"] = htmlspecialchars_decode($v["excerpt"]);
            }
			$sm->assign("items", $items);
            
			$breadcrumbs = self::generateBreadcrumbs($id);
			$sm->assign("breadcrumbs", $breadcrumbs);
            
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
			}
            
            $sm->configLoad($htaccess_file);
            
			$sm->assign("link_bg", $link_bg);
			$sm->assign("link_en", $link_en);
			$sm->assign("link_de", $link_de);
			$sm->assign("link_ru", $link_ru);
			$sm->assign("link_ro", $link_ro);
            
			$sm->assign("breadcrumbs", $breadcrumbs);
            
            $categoriesObj = new Categories();
            $filter_categories = $categoriesObj->getCategories();
            $sm->assign("filter_categories", $filter_categories);
			
			$sm->display("category_types.html");
		}
		
	}
	
?>