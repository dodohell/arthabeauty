<?php
	class Manifacturers extends Settings{
		
		
		public static function getRecord(int $id){
			global $db;
			global $lng;
			global $manifacturers_table;
			
            $row = $db->getRow("SELECT *, name_{$lng} AS name FROM ".$manifacturers_table." WHERE id = {$id}"); safeCheck($row);
			
			return $row;
		}
		
		public static function searchManifacturer($name = ""){
			global $db;
			global $lng;
			global $manifacturers_table;
			
			$name = strtolower($name);
			
			$row = $db->getRow("SELECT *, name_{$lng} AS name FROM ".$manifacturers_table." WHERE LOWER(name_{$lng}) = '".$name."' AND edate = 0"); safeCheck($row);
			
			return $row;
		}
		
		
		public static function getManifacturers(){
			global $db;
			global $lng;
			global $manifacturers_table;
			
			$manifacturers = $db->getAll("SELECT *,
                                            name_{$lng} AS name,
                                            description_{$lng} AS description
                                        FROM ".$manifacturers_table."
                                        WHERE
                                           edate = 0
                                        AND name_{$lng} <> ''
                                        AND active = 1
                                        ORDER BY pos"); safeCheck($manifacturers);
			return $manifacturers;
		}
		
	}
	
?>