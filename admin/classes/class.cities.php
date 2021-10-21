<?php
	class cities extends Settings{
		
		public $pagination = "";
		
		function getRecord($id = 0){
			global $db;
			global $cities_table;
            global $lng;
			
			$id = (int)$id;
			
			$row = $db->getRow("SELECT *, name_{$lng} AS name FROM ".$cities_table." WHERE id = '".$id."'"); safeCheck($row);
			
			return $row;
		}
		
		function updateRow($test = ""){
			global $db;
			global $cities_table;
			
			$row = $db->getRow("SELECT * FROM ".$cities_table.""); safeCheck($row);
			
			return $row;
		}
		
		function addEditRow($params){
			global $db;
			global $cities_table;
			
			$act = $params->getString("act");
			$id = $params->getInt("id");
			$fields = array(
//				'district_id'	=> $params->getInt("district_id"),
//				'city_type'     => $params->getString("city_type"),
				'postcode'      => $params->getString("postcode"),
				'name_bg'       => $params->getString("name_bg"),
				'name_en'       => $params->getString("name_en"),
				'name_de'       => $params->getString("name_de"),
				'name_ru'       => $params->getString("name_ru"),
				'name_ro'       => $params->getString("name_ro"),
				'description_bg'	=> $params->getString("description_bg"),
				'description_en'	=> $params->getString("description_en"),
				'description_de'	=> $params->getString("description_de"),
				'description_ru'	=> $params->getString("description_ru"),
				'description_ro'	=> $params->getString("description_ro"),
				'active'			=> $params->getInt("active"),
				'cms_user_id'		=> $_SESSION["uid"]
			);
            
			if($act == "add") {
                $fields["postdate"] = time();
				shiftPos($db, $cities_table);
				$res = $db->autoExecute($cities_table,$fields,DB_AUTOQUERY_INSERT); safeCheck($res);
				$id = mysqli_insert_id($db->connection);
			}
			
			if($act == "edit") {
                $fields["updated_date"] = time();
				$res = $db->autoExecute($cities_table,$fields,DB_AUTOQUERY_UPDATE,"id = " . $id);safeCheck($res);
			}
			
		}

		function deleteField($id, $field){
			global $db;
			global $lng;
			global $cities_table;
			
			$res = $db->autoExecute($cities_table, array( $field => "" ), DB_AUTOQUERY_UPDATE, " id = '".$id."' "); safeCheck($res);
			
			return $res;
		}
		
		function getCities($page, $limit, $params){
			global $db;
			global $sm;
			global $lng;
			global $cities_table;
			
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
                                        {$cities_table}
                                    WHERE 
                                        edate = 0
                                    {$sql_where}"); safeCheck($pages);
			$total_pages = ceil($pages["cntr"]/$limit);
			$generate_pages = '';
			
			if ( $page > 0 ){
				$generate_pages .= '<li class="page-item"><a class="page-link" href="cities.php?'.$_SERVER["QUERY_STRING"].'&page=0" class="first paginate_button paginate_button_enabled" tabindex="0">First</a></li>';
			}else{
				$generate_pages .= '<li class="page-item disabled"><a class="page-link" href="#" class="first paginate_button paginate_button_disabled" tabindex="0">First</a></li>';
			}
			if ( $page > 0 ){
				$generate_pages .= '<li class="page-item"><a class="page-link" href="cities.php?'.$_SERVER["QUERY_STRING"].'&page='.($page-1).'" class="previous paginate_button paginate_button_enabled" tabindex="0">Previous</a></li>';
			}else{
				$generate_pages .= '<li class="page-item disabled"><a class="page-link" href="#" class="previous paginate_button paginate_button_disabled" tabindex="0">Previous</a></li>';
			}
			
			$generate_pages .= '';
			for ( $i = 0 ; $i < $total_pages; $i++ ){
				if ( $page == $i ){
					$generate_pages .= '<li class="page-item active"><a class="page-link" href="cities.php?'.$_SERVER["QUERY_STRING"].'&page='.$i.'" tabindex="0">'.($i+1).'</a></li>';
				}else{
					$generate_pages .= '<li class="page-item"><a class="page-link" href="cities.php?'.$_SERVER["QUERY_STRING"].'&page='.$i.'" tabindex="0">'.($i+1).'</a></li>';
				}
			}
			$generate_pages .= '';
			
			if ( $page < $total_pages-1 ){
				$generate_pages .= '<li class="page-item"><a class="page-link" href="cities.php?'.$_SERVER["QUERY_STRING"].'&page='.($page+1).'" class="next paginate_button paginate_button_enabled" tabindex="0">Next</a></li>';
			}else{
				$generate_pages .= '<li class="page-item disabled"><a class="page-link" href="#" class="next paginate_button paginate_button_disabled" tabindex="0">Next</a></li>';
			}
			
			if ( $page < $total_pages-1 ){
				$generate_pages .= '<li class="page-item"><a class="page-link" href="cities.php?'.$_SERVER["QUERY_STRING"].'&page='.($total_pages-1).'" class="last paginate_button paginate_button_enabled" tabindex="0">Last</a></li>';
			}else{
				$generate_pages .= '<li class="page-item disabled"><a class="page-link" href="#" class="last paginate_button paginate_button_disabled" tabindex="0">Last</a></li>';
			}
			
			$this->pagination = $generate_pages;
            
            
			$cities = $db->getAll("SELECT
                                        *,
                                        name_{$lng} AS name
                                    FROM 
                                        {$cities_table}
                                    WHERE 
                                        edate = 0
                                    {$sql_where}
                                    ORDER BY name
                                    LIMIT {$start}, {$limit}"); safeCheck($cities);
			return $cities;
		}
        
        function getCitiesPagination(){
			return $this->pagination;
		}
		
		function deleteRecord($id){
			global $db;
			global $cities_table;
			
			$fields = array(
								"edate" => time()
							);
			$res = $db->autoExecute($cities_table, $fields, DB_AUTOQUERY_UPDATE, " id = '".$id."' "); safeCheck($res);
			
			return $res;
		}
		
	}
	
?>