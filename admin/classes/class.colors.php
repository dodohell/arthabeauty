<?php

class Colors extends Settings {

    public $pagination = "";

    public static function getRecord(int $id) {
        global $db;
        global $lng;
        global $colors_table;

        $row = $db->getRow("SELECT 
                                c.*, 
                                c.name_$lng AS name
                            FROM
                                " . $colors_table . " AS c
                            WHERE 
                                c.id = " . $id); safeCheck($row);
        return $row;
    }

    /**
     * 
     * @global type $db
     * @global string $colors_table
     * @param FilteredMap $params
     */
    public function addEditRow($params) {
        global $db;
        global $colors_table;
        
        $act = $params->getString("act");
        $id = $params->getInt("id");
        
        $fields = array(
            'name_bg'           => $params->getString('name_bg'),
            'name_en'           => $params->getString('name_en'),
            'name_de'           => $params->getString('name_de'),
            'name_ru'           => $params->getString('name_ru'),
            'name_ro'           => $params->getString('name_ro'),
            'code'              => $params->getString('code'),
            'active'            => $params->getInt('active') === 1 ? 1 : 0,
            'cms_user_id'       => $_SESSION["uid"]
        );

        $pic = copyImage($_FILES['pic'], "../files/", "../files/tn/", "../files/tntn/", "250x");
        if (!empty($pic))
            $fields['pic'] = $pic;

        if ($act == "add") {
            $fields["postdate"] = time();
            shiftPos($db, $colors_table);
            $res = $db->autoExecute($colors_table, $fields, DB_AUTOQUERY_INSERT); safeCheck($res);
            $id = mysqli_insert_id($db->connection);
        }

        if ($act == "edit") {
            $fields["updated_date"] = time();
            $id = $params->getInt("id");
            $res = $db->autoExecute($colors_table, $fields, DB_AUTOQUERY_UPDATE, "id = " . $id); safeCheck($res);
        }
        
        return $id;
    }

    public function deleteField($id, $field) {
        global $db;
        global $colors_table;

        $res = $db->autoExecute($colors_table, array($field => ""), DB_AUTOQUERY_UPDATE, " id = '" . $id . "' ");
        safeCheck($res);

        return $res;
    }

    public function getColors($activeOnly = 0) {
        global $db;
        global $lng;
        global $colors_table;
        
        if ($activeOnly) {
            $activeOnlySQL = " AND active = 1 ";
        }
        
        $items = $db->getAll("SELECT
                                *,
                                name_{$lng} AS name
                            FROM
                                ".$colors_table."
                            WHERE
                                edate = 0
                            {$activeOnlySQL}
                            ORDER BY pos"); safeCheck($items);
        return $items;
    }

    public function deleteRecord($id) {
        global $db;
        global $colors_table;

        $fields = array(
            "edate" => time(),
            "edate_cms_user_id" => $_SESSION["uid"]
        );
        $res = $db->autoExecute($colors_table, $fields, DB_AUTOQUERY_UPDATE, " id = '" . $id . "' ");
        safeCheck($res);

        return $res;
    }

}
