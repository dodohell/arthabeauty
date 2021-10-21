<?php
	class UsersShop extends Settings{
		
		function getProduct($id = 0){
			global $db;
			global $shop_products_table;
			
			$row = $db->getRow("SELECT * FROM ".$shop_products_table." WHERE id = '".$id."'"); safeCheck($row);
			
			return $row;
		}
		
		function updateShopSettings($params){
			global $db;
			global $user;
			global $shop_settings_table;
			
			$row = $db->getRow("SELECT * FROM ".$shop_settings_table." WHERE user_id = '".$user["id"]."'"); safeCheck($row);
			
			$fields = array(
								"delivery_provider_id" => (int)$params["delivery_provider_id"],
								"delivery_price" => (double)$params["delivery_price"],
								"delivery_price_to_address" => (double)$params["delivery_price_to_address"],
								"delivery_free_above" => (double)$params["delivery_free_above"],
								"common_information" => htmlspecialchars(trim($params["common_information"]), ENT_QUOTES),
								"delivery_terms" => htmlspecialchars(trim($params["delivery_terms"]), ENT_QUOTES),
								"return_policy" => htmlspecialchars(trim($params["return_policy"]), ENT_QUOTES),
								"warranty" => htmlspecialchars(trim($params["warranty"]), ENT_QUOTES),
								"privacy_policy" => htmlspecialchars(trim($params["privacy_policy"]), ENT_QUOTES),
							);
			
			if ( $row["id"]  ){
				$res = $db->autoExecute($shop_settings_table, $fields, DB_AUTOQUERY_UPDATE, " user_id = '".$user["id"]."' "); safeCheck($res);
			}else{
				$fields["user_id"] = (int)$user["id"];
				$res = $db->autoExecute($shop_settings_table, $fields, DB_AUTOQUERY_INSERT); safeCheck($res);
			}
			
			
			header("Location: /myshop");
			die();
			
		}
		
		function addEditProduct($params, $act = "add"){
			global $db;
			global $lng;
			global $user;
			global $install_path;
			global $shop_products_table;
			global $shop_product_images_table;
			
			$id = (int)$params["id"];
			
			$fields = array(
				'name'	=> htmlspecialchars(trim($params['name'])),
				'option_name'	=> htmlspecialchars(trim($params['option_name'])),
				'excerpt'	=> htmlspecialchars(trim($params['excerpt'])),
				'description'	=> htmlspecialchars(trim($params['description'])),
				'price'	=> (double)$params['price'],
				'price_specialoffer'	=> (double)$params['price_specialoffer'],
				'shop_category_id'	=> (int)$params['shop_category_id'],
				'lang'	=> $lng,
				'active'	=> (int)$params['active'],
				'pos'	=> (int)$params['pos'],
				'min_quantity'	=> (int)$params['min_quantity'],
				'user_id'		=> $user["id"],
				"postdate"		=> time()
			);
			
			$act = $params["act"];
			if ( !$act ){
				$act = "add";
			}
			
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
			
			
			/*
			if($_FILES){
				$picFields = array(
					'product_id' => $id,
					'pos'	=> (int)$params['pos'],
					'user_id'		=> $user["id"],
					"postdate"		=> time()
				);

				$files = $this->reArrayFiles($_FILES['pic']);
				
				foreach($files as $file){
					$pic = copyImage($file, $install_path."files/shop/".$id."/", $install_path."files/shop/".$id."/tn/", $install_path."files/shop/".$id."/tntn/", "350x");
					if(!empty($pic)) $picFields['pic'] = $pic;
					
					$res = $db->autoExecute($shop_product_images_table,$picFields,DB_AUTOQUERY_INSERT); 
					safeCheck($res);
					
				}
		
			}*/
			
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

		function deleteProduct($params){
			global $db;
			global $shop_products_table;
			global $user;
			
			
			$id = (int)$params["id"];
			$res = $db->autoExecute($shop_products_table,array("edate" => time()),DB_AUTOQUERY_UPDATE,"id = " . $id . " AND user_id = '".$user["id"]."' ");safeCheck($res);
		}
		
		function addEditProductOption($params, $act = "add"){
			global $db;
			global $lng;
			global $user;
			global $install_path;
			global $shop_products_options_table;
			
			$id = (int)$params["id"];
			
			$fields = array(
				'name'	=> htmlspecialchars(trim($params['name'])),
				'price'	=> (double)$params['price'],
				'product_id'	=> (int)$params['product_id'],
				'pos'	=> (int)$params['pos'],
				'lang'		=> $lng,
				'user_id'		=> $user["id"],
				"postdate"		=> time()
			);
			
			$act = $params["act"];
			if ( !$act ){
				$act = "add";
			}
			
			
			if($act == "add") {
				shiftPos($db, $shop_products_options_table);
				$res = $db->autoExecute($shop_products_options_table,$fields,DB_AUTOQUERY_INSERT); safeCheck($res);
				$id = mysqli_insert_id($db->connection);
			}
			
			if($act == "add") {
				$res = $db->autoExecute($shop_products_options_table,$fields,DB_AUTOQUERY_UPDATE,"id = " . $id);safeCheck($res);
			}
			
			if($act == "edit") {
				$id = (int)$params["id"];
				$res = $db->autoExecute($shop_products_options_table,$fields,DB_AUTOQUERY_UPDATE,"id = " . $id);safeCheck($res);
			}
			
			if($act == "delete") {
				$id = (int)$params["id"];
				$res = $db->autoExecute($shop_products_options_table,array("edate" => time()),DB_AUTOQUERY_UPDATE,"id = " . $id);safeCheck($res);
			}
			
			
			
			return $id;
		}
		
		function deletePic($params){
			
			global $db;
			global $shop_product_images_table;
			
			$id = (int)$params['id'];
			$res = $db->autoExecute($shop_product_images_table, array("edate" => time()), DB_AUTOQUERY_UPDATE, " id = '".$id."' "); safeCheck($res);

			/*header("Location: /site/index.php?param=myshop-product&id=".$product_id);*/
			die();
		}
		
		function addPic($params){
			global $db;
			global $user;
			global $shop_product_images_table;
			
			$fields = array(
								"pic" => $params['file'],
								"user_id" => $user["id"],
								"product_id" => (int)$params['product_id'],
							);
			shiftPos($db, $shop_product_images_table);
			$res = $db->autoExecute($shop_product_images_table, $fields, DB_AUTOQUERY_INSERT); safeCheck($res);

			//return $res;
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
		
		function moveImage($params){
			global $db;
			global $user;
			global $shop_product_images_table;
			
			$product_id = (int)$params["product_id"];
			
			foreach($_REQUEST['items'] as $key => $values) {
				$itemData = explode("@@_@@", $values);
				$sql = "UPDATE " . $shop_product_images_table . " SET pos = " . $itemData[0] . " WHERE id = " . $itemData[1] . " AND product_id = '".$product_id."' AND  user_id = '".$user["id"]."'";
				$res = $db->Query($sql); safeCheck($res);
			}
		
		}
		
		function getProductOptions($product_id, $return_type = ""){
			global $db;
			global $user;
			global $shop_products_options_table;
			
			
			$all = $db->getAll("SELECT * FROM ".$shop_products_options_table." WHERE product_id = '".$product_id."' AND user_id = '".$user["id"]."' AND edate = 0 ORDER BY pos, id"); safeCheck($all);
			
			if ( $return_type == 3 ){
				echo json_encode($all);
			}else{
				return $all;
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
		
		function getUsersShop($options = array()){
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
		
		function getPageDashboard(){
			global $db;
			global $lng;
			global $sm;
			global $user;
			
			$settingsObj = new Settings();
			$settingsObj->checkLogin();
			
			$settings = $this->getSettings();
			$sm->assign("settings", $settings);
			
			$carts = $this->getCarts(array("supplier_id" => $user["id"]));
			$sm->assign("carts", $carts);
			
			$orders = $this->getCartProducts(array("supplier_id" => $user["id"], "carts" => $carts));
			$sm->assign("orders", $orders);
			
			$order_statuses = $this->getOrderStatuses();
			$sm->assign("order_statuses", $order_statuses);
			
			$guests = $this->getUsersShop();
			$sm->assign("guests_number", sizeof($guests));
			
			$products = $this->getProducts();
			$sm->assign("products", $products);
			
			$sm->assign("star", '<span class="star">*</span>');
			$sm->assign("page_categories", 1);
			$sm->display("my_shop_dashboard.html");
		}
		
		function getPageProducts(){
			global $db;
			global $lng;
			global $sm;
			global $user;
			
			$settingsObj = new Settings();
			$settingsObj->checkLogin();
			
			$settings = $this->getSettings();
			$sm->assign("settings", $settings);
			
			$products = $this->getProducts();
			$sm->assign("products", $products);
			
			$sm->assign("star", '<span class="star">*</span>');
			$sm->assign("page_categories", 1);
			$sm->display("my_shop_products.html");
		}
		
		
		function getSettings(){
			global $db;
			global $user;
			global $shop_settings_table;
			
			$row = $db->getRow("SELECT * FROM ".$shop_settings_table." WHERE user_id = '".$user["id"]."'"); safeCheck($row);
			
			return $row;
		}
		
		function getPageSettings(){
			global $db;
			global $lng;
			global $sm;
			global $user;
			
			$settingsObj = new Settings();
			$settingsObj->checkLogin();
			
			$delivery_providers = $this->getDeliveryProviders();
			$sm->assign("delivery_providers", $delivery_providers);
			
			$row = $this->getSettings();
			$sm->assign("row", $row);
			
			$sm->assign("star", '<span class="star">*</span>');
			$sm->assign("page_categories", 1);
			$sm->display("my_shop_settings.html");
		}
		
		function getDeliveryProviders(){
			global $db;
			global $lng;
			global $shop_delivery_providers_table;
			
			$results = $db->getAll("SELECT *, name_{$lng} AS name FROM ".$shop_delivery_providers_table." WHERE edate = 0 ORDER BY pos"); safeCheck($results);
			
			return $results;
		}
		
		function getPageCart($params){
			global $db;
			global $lng;
			global $sm;
			global $shop_carts_to_supplier_table;
			global $user;
			
			$settingsObj = new Settings();
			$settingsObj->checkLogin();
			
			$cart = $this->getCart(array("cart_id" => (int)$params["id"]));
			$sm->assign("cart", $cart);
			
			$settings = $this->getSettings();
			$sm->assign("settings", $settings);
			
			$check = $db->getRow("SELECT * FROM ".$shop_carts_to_supplier_table." WHERE supplier_id = '".$user["id"]."' AND cart_id = '".(int)$params["id"]."'"); safeCheck($check);
			$sm->assign("order_status", $check);
			
			$delivery_suppliers = $this->getDeliverySuppliers();
			$sm->assign("delivery_suppliers", $delivery_suppliers);
			
			$order_statuses = $this->getOrderStatuses();
			$sm->assign("order_statuses", $order_statuses);
			
			$sm->assign("star", '<span class="star">*</span>');
			$sm->assign("page_categories", 1);
			$sm->display("my_shop_cart.html");
		}
		
		function updateOrderStatus($params){
			global $db;
			global $user;
			global $htaccessVars;
			global $emails_shop_test;
			global $htaccess_file;
			global $shop_carts_to_supplier_table;
			global $delivery_suppliers_table;
			global $lng;
			
			$cart_id = (int)$params["id"];
			$cart = $this->getCart(array( "cart_id" => $cart_id ));
			
			$order_status_id = (int)$params["order_status_id"];
			$delivery_supplier_id = (int)$params["delivery_supplier_id"];
			$delivery_supplier_name = htmlspecialchars(trim($params["delivery_supplier_name"]), ENT_QUOTES);
			$tracking_number = htmlspecialchars(trim($params["tracking_number"]), ENT_QUOTES);
			
			$check = $db->getRow("SELECT * FROM ".$shop_carts_to_supplier_table." WHERE supplier_id = '".$user["id"]."' AND cart_id = '".$cart_id."'"); safeCheck($check);
			
			if ( $check["order_status_id"] != $order_status_id ){
				$fields =  array(
									"order_status_id" => $order_status_id, 
									"delivery_supplier_id" => $delivery_supplier_id, 
									"delivery_supplier_name" => $delivery_supplier_name, 
									"lang" => $lng, 
									"tracking_number" => $tracking_number
								);
				
				if ( $check["id"] ){
					$res = $db->autoExecute($shop_carts_to_supplier_table, $fields, DB_AUTOQUERY_UPDATE, " id = '".$check["id"]."' "); safeCheck($res);
				}else{
					$fields["cart_id"] = $cart_id;
					$fields["supplier_id"] = $user["id"];
					$res = $db->autoExecute($shop_carts_to_supplier_table, $fields, DB_AUTOQUERY_INSERT); safeCheck($res);
				}
				$order_status = $this->getOrderStatus($order_status_id);
				
				$shop = new Shop();
				$order_html = $shop->createOrderTemplateBySupplier($cart_id, $user["id"]);
				
				$settingsObj = new Settings();
				
				$supplierObj = new Users();
				$shop = $supplierObj->getRecord($user["id"]);
				
				$delivery_supplier = $db->getRow("SELECT *, name_{$lng} AS name FROM ".$delivery_suppliers_table." WHERE edate = 0 AND id = '".$fields["delivery_supplier_id"]."'"); safeCheck($delivery_supplier);
				if ( $fields["delivery_supplier_id"] == 7 ){
					$delivery_supplier["name"] = $fields["delivery_supplier_name"];
				}
				
				$replace_array = array("[order_number]", "[shop-name]", "[firstname]", "[lastname]", "[order-template]", "[delivery-supplier-name]", "[tracking_number]");
				$content_array = array($cart_id, $shop["name"], $cart["user_info"]["billing_first_name"], $cart["user_info"]["billing_family_name"], $order_html, $delivery_supplier["name"], $fields["tracking_number"]);
				
				$top_mail_template = str_replace($replace_array, $content_array, $order_status["top_mail_template"]);
				$bottom_mail_template = str_replace($replace_array, $content_array, $order_status["bottom_mail_template"]);
				$shop_email_template = str_replace($replace_array, $content_array, $order_status["shop_email"]);
				$admin_email_template = str_replace($replace_array, $content_array, $order_status["admin_email"]);
				
				$subject = str_replace($replace_array, $content_array, $order_status["subject"]);
				
				// EMAIL към потребителя
				if ( trim(strip_tags($order_status["top_mail_template"])) ){
					
					$user_email_html = $top_mail_template.$bottom_mail_template;
					$settingsObj->mailSender($cart["user_info"]["billing_email"], $subject, $user_email_html);
					foreach($emails_shop_test as $v){
						$settingsObj->mailSender($v, $subject, $user_email_html);
					}
				}
				
				// EMAIL към търговеца
				if ( trim(strip_tags($order_status["shop_email"])) ){
					$shop_email_html = $shop_email_template;
					$settingsObj->mailSender($user["email"], $subject, $shop_email_html);
					foreach($emails_shop_test as $v){
						$settingsObj->mailSender($v, $subject, $shop_email_html);
					}
				}
				
				// EMAIL към админа
				if ( trim(strip_tags($order_status["admin_email"])) ){
					$admin_email_html = $admin_email_template;
					$settingsObj->mailSender("shop@weddingday.bg", $subject, $admin_email_html);
					foreach($emails_shop_test as $v){
						$settingsObj->mailSender($v, $subject, $admin_email_html);
					}
				}
				
				
			}
			
			if ( $params["redirect_dashboard"] ){
				header("Location: " . $htaccessVars["htaccess_my_shop_orders"]);
			}else{
				header("Location: /site/index.php?param=myshop-cart&id=".$cart_id);
			}
		
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
		
		function getDeliverySuppliers(){
			global $db;
			global $lng;
			global $delivery_suppliers_table;
			
			$results = $db->getAll("SELECT *, name_{$lng} AS name FROM ".$delivery_suppliers_table." WHERE edate = 0 AND name_{$lng} <> '' ORDER BY pos "); safeCheck($results);
			
			return $results;
		}
		
		function getCarts($options = array()){
			global $db;
			global $lng;
			global $user;
			global $shop_products_table;
			global $shop_carts_table;
			global $shop_carts_products_table;
			global $shop_carts_users_table;
			global $shop_order_statuses_table;
			global $shop_carts_to_supplier_table;
			
			if ( $options["supplier_id"] ){
				$supplier_id = (int)$options["supplier_id"];
				$sql_where .= " AND supplier_id = '".$supplier_id."' ";
			}
			
			$sql = "SELECT DISTINCT cart_id FROM ".$shop_carts_products_table." WHERE edate = 0 {$sql_where} ORDER BY cart_id DESC";
			$all = $db->getAll($sql); safeCheck($all);
			foreach( $all as $k => $v ){
				$carts[$k] = $db->getRow("SELECT * FROM ".$shop_carts_table." WHERE id = '".$v["cart_id"]."' AND order_status_id != 0"); safeCheck($cart);
				
				$order_status = $db->getRow("SELECT cts.*, (SELECT os.name_{$lng} AS name FROM ".$shop_order_statuses_table." AS os WHERE os.id = cts.order_status_id) AS name 
											 FROM ".$shop_carts_to_supplier_table." AS cts 
											 WHERE cts.cart_id = '".$v["cart_id"]."' 
											 AND cts.supplier_id = '".$supplier_id."'"); safeCheck($order_status);
				
				$carts[$k]["order_status_supplier_id"] = $order_status["order_status_id"];
				$carts[$k]["order_status"] = $order_status["name"];
				
				$carts[$k]["user_info"] = $db->getRow("SELECT * FROM ".$shop_carts_users_table." WHERE cart_id = '".$v["cart_id"]."'"); safeCheck($cart);
				$carts[$k]["products"] = $this->getCartProducts(array("supplier_id" => $supplier_id, "cart_id" => $v["cart_id"]));
				$total_price = 0;
				$total_commission = 0;
				foreach( $carts[$k]["products"] as $kk => $vv ){
					$total_price += $vv["product_price_total"];
					$total_commission += $vv["commission_owed"];
				}
				$carts[$k]["total_price"] = number_format($total_price, 2, ".", "");
				$carts[$k]["total_commission"] = number_format($total_commission, 2, ".", "");
			}
			
			return $carts;
		}
		
		function getOrderStatuses(){
			global $db;
			global $lng;
			global $shop_order_statuses_table;
			
			$all = $db->getAll("SELECT *, name_{$lng} AS name, top_mail_template_{$lng} AS top_mail_template, bottom_mail_template_{$lng} AS bottom_mail_template FROM ".$shop_order_statuses_table." WHERE edate = 0 ORDER BY pos"); safeCheck($all);
			
			return $all;
		}
		
		
		function getCart($options = array()){
			global $db;
			global $lng;
			global $user;
			global $shop_products_table;
			global $shop_carts_table;
			global $shop_carts_products_table;
			global $shop_products_options_table;
			global $shop_carts_users_table;
			
			$cart = $db->getRow("SELECT * FROM ".$shop_carts_table." WHERE id = '".$options["cart_id"]."'"); safeCheck($cart);
			$cart["user_info"] = $db->getRow("SELECT * FROM ".$shop_carts_users_table." WHERE cart_id = '".$cart["id"]."'"); safeCheck($cart["user_info"]);
			
			
			$cart["products"] = $this->getCartProducts(array("supplier_id" => $user["id"], "cart_id" => $options["cart_id"]));
			$total_price = 0;
			foreach( $cart["products"] as $kk => $vv ){
				$option = $db->getRow("SELECT options.* FROM ".$shop_products_options_table." AS options WHERE options.id = '".$vv["option_id"]."' "); safeCheck($option);
				$vv["option"] = $option;
				
				$total_price += $vv["product_price_total"];
				
				$cart["products"][$kk] = $vv;
			}
			
			$status = $this->getCartStatus(array("cart_id" => $options["cart_id"], "supplier_id" => $user["id"]));
			
			$cart["delivery_price"] = $status["delivery_price"];
			$cart["total_price"] = number_format($total_price+$cart["delivery_price"], 2, ".", "");
			$cart["total_price_without_delivery"] = number_format($total_price, 2, ".", "");
			
			return $cart;
		}
		
		function getCartProducts($options = array()){
			global $db;
			global $lng;
			global $user;
			global $shop_products_table;
			global $shop_carts_products_table;
			
			if ( $options["supplier_id"] ){
				$supplier_id = (int)$options["supplier_id"];
				$sql_where .= " AND supplier_id = '".$supplier_id."' ";
			}
			if ( $options["cart_id"] ){
				$cart_id = (int)$options["cart_id"];
				$sql_where .= " AND cart_id = '".$cart_id."' ";
			}
			
			$sql = "SELECT * FROM ".$shop_carts_products_table." WHERE edate = 0 {$sql_where} ORDER BY cart_id DESC";
			$all = $db->getAll($sql); safeCheck($all);
			foreach($all as $k => $v){
				$v["product"] = $db->getRow("SELECT * FROM ".$shop_products_table." WHERE id = '".$v["product_id"]."'"); safeCheck($v["product"]);
				$all[$k] = $v;
			}
		
			return $all;
		}
		
		
		
		function getCartStatus($options = array()){
			global $db;
			global $lng;
			global $user;
			global $shop_order_statuses_table;
			global $shop_carts_to_supplier_table;
			
			if ( $options["supplier_id"] ){
				$supplier_id = (int)$options["supplier_id"];
				$sql_where .= " AND cts.supplier_id = '".$supplier_id."' ";
			}
			if ( $options["cart_id"] ){
				$cart_id = (int)$options["cart_id"];
				$sql_where .= " AND cts.cart_id = '".$cart_id."' ";
			}
			$order_status = $db->getRow("SELECT cts.*, (SELECT os.name_{$lng} AS name FROM ".$shop_order_statuses_table." AS os WHERE os.id = cts.order_status_id) AS name 
										 FROM ".$shop_carts_to_supplier_table." AS cts 
										 WHERE id = id {$sql_where}"); safeCheck($order_status);
			
			return $order_status;
		}
		
		function getProducts(){
			global $db;
			global $lng;
			global $user;
			global $shop_products_table;
			global $shop_categories_table;
			global $shop_product_images_table;
			
			$sql = "SELECT products.*,
						   (SELECT pic FROM " . $shop_product_images_table . " WHERE product_id = products.id ORDER BY pos LIMIT 1) as mainPic,
						   (SELECT categories.name_{$lng} FROM ".$shop_categories_table." AS categories WHERE categories.edate = 0 AND categories.id = products.shop_category_id) AS category_name
					FROM ".$shop_products_table." AS products
					WHERE products.edate = 0
					AND products.user_id = '".$user["id"]."'
					ORDER BY pos
						   ";

			$all = $db->getAll($sql);
			
			return $all;
		}
		
		function getPageProduct(){
			global $db;
			global $lng;
			global $sm;
			
			$settingsObj = new Settings();
			$settingsObj->checkLogin();
			
			$guests = $this->getUsersShop();
			$sm->assign("guests_number", sizeof($guests));
			
			$shop_categories = $this->getShopCategories();
			$sm->assign("shop_categories", $shop_categories);
			
			$row = $this->getProduct((int)$_REQUEST["id"]);
			$sm->assign("images", $images);
			$sm->assign("row", $row);
			
			$sm->assign("star", '<span class="star">*</span>');
			$sm->assign("page_categories", 1);
			$sm->display("my_shop_product.html");
		}
		/*
		function getProductImages($id){
			
			global $db;
			global $shop_product_images_table;
			
			$product_images = $db->getAll("SELECT * FROM ".$shop_product_images_table." WHERE edate = 0 AND product_id = " . $id . " ORDER BY pos");
			safeCheck($product_images);
			return $product_images;
		}*/
		
		function getProductImages($options = array()){
			global $db;
			global $lng;
			global $user;
			global $shop_product_images_table;
			
			$product_id = (int)$options["product_id"];
			if ( (int)$options["limit"] ){
				$sql_limit = " LIMIT ".(int)$options["limit"]." ";
			}
			
			$images = $db->getAll("SELECT * FROM ".$shop_product_images_table." WHERE edate = 0 AND product_id = '".$product_id."' AND user_id = '".$user["id"]."' ORDER BY pos {$sql_limit}"); safeCheck($images);
			
			// if ( $options["return_type"] == 3 ){
				echo json_encode($images);
			// }else{
				// return $images;
			// }
		}
		
		function getShopCategories($options = array()){
			global $db;
			global $lng;
			global $shop_categories_table;
			global $customers_to_shop_categories_table;
			
			$shop_categories = $db->getAll("SELECT *, name_{$lng} AS name FROM ".$shop_categories_table." WHERE edate = 0 AND shop_category_id = 0 AND name_{$lng} <> '' ORDER BY pos"); safeCheck($shop_categories);
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
			
			$subcategories = $db->getAll("SELECT *, name_{$lng} AS name FROM ".$shop_categories_table." WHERE edate = 0 AND shop_category_id = '".$id."'  AND name_{$lng} <> '' ORDER BY pos"); safeCheck($subcategories);
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
		
	}
	
