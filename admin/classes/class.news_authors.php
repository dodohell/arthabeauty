<?php
	class NewsAuthors extends Settings{
		
		public $pagination = "";
		
		public static function getRecord(int $id){
			global $db;
            global $lng;
			global $news_authors_table;

			$row = $db->getRow("SELECT
                                    *, name_{$lng} AS name
                                FROM
                                    ".$news_authors_table."
                                WHERE 
                                    id = {$id}"); safeCheck($row);
			return $row;
		}
		
		public function addEditRow(FilteredMap $params){
			global $db;
			global $news_authors_table;
			
			$act = $params->getString("act");
			$id = $params->getInt("id");
			$fields = array(
				'name_bg'       => $params->getString("name_bg"),
				'name_en'       => $params->getString("name_en"),
				'name_de'       => $params->getString("name_de"),
				'name_ru'       => $params->getString("name_ru"),
				'name_ro'       => $params->getString("name_ro"),
				'active'		=> $params->getInt("active"),
				'cms_user_id'	=> $_SESSION["uid"]
			);
            
			$pic = copyImage($_FILES['pic'], "../files/", "../files/tn/", "../files/tntn/", "250x");
			if(!empty($pic)) $fields['pic'] = $pic;
			
			if($act == "add") {
                $fields["postdate"] = time();
				shiftPos($db, $news_authors_table);
				$res = $db->autoExecute($news_authors_table,$fields,DB_AUTOQUERY_INSERT); safeCheck($res);
				$id = mysqli_insert_id($db->connection);
			}
			
			if($act == "edit") {
                $fields["updated_date"] = time();
				$res = $db->autoExecute($news_authors_table,$fields,DB_AUTOQUERY_UPDATE,"id = " . $id);safeCheck($res);
			}
			return $id;
		}

		public function deleteField($id, $field){
			global $db;
			global $news_authors_table;
			
			$res = $db->autoExecute($news_authors_table, array( $field => "" ), DB_AUTOQUERY_UPDATE, " id = '".$id."' "); safeCheck($res);
			
			return $res;
		}
		
		public function getNewsAuthors($page, $limit, $params){
			global $db;
			global $sm;
			global $lng;
			global $news_authors_table;
			
            $name = $params->getString("name");
            $sm->assign("name", $name);
            
            $sql_where = "";
            if($name){
                $sql_where = ' AND (name_bg LIKE "%'.$name .'%" OR name_en LIKE "%'.$name .'%")';
            }
            $start = $page * $limit;
			$pages = $db->getRow("SELECT 
                                        count(id) AS cntr 
                                    FROM 
                                        {$news_authors_table}
                                    WHERE 
                                        edate = 0
                                    {$sql_where}"); safeCheck($pages);
			$total_pages = ceil($pages["cntr"]/$limit);
			$generate_pages = '';
			
			if ( $page > 0 ){
				$generate_pages .= '<li class="page-item"><a class="page-link" href="news_authors.php?'.$_SERVER["QUERY_STRING"].'&page=0" class="first paginate_button paginate_button_enabled" tabindex="0">First</a></li>';
			}else{
				$generate_pages .= '<li class="page-item disabled"><a class="page-link" href="#" class="first paginate_button paginate_button_disabled" tabindex="0">First</a></li>';
			}
			if ( $page > 0 ){
				$generate_pages .= '<li class="page-item"><a class="page-link" href="news_authors.php?'.$_SERVER["QUERY_STRING"].'&page='.($page-1).'" class="previous paginate_button paginate_button_enabled" tabindex="0">Previous</a></li>';
			}else{
				$generate_pages .= '<li class="page-item disabled"><a class="page-link" href="#" class="previous paginate_button paginate_button_disabled" tabindex="0">Previous</a></li>';
			}
			
			$generate_pages .= '';
			for ( $i = 0 ; $i < $total_pages; $i++ ){
				if ( $page == $i ){
					$generate_pages .= '<li class="page-item active"><a class="page-link" href="news_authors.php?'.$_SERVER["QUERY_STRING"].'&page='.$i.'" tabindex="0">'.($i+1).'</a></li>';
				}else{
					$generate_pages .= '<li class="page-item"><a class="page-link" href="news_authors.php?'.$_SERVER["QUERY_STRING"].'&page='.$i.'" tabindex="0">'.($i+1).'</a></li>';
				}
			}
			$generate_pages .= '';
			
			if ( $page < $total_pages-1 ){
				$generate_pages .= '<li class="page-item"><a class="page-link" href="news_authors.php?'.$_SERVER["QUERY_STRING"].'&page='.($page+1).'" class="next paginate_button paginate_button_enabled" tabindex="0">Next</a></li>';
			}else{
				$generate_pages .= '<li class="page-item disabled"><a class="page-link" href="#" class="next paginate_button paginate_button_disabled" tabindex="0">Next</a></li>';
			}
			
			if ( $page < $total_pages-1 ){
				$generate_pages .= '<li class="page-item"><a class="page-link" href="news_authors.php?'.$_SERVER["QUERY_STRING"].'&page='.($total_pages-1).'" class="last paginate_button paginate_button_enabled" tabindex="0">Last</a></li>';
			}else{
				$generate_pages .= '<li class="page-item disabled"><a class="page-link" href="#" class="last paginate_button paginate_button_disabled" tabindex="0">Last</a></li>';
			}
			
			$this->pagination = $generate_pages;
            
            
			$news_authors = $db->getAll("SELECT
                                        *,
                                        name_{$lng} AS name
                                    FROM 
                                        {$news_authors_table}
                                    WHERE 
                                        edate = 0
                                    {$sql_where}
                                    ORDER BY pos
                                    LIMIT {$start}, {$limit}"); safeCheck($news_authors);
			return $news_authors;
		}
        
		public function getNewsAuthorsAll(){
			global $db;
			global $lng;
			global $news_authors_table;
			
			$news_authors = $db->getAll("SELECT
                                        *,
                                        name_{$lng} AS name
                                    FROM 
                                        {$news_authors_table}
                                    WHERE 
                                        edate = 0
                                    ORDER BY pos"); safeCheck($news_authors);
			return $news_authors;
		}
        
        public function getNewsAuthorsPagination(){
			return $this->pagination;
		}
		
		public function deleteRecord($id){
			global $db;
			global $news_authors_table;
			
			$fields = array(
								"edate" => time()
							);
			$res = $db->autoExecute($news_authors_table, $fields, DB_AUTOQUERY_UPDATE, " id = '".$id."' "); safeCheck($res);
			
			return $res;
		}
		
	}
	
?>