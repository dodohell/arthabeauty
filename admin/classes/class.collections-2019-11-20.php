<?php

class Collections extends Settings {

    public $pagination = "";

    public static function getRecord(int $id) {
        global $db;
        global $lng;
        global $collections_table;

        $row = $db->getRow("SELECT 
                                c.*, 
                                c.name_$lng AS name
                            FROM
                                " . $collections_table . " AS c
                            WHERE 
                                c.id = " . $id); safeCheck($row);
        return $row;
    }

    /**
     * 
     * @global type $db
     * @global string $collections_table
     * @param FilteredMap $params
     */
    public function addEditRow($params) {
        global $db;
        global $collections_table;
        
        $act = $params->getString("act");
        $id = $params->getInt("id");
        $brand_id = $params->getInt('brand_id');
        
        $fields = array(
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
            'brand_id'          => $brand_id > 0 ? $brand_id : 0,
            'url_bg'            => $params->getString('url_bg'),
            'url_en'            => $params->getString('url_en'),
            'url_de'            => $params->getString('url_de'),
            'url_ru'            => $params->getString('url_ru'),
            'url_ro'            => $params->getString('url_ro'),
            'htaccess_url_bg'   => $params->getString('htaccess_url_bg'),
            'htaccess_url_en'   => $params->getString('htaccess_url_en'),
            'htaccess_url_de'   => $params->getString('htaccess_url_de'),
            'htaccess_url_ru'   => $params->getString('htaccess_url_ru'),
            'htaccess_url_ro'   => $params->getString('htaccess_url_ro'),
            'meta_title_bg'     => $params->getString('meta_title_bg'),
            'meta_title_en'     => $params->getString('meta_title_en'),
            'meta_title_de'     => $params->getString('meta_title_de'),
            'meta_title_ru'     => $params->getString('meta_title_ru'),
            'meta_title_ro'     => $params->getString('meta_title_ro'),
            'meta_description_bg' => $params->getString('meta_description_bg'),
            'meta_description_en' => $params->getString('meta_description_en'),
            'meta_description_de' => $params->getString('meta_description_de'),
            'meta_description_ru' => $params->getString('meta_description_ru'),
            'meta_description_ro' => $params->getString('meta_description_ro'),
            'meta_keywords_bg'  => $params->getString('meta_keywords_bg'),
            'meta_keywords_en'  => $params->getString('meta_keywords_en'),
            'meta_keywords_de'  => $params->getString('meta_keywords_de'),
            'meta_keywords_ru'  => $params->getString('meta_keywords_ru'),
            'meta_keywords_ro'  => $params->getString('meta_keywords_ro'),
            'meta_metatags_bg'  => $params->getString('meta_metatags_bg'),
            'meta_metatags_en'  => $params->getString('meta_metatags_en'),
            'meta_metatags_de'  => $params->getString('meta_metatags_de'),
            'meta_metatags_ru'  => $params->getString('meta_metatags_ru'),
            'meta_metatags_ro'  => $params->getString('meta_metatags_ro'),
            'website'           => $params->getString('website'),
            'first_page'        => $params->getInt('first_page'),
            'active'            => $params->getInt('active'),
            'cms_user_id'       => $_SESSION["uid"]
        );

        $pic = copyImage($_FILES['pic'], "../files/", "../files/tn/", "../files/tntn/", "250x");
        if (!empty($pic))
            $fields['pic'] = $pic;

        if ($act == "add") {
            $fields["postdate"] = time();
            shiftPos($db, $collections_table);
            $res = $db->autoExecute($collections_table, $fields, DB_AUTOQUERY_INSERT); safeCheck($res);
            $id = mysqli_insert_id($db->connection);
        }

        if ($act == "edit") {
            $fields["updated_date"] = time();
            $id = $params->getInt("id");
            $res = $db->autoExecute($collections_table, $fields, DB_AUTOQUERY_UPDATE, "id = " . $id); safeCheck($res);
        }

        $htaccessUpdate = new Settings();
        $htaccess_type = "collections";

        if ($params->getString("htaccess_url_bg")) {
            $htaccessUpdate->updateHtaccess("bg", $params->getString("htaccess_url_bg"), $htaccess_type, $id);
        }
        if ($params->getString("htaccess_url_en")) {
            $htaccessUpdate->updateHtaccess("en", $params->getString("htaccess_url_en"), $htaccess_type, $id);
        }
        if ($params->getString("htaccess_url_de")) {
            $htaccessUpdate->updateHtaccess("de", $params->getString("htaccess_url_de"), $htaccess_type, $id);
        }
        if ($params->getString("htaccess_url_ru")) {
            $htaccessUpdate->updateHtaccess("ru", $params->getString("htaccess_url_ru"), $htaccess_type, $id);
        }
        if ($params->getString("htaccess_url_ro")) {
            $htaccessUpdate->updateHtaccess("ro", $params->getString("htaccess_url_ro"), $htaccess_type, $id);
        }

        return $id;
    }

    public function deleteField($id, $field) {
        global $db;
        global $collections_table;

        $res = $db->autoExecute($collections_table, array($field => ""), DB_AUTOQUERY_UPDATE, " id = '" . $id . "' ");
        safeCheck($res);

        return $res;
    }

    public function getCollections($activeOnly = 0) {
        global $db;
        global $lng;
        global $collections_table;
        
        if ($activeOnly) {
            $activeOnlySQL = " AND active = 1 ";
        }
        
        $items = $db->getAll("SELECT
                                id,
                                name_{$lng} AS name,
                                active
                            FROM
                                ".$collections_table."
                            WHERE
                                edate = 0
                            {$activeOnlySQL}
                            ORDER BY pos"); safeCheck($items);
        return $items;
    }
    
    public static function getCollectionsByBrandId($brand_id) {
        global $db;
        global $lng;
        global $collections_table;
        
        $items = array();
        if($brand_id > 0){
            $items = $db->getAll("SELECT 
                                    collections.*,
                                    collections.name_{$lng} AS name
                                FROM ".$collections_table." AS collections
                                WHERE 
                                    collections.edate = 0 
                                AND collections.brand_id = '".$brand_id."'
                                ORDER BY collections.pos"); safeCheck($items);
        }
        return $items;
    }

    public function deleteRecord($id) {
        global $db;
        global $collections_table;

        $fields = array(
            "edate" => time(),
            "edate_cms_user_id" => $_SESSION["uid"]
        );
        $res = $db->autoExecute($collections_table, $fields, DB_AUTOQUERY_UPDATE, " id = '" . $id . "' ");
        safeCheck($res);

        return $res;
    }

}
