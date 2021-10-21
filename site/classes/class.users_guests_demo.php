<?php
	class UsersGuestsDemo extends Settings{
		
		
		function addEditUserGroup($params, $act = "add"){
			global $db;
			global $users_guests_groups_demo_table;
			
			$id = (int)$params["id"];
			$fields = array(
				'name'	=> htmlspecialchars(trim($params['name'])),
				'session_id'	=> session_id(),
				'user_id'		=> $_SESSION["user"]["id"],
				"postdate"		=> time()
			);
			
			if($act == "add") {
				shiftPos($db, $users_guests_groups_demo_table);
				$res = $db->autoExecute($users_guests_groups_demo_table,$fields,DB_AUTOQUERY_INSERT); safeCheck($res);
				$id = mysqli_insert_id($db->connection);
			}
			
			if($act == "edit") {
				$id = (int)$params["id"];
				$res = $db->autoExecute($users_guests_groups_demo_table,$fields,DB_AUTOQUERY_UPDATE,"id = " . $id);safeCheck($res);
			}
			
			if($act == "delete") {
				$id = (int)$params["id"];
				
				$res = $db->autoExecute($users_guests_groups_demo_table,array("edate" => time()),DB_AUTOQUERY_UPDATE,"id = " . $id);safeCheck($res);
			}
			return $id;
		}
		
		function getUserGroups(){
			global $db;
			global $user;
			global $users_guests_demo_table;
			global $users_guests_groups_demo_table;
			global $users_guests_to_users_guests_groups_demo_table;
			
			$user_groups = $db->getAll("SELECT users_guests_groups.*,
											   (SELECT COUNT(ugtugg.id) FROM ".$users_guests_to_users_guests_groups_demo_table." AS ugtugg WHERE ugtugg.user_guest_group_id = users_guests_groups.id AND (SELECT ug.edate FROM ".$users_guests_demo_table." AS ug WHERE ug.id = ugtugg.user_guest_id) = 0 ) AS countMembers
										FROM ".$users_guests_groups_demo_table." AS users_guests_groups 
										WHERE users_guests_groups.edate = 0 
										AND users_guests_groups.session_id = '".session_id()."'  
										ORDER BY name"); safeCheck($user_groups);
/*
			$user_groups = $db->getAll("SELECT
                                                                    COUNT(*) AS countMembers, b.name, b.id
                                                                FROM
                                                                    $users_guests_to_users_guests_groups_demo_table AS a
                                                                   LEFT JOIN $users_guests_groups_demo_table AS b
                                                                       ON (`a`.`user_guest_group_id` = `b`.`id`)
                                                                       WHERE b.user_id ='".$user["id"]."'
                                                                       AND b.edate = 0
                                                                GROUP BY b.name ,b.id "); safeCheck($user_groups);*/
			
			return $user_groups;
			
		}
		
		function addEditUserGuest($params, $act = "add"){
			global $db;
			global $user;
			global $users_guests_demo_table;
			global $users_guests_to_users_guests_groups_demo_table;
			
			$id = (int)$params["id"];
			$partner_id = (int)$params["partner_id"];
			$fields = array(
				'name'	=> htmlspecialchars(trim($params['name'])),
				'facebook_id'	=> htmlspecialchars(trim($params['facebook_id'])),
				'user_type'	=> (int)$params["user_type"],
				'partner_id'	=> $partner_id,
				'email'	=> htmlspecialchars(trim($params['email'])),
				'food_menu'	=> htmlspecialchars(trim($params['food_menu'])),
				'address'	=> htmlspecialchars(trim($params['address'])),
				'session_id'		=> session_id(),
				'user_id'		=> $user["id"],
				"postdate"		=> time()
			);
			
			if ( $params["participate"] == 1 ){
				$fields["confirmed"] = 1;
				$fields["rejected"] = 0;
				//$fields["confirmed_partner"] = $params["confirmed_partner"];
				$fields["confirmed_partner"] = "";
			}
			if ( $params["participate"] == 2 ){
				$fields["rejected"] = 1;
				$fields["confirmed"] = 1;
				$fields["confirmed_partner"] = "";
			}
			
			if($act == "add") {
				shiftPos($db, $users_guests_demo_table);
				$res = $db->autoExecute($users_guests_demo_table,$fields,DB_AUTOQUERY_INSERT); safeCheck($res);
				$id = mysqli_insert_id($db->connection);
			}
			
			if($act == "edit") {
				$id = (int)$params["id"];
				$res = $db->autoExecute($users_guests_demo_table,$fields,DB_AUTOQUERY_UPDATE,"id = " . $id);safeCheck($res);
			}
			
			
			if($act == "delete") {
				$id = (int)$params["id"];
				
				$res = $db->autoExecute($users_guests_demo_table,array("edate" => time()),DB_AUTOQUERY_UPDATE,"id = " . $id);safeCheck($res);
			}else{
				
				$user_groups = $params["user_groups"];
				$res = $db->Query("DELETE FROM ".$users_guests_to_users_guests_groups_demo_table." WHERE user_guest_id = '".$id."' AND user_id = '".$user["id"]."' "); safeCheck($res);
				foreach($user_groups as $k => $v){
					$fields_tmp = array(
										"user_guest_id" => $id,
										"user_guest_group_id" => (int)$v,
										"user_id" => $user["id"],
										'session_id'		=> session_id()
									);
					$res = $db->autoExecute($users_guests_to_users_guests_groups_demo_table, $fields_tmp, DB_AUTOQUERY_INSERT); safeCheck($res);
				}
				
			}
			
			if ( $params["confirmed_partner"] && $params["confirmed_partner"] != 'null' ){
				$fields_user = array(
										'name' => $params["confirmed_partner"],
										'partner_id'	=> $id,
										'participate'	=> $params["participate"],
										'email'	=> htmlspecialchars(trim($params['email'])),
										'food_menu'	=> htmlspecialchars(trim($params['food_menu'])),
										'address'	=> htmlspecialchars(trim($params['address'])),
										'user_groups'	=> $params["user_groups"],
										'session_id'		=> session_id()
									);
				$this->addEditUserGuest($fields_user);
			}
			
			
			
			return $id;
		}
		
		function moveUserGuest($params){
			global $db;
			global $user;
			global $users_guests_demo_table;
			
			$website_id = (int)$_SESSION["userWebsite"];
			
			foreach($params['items'] as $key => $values) {
				$itemData = explode("@@_@@", $values);
				$sql = "UPDATE " . $users_guests_demo_table . " SET pos = " . $itemData[0] . " WHERE id = " . $itemData[1] . " AND  user_id = '".$user["id"]."'";
				$res = $db->Query($sql); safeCheck($res);
			}
		}
		
		
		function getUserGuests(){
			global $db;
			global $users_guests_demo_table;
			global $users_guests_groups_demo_table;
			global $users_guests_to_users_guests_groups_demo_table;
			global $user;
			
			$users_guests = $db->getAll("SELECT * FROM ".$users_guests_demo_table." WHERE edate = 0 AND session_id = '".session_id()."' ORDER BY pos"); safeCheck($users_guests);
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
				
				$users_groups_selected = $db->getAll("SELECT * , count(*) as count FROM ".$users_guests_to_users_guests_groups_demo_table." WHERE user_guest_id = '".$v["id"]."' AND session_id = '".session_id()."'"); safeCheck($user_groups);
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
			global $users_guests_groups_demo_table;
			global $user;
			
			$users_groups = $db->getAll("SELECT * FROM ".$users_guests_groups_demo_table." WHERE edate = 0 AND session_id = '".session_id()."' ORDER BY pos"); safeCheck($users_groups);
			
			return $users_groups;
		}
		
		function getUserGuestsByUsersGroup($options = array()){
			global $db;
			global $sm;
			global $users_guests_demo_table;
			global $users_guests_groups_demo_table;
			global $users_guests_to_users_guests_groups_demo_table;
			global $user;
			
			$sql_where = "";
			if ( isset($options["seated"]) ){
				$sql_where .= " AND users_guests.seated = '".$options['seated']."' ";
			}
			
			$users_groups = $this->getUsersGroups();

			foreach($users_groups as $k => $v){
				$sql = "SELECT users_guests.*
						 FROM ".$users_guests_demo_table." AS users_guests,
							  ".$users_guests_to_users_guests_groups_demo_table." AS ugtugg
						 WHERE users_guests.edate = 0
						 AND users_guests.id = ugtugg.user_guest_id
						 AND ugtugg.user_guest_group_id = '".$v["id"]."'
						 AND users_guests.session_id = '".session_id()."'
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
					FROM ".$users_guests_demo_table." AS users_guests
					WHERE users_guests.edate = 0
					AND users_guests.session_id = '".session_id()."'
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
			global $users_guests_demo_table;
			
			$id = (int)$id;
			
			$row = $db->getRow("SELECT * FROM ".$users_guests_demo_table." WHERE id = '".$id."' AND session_id = '".session_id()."'"); safeCheck($row);
			
			return $row;
		}
		
		function checkUser($email){
			global $db;
			global $lng;
			global $users_guests_demo_table;
			
			$row = $db->getRow("SELECT * FROM ".$users_guests_demo_table." WHERE email = '".$email."'"); safeCheck($row);
			
			return $row;
		}
		
		function registerUser($params){
			global $db;
			global $sm;
			global $lng;
			global $user;
			global $users_guests_demo_table;
			
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
										"session_id" => session_id(),
										"register_ip" => $_SERVER["REMOTE_ADDR"],
										"register_date" => time(),
										"active" => 1
									);
					$res = $db->autoExecute($users_guests_demo_table, $fields, DB_AUTOQUERY_INSERT); safeCheck($res);
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
			global $users_guests_demo_table;
			
			$row = $db->getRow("SELECT * FROM ".$users_guests_demo_table." WHERE email = '".$params["email"]."' AND password = '".md5($params["password"])."'"); safeCheck($row);
			
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
		
		function getUsersGuests($options = array()){
			global $db;
			global $lng;
			global $user;
			global $users_guests_demo_table;
			
			$sql_where = ""; 
			
			if ( $options["customer_id"] ){
				$sql_where = " AND customer_id = '".$options["customer_id"]."' ";
			}
			
			$users_guests = $db->getAll("SELECT *
								  FROM ".$users_guests_demo_table." 
								  WHERE edate = 0 
								  AND session_id = '".session_id()."'
								  {$sql_where}
								  ORDER BY id, pos"); safeCheck($users_guests);
			
			return $users_guests;
		}
		
		function getPage(){
			global $db;
			global $lng;
			global $sm;
			
			
			$guests = $this->getUsersGuests();
			$sm->assign("guests_number", sizeof($guests));
			
			$websiteObj = new Website();
			$website = $websiteObj->getWebsiteID();
			$sm->assign("website", $website);
			
			$food_menu_tmp = explode("\n", $website["wedding_food_menu"]);
			foreach( $food_menu_tmp as $k => $v ){
				if ( trim($v) ){
					$food_menu[] = trim($v);
				}
			}
			$sm->assign("food_menu", $food_menu);
			
			
			$sm->assign("star", '<span class="star">*</span>');
			$sm->assign("page_categories", 1);
			$sm->display("guest-list-demo.html");
		}
		
		function getPageBeforeLogin(){
			global $db;
			global $lng;
			global $sm;
			global $user;
			
			$settingsObj = new Settings();
			$settingsObj->setLoginRedirect("guest_list");
			
			
			$sm->assign("page_categories", 1);
			$sm->display("guest-list-before-login.html");	
		}
		
		function getPageSmall(){
			global $db;
			global $lng;
			global $sm;
			
			
			$sm->assign("star", '<span class="star">*</span>');
			$sm->assign("page_categories", 1);
			$sm->display("guest-list-small-demo.html");
		}
		
		function getPagePrint(){
			global $db;
			global $lng;
			global $sm;
			
			$users_groups = $this->getUserGuestsByUsersGroup();
			$sm->assign("users_groups", $users_groups);
			
			$sm->assign("star", '<span class="star">*</span>');
			$sm->assign("page_categories", 1);
			$sm->display("guest-list-print.html");
		}
		
		function getPagePrintPdf(){
			global $db;
			global $lng;
			global $sm;
			
			$users_groups = $this->getUserGuestsByUsersGroup();
			$sm->assign("users_groups", $users_groups);
			
			$sm->assign("star", '<span class="star">*</span>');
			$sm->assign("page_categories", 1);
			
			ob_start();
			$sm->display("guest-list-print.html");	
			$html = ob_get_clean();
			
			$mpdf=new mPDF('utf-8', 'A4-L'); 
			$mpdf->useAdobeCJK = true;		
			$mpdf->SetAutoFont(AUTOFONT_ALL);	
			$mpdf->SetHTMLHeader($site_title["description"]);
			$mpdf->WriteHTML($html);
			if ( file_exists($install_path."files-export/".$user["id"]."/") === false ){
				mkdir($install_path."files-export/".$user["id"]."/");
				chmod($install_path."files-export/".$user["id"]."/", 0777);
			}
			$download_file = "guest-list.pdf";
			//unlink($download_file);
			$mpdf->Output($download_file, 'D'); 
			//$mpdf->Output(); 
		}
		
		
		
		function getAjaxPage(){
			global $db;
			global $lng;
			global $sm;
			
			$type = htmlspecialchars(trim($_REQUEST["type"]),ENT_QUOTES);
			if ( $type == "postUserGroup" ){
				$name = htmlspecialchars(trim($_REQUEST["type"]),ENT_QUOTES);
				$return_id = $this->addEditUserGroup($_REQUEST);
				echo $return_id;
			}
			if ( $type == "editUserGroup" ){
				$name = htmlspecialchars(trim($_REQUEST["type"]),ENT_QUOTES);
				$return_id = $this->addEditUserGroup($_REQUEST, "edit");
				echo $return_id;
			}
			if ( $type == "deleteUserGroup" ){
				$return_id = $this->addEditUserGroup($_REQUEST, "delete");
				echo $return_id;
			}
			if ( $type == "getUserGroups" ){
				$user_groups = $this->getUserGroups();
				echo json_encode($user_groups);
			}
			
			if ( $type == "postUserGuest" ){
				$name = htmlspecialchars(trim($_REQUEST["type"]),ENT_QUOTES);
				$return_id = $this->addEditUserGuest($_REQUEST);
				echo $return_id;
			}
			if ( $type == "editUsersGuest" ){
				$name = htmlspecialchars(trim($_REQUEST["type"]),ENT_QUOTES);
				$return_id = $this->addEditUserGuest($_REQUEST, "edit");
				echo $return_id;
			}
			if ( $type == "deleteUsersGuest" ){
				$return_id = $this->addEditUserGuest($_REQUEST, "delete");
				echo $return_id;
			}
			if ( $type == "getUsersGuests" ){
				$user_guests = $this->getUserGuests();
				echo json_encode($user_guests);
			}
		}
		
		function getStats(){
			$guests = $this->getUsersGuests();
		
			$results["confirmed"] = 0;
			$results["men"] = 0;
			$results["women"] = 0;
			$results["boys"] = 0;
			$results["girls"] = 0;
			foreach($guests as $k => $v){
				if ( $v["confirmed"] ){
					$results["confirmed"]++ ;
				}
				if ( $v["user_type"] == 1 || $v["user_type"] == 6 ){
					$results["men"]++;
				}
				if ( $v["user_type"] == 2 || $v["user_type"] == 5 ){
					$results["women"]++;
				}
				if ( $v["user_type"] == 3){
					$results["boys"]++;
				}
				if ( $v["user_type"] == 4){
					$results["girls"]++;
				}
			}
			$results["allguests"] = sizeof($guests);
			
			echo json_encode($results);
		}
		
		
		
		function copyDemoToUser($user_id, $session_id){
			global $db;
			global $lng;
			global $sm;
			global $users_guests_demo_table;
			global $users_guests_groups_demo_table;
			global $users_guests_to_users_guests_groups_demo_table;
			global $users_guests_table;
			global $users_guests_groups_table;
			global $users_guests_to_users_guests_groups_table;
			
			if ( $session_id && $user_id ){
				$users_groups = $db->getAll("SELECT * FROM ".$users_guests_groups_demo_table." WHERE session_id = '".$session_id."'"); safeCheck($users_groups);
				foreach($users_groups as $k => $v){
					$fields = array(
										"name" => $v["name"],
										"user_id" => $user_id,
										"old_id" => $v["id"],
										"postdate" => $v["postdate"],
										"pos" => $v["pos"],
										"edate" => $v["edate"],
									);
					$res = $db->autoExecute($users_guests_groups_table, $fields, DB_AUTOQUERY_INSERT); safeCheck($res);
					$tmp_id = mysqli_insert_id($db->connection);
				}
				
				$users_guests = $db->getAll("SELECT * FROM ".$users_guests_demo_table." WHERE session_id = '".$session_id."'"); safeCheck($users_guests);
				foreach($users_guests as $kk => $vv){
					$fields = array(
										"name" => $vv["name"],
										"email" => $vv["email"],
										"facebook_id" => $vv["facebook_id"],
										"address" => $vv["address"],
										"seated" => $vv["seated"],
										"confirmed" => $vv["confirmed"],
										"confirmed_partner" => $vv["confirmed_partner"],
										"partner_id" => $vv["partner_id"],
										"rejected" => $vv["rejected"],
										"food_menu" => $vv["food_menu"],
										"accommodation_id" => $vv["accommodation_id"],
										"accommodation_duration" => $vv["accommodation_duration"],
										"notes" => $vv["notes"],
										"user_type" => $vv["user_type"],
										"postdate" => $vv["postdate"],
										"food_menu" => $vv["food_menu"],
										"tmp_id" => $vv["id"],
										"user_id" => $user_id,
										"pos" => $vv["pos"],
										"edate" => $vv["edate"],
									);
					$res = $db->autoExecute($users_guests_table, $fields, DB_AUTOQUERY_INSERT); safeCheck($res);
					$user_guest_id = mysqli_insert_id($db->connection);
					
					$ugtugg = $db->getRow("SELECT * FROM ".$users_guests_to_users_guests_groups_demo_table." WHERE user_guest_id = '".$vv["id"]."'"); safeCheck($res);
					foreach( $ugtugg as $kkk => $vvv ){
						$check = $db->getRow("SELECT * FROM ".$users_guests_groups_table." WHERE old_id = '".$vvv["user_guest_group_id"]."' AND user_id = '".$user_id."' "); safeCheck($check);
						$fields = array(
											"user_guest_id" => $user_guest_id,
											"user_guest_group_id" => $check["id"],
											"user_id" => $user_id
										);
						$res = $db->autoExecute($users_guests_to_users_guests_groups_table, $fields, DB_AUTOQUERY_INSERT); safeCheck($res);
					}
				}
				
				$res = $db->Query("DELETE FROM ".$users_guests_demo_table." WHERE session_id = '".session_id()."'"); safeCheck($res);
				$res = $db->Query("DELETE FROM ".$users_guests_groups_demo_table." WHERE session_id = '".session_id()."'"); safeCheck($res);
				$res = $db->Query("DELETE FROM ".$users_guests_to_users_guests_groups_demo_table." WHERE session_id = '".session_id()."'"); safeCheck($res);
			}
		}
		
	}
	
