<?php

class EmailNotifications extends Settings {

    public $pagination = "";

    function getRecord($id = 0) {
        global $db;
        global $email_notifications_table;

        $id = (int) $id;

        $row = $db->getRow("SELECT * FROM " . $email_notifications_table . " WHERE id = '" . $id . "'");
        safeCheck($row);

        return $row;
    }

    function addEditRow($params) {
        global $db;
        global $email_notifications_table;

        $act = $params["act"];
        $id = (int) $params["id"];
        $fields = array(
            'email' => htmlspecialchars(trim($params['email'])),
            'category_id' => (int) $params['category_id'],
            'user_id' => (int) $params['user_id'],
            'sent' => (int) $params['sent'],
            "postdate" => (int) $params['postdate']
        );

        if ($act == "add") {
            shiftPos($db, $email_notifications_table);
            $res = $db->autoExecute($email_notifications_table, $fields, DB_AUTOQUERY_INSERT);
            safeCheck($res);
            $id = mysqli_insert_id($db->connection);
        }

        if ($act == "edit") {
            $id = (int) $params["id"];
            $res = $db->autoExecute($email_notifications_table, $fields, DB_AUTOQUERY_UPDATE, "id = " . $id);
            safeCheck($res);
        }
    }

    public function updateTable($param) {
        global $db;
        global $requests_to_customers_table;
        global $email_notifications_table;

        $requestCategoryEmails = $db->getAll("SELECT DISTINCT email, category_id, city_id, user_id FROM " . $requests_to_customers_table . " WHERE email IS NOT NULL AND direct_request = 0 AND category_id > 0 AND lang = 'bg'"); safeCheck($requestCategoryEmails);

        if (is_array($requestCategoryEmails)) {
            $DataArr = array();
            foreach ($requestCategoryEmails as $row) {
                $email = mysql_real_escape_string($row["email"]);
                $category_id = mysql_real_escape_string($row["category_id"]);
                $city_id = mysql_real_escape_string($row["city_id"]);
                $user_id = mysql_real_escape_string($row["user_id"]);

                $DataArr[] = "('$email', '$category_id', '$city_id', '$user_id')";
            }

            $sql = "INSERT IGNORE INTO $email_notifications_table (email, category_id, city_id, user_id) values ";
            $sql .= implode(',', $DataArr);

            $res = $db->Query($sql); safeCheck($res);
        }
    }

    function deleteField($id, $field) {
        global $db;
        global $lng;
        global $email_notifications_table;

        $res = $db->autoExecute($email_notifications_table, array($field => ""), DB_AUTOQUERY_UPDATE, " id = '" . $id . "' ");
        safeCheck($res);

        return $res;
    }

    function getEmailNotifications($options = array()) {
        global $db;
        global $lng;
        global $email_notifications_table;

        $email_notifications = $db->getAll("SELECT * FROM " . $email_notifications_table);
        safeCheck($email_notifications);

        return $email_notifications;
    }

    public function sendLatestNews($count = 5) {
        global $sm;
        global $host;

        $news = new News();
        $latestNews = $news->getLatestNews($count);
        $message_heading = "Най-новото от Weddind Day";
        $sm->assign("news", $latestNews);
        $sm->assign("host", $host);
        $sm->assign("message_heading", $message_heading);

        $message_text = $sm->fetch('email_last_news.html');
        $this->mailSender("gergana@development-bg.com", $message_heading, $message_text);
        $this->mailSender("kkalchev@development-bg.com", $message_heading, $message_text);
    }

    public function sendCustomersProfilesToRecipients() {
        global $db;
        global $email_notifications_table;
//        ini_set("display_errors", 1);
//        error_reporting(E_ALL & ~(E_STRICT|E_NOTICE|E_DEPRECATED|E_WARNING));
        $recipients = $this->mailRecipients();

        foreach ($recipients as $k => $v) {
            $recipient = $db->getRow("SELECT id, email, category_id, city_id FROM " . $email_notifications_table . " WHERE email = '" . $v['email'] . "' AND sent = 0"); safeCheck($recipient);
 
            if ($recipient) {
                $result = 1;
                $result = $this->sendCustomersProfiles($recipient["category_id"], $recipient["email"], $recipient["city_id"]);
                
                if ($result) {
                    $fields = array(
                        "sent" => 1,
                        "postdate" => time()
                    );
                    $res = $db->autoExecute($email_notifications_table, $fields, DB_AUTOQUERY_UPDATE, " id = '" . $recipient['id'] . "' "); safeCheck($res);
                } else {
                    $fields = array(
                        "failed" => 1
                    );
                    $res = $db->autoExecute($email_notifications_table, $fields, DB_AUTOQUERY_UPDATE, " id = '" . $recipient['id'] . "' "); safeCheck($res);
                }
            }
           
        }
    }

