<?php
	class UsersAdmin extends Settings{
		
		public $pagination = "";
		
		function getRecord($id = 0){
			global $db;
			global $lng;
			global $users_admin_table;
			
			$id = (int)$id;
			
			$row = $db->getRow("SELECT * FROM ".$users_admin_table." WHERE id = '".$id."'"); safeCheck($row);
			
			return $row;
		}
		
		function updateRow($test = ""){
			global $db;
			global $users_admin_table;
			
			$row = $db->getRow("SELECT * FROM ".$users_admin_table.""); safeCheck($row);
			
			return $row;
		}
		
        /**
         * 
         * @global type $db
         * @global type $users_admin_table
         * @global type $news_to_news_categories_table
         * @param FilteredMap $params
         */
		public function addEditRow($params){
			global $db;
			global $users_admin_table;
			global $fullmenu;
            global $users_admin_to_menus_table;

            $act = $params->getString("act");
			$id = $params->getInt("id");
			$fields = array(
				'uname'             => $params->getString("uname"),
				'email'             => $params->getEmail("email"),
				'firstname'         => $params->getString("firstname"),
				'lastname'          => $params->getString("lastname"),
				'companyname'       => $params->getString("companyname"),
				'businessaddress'	=> $params->getString("businessaddress"),
				'phone'             => $params->getString("phone"),
				'active'            => $params->getInt("active"),
				'level'         	=> $params->getInt("level"),
			);
            
			$password = $params->get("password");
			$confirm_password = $params->get("confirm_password");
            if ($password && $password == $confirm_password) {
                $passwordHash = password_hash($password, PASSWORD_BCRYPT, ["cost" => 10]);
                $fields["upass"] = $passwordHash;
            }
            
			if($act == "add") {
				shiftPos($db, $users_admin_table);
				$fields["postdate"] = time();
				$fields["register_ip"] = $_SERVER["REMOTE_ADDR"];
				$fields["register_by_id"] = $_SESSION["uid"];
				$res = $db->autoExecute($users_admin_table,$fields,DB_AUTOQUERY_INSERT); safeCheck($res);
				$id = mysqli_insert_id($db->connection);
			}
			
			if($act == "edit") {
                $fields["last_change"] = time();
                $fields["last_ip"] = $_SERVER["REMOTE_ADDR"];
                $fields["last_id"] = $_SESSION["uid"];
				$res = $db->autoExecute($users_admin_table,$fields,DB_AUTOQUERY_UPDATE,"id = " . $id);safeCheck($res);
			}
            
            $readArr= $params->get("read_menu");
            $writeArr= $params->get("edit_menu");
            $fullArr= $params->get("full_menu");
            
            foreach( $fullmenu as $k=>$v){
                $accessField = array( 'user_id' => $id , 'menu_id'=>$v['menu_id'] );
                if( $fullArr[ $v['menu_id'] ] ){ // Access 3
                    //dbg('Пълен достъп');
                    $accessField['permission_level'] = 3;
                }elseif( $writeArr[ $v['menu_id'] ] ){ // Access 2
                    //dbg('Редакция без изтриване');
                    $accessField['permission_level'] = 2;
                }elseif( $readArr[ $v['menu_id'] ] ){// Access 1
                    //dbg('Само преглед'); 
                    $accessField['permission_level'] = 1;
                }else{
                    //dbg('Без Достъп'); //none Access 0
                    $accessField['permission_level'] = 0;
                }
                $check =  $db->getRow("SELECT id FROM " . $users_admin_to_menus_table . " WHERE user_id = " . $accessField['user_id'] . " AND menu_id=".$accessField['menu_id']);
                if( $check["id"]){
                    $res = $db->autoExecute($users_admin_to_menus_table,$accessField,DB_AUTOQUERY_UPDATE,"id = " . $check["id"]);
                }else{
                    $res = $db->autoExecute($users_admin_to_menus_table,$accessField,DB_AUTOQUERY_INSERT);
                }
                if($v["submenu"]){
                    foreach ($v["submenu"] as $kk => $vv) {
                        $accessField = array( 'user_id' => $id , 'menu_id'=>$vv['menu_id'] );
                        if( $fullArr[ $vv['menu_id'] ] ){ // Access 3
                            //dbg('Пълен достъп');
                            $accessField['permission_level'] = 3;
                        }elseif( $writeArr[ $vv['menu_id'] ] ){ // Access 2
                            //dbg('Редакция без изтриване');
                            $accessField['permission_level'] = 2;
                        }elseif( $readArr[ $vv['menu_id'] ] ){// Access 1
                            //dbg('Само преглед'); 
                            $accessField['permission_level'] = 1;
                        }else{
                            //dbg('Без Достъп'); //none Access 0
                            $accessField['permission_level'] = 0;
                        }
                        $check =  $db->getRow("SELECT id FROM " . $users_admin_to_menus_table . " WHERE user_id = " . $accessField['user_id'] . " AND menu_id=".$accessField['menu_id']);
                        if( $check["id"]){
                            $res = $db->autoExecute($users_admin_to_menus_table,$accessField,DB_AUTOQUERY_UPDATE,"id = " . $check["id"]);
                        }else{
                            $res = $db->autoExecute($users_admin_to_menus_table,$accessField,DB_AUTOQUERY_INSERT);
                        }
                    }
                }
            }
			
		}
		
		function deleteRecord($id){
			global $db;
			global $users_admin_table;
			
			$fields = array(
								"edate" => time()
							);
			$res = $db->autoExecute($users_admin_table, $fields, DB_AUTOQUERY_UPDATE, " id = '".$id."' "); safeCheck($res);
			
			return $res;
		}
		
		
		function getUsersAdmin($page = 0, $limit = 50, $search_string = ""){
			global $db;
			global $lng;
			global $users_admin_table;
			
			$search_string =  htmlspecialchars(trim($_REQUEST["search_string"]), ENT_QUOTES);
			if ( $search_string ){
				$search_string = strtolower($search_string);
				$sql_search_string = " AND (LOWER(name_{$lng}) LIKE '%".$search_string."%' OR LOWER(excerpt_{$lng}) LIKE '%".$search_string."%' OR LOWER(description_{$lng}) LIKE '%".$search_string."%')";
			}
			
			
			$start = $page * $limit;
			$pages = $db->getRow("SELECT count(id) AS cntr FROM ".$users_admin_table." WHERE edate = 0 {$sql_search_string}"); safeCheck($pages);
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
			
			$news = $db->getAll("SELECT * FROM ".$users_admin_table." WHERE edate = 0 {$sql_search_string} ORDER BY firstname, lastname, email LIMIT {$start}, {$limit}"); safeCheck($news);
			foreach($news as $k => $v){
				
				$news[$k] = $v;
			}
			return $news;
		}
		
		function getUsersAdminPagination(){
			return $this->pagination;
		}
		
	}
	
?>