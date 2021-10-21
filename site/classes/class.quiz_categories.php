<?php

class QuizCategories extends Settings {

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
    
    public static function getQuizCategoriesBySex($sex, $returnType = 3) {
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
                            AND active = 1
                            AND sex = {$sex}
                            ORDER BY pos"); safeCheck($items);
        
        if($returnType == 3){
            echo json_encode($items);
            die();
        }
        
        return $items;
    }

}
