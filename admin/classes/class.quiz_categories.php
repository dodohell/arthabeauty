<?php

class QuizCategories extends Settings {

    public $pagination = "";

    public static function getRecord(int $id) {
        global $db;
        global $lng;
        global $quiz_categories_table;

        $row = $db->getRow("SELECT 
                                c.*, 
                                c.name_$lng AS name
                            FROM
                                " . $quiz_categories_table . " AS c
                            WHERE 
                                c.id = " . $id); safeCheck($row);
        return $row;
    }

    /**
     * 
     * @global type $db
     * @global string $quiz_categories_table
     * @param FilteredMap $params
     */
    public function addEditRow($params) {
        global $db;
        global $quiz_categories_table;
        
        $act = $params->getString("act");
        $id = $params->getInt("id");
        
        $fields = array(
            'sex'               => $params->getInt('sex'),
            'name_bg'           => $params->getString('name_bg'),
            'name_en'           => $params->getString('name_en'),
            'name_de'           => $params->getString('name_de'),
            'name_ru'           => $params->getString('name_ru'),
            'name_ro'           => $params->getString('name_ro'),
            'description_bg'    => $params->getString('description_bg'),
            'description_en'    => $params->getString('description_en'),
            'description_de'    => $params->getString('description_de'),
            'description_ru'    => $params->getString('description_ru'),
            'description_ro'    => $params->getString('description_ro'),
            'active'            => $params->getInt('active'),
            'cms_user_id'       => $_SESSION["uid"]
        );

        $pic = copyImage($_FILES['pic'], "../files/", "../files/tn/", "../files/tntn/", "250x");
        if (!empty($pic))
            $fields['pic'] = $pic;

        if ($act == "add") {
            $fields["postdate"] = time();
            shiftPos($db, $quiz_categories_table);
            $res = $db->autoExecute($quiz_categories_table, $fields, DB_AUTOQUERY_INSERT); safeCheck($res);
            $id = mysqli_insert_id($db->connection);
        }

        if ($act == "edit") {
            $fields["updated_date"] = time();
            $id = $params->getInt("id");
            $res = $db->autoExecute($quiz_categories_table, $fields, DB_AUTOQUERY_UPDATE, "id = " . $id); safeCheck($res);
        }

        return $id;
    }

    public function deleteField($id, $field) {
        global $db;
        global $quiz_categories_table;

        $res = $db->autoExecute($quiz_categories_table, array($field => ""), DB_AUTOQUERY_UPDATE, " id = '" . $id . "' ");
        safeCheck($res);

        return $res;
    }

    public function getQuizCategories($activeOnly = 0) {
        global $db;
        global $lng;
        global $quiz_categories_table;
        global $quiz_questions_table;
        
        if ($activeOnly) {
            $activeOnlySQL = " AND qc.active = 1 ";
        }
        
        $items = $db->getAll("SELECT
                                qc.*,
                                qc.name_{$lng} AS name,
                                (SELECT
                                    COUNT(qq.id) AS cntr
                                FROM
                                    {$quiz_questions_table} AS qq
                                WHERE
                                    qc.id = qq.quiz_category_id
                                AND qq.edate = 0) AS quiz_questions_count
                            FROM
                                ".$quiz_categories_table." AS qc
                            WHERE
                                qc.edate = 0
                            {$activeOnlySQL}
                            ORDER BY qc.pos"); safeCheck($items);
        return $items;
    }
    
    public static function getQuizCategoriesAll() {
        global $db;
        global $lng;
        global $quiz_categories_table;
        
        $items = $db->getAll("SELECT
                                *,
                                name_{$lng} AS name
                            FROM
                                ".$quiz_categories_table."
                            WHERE
                                edate = 0
                            ORDER BY pos"); safeCheck($items);
        return $items;
    }

    public function deleteRecord($id) {
        global $db;
        global $quiz_categories_table;

        $fields = array(
            "edate" => time(),
            "cms_user_id" => $_SESSION["uid"]
        );
        $res = $db->autoExecute($quiz_categories_table, $fields, DB_AUTOQUERY_UPDATE, " id = '" . $id . "' "); safeCheck($res);

        return $res;
    }

}
