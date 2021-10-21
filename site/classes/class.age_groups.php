<?php

class AgeGroups extends Settings {

    public static function getRecord(int $id) {
        global $db;
        global $lng;
        global $age_groups_table;

        $row = $db->getRow("SELECT 
                                ag.*, 
                                ag.name_$lng AS name
                            FROM
                                " . $age_groups_table . " AS ag
                            WHERE 
                                ag.edate = 0
                            AND ag.id = " . $id); safeCheck($row);
        return $row;
    }

    public static function getAgeGroups($activeOnly = 0) {
        global $db;
        global $lng;
        global $age_groups_table;
        
        if ($activeOnly) {
            $activeOnlySQL = " AND active = 1 ";
        }
        
        $items = $db->getAll("SELECT
                                *,
                                name_{$lng} AS name
                            FROM
                                ".$age_groups_table."
                            WHERE
                                edate = 0
                            {$activeOnlySQL}
                            ORDER BY pos"); safeCheck($items);
        return $items;
    }

}
