<?php
	class UsersTables extends Settings{
		
		
		function addEditUserTable($params, $act = "add"){
			global $db;
			global $users_tables_table;
			global $users_tables_guests_table;
			global $user;
			
			$places = (int)$params["table_places"];
			
			$id = (int)$params["id"];
			$fields = array(
				"name" => htmlspecialchars(trim($params["table_name"]), ENT_QUOTES),
				"places" => $places,
				"user_id" => $user["id"],
				"postdate" => time()
			);
			
			if ( $params["table_type"] ){
				$fields["table_type"] = (int)$params["table_type"];
			}
			
			if ( $params["act"] ){
				$act = $params["act"];
			}
			
			if($act == "add"){
				shiftPos($db, $users_tables_table);
				$res = $db->autoExecute($users_tables_table,$fields,DB_AUTOQUERY_INSERT); safeCheck($res);
				$id = mysqli_insert_id($db->connection);
				
				for ( $i = 0; $i < $places ; $i++ ){
					$res = $db->autoExecute($users_tables_guests_table, array("table_id" => $id, "pos" => $i, "postdate" => time(), "user_id" => $user["id"]), DB_AUTOQUERY_INSERT); safeCheck($res);
				}
				
			}
			
			if($act == "edit"){
				$id = (int)$params["id"];
				$res = $db->autoExecute($users_tables_table,$fields,DB_AUTOQUERY_UPDATE,"id = " . $id. " AND user_id = '".$user["id"]."' ");safeCheck($res);
				
				$taken_places = $db->getRow("SELECT count(id) AS cntr FROM ".$users_tables_guests_table." WHERE table_id = '".$id."' AND user_guest_id != 0 AND edate = 0"); safeCheck($taken_places);
				$free_places = $db->getRow("SELECT count(id) AS cntr FROM ".$users_tables_guests_table." WHERE table_id = '".$id."' AND user_guest_id = 0 AND edate = 0"); safeCheck($free_places);
				$all_places = $db->getRow("SELECT count(id) AS cntr FROM ".$users_tables_guests_table." WHERE table_id = '".$id."' AND edate = 0"); safeCheck($free_places);
				
				if ( $places > $free_places["cntr"] ){
					$add_places = $places - $free_places["cntr"];
					
					for ( $i = $all_places['cntr']; $i < $places ; $i++ ){
						$res = $db->autoExecute($users_tables_guests_table, array("table_id" => $id, "pos" => $i, "postdate" => time(), "user_id" => $user["id"]), DB_AUTOQUERY_INSERT); safeCheck($res);
					}
				}
				if($places - $taken_places["cntr"] < $free_places["cntr"]  ){
					$remove_places = $all_places["cntr"] - $places;
					$all_places = $db->getAll("SELECT * FROM ".$users_tables_guests_table." WHERE table_id = '".$id."' AND user_guest_id = 0 AND edate = 0 ORDER BY pos DESC"); safeCheck($all_places);
					
					for ( $i = 0 ; $i < $remove_places; $i++ ){
						$res = $db->autoExecute($users_tables_guests_table, array("edate" => time()), DB_AUTOQUERY_UPDATE, " id = '".$all_places[$i]["id"]."' AND user_id = '".$user["id"]."'"); safeCheck($res);
						
					}
					
				}
			}
			
			if($act == "delete"){
				$id = (int)$params["id"];
				$res = $db->autoExecute($users_tables_table,array("edate" => time()),DB_AUTOQUERY_UPDATE,"id = " . $id);safeCheck($res);
			}
			return $id;
		}
		
		
		function updateTableCoords($params){
			global $db;
			global $users_tables_table;
			global $users_tables_guests_table;
			global $user;
			
			
			$id = (int)$params["table_id"];
			if ( $params["xcoord"] > 0 ){
				$xcoord = (double)$params["xcoord"];
			}else{
				$xcoord = 0;
			}
			if ( $params["ycoord"] > 0 ){
				$ycoord = (double)$params["ycoord"];
			}else{
				$ycoord = 0;
			}
			
			$fields = array(
				"xcoord" => $params["xcoord"],
				"ycoord" => $params["ycoord"],
			);
			
			$res = $db->autoExecute($users_tables_table, $fields, DB_AUTOQUERY_UPDATE, "id = " . $id. " AND user_id = '".$user["id"]."' ");safeCheck($res);
			
			return $id;
		}
		
		
		function getUserTables(){
			global $db;
			global $users_tables_table;
			global $users_tables_guests_table;
			global $users_guests_table;
			global $user;
			
			$user_tables = $db->getAll("SELECT * FROM ".$users_tables_table." WHERE edate = 0 AND user_id = '".$user["id"]."' ORDER BY name"); safeCheck($user_tables);
			foreach($user_tables as $k => $v){
				$guests = $db->getAll("SELECT * FROM ".$users_tables_guests_table." WHERE edate = 0 AND table_id = '".$v["id"]."' AND user_id = '".$user["id"]."' ORDER BY pos "); safeCheck($guests);
				foreach($guests as $kk => $vv){
					$row = $db->getRow("SELECT * FROM ".$users_guests_table." WHERE edate = 0 AND id = '".$vv["user_guest_id"]."' AND user_id = '".$user["id"]."'"); safeCheck($row);
					if ( $row["id"] ){
						$vv["guest"] = $row;
					}else{
						$vv["guest"]["name"] = "";
						$vv["guest"]["id"] = "";
					}
					$guests[$kk] = $vv;
				}
				$v["guests"] = $guests;
				$user_tables[$k] = $v;
			}
			
			return $user_tables;
		}
		
		function addUserGuest($params){
			global $db;
			global $sm;
			global $users_tables_guests_table;
			global $users_guests_table;
			global $user;
			
			$fields = array(
								"table_id" => $params["table_id"],
								"user_guest_id" => $params["user_guest_id"],
								"pos" => $params["pos"],
								"user_id" => $user["id"],
							);
			$res = $db->autoExecute($users_tables_guests_table, array("user_guest_id" => 0), DB_AUTOQUERY_UPDATE, " user_id = '".$user["id"]."' AND user_guest_id = '".$params["user_guest_id"]."' "); safeCheck($res);
			$res = $db->autoExecute($users_tables_guests_table, $fields, DB_AUTOQUERY_UPDATE, " table_id = '".$params["table_id"]."' AND user_id = '".$user["id"]."' AND pos = '".$params["pos"]."'"); safeCheck($res);
			
			$res = $db->autoExecute($users_guests_table, array("seated" => 1), DB_AUTOQUERY_UPDATE, " id = '".$params["user_guest_id"]."' AND user_id = '".$user["id"]."' "); safeCheck($res);
		}
		
		
		function deleteUserGuest($params){
			global $db;
			global $sm;
			global $users_tables_guests_table;
			global $users_guests_table;
			global $user;
			
			$fields = array(
								"table_id" => $params["table_id"],
								"user_guest_id" => 0,
								"pos" => $params["pos"],
								"user_id" => $user["id"],
							);
			$res = $db->autoExecute($users_tables_guests_table, array("user_guest_id" => 0), DB_AUTOQUERY_UPDATE, " user_id = '".$user["id"]."' AND user_guest_id = '".$params["user_guest_id"]."' "); safeCheck($res);
			$res = $db->autoExecute($users_tables_guests_table, $fields, DB_AUTOQUERY_UPDATE, " table_id = '".$params["table_id"]."' AND pos = '".$params["pos"]."' AND user_id = '".$user["id"]."' "); safeCheck($res);
			
			$res = $db->autoExecute($users_guests_table, array("seated" => 0), DB_AUTOQUERY_UPDATE, " id = '".$params["user_guest_id"]."' AND user_id = '".$user["id"]."' "); safeCheck($res);
		}
		
		function deleteUserTable($params){
			global $db;
			global $sm;
			global $users_tables_guests_table;
			global $users_guests_table;
			global $users_tables_table;
			global $user;
			
			$table_id = (int)$params["id"];
			
			$users_guests = $db->getAll("SELECT * FROM ".$users_tables_guests_table." WHERE table_id = '".$table_id."' AND edate = 0 AND user_id = '".$user["id"]."'"); safeCheck($users_guests);
			foreach($users_guests as $k => $v){
				$res = $db->autoExecute($users_guests_table, array("seated" => 0), DB_AUTOQUERY_UPDATE, " id = '".$v["user_guest_id"]."' AND user_id = '".$user["id"]."'"); safeCheck($res);
			}
			
			
			$res = $db->autoExecute($users_tables_guests_table, array("user_guest_id" => 0), DB_AUTOQUERY_UPDATE, " user_id = '".$user["id"]."' AND table_id = '".$table_id."' "); safeCheck($res);
			$res = $db->autoExecute($users_tables_table, array("edate" => time()), DB_AUTOQUERY_UPDATE, " id = '".$table_id."' AND user_id = '".$user["id"]."' "); safeCheck($res);
		}
		
		function getRecord($id = 0){
			global $db;
			global $lng;
			global $users_tables_table;
			
			$id = (int)$id;
			
			$row = $db->getRow("SELECT * FROM ".$users_tables_table." WHERE id = '".$id."'"); safeCheck($row);
			
			return $row;
		}
		
		function getPage(){
			global $db;
			global $lng;
			global $sm;
			global $user;
			
			$settingsObj = new Settings();
			$settingsObj->checkLogin();
			
			$usersGuestsObj = new UsersGuests();
			$user_guests = $usersGuestsObj->getUserGuestsByUsersGroup(array("seated" => 0));
			$sm->assign("user_guests", $user_guests);
			
			
			$guests = $usersGuestsObj->getUsersGuests();
			$sm->assign("guests_number", sizeof($guests));
			
			$websiteObj = new Website();
			$website = $websiteObj->getWebsiteID();
			$sm->assign("website", $website);
			
			$sm->assign("page_categories", 1);
			$sm->assign("page_user_tables", 1);
			$sm->display("table-order.html");	
		}
		
		
		function getPagePrintPdf(){
			global $db;
			global $lng;
			global $sm;
			
			$user_tables = $this->getUserTables();
			$sm->assign("user_tables", $user_tables);
			
			$sm->assign("star", '<span class="star">*</span>');
			$sm->assign("page_categories", 1);
			
			ob_start();
			$sm->display("table-order-print.html");	
			$html = ob_get_clean();
			
			$mpdf=new mPDF('utf-8', 'A4'); 
			$mpdf->useAdobeCJK = true;		
			$mpdf->SetAutoFont(AUTOFONT_ALL);	
			$mpdf->SetHTMLHeader($site_title["description"]);
			$mpdf->WriteHTML($html);
			if ( file_exists($install_path."files-export/".$user["id"]."/") === false ){
				mkdir($install_path."files-export/".$user["id"]."/");
				chmod($install_path."files-export/".$user["id"]."/", 0777);
			}
			$download_file = "table-order.pdf";
			//unlink($download_file);
			$mpdf->Output($download_file, 'D'); 
			//$mpdf->Output(); 
		}
		
		
		function getPageBeforeLogin(){
			global $db;
			global $lng;
			global $sm;
			global $user;
			
			$settingsObj = new Settings();
			$settingsObj->setLoginRedirect("table_order");
			
			
			$sm->assign("page_user_tables", 1);
			$sm->assign("page_categories", 1);
			$sm->display("table-order-before-login.html");	
		}
		
		function getAjaxPage($type = ""){
			global $db;
			global $lng;
			global $sm;
			
			if ( $type == "get-user-tables" ){
				$user_tables = $this->getUserTables();
				echo json_encode($user_tables);
			}
		}
		
		
		function editTable($params){
			global $db;
			global $sm;
			global $user;
			global $users_guests_table;
			global $users_tables_guests_table;
			
			$row = $this->getRecord($params["id"]);
			$sm->assign("row", $row);
			$all_places = $db->getRow("SELECT count(id) AS cntr FROM ".$users_tables_guests_table." WHERE table_id = '".$params['id']."' AND user_id = '".$user["id"]."' AND edate = 0"); safeCheck($all_places);
			$free_places = $db->getRow("SELECT count(id) AS cntr FROM ".$users_tables_guests_table." WHERE table_id = '".$params['id']."' AND user_id = '".$user["id"]."' AND user_guest_id = 0 AND edate = 0"); safeCheck($free_places);
			$sm->assign("free_places", $free_places["cntr"]);
			$sm->assign("all_places", $all_places["cntr"]);
			$sm->assign("taken_places", $all_places["cntr"]-$free_places['cntr']);
			
			
			$sm->display("table-order-edit-table.html");
		}
		
	}
	
?>