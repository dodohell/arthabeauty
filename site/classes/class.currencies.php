<?php

class Currencies extends Settings {

    public static function getRecord(int $id, int $activeOnly = 0) {
        global $db;
        global $lng;
        global $currencies_table;
        
        $sqlWhere = $activeOnly == 1 ? " AND c.active = 1 " : "";
        
        $row = $db->getRow("SELECT 
                                c.*, 
                                c.name_$lng AS name,
                                c.abbreviation_$lng AS abbreviation
                            FROM
                                " . $currencies_table . " AS c
                            WHERE 
                                c.id = {$id}
                            AND c.edate = 0
                            {$sqlWhere}"); safeCheck($row);
        return $row;
    }
    
    public static function getRecordByCode($code, int $activeOnly = 0) {
        global $db;
        global $lng;
        global $currencies_table;
        
        $code = htmlspecialchars(trim($code), ENT_QUOTES);
        $sqlWhere = $activeOnly == 1 ? " AND c.active = 1 " : "";
        
        $row = $db->getRow("SELECT 
                                c.*, 
                                c.name_$lng AS name,
                                c.abbreviation_$lng AS abbreviation
                            FROM
                                " . $currencies_table . " AS c
                            WHERE 
                                c.code = '{$code}'
                            AND c.edate = 0
                            {$sqlWhere}"); safeCheck($row);
        return $row;
    }
    
    public static function getBaseCurrency() {
        global $db;
        global $lng;
        global $currencies_table;
        
        $row = $db->getRow("SELECT
                                c.*,
                                c.name_$lng AS name,
                                c.abbreviation_$lng AS abbreviation
                            FROM
                                " . $currencies_table . " AS c
                            WHERE
                                c.is_base = 1
                            AND c.active = 1
                            AND c.edate = 0"); safeCheck($row);
        return $row;
    }

    public function getCurrencies($activeOnly = 0) {
        global $db;
        global $lng;
        global $currencies_table;
        
        if ($activeOnly) {
            $activeOnlySQL = " AND active = 1 ";
        }
        
        $items = $db->getAll("SELECT
                                *,
                                name_{$lng} AS name,
                                abbreviation_{$lng} AS abbreviation
                            FROM
                                ".$currencies_table."
                            WHERE
                                edate = 0
                            {$activeOnlySQL}
                            ORDER BY pos"); safeCheck($items);
        return $items;
    }
    
}
