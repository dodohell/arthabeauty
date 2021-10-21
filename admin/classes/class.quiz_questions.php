<?php

class QuizQuestions extends Settings {

    public $pagination = "";

    public static function getRecord(int $id) {
        global $db;
        global $lng;
        global $quiz_questions_table;

        $row = $db->getRow("SELECT 
                                c.*, 
                                c.name_$lng AS name
                            FROM
                                " . $quiz_questions_table . " AS c
                            WHERE 
                                c.id = " . $id); safeCheck($row);
        return $row;
    }

    /**
     * 
     * @global type $db
     * @global string $quiz_questions_table
     * @param FilteredMap $params
     */
    public function addEditRow($params) {
        global $db;
        global $quiz_questions_table;
        
        $act = $params->getString("act");
        $id = $params->getInt("id");
        
        $fields = array(
            'quiz_category_id'  => $params->getInt('quiz_category_id'),
            'name_bg'           => $params->getString('name_bg'),
            'name_en'           => $params->getString('name_en'),
            'name_de'           => $params->getString('name_de'),
            'name_ru'           => $params->getString('name_ru'),
            'name_ro'           => $params->getString('name_ro'),
            'active'            => $params->getInt('active') === 1 ? 1 : 0,
            'multiple'            => $params->getInt('multiple') === 1 ? 1 : 0,
            'cms_user_id'       => $_SESSION["uid"]
        );

//        $pic = copyImage($_FILES['pic'], "../files/", "../files/tn/", "../files/tntn/", "250x");
//        if (!empty($pic))
//            $fields['pic'] = $pic;

        if ($act == "add") {
            //$fields["type_id"] = $params->getInt('type_id');
            $fields["postdate"] = time();
            shiftPos($db, $quiz_questions_table);
            $res = $db->autoExecute($quiz_questions_table, $fields, DB_AUTOQUERY_INSERT); safeCheck($res);
            $id = mysqli_insert_id($db->connection);
        }

        if ($act == "edit") {
            $fields["updated_date"] = time();
            $id = $params->getInt("id");
            $res = $db->autoExecute($quiz_questions_table, $fields, DB_AUTOQUERY_UPDATE, "id = " . $id); safeCheck($res);
        }
        
        return $id;
    }

    public function deleteField($id, $field) {
        global $db;
        global $quiz_questions_table;

        $res = $db->autoExecute($quiz_questions_table, array($field => ""), DB_AUTOQUERY_UPDATE, " id = '" . $id . "' "); safeCheck($res);

        return $res;
    }
    
    public function deleteOptionField($id, $field) {
        global $db;
        global $quiz_questions_options_table;

        $res = $db->autoExecute($quiz_questions_options_table, array($field => ""), DB_AUTOQUERY_UPDATE, " id = '" . $id . "' "); safeCheck($res);

        return $res;
    }

    public function getQuizQuestions($activeOnly = 0) {
        global $db;
        global $lng;
        global $quiz_questions_table;
        
        if ($activeOnly) {
            $activeOnlySQL = " AND active = 1 ";
        }
        
        $items = $db->getAll("SELECT
                                *,
                                name_{$lng} AS name
                            FROM
                                ".$quiz_questions_table."
                            WHERE
                                edate = 0
                            {$activeOnlySQL}
                            ORDER BY pos"); safeCheck($items);
        return $items;
    }

    public function deleteRecord($id) {
        global $db;
        global $quiz_questions_table;
        
        $res = false;
        $check = $this->getRecord($id);
        if($check["type_id"] > 2){
            $fields = array(
                "edate" => time(),
                "cms_user_id" => $_SESSION["uid"]
            );
            $res = $db->autoExecute($quiz_questions_table, $fields, DB_AUTOQUERY_UPDATE, " id = '" . $id . "' "); safeCheck($res);
        }

        return $res;
    }
    
     public static function getTypes(int $activeOnly = 1) {
        global $db;
        global $lng;
        global $quiz_questions_types_table;
        
        if ($activeOnly === 1) {
            $activeOnlySQL = " AND active = 1 ";
        }
        
        $items = $db->getAll("SELECT
                                *,
                                name_{$lng} AS name
                            FROM
                                ".$quiz_questions_types_table."
                            WHERE
                                edate = 0
                            {$activeOnlySQL}
                            ORDER BY pos"); safeCheck($items);
        return $items;
    }
}
