<?php

class Settings {

    function generateEditor($langs, $fieldName, $contentValue) {
        $tabs = "";
        for ($i = 0; $i < count($langs); $i++) {
            $field = $fieldName . "_" . strtolower($langs[$i]['short']);
            if ($i == 0) {
                $activeTab = " activeTab";
                $activeTabEditor = " activeTabEditor";
            } else {
                $activeTab = "";
                $activeTabEditor = "";
            }

            $tabs .= '<div class="tabLanguage' . $activeTab . '">' . $langs[$i]["long"] . '</div>';
            $editors .= '<div class="tabLanguageEditor' . $activeTabEditor . '"><textarea class="ckeditor inputField" cols="80" id="' . $field . '" name="' . $field . '" rows="10">' . $contentValue[$field] . '</textarea></div>';
        }
        $js = '
					<script language="javascript" type="text/javascript">
					
					$(document).ready(function(){
						$("#editor_' . $fieldName . ' .tabLanguage").bind("click", function(){
							var index = $("#editor_' . $fieldName . ' .tabLanguage").index(this);
							$("#editor_' . $fieldName . ' .tabLanguage").removeClass("activeTab");
							$(this).addClass("activeTab");
							
							$("#editor_' . $fieldName . ' .tabLanguageEditor").removeClass("activeTabEditor");
							$("#editor_' . $fieldName . ' .tabLanguageEditor").eq(index).addClass("activeTabEditor");
							
						});
					});
					</script>
					';

        return $js . '<div class="editorHolder" id="editor_' . $fieldName . '">' . $tabs . '<div class="clear"></div>' . $editors . '</div>' . $js_end;
    }

    function updateHtaccess($lang, $htaccess_url, $type, $record_id = 0) {
        global $db;
        global $htaccess_table;
//        if($lang == 'ro'){
//            echo "<pre>";
//            var_dump($lang);
//            var_dump($htaccess_url);
//            var_dump($type);
//            var_dump($record_id);
//            echo "</pre>";
//            exit();
//        }
        $fields = array(
            "lang" => $lang,
            "htaccess_url" => $htaccess_url,
            "type" => $type,
            "record_id" => $record_id
        );
        $result = $this->checkHtaccess($htaccess_url, $type, $record_id);
        
        if (!$result) {
            $res = $db->autoExecute($htaccess_table, $fields, DB_AUTOQUERY_INSERT);
            safeCheck($res);
        } else {
            $update_id = $this->checkHtaccessByID($htaccess_url, $type, $record_id);
            if ($update_id) {
                $res = $db->autoExecute($htaccess_table, $fields, DB_AUTOQUERY_UPDATE, " id = '" . $update_id . "' ");
                safeCheck($res);
            }
        }
    }

    function checkHtaccess($htaccess_url, $type = "", $record_id = 0, $lang = "") {
        global $db;
        global $htaccess_table;

        $sql = "SELECT * FROM " . $htaccess_table . " WHERE edate = 0 AND htaccess_url = '" . $htaccess_url . "'";
        $check = $db->getRow($sql); safeCheck($check);
        
        $result = 0;
        if ($check["id"]) {
            $result = 1;
            if ($record_id != $check["record_id"]) {
                $result = 0;
            }
            if ($record_id == $check["record_id"] && $type == $check["type"] && $lang == $check["lang"])  {
                $result = 0;
            }
        }
        
        return $result;
    }

    function checkHtaccessByID($htaccess_url, $type = "", $record_id = 0) {
        global $db;
        global $htaccess_table;
        
        $check = $db->getRow("SELECT * FROM " . $htaccess_table . " WHERE edate = 0 AND htaccess_url = '" . $htaccess_url . "' AND record_id = '" . $record_id . "'"); safeCheck($check);
        
        return $check["id"];
    }
    
    /**
     * 
     * @global DB $db
     * @global FilteredMap $params
     * @global string $htaccess_table
     * @param string $htaccess_url
     * @param type $page
     * @param type $language_check
     * @return type
     */
    public function checkHtaccessByTerm($htaccess_url = "", $page = 0, $language_check = 0 ) {
        global $db;
        global $params;
        global $htaccess_table;
        
        if($params->has("page")){
            $page = $params->getInt("page");
        }
        
        $htaccess_url = "/" . $htaccess_url;
        $check = $db->getRow("SELECT * FROM " . $htaccess_table . " WHERE edate = 0 AND htaccess_url = '" . $htaccess_url . "' ORDER BY id DESC"); safeCheck($check);
        
        if ($check["record_id"] && !$language_check) {
            if ($check["type"] == "menu") {
                $menuPage = new Menus();
                $menuPage->getPage($check["record_id"]);
            }
            if ($check["type"] == "news") {
                $newsPage = new News();
                $newsPage->getPage($check["record_id"]);
            }
            if ($check["type"] == "categories") {
                $categoriesPage = new Categories();
                //$categoriesPage->getPage($check["record_id"], $page, $options);
                $categoriesPage->getPage($check["record_id"], $page, $params);
            }
            if ($check["type"] == "category_types") {
                $categoryTypes = new CategoryTypes();
                $categoryTypes->getPage($check["record_id"], $page, $params);
            }
            if ($check["type"] == "products") {
                $products = new Products();
                $products->getProductPage($check["record_id"]);
            }
            if ($check["type"] == "news_categories") {
                $newsPage = new News();
                $newsPage->getNewsCategoryPage($check["record_id"], $page);
            }
        }
        
        return $check;
    }

