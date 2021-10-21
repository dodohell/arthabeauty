<?php

class EmailNotifications extends Settings {

    public function sendLatestNews($email = "", $count = 5) {
        global $sm;
        global $host;
        
        $news = new News();
        $latestNews = $news->getLatestNews($count);
        $message_heading = "Най-новото от Wedding Day";
        $sm->assign("email", $email);
        $sm->assign("news", $latestNews);
        $sm->assign("host", $host);
        $sm->assign("message_heading", $message_heading);
        
        $message_text = $sm->fetch('email_last_news.html');
//        $this->mailSender("kkalchev@development-bg.com", $message_heading, $message_text);
//        $this->mailSender("gergana@development-bg.com", $message_heading, $message_text); exit;
        return $this->mailSender($email, $message_heading, $message_text);
	}
    
    public function sendNewsToRecipients() {
        global $db;
        global $email_notifications_table;

        $recipients = $this->mailRecipients();

        foreach ($recipients as $k => $v) {
            $recipient = $db->getRow("SELECT DISTINCT email FROM " . $email_notifications_table . " WHERE email = '" . $v['email'] . "' AND unsubscribe_news = 0 "); safeCheck($recipient);
 
            if ($recipient) {
                //$result = 1;
                $result = $this->sendLatestNews($recipient["email"]);
                
                if ($result) {
                    $fields = array(
                        "postdate_news" => time()
                    );
                    $res = $db->autoExecute($email_notifications_table, $fields, DB_AUTOQUERY_UPDATE, " email = '" . $recipient['email'] . "' "); safeCheck($res);
                } else {
                    $fields = array(
                        "failed_news" => 1
                    );
                    $res = $db->autoExecute($email_notifications_table, $fields, DB_AUTOQUERY_UPDATE, " email = '" . $recipient['email'] . "' "); safeCheck($res);
                }
            }
           
        }
    }
    
    public function mailRecipients() {
        global $db;
        global $email_notifications_table;

        $recipients = $db->getAll("SELECT DISTINCT email FROM " . $email_notifications_table . " WHERE email IS NOT NULL"); safeCheck($recipients);

        return $recipients;
    }
    
    public function unsubscribe($email, $type) {
        global $db;
        global $email_notifications_table;
        
        if($type == "customers-profiles" ){
            $fields = array("unsubscribe_customers_profiles" => 1);
            $res = $db->autoExecute($email_notifications_table, $fields, DB_AUTOQUERY_UPDATE, " email = '" .$email. "' AND unsubscribe_customers_profiles = 0 "); safeCheck($res);
        }
        if($type == "news" ){
            $fields = array("unsubscribe_news" => 1);
            $res = $db->autoExecute($email_notifications_table, $fields, DB_AUTOQUERY_UPDATE, " email = '" .$email. "' AND unsubscribe_news = 0 "); safeCheck($res);
        }
        
        header("Location: /messages/3300");
        die();
    }
    
