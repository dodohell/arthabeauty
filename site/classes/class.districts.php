<?php

class Districts extends Settings {

    public static function getRecord(int $id) {
        global $db;
        global $districts_table;

        $row = $db->getRow("SELECT * FROM " . $districts_table . " WHERE id = " . $id);
        safeCheck($row);

        return $row;
    }

    function getDistricts() {
        global $db;
        global $lng;
        global $districts_table;

        $districts = $db->getAll("SELECT *, name_{$lng} AS name FROM " . $districts_table . " WHERE edate = 0 ORDER BY pos");
        safeCheck($districts);
//			if ($options && $options["selected"] && $options["customer_id"] ){
//				$districtsSelected = $db->getAll("SELECT * FROM ".$customers_to_districts_table." WHERE customer_id = '".$options["customer_id"]."'"); safeCheck($districtsSelected);
//			}
//			
//			foreach($districts as $k => $v){
//				foreach($districtsSelected as $kk => $vv){
//					if ( $vv["city_id"] == $v["id"] ){
//						$v["selected"] = "checked";
//					}
//				}
//				
//				$districts[$k] = $v;
//			}

        return $districts;
    }

}
