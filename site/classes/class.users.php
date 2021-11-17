<?php

class Users extends Settings {

    function getRecord($id) {
        global $db;
        global $lng;
        global $users_table;

        $id = (int) $id;

        $row = $db->getRow("SELECT * FROM " . $users_table . " WHERE id = '" . $id . "'");
        safeCheck($row);

        return $row;
    }

    public function checkUser($email, $type = 1) {
        global $db;
        global $users_table;
        if ($type == "2") {
            $sql_type = " AND facebook_user = 0 AND google_user = 0 ";
        }
        $row = $db->getRow("SELECT * FROM " . $users_table . " WHERE edate = 0 AND email = '" . $email . "' {$sql_type} ");
        safeCheck($row);

        return $row;
    }
    
    public function checkEmailExists(FilteredMap $params, $returnType = 1) {
        $email = $params->getEmail("email");
        $user = array();
        if($email){
            $user = $this->checkUser($email, 2);
        }
        if($returnType == 3){
            echo $user ? 1 : 0;
            die();
        }
        return $user ? 1 : 0;
    }

    public function registerUser(FilteredMap $params) {
        global $db;
        global $sm;
        global $lng;
        global $host;
        global $emails_test;
        global $users_table;
        global $htaccess_file;
        global $recaptcha_secret_key;

        $sm->configLoad($htaccess_file);
        $htaccessVars = $sm->getConfigVars();

        $first_name = $params->getString("first_name");
        $last_name = $params->getString("last_name");
        $email = $params->getString("email");
        $password = $params->getString("password");
        $confirm_password = $params->getString("confirm_password");
        $how_did_you_hear = $params->getString("how_did_you_hear");
        $mailinglist = $params->getInt("mailinglist");
        $agree_terms = $params->get("agree_terms");
        $agree_terms_gdpr = $params->get("agree_terms_gdpr");

        //--------- reCAPTCHA----------------------------------------------
//        $g_recaptcha_response = $params->getString("g-recaptcha-response");
//        if (!$g_recaptcha_response) {
//            header("Location: /messages/405");
//            die();
//        }
//        $reCaptchaCheck = Helpers::checkReCaptcha($g_recaptcha_response);
//        if ($reCaptchaCheck !== true) {
//            header("Location: /messages/405");
//            die();
//        }

        if ($first_name && $last_name && $email && $password && $confirm_password && $agree_terms && $agree_terms_gdpr) {
            $check = $this->checkUser($email, 2);
            if (!$check["id"]) {
                $fields = array(
                    "first_name" => $first_name,
                    "last_name" => $last_name,
                    "email" => $email,
                    "how_did_you_hear" => $how_did_you_hear,
                    "mailinglist" => $mailinglist,
                    "agree_terms" => $agree_terms,
                    "agree_terms_gdpr" => $agree_terms_gdpr,
                    "last_login_time" => time(),
                    "last_login_ip" => $_SERVER["REMOTE_ADDR"],
                    "ip" => $_SERVER["REMOTE_ADDR"],
                    "postdate" => time(),
                    "active" => 1
                );
                
                if ($password && $password == $confirm_password) {
                    $passwordHash = password_hash($password, PASSWORD_BCRYPT, ["cost" => 10]);
                    $fields["password_hash"] = $passwordHash;
                }
                
                $res = $db->autoExecute($users_table, $fields, DB_AUTOQUERY_INSERT); safeCheck($res);
                $id = mysqli_insert_id($db->connection);
                
                $row = $db->getRow("SELECT * FROM " . $users_table . " WHERE id = '" . $id . "'"); safeCheck($row);
                $_SESSION["user"] = $row;
                if ($lng == "bg") {
                    $message = "Здравей, " . $first_name . " " . $last_name . "!<br />
                        Добре дошъл/а в семейството на ArthaBeauty!<br /><br />
                        Благодарим за твоята регистрация! <br /><br />

                        Можеш да започнеш управлението на своя профил <a href='" . $host . "login-page' target='_blank'>ТУК</a>.<br /><br />

                        С уважение!<br />
                        Arthabeauty
                        ";
                } else {
                    $message = "Здравей, " . $first_name . " " . $last_name . "!<br />
                        Добре дошъл/а в семейството на ArthaBeauty!<br /><br />
                        Благодарим за твоята регистрация! <br /><br />

                        Можеш да започнеш управлението на своя профил <a href='" . $host . "login-page' target='_blank'>ТУК</a>.<br /><br />

                        С уважение!<br />
                        Arthabeauty
                        ";
                }
                
                $subject = "Успешно се регистрира в arthabeauty.bg! Радваме се, че си тук!";
                
                foreach ($emails_test as $v) {
                    mailSender($v, $subject, $message);
                }
                mailSender($row["email"], $subject, $message);
                
                header("Location: " . $htaccessVars["htaccess_my_profile"]);
                die();
            } else {
                header("Location: /messages/300");
                die();
            }
        } else {
            header("Location: /messages/406");
            die();
        }
    }

