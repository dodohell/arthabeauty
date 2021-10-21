<?php

class Users extends Settings {

    public $pagination = "";

    function getRecord($id = 0) {
        global $db;
        global $lng;
        global $users_table;

        $id = (int) $id;

        $row = $db->getRow("SELECT * FROM " . $users_table . " WHERE id = '" . $id . "'");
        safeCheck($row);

        return $row;
    }

    function updateRow($test = "") {
        global $db;
        global $users_table;

        $row = $db->getRow("SELECT * FROM " . $users_table . "");
        safeCheck($row);

        return $row;
    }
    
    function addEditRow() {
        global $db;
        global $users_table;
        global $request;
        global $params;
        
        $act = $params->getString("act");
        $id = $params->getInt("id");
        
        $first_name         = $params->getString("first_name");
		$last_name          = $params->getString("last_name");
		$email              = $params->getEmail("email");
		$password           = $params->getString("password");
        $confirm_password   = $params->getString("confirm_password");
		$mailinglist        = $params->getString("mailinglist");
		$active             = $params->getInt("active");
		$user_group_id      = $params->getInt("user_group_id");
        
        $fields  = array(
            'first_name'	=> $first_name,
            'last_name'	    => $last_name,
            'email'			=> $email,
            'uname'			=> $params->getString("uname"),
            'dob'			=> mktime(0,0,0,$_REQUEST["dob_Month"],$_REQUEST["dob_Day"],$_REQUEST["dob_Year"]),
            'mailinglist'	=> $mailinglist,
            'active'        => $active,
            'user_group_id' => $user_group_id > 0 ? $user_group_id : 0
        );
        
        $pic_header = copyImage($_FILES['pic_header'], "../files/", "../files/tn/", "../files/tntn/", "250x");
        if (!empty($pic_header))
            $fields['pic_header'] = $pic_header;
        
        $pic = copyImage($_FILES['pic'], "../files/", "../files/tn/", "../files/tntn/", "250x");
        if (!empty($pic))
            $fields['pic'] = $pic;
        
        if ($password && $password == $confirm_password) {
            $passwordHash = password_hash($password, PASSWORD_BCRYPT, ["cost" => 10]);
            $fields["password_hash"] = $passwordHash;
        }
        
        if ($act == "add") {
            shiftPos($db, $users_table);
            $fields["ip"] = $request->getIp();
            $fields["register_date"] = time();
            $res = $db->autoExecute($users_table, $fields, DB_AUTOQUERY_INSERT);
            safeCheck($res);
            $id = mysqli_insert_id($db->connection);
        }
        
        if ($act == "edit") {
            $id = $params->getInt("id");
            $res = $db->autoExecute($users_table, $fields, DB_AUTOQUERY_UPDATE, "id = " . $id);
            safeCheck($res);
        }
    }

    function deleteRecord($id) {
        global $db;
        global $users_table;

        $fields = array(
            "edate" => time()
        );
        $res = $db->autoExecute($users_table, $fields, DB_AUTOQUERY_UPDATE, " id = '" . $id . "' ");
        safeCheck($res);

        return $res;
    }

    function getUsers($page = 0, $limit = 50, $search_string = "", $sortby = 0) {
        global $db;
        global $lng;
        global $users_table;
        global $params;
        
        $search_string = $params->getString("search_string");
        if ($search_string) {
            $search_string = strtolower($search_string);
            $sql_search_string = " AND (LOWER(CONCAT(first_name, ' ', last_name)) LIKE '%" . $search_string . "%' OR LOWER(email) LIKE '%" . $search_string . "%' OR LOWER(phone) LIKE '%" . $search_string . "%')";
        }
        if ($sortby > 0) {
            switch ($sortby) {
                case 1: $sql_sortby = "users.register_date DESC, ";
                    break;
                case 2: $sql_sortby = "users.register_date, ";
                    break;
                case 3: $sql_sortby = "users.last_login DESC, ";
                    break;
                case 4: $sql_sortby = "users.last_login, ";
                    break;
            }
        }
        
        $start = $page * $limit;
        $pages = $db->getRow("SELECT count(id) AS cntr FROM " . $users_table . " AS users WHERE edate = 0 {$sql_search_string} {$sql_where}");
        safeCheck($pages);
        $total_pages = ceil($pages["cntr"] / $limit);
        $generate_pages = '';
        
        if ($page > 0) {
            $generate_pages .= '<a href="users.php?' . $_SERVER["QUERY_STRING"] . '&page=0" class="first paginate_button paginate_button_enabled" tabindex="0">First</a>';
        } else {
            $generate_pages .= '<a href="#" class="first paginate_button paginate_button_disabled" tabindex="0">First</a>';
        }
        if ($page > 0) {
            $generate_pages .= '<a href="users.php?' . $_SERVER["QUERY_STRING"] . '&page=' . ($page - 1) . '" class="previous paginate_button paginate_button_enabled" tabindex="0">Previous</a>';
        } else {
            $generate_pages .= '<a href="#" class="previous paginate_button paginate_button_disabled" tabindex="0">Previous</a>';
        }
        
        $generate_pages .= '<span>';
        for ($i = 0; $i < $total_pages; $i++) {
            if ($page == $i) {
                $generate_pages .= '<a href="users.php?' . $_SERVER["QUERY_STRING"] . '&page=' . $i . '" class="paginate_active" tabindex="0">' . ($i + 1) . '</a>';
            } else {
                $generate_pages .= '<a href="users.php?' . $_SERVER["QUERY_STRING"] . '&page=' . $i . '" class="paginate_button" tabindex="0">' . ($i + 1) . '</a>';
            }
        }
        $generate_pages .= '</span>';
        
        if ($page < $total_pages - 1) {
            $generate_pages .= '<a href="users.php?' . $_SERVER["QUERY_STRING"] . '&page=' . ($page + 1) . '" class="next paginate_button paginate_button_enabled" tabindex="0">Next</a>';
        } else {
            $generate_pages .= '<a href="#" class="next paginate_button paginate_button_disabled" tabindex="0">Next</a>';
        }
        
        if ($page < $total_pages - 1) {
            $generate_pages .= '<a href="users.php?' . $_SERVER["QUERY_STRING"] . '&page=' . ($total_pages - 1) . '" class="last paginate_button paginate_button_enabled" tabindex="0">Last</a>';
        } else {
            $generate_pages .= '<a href="#" class="last paginate_button paginate_button_disabled" tabindex="0">Last</a>';
        }
        
        $this->pagination = $generate_pages;
        
        $users = $db->getAll("SELECT * FROM " . $users_table . " AS users WHERE edate = 0 {$sql_search_string} {$sql_where} ORDER BY {$sql_sortby} first_name, email, phone  LIMIT {$start}, {$limit}");
        safeCheck($users);
        
        return $users;
    }

    function getAllUsers() {
        global $db;
        global $users_table;

        $users = $db->getAll("SELECT * FROM " . $users_table . " WHERE edate = 0 ORDER BY first_name, email, phone");
        safeCheck($users);

        return $users;
    }

    function getUsersPagination() {
        return $this->pagination;
    }

}

?>