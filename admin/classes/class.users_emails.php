<?
	class UsersEmails extends Settings{
		
		public $pagination = "";
		
		function getRecord($id = 0){
			global $db;
			global $lng;
			global $users_emails_table;
			
			$id = (int)$id;
			
			$row = $db->getRow("SELECT *  FROM ".$users_emails_table." WHERE id = '".$id."'"); safeCheck($row);
			
			return $row;
		}
		
		function updateRow($test = ""){
			global $db;
			global $users_emails_table;
			
			$row = $db->getRow("SELECT * FROM ".$users_emails_table.""); safeCheck($row);
			
			return $row;
		}
		
		function addEditRow($params){
			global $db;
			global $users_emails_table;
			global $users_emails_to_users_table;
			
			$act = $params["act"];
			$id = (int)$params["id"];
			$fields = array(
				'subject'	=> $params['subject'],
				'description'	=> $params['description'],
				'postdate'		=> time(),
				'cms_user_id'		=> $_SESSION["uid"],
			);
			
			
			if($act == "add") {
				shiftPos($db, $users_emails_table);
				$res = $db->autoExecute($users_emails_table,$fields,DB_AUTOQUERY_INSERT); safeCheck($res);
				$id = mysqli_insert_id($db->connection);
				
			}
			
			if($act == "edit") {
				$id = (int)$params["id"];
				$res = $db->autoExecute($users_emails_table,$fields,DB_AUTOQUERY_UPDATE,"id = " . $id);safeCheck($res);
			}
			
			
			$res = $db->Query("DELETE FROM ".$users_emails_to_users_table." WHERE users_email_id = '{$id}'"); safeCheck($res);
			$users = $_REQUEST["users"];
			if (sizeof($users)){
				foreach($users as $k=>$v){
					$tmp = Users::getRecord($v);
					$res = $db->Query("INSERT INTO ".$users_emails_to_users_table." (users_email_id, user_id, to_email, to_name) VALUES ('".$id."','".$v."','".$tmp['email']."','".$tmp['name']."')"); safeCheck($res);
				}
			}
			
			if ( isset($params["Send"]) ){
				$emails = $this->getSelectedUsersEmails($id);
				
				foreach($emails as $k => $v){
					$this->mailSender($v["to_email"], $params['subject'], nl2br($params['description']));
				}
			}
			
		}
		
		
		function deleteRecord($id){
			global $db;
			global $users_emails_table;
			
			$fields = array(
								"edate" => time()
							);
			$res = $db->autoExecute($users_emails_table, $fields, DB_AUTOQUERY_UPDATE, " id = '".$id."' "); safeCheck($res);
			
			return $res;
		}
		
		
		
		function getSelectedUsers($id){
			global $db;
			global $users_emails_to_users_table;
			
			$all = $db->getAll("SELECT * FROM ".$users_emails_to_users_table." WHERE users_email_id = '".$id."'"); safeCheck($all);
			
			return $all;
		}
		
		function getSelectedUsersEmails($id){
			global $db;
			global $users_table;
			global $users_emails_to_users_table;
			
			$all = $db->getAll("SELECT uetu.*, users.email FROM ".$users_emails_to_users_table." AS uetu, ".$users_table." AS users WHERE users.id = uetu.user_id AND uetu.users_email_id = '".$id."'"); safeCheck($all);
			
			return $all;
		}
		
		
		function getUsersEmails($page = 0, $limit = 50, $search_string = ""){
			global $db;
			global $lng;
			global $users_emails_table;
			
			$search_string =  htmlspecialchars(trim($_REQUEST["search_string"]), ENT_QUOTES);
			if ( $search_string ){
				$search_string = strtolower($search_string);
				$sql_search_string = " AND (LOWER(subject) LIKE '%".$search_string."%' OR LOWER(description) LIKE '%".$search_string."%')";
			}
			
			
			$start = $page * $limit;
			$pages = $db->getRow("SELECT count(id) AS cntr FROM ".$users_emails_table." WHERE edate = 0 {$sql_search_string}"); safeCheck($pages);
			$total_pages = ceil($pages["cntr"]/$limit);
			$generate_pages = '';
			
			if ( $page > 0 ){
				$generate_pages .= '<a href="users_emails.php?'.$_SERVER["QUERY_STRING"].'&page=0" class="first paginate_button paginate_button_enabled" tabindex="0">First</a>';
			}else{
				$generate_pages .= '<a href="#" class="first paginate_button paginate_button_disabled" tabindex="0">First</a>';
			}
			if ( $page > 0 ){
				$generate_pages .= '<a href="users_emails.php?'.$_SERVER["QUERY_STRING"].'&page='.($page-1).'" class="previous paginate_button paginate_button_enabled" tabindex="0">Previous</a>';
			}else{
				$generate_pages .= '<a href="#" class="previous paginate_button paginate_button_disabled" tabindex="0">Previous</a>';
			}
			
			$generate_pages .= '<span>';
			for ( $i = 0 ; $i < $total_pages; $i++ ){
				if ( $page == $i ){
					$generate_pages .= '<a href="users_emails.php?'.$_SERVER["QUERY_STRING"].'&page='.$i.'" class="paginate_active" tabindex="0">'.($i+1).'</a>';
				}else{
					$generate_pages .= '<a href="users_emails.php?'.$_SERVER["QUERY_STRING"].'&page='.$i.'" class="paginate_button" tabindex="0">'.($i+1).'</a>';
				}
			}
			$generate_pages .= '</span>';
			
			if ( $page < $total_pages-1 ){
				$generate_pages .= '<a href="users_emails.php?'.$_SERVER["QUERY_STRING"].'&page='.($page+1).'" class="next paginate_button paginate_button_enabled" tabindex="0">Next</a>';
			}else{
				$generate_pages .= '<a href="#" class="next paginate_button paginate_button_disabled" tabindex="0">Next</a>';
			}
			
			if ( $page < $total_pages-1 ){
				$generate_pages .= '<a href="users_emails.php?'.$_SERVER["QUERY_STRING"].'&page='.($total_pages-1).'" class="last paginate_button paginate_button_enabled" tabindex="0">Last</a>';
			}else{
				$generate_pages .= '<a href="#" class="last paginate_button paginate_button_disabled" tabindex="0">Last</a>';
			}
			
			$this->pagination = $generate_pages;
			
			$users_emails = $db->getAll("SELECT * FROM ".$users_emails_table." WHERE edate = 0 {$sql_search_string} ORDER BY postdate DESC LIMIT {$start}, {$limit}"); safeCheck($users_emails);
			foreach($users_emails as $k => $v){
				
				$users_emails[$k] = $v;
			}
			return $users_emails;
		}
		
		function getUsersEmailsPagination(){
			return $this->pagination;
		}
		
		function mailSender($email, $message_heading, $message_text, $file_attachment_1 = "", $file_attachment_2 = "", $file_attachment_3 = ""){
			global $domains_cyrillic;
			global $install_path;
			global $sm;
			global $host;
			global $contacts;
			global $copyrights;
			require_once($install_path."phpmailer/class.phpmailer.php");
			
			$sm->assign("subject", $message_heading);
			$sm->assign("message", $message_text);

			$message_text = '<html>
									<head>
										<title>'.$message_heading.'</title>
									</head>
									<body>
										<table width="100%" cellpadding="0" cellspacing="0">
											<tr>
												<td valign="top" width="240">
													<a href="'.$host.'" target="_blank"><img src="'.$host.'images/logo.png" border="0" /></a>
													<br>
												</td>
												<td valign="top">
													<span style="font-size: 11px;">
													'.$contacts["description"].'
													<br>
													</span>
													<br>
												</td>
											</tr>
											<tr>
												<td colspan="2" height="10" bgcolor="#B48F6C">
												</td>
											</tr>
										</table>
										
										<table width="100%" cellpadding="0" cellspacing="0">
											<tr>
												<td>
													<br><br><br>
													'.$message_text.'
													<br><br><br>
												</td>
											</tr>
										</table>
										<table width="100%" cellpadding="5" cellspacing="0">
											<tr>
												<td valign="top" bgcolor="#B48F6C">
													<span style="color: #ffffff; font-size: 11px;">'.$copyrights["description"].'</span>
												</td>
											</tr>
										</table>
									</body>
									</html>';
			
			
			$mailObj = new PHPMailer();
			$mailObj->CharSet = 'utf-8';
			$mailObj->From      = 'support@weddingday.bg';
			$mailObj->isHTML(true);
			$mailObj->FromName  = 'WeddingDay.bg';
			$mailObj->Subject   = $message_heading;
			$mailObj->Body      = $message_text;
			$mailObj->AddAddress( $email );

			if ( $file_attachment_1 ){
				$mailObj->AddAttachment( $file_attachment_1 );
			}

			if ( $file_attachment_2 ){
				$file_to_attach = $install_path.'files/emails/'.$file_attachment_2;
				$mailObj->AddAttachment( $file_to_attach , $file_attachment_2 );
			}

			if ( $file_attachment_3 ){
				$file_to_attach = $install_path.'files/emails/'.$file_attachment_3;
				$mailObj->AddAttachment( $file_to_attach , $file_attachment_3 );
			}

			return $mailObj->Send();
		}
		
	}
	
?>