    function editUser($params) {
        global $db;
        global $sm;
        global $lng;
        global $users_table;
        global $user;

        $settingsObj = new Settings();
        $settingsObj->checkLogin();


        $name = htmlspecialchars(trim($params["name"]), ENT_QUOTES);
        $email = htmlspecialchars(trim($params["email"]), ENT_QUOTES);
        $password = htmlspecialchars(trim($params["password"]), ENT_QUOTES);
        $password_confirm = htmlspecialchars(trim($params["password_confirm"]), ENT_QUOTES);
        $phone = htmlspecialchars(trim($params["phone"]), ENT_QUOTES);
        $bulletin = (int) $params["bulletin"];
        $agree_terms = (int) $params["agree_terms"];
        $person_type = (int) $params["person_type"];

        if ($name && $email && $phone) {
            if (!$check["id"]) {
                $wedding_dates = explode("/", $params["wedding_day"]);
                $wedding_day = (int) $wedding_dates[2] . "-" . str_pad((int) $wedding_dates[1], 2, 0, STR_PAD_LEFT) . "-" . str_pad((int) $wedding_dates[0], 2, 0, STR_PAD_LEFT);
                $wedding_day_timestamp = mktime(0, 0, 0, $wedding_dates[1], $wedding_dates[0], $wedding_dates[2]);
                $fields = array(
                    "name" => $name,
                    "email" => $email,
                    "phone" => $phone,
                    "bulletin" => $bulletin,
                    "wedding_day" => $wedding_day,
                    "wedding_day_timestamp" => $wedding_day_timestamp,
                    "agree_terms" => $agree_terms,
                    "person_type" => $person_type,
                    "update_ip" => $_SERVER["REMOTE_ADDR"],
                    "update_date" => time(),
                    "active" => 1
                );
                if ($password && $password == $password_confirm && $password != "") {
                    $fields["password"] = md5($password);
                }
                if ($person_type && !$user["person_type_set"]) {
                    $fields["person_type_set"] = 1;
                    if ($person_type == 1) {
                        $user_type = 5;
                    }
                    if ($person_type == 2) {
                        $user_type = 6;
                    }
                    $fields_user_type = array(
                        'name' => $name,
                        'user_type' => $user_type,
                        'email' => $email,
                        'user_id' => $user["id"],
                        "postdate" => time()
                    );
                    $userGuestObj = new UsersGuests();
                    $userGuestObj->addEditUserGuest($fields_user_type);
                }
                $res = $db->autoExecute($users_table, $fields, DB_AUTOQUERY_UPDATE, " id = '" . $user["id"] . "' ");
                safeCheck($res);
                $_SESSION["user"] = $this->getRecord($user["id"]);

                header("Location: " . $htaccessVars["htaccess_my_profile"]);
                die();
            } else {
                header("Location: /messages/300");
                die();
            }
        }
    }