    function getIndexPage() {
        global $sm;
        global $params;
        global $language_file;
		
		$sm->configLoad($language_file);
        
        $category_types = CategoryTypes::getCategoryTypes($params);
        $sm->assign("category_types", $category_types);
        
        $hotoffers = Products::getHotOffers();
        $sm->assign("hotoffers", $hotoffers);
		
		$latest_news = News::getLatestNews(3);
		$sm->assign("latest_news", $latest_news);
		
		$new_products = Products::getNewOffers(3);
		$sm->assign("new_products", $new_products);
		
        
//        $specialOffersObj = new SpecialOffers();
//        $special_offers = $specialOffersObj->getSpecialOffers(array("limit" => 12));
//        $sm->assign("special_offers", $special_offers);
        
        $sm->assign("index", 1);
        $sm->display("index.html");
    }

    function getSitemap() {
        global $sm;
        global $db;
        global $lng;
        global $user;
        global $host;
        global $htaccess_file;
        global $news_table;

        $sm->configLoad($htaccess_file);
        $htaccessVars = $sm->getConfigVars();
        header("Content-type: text/xml; charset=utf-8");

        echo
        "<?xml version=\"1.0\" encoding=\"UTF-8\"?>
			<urlset xmlns:xsi=\"http://www.w3.org/2001/XMLSchema-instance\"
				 xsi:schemaLocation=\"http://www.sitemaps.org/schemas/sitemap/0.9 http://www.sitemaps.org/schemas/sitemap/0.9/sitemap.xsd\"
				 xmlns=\"http://www.sitemaps.org/schemas/sitemap/0.9\">";
        echo "
			<url>	
				<loc>" . $host . "</loc>
				<changefreq>weekly</changefreq>
			  <priority>1</priority>
		   </url>";
        $productsObj = new Customers();
        $products = $productsObj->getCustomersAll();


        foreach ($products as $k => $v) {
            echo "
				<url>	
					<loc>" . $host . "" . $htaccessVars["htaccess_customer"] . "/" . $v["id"] . "</loc>
					<changefreq>weekly</changefreq>
				  <priority>0.9</priority>
			   </url>";
        }

        $news = $db->getAll("SELECT *, name_{$lng} AS name FROM " . $news_table . " WHERE edate = 0 AND name_{$lng} <> '' AND name_{$lng} IS NOT NULL AND active = 1 ORDER BY pos");
        safeCheck($news);
        foreach ($news as $k => $v) {
            echo "
				<url>	
					<loc>" . $host . "" . $htaccessVars["htaccess_news_article"] . "/" . $v["id"] . "</loc>
					<changefreq>weekly</changefreq>
				  <priority>0.9</priority>
			   </url>";
        }

        echo "</urlset>";
    }

    function getFromCommon($tag) {
        global $db;
        global $common_table;
        global $lng;

        $row = $db->getRow("SELECT *, description_{$lng} AS description FROM " . $common_table . " WHERE tag = '" . $tag . "'");
        safeCheck($row);

        return $row;
    }

