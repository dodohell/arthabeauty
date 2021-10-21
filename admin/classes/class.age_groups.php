<?php

class AgeGroups extends Settings {

    public $pagination = "";

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

    /**
     * 
     * @global type $db
     * @global string $age_groups_table
     * @param FilteredMap $params
     */
    public function addEditRow($params) {
        global $db;
        global $age_groups_table;
        
        $act = $params->getString("act");
        $id = $params->getInt("id");
        
        $fields = array(
            'name_bg'           => $params->getString('name_bg'),
            'name_en'           => $params->getString('name_en'),
            'name_de'           => $params->getString('name_de'),
            'name_ru'           => $params->getString('name_ru'),
            'name_ro'           => $params->getString('name_ro'),
            'active'            => $params->getInt('active') === 1 ? 1 : 0,
            'cms_user_id'       => $_SESSION["uid"]
        );

        $pic = copyImage($_FILES['pic'], "../files/", "../files/tn/", "../files/tntn/", "250x");
        if (!empty($pic))
            $fields['pic'] = $pic;

        if ($act == "add") {
            $fields["postdate"] = time();
            shiftPos($db, $age_groups_table);
            $res = $db->autoExecute($age_groups_table, $fields, DB_AUTOQUERY_INSERT); safeCheck($res);
            $id = mysqli_insert_id($db->connection);
        }

        if ($act == "edit") {
            $fields["updated_date"] = time();
            $id = $params->getInt("id");
            $res = $db->autoExecute($age_groups_table, $fields, DB_AUTOQUERY_UPDATE, "id = " . $id); safeCheck($res);
        }
        
        return $id;
    }

    public function deleteField($id, $field) {
        global $db;
        global $age_groups_table;

        $res = $db->autoExecute($age_groups_table, array($field => ""), DB_AUTOQUERY_UPDATE, " id = '" . $id . "' ");
        safeCheck($res);

        return $res;
    }

    public function getAgeGroups($activeOnly = 0) {
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

    public function deleteRecord($id) {
        global $db;
        global $age_groups_table;

        $fields = array(
            "edate" => time(),
            "edate_cms_user_id" => $_SESSION["uid"]
        );
        $res = $db->autoExecute($age_groups_table, $fields, DB_AUTOQUERY_UPDATE, " id = '" . $id . "' ");
        safeCheck($res);

        return $res;
    }

}
