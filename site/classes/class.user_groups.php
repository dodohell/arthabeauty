<?php
	class UserGroups extends Settings{
		
		public static function getRecord(int $id){
			global $db;
            global $lng;
			global $user_groups_table;
			
			$row = $db->getRow("SELECT *, name_{$lng} AS name, description_{$lng} AS description FROM ".$user_groups_table." WHERE id = {$id}"); safeCheck($row);
			
			return $row;
		}
        
		public function getUserGroupsAllActive(){
			global $db;
			global $lng;
			global $user_groups_table;
            
			$user_groups = $db->getAll("SELECT
                                        *,
                                        name_{$lng} AS name
                                    FROM 
                                        {$user_groups_table}
                                    WHERE 
                                        edate = 0
                                    AND active = 1
                                    ORDER BY pos"); safeCheck($user_groups);
			return $user_groups;
		}
        
        public function getUserGroupsPagination(){
			return $this->pagination;
		}
		
		public function deleteRecord($id){
			global $db;
			global $user_groups_table;
			
			$fields = array(
								"edate" => time()
							);
			$res = $db->autoExecute($user_groups_table, $fields, DB_AUTOQUERY_UPDATE, " id = '".$id."' "); safeCheck($res);
			
			return $res;
		}
		
	}
	
?>