    public function sendCustomersProfilesToRecipients() {
        global $db;
        global $email_notifications_table;

        $recipients = $this->mailRecipients();

        foreach ($recipients as $k => $v) {
            $recipient = $db->getRow("SELECT id, email, category_id, city_id FROM " . $email_notifications_table . " WHERE email = '" . $v['email'] . "' AND unsubscribe_customers_profiles = 0 AND sent = 0"); safeCheck($recipient);
 
            if ($recipient["category_id"]) {
                //$result = 1;
                $result = $this->sendCustomersProfiles($recipient["category_id"], $recipient["city_id"], $recipient["email"]);
                
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
    
    public function sendCustomersProfiles($category_id, $city_id = 0, $recipient_email = "", $count = 5) {
        global $sm;
        global $host;

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

//        $sm->display("email_customers_profiles.html"); exit;
        $message_text = $sm->fetch('email_customers_profiles.html');
//        $this->mailSender("kkalchev@development-bg.com", $message_heading, $message_text);
//        $this->mailSender("kkalchev@development-bg.com", $message_heading, $message_text); exit;
        return $this->mailSender($recipient_email, $message_heading, $message_text); 
    }
    
    public function mailSenderCustom($email, $message_heading, $message_text, $file_attachment_1 = "", $file_attachment_2 = "", $file_attachment_3 = "") {
        global $install_path;
        
        require_once($install_path."phpmailer/class.phpmailer.php");

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

        return !$mailObj->Send() ? false : true;
    }

	public function updateTable($param) {
        global $db;
        global $requests_to_customers_table;
        global $email_notifications_table;
        global $users_table;

        $requestCategoryEmails = $db->getAll("SELECT DISTINCT email, category_id, city_id, user_id FROM " . $requests_to_customers_table . " WHERE email IS NOT NULL AND direct_request = 0 AND category_id > 0 AND lang = 'bg' AND postdate >= '".(time()-86400)."' "); safeCheck($requestCategoryEmails);
        //$requestCategoryEmails = $db->getAll("SELECT DISTINCT email, category_id, city_id, user_id FROM " . $requests_to_customers_table . " WHERE email IS NOT NULL AND direct_request = 0 AND category_id > 0 AND lang = 'bg' "); safeCheck($requestCategoryEmails);
        $usersEmails = $db->getAll("SELECT DISTINCT email
                                        FROM $users_table
                                        WHERE register_language = 'bg'
                                        AND edate = 0
                                        AND active = 1
                                        AND register_date >= '".(time()-86400)."' "); safeCheck($usersEmails);
        $DataArr = array();
        
        if (is_array($requestCategoryEmails)) {
            foreach ($requestCategoryEmails as $row) {
                $email = mysql_real_escape_string($row["email"]);
                $category_id = mysql_real_escape_string($row["category_id"]);
                $city_id = mysql_real_escape_string($row["city_id"]);
                $user_id = mysql_real_escape_string($row["user_id"]);

                $DataArr[] = "('$email', '$category_id', '$city_id', '$user_id', '".time()."')";
            }
        }
        
        if (is_array($usersEmails)) {
            $emails_ready = [];
            foreach ($usersEmails as $k => $v) {
                $match = 0;
                foreach ($requestCategoryEmails as $kk => $vv) {
                    if($v["email"] == $vv["email"]){
                        $match = 1;
                    }
                }
                if ( $match == 0 ){
                    if ( trim($v["email"]) ){
                        $emails_ready[] = $v["email"];
                    }
                }
            }
            
            foreach ($emails_ready as $eRow){
                $email = mysql_real_escape_string($eRow);
                $category_id = NULL;
                $city_id = NULL;
                $user_id = NULL;

                $DataArr[] = "('$email', '$category_id', '$city_id', '$user_id', '".time()."')";
            }
        }
        
        if($DataArr){
            $sql = "INSERT IGNORE INTO $email_notifications_table (email, category_id, city_id, user_id, postdate) values ";
            $sql .= implode(',', $DataArr);
            
            $res = $db->Query($sql); safeCheck($res);
        }
        
    }
	
	function sendUnansweredGenerators(){
		global $db;
		global $emails_test;
		global $requests_to_customers_table;
		global $requests_to_customers_offers_table;
		
		$since_when = time()-(86400 * 3);
		
		$all = $db->getAll("SELECT * FROM ".$requests_to_customers_table." WHERE edate = 0 AND sent = 1 AND status != 3 AND reminder_customer_sent = 0 AND FROM_UNIXTIME(postdate, '%Y-%m-%d') = '".date("Y-m-d", $since_when)."' ORDER BY postdate DESC");
		
		$settingsObj = new Settings();
		$customersObj = new Customers();
		foreach( $all as $k => $v ){
			$tmp = $db->getRow("SELECT * FROM ".$requests_to_customers_offers_table." WHERE request_id = '".$v["id"]."'"); safeCheck($tmp);
			
			if ( !$tmp["id"] ){
				$customer = $customersObj->getRecord($v["customer_id"]);
				if ($v["lang"] == "ro") {
					$subject = "Aveţi o cerere de la acest utilizator";
				} else {
					$subject = "Потребител Ви изпрати запитване";
				}
				
				$res = $db->autoExecute($requests_to_customers_table, array("reminder_customer_sent" => 1, "reminder_customer_sent_on" => time()), DB_AUTOQUERY_UPDATE, " id = '".$v["id"]."' "); safeCheck($res);
				
				
				
				$settingsObj->mailSender($customer["contact_email"], $subject, $v["message"], "", "", "", array(), array("agent@weddingday.bg"));
				
				foreach ($emails_test as $kk => $vv) {
					$settingsObj->mailSender($vv, $subject, $v["message"], "", "", "", array(), array($v["email"], "agent@weddingday.bg"));
				}
			}
		}
		
	}
	
	function sendUnansweredGeneratorsNotification(){
		global $db;
		global $emails_test;
		global $requests_to_customers_table;
		global $requests_to_customers_offers_table;
		
		$since_when = time()-(86400 * 2);
		
		$all = $db->getAll("SELECT * FROM ".$requests_to_customers_table." WHERE edate = 0 AND sent = 1 AND status != 3 AND reminder_customer_sent = 1 AND reminder_customer_final_sent = 0 AND FROM_UNIXTIME(reminder_customer_sent_on, '%Y-%m-%d') = '".date("Y-m-d", $since_when)."' ORDER BY postdate DESC");
		
		$settingsObj = new Settings();
		$customersObj = new Customers();
		foreach( $all as $k => $v ){
			$tmp = $db->getRow("SELECT * FROM ".$requests_to_customers_offers_table." WHERE request_id = '".$v["id"]."'"); safeCheck($tmp);
			
			if ( !$tmp["id"] ){
				$customer = $customersObj->getRecord($v["customer_id"]);
				
				$subject = "Сигнал за генератор №".$v["temp_id"];
				
				if ( $v["lang"] == "bg" ){
					$customer_link = "https://www.weddingday.bg/customerbg/".$v["customer_id"];
				}else{
					$customer_link = "https://www.miresesinunti.ro/customerro/".$v["customer_id"];
				}
				
				$message = 'Клиент <a href="'.$customer_link.'">'.$customer["name"].'</a> не отговори на запитване от '.date("d/m/Y", $v["postdate"]).' дори и след изпращане на повторно запитване от наша страна.<br />
							<a href="https://www.weddingday.bg/admin/customers_request_generator_ae.php?act=edit&temp_id='.$v["temp_id"].'&email='.$v["email"].'&without_sent=1">ВИЖ В АДМИНА</a>';
				
				$res = $db->autoExecute($requests_to_customers_table, array("reminder_customer_final_sent" => 1, "reminder_customer_final_sent_on" => time()), DB_AUTOQUERY_UPDATE, " id = '".$v["id"]."' "); safeCheck($res);
				
				foreach ($emails_test as $kk => $vv) {
					$settingsObj->mailSender($vv, $subject, $message, "", "", "", array(), array($v["email"], "agent@weddingday.bg"));
				}
			}
		}
		
	}
	
	
}

?>