    public function loginUser(FilteredMap $params, $fbUserData = array()) {
        global $db;
        global $sm;
        global $users_table;
        global $htaccess_file;
        global $language_file;

        $sm->configLoad($htaccess_file);
        $htaccessVars = $sm->getConfigVars();

        $email = $fbUserData ? $fbUserData["email"] : $params->getString("login_email");

        if ($fbUserData) {
            $facebook_id = $fbUserData["id"];
            $row = $db->getRow("SELECT * FROM " . $users_table . " AS u WHERE u.facebook_id = '" . $facebook_id . "' AND u.facebook_user = 1 AND u.edate = 0");
            safeCheck($row);
//                echo json_encode($row);
            if ($row["id"]) {
                $_SESSION["userID"] = $row["id"];
                $_SESSION["user"] = $row;
                //Update last login info
                $res = $db->autoExecute($users_table, array("last_login_time" => time(), "last_login_ip" => $_SERVER["REMOTE_ADDR"]), DB_AUTOQUERY_UPDATE, " id = '" . $row["id"] . "' "); safeCheck($res);
                
                header("Location: /myprofile");
                die();
            } else { //echo json_encode($_REQUEST);
                $name = $fbUserData["name"];
                $nameArr = explode(" ", $name);
                $first_name = current($nameArr);
                $last_name = end($nameArr);
                $image_url = isset($fbUserData["picture"]["url"]) ? $fbUserData["picture"]["url"] : "";
                
                $fields = array(
                    "first_name" => $first_name,
                    "last_name" => $last_name,
                    "fullname" => $name,
                    "facebook_id" => $facebook_id,
                    "facebook_user" => 1,
                    "image_url" => $image_url,
                    "active" => 1,
                    "postdate" => time(),
                    "ip" => $_SERVER["REMOTE_ADDR"],
                    "agree_terms" => 1,
                    "agree_terms_gdpr" => 1
                );
                if ($email) {
                    $fields["email"] = $email;
                }
                $res = $db->autoExecute($users_table, $fields, DB_AUTOQUERY_INSERT);
                safeCheck($res);
                $id = mysqli_insert_id($db->connection);
                $user = $db->getRow("SELECT * FROM " . $users_table . " WHERE id = '" . $id . "'");
                safeCheck($user);
                $_SESSION["user"] = $user;
                
                header("Location: /myprofile");
                die();
            }
        }

        if ($params->has("googleLogin")) {
            $google_id = $params->getString("google_id");
            $row = $db->getRow("SELECT * FROM " . $users_table . " AS u WHERE u.google_id = '" . $google_id . "' AND u.google_user = 1 AND u.edate = 0");
            safeCheck($row);
            //echo json_encode($row);
            if ($row["id"]) {
                $_SESSION["userID"] = $row["id"];
                $_SESSION["user"] = $row;
                //Update last login info
                $res = $db->autoExecute($users_table, array("last_login_time" => time(), "last_login_ip" => $_SERVER["REMOTE_ADDR"]), DB_AUTOQUERY_UPDATE, " id = '" . $row["id"] . "' ");
                safeCheck($res);
//                    header("Location: /myprofile");
//                    die();
            } else { //echo json_encode($_REQUEST);
                $full_name = $params->getString("full_name");
                $given_name = $params->getString("given_name");
                $family_name = $params->getString("family_name");
                $image_url = $params->getString("image_url");
//                    $nameArr = explode(" ", $name);
//                    $first_name = current($nameArr);
//                    $last_name = end($nameArr);

                $fields = array(
                    "google_id" => $google_id,
                    "first_name" => $given_name,
                    "last_name" => $family_name,
                    "fullname" => $full_name,
                    "google_user" => 1,
                    "image_url" => $image_url,
                    "active" => 1,
                    "postdate" => time(),
                    "ip" => $_SERVER["REMOTE_ADDR"],
                    "agree_terms" => 1,
                    "agree_terms_gdpr" => 1
                );
                if ($email) {
                    $fields["email"] = $email;
                }
                $res = $db->autoExecute($users_table, $fields, DB_AUTOQUERY_INSERT);
                safeCheck($res);
                $id = mysqli_insert_id($db->connection);
                $user = $db->getRow("SELECT * FROM " . $users_table . " WHERE id = '" . $id . "'");
                safeCheck($user);
                $_SESSION["user"] = $user;
//                    header("Location: /myprofile");
//                    die();
            }
        }

        if ($params->has("login")) {
            $password = $params->getString("login_password");
            $email = $params->getString("login_email");
            
//            $captcha_token = $params->getString("g-recaptcha-response");
//            $captchaCheck = Helpers::checkReCaptcha($captcha_token);
            
            $sm->configLoad($language_file);
            $langVars = $sm->getConfigVars();
            
            $result = array();
            
//            if (!$captchaCheck) {
//                $result["code"] = 405;
//                $result["title"] = $langVars["message_title_405"];
//                $result["message"] = $langVars["message_description_405"];
////                header("Location: /messages/405");
////                die();
//            }

            if ($email && $password) {
                $row = $db->getRow("SELECT * FROM " . $users_table . " WHERE email = '" . $email . "' AND active = 1 AND edate = 0 "); safeCheck($row);

                if ($row["email"] && password_verify($password, $row["password_hash"])) {
                    $_SESSION["userID"] = $row["id"];
                    $_SESSION["user"] = $row;
                    //Update last login info
                    $res = $db->autoExecute($users_table, array("last_login_time" => time(), "last_login_ip" => $_SERVER["REMOTE_ADDR"]), DB_AUTOQUERY_UPDATE, " id = '" . $row["id"] . "' "); safeCheck($res);
                    
                    $result["code"] = 200;
                    $result["title"] = $langVars["message_title_201"]; //"Login successful";
                    $result["message"] = $langVars["message_description_201"]; //"Login successful";
//                    header("Location: " . $htaccessVars["htaccess_my_profile"]);
//                    die();
                } else {
                    $result["code"] = 301;
                    $result["title"] = $langVars["message_title_301"];
                    $result["message"] = $langVars["message_description_301"];
//                    header("Location: /messages/301");
//                    die();
                }
            } else {
                $result["code"] = 301;
                $result["title"] = $langVars["message_title_301"];
                $result["message"] = $langVars["message_description_301"];
//                header("Location: /messages/301");
//                die();
            }
            
            echo json_encode($result);
            die();
        }
    }

    function logoutUser() {
        global $lng;
        global $user_cookie_name;
        
        setcookie($user_cookie_name, "", time() - 86400, "/"); 
        
        session_destroy();

        if ($lng == "en") {
            $redirect_url = "/" . $lng;
        } else {
            $redirect_url = "/";
        }

        header("Location: " . $redirect_url);
        die();
    }

