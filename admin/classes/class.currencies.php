<?php

class Currencies extends Settings {

    public $pagination = "";

    public static function getRecord(int $id) {
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
                                c.id = " . $id); safeCheck($row);
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

    /**
     * 
     * @global type $db
     * @global string $currencies_table
     * @param FilteredMap $params
     */
    public function addEditRow($params) {
        global $db;
        global $currencies_table;
        
        $act = $params->getString("act");
        $id = $params->getInt("id");
        
        $fields = array(
            'code'              => $params->getString('code'),
            'rate'              => $params->getNumber('rate'),
            'name_bg'           => $params->getString('name_bg'),
            'name_en'           => $params->getString('name_en'),
            'name_de'           => $params->getString('name_de'),
            'name_ru'           => $params->getString('name_ru'),
            'name_ro'           => $params->getString('name_ro'),
            'abbreviation_bg'   => $params->getString('abbreviation_bg'),
            'abbreviation_en'   => $params->getString('abbreviation_en'),
            'abbreviation_de'   => $params->getString('abbreviation_de'),
            'abbreviation_ru'   => $params->getString('abbreviation_ru'),
            'abbreviation_ro'   => $params->getString('abbreviation_ro'),
            'abb_before_amount' => $params->getInt('abb_before_amount') === 1 ? 1 : 0,
            'is_base'           => $params->getInt('is_base') === 1 ? 1 : 0,
            'active'            => $params->getInt('active') === 1 ? 1 : 0,
            'cms_user_id'       => $_SESSION["uid"]
        );

        $pic = copyImage($_FILES['pic'], "../files/", "../files/tn/", "../files/tntn/", "250x");
        if (!empty($pic))
            $fields['pic'] = $pic;

        if ($act == "add") {
            $fields["postdate"] = time();
            shiftPos($db, $currencies_table);
            $res = $db->autoExecute($currencies_table, $fields, DB_AUTOQUERY_INSERT); safeCheck($res);
            $id = mysqli_insert_id($db->connection);
        }

        if ($act == "edit") {
            $fields["updated_date"] = time();
            $id = $params->getInt("id");
            $res = $db->autoExecute($currencies_table, $fields, DB_AUTOQUERY_UPDATE, "id = " . $id); safeCheck($res);
        }
        
        return $id;
    }

    public function deleteField($id, $field) {
        global $db;
        global $currencies_table;

        $res = $db->autoExecute($currencies_table, array($field => ""), DB_AUTOQUERY_UPDATE, " id = '" . $id . "' ");
        safeCheck($res);

        return $res;
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

    public function deleteRecord($id) {
        global $db;
        global $currencies_table;

        $fields = array(
            "edate" => time(),
            "edate_cms_user_id" => $_SESSION["uid"]
        );
        $res = $db->autoExecute($currencies_table, $fields, DB_AUTOQUERY_UPDATE, " id = '" . $id . "' ");
        safeCheck($res);

        return $res;
    }

}