    function searchPage(FilteredMap $params) {
        global $db;
        global $sm;
        global $lng;
        global $host;
        global $user;
        global $language_file;
        global $products_table;
        global $products_images_table;
        global $product_to_category_table;
        global $attributes_table;
        global $product_to_attribute_option_table;
        global $attributes_to_attribute_options_table;
        global $categories_table;
        global $product_types_table;
        global $brands_table;
        global $products_rating_table;
        global $products_comments_table;
        global $favourites_table;
        global $limit;
        
        $sql_where = "";
        $sql_where_join = "";

        $search_string = $params->getString("search_string");
        $code = $params->getString("code");
        $category_id = $params->getInt("category_id");

        if ($search_string) {
            //$sql_where .= "AND ( LOWER(name_{$lng}) LIKE '%" . $search_string . "%' OR LOWER(excerpt_{$lng}) LIKE '%" . $search_string . "%' OR LOWER(description_{$lng}) LIKE '%" . $search_string . "%' )";
//            $sql_where .= " AND MATCH(products.name_{$lng},products.excerpt_{$lng},products.description_{$lng}) AGAINST('{$search_string}' WITH QUERY EXPANSION)";
            $sql_where .= " AND (MATCH(products.name_en,products.name_{$lng}) AGAINST('{$search_string}') OR products.barcode LIKE '%{$search_string}%' )";
        }//,products.excerpt_{$lng},products.description_{$lng}
        if ($code) {
            $sql_where .= " AND products.code = '{$code}' ";
        }
        
        $sort_by = $params->has("sort_by") ? $params->getString("sort_by") : "";
        $sm->assign("sort_by", $sort_by);
        $sql_order_by = " ORDER BY products.postdate DESC ";
        if($sort_by == "price-asc"){
            $sql_order_by = " ORDER BY products.price ASC";
        }else if($sort_by == "price-desc"){
            $sql_order_by = " ORDER BY products.price DESC ";
        }else if($sort_by == "postdate-asc"){
            $sql_order_by = " ORDER BY products.postdate ASC ";
        }else if($sort_by == "postdate-desc"){
            $sql_order_by = " ORDER BY products.postdate DESC ";
        }
        
        $children = $db->getAll("SELECT * FROM ".$categories_table." WHERE edate = 0 AND category_id = '".$category_id."'"); safeCheck($children);
        if ($children && count($children) > 0 ){
            $sql_ptc_join_in .= " AND ptc.category_id IN ( ".$category_id;
            foreach($children as $k => $v){
                $sql_ptc_join_in .= ",".$v["id"];
            }
            $sql_ptc_join_in .= " ) ";
        }else{
            $sql_ptc_join_in .= " AND ptc.category_id IN ( ".$category_id.")";
//            $sql_ptc_join_in .= "";
        }
        
        $categoriesObj = new Categories();
        if ($category_id) {
//           $sql_where .= " AND ptc.category_id = ".$category_id;
            $sql_where_join .= " INNER JOIN $product_to_category_table AS ptc ON ptc.product_id = products.id {$sql_ptc_join_in}";
            $category = $categoriesObj->getRecord($category_id);
        }
        
//        $category_type_id = $params->getInt("category_type_id");

//        if ($category_type_id) {
//            $sql_where .= "AND ctct.product_id = products.id AND ctct.category_type_id = '" . $category_type_id . "'";
//            $sql_where_from .= ", " . $products_to_category_types_table . " AS ctct";
//        }

        /* Get Left Hand Side Search Settings for this Category */
//        if ($category["id"]) {
//            if ($category["filter_categories"]) {
//                $categoriesObj = new Categories();
//                $filter_categories = $categoriesObj->getCategories(["no_subcategories" => 1]);
//                $sm->assign("filter_categories", $filter_categories);
//            }
//            if ($category["filter_category_types"]) {
//                $filter_category_types = CategoryTypes::getCategoryTypes();
//                $sm->assign("filter_category_types", $filter_category_types);
//            }
//        } else {
//            $category["filter_categories"] = 1;
//            $category["filter_category_types"] = 1;
//
//            $categoriesObj = new Categories();
//            $filter_categories = $categoriesObj->getCategories(["no_subcategories" => 1]);
//            $sm->assign("filter_categories", $filter_categories);
//
//            $filter_category_types = CategoryTypes::getCategoryTypes($params);
//            $sm->assign("filter_category_types", $filter_category_types);
//        }
        $sm->assign("row", $category);
        $sm->assign("from_search_page", 1);
        $sm->assign("filter_search_string", $search_string);
        $sm->assign("filter_code", $code);
        $sm->assign("filter_category_id", $category_id);
        
        /* Generate Pages Listings  */
        $counter = $db->getRow("SELECT 
                                    COUNT(DISTINCT products.id) AS cntr
                                FROM " . $products_table . " AS products
                                    {$sql_where_join}
                                WHERE 
                                    products.edate = 0 
                                AND products.active = 1
                                AND products.name_{$lng} <> ''
                                {$sql_where}"); safeCheck($counter);
				
        $count = $counter["cntr"];
        $sm->assign("resultsCount", $count);
        
        $page = $params->getInt("page");
        $start = $limit * $page;
        $pages = ceil($count / $limit);

        $generate_pages = '';
        
        $sm->assign("count", $count);
        $sm->assign("start", $start+1);
        $sm->assign("end", $start+$limit);
//        $sm->configLoad($htaccess_file);
//        $htaccessVars = $sm->getConfigVars();
        
        $url_prefix = $_SERVER["REQUEST_URI"] . "&page=";
        
        for ($i = 0; $i < $pages; $i++) {
            $selected = '';
            if ($i == $page) {
                $selected = 'active';
            }
            if ($i == 0) {
                $generate_pages .= '<a class="page-link '.$selected.'" href="' . $url_prefix . '">' . ($i + 1) . '</a>';
            } else {
                $generate_pages .= '<a class="page-link '.$selected.'" href="' . $url_prefix . $i . '">' . ($i + 1) . '</a>';
            }
        }

        if ($page > 0) {
            if ($page == 1) {
                $generate_pages = '<a href="' . $url_prefix . '" class="prev">&lt;</a>' . $generate_pages;
            } else {
                $generate_pages = '<a href="' . $url_prefix . ($page - 1) . '" class="prev">&lt;</a>' . $generate_pages;
            }
        }

        if ($page < $pages - 1) {
            $generate_pages .= '<a href="' . $url_prefix . ($page + 1) . '" class="next">&gt;</a>';
        }
        
        $priceAscLink = $_SERVER["REQUEST_URI"] . "&sort_by_price=asc";
        $priceDescLink = $_SERVER["REQUEST_URI"] . "&sort_by_price=desc";
        $sm->assign("priceAscLink", $priceAscLink);
        $sm->assign("priceDescLink", $priceDescLink);
        
        $sort_by_price = "";
        if($params->getString("sort_by_price") == "asc"){
            $sort_by_price = "ASC";
        }elseif ($params->getString("sort_by_price") == "desc"){
            $sort_by_price = "DESC";
        }
        
        //$sql_sort_by = $sort_by_price ? " ORDER BY products.price {$sort_by_price}" : " ORDER BY products.id DESC ";
        
        $sql = "SELECT DISTINCT
                    products.*,
                    products.name_{$lng} AS name,
                    products.excerpt_{$lng} AS excerpt,
                    products.description_{$lng} AS description,
                    products.meta_title_{$lng} AS meta_title,
                    products.meta_keywords_{$lng} AS meta_keywords,
                    products.meta_description_{$lng} AS meta_description,
                    products.meta_metatags_{$lng} AS meta_metatags,
                    (SELECT brands.name_{$lng} FROM ".$brands_table." AS brands WHERE brands.edate = 0 AND products.brand_id = brands.id) AS brand_name,
                    (SELECT pi.pic FROM " . $products_images_table . " AS pi WHERE pi.product_id = products.id ORDER BY pi.pos LIMIT 1) as mainPic,
                    (SELECT product_types.name_{$lng} FROM ".$product_types_table." AS product_types WHERE products.product_type_id = product_types.id) AS product_type_name,
                    (SELECT SUM(rating.rating)/COUNT(rating.id) FROM ".$products_comments_table." AS rating WHERE rating.product_id = products.id) AS rating,
                    (SELECT COUNT(rating2.id) FROM ".$products_comments_table." AS rating2 WHERE rating2.product_id = products.id AND rating2.edate = 0) AS reviews_count
                FROM 
                    ".$products_table." AS products
                {$sql_where_join}
                WHERE
                    products.edate = 0
                AND products.active = 1
                AND products.name_{$lng} <> ''
                {$sql_where}
                -- {$sql_sort_by}
                {$sql_order_by}
                LIMIT {$start}, {$limit}";
        $products = $db->getAll($sql); safeCheck($products);
        
        $helpers = new Helpers();
        $user_group_id = Helpers::getCurentUserGroupId();
        
        foreach ($products as $k => $v) {
            $price_specialoffer = getSpecialOfferPrice($v["id"], $v["brand_id"], 1);
            
            if ( $price_specialoffer["price_specialoffer"] > 0.0 ){
                $v["price"] = $helpers->getDiscountedPrice($v["price"], 1, $user_group_id);
                $v["price_specialoffer"] = $helpers->getDiscountedPrice($price_specialoffer["price_specialoffer"], 1, $user_group_id);
                $v["price_specialoffer_text"] = $price_specialoffer["price_specialoffer_text"];
                $v["bonus_points"] = $price_specialoffer["bonus_points"];
                $v["bonus_points_win"] = round($price_specialoffer["price_specialoffer"] * 1, 0);
                $v["discount_date_to"] = $price_specialoffer["discount_date_to"];
            }else{
                $v["price"] = $helpers->getDiscountedPrice($v["price"], 0, $user_group_id);
                $v["bonus_points_win"] = round($v["price"] * 1, 0);
            }
            
            $v["excerpt"] = htmlspecialchars_decode($v["excerpt"]);
            $v["description"] = htmlspecialchars_decode($v["description"]);
            
            if ( $user["id"] ){
                $check = $db->getRow("SELECT * FROM ".$favourites_table." WHERE edate = 0 AND product_id = ".$v["id"]." AND user_id = ".$user["id"]); safeCheck($check);
                if ( $check["id"] ){
                    $v["in_favourites"] = 1;
                }else{
                    $v["in_favourites"] = 0;
                }
            }

            $v["excerpt"] = htmlspecialchars_decode($v["excerpt"]);
            $v["description"] = htmlspecialchars_decode($v["description"]);
            
            $products[$k] = $v;
        }
        $sm->assign("items", $products);
        
      $products_all = $db->getAll("SELECT 
                                      products.id, 
                                      products.price
                                  FROM 
                                      ".$products_table." AS products
                                  WHERE 
                                      products.edate = 0
                                  AND products.active = 1
                                  AND products.brand_id = {$id} 
                                  $sql_where
                                  "); safeCheck($products_all);
        
			$product_ids = array();
			foreach($products_all as $k => $v){
				$product_ids[] = $v["id"];
				if ( $v["price"] > $price_max ){
					$price_max = $v["price"];
				}
				if ( $v["price"] < $price_min ){
					$price_min = $v["price"];
				}
			}
			$sm->assign("price_max", $price_max);
			$sm->assign("price_min", $price_min);
			
			if ( sizeof($product_ids) > 0 ){
				if ( sizeof($product_ids) == 1 ){
					$sql_search_in_products = " product_id = '".$product_ids[0]."' ";
					$sql_search_in_products_id = " id = '".$product_ids[0]."' ";
				}else{
					$sql_search_in_products = " product_id IN (".implode(",", $product_ids).")";
					$sql_search_in_products_id = " id IN (".implode(",", $product_ids).")";
				}
				
				
				// GET DISTINCT PRODUCT ATTRIBUTE OPTIONS FROM WITHIN THE PRODUCT SELECTION TO CREATE LEFT HAND SIDE SEARCH
				$distinct_options = $db->getAll("SELECT DISTINCT attribute_option_id FROM ".$product_to_attribute_option_table." WHERE edate = 0 AND {$sql_search_in_products}"); safeCheck($distinct_options);
				foreach($distinct_options as $k => $v){
					if ( $v["attribute_option_id"] ){
						$attribute_options_ids[] = $v["attribute_option_id"];
					}
				}
				
				if ( sizeof($attribute_options_ids) > 0 ){
					if ( sizeof($attribute_options_ids) == 1 ){
						$sql_search_in_attribute_options = " AND id = '".$attribute_options_ids[0]."' ";
					}else{
						$sql_search_in_attribute_options = " AND id IN (".implode(",", $attribute_options_ids).")";
					}
				}
				
				// GET DISTINCT PRODUCT ATTRIBUTES FROM WITHIN THE PRODUCT SELECTION TO CREATE LEFT HAND SIDE SEARCH
				$distinct_attributes = $db->getAll("SELECT DISTINCT attribute_id FROM ".$product_to_attribute_option_table." WHERE edate = 0 AND {$sql_search_in_products}"); safeCheck($distinct_attributes);
				foreach($distinct_attributes as $k => $v){
					$attribute_ids[] = $v["attribute_id"];
				}
				
				if ( sizeof($attribute_ids) > 0 ){
					if ( sizeof($attribute_ids) == 1 ){
						$sql_search_in_attributes = " AND id = '".$attribute_ids[0]."' ";
					}else{
						$sql_search_in_attributes = " AND id IN (".implode(",", $attribute_ids).")";
					}
					
					// GET SELECTED ATTRIBUTES
					if ( $params->has('attribute_options') ){
						$attribute_options_selected = $params->get('attribute_options');
					}
					
					// GET DISTINCT PRODUCT ATTRIBUTES BASED ON THE ATTRIBUTES USED WITHIN THE PRODUCTS OF THIS CATEGORY
					$attributes = $db->getAll("SELECT *, name_{$lng} AS name FROM ".$attributes_table." WHERE edate = 0 AND is_filterable = 1 {$sql_search_in_attributes} ORDER BY pos"); safeCheck($attributes);
					foreach($attributes as $k => $v){
						$attribute_options = $db->getAll("SELECT *, option_text_{$lng} AS option_text FROM ".$attributes_to_attribute_options_table." AS atao WHERE atao.attribute_id = '".$v["id"]."' {$sql_search_in_attribute_options} ORDER BY pos"); safeCheck($attribute_options);
						
						foreach( $attribute_options as $kk => $vv ){
							if ( $attribute_options_selected ){
								foreach( $attribute_options_selected as $kkk => $vvv ){
									if ( $vv['id'] == $vvv ){
										$vv['selected'] = 'checked';
									}
								}
							}
							$attribute_options[$kk] = $vv;
						}
						
						$v["attribute_options"] = $attribute_options;
						$attributes[$k] = $v;
					}
					$sm->assign("attributes", $attributes);
					// dbg($attributes);
				}
                
				if($content["page"] != "brand"){
                    $sql = "SELECT DISTINCT brand_id FROM ".$products_table." AS products WHERE edate = 0 AND {$sql_search_in_products_id}";
                    $distinct_brands = $db->getAll($sql); safeCheck($distinct_brands);

                    if ( sizeof($distinct_brands) > 0 ){
                        if ( sizeof($distinct_brands) == 1 ){
                            $sql_brands_select = " AND id = '".$distinct_brands[0]["brand_id"]."' ";
                        }else{
                            foreach($distinct_brands as $k => $v){
                                if ( $v["brand_id"] ){
                                    $distinct_brands_implode[] = $v["brand_id"];
                                }
                            }

                            $sql_brands_select = " AND id IN(".implode(",", $distinct_brands_implode).") ";
                        }
                    }
                    $sql = "SELECT id, pic, name_{$lng} AS name, LEFT(name_{$lng}, 1) AS nameLetter FROM ".$brands_table." WHERE edate = 0 {$sql_brands_select} ORDER BY name_{$lng}";
                    $sqlFirstLetter = "SELECT DISTINCT LEFT(name_{$lng}, 1) AS nameLetter FROM ".$brands_table." WHERE edate = 0 {$sql_brands_select} ORDER BY name_{$lng}";
                    $brands_selected = $db->getAll($sql); safeCheck($brands_selected);
                    $brands_letters = $db->getAll($sqlFirstLetter); safeCheck($brands_letters);

                    $sm->assign("brands_selected", $brands_selected);
                    $sm->assign("brands_letters", $brands_letters);
                }
                
			}
			// END OF FILTERS SECTION
        
        //TODO Rating
//		$productsObj = new Products();
//		foreach( $products as $k => $v ){
//			$v["rating"] = $productsObj->getCustomerRating($v["id"]);
//			$products[$k] = $v;
//		}
        
        $filter_categories = $categoriesObj->getCategories();
        $sm->assign("filter_categories", $filter_categories);
        
        $sm->configLoad($language_file);
        $configVars = $sm->getConfigVars();
        $breadcrumbs = '<a href="'.$host.'">'.$configVars["home_breadcrumbs"].'</a> <span>|</span>';
        $breadcrumbs .= '<span>'.$configVars["search_breadcrumbs"].'</span>';
        $sm->assign("breadcrumbs", $breadcrumbs);
        
        $sm->assign("products", $products);
        $sm->assign("search_string", $search_string);
        $sm->assign("page_categories", 1);
        $sm->assign("pages", $generate_pages);
        
        $sm->display("search.html");
    }

    public function getMessagePage(int $code) {
        global $sm;
        global $language_file;

        $sm->configLoad($language_file);
        $configVars = $sm->getConfigVars();

        $sm->assign("message_title", $configVars["message_title_" . $code]);
        $sm->assign("message_description", $configVars["message_description_" . $code]);
        $sm->assign("messageCode", $code);

        $sm->display("message.html");
    }

    function generateCaptcha() {
        global $install_path;
        
        $md5 = md5(microtime() * time());

        $string = substr($md5, 0, 5);
        $captcha = imagecreatefrompng($install_path."/images/captcha.png");
        $black = imagecolorallocate($captcha, 180, 143, 108);
        $line = imagecolorallocate($captcha, 233, 239, 239);
        $line2 = imagecolorallocate($captcha, 0, 239, 0);

        imageline($captcha, 0, 0, 39, 29, $line);
        imageline($captcha, 40, 0, 64, 29, $line);
        $linesnumber = rand(1, 3);
        for ($i = 1; $i < $linesnumber; $i++) {
            $color = rand(90, 10);
            $line = imagecolorallocate($captcha, $color, $color, $color);
            imageline($captcha, rand(0, 80), rand(0, 30), rand(0, 80), rand(0, 30), $line);
        }
        imagestring($captcha, 4, 3, 4, $string, $black);

        $_SESSION['sess_captchaRequest1'] = $string . "123PropertiesDefensiveText@3";
        
        @header("Content-type: image/png");
        imagepng($captcha);
    }

    function checkCaptcha($code, $return_type = 1) {
        global $host;
        if ($_SESSION['sess_captchaRequest1'] == $code . "123PropertiesDefensiveText@3") {
            if ($return_type == 1) {
                echo 1;
            }
            if ($return_type == 2) {
                return 1;
            }
        } else {
            if ($return_type == 1) {
                echo 0;
            }
            if ($return_type == 2) {
                return 0;
            }
        }
    }

    function checkLogin() {
        global $user;
        global $sm;
        global $htaccess_file;

        $sm->configLoad($htaccess_file);
        $htaccessVars = $sm->getConfigVars();

        if ($user["id"]) {
            return 1;
        } else {
            header("Location: " . $htaccessVars["htaccess_login_page"]);
            die();
        }
    }

    function postTable($params) {
        global $db;
        global $users_tables_table;
        global $user;

        $fields = array(
            "name" => htmlspecialchars(trim($params["table_name"]), ENT_QUOTES),
            "places" => (int) $params["table_places"],
            "user_id" => $user["id"],
            "postdate" => time()
        );
        $res = $db->autoExecute($users_tables_table, $fields, DB_AUTOQUERY_INSERT);
        safeCheck($res);
    }

    function mailSender($email, $message_heading, $message_text, $file_attachment_1 = "", $file_attachment_2 = "", $file_attachment_3 = "", $email_content = array(), $reply_tos = array()) {
        global $domains_cyrillic;
        global $install_path;
        global $sm;
        global $db;
        global $lng;
        global $host;
        global $contacts;
        global $copyrights;
        require_once($install_path . "phpmailer/class.phpmailer.php");

        $sm->assign("subject", $message_heading);
        $sm->assign("message", $message_text);

        $logo = '<img src="https://www.arthabeauty.com/images/logo.png" border="0" />';

            $message_text = '<html>
                                                <head>
                                                        <title>' . $message_heading . '</title>
                                                </head>
                                                <body>
													<table width="100%">
														<tr>
															<td width="100%" style="background-color: #eee; padding: 50px 0px;">
																<table width="650" align="center"><tr><td style="background-color: #fff; padding: 20px;">
																	<table width="100%" cellpadding="0"  cellspacing="0">
																			<tr>
																					<td valign="top" width="240">
																							<a href="' . $host . '" target="_blank">' . $logo . '</a>
																							<br>
																					</td>
																					<td valign="top">
																							<span style="font-size: 11px;">
																							' . $contacts["description"] . '
																							<br>
																							</span>
																							<br>
																					</td>
																			</tr>
																			<tr>
																					<td colspan="2" height="10" bgcolor="#ccc">
																					</td>
																			</tr>
																	</table>

																	<table width="100%" cellpadding="0" cellspacing="0">
																			<tr>
																					<td style="padding:5px;">
																							<br><br><br>
																							' . $message_text . '
																							<br><br><br>
																					</td>
																			</tr>
																	</table>
																	<table width="100%" cellpadding="5" cellspacing="0">
																			<tr>
																					<td valign="top" bgcolor="#ccc">
																							<span style="color: #ffffff; font-size: 11px;">' . $copyrights["description"] . '</span>
																					</td>
																			</tr>
																	</table>
																</td></tr></table>
															</td>
														</tr>
													</table>
                                                </body>
                                        </html>';

        $mailObj = new PHPMailer();
        $mailObj->CharSet = 'utf-8';
        
        $mailObj->From = 'support@arthabeauty.com';
        $mailObj->FromName = 'ArthaBeauty.com';
        
        $mailObj->isHTML(true);
        $mailObj->Subject = $message_heading;
        $mailObj->Body = $message_text;
        $mailObj->AddAddress($email);

        if (sizeof($reply_tos) > 0) {
            foreach ($reply_tos as $k => $v) {
                $mailObj->addReplyTo($v);
            }
        }

        if ($file_attachment_1) {

            $mailObj->AddAttachment($file_attachment_1);
        }

        if ($file_attachment_2) {
            $mailObj->AddAttachment($file_attachment_2);
        }

        if ($file_attachment_3) {
            $mailObj->AddAttachment($file_attachment_3);
        }

        return $mailObj->Send();
    }

    function mailReminder($email, $message_heading, $message_text, $file_attachment_1 = "", $file_attachment_2 = "", $file_attachment_3 = "", $email_content = array()) {
        global $domains_cyrillic;
        global $install_path;
        global $sm;
        global $db;
        global $lng;
        global $host;
        global $contacts;
        global $copyrights;
        require_once($install_path . "phpmailer/class.phpmailer.php");

        $sm->assign("subject", $message_heading);
        $sm->assign("message", $message_text);

        $mailObj = new PHPMailer();
        $mailObj->CharSet = 'utf-8';
        
        $mailObj->From = 'support@arthabeauty.com';
        $mailObj->FromName = 'Arthabeauty.com';
        
        $mailObj->isHTML(true);
        $mailObj->Subject = $message_heading;
        $mailObj->Body = $message_text;
        $mailObj->AddAddress($email);

        if ($file_attachment_1) {
            $mailObj->AddAttachment($file_attachment_1);
        }

        if ($file_attachment_2) {
            $file_to_attach = $install_path . 'files/emails/' . $file_attachment_2;
            $mailObj->AddAttachment($file_to_attach, $file_attachment_2);
        }

        if ($file_attachment_3) {
            $file_to_attach = $install_path . 'files/emails/' . $file_attachment_3;
            $mailObj->AddAttachment($file_to_attach, $file_attachment_3);
        }

        return $mailObj->Send();
    }

    function setLoginRedirect($type, $value = 0) {
        unset($_SESSION["create_website"]);
        unset($_SESSION["create_wedding_planner"]);
        unset($_SESSION["create_budget"]);
        unset($_SESSION["create_table_order"]);
        unset($_SESSION["create_guest_list"]);
        unset($_SESSION["get_special_offer_voucher"]);
        if ($type == "website") {
            $_SESSION["create_website"] = 1;
        }
        if ($type == "wedding_planner") {
            $_SESSION["create_wedding_planner"] = 1;
        }
        if ($type == "budget") {
            $_SESSION["create_budget"] = 1;
        }
        if ($type == "table_order") {
            $_SESSION["create_table_order"] = 1;
        }
        if ($type == "get_special_offer_voucher") {
            $_SESSION["get_special_offer_voucher"] = $value;
        }
        if ($type == "guest_list") {
            $_SESSION["create_guest_list"] = 1;
        }
    }

    function getFacebookLastGroupContent($limit = 0) {
        global $db;
        global $sm;
        global $facebook_group_feed_table;

        if ($limit) {
            $sql_limit = "  LIMIT " . $limit;
        }

        $posts = $db->getAll("SELECT * FROM " . $facebook_group_feed_table . " ORDER BY postdate DESC {$sql_limit}");
        safeCheck($posts);
        foreach ($posts as $k => $v) {
            $url_data = explode("_", $v["post_id"]);
            $v["group_id"] = $url_data[0];
            $v["group_post_id"] = $url_data[1];
            $posts[$k] = $v;
        }

        return $posts;
    }

    function getFacebookGroupContent($options = array()) {
        global $db;
        global $sm;
        global $facebook_group_feed_table;
        global $htaccess_file;
        
        $sm->configLoad($htaccess_file);
	$htaccessVars = $sm->getConfigVars();

        $limit = isset($options['limit']) ? $options['limit'] : 10;
        $page = isset($options['page']) ? $options['page'] : 0;
        $start = $page * $limit;
        $sql_limit .= " {$start}, {$limit} ";

        $counter = $db->getRow("SELECT COUNT(id) AS cntr FROM " . $facebook_group_feed_table . " ");
        safeCheck($counter);
        $pages = ceil($counter["cntr"] / $limit);

        $url_prefix = '/facebook-group';
        for ($i = 0; $i < $pages; $i++) {
            if ($i == 0) {
                if ($i == $page) {
                    $generate_pages .= '<a href="' . $url_prefix . '" class="thispage">' . ($i + 1) . '</a>';
                } else {
                    $generate_pages .= '<a href="' . $url_prefix . '">' . ($i + 1) . '</a>';
                }
            } else {
                if ($i == $page) {
                    $generate_pages .= '<a href="' . $url_prefix . "/" . $htaccessVars["htaccess_page"] . "/" . $i . '" class="thispage">' . ($i + 1) . '</a>';
                } else {
                    $generate_pages .= '<a href="' . $url_prefix . "/" . $htaccessVars["htaccess_page"] . "/" . $i . '">' . ($i + 1) . '</a>';
                }
            }
        }
        if ($page < $pages - 1) {
            $generate_pages .= '<a href="' . $url_prefix . "/" . $htaccessVars["htaccess_page"] . "/" . ($page + 1) . '" class="next">&gt;</a>';
        }

        if ($page > 0) {
            if ($page == 1) {
                $generate_pages = '<a href="' . $url_prefix . '" class="prev">&lt;</a>' . $generate_pages;
            } else {
                $generate_pages = '<a href="' . $url_prefix . "/" . $htaccessVars["htaccess_page"] . "/" . ($page - 1) . '" class="prev">&lt;</a>' . $generate_pages;
            }
        }

        $generate_pages = $generate_pages;
        $sm->assign("generate_pages", $generate_pages);




        $posts = $db->getAll("SELECT * FROM " . $facebook_group_feed_table . " ORDER BY postdate DESC LIMIT $sql_limit ");
        safeCheck($posts);
        foreach ($posts as $k => $v) {
            $url_data = explode("_", $v["post_id"]);
            $v["group_id"] = $url_data[0];
            $v["group_post_id"] = $url_data[1];
            $posts[$k] = $v;
        }
        $sm->assign("posts", $posts);

        $sm->display("facebook-group.html");
    }

    function parseFacebookGroup() {
        global $db;
        global $facebook_group_feed_table;

        $ch = curl_init();

        curl_setopt($ch, CURLOPT_URL, 'https://graph.facebook.com/v2.10/1857191881193745/feed?fields=description,actions,full_picture,story,picture,from,id,message,created_time,link,name&access_token=107705512959170|f22f88a406567a946395302ba220dab3');

        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_TIMEOUT, 3);
        $feed_posts_content = trim(curl_exec($ch));
        curl_close($ch);
        $feed_posts = json_decode($feed_posts_content);

        foreach ($feed_posts->data as $k => $v) {
            $posts[$k]["name"] = $v->name;
            $posts[$k]["facebook_name"] = $v->from->name;
            $posts[$k]["facebook_id"] = $v->from->id;
            $posts[$k]["story"] = $v->story;
            $posts[$k]["message"] = htmlspecialchars($v->message, ENT_QUOTES);
            $posts[$k]["description"] = htmlspecialchars($v->description, ENT_QUOTES);
            $posts[$k]["thumbnail"] = $v->picture;
            $posts[$k]["full_picture"] = $v->full_picture;
            $posts[$k]["post_id"] = $v->id;
            $posts[$k]["link"] = $v->link;
            $posts[$k]["created_time"] = $v->created_time;
            $posts[$k]["created_time_timestamp"] = strtotime($v->created_time);
            $posts[$k]["postdate"] = time();
        }

        foreach ($posts as $k => $v) {
            $check = $db->getRow("SELECT * FROM " . $facebook_group_feed_table . " WHERE post_id = '" . $v["post_id"] . "'");
            safeCheck($check);
            if (!$check["id"]) {
                $fields = $v;
                if (trim($v["description"]) != '' || trim($v["link"]) != '') {
                    $res = $db->autoExecute($facebook_group_feed_table, $fields, DB_AUTOQUERY_INSERT);
                    safeCheck($res);
                }
            }
        }



        return $feed_posts;
    }
    
    public function postContact(FilteredMap $params) {
        global $db;
        global $emails_test;
        global $requests_table;
        
        $contact_subject = $params->getString("contact_subject");
        $contact_fname = $params->getString("contact_fname");
        $contact_lname = $params->getString("contact_lname");
        $contact_email = $params->getString("contact_email");
        $contact_phone = $params->getString("contact_phone");
        $contact_message = $params->getString("contact_message");
        $info_id = $params->getInt("info_id");
        
        $contact_captcha_token = $params->getString("g-recaptcha-response");
        $captchaCheck = Helpers::checkReCaptcha($contact_captcha_token);
        
        if (trim($contact_email) && trim($contact_fname) && trim($contact_lname) && $captchaCheck) {
            $message = "<br />Name: " . $contact_fname." " . $contact_lname;
            $message .= "<br />e-mail: " . $contact_email;
            if ($contact_phone){ $message .= "<br />phone: " . $contact_phone; }
            $message .= "<br />Message: " . $contact_message;
            //$message .= "<br />--------------------------------------------------<br />".phoneticConverter($message);
            //$subject = "Потребител Ви изпрати запитване - ФОРМА ЗА КОНТАКТИ";
			if ( $contact_subject ){
				$subject = $contact_subject;
			}else{
				$subject = "Потребител Ви изпрати запитване - ФОРМА ЗА КОНТАКТИ";
			}
            
            $fields = array(
                "subject" => $contact_subject,
                "fname" => $contact_fname,
                "lname" => $contact_lname,
                "email" => $contact_email,
                "phone" => $contact_phone,
                "comments" => $contact_message,
                "message" => htmlspecialchars(trim($message), ENT_QUOTES),
                "info_id" => $info_id,
                "ip_address" => $_SERVER["REMOTE_ADDR"],
                "postdate" => time(),
            );
            $res = $db->autoExecute($requests_table, $fields, DB_AUTOQUERY_INSERT); safeCheck($res);
            
            foreach ($emails_test as $v) {
                $this->mailSender($v, $subject, $message);
            }

            //$settingsObj->mailSender($row["contact_email"], $subject, $message);
            echo 1;
            die();
        } else {
            echo 0;
            die();
        }
    }

}

?>