    function getUsers($options = array()) {
        global $db;
        global $users_table;

        $users = $db->getAll("SELECT 
                                    *
								  FROM " . $users_table . " 
								  WHERE edate = 0 
								  AND first_name <> ''
								  AND active = 1
								  ORDER BY pos");
        safeCheck($users);
        return $users;
    }

    public function forgotPasswordProceed(FilteredMap $params) {
        global $db;
        global $lng;
        global $users_table;
        global $emails_test;

        $settingsObj = new Settings();
        
        $email = $params->getEmail("email");
        
        $check = $this->checkUser($email);

        if ($check["id"]) {
            $newpass = substr(md5(time() . $check["email"] . $_SERVER["REMOTE_ADDR"]), 0, 8);

            if ($lng == "bg") {
                $message .= "Здравейте, " . $check["name"] . "<br><br>";
                $message .= "Вашата нова парола за достъп до www.arthabeauty.com е: <b>" . $newpass . "</b><br><br>";
                $message .= "Благодарим Ви, че използвате www.arthabeauty.com!<br>Екипът";

                $subject = "Възстановяване на парола";
            } else {
                $message .= "Здравейте, " . $check["name"] . "<br><br>";
                $message .= "Вашата нова парола за достъп до www.arthabeauty.com е: <b>" . $newpass . "</b><br><br>";
                $message .= "Благодарим Ви, че използвате www.arthabeauty.com!<br>Екипът";

                $subject = "Възстановяване на парола";
            }
            foreach ($emails_test as $v) {
                $settingsObj->mailSender($v, $subject, $message);
            }

            $settingsObj->mailSender($check["email"], $subject, $message);

            $passwordHash = password_hash($newpass, PASSWORD_BCRYPT, ["cost" => 10]);
            $res = $db->autoExecute($users_table, array("password_hash" => $passwordHash), DB_AUTOQUERY_UPDATE, " id = '" . $check["id"] . "' "); safeCheck($res);
            header("Location: /messages/500");
            die();
        } else {
            header("Location: /messages/501");
            die();
        }
    }

    public function changePassword(FilteredMap $params) {
        global $db;
        global $user;
        global $users_table;

        $settingsObj = new Settings();
        $settingsObj->checkLogin();

        if (!$user["facebook_user"] && !$user["google_user"]) {
            $user_id = $user["id"];
            $password = $params->getString("password");
            $confirm_password = $params->getString("confirm_password");
            if ($password === $confirm_password) {
                $passwordHash = password_hash($password, PASSWORD_BCRYPT, ["cost" => 10]);
                $res = $db->autoExecute($users_table, array("password_hash" => $passwordHash), DB_AUTOQUERY_UPDATE, " id = '" . $user_id . "' ");
                safeCheck($res);
                header("Location: /myprofile");
                die();
            } else {
                header("Location: /messages/408");
                die();
            }
        }
        header("Location: /messages/409");
        die();
    }
    
    public function saveUser(FilteredMap $params) {
        global $db;
        global $user;
        global $users_table;

        $settingsObj = new Settings();
        $settingsObj->checkLogin();
        
        $user_id = $user["id"];
        
        $password = $params->getString("password");
        $confirm_password = $params->getString("confirm_password");
        if($password && $confirm_password){
            if (!$user["facebook_user"] && !$user["google_user"]) {
                if ($password === $confirm_password) {
                    $passwordHash = password_hash($password, PASSWORD_BCRYPT, ["cost" => 10]);
                    $res = $db->autoExecute($users_table, array("password_hash" => $passwordHash), DB_AUTOQUERY_UPDATE, " id = '" . $user_id . "' "); safeCheck($res);
                } else {
                    header("Location: /messages/408");
                    die();
                }
            }else{
                header("Location: /messages/409");
                die();
            }
        }
        
        $first_name = $params->getString("first_name");
        $last_name = $params->getString("last_name");

        if($first_name && $last_name){
            $res = $db->autoExecute($users_table, array("first_name" => $first_name, "last_name" => $last_name), DB_AUTOQUERY_UPDATE, " id = '" . $user_id . "' "); safeCheck($res);
            $_SESSION["user"]["first_name"] = $first_name;
            $_SESSION["user"]["last_name"] = $last_name;
        }
        
        header("Location: /myprofile");
        die();
    }

    public function mailinglistSubscribeUnsubscribe() {
        global $db;
        global $user;
        global $users_table;

        $settingsObj = new Settings();
        $settingsObj->checkLogin();

        $user_id = $user["id"];
        //-------------- Unsubscribe ---------------------------------------
        if ($user["mailinglist"] == 1) {
            $fields["mailinglist"] = 0;
        } else {
            //--------------- Subscribe ----------------------------------------
            $fields["mailinglist"] = 1;
        }

        $res = $db->autoExecute($users_table, $fields, DB_AUTOQUERY_UPDATE, " id = '" . $user_id . "' ");
        safeCheck($res);
        $user = $this->getRecord($user_id);
        $_SESSION["user"] = $user;

        header("Location: /myprofile");
        die();
    }

    function getRegisterPage() {
        global $sm;

        $sm->assign("star", '<span class="star">*</span>');
        $sm->display("register.html");
    }

    public function getLoginPage() {
        global $sm;

        $sm->assign("star", '<span class="star">*</span>');
        $sm->display("login-page.html");
    }

    function getForgotPasswordPage() {
        global $sm;

        $sm->assign("star", '<span class="star">*</span>');
        $sm->display("forgot-password.html");
    }

    public function getUserAddresses($user_id) {
        global $db;
        global $lng;
        global $users_addresses_table;
        global $cities_table;
        global $districts_table;

        $user_addresses = $db->getAll("SELECT * FROM " . $users_addresses_table . " AS ua WHERE ua.edate = 0 AND ua.user_id = '" . $user_id . "' ORDER BY ua.default_shipping DESC, ua.default_billing DESC"); safeCheck($user_addresses);

        foreach ($user_addresses as $k => $v) {
            if ($v["city_id"]) {
                $city = $db->getRow("SELECT *, name_{$lng} AS name FROM " . $cities_table . " WHERE id = '" . $v["city_id"] . "' AND edate = 0");
                safeCheck($city);
                if ($city["district_id"]) {
                    $district = $db->getRow("SELECT *, name_{$lng} AS name FROM " . $districts_table . " WHERE id = '" . $v["district_id"] . "' AND edate = 0");
                    safeCheck($district);
                }
                $v["city_name"] = $city["name"];
                $v["district_name"] = $district["name"];
            }
            $user_addresses[$k] = $v;
        }

        return $user_addresses;
    }
    
    public function getLoggedUserAddresses($return_type = 1) {
        global $db;
        global $lng;
        global $users_addresses_table;
        global $cities_table;
        global $districts_table;
        
        $user_id = $_SESSION["userID"];
        if($user_id){
            $user_addresses = $db->getAll("SELECT * FROM " . $users_addresses_table . " AS ua WHERE ua.edate = 0 AND ua.user_id = '" . $user_id . "'"); safeCheck($user_addresses);
            foreach ($user_addresses as $k => $v) {
                if ($v["city_id"]) {
                    $city = $db->getRow("SELECT *, name_{$lng} AS name FROM " . $cities_table . " WHERE id = '" . $v["city_id"] . "' AND edate = 0"); safeCheck($city);
                    if ($city["district_id"]) {
                        $district = $db->getRow("SELECT *, name_{$lng} AS name FROM " . $districts_table . " WHERE id = '" . $v["district_id"] . "' AND edate = 0"); safeCheck($district);
                    }
                    $v["city_name"] = $city["name"];
                    $v["district_name"] = $district["name"];
                }
                $user_addresses[$k] = $v;
            }
        }else{
            $user_addresses = array();
        }
        if($return_type == 3){
            echo json_encode($user_addresses);
            die();
        }
        return $user_addresses;
    }

    public function addEditAddress(FilteredMap $params, $act) {
        global $sm;
        global $db;
        global $lng;
        global $host;
        global $user;
        global $language_file;
        global $users_addresses_table;
        global $districts_table;
        global $cities_table;
        global $htaccess_file;

        $settingsObj = new Settings();
        $settingsObj->checkLogin();

        $user_id = $user["id"];

        if ($user_id) {
            $id = $params->getInt("id");
            $act = htmlspecialchars(trim($act), ENT_QUOTES);
            $sm->assign("id", $id);
            $sm->assign("act", $act);

            $row = $db->getRow("SELECT * FROM " . $users_addresses_table . " WHERE id = '{$id}' AND user_id = '" . $user_id . "'"); safeCheck($row);
            $sm->assign("row", $row);
            if ($row && $act == "edit") {
                if ($row["user_id"] != $user_id) {
                    header("Location: /");
                    die();
                }
            }
            
            $districts = $db->getAll("SELECT *, name_{$lng} AS name FROM " . $districts_table . " WHERE edate = 0 ORDER BY name_{$lng}"); safeCheck($districts);
            $sm->assign("districts", $districts);
            
            $cities = $db->getAll("SELECT *, name_{$lng} AS name FROM " . $cities_table . " WHERE edate = 0 AND district_id = '" . $row["district_id"] . "' ORDER BY name_{$lng}"); safeCheck($cities);
            $sm->assign("cities", $cities);
            
            $sm->configLoad($htaccess_file);
            $htaccessVars = $sm->getConfigVars();
            
            if ($params->has("buttonSave")) {
                $firstname = $params->getString("firstname");
                $lastname = $params->getString("lastname");
                $email = $params->getString("email");
                $phone = $params->getString("phone");
                $district_id = $params->getInt("district_id");
                $city_id = $params->getInt("city_id");
                $address_line_1 = $params->getString("address_line_1");
                $address_line_2 = $params->getString("address_line_2");
                $postcode = $params->getString("postcode");
                $company_name = $params->getString("company_name");
                $vat_number = $params->getString("vat_number");
                $company_city = $params->getString("company_city");
                $company_mol = $params->getString("company_mol");

                $default_shipping = $params->getInt("default_shipping");
                $default_billing = $params->getInt("default_billing");

                $fields = array(
                    "firstname" => $firstname,
                    "lastname" => $lastname,
                    "email" => $email,
                    "phone" => $phone,
                    "district_id" => $district_id,
                    "city_id" => $city_id,
                    "address_line_1" => $address_line_1,
                    "address_line_2" => $address_line_2,
                    "postcode" => $postcode,
                    "company_name" => $company_name,
                    "vat_number" => $vat_number,
                    "company_city" => $company_city,
                    "company_mol" => $company_mol,
                    "default_shipping" => $default_shipping,
                    "default_billing" => $default_billing,
                    "user_id" => $user_id
                );

                if ($act == "add") {
                    $res = $db->autoExecute($users_addresses_table, $fields, DB_AUTOQUERY_INSERT);
                    safeCheck($res);
                    $id = mysqli_insert_id($db->connection);
                }
                if ($act == "edit") {
                    $res = $db->autoExecute($users_addresses_table, $fields, DB_AUTOQUERY_UPDATE, " id = " . $id . " ");
                    safeCheck($res);
                }
                if ($default_shipping) {
                    $db->autoExecute($users_addresses_table, array("default_shipping" => 0), DB_AUTOQUERY_UPDATE, " user_id = '" . $user_id . "' ");
                    $db->autoExecute($users_addresses_table, array("default_shipping" => 1), DB_AUTOQUERY_UPDATE, " id = '" . $id . "'");
                }
                if ($default_billing) {
                    $db->autoExecute($users_addresses_table, array("default_billing" => 0), DB_AUTOQUERY_UPDATE, " user_id = '" . $user_id . "' ");
                    $db->autoExecute($users_addresses_table, array("default_billing" => 1), DB_AUTOQUERY_UPDATE, " id = '" . $id . "'");
                }

                header("Location: " . $htaccessVars["htaccess_my_profile"]);
                die();
            }
            
            $sm->configLoad($language_file);
			$configVars = $sm->getConfigVars();
            $currentPoint = $act == "add" ? $configVars["addaddress_breadcrumbs"] : $configVars["editaddress_breadcrumbs"];
            $breadcrumbs = '<li class="breadcrumb-item"><a href="'.$host.'">'.$configVars["home_breadcrumbs"].'</a></li>';
            $breadcrumbs .= '<li class="breadcrumb-item"><a href="'.$host.$htaccessVars["htaccess_my_profile"].'">'.$configVars["myprofile_breadcrumbs"].'</a></li>';
            $breadcrumbs .= '<li class="breadcrumb-item">'.$currentPoint.'</li>';
            $sm->assign("breadcrumbs", $breadcrumbs);
            
            $sm->display("myaddress.html");
        }
    }
    
    public function addEditDeliveryAddress(FilteredMap $params, $act) {
        global $sm;
        global $db;
        global $user;
        global $users_addresses_table;
        global $htaccess_file;

        $settingsObj = new Settings();
        $settingsObj->checkLogin();

        $user_id = $user["id"];

        if ($user_id) {
            $id = $params->getInt("id");
            $act = htmlspecialchars(trim($act), ENT_QUOTES);
            $sm->assign("id", $id);
            $sm->assign("act", $act);

            $row = $db->getRow("SELECT * FROM " . $users_addresses_table . " WHERE id = '{$id}' AND user_id = '" . $user_id . "'"); safeCheck($row);
            $sm->assign("row", $row);
            if ($row && $act == "edit") {
                if ($row["user_id"] != $user_id) {
                    header("Location: /");
                    die();
                }
            }
            
            $sm->configLoad($htaccess_file);
            $htaccessVars = $sm->getConfigVars();
            
            if ($params->has("buttonSave")) {
                $firstname = $params->getString("delivery_name");
                $lastname = $params->getString("delivery_family_name");
//                $email = $params->getString("email");
//                $phone = $params->getString("phone");
                $district_id = $params->getInt("delivery_region");
                $city_id = $params->getInt("delivery_city");
                $address_line_1 = $params->getString("delivery_address_line_1");
                $address_line_2 = $params->getString("delivery_address_line_2");
                $postcode = $params->getString("delivery_postcode");
//                $company_name = $params->getString("company_name");
//                $vat_number = $params->getString("vat_number");
//                $company_city = $params->getString("company_city");
//                $company_mol = $params->getString("company_mol");

                $default_shipping = 1; //$params->getInt("default_shipping");

                $fields = array(
                    "firstname" => $firstname,
                    "lastname" => $lastname,
//                    "email" => $email,
//                    "phone" => $phone,
                    "district_id" => $district_id,
                    "city_id" => $city_id,
                    "address_line_1" => $address_line_1,
                    "address_line_2" => $address_line_2,
                    "postcode" => $postcode,
//                    "company_name" => $company_name,
//                    "vat_number" => $vat_number,
//                    "company_city" => $company_city,
//                    "company_mol" => $company_mol,
                    "default_shipping" => $default_shipping,
                    "default_billing" => 0,
                    "user_id" => $user_id
                );

                if ($act == "add") {
                    $res = $db->autoExecute($users_addresses_table, $fields, DB_AUTOQUERY_INSERT); safeCheck($res);
                    $id = mysqli_insert_id($db->connection);
                }
                if ($act == "edit") {
                    $res = $db->autoExecute($users_addresses_table, $fields, DB_AUTOQUERY_UPDATE, " id = " . $id . " "); safeCheck($res);
                }
                if ($default_shipping) {
                    $res = $db->autoExecute($users_addresses_table, array("default_shipping" => 0), DB_AUTOQUERY_UPDATE, " user_id = '" . $user_id . "' "); safeCheck($res);
                    $res = $db->autoExecute($users_addresses_table, array("default_shipping" => 1), DB_AUTOQUERY_UPDATE, " id = '" . $id . "'"); safeCheck($res);
                }
            }
            header("Location: " . $htaccessVars["htaccess_my_profile"]);
            die();
        }
        header("Location: /");
        die();
    }
    
    public function addEditBillingAddress(FilteredMap $params, $act) {
        global $sm;
        global $db;
        global $user;
        global $users_addresses_table;
        global $htaccess_file;
        
        $settingsObj = new Settings();
        $settingsObj->checkLogin();
        
        $user_id = $user["id"];
        
        $id = $params->getInt("id");
        $act = htmlspecialchars(trim($act), ENT_QUOTES);
        $sm->assign("id", $id);
        $sm->assign("act", $act);
        
        $row = $db->getRow("SELECT * FROM " . $users_addresses_table . " WHERE id = '{$id}' AND user_id = '" . $user_id . "'"); safeCheck($row);
        $sm->assign("row", $row);
        if ($row && $act == "edit") {
            if ($row["user_id"] != $user_id) {
                header("Location: /");
                die();
            }
        }
        
        $sm->configLoad($htaccess_file);
        $htaccessVars = $sm->getConfigVars();
        
        if ($params->has("buttonSave")) {
            $firstname = $params->getString("billing_name");
            $lastname = $params->getString("billing_family_name");
            $email = $params->getString("billing_email");
            $phone = $params->getString("billing_phone");
            $district_id = $params->getInt("billing_region");
            $city_id = $params->getInt("billing_city");
            $address_line_1 = $params->getString("billing_address_line_1");
            $address_line_2 = $params->getString("billing_address_line_2");
            $postcode = $params->getString("billing_postcode");
//                $company_name = $params->getString("company_name");
//                $vat_number = $params->getString("vat_number");
//                $company_city = $params->getString("company_city");
//                $company_mol = $params->getString("company_mol");
            
            $default_billing = 1;
            
            $fields = array(
                "firstname" => $firstname,
                "lastname" => $lastname,
                "email" => $email,
                "phone" => $phone,
                "district_id" => $district_id,
                "city_id" => $city_id,
                "address_line_1" => $address_line_1,
                "address_line_2" => $address_line_2,
                "postcode" => $postcode,
//                    "company_name" => $company_name,
//                    "vat_number" => $vat_number,
//                    "company_city" => $company_city,
//                    "company_mol" => $company_mol,
                "default_shipping" => 0,
                "default_billing" => $default_billing,
                "user_id" => $user_id
            );
            
            if ($act == "add") {
                $res = $db->autoExecute($users_addresses_table, $fields, DB_AUTOQUERY_INSERT); safeCheck($res);
                $id = mysqli_insert_id($db->connection);
            }
            if ($act == "edit") {
                $res = $db->autoExecute($users_addresses_table, $fields, DB_AUTOQUERY_UPDATE, " id = " . $id . " "); safeCheck($res);
            }
            if ($default_billing) {
                $res = $db->autoExecute($users_addresses_table, array("default_billing" => 0), DB_AUTOQUERY_UPDATE, " user_id = '" . $user_id . "' "); safeCheck($res);
                $res = $db->autoExecute($users_addresses_table, array("default_billing" => 1), DB_AUTOQUERY_UPDATE, " id = '" . $id . "'"); safeCheck($res);
            }
            
            header("Location: " . $htaccessVars["htaccess_my_profile"]);
            die();
        }
        header("Location: /");
        die();
    }

    public function getProfilePage() {
        global $sm;
        global $db;
        global $lng;
        global $user;
        global $host;
        global $language_file;
        global $carts_table;
        global $order_statuses_table;
        global $useBonusPoints;

        $settingsObj = new Settings();
        $settingsObj->checkLogin();

        //$user_addresses = $this->getUserAddresses($user["id"]);
        $districts = Cities::getDistrictsAllActive();
        $sm->assign("regions", $districts);
        
//        $usersObj = new Users();
//        $user_addresses = $usersObj->getUserAddresses($user["id"]);
//        $sm->assign("user_addresses", $user_addresses);

        $billing_address = Users::getUserBillingAddress();
        if( $billing_address["district_id"] ) {
            $billing_cities = Cities::getCitiesByDistrictId($billing_address["district_id"]);
            $sm->assign("billing_cities",$billing_cities);
        }

        $delivery_address = Users::getUserDeliveryAddress();
        if( $delivery_address["district_id"] ) {
            $delivery_cities = Cities::getCitiesByDistrictId($delivery_address["district_id"]);
            $sm->assign("delivery_cities",$delivery_cities);
        }

        $sm->assign("billing_address", $billing_address);
        $sm->assign("delivery_address", $delivery_address);
        
        $sql = "SELECT 
                    *, 
                    FROM_UNIXTIME(postdate, '%Y') AS post_year,
                    (SELECT s.name_{$lng} FROM {$order_statuses_table} AS s WHERE orders.status = s.id AND s.edate = 0) AS status_name
                FROM 
                    ".$carts_table." AS orders
                WHERE 
                    edate = 0 
                    AND user_id = ".$user["id"]."
                ORDER BY 
                    id DESC
                LIMIT 3 ";
        $carts = $db->getAll($sql); safeCheck($carts);
        $cartObj = new Cart();
        foreach($carts as $k => $v){
            if ( $useBonusPoints ){
                $v["total_amount"] = $v["total_amount"] - $v["bonus_points_amount"];
            }
            $v["info"] = $cartObj->getCart($v["id"], $user["id"]);
            $carts[$k] = $v;
        }

        $sm->assign("carts", $carts);
        
        $sm->configLoad($language_file);
        $configVars = $sm->getConfigVars();
        $breadcrumbs = '<li class="breadcrumb-item"><a href="'.$host.'">'.$configVars["home_breadcrumbs"].'</a></li>';
        $breadcrumbs .= '<li class="breadcrumb-item">'.$configVars["myprofile_breadcrumbs"].'</li>';
        $sm->assign("breadcrumbs", $breadcrumbs);
        
        $sm->assign("page_profile", 1);
        $sm->assign("user", $user);
        //$sm->assign("user_addresses", $user_addresses);
        $sm->assign("star", '<span class="star">*</span>');
        $sm->display("myprofile.html");
    }
    
    public static function getUserBillingAddress($userId = 0) {
        global $user;
        global $db;
        global $lng;
        global $users_addresses_table;
        global $cities_table;
        global $districts_table;
        
        $id = $userId ? $userId : $user["id"];
        
        $billing_address = $db->getRow("SELECT ua.*, 
                                            (SELECT cities.name_{$lng} FROM ".$cities_table." AS cities WHERE cities.id = ua.city_id) AS city_name,
                                            (SELECT districts.name_{$lng} FROM ".$districts_table." AS districts WHERE districts.id = ua.district_id) AS district_name 
                                        FROM ".$users_addresses_table." AS ua
                                        WHERE ua.user_id = ".$id."
                                        AND ua.edate = 0 
                                        AND ua.default_billing = 1"); safeCheck($billing_address);
        return $billing_address;
    }
    
    public static function getUserDeliveryAddress($userId = 0) {
        global $user;
        global $db;
        global $lng;
        global $users_addresses_table;
        global $cities_table;
        global $districts_table;
        
        $id = $userId ? $userId : $user["id"];
        
        $delivery_address = $db->getRow("SELECT ua.*, 
                                            (SELECT cities.name_{$lng} FROM ".$cities_table." AS cities WHERE cities.id = ua.city_id) AS city_name,
                                            (SELECT districts.name_{$lng} FROM ".$districts_table." AS districts WHERE districts.id = ua.district_id) AS district_name 
                                        FROM ".$users_addresses_table." AS ua
                                        WHERE ua.user_id = ".$id."
                                        AND ua.edate = 0 
                                        AND ua.default_shipping = 1"); safeCheck($delivery_address);
        return $delivery_address;
    }

}

?>