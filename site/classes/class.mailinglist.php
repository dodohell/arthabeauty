<?php

class Mailinglist extends Settings {

    public static function getRecord(int $id) {
        global $db;
        global $mailinglist_table;

        $row = $db->getRow("SELECT * FROM " . $mailinglist_table . " WHERE id = " . $id); safeCheck($row);

        return $row;
    }
    
    public static function getRecordByEmail($email) {
        global $db;
        global $mailinglist_table;
        
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            return false;
        }
        
        $row = $db->getRow("SELECT * FROM " . $mailinglist_table . " WHERE email = '" . $email . "' AND edate = 0"); safeCheck($row);
        
        return $row;
    }

    public function getMails() {
        global $db;
        global $mailinglist_table;

        $res = $db->getAll("SELECT 
                                *
                            FROM " . $mailinglist_table . " 
                            WHERE 
                              edate = 0
                            ORDER BY postdate"); safeCheck($res);
        return $res;
    }
    
    public function getActiveMails() {
        global $db;
        global $mailinglist_table;

        $res = $db->getAll("SELECT 
                            *
                            FROM " . $mailinglist_table . " 
                            WHERE 
                                edate = 0
                            AND active = 1
                            ORDER BY postdate"); safeCheck($res);
        return $res;
    }

    public function mailinglistSubscribe($email, $return_type = 3) {
        global $db;
        global $sm;
        global $mailinglist_table;
        global $language_file;
        
        $sm->configLoad($language_file);
        $langVars = $sm->getConfigVars();
        
        $result = array();
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            if($return_type === 3){
                $result["code"] = 0;
                $result["message"] = $langVars["invalid_email_address"];
                echo json_encode($result);
                die();
            }else{
                return $result;
            }
        }
        
        $fields = array(
            "email" => $email,
            "agree_terms" => 1,
            "agree_terms_gdpr" => 1,
            "active" => 1,
        );
        
        $check = self::getRecordByEmail($email);
        if($check){
            if($check["active"] == 1){
                $result["code"] = 0;
                $result["message"] = $langVars["already_subscribed"];
            }else if($check["active"] == 0){
                $fields["`update`"] = time();
                $fields["ip_update"] = $_SERVER["REMOTE_ADDR"];
                $res = $db->autoExecute($mailinglist_table, $fields, DB_AUTOQUERY_UPDATE, " id = " . $check["id"]); safeCheck($res);
                $result["code"] = 2;
                $result["message"] = $langVars["subscription_activated"];
            }
        }else{
            $fields["postdate"] = time();
            $fields["ip"] = $_SERVER["REMOTE_ADDR"];
            $res = $db->autoExecute($mailinglist_table, $fields, DB_AUTOQUERY_INSERT); safeCheck($res);
            $result["code"] = 1;
            $result["message"] = $langVars["subscription_successful"];
        }
        if($return_type === 3){
            echo json_encode($result);
            die();
        }else{
            return $result;
        }
    }
    public function mailinglistSubscribeHome($email) {
        global $db;
        global $sm;
        global $newsletter_emails_table;
        global $language_file;
        
        $sm->configLoad($language_file);
        $langVars = $sm->getConfigVars();
        $result = array();
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
                $result["message"] = $langVars["invalid_email_address"];
                echo json_encode($result);
                die();
        }
        
        $exists = $db->getRow("SELECT
                                    id
                                FROM
                                    {$newsletter_emails_table}
                                WHERE
                                    email = '{$email}'
                                AND edate = 0
                            ");
        if ($exists) {
            $result["message"] = $langVars["email_address_exists"];
            echo json_encode($result);
            die();
        }
        
        $fields = array(
            "email" => $email
        );

        $fields["postdate"] = time();
        $fields["ip_address"] = $_SERVER["REMOTE_ADDR"];
        $res = $db->autoExecute($newsletter_emails_table, $fields, DB_AUTOQUERY_INSERT); safeCheck($res);
        $result["message"] = $langVars["subscription_successful"];
        echo json_encode($result);
    }
}
