<?php
	class Shop extends Settings{
		
		function getProduct($id = 0){
			global $db;
			global $shop_products_table;
			
			$row = $db->getRow("SELECT * FROM ".$shop_products_table." WHERE id = '".$id."'"); safeCheck($row);
			
			$userObj = new Users();
			$supplier = $userObj->getRecord($row["user_id"]);
			
			$row["supplier"] = $supplier;
			
			return $row;
		}
		
		function getShopCategory($id = 0){
			global $db;
			global $lng;
			global $shop_categories_table;
			
			$row = $db->getRow("SELECT *, name_{$lng} AS name, description_{$lng} AS description FROM ".$shop_categories_table." WHERE id = '".$id."'"); safeCheck($row);
			
			
			return $row;
		}
		
		function imageOrientation($pic, $product_id){
			global $install_path;
			global $website;
			list($width, $height) = getimagesize($install_path.'files/shop/'.$product_id.'/'.$pic);
			
			if ($width > $height) {
				return "landscape";
			} else {
				return "portrait";
			}
		}
		
		function addEditProduct($params, $act = "add"){
			global $db;
			global $lng;
			global $user;
			global $install_path;
			global $shop_products_table;
			
			$id = (int)$params["id"];
			
			$fields = array(
				'name'	=> htmlspecialchars(trim($params['name'])),
				'excerpt'	=> htmlspecialchars(trim($params['excerpt'])),
				'description'	=> htmlspecialchars(trim($params['description'])),
				'option_name'	=> htmlspecialchars(trim($params['option_name'])),
				'price'	=> (double)$params['price'],
				'price_specialoffer'	=> (double)$params['price_specialoffer'],
				'shop_category_id'	=> (int)$params['shop_category_id'],
				'active'	=> (int)$params['active'],
				'pos'	=> (int)$params['pos'],
				'lang'		=> $lng,
				'user_id'		=> $user["id"],
				"postdate"		=> time()
			);
			
			$act = $_REQUEST["act"];
			
			if($act == "add") {
				shiftPos($db, $shop_products_table);
				$res = $db->autoExecute($shop_products_table,$fields,DB_AUTOQUERY_INSERT); safeCheck($res);
				$id = mysqli_insert_id($db->connection);
			}
			
			
			if ( !file_exists($install_path."files/shop/".$id."/") ){
				mkdir($install_path."files/shop/".$id."/");
				mkdir($install_path."files/shop/".$id."/tn/");
				mkdir($install_path."files/shop/".$id."/tntn/");
				chmod($install_path."files/shop/".$id."/", 0777);
				chmod($install_path."files/shop/".$id."/tn/", 0777);
				chmod($install_path."files/shop/".$id."/tntn/", 0777);
			}
			
			$pic = copyImage($_FILES['pic'], $install_path."files/shop/".$id."/", $install_path."files/shop/".$id."/tn/", $install_path."files/shop/".$id."/tntn/", "350x");
			if(!empty($pic)) $fields['pic'] = $pic;
			
			if($act == "add" && $pic) {
				$res = $db->autoExecute($shop_products_table,$fields,DB_AUTOQUERY_UPDATE,"id = " . $id);safeCheck($res);
			}
			
			if($act == "edit") {
				$id = (int)$params["id"];
				$res = $db->autoExecute($shop_products_table,$fields,DB_AUTOQUERY_UPDATE,"id = " . $id);safeCheck($res);
			}
			
			if($act == "delete") {
				$id = (int)$params["id"];
				$res = $db->autoExecute($shop_products_table,array("edate" => time()),DB_AUTOQUERY_UPDATE,"id = " . $id);safeCheck($res);
			}
			
			
			
			return $id;
		}
		
		
		function proceedCheckout($params){
			global $db;
			global $sm;
			global $lng;
			global $shop_carts_table;
			global $shop_products_table;
			global $shop_carts_products_table;
			global $shop_carts_users_table;
			global $shop_carts_to_supplier_table;
			global $users_table;
			global $language_file;
			global $emails_test;
			global $emails_shop_test;
			
			
			$sm->configLoad($language_file);
			$configVars = $sm->getConfigVars();
			
			$cart = $this->getCart();
			
			$fields_user = array(
									"billing_first_name" => htmlspecialchars(trim($params["billing_first_name"]), ENT_QUOTES),
									"billing_family_name" => htmlspecialchars(trim($params["billing_family_name"]), ENT_QUOTES),
									"billing_email" => htmlspecialchars(trim($params["billing_email"]), ENT_QUOTES),
									"billing_phone" => htmlspecialchars(trim($params["billing_phone"]), ENT_QUOTES),
									"billing_address" => htmlspecialchars(trim($params["billing_address"]), ENT_QUOTES),
									"billing_city" => htmlspecialchars(trim($params["billing_city"]), ENT_QUOTES),
									"billing_postcode" => htmlspecialchars(trim($params["billing_postcode"]), ENT_QUOTES),
									"billing_message" => htmlspecialchars(trim($params["billing_message"]), ENT_QUOTES),
									"delivery_first_name" => htmlspecialchars(trim($params["delivery_first_name"]), ENT_QUOTES),
									"delivery_family_name" => htmlspecialchars(trim($params["delivery_family_name"]), ENT_QUOTES),
									"delivery_email" => htmlspecialchars(trim($params["delivery_email"]), ENT_QUOTES),
									"delivery_phone" => htmlspecialchars(trim($params["delivery_phone"]), ENT_QUOTES),
									"delivery_address" => htmlspecialchars(trim($params["delivery_address"]), ENT_QUOTES),
									"delivery_city" => htmlspecialchars(trim($params["delivery_city"]), ENT_QUOTES),
									"delivery_postcode" => htmlspecialchars(trim($params["delivery_postcode"]), ENT_QUOTES),
									"cart_id" => $cart["id"],
									"agree_terms_sellers" => (int)$params["agree_terms_sellers"],
									"total_amount" => $cart["total_price"],
									"postdate" => time(),
									"lang" => $lng,
									"ip" => $_SERVER["REMOTE_ADDR"]
								);
			
			$res = $db->autoExecute($shop_carts_users_table, $fields_user, DB_AUTOQUERY_INSERT); safeCheck($res);
			$res = $db->autoExecute($shop_carts_table, array("order_status_id" => 1), DB_AUTOQUERY_UPDATE, " id = '".$cart["id"]."' "); safeCheck($res);
			
			foreach( $cart["products"] as $k => $v ){
				if ( $v["product"]["user_id"] ){
					$suppliers[] = $v["product"]["user_id"];
				}
			}
			//$suppliers[8] = 2;
			//$suppliers[5] = 3;
			$suppliers_ready = array_values(array_unique($suppliers));
			$settingsObj = new Settings();
			$subject = $configVars["supplier_order_email"];
			$supplier_order_email = $settingsObj->getFromCommon('supplier_order_email');
			//dbg($suppliers);
			//dbg($suppliers_ready);
			$shops_name = "";
			foreach( $suppliers_ready as $k => $v ){
				$supplier = $db->getRow("SELECT * FROM ".$users_table." WHERE id = '".$v."'"); safeCheck($supplier);
				//$cart_tmp = $this->getCartSuppliers($cart["id"], $v);
				$cart_tmp = $this->getCartSuppliers($cart["id"], $v);
				$check = $db->getRow("SELECT * FROM ".$shop_carts_to_supplier_table." WHERE supplier_id = '".$v."' AND cart_id = '".$cart["id"]."'"); safeCheck($check);
				if ( $check["id"] ){
					$res = $db->autoExecute($shop_carts_to_supplier_table, array("order_status_id" => 1, "supplier_id" => $v, "cart_id" => $cart["id"], "delivery_price" => $cart_tmp["results"]["carts_delivery_amount"], "total_amount" => $cart_tmp["results"]["carts_total"]), DB_AUTOQUERY_UPDATE, " id = '".$check["id"]."' "); safeCheck($res);
				}else{
					$res = $db->autoExecute($shop_carts_to_supplier_table, array("order_status_id" => 1, "supplier_id" => $v, "cart_id" => $cart["id"], "delivery_price" => $cart_tmp["results"]["carts_delivery_amount"], "total_amount" => $cart_tmp["results"]["carts_total"], "lang" => $lng), DB_AUTOQUERY_INSERT); safeCheck($res);
				}
				
				$order_status_id = 1;
				$order_status = $this->getOrderStatus($order_status_id);
				
				$order_html = $this->createOrderTemplateBySupplier($cart["id"], $user["id"]);
					
				$settingsObj = new Settings();
				
				$supplierObj = new Users();
				$shop = $supplierObj->getRecord($v);
				
				$cart["user_info"] = $db->getRow("SELECT * FROM ".$shop_carts_users_table." WHERE cart_id = '".$cart["id"]."' ORDER BY id DESC"); safeCheck($cart);
				
				$replace_array = array("[order_number]", "[shop-name]", "[firstname]", "[lastname]", "[order-template]");
				$content_array = array($cart["id"], $shop["name"], $cart["user_info"]["billing_first_name"], $cart["user_info"]["billing_family_name"], $order_html);
				
				if ( $k == 0 ){
					$shops_name .= $shop["name"];
				}else{
					$shops_name .= ", ".$shop["name"];
				}
				
				$shop_email_template = str_replace($replace_array, $content_array, $order_status["shop_email"]);
				$admin_email_template = str_replace($replace_array, $content_array, $order_status["admin_email"]);
				
				$subject = str_replace($replace_array, $content_array, $order_status["subject"]);
				
				// EMAIL към търговеца
				if ( trim(strip_tags($order_status["shop_email"])) ){
					$shop_email_html = $shop_email_template;
					$settingsObj->mailSender($shop["email"], $subject, $shop_email_html);
					foreach($emails_shop_test as $vv){
						$settingsObj->mailSender($vv, $subject, $shop_email_html);
					}
				}
				
				// EMAIL към админа
				if ( trim(strip_tags($order_status["admin_email"])) ){
					$admin_email_html = $admin_email_template;
					$settingsObj->mailSender("shop@weddingday.bg", $subject, $admin_email_html);
					foreach($emails_shop_test as $vv){
						$settingsObj->mailSender($vv, $subject, $admin_email_html);
					}
				}
					
					
				
			}
			
			
			$replace_array = array("[order_number]", "[shops-name]", "[firstname]", "[lastname]", "[order-template]");
			$content_array = array($cart["id"], $shops_name, $cart["user_info"]["billing_first_name"], $cart["user_info"]["billing_family_name"], $order_html);
			
			$top_mail_template = str_replace($replace_array, $content_array, $order_status["top_mail_template"]);
			$bottom_mail_template = str_replace($replace_array, $content_array, $order_status["bottom_mail_template"]);
			
			$subject = str_replace($replace_array, $content_array, $order_status["subject"]);
			
			
			// EMAIL към потребителя
			if ( trim(strip_tags($order_status["top_mail_template"])) ){
				$user_email_html = $top_mail_template.$bottom_mail_template;
				$settingsObj->mailSender($cart["user_info"]["billing_email"], $subject, $user_email_html);
				foreach($emails_shop_test as $vv){
					$settingsObj->mailSender($vv, $subject, $user_email_html);
				}
			}
			
			
			
			
			unset($_SESSION["cart_id"]);
			header("Location: /shop-thank-you-message");
			die();
		}
		
		function getOrderStatus($order_status_id = 0){
			global $db;
			global $lng;
			global $shop_order_statuses_table;
			
			$row = $db->getRow("SELECT *, 
										name_{$lng} AS name, 
										subject_{$lng} AS subject, 
										shop_email_{$lng} AS shop_email, 
										admin_email_{$lng} AS admin_email, 
										top_mail_template_{$lng} AS top_mail_template, 
										bottom_mail_template_{$lng} AS bottom_mail_template 
								FROM ".$shop_order_statuses_table." 
								WHERE edate = 0 
								AND id = '".$order_status_id."'"); safeCheck($row);
			
			return $row;
		}
		
		function deletePic($id){
			global $db;
			global $shop_products_table;
			
			$res = $db->autoExecute($shop_products_table, array("pic" => ""), DB_AUTOQUERY_UPDATE, " id = '".$id."' "); safeCheck($res);
			
			header("Location: /site/index.php?param=myshop-product&id=".$id);
			die();
		}
		
		function moveProduct($params){
			global $db;
			global $user;
			global $users_guests_table;
			
			$website_id = (int)$_SESSION["userWebsite"];
			
			foreach($params['items'] as $key => $values) {
				$itemData = explode("@@_@@", $values);
				$sql = "UPDATE " . $users_guests_table . " SET pos = " . $itemData[0] . " WHERE id = " . $itemData[1] . " AND  user_id = '".$user["id"]."'";
				$res = $db->Query($sql); safeCheck($res);
			}
		}
		
		
		function getUserGuests(){
			global $db;
			global $users_guests_table;
			global $users_guests_groups_table;
			global $users_guests_to_users_guests_groups_table;
			global $user;
			
			$users_guests = $db->getAll("SELECT * FROM ".$users_guests_table." WHERE edate = 0 AND user_id = '".$user["id"]."' ORDER BY pos"); safeCheck($users_guests);
			$users_groups = $this->getUserGroups();
			foreach($users_guests as $k => $v){
				$users_groups_tmp = $users_groups;
				if ( $v["accommodation_id"] ){
					$accommodation = Website::getAccommodation($v["accommodation_id"]);
				}
				
				$v["accommodation_name"] = $accommodation["name"];
				$v["accommodation_price"] = $accommodation["price_double_room"]*$v["accommodation_duration"];
				$v["notes"] = nl2br($v["notes"]);
				
				$v["partner"] = "";
				if ( $v["partner_id"] ){
					$partner = $this->getRecord($v["partner_id"]);
					$v["partner"] = $partner["name"];
				}
				
				$users_groups_selected = $db->getAll("SELECT * , count(*) as count FROM ".$users_guests_to_users_guests_groups_table." WHERE user_guest_id = '".$v["id"]."' AND user_id = '".$user["id"]."'"); safeCheck($user_groups);
				$users_groups_selected_edit = array();
				foreach( $users_groups_tmp as $kk => $vv ){
					foreach($users_groups_selected as $kkk => $vvv){
						if ( $vvv["user_guest_group_id"] == $vv["id"] ){
							$vv["selected"] = "checked";
							$users_groups_selected_edit[] = $vv;
						}
					}
					$users_groups_tmp[$kk] = $vv;
				}
				$v["users_groups_selected"] = $users_groups_selected_edit;
				$v["users_groups"] = $users_groups_tmp;
				$users_guests[$k] = $v;
			}
			
			return $users_guests;
			
		}
		
		function getUsersGroups(){
			global $db;
			global $users_guests_groups_table;
			global $user;
			
			$users_groups = $db->getAll("SELECT * FROM ".$users_guests_groups_table." WHERE edate = 0 AND user_id = '".$user["id"]."' ORDER BY pos"); safeCheck($users_groups);
			
			return $users_groups;
		}
		
		function getUserGuestsByUsersGroup($options = array()){
			global $db;
			global $sm;
			global $users_guests_table;
			global $users_guests_groups_table;
			global $users_guests_to_users_guests_groups_table;
			global $user;
			
			$sql_where = "";
			if ( isset($options["seated"]) ){
				$sql_where .= " AND users_guests.seated = '".$options['seated']."' ";
			}
			
			$users_groups = $this->getUsersGroups();

			foreach($users_groups as $k => $v){
				$sql = "SELECT users_guests.*
						 FROM ".$users_guests_table." AS users_guests,
							  ".$users_guests_to_users_guests_groups_table." AS ugtugg
						 WHERE users_guests.edate = 0
						 AND users_guests.id = ugtugg.user_guest_id
						 AND ugtugg.user_guest_group_id = '".$v["id"]."'
						 AND users_guests.user_id = '".$user["id"]."'
						 {$sql_where}
						 ORDER BY users_guests.id, users_guests.name
						 ";
				$users_guests = $db->getAll($sql); safeCheck($users_guests);
				foreach($users_guests as $kk => $vv){
					$used_guests[] = $vv["id"];
				}
				$v["users_guests"] = $users_guests;
				$users_groups[$k] = $v;
			}
			$used_guests = array_unique($used_guests);
			$used_guests_sql = implode(",", $used_guests);
			
			if ( $used_guests > 0 ){
				if ( $used_guests == 1 ){
					$sql_where .= "AND users_guests.id = '".$used_guests[0]."'";
				}else{
					$sql_where .= "AND users_guests.id NOT IN (".$used_guests_sql.")";
				}
			}
			
			$sql = "SELECT users_guests.*
					FROM ".$users_guests_table." AS users_guests
					WHERE users_guests.edate = 0
					AND users_guests.user_id = '".$user["id"]."'
					{$sql_where}
					ORDER BY users_guests.id, users_guests.name
					";
			$no_groups = $db->getAll($sql); safeCheck($no_groups);
			
			$current_element = sizeof($users_groups);
			if ( sizeof($no_groups) ){
				$users_groups[$current_element]["id"] = "0";
				$users_groups[$current_element]["name"] = "";
				$users_groups[$current_element]["user_id"] = $user["id"];
				$users_groups[$current_element]["pos"] = 0;
				$users_groups[$current_element]["edate"] = 0;
				$users_groups[$current_element]["users_guests"] = $no_groups;
			}
			if ( $options["return_type"] == 1 ){
				echo json_encode($users_groups);
			}elseif ( $options["return_type"] == 3 ){
				$sm->assign("table_id", (int)$options["table_id"]);
				$sm->assign("pos", (int)$options["pos"]);
				$sm->assign("users_groups", $users_groups);
				$sm->display("table-order-guests.html");
			}else{
				return $users_groups;
			}
			
		}
		
		
		function getRecord($id = 0){
			global $db;
			global $user;
			global $lng;
			global $users_guests_table;
			
			$id = (int)$id;
			
			$row = $db->getRow("SELECT * FROM ".$users_guests_table." WHERE id = '".$id."' AND user_id = '".$user["id"]."'"); safeCheck($row);
			
			return $row;
		}
		
		function checkUser($email){
			global $db;
			global $lng;
			global $users_guests_table;
			
			$row = $db->getRow("SELECT * FROM ".$users_guests_table." WHERE email = '".$email."'"); safeCheck($row);
			
			return $row;
		}
		
		function registerUser($params){
			global $db;
			global $sm;
			global $lng;
			global $user;
			global $users_guests_table;
			
			$name = htmlspecialchars(trim($params["name"]),ENT_QUOTES);
			$email = htmlspecialchars(trim($params["email"]),ENT_QUOTES);
			$password = htmlspecialchars(trim($params["password"]),ENT_QUOTES);
			$password_confirm = htmlspecialchars(trim($params["password_confirm"]),ENT_QUOTES);
			$phone = htmlspecialchars(trim($params["phone"]),ENT_QUOTES);
			$bulletin = (int)$params["bulletin"];
			$agree_terms = (int)$params["agree_terms"];
			
			if ( $name && $email && $password && $password_confirm && $phone ){
				$check = $this->checkUser($email);
				if( !$check["id"] ){
					$fields = array(
										"name" => $name,
										"email" => $email,
										"password" => md5($password),
										"phone" => $phone,
										"bulletin" => $bulletin,
										"agree_terms" => $agree_terms,
										"user_id" => $user["id"],
										"register_ip" => $_SERVER["REMOTE_ADDR"],
										"register_date" => time(),
										"active" => 1
									);
					$res = $db->autoExecute($users_guests_table, $fields, DB_AUTOQUERY_INSERT); safeCheck($res);
					header("Location: /messages/200");
					die();
				}else{
					header("Location: /messages/300");
					die();
				}
			}
			
		}
		
		function loginUser($params){
			global $db;
			global $lng;
			global $users_guests_table;
			
			$row = $db->getRow("SELECT * FROM ".$users_guests_table." WHERE email = '".$params["email"]."' AND password = '".md5($params["password"])."'"); safeCheck($row);
			
			if ( $row["id"] ){
				$_SESSION["user"] = $row;
			}
			
			echo json_encode($_SESSION["user"]);
		}
		
		
		function logoutUser(){
			unset($_SESSION["user"]);
			header("Location: /");
			die();
		}
		
		function getShop($options = array()){
			global $db;
			global $lng;
			global $user;
			global $users_guests_table;
			
			$sql_where = ""; 
			
			if ( $options["customer_id"] ){
				$sql_where = " AND customer_id = '".$options["customer_id"]."' ";
			}
			
			$users_guests = $db->getAll("SELECT *
								  FROM ".$users_guests_table." 
								  WHERE edate = 0 
								  AND user_id = '".$user["id"]."'
								  {$sql_where}
								  ORDER BY id, pos"); safeCheck($users_guests);
			
			return $users_guests;
		}
		
		function getPageIndex(){
			global $db;
			global $lng;
			global $sm;
			global $install_path;
			global $shop_categories_table;
			
			$settingsObj = new Settings();
			//$settingsObj->checkLogin();
			
			$cart_suppliers = $this->getCartSuppliers();
			$sm->assign("carts_results", $cart_suppliers["results"]);
			
			$shop_categories = $this->getShopCategories();
			$sm->assign("shop_categories", $shop_categories);
            
			/*старото*/
            $page = (int)$_REQUEST["page"];

            $results = $this->getProducts(array("limit" => 12, "with_pages" => 1, "index_page" => 1, "page" => $page));
            foreach($results["products"] as $k => $v){
                $v["image_orientation"] = $this->imageOrientation($v["mainPic"], $v["id"]);
                $results["products"][$k] = $v;
            }
            $sm->assign("products", $results["products"]);
            $sm->assign("pages", $results["pages"]);  
            
            /*новото*/
          
           $homepage_shop_categories = $db->getAll("SELECT *, name_{$lng} AS name FROM ".$shop_categories_table." WHERE edate = 0 AND homepage = 1 AND active = 'checked' AND name_{$lng} <> '' ORDER BY pos"); safeCheck($homepage_shop_categories);
        
           
        /**/
            
			$sm->assign("homepage_shop_categories", $homepage_shop_categories);
			$sm->assign("star", '<span class="star">*</span>');
			$sm->assign("page_categories", 1);
			$sm->display("shop.html");
		}
		
		function getPageCategory($options = array()){
			global $db;
			global $lng;
			global $sm;
			
			$settingsObj = new Settings();
			//$settingsObj->checkLogin();
			
			$page = (int)$options["page"];
			
			$shop_category_id = (int)$options["shop_category_id"];
			
			$row = $this->getShopCategory($shop_category_id);
			$sm->assign("row", $row);
			
			if ( $page && $lng == "bg" ){
				$meta_addon = " - стр. ".$page;
			}
			
			if ( $row["meta_title"] ){
				$sm->assign("infoTitle", $row['meta_title'].$meta_addon);
			}else{
				$sm->assign("infoTitle", $row['name'].$meta_addon);
			}
			if ( $row["meta_description"] ){
				$sm->assign("infoDescr", $row['meta_description'].$meta_addon);
			}else{
				if ( $row["meta_title"] ){
					$sm->assign("infoDescr", $row['meta_title'].$meta_addon);
				}else{
					$sm->assign("infoDescr", $row['name'].$meta_addon);
				}
			}
			
			if ( $row["meta_keywords"] ){
				$sm->assign("infoKeys", $row['meta_keywords']);
			}else{
				$sm->assign("infoKeys", $row['name']);
			}
			
			$cart_suppliers = $this->getCartSuppliers();
			$sm->assign("carts_results", $cart_suppliers["results"]);
			
			$shop_categories = $this->getShopCategories();
			$sm->assign("shop_categories", $shop_categories);
			
			$breadcrumbs = $this->generateBreadcrumbs($shop_category_id);
			$sm->assign("breadcrumbs", $breadcrumbs);
			
			$results = $this->getProducts(array("limit" => 12, "with_pages" => 1, "id" => $shop_category_id, "page" => $page));
			foreach($results["products"] as $k => $v){
				$v["image_orientation"] = $this->imageOrientation($v["mainPic"], $v["id"]);
				$results["products"][$k] = $v;
			}
			$sm->assign("products", $results["products"]);
			$sm->assign("pages", $results["pages"]);
			
			$sm->assign("star", '<span class="star">*</span>');
			$sm->assign("page_categories", 1);
			$sm->display("shop.html");
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
			global $shop_categories_table;
			global $left;
			
			$sm->configLoad($language_file);
			$configVars = $sm->getConfigVars();
			$sm->configLoad($htaccess_file);
			$htaccessVars = $sm->getConfigVars();
			
			$row = $db->getRow("SELECT id, shop_category_id, name_{$lng} AS name, url_{$lng} AS url, url_target AS target, htaccess_url_{$lng} AS htaccess_url FROM ".$shop_categories_table." WHERE id = '".$id."'"); safeCheck($row);
			$row['link_title'] = str_replace($link_find, $link_repl, $row['name']);
			
			$htaccess_prefix = $htaccessVars["htaccess_shop_category"];
			if ( $row["url"] ){
				$tmp_breadcrumbs = '<span class="single"><a href="'.$row["url"].'" target="'.$row["target"].'">'.$row["name"].'</a></span>'.$tmp_breadcrumbs;
			}elseif( $row["htaccess_url"] ){
				$tmp_breadcrumbs = '<span class="single"><a href="'.$row["htaccess_url"].'" target="'.$row["target"].'">'.$row["name"].'</a></span>'.$tmp_breadcrumbs;
			}else{
				$tmp_breadcrumbs = '<span class="single"><a href="'.$htaccess_prefix.'/'.$row["id"].'" target="'.$row["target"].'">'.$row["name"].'</a></span>'.$tmp_breadcrumbs;
			}
			if ($row["shop_category_id"] != 0){
				return self::generateBreadcrumbs($row["shop_category_id"], $tmp_breadcrumbs);
			}else{
				return '<span class="single"><a href="'.$host.'">'.$configVars["home_breadcrumbs"].'</a></span>'.$tmp_breadcrumbs;
			}
		}
		
		function getProducts($options = array()){
			global $db;
			global $sm;
			global $lng;
			global $user;
			global $htaccess_file;
			global $shop_products_table;
			global $shop_categories_table;
			global $shop_product_images_table;
			
			if ( $options["limit"] ){
				$page = (int)$options["page"];
				$start = $page * $options["limit"];
				$limit = (int)$options["limit"];
				$sql_limit = " LIMIT {$start}, {$limit}";
			}
			
			if ( $options["id"] ){
				$id = (int)$options["id"];
				$children = $db->getAll("SELECT * FROM ".$shop_categories_table." WHERE edate = 0 AND shop_category_id = '".$id."'"); safeCheck($children);
				if ( sizeof($children) > 0 ){
					$sql_where .= "  AND ( products.shop_category_id = '".$id."'  ";
					foreach($children as $k => $v){
						$sql_where .= "  OR products.shop_category_id = '".$v["id"]."'  ";
					}
					$sql_where .= "  ) ";
				}else{
					$sql_where .= "  AND products.shop_category_id = '".$id."'  ";
				}
			}
			
			if ( $options["user_id"] ){
				$sql_where .= " AND products.user_id = '".$user["id"]."'";
			}
			
			/* Generate Pages Listings  */
			$counter = $db->getRow("SELECT COUNT(products.id) AS cntr FROM ".$shop_products_table." AS products	{$sql_where_from} WHERE products.edate = 0 AND products.active = '1' AND products.lang = '{$lng}' <> '' {$sql_where}"); safeCheck($counter);
			
			$limit = $options["limit"];
			$page = $options["page"];
			$start = $limit * $page;
			$pages = ceil($counter["cntr"]/$limit);
			$generate_pages = "";
			
			$sm->configLoad($htaccess_file);
			$htaccessVars = $sm->getConfigVars();
			
			$url_page = '/'.$htaccessVars["htaccess_page"].'/';
			if ( $options["index_page"] ){
				$url_prefix = $host.$htaccessVars["htaccess_shop"];
			}else{
				if ( $category["htaccess_url"] ){
					$url_prefix = $category["htaccess_url"];
				}else{
					$url_prefix = $host.$htaccessVars["htaccess_shop_category"]."/".$id;
				}
			}
			
			
			
			for ( $i = 0 ; $i < $pages; $i++ ){
				$selected = '';
				if ( $i == $page ){
					$selected = ' class="thispage"';
				}
				if ( $i == 0 ){
					$generate_pages .= '<a href="'.$url_prefix.'"'.$selected.'>'.($i+1).'</a>';
				}else{
					$generate_pages .= '<a href="'.$url_prefix.$url_page.$i.'"'.$selected.'>'.($i+1).'</a>';
				}
			}
			
			if ( $page > 0 ){
				if ( $page == 1 ){
					$generate_pages = '<a href="'.$url_prefix.'" class="prev">&lt;</a>'.$generate_pages;
					
				}else{
					$generate_pages = '<a href="'.$url_prefix.$url_page.($page-1).'" class="prev">&lt;</a>'.$generate_pages;
				}
			}
			
			if ( $page < $pages-1 ){
				$generate_pages .= '<a href="'.$url_prefix.$url_page.($page+1).'" class="next">&gt;</a>';
			}
			
			
			$sql = "SELECT products.*,
						   (SELECT pic FROM " . $shop_product_images_table . " WHERE product_id = products.id ORDER BY pos LIMIT 1) as mainPic,
						   (SELECT categories.name_{$lng} FROM ".$shop_categories_table." AS categories WHERE categories.edate = 0 AND categories.id = products.shop_category_id) AS category_name
					FROM ".$shop_products_table." AS products
					WHERE products.edate = 0
					AND products.active = '1'
					AND products.lang = '{$lng}'
					{$sql_where}
					ORDER BY id DESC
					{$sql_limit}";
			
			$all = $db->getAll($sql);
			
			if ( $options["with_pages"] ){
				$results["products"] = $all;
				$results["pages"] = $generate_pages;
			}else{
				$results = $all;
			}
			
			return $results;
		}
		
		function getProductOptions($product_id, $return_type = ""){
			global $db;
			global $shop_products_options_table;
			
			
			$all = $db->getAll("SELECT * FROM ".$shop_products_options_table." WHERE product_id = '".$product_id."' AND edate = 0 ORDER BY pos, id"); safeCheck($all);
			
			if ( $return_type == 3 ){
				echo json_encode($all);
			}else{
				return $all;
			}
			
		}
		
		function getPageProduct(){
			global $db;
			global $lng;
			global $sm;
			global $shop_product_images_table;
			
			$settingsObj = new Settings();
			//$settingsObj->checkLogin();
			
			$id = (int)$_REQUEST["id"];
			
			$cart_suppliers = $this->getCartSuppliers();
			$sm->assign("carts_results", $cart_suppliers["results"]);
			
			$guests = $this->getShop();
			$sm->assign("guests_number", sizeof($guests));
			
			$shop_categories = $this->getShopCategories();
			$sm->assign("shop_categories", $shop_categories);
			
			$shop_images = $db->getAll("SELECT * FROM ".$shop_product_images_table." WHERE product_id = '".$id."' AND edate = 0 ORDER BY pos, id"); safeCheck($all);
			$sm->assign("shop_images", $shop_images);
			
			$row = $this->getProduct($id);
			$sm->assign("row", $row);
			
			if ( $row["meta_title"] ){
				$sm->assign("infoTitle", $row['meta_title'].$meta_addon);
			}else{
				$sm->assign("infoTitle", $row['name'].$meta_addon);
			}
			if ( $row["meta_description"] ){
				$sm->assign("infoDescr", $row['meta_description'].$meta_addon);
			}else{
				if ( $row["meta_title"] ){
					$sm->assign("infoDescr", $row['meta_title'].$meta_addon);
				}else{
					$sm->assign("infoDescr", $row['name'].$meta_addon);
				}
			}
			
			if ( $row["meta_keywords"] ){
				$sm->assign("infoKeys", $row['meta_keywords']);
			}else{
				$sm->assign("infoKeys", $row['name']);
			}
			
			
			$breadcrumbs = $this->generateBreadcrumbs($row["shop_category_id"]);
			$sm->assign("breadcrumbs", $breadcrumbs);
			
			$options = $this->getProductOptions($id);
			$sm->assign("options", $options);
			
			$sm->assign("star", '<span class="star">*</span>');
			$sm->assign("page_categories", 1);
			$sm->display("shop_product.html");
		}
		
		function getPageCart(){
			global $db;
			global $lng;
			global $sm;
			
			$settingsObj = new Settings();
			//$settingsObj->checkLogin();
			
			$cart_suppliers = $this->getCartSuppliers();
			
			$sm->assign("cart_products", $cart["products"]);
			$sm->assign("cart_suppliers", $cart_suppliers["carts"]);
			$sm->assign("cart_suppliers_count", sizeof($cart_suppliers["carts"]));
			$sm->assign("carts_results", $cart_suppliers["results"]);
			
			//dbg($cart_suppliers["results"]);
			
			$shop_categories = $this->getShopCategories();
			$sm->assign("shop_categories", $shop_categories);
			
			$sm->assign("star", '<span class="star">*</span>');
			$sm->assign("page_categories", 1);
			$sm->display("shop_cart.html");
		}
		
		function getPageCheckout(){
			global $db;
			global $lng;
			global $sm;
			
			$settingsObj = new Settings();
			//$settingsObj->checkLogin();
			
			$cart_suppliers = $this->getCartSuppliers();
			$sm->assign("carts_results", $cart_suppliers["results"]);
			
			$cart = $this->getCart();
			
			$sm->assign("cart_products", $cart["products"]);
			$sm->assign("cart", $cart);
			
			$shop_categories = $this->getShopCategories();
			$sm->assign("shop_categories", $shop_categories);
			
			$sm->assign("star", '<span class="star">*</span>');
			$sm->assign("page_categories", 1);
			$sm->display("shop_checkout.html");
		}
		
		
		function getShopCategories($options = array()){
			global $db;
			global $lng;
			global $shop_categories_table;
			global $customers_to_shop_categories_table;
			
			$shop_categories = $db->getAll("SELECT *, name_{$lng} AS name FROM ".$shop_categories_table." WHERE edate = 0 AND shop_category_id = 0 AND active = 'checked' AND name_{$lng} <> '' ORDER BY pos"); safeCheck($shop_categories);
			if ( $options["selected"] && $options["customer_id"] ){
				$shop_categoriesSelected = $db->getAll("SELECT * FROM ".$customers_to_shop_categories_table." WHERE customer_id = '".$options["customer_id"]."'"); safeCheck($shop_categoriesSelected);
			}
			foreach($shop_categories as $k => $v){
				$v["subcategories"] = self::getSubcategories($v["id"], 1, $options);
				foreach($shop_categoriesSelected as $kk => $vv){
					if ( $vv["shop_category_id"] == $v["id"] ){
						$v["selected"] = "checked";
					}
				}
				
				$shop_categories[$k] = $v;
			}
			return $shop_categories;
		}
		
		function getSubcategories($id, $level = 0, $options = array()){
			global $db;
			global $lng;
			global $shop_categories_table;
			global $customers_to_shop_categories_table;
			
			$subcategories = $db->getAll("SELECT *, name_{$lng} AS name FROM ".$shop_categories_table." WHERE edate = 0 AND shop_category_id = '".$id."' AND active = 'checked' AND name_{$lng} <> '' ORDER BY pos"); safeCheck($subcategories);
			if ( $options["selected"] && $options["customer_id"] ){
				$shop_categoriesSelected = $db->getAll("SELECT * FROM ".$customers_to_shop_categories_table." WHERE customer_id = '".$options["customer_id"]."'"); safeCheck($shop_categoriesSelected);
			}
			foreach($subcategories as $k => $v){
				$v["subcategories"] = self::getSubcategories($v["id"], $level+1, $options);
				$v["level"] = $level;
				foreach($shop_categoriesSelected as $kk => $vv){
					if ( $vv["shop_category_id"] == $v["id"] ){
						$v["selected"] = "checked";
					}
				}
				$subcategories[$k] = $v;
			}
			
			return $subcategories;
		}
		
		function updateDeliverySettings($params){
			global $db;
			global $sm;
			global $shop_carts_table;
			global $htaccess_file;
			
			$sm->configLoad($htaccess_file);
			$htaccessVars = $sm->getConfigVars();
			
			$fields = array( "delivery_to" => (int)$params["delivery_to"], "delivery_to_set" => 1 );
			
			if ($_SESSION["cart_id"]){
				$cart_id = (int)$_SESSION["cart_id"];
				$sql_cart_select = " id = '".$cart_id."' ";
			}else{
				$sql_cart_select = " id = '0' ";
				
			}
			
			
			$cart = $db->getRow("SELECT * FROM ".$shop_carts_table." WHERE ".$sql_cart_select); safeCheck($cart);
			$cart_id = $cart["id"];
			
			$res = $db->autoExecute($shop_carts_table, $fields, DB_AUTOQUERY_UPDATE, " id = '".$cart_id."' "); safeCheck($res);
			
			header("Location: ".$htaccessVars["htaccess_shop_cart"]);
			die();
		}
		
		function addToCart($params){
			global $db;
			global $sm;
			global $lng;
			global $shop_carts_table;
			global $htaccess_file;
			
			$sm->configLoad($htaccess_file);
			$htaccessVars = $sm->getConfigVars();
			
			if ($_SESSION["cart_id"]){
				$cart_id = (int)$_SESSION["cart_id"];
				$sql_cart_select = " id = '".$cart_id."' ";
			}else{
				$sql_cart_select = " id = '0' ";
				
			}
			
			$cart = $db->getRow("SELECT * FROM ".$shop_carts_table." WHERE ".$sql_cart_select); safeCheck($cart);
			$cart_id = $cart["id"];
			
			$id = (int)$params["id"];
			
			if ($id){
				if (!$cart_id){
					$res = $db->autoExecute($shop_carts_table, array("postdate" => time(), "status" => "0", "session_id" => session_id(), "ip" => $_SERVER["REMOTE_ADDR"], "user_id" => $_SESSION["userID"], "delivery_to" => 1, "lang" => $lng), DB_AUTOQUERY_INSERT ); safeCheck($res);
					$cart_id = mysqli_insert_id($db->connection);
					$this->addProductToCart($id,$cart_id,$_REQUEST, $_REQUEST["quantity"]);
					
					$_SESSION["cart_id"] = $cart_id;
				}else{
					$this->addProductToCart($id,$cart_id,$_REQUEST);
				}
				header("Location: ".$htaccessVars["htaccess_shop_cart"]);
				die();
			}
			
			
		}
		
		function addProductToCart($product_id, $cart_id, $params, $quantity = 1 ){
			global $db;
			global $lng;
			global $shop_products_table;
			global $shop_products_options_table;
			global $shop_carts_products_table;
			
			$option_id = (int)$params["option_id"];
			if ( $option_id ){
				$sql_option = " AND option_id = '".$option_id."' ";
				$option = $db->getRow("SELECT * FROM ".$shop_products_options_table." WHERE edate = 0 AND id = '".$option_id."'"); safeCheck($option);
			}
			
			$product = $db->getRow("SELECT * FROM ".$shop_products_table." WHERE id = '".$product_id."'"); safeCHeck($product);
			
			$userObj = new Users();
			$supplier_details = $userObj->getRecord($product["user_id"]);
			
			
			$check_product = $db->getRow("SELECT quantity FROM ".$shop_carts_products_table." WHERE product_id = '".$product_id."' {$sql_option} AND cart_id = '".$cart_id."' AND edate = 0"); safeCheck($check_product);
			
			if ($product["price_specialoffer"] > 0.0){
				$price_use = $product["price_specialoffer"];
			}else{
				$price_use = $product["price"];
			}
			
			if ( $option["price"] > 0.0 ){
				$price_use = $option["price"];
			}
			
			$quantity = (int)$params["quantity"];
			
			if ( !$quantity ){
				$quantity = 1;
			}
			
			$fields = array(
								"product_id" => $product_id,
								"option_id" => $option_id,
								"cart_id" => $cart_id,
								"quantity" => $quantity,
								"lang" => $lng,
								"postdate" => time(),
								"ip_address" => $_SERVER["REMOTE_ADDR"],
								"supplier_id" => $product["user_id"],
								"product_price" => $price_use,
								"product_price_total" => $price_use * $quantity,
								"commission" => $supplier_details["commission"],
								"commission_owed" => ($price_use * $quantity * $supplier_details["commission"])/100,
							);
			
			if ($check_product["quantity"] == 0){
				$res = $db->autoExecute($shop_carts_products_table, $fields, DB_AUTOQUERY_INSERT ); safeCheck($res);
			}else{
				$fields["quantity"] = $quantity+$check_product["quantity"];
				$fields["product_price_total"] = $fields["product_price"] * $fields["quantity"];
				$fields["commission"] = $supplier_details["commission"];
				$fields["commission_owed"] = ($fields["product_price_total"] * $supplier_details["commission"])/100;
				$res = $db->autoExecute($shop_carts_products_table, $fields, DB_AUTOQUERY_UPDATE, " cart_id = '".$cart_id."' AND product_id = '".$product_id."' {$sql_option} " ); safeCheck($res);
			}
		}
		
		function checkAndAddProductToCart($product_id, $cart_id, $params, $entered = 0){
			global $db;
			global $shop_carts_products_table;
			
			$option_id = (int)$params["option_id"];
			if ( $option_id ){
				$sql_option = " AND option_id = '".$option_id."' ";
			}
			
			
			$check_product = $db->getAll("SELECT * FROM ".$shop_carts_products_table." WHERE product_id = '".$product_id."' {$sql_option} AND cart_id = '".$cart_id."' AND edate = 0"); safeCheck($check_product);
			
			if (sizeof($check_product)){
				foreach($check_product as $k => $v ){
					$res = $db->autoExecute($shop_carts_products_table, array("quantity" => $v["quantity"]+1), DB_AUTOQUERY_UPDATE, " id = '".$v["id"]."' " ); safeCheck($res);
				}
			}else{
				addProductToCart($product_id,$cart_id,$params, $params["quantity"]);
			}
			
			
		}
		
		
		function updateQuantity($params){
			global $db;
			global $sm;
			global $htaccess_file;
			global $shop_products_table;
			global $shop_carts_products_table;
			
			$sm->configLoad($htaccess_file);
			$htaccessVars = $sm->getConfigVars();
			
			$product_cart_id = (int)$params["product_id"];
			$quantity = (int)$params["quantity"];
			if ( $quantity > 0 ){
				$row = $db->getRow("SELECT * FROM ".$shop_carts_products_table." WHERE id = '".$product_cart_id."'"); safeCheck($row);
				$product = $db->getRow("SELECT * FROM ".$shop_products_table." WHERE id = '".$row["product_id"]."'"); safeCHeck($product);
				
				$userObj = new Users();
				$supplier_details = $userObj->getRecord($product["user_id"]);
				
				$res = $db->autoExecute($shop_carts_products_table, array("quantity" => $quantity, "product_price_total" => $row["product_price"] * $quantity, "commission" => $supplier_details["commission"], "commission_owed" => ($row["product_price"] * $quantity * $supplier_details["commission"])/ 100),DB_AUTOQUERY_UPDATE, " id = '".$product_cart_id."' "); safeCheck($res);
			}
			
			header("Location: ".$htaccessVars["htaccess_shop_cart"]);
			die();
		}
		
		function deleteProductFromCart($params){
			global $db;
			global $sm;
			global $shop_carts_products_table;
			global $htaccess_file;
			
			$sm->configLoad($htaccess_file);
			$htaccessVars = $sm->getConfigVars();
			
			
			$remove_id = (int)$_REQUEST["id"];
			
			
			$res = $db->autoExecute($shop_carts_products_table, array("edate" => time()), DB_AUTOQUERY_UPDATE, "id = '".$remove_id."'");
			$cart = $this->getCart();
			$_SESSION["cart"] = $cart;
			
			header("Location: ".$htaccessVars["htaccess_shop_cart"]);
			die();
		}
		
		function getCart($cart_id_preset = 0, $supplier_id = 0){
			global $db;
			global $lng;
			global $shop_carts_table;
			global $shop_carts_products_table;
			global $shop_products_table;
			
			if ( $cart_id_preset ){
				$cart_id = $cart_id_preset;
			}else{
				$cart_id = (int)$_SESSION["cart_id"];
			}
			
			$cart = $db->getRow("SELECT * FROM ".$shop_carts_table." WHERE id = '".$cart_id."'"); safeCheck($cart);
			
			$cart_products = $db->getAll("SELECT * FROM ".$shop_carts_products_table." WHERE edate = 0 AND cart_id = '".$cart_id."'"); safeCheck($cart_products);
			$total_price = 0;
			$total_price_delivery_products = 0;
			$storage_cart = 0;
			$delivery_cart = 0;
			foreach( $cart_products as $k => $v ){
				$product = $db->getRow("SELECT products.* FROM ".$shop_products_table." AS products WHERE products.id = '".$v["product_id"]."' "); safeCheck($product);
				$v["product"] = $product;
				
				$total_price += $v["product_price"] * $v["quantity"];
				
				$cart_products[$k] = $v;
			}
			
			
			$cart["total_price_delivery_products"] = $total_price_delivery_products;
			$cart["storage_cart"] = $storage_cart;
			$cart["delivery_cart"] = $delivery_cart;
			$cart["products"] = $cart_products;
			$cart["total_price"] = number_format($total_price, 2, ".", "");
			$cart["total_price_with_delivery"] = number_format($total_price + $cart["delivery_amount"], 2, ".", "");
			//$cart["delivery_amount_show"] = calculateDelivery($cart["id"]);
			//dbg($cart);
			//dbg($cart);
			return $cart;
		}
		
		function getCartSuppliers($order_id = 0, $supplier_id = 0){
			global $db;
			global $lng;
			global $users_table;
			global $shop_carts_table;
			global $shop_carts_products_table;
			global $shop_products_table;
			global $shop_product_images_table;
			global $shop_products_options_table;
			
			if ( $cart_id_preset ){
				$cart_id = $cart_id_preset;
			}else{
				$cart_id = (int)$_SESSION["cart_id"];
			}
			
			if ( $order_id ){
				$cart_id = $order_id;
			}
			
			if ( $supplier_id ){
				$sql_supplier = " AND supplier_id = '".$supplier_id."' ";
			}
			
			$cart_global = $db->getRow("SELECT * FROM ".$shop_carts_table." WHERE id = '".$cart_id."'"); safeCheck($cart_global);
			
			$carts_total = 0;
			$cart_products_number = 0;
			$carts_delivery_amount = 0;
			$carts_total_with_delivery = 0;
			$shop_special_delivery = 0;
			
			$carts = $db->getAll("SELECT DISTINCT supplier_id FROM ".$shop_carts_products_table." WHERE cart_id = '".$cart_id."' {$sql_supplier} AND edate = 0 "); safeCheck($carts);
			foreach( $carts as $k => $v ){
				$supplier = $db->getRow("SELECT * FROM ".$users_table." WHERE edate = 0 AND id = '".$v["supplier_id"]."'"); safeCheck($supplier);
				$v["supplier"] = $supplier;
				$v["supplier_settings"] = $this->getSettings($v["supplier_id"]);
				
				$cart_products = $db->getAll("SELECT * FROM ".$shop_carts_products_table." WHERE edate = 0 AND cart_id = '".$cart_id."' AND supplier_id = '".$v["supplier_id"]."'"); safeCheck($cart_products);
				
				$total_price = 0;
				$total_price_delivery_products = 0;
				$storage_cart = 0;
				$delivery_cart = 0;
				foreach( $cart_products as $kk => $vv ){
					$product = $db->getRow("SELECT products.*, (SELECT pic FROM " . $shop_product_images_table . " WHERE product_id = products.id ORDER BY pos LIMIT 1) as pic FROM ".$shop_products_table." AS products WHERE products.id = '".$vv["product_id"]."' "); safeCheck($product);
					$vv["product"] = $product;
					
					$option = $db->getRow("SELECT options.* FROM ".$shop_products_options_table." AS options WHERE options.id = '".$vv["option_id"]."' "); safeCheck($option);
					$vv["option"] = $option;
					
					$total_price += $vv["product_price"] * $vv["quantity"];
					$cart_products_number += $vv["quantity"];
					$cart_products[$kk] = $vv;
				}
				
				if ( $v["supplier_settings"]["delivery_free_above"] <= $total_price  && $v["supplier_settings"]["delivery_free_above"] > 0.0 ){
					$v["delivery_amount"] = 0;
				}else{
					if ( !$v["supplier_settings"]["shop_special_delivery"] ){
						if ( $cart_global["delivery_to"] == 1 ){
							$v["delivery_amount"] = $v["supplier_settings"]["delivery_price_to_address"];
						}else{
							$v["delivery_amount"] = $v["supplier_settings"]["delivery_price"];
						}
					}
				}
				$carts_total += $total_price;
				$carts_delivery_amount += $v["delivery_amount"];
				$carts_total_with_delivery += ($v["delivery_amount"] + $total_price);
				$v["storage_cart"] = $storage_cart;
				$v["delivery_cart"] = $delivery_cart;
				if ( $v["supplier_settings"]["shop_special_delivery"] ){
					$v["shop_special_delivery"] = 1;
					$shop_special_delivery = 1;
					$cart_global["shop_special_delivery"] = 1;
				}
				$v["total_price"] = number_format($total_price, 2, ".", "");
				$v["total_price_with_delivery"] = number_format($total_price + $v["delivery_amount"], 2, ".", "");
				$v["products"] = $cart_products;
				$carts[$k] = $v;
			}
			$results["carts"] = $carts;
			$results["results"]["carts_total"] = number_format($carts_total, 2, ".", "");
			$results["results"]["cart_products_number"] = $cart_products_number;
			$results["results"]["carts_delivery_amount"] = number_format($carts_delivery_amount, 2, ".", "");
			$results["results"]["carts_total_with_delivery"] = number_format($carts_total_with_delivery, 2, ".", "");
			
			$results["results"]["shop_special_delivery"] = $shop_special_delivery;
			$results["results"]["delivery_to_set"] = $cart_global["delivery_to_set"];
			$results["results"]["delivery_to"] = $cart_global["delivery_to"];
			$results["results"]["carts_total_with_delivery"] = number_format($carts_total_with_delivery, 2, ".", "");
			
			
			
			return $results;
		}
		
		function getPageSupplier($id, $tag = ""){
			global $db;
			global $sm;
			
			$userObj = new Users();
			$shopUser = $userObj->getRecord($id);
			$sm->assign("shopUser", $shopUser);
			
			$cart_suppliers = $this->getCartSuppliers();
			$sm->assign("carts_results", $cart_suppliers["results"]);
			
			
			$row = $this->getSettings($id);
			$sm->assign("row", $row);
			$sm->assign("tag", $tag);
			
			$sm->assign("page_supplier", 1);
			$sm->assign("page_categories", 1);
			$sm->display("shop_supplier.html");
		}
		
		function getSettings($user_id){
			global $db;
			global $shop_settings_table;
			global $users_table;
			
			$row = $db->getRow("SELECT * FROM ".$shop_settings_table." WHERE user_id = '".$user_id."'"); safeCheck($row);
			$user = $db->getRow("SELECT * FROM ".$users_table." WHERE id = '".$user_id."'"); safeCheck($row);
			$row["shop_special_delivery"] = $user["shop_special_delivery"];
			
			return $row;
		}
		
		function createOrderTemplate($order_id){
			global $db;
			global $sm;
			global $lng;
			global $language_file;
			global $shop_carts_table;
			global $shop_carts_products_table;
			global $shop_carts_users_table;
			global $shop_products_table;
			
			$sm->configLoad($language_file);
			$configVars = $sm->getConfigVars();
			
			$cart_suppliers = $this->getCartSuppliers($order_id);
			
			$cart = $this->getCart($order_id);
			
			$order_id = (int)$order_id;
			$row = $db->getRow("SELECT * FROM ".$shop_carts_table." WHERE id = '".$order_id."' "); safeCheck($row);
			$carts_products = $db->getAll("SELECT * FROM ".$shop_carts_products_table." WHERE edate = 0 AND cart_id = '".$order_id."' "); safeCheck($carts_products);
			foreach($carts_products as $k => $v){
				$product = $db->getRow("SELECT * FROM ".$shop_products_table." WHERE edate = 0 AND id = '".$v["product_id"]."'"); safeCheck($product);
				
				$v["product"] = $product;
				$carts_products[$k] = $v;
			}
			
			$user_info = $db->getRow("SELECT * FROM ".$shop_carts_users_table." WHERE cart_id = '".$order_id."' "); safeCheck($user_info);
			
			$row["order_number"] = date("Y", $cart["postdate"]).str_pad($cart["id"], 4, "0", STR_PAD_LEFT);
			
			$message = "";
			$message = '
				<h3>Поръчка номер №'.$row["order_number"].'</h3>
			
				<table width="100%"><tr>
					<td width="40%">
					<h3>Информация за плащане</h3>
					'.$user_info["billing_first_name"].' '.$user_info["billing_family_name"].'<br />
					'.$user_info["billing_phone"].'<br />
					'.$user_info["billing_email"].'<br />
					'.$user_info["billing_address"].'<br />
					'.$user_info["billing_postcode"].' '.$user_info["billing_city"].' <br />
					</td>
					</tr>
					</table>
					<hr />
					<br />
				';
				
				foreach($cart_suppliers["carts"] as $k => $v){
					$products_tmp = $v["products"];
					$message .= '<h3>'.$configVars["order_to_user"].' '.$v["supplier"]["name"].'</h3>';
					$message .= '<table border="1" width="100%">';
					foreach( $products_tmp as $kk => $vv ){
						$message .='<tr>
										<td width="50%">
												<strong>'.$vv["product"]["name"].'</strong><br />
												';
						
											$message.= '</td><td width="15%" valign="top">'.$vv["product_price"].'  '.$configVars["currency"].'</td>
											<td width="5%" valign="top">'.$vv["quantity"].'</td>
											<td width="15%" valign="top" align="right">'.number_format($vv["product_price_total"], 2, ".", "").'  '.$configVars["currency"].'</td>
									</tr>';
					}
					if ( $v["supplier_settings"]["shop_special_delivery"] ){
						$message .= '<tr>
							<td>
								'.$configVars["shop_cart_delivery_special"].'
							</td>
							<td>
								&nbsp;
							</td>
							<td>
								&nbsp;
							</td>
							<td align="right">
								'.$configVars["shop_cart_delivery_special_amount"].'
							</td>
						</tr>';
					}else{
						$message .= '<tr>
							<td>
								'.$configVars["total_price_delivery"].'
							</td>
							<td>
								'.number_format($v["delivery_amount"], 2, ".", "").' '.$configVars["currency"].'
							</td>
							<td>
								1
							</td>
							<td align="right">
								'.number_format($v["delivery_amount"], 2, ".", "").' '.$configVars["currency"].'
							</td>
						</tr>';
					}
						$message .='
						<tr>
							<td align="right" colspan="3">
								'.$configVars["total_price"].'
							</td>
							<td align="right">
								'.number_format($v["total_price_with_delivery"], 2, ".", "").' '.$configVars["currency"].'
							</td>
						</tr>
						</table>';
				}
			
			
			$message .='
							<center><h3>'.$configVars["your_order"].'</h3></center>
								<table border="1" width="50%" align="center">
									<tr>
										<td align="right" colspan="3">
											'.$configVars["total_price_products"].'
										</td>
										<td align="right">
											'.number_format($cart_suppliers["results"]["carts_total"], 2, ".", "").' '.$configVars["currency"].'
										</td>
									</tr>';
			if ( $cart_suppliers["results"]["shop_special_delivery"] ){
				$message .= '<tr>
										<td align="right" colspan="3">
											'.$configVars["total_price_delivery"].' '.$configVars["plus_shop_special_delivery"].'
										</td>
										<td align="right">
											'.$configVars["plus_shop_special_delivery_amount"].'
										</td>
									</tr>';
			}else{
				$message .= '<tr>
										<td align="right" colspan="3">
											'.$configVars["total_price_delivery"].'
										</td>
										<td align="right">
											'.number_format($cart_suppliers["results"]["carts_delivery_amount"], 2, ".", "").' '.$configVars["currency"].'
										</td>
									</tr>';
			}
									
			$message .= '<tr>
										<td align="right" colspan="3">
											'.$configVars["total_price_products_and_delivery"].'
										</td>
										<td align="right">
											'.number_format($cart_suppliers["results"]["carts_total_with_delivery"], 2, ".", "").' '.$configVars["currency"].'
										</td>
									</tr>
								</table>';
				
			return $message;
		}
		
		function createOrderTemplateBySupplier($order_id, $supplier_id){
			global $db;
			global $sm;
			global $lng;
			global $language_file;
			global $shop_carts_table;
			global $shop_carts_products_table;
			global $shop_carts_users_table;
			global $shop_products_table;
			
			$sm->configLoad($language_file);
			$configVars = $sm->getConfigVars();
			
			$cart_suppliers = $this->getCartSuppliers($order_id, $supplier_id);
			
			$cart = $this->getCart($order_id);
			
			$order_id = (int)$order_id;
			$row = $db->getRow("SELECT * FROM ".$shop_carts_table." WHERE id = '".$order_id."' "); safeCheck($row);
			$carts_products = $db->getAll("SELECT * FROM ".$shop_carts_products_table." WHERE edate = 0 AND cart_id = '".$order_id."' "); safeCheck($carts_products);
			foreach($carts_products as $k => $v){
				$product = $db->getRow("SELECT * FROM ".$shop_products_table." WHERE edate = 0 AND id = '".$v["product_id"]."'"); safeCheck($product);
				
				$v["product"] = $product;
				$carts_products[$k] = $v;
			}
			
			$user_info = $db->getRow("SELECT * FROM ".$shop_carts_users_table." WHERE cart_id = '".$order_id."' "); safeCheck($user_info);
			
			$row["order_number"] = date("Y", $cart["postdate"]).str_pad($cart["id"], 4, "0", STR_PAD_LEFT);
			
			$message = "";
			$message = '
				<h3>Поръчка номер №'.$row["order_number"].'</h3>
			
				<table width="100%"><tr>
					<td width="40%">
					<h3>Информация за плащане</h3>
					'.$user_info["billing_first_name"].' '.$user_info["billing_family_name"].'<br />
					'.$user_info["billing_phone"].'<br />
					'.$user_info["billing_email"].'<br />
					'.$user_info["billing_address"].'<br />
					'.$user_info["billing_postcode"].' '.$user_info["billing_city"].' <br />
					</td>
					</tr>
					</table>
					<hr />
					<br />
				';
				
				foreach($cart_suppliers["carts"] as $k => $v){
					$products_tmp = $v["products"];
					$message .= '<h3>'.$configVars["order_to_user"].' '.$v["supplier"]["name"].'</h3>';
					$message .= '<table border="1" width="100%">';
					foreach( $products_tmp as $kk => $vv ){
						$message .='<tr>
										<td width="50%">
												<strong>'.$vv["product"]["name"].'</strong><br />
												';
						
											$message.= '</td><td width="15%" valign="top">'.$vv["product_price"].'  '.$configVars["currency"].'</td>
											<td width="5%" valign="top">'.$vv["quantity"].'</td>
											<td width="15%" valign="top" align="right">'.number_format($vv["product_price_total"], 2, ".", "").'  '.$configVars["currency"].'</td>
									</tr>';
					}
						$message .= '<tr>
							<td>
								'.$configVars["total_price_delivery"].'
							</td>
							<td>
								'.number_format($v["delivery_amount"], 2, ".", "").' '.$configVars["currency"].'
							</td>
							<td>
								1
							</td>
							<td align="right">
								'.number_format($v["delivery_amount"], 2, ".", "").' '.$configVars["currency"].'
							</td>
						</tr>';
						$message .='
						<tr>
							<td align="right" colspan="3">
								'.$configVars["total_price"].'
							</td>
							<td align="right">
								'.number_format($v["total_price_with_delivery"], 2, ".", "").' '.$configVars["currency"].'
							</td>
						</tr>
						</table>';
				}
			
			
			$message .='
							<center><h3>'.$configVars["your_order"].'</h3></center>
								<table border="1" width="50%" align="center">
									<tr>
										<td align="right" colspan="3">
											'.$configVars["total_price_products"].'
										</td>
										<td align="right">
											'.number_format($cart_suppliers["results"]["carts_total"], 2, ".", "").' '.$configVars["currency"].'
										</td>
									</tr>
									<tr>
										<td align="right" colspan="3">
											'.$configVars["total_price_delivery"].'
										</td>
										<td align="right">
											'.number_format($cart_suppliers["results"]["carts_delivery_amount"], 2, ".", "").' '.$configVars["currency"].'
										</td>
									</tr>
									<tr>
										<td align="right" colspan="3">
											'.$configVars["total_price_products_and_delivery"].'
										</td>
										<td align="right">
											'.number_format($cart_suppliers["results"]["carts_total_with_delivery"], 2, ".", "").' '.$configVars["currency"].'
										</td>
									</tr>
								</table>';
				
			return $message;
		}
		
		function cartCalculateTotal($id){
			global $db;
			global $lng;
			global $shop_options_table;
			global $shop_carts_table;
			global $shop_carts_users_table;
			global $shop_carts_products_table;
			global $shop_products_table;
			
			
			$cart = $db->getRow("SELECT * FROM ".$shop_carts_table." WHERE id = '".$id."'"); safeCheck($cart);
			$cart_products = $db->getAll("SELECT * FROM ".$shop_carts_products_table." WHERE cart_id = '".$id."' AND edate = 0"); safeCheck($cart_products);
			$total_weight = 0;
			foreach($cart_products as $k => $v){
				$tmp_total_amount = 0;
				$tmp_total_weight = 0;
				$tmp_total_amount = $v["quantity"] * $v["product_price"];
				$tmp_total_weight = $v["quantity"] * $v["product_weight"];
				
				
				$total_amount += $tmp_total_amount;
				$total_weight += $tmp_total_weight;
				$cart_products[$k] = $v;
			}
			$cart["weight"] = $total_weight;
			
			$res = $db->autoExecute($shop_carts_table, array("total_amount" => $total_amount, "delivery_price" => $delivery_price_use), DB_AUTOQUERY_UPDATE, " id = '".$id."' "); safeCheck($res);
			$res = $db->autoExecute($shop_carts_users_table, array("total_amount" => $total_amount), DB_AUTOQUERY_UPDATE, " id = '".$user_info["id"]."' "); safeCheck($res);
			
			
			$total_amount_with_delivery = $total_amount + $delivery_price_use;
			
			return $total_amount_with_delivery;
		}
		
	}
	