    public function mailRecipients() {
        global $db;
        global $email_notifications_table;

        $recipients = $db->getAll("SELECT DISTINCT email FROM " . $email_notifications_table . " WHERE email IS NOT NULL");
        safeCheck($recipients);

        return $recipients;
    }

    public function sendCustomersProfiles($category_id, $recipient_email, $city_id = 0, $count = 5) {
        global $sm;
        global $host;
//dbg($category_id." ".$recipient_email." ".$city_id." ".$count ); exit;
        if(!is_numeric($category_id)){
            throw new Exception("category_id must be numeric");
        }
        if(!is_numeric($city_id)){
            throw new Exception("city_id must be numeric");
        }
        
        $categoriesObj = new Categories();
        $category = $categoriesObj->getRecord($category_id);
        $sort_by = "price_min";
        if ( $category["sort_by_price_min"] ){
                $sort_by = "price_min";
        }elseif ( $category["sort_by_price_max"] ){
                $sort_by = "price_max";
        }elseif ( $category["sort_by_price_plate"] ){
                $sort_by = "price_plate";
        }elseif ( $category["sort_by_price_piece_min"] ){
                $sort_by = "price_piece_min";
        }elseif ( $category["sort_by_pos"] ){
                $sort_by = "pos";
        }
        
        $sort_by_type = "ASC";
        
        $customersObj = new Customers();
        $customers = $customersObj->getCustomers(array("category_id" => (int)$category_id, "city_id" => (int)$city_id, "limit" => $count, "sort_by" => $sort_by, "sort_by_type" => $sort_by_type));
        
        $message_heading = "Предложенията на Wedding Day за ". mb_strtolower($category["name"]);
        $sm->assign("categoryName", $category["name"]);
        $sm->assign("recipientEmail", $recipient_email);
        $sm->assign("customers", $customers["customers"]);
        $sm->assign("host", $host);
        $sm->assign("message_heading", $message_heading);
        //var_dump($customers); exit;
        $sm->display("email_customers_profiles.html"); exit;
        $message_text = $sm->fetch('email_customers_profiles.html');
        $this->mailSender("gergana@development-bg.com", $message_heading, $message_text);
        //$this->mailSender("support@weddingday.bg", $message_heading, $message_text);
        return $this->mailSender("kkalchev@development-bg.com", $message_heading, $message_text);
    }

    public function mailSender($email, $message_heading, $message_text, $file_attachment_1 = "", $file_attachment_2 = "", $file_attachment_3 = "") {
        global $install_path;

        require_once($install_path . "phpmailer/class.phpmailer.php");

        $mailObj = new PHPMailer();
        $mailObj->CharSet = 'utf-8';
        $mailObj->From = 'support@weddingday.bg';
        $mailObj->isHTML(true);
        $mailObj->FromName = 'Weddingday.bg';
        $mailObj->Subject = $message_heading;
        $mailObj->Body = $message_text;
        $mailObj->AddAddress($email);

        if ($file_attachment_1) {
            $file_to_attach = $install_path . 'files/emails/' . $file_attachment_1;
            $mailObj->AddAttachment($file_to_attach, $file_attachment_1);
        }

        if ($file_attachment_2) {
            $file_to_attach = $install_path . 'files/emails/' . $file_attachment_2;
            $mailObj->AddAttachment($file_to_attach, $file_attachment_2);
        }

        if ($file_attachment_3) {
            $file_to_attach = $install_path . 'files/emails/' . $file_attachment_3;
            $mailObj->AddAttachment($file_to_attach, $file_attachment_3);
        }

        return $mailObj->Send();
    }

}

?>