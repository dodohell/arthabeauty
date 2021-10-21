<?php

function safeCheck($var) {
    if (DB::isError($var)) {
        if ($_SERVER["REMOTE_ADDR"] == "84.201.192.58" || $_SERVER["REMOTE_ADDR"] == "87.120.113.171" || $_SERVER["REMOTE_ADDR"] == "46.47.121.163" || $_SERVER["REMOTE_ADDR"] == "62.73.116.1" || $_SERVER["REMOTE_ADDR"] == "89.25.103.120" || $_SERVER["REMOTE_ADDR"] == "46.10.51.223" || $_SERVER["REMOTE_ADDR"] == "95.42.25.65") {
            echo $var->getMessage() . "<br>\n";
            dbg($var);
            die();
        }
    }
}

function dbg($var) {
    if ($_SERVER["REMOTE_ADDR"] == "84.201.192.58" || $_SERVER["REMOTE_ADDR"] == "62.73.115.136" || $_SERVER["REMOTE_ADDR"] == "87.120.113.171" || $_SERVER["REMOTE_ADDR"] == "46.47.121.163" || $_SERVER["REMOTE_ADDR"] == "62.73.116.1" || $_SERVER["REMOTE_ADDR"] == "89.25.103.120" || $_SERVER["REMOTE_ADDR"] == "46.10.51.223" || $_SERVER["REMOTE_ADDR"] == "95.42.25.65") {
        echo "<pre>";
        print_r($var);
        echo "</pre>";
    }
}

function highlight($str, $highl) {
    return eregi_replace((sql_regcase($highl)), "<font class=\"highlight\" >\\0</font>", $str);
}

function getRowsForSelect(&$dbResult, $key, $value, $choose = 0) {
    safecheck($dbResult);
    $result = array();
    if ($choose) {
        $result["-"] = "--------------";
    }
    while ($row = & $dbResult->fetchRow()) {
        $result[$row[$key]] = $row[$value];
    }
    return $result;
}

function copyImage($file, $dest, $tn = "", $tntn = "", $size = "") {
    $image = "";
    if (strstr($file["type"], "image")) {
        $path_info_big = pathinfo($file["name"]);

        $rnd = rand(10000, 99999);
        $unix_time = time();
        $source_big = $file["tmp_name"];
        //$fname = strtolower(time()."_".str_replace(" ","_",$file["name"]));
        //$name_big = $fname;
        $extension = strtolower($path_info_big["extension"]);
        $name_big = "{$unix_time}{$rnd}." . $extension;

        $name_big = strtolower($name_big);
        $dest_big = "{$dest}/{$name_big}";
        $dest_tn = "{$tn}/{$name_big}";
        $dest_tntn = "{$tntn}/{$name_big}";

        if (@copy($source_big, $dest_big)) {
            $image = $name_big;
            $img = new bimage($dest_big);
//            if ($extension == "svg"){
//                $img->save();
//            }
            /*
              if($img->width > 700 ) {
              $img->resizeByWidth(700);
              $img->save();
              }
             */
        }
        if ($size != "" || $tn != "") {
            //if ($extension != "svg") {

            $s = explode("x", $size);
            @copy($source_big, $dest_tn);
            @copy($dest_tn, $dest_tntn);

            $image = $name_big;

            $img = new bimage($dest_tn);
            if ($s[0] == 0)
                $img->resizeByHeight($s[1]);
            elseif ($s[1] == 0)
                $img->resizeByWidth($s[0]);
            else
                $img->autoResize($s[0], $s[1]);
            $img->save();

            $img1 = new bimage($dest_tntn);
            $img1->resizeByHeight(120);
            $img1->save();
            //}
        }
    }
    return $image;
}

function copyImageSpecial($file, $dest, $tn = "", $tntn = "", $size = "") {
    $image = "";
    if (strstr($file["type"], "image")) {
        $path_info_big = pathinfo($file["name"]);
        $rnd = rand(10000, 99999);
        $unix_time = time();
        $source_big = $file["tmp_name"];
        $name_big = "{$unix_time}{$rnd}." . $path_info_big["extension"];
        $name_big = strtolower($name_big);
        $dest_big = "{$dest}/{$name_big}";
        $dest_tn = "{$tn}/{$name_big}";
        $dest_tntn = "{$tntn}/{$name_big}";

        if (@copy($source_big, $dest_big)) {
            $image = $name_big;
        }
        if ($size != "" || $tn != "") {
            $s = explode("x", $size);
            @copy($source_big, $dest_tn);
            @copy($dest_tn, $dest_tntn);

            $image = $name_big;

            $img = new bimage($dest_tn);
            if ($s[0] == 0)
                $img->resizeByHeight($s[1]);
            elseif ($s[1] == 0)
                $img->resizeByWidth($s[0]);
            else
                $img->autoResize($s[0], $s[1]);
            $img->save();

            $img1 = new bimage($dest_tntn);
            $img1->resizeByWidth(80);
            $img1->save();
        }
    }
    return $image;
}

function copyImageSpecial1($file, $dest, $tn = "", $tntn = "", $size = "") {
    $image = "";
    if (strstr($file["type"], "image")) {
        $path_info_big = pathinfo($file["name"]);
        $rnd = rand(10000, 99999);
        $unix_time = time();
        $source_big = $file["tmp_name"];
        $name_big = "{$unix_time}{$rnd}." . $path_info_big["extension"];
        $name_big = strtolower($name_big);
        $dest_big = "{$dest}/{$name_big}";
        $dest_tn = "{$tn}/{$name_big}";
        $dest_tntn = "{$tntn}/{$name_big}";

        if (@copy($source_big, $dest_big)) {
            $image = $name_big;
        }
        if ($size != "" || $tn != "") {
            $s = explode("x", $size);
            @copy($source_big, $dest_tn);
            @copy($dest_tn, $dest_tntn);

            $image = $name_big;

            $img = new bimage($dest_tn);
            if ($s[0] == 0)
                $img->resizeByHeight($s[1]);
            elseif ($s[1] == 0)
                $img->resizeByWidth($s[0]);
            else
                $img->resize($s[0], $s[1]);
            $img->save();

            $img1 = new bimage($dest_tntn);
            $img1->resizeByWidth(80);
            $img1->save();
        }
    }
    return $image;
}

function copyFile($file, $dest) {
    $name = $file["name"];
    $path_info = pathinfo($name);
    $ext = strtolower($path_info["extension"]);
    $source = $file["tmp_name"];
    if (!in_array($ext, array("exe", "asp", "php", "phtm", "phtml", "php3", "php4", "php42", "php5"))) {
        $fname = strtolower(time() . "_" . str_replace(" ", "_", $name));
        if (@copy($source, $dest . $fname)) {
            $filename = $fname;
        }
    }
    return $filename;
}

function copyFileFLV($file, $dest) {
    $name = $file["name"];
    $path_info = pathinfo($name);
    $ext = strtolower($path_info["extension"]);
    $source = $file["tmp_name"];
    if ($ext == "flv") {
        $fname = strtolower(time() . "_" . str_replace(" ", "_", $name));
        if (@copy($source, $dest . $fname)) {
            $filename = $fname;
        }
    }
    return $filename;
}

function copyFileEx($file, $dest) {
    $name = $file["name"];
    $path_info = pathinfo($name);
    $ext = strtolower($path_info["extension"]);
    $source = $file["tmp_name"];
    if (!in_array($ext, array("exe", "asp", "php", "phtm", "phtml", "php3", "php4", "php42", "php5"))) {
        $fname = str_replace(" ", "_", $name);
        if (@copy($source, $dest . $fname)) {
            $filename = $fname;
        }
    }
    return $filename;
}

function shiftPos($db, $table) {
    $sql = "UPDATE $table	SET  pos = pos+1";
    $res = $db->Query($sql);
    safeCheck($result);
}

function switchPos($db, $table, $pos1, $pos2) {
    $sql = "UPDATE $table
								SET  pos = (CASE  WHEN pos='$pos1' THEN  '$pos2' WHEN pos= '$pos2' THEN '$pos1' END)
								WHERE pos IN ( '$pos1' , '$pos2' )";
    $res = $db->Query($sql);
    safeCheck($result);
}

function checkLevel($current_level, $required_level) {
    if ($current_level >= $required_level) {
        
    } else {
        header("Location: unauthorized.php");
        die();
    }
}

function pagenate($limit, $style = "") {
    $currpage = $_SERVER['PHP_SELF'];
    $query_string = $_SERVER['QUERY_STRING'];

    $pattern = "/^page=[\d]+&(.*)$/";
    $replace = "$1";
    $query_string = preg_replace($pattern, $replace, $query_string);

    global $db;
    $sql = "SELECT FOUND_ROWS() AS num_row;";
    $result = $db->getRow($sql)
        or die("Cayaea FOUNDROW ia ii?a aa na eciueie!");

    //dbg($result);

    $row = $result["num_row"];
    $numrows = $row;
    if ($numrows > 0) {
        if ($numrows > $limit) {
            if (isset($_GET['page'])) {
                $page = $_GET['page'];
            } else {
                $page = 1;
            }
        }

        if ($page - 1 < 0) {
            $pagelinks = "<div class=\"$style\" style=\"color: #604347;font-weight: bold;\" >&laquo;</div>";
        } elseif ($page == 1) {
            $pagelinks = "<div class=\"$style\" style=\"color: #604347;font-weight: bold;\" >&laquo;</div>";
        } else {
            $pageprev = $page - 1;
            $pagelinks = "<div class=\"$style\" style=\"color: #604347;font-weight: bold;\"><a href='" . $currpage . "?page=" . $pageprev . "&" . $query_string . "' class=\"blueLink\">&laquo;</a></div>";
        }

        $numofpages = ceil($numrows / $limit);
        $range = 7;

        $lrange = max(1, $page - (($range - 1) / 2));
        $rrange = min($numofpages, $page + (($range - 1) / 2));

        if (($rrange - $lrange) < ($range - 1)) {
            if ($lrange == 1) {
                $rrange = min($lrange + ($range - 1), $numofpages);
            } else {
                $lrange = max($rrange - ($range - 1), 0);
            }
        }

        if ($lrange > 1) {
            //$pagelinks .= "..";
        } else {
            //$pagelinks .= "&nbsp;&nbsp;";
        }

        for ($i = 1; $i <= $numofpages; $i++) {

            if ($i == $page) {
                $pagelinks .= "<div class=\"$style\" style=\"color: #EE1D23; font-weight: bold;\" >$i</div>";
            } else {
                if ($lrange <= $i and $i <= $rrange) {
                    $pagelinks .= "<div class=\"$style\"><a href='" . $currpage . "?page=" . $i . "&" . $query_string . "' class=\"blueLink\">" . $i . " </a></div>";
                }
            }
        }

        if ($rrange < $numofpages) {
            //$pagelinks .= "..";
        } else {
            //$pagelinks .="&nbsp;&nbsp;";
        }

        if ($numofpages == 1) {
            $pagelinks .= "<div class=\"$style\" style=\"color: #604347;font-weight: bold;\" >&raquo;</div>";
        } elseif (($numrows - ($limit * $page)) > 0) {
            $pagenext = $page + 1;
            $pagelinks .= "<div class=\"$style\"><a href=\"" . $currpage . "?page=" . $pagenext . "&" . $query_string . "\" class=\"blueLink\">&raquo;</a></div>";
        } else {
            $pagelinks .= "<div  class=\"$style\" style=\"color: #604347;font-weight: bold;\">&raquo;</div>";
        }

        return $pagelinks;
    }
}

function getFromCommon($db, $table, $lng, $tag) {
    $result = $db->getRow("SELECT  id, description_" . $lng . " AS description,pic_1,pic_2 
						   FROM " . $table . " 
						   WHERE tag = '" . $tag . "'");
    return $result;
}

function deleteImages($img_name) {
    @unlink("../files/" . $img_name);
    @unlink("../files/tn/" . $img_name);
    @unlink("../files/tntn/" . $img_name);
}

function getLegendName($filePath, $leftmenu) {
    $lastSlashPos = strrpos($filePath, "/");
    $fileName = substr($filePath, ($lastSlashPos + 1));
    foreach ($leftmenu as $key => $value) {
        if ($value['url'] == $fileName) {
            return $value['name'];
        }
    }
}

function generateSpaw($langs, $fieldName, $contentValue) {
    $field = $fieldName . "_" . strtolower($langs[0]['short']);
    $spaw = new SpawEditor($field, $contentValue[$field], "", "", "", "", "", "", $langs[0]['long']);
    for ($i = 1; $i < count($langs); $i++) {
        $field = $fieldName . "_" . strtolower($langs[$i]['short']);
        $spaw->addPage(new SpawEditorPage($field, $langs[$i]['long'], $contentValue[$field]));
    }
    return $spaw;
}

function generateSpawNoLang($langs, $fieldName, $contentValue) {
    $field = $fieldName;
    $spaw = new SpawEditor($field, $contentValue[$field], "", "", "", "", "", "", $langs[0]['long']);
    $field = $fieldName;
    $spaw->addPage(new SpawEditorPage($field, $langs[$i]['long'], $contentValue[$field]));

    return $spaw;
}

function checkEmail($email) {
    if (preg_match("/^[a-zA-Z0-9_.-]+@[a-zA-Z0-9-]+.[a-zA-Z0-9-.]+$/", $email)) {
        list($username, $domain) = explode('@', $email);
        if (!checkdnsrr($domain, 'MX')) {
            return false;
        }
        return true;
    }
    return false;
}

function getStandartQuery($table, $lng, $fields = array(), $where = "") {
    global $db;
    $items = $db->getAll("SELECT id, name_" . $lng . " AS name " . (count($fields) > 0 ? "," : "") . implode(", ", $fields) . "
					      FROM " . $table . "
						  WHERE edate = 0 " . $where . "
						  ORDER BY pos");
    safeCheck($items);

    return $items;
}

function emptyDir($dir) {
    if (is_dir($dir)) {
        if ($dh = opendir($dir)) {
            while (($file = readdir($dh)) !== false) {
                if ($file != ".." && $file != "." && $file != "tn" && $file != "tntn") {
                    @unlink($dir . $file);
                }
            }
            closedir($dh);
        }
    }
}

function insertImagesInDB($db, $table, $id, $dirName, $dirTo, $dbInsert = true) {
    if (is_dir($dirName)) {
        if ($dh = opendir($dirName)) {
            while (($file = readdir($dh)) !== false) {
                if ($file != ".." && $file != "." && $file != "tn" && $file != "tntn") {
                    if (copy($dirName . $file, $dirTo . $file)) {
                        if ($dbInsert)
                            $db->query("INSERT INTO " . $table . " (property_id, pic) VALUES(" . $id . ", '" . $file . "')");
                    }
                    @unlink($dirName . $file);
                }
            }
            closedir($dh);
        }
    }
}

function phoneticConverter($stringCyr) {
    $bgChars = array('а', 'А', 'б', 'Б', 'в', 'В', 'г', 'Г', 'д', 'Д', 'е', 'Е', 'ж', 'Ж', 'з', 'З', 'и', 'И', 'й', 'Й',
        'к', 'К', 'л', 'Л', 'м', 'М', 'н', 'Н', 'о', 'О', 'п', 'П', 'р', 'Р', 'с', 'С', 'т', 'Т', 'у', 'У',
        'ф', 'Ф', 'х', 'Х', 'ц', 'Ц', 'ч', 'Ч', 'ш', 'Ш', 'щ', 'Щ', 'ъ', 'Ъ', 'ь', 'Ь', 'ю', 'Ю', 'я', 'Я');

    $phChars = array('a', 'A', 'b', 'B', 'v', 'V', 'g', 'G', 'd', 'D', 'e', 'E', 'zh', 'Zh', 'z', 'Z', 'i', 'I', 'y', 'Y',
        'k', 'K', 'l', 'L', 'm', 'M', 'n', 'N', 'o', 'O', 'p', 'P', 'r', 'R', 's', 'S', 't', 'T', 'u', 'U',
        'f', 'F', 'h', 'H', 'ts', 'Ts', 'ch', 'Ch', 'sh', 'Sh', 'sht', 'Sht', 'a', 'A', 'y', 'Y', 'yu', 'Yu', 'ya', 'Ya');
    return str_replace($bgChars, $phChars, $stringCyr);
}

function retrieve_rss($url) {
    require_once('rss_fetch.inc');
    $rss = fetch_rss($url);
    $i = 0;
    $result = array();

    foreach ($rss->items as $item) {
        if ($i == 10) {
            break;
        } else {
            $item['href'] = @nl2br($item['link']);
            $item['title'] = @strtoupper(substr(@nl2br($item['title']), 0, 1)) . strtolower(substr(@nl2br($item['title']), 1, strlen($item['title'])));
            $postdate = @explode("-", @nl2br($item['dc']['date']));
            $i++;
            $result[] = $item;
        }
    }


    return $result;
}

function getSubmenus($id, $level = 0) {
    global $db;
    global $categories_table;
    global $lng;
    $width = 600;
    $items = $db->getAll("SELECT id, category_id, name_{$lng} AS name, active
						  FROM " . $categories_table . "
						  WHERE category_id = '" . $id . "'
						  AND edate = 0
						  ORDER BY pos
						  ");
    safeCheck($items);

    foreach ($items as $k => $v) {
        $v["submenus"] = getSubmenus($v["id"], $level + 1);
        $v["level"] = $level + 1;
        $v["padding_left"] = $level * 20;
        $v["width"] = $width - $level * 20;
        for ($i = 0; $i <= $v["level"]; $i++) {
            $v["nbsps"] .= "&nbsp;&nbsp;&nbsp;";
        }
        //$v["widthI"] = 100-$level*20;
        $v["first"] = 0;
        $v["current"] = $k;
        $v["last"] = sizeof($items) - 1;
        $items[$k] = $v;
    }

    return $items;
}

function getSubmenusMenus($id, $level = 0) {
    global $db;
    global $static_info_table;
    global $lng;
    $width = 600;
    $items = $db->getAll("SELECT id, name_{$lng} AS name, id_menu, active
						  FROM " . $static_info_table . "
						  WHERE id_menu = " . $id . "
						  AND edate = 0
						  ORDER BY pos
						  ");
    safeCheck($items);

    foreach ($items as $k => $v) {
        $v["submenus"] = getSubmenusMenus($v["id"], $level + 1);
        $v["level"] = $level + 1;
        $v["padding_left"] = $level * 20;
        $v["width"] = $width - $level * 20;
        for ($i = 0; $i <= $v["level"]; $i++) {
            $v["nbsps"] .= "&nbsp;&nbsp;&nbsp;";
        }
        //$v["widthI"] = 100-$level*20;
        $v["first"] = 0;
        $v["current"] = $k;
        $v["last"] = sizeof($items) - 1;
        $items[$k] = $v;
    }

    return $items;
}

function getCategories($id, $level = 0) {
    global $db;
    global $categories_table;
    global $lng;
    $width = 600;
    $items = $db->getAll("SELECT id, category_id, name_{$lng} AS name, active
						  FROM " . $categories_table . "
						  WHERE category_id = '" . $id . "'
						  AND edate = 0
						  AND active = 'checked' 
						  ORDER BY pos
						  ");
    safeCheck($items);

    foreach ($items as $k => $v) {
        $v["subcategories"] = getCategories($v["id"], $level + 1);
        $v["level"] = $level + 1;
        $v["padding_left"] = $level * 10;
        $v["width"] = $width - $level * 10;
        for ($i = 0; $i <= $v["level"]; $i++) {
            $v["nbsps"] .= "&nbsp;&nbsp;&nbsp;";
        }
        $v["first"] = 0;
        $v["current"] = $k;
        $v["half"] = ceil(sizeof($items) / 2);
        $v["last"] = sizeof($items) - 1;
        $items[$k] = $v;
    }

    return $items;
}

function getCategoriesByType($id, $level = 0, $category_type = 0) {
    global $db;
    global $categories_table;
    global $category_to_category_type_table;
    global $lng;
    $width = 600;
    $items = $db->getAll("SELECT categories.id, categories.category_id, categories.name_{$lng} AS name, categories.active
						  FROM " . $categories_table . " AS categories, 
								" . $category_to_category_type_table . " AS ctct
						  WHERE categories.category_id = '" . $id . "'
						  AND categories.edate = 0
						  AND categories.id = ctct.category_id
						  AND ctct.category_type_id = '" . $category_type . "'
						  AND categories.active = 'checked' 
						  ORDER BY categories.pos
						  ");
    safeCheck($items);

    foreach ($items as $k => $v) {
        $v["subcategories"] = getCategoriesByType($v["id"], $level + 1, $category_type);
        $v["level"] = $level + 1;
        $v["padding_left"] = $level * 10;
        $v["width"] = $width - $level * 10;
        for ($i = 0; $i <= $v["level"]; $i++) {
            $v["nbsps"] .= "&nbsp;&nbsp;";
        }
        $v["first"] = 0;
        $v["current"] = $k;
        $v["last"] = sizeof($items) - 1;
        $items[$k] = $v;
    }

    return $items;
}

function getSubmenusCheckboxes($id, $level = 0) {
    global $db;
    global $categories_table;
    global $lng;
    $width = 600;
    $items = $db->getAll("SELECT 
								 name_" . $lng . " AS name,
								 id,
								 category_id
						  FROM " . $categories_table . "
						  WHERE category_id = " . $id . "
						  AND edate = 0
						  ORDER BY pos
						  ");
    safeCheck($items);

    foreach ($items as $k => $v) {

        $v["level"] = $level + 1;
        $v["padding_left"] = $level * 20;
        $v["width"] = $width - $level * 20;
        for ($i = 0; $i <= $v["level"]; $i++) {
            $v["nbsps"] .= "&nbsp;&nbsp;&nbsp;";
        }
        echo $v["nbsps"] . '<input type="checkbox" value="' . $v["id"] . '" name="categories[]" /> ' . $v["name"] . '<br />';
        $v["submenus"] = getSubmenusCheckboxes($v["id"], $level + 1);
        //$v["widthI"] = 100-$level*20;
        $v["first"] = 0;
        $v["current"] = $k;
        $v["last"] = sizeof($items) - 1;
        $items[$k] = $v;
    }

    return $items;
}

function getSubmenusCheckboxesProduct($id, $level = 0, $product_id = 0) {
    global $db;
    global $categories_table;
    global $product_to_category_table;
    global $lng;
    $width = 600;
    $items = $db->getAll("SELECT 
								 id,
								 name_" . $lng . " AS name,
								 category_id,
                                 active
						  FROM " . $categories_table . "
						  WHERE category_id = " . $id . "
						  AND edate = 0
						  ORDER BY pos
						  ");
    safeCheck($items);

    foreach ($items as $k => $v) {

        $v["level"] = $level + 1;
        $v["padding_left"] = $level * 20;
        $v["width"] = $width - $level * 20;
        $selected = $db->getRow("SELECT count(*) AS cntr FROM " . $product_to_category_table . " WHERE product_id = '{$product_id}' AND category_id = '" . $v["id"] . "'");
        safeCheck($selected);

        if ($selected["cntr"]) {
            $v["selected"] = "checked";
        }
        for ($i = 0; $i <= $v["level"]; $i++) {
            $v["nbsps"] .= "&nbsp;&nbsp;&nbsp;";
        }
        //$v["nbsps"].'<input type="checkbox" value="'.$v["id"].'" name="categories[]" /> '.$v["name"].'<br />';
        $v["submenus"] = getSubmenusCheckboxesProduct($v["id"], $level + 1, $product_id);
        //$v["widthI"] = 100-$level*20;
        $v["first"] = 0;
        $v["current"] = $k;
        $v["last"] = sizeof($items) - 1;
        $items[$k] = $v;
    }

    return $items;
}

function productsToCategories($id) {
    global $db;
    global $product_to_category_table;
    
    $products = array();
    $productsToCategory = $db->getAll("SELECT product_id FROM ".$product_to_category_table." WHERE category_id = $id"); safeCheck($productToCategory);

    foreach($productsToCategory as $productToCategory) {
        $products[] = $productToCategory['product_id'];
    }

    return $products;
}

function brandsToCategories($id) {
    global $db;
    global $brand_to_category_table;
    
    $brands = array();
    $brandsToCategory = $db->getAll("SELECT brand_id FROM ".$brand_to_category_table." WHERE category_id = $id"); safeCheck($brandToCategory);

    foreach($brandsToCategory as $brandToCategory) {
        $brands[] = $brandToCategory['brand_id'];
    }

    return $brands;
}

function collectionsToCategories($id) {
    global $db;
    global $collection_to_category_table;

    $collections = array();
    $collectionsToCategory = $db->getAll("SELECT collection_id FROM ".$collection_to_category_table." WHERE category_id = $id"); safeCheck($collectionToCategory);
    
    foreach($collectionsToCategory as $collectionToCategory) {
        $collections[] = $collectionToCategory['collection_id'];
    }

    return $collections;
}

function getSubcatsList($id, $level = 0) {
    global $db;
    global $categories_table;
    global $product_to_category_table;
    global $products_table;
    global $lng;
    $width = 600;
    $items = $db->getAll("SELECT 
                                c.id,
                                c.name_" . $lng . " AS name,
                                c.category_id,
                                c.active
                            FROM
                                " . $categories_table . " AS c
                            WHERE 
                                c.category_id = " . $id . "
                            AND c.edate = 0
                            ORDER BY c.pos");
    safeCheck($items);
    
    foreach ($items as $k => $v) {
        $v["level"] = $level + 1;
        $v["padding_left"] = $level * 20;
        $v["width"] = $width - $level * 20;

        for ($i = 0; $i <= $v["level"]; $i++) {
            $v["nbsps"] .= "&nbsp;&nbsp;&nbsp;";
        }
        //$v["nbsps"].'<input type="checkbox" value="'.$v["id"].'" name="categories[]" /> '.$v["name"].'<br />';
        $v["submenus"] = getSubcatsList($v["id"], $level + 1, $product_id);
        //$v["widthI"] = 100-$level*20;
        $v["first"] = 0;
        $v["current"] = $k;
        $v["last"] = sizeof($items) - 1;

        $products = productsToCategories($v["id"]);
        $brands = brandsToCategories($v["id"]);
        $collections = collectionsToCategories($v["id"]);

        $sqlCount = "SELECT 
                        COUNT(DISTINCT products.id) as cntr
                    FROM 
                        ".$products_table." AS products
                    WHERE";
            if($products || $brands || $collections) {
                $sqlCount .= " (";
                if(!empty($products)) {
                    $sql_in_ptc = implode(",", $products);
                    $sqlCount .= " products.id IN ({$sql_in_ptc})";
                }
                if(!empty($brands)) {
                    $condition = (!$products) ? '' : 'OR'; 
                    $sql_in_btc = implode(",", $brands);
                    $sqlCount .= " {$condition} products.brand_id IN ({$sql_in_btc})";
                }
                if(!empty($collections)) {
                    $condition = (!$products && !$brands) ? '' : 'OR'; 
                    $sql_in_ctc = implode(",", $collections);
                    $sqlCount .= " {$condition} products.collection_id IN ({$sql_in_ctc})";
                }
                $sqlCount .= ") ";
            } else {
                $sqlCount .= " 0";
            }
            
            $results = $db->getRow($sqlCount); safeCheck($results);
            
            $v["products_count"] = (int)$results["cntr"];
                
        $items[$k] = $v;
    }

    return $items;
}

function getSubmenusCheckboxesDiscount($id, $level = 0, $discount_id) {
    global $db;
    global $categories_table;
    global $category_to_discount_table;
    global $lng;
    $width = 600;
    $items = $db->getAll("SELECT 
								 name_" . $lng . " AS name,
								 id,
								 category_id
						  FROM " . $categories_table . "
						  WHERE category_id = " . $id . "
						  AND edate = 0
						  ORDER BY pos
						  ");
    safeCheck($items);

    foreach ($items as $k => $v) {

        $v["level"] = $level + 1;
        $v["padding_left"] = $level * 20;
        $v["width"] = $width - $level * 20;
        $selected = $db->getRow("SELECT count(*) AS cntr FROM " . $category_to_discount_table . " WHERE discount_id = '{$discount_id}' AND category_id = '" . $v["id"] . "'");
        safeCheck($selected);

        if ($selected["cntr"]) {
            $v["selected"] = "checked";
        }
        for ($i = 0; $i <= $v["level"]; $i++) {
            $v["nbsps"] .= "&nbsp;&nbsp;&nbsp;";
        }
        //$v["nbsps"].'<input type="checkbox" value="'.$v["id"].'" name="categories[]" /> '.$v["name"].'<br />';
        $v["submenus"] = getSubmenusCheckboxesDiscount($v["id"], $level + 1, $discount_id);
        //$v["widthI"] = 100-$level*20;
        $v["first"] = 0;
        $v["current"] = $k;
        $v["last"] = sizeof($items) - 1;
        $items[$k] = $v;
    }

    return $items;
}

function getSubmenusCheckboxesPromoCode($id, $level = 0, $promo_code_id) {
    global $db;
    global $categories_table;
    global $category_to_promo_code_table;
    global $lng;
    $width = 600;
    $items = $db->getAll("SELECT 
								 name_" . $lng . " AS name,
								 id,
								 category_id
						  FROM " . $categories_table . "
						  WHERE category_id = " . $id . "
						  AND edate = 0
						  ORDER BY pos
						  ");
    safeCheck($items);

    foreach ($items as $k => $v) {

        $v["level"] = $level + 1;
        $v["padding_left"] = $level * 20;
        $v["width"] = $width - $level * 20;
        $selected = $db->getRow("SELECT count(*) AS cntr FROM " . $category_to_promo_code_table . " WHERE promo_code_id = '{$promo_code_id}' AND category_id = '" . $v["id"] . "'");
        safeCheck($selected);

        if ($selected["cntr"]) {
            $v["selected"] = "checked";
        }
        for ($i = 0; $i <= $v["level"]; $i++) {
            $v["nbsps"] .= "&nbsp;&nbsp;&nbsp;";
        }
        //$v["nbsps"].'<input type="checkbox" value="'.$v["id"].'" name="categories[]" /> '.$v["name"].'<br />';
        $v["submenus"] = getSubmenusCheckboxesPromoCode($v["id"], $level + 1, $promo_code_id);
        //$v["widthI"] = 100-$level*20;
        $v["first"] = 0;
        $v["current"] = $k;
        $v["last"] = sizeof($items) - 1;
        $items[$k] = $v;
    }

    return $items;
}


function getSubmenusCheckboxesMenus($id, $level = 0, $info_id) {
    global $db;
    global $categories_table;
    global $static_info_to_category_table;
    global $lng;
    $width = 600;
    $items = $db->getAll("SELECT 
								 name_" . $lng . " AS name,
								 id,
								 category_id
						  FROM " . $categories_table . "
						  WHERE category_id = " . $id . "
						  AND edate = 0
						  ORDER BY pos
						  ");
    safeCheck($items);

    foreach ($items as $k => $v) {

        $v["level"] = $level + 1;
        $v["padding_left"] = $level * 20;
        $v["width"] = $width - $level * 20;
        $selected = $db->getRow("SELECT count(*) AS cntr FROM " . $static_info_to_category_table . " WHERE info_id = '{$info_id}' AND category_id = '" . $v["id"] . "'");
        safeCheck($selected);

        if ($selected["cntr"]) {
            $v["selected"] = "checked";
        }
        for ($i = 0; $i <= $v["level"]; $i++) {
            $v["nbsps"] .= "&nbsp;&nbsp;&nbsp;";
        }
        //$v["nbsps"].'<input type="checkbox" value="'.$v["id"].'" name="categories[]" /> '.$v["name"].'<br />';
        $v["submenus"] = getSubmenusCheckboxesMenus($v["id"], $level + 1, $info_id);
        //$v["widthI"] = 100-$level*20;
        $v["first"] = 0;
        $v["current"] = $k;
        $v["last"] = sizeof($items) - 1;
        $items[$k] = $v;
    }

    return $items;
}

function getSubmenusCheckboxesCategories($id, $level = 0, $info_id) {
    global $db;
    global $categories_table;
    global $category_to_category_table;
    global $lng;
    $width = 600;
    $items = $db->getAll("SELECT 
								 name_" . $lng . " AS name,
								 id,
								 category_id
						  FROM " . $categories_table . "
						  WHERE category_id = " . $id . "
						  AND edate = 0
						  ORDER BY pos
						  ");
    safeCheck($items);

    foreach ($items as $k => $v) {

        $v["level"] = $level + 1;
        $v["padding_left"] = $level * 20;
        $v["width"] = $width - $level * 20;
        $selected = $db->getRow("SELECT count(*) AS cntr FROM " . $category_to_category_table . " WHERE category_main_id = '{$info_id}' AND category_id = '" . $v["id"] . "'");
        safeCheck($selected);

        if ($selected["cntr"]) {
            $v["selected"] = "checked";
        }
        for ($i = 0; $i <= $v["level"]; $i++) {
            $v["nbsps"] .= "&nbsp;&nbsp;&nbsp;";
        }
        //$v["nbsps"].'<input type="checkbox" value="'.$v["id"].'" name="categories[]" /> '.$v["name"].'<br />';
        $v["submenus"] = getSubmenusCheckboxesCategories($v["id"], $level + 1, $info_id);
        //$v["widthI"] = 100-$level*20;
        $v["first"] = 0;
        $v["current"] = $k;
        $v["last"] = sizeof($items) - 1;
        $items[$k] = $v;
    }

    return $items;
}

function getSubmenusCheckboxesSelected($id, $level = 0, $user_profile_id, $main_category_id) {
    global $db;
    global $categories_table;
    global $lng;
    global $user_profile_to_category_table;
    $width = 600;
    $items = $db->getAll("SELECT 
								 name_" . $lng . " AS name,
								 id,
								 category_id
						  FROM " . $categories_table . "
						  WHERE category_id = " . $id . "
						  AND edate = 0
						  ORDER BY pos
						  ");
    safeCheck($items);

    foreach ($items as $k => $v) {
        $check_selected = $db->getRow("SELECT * FROM " . $user_profile_to_category_table . " WHERE user_profile_id = " . $user_profile_id . " AND category_id=" . $v["id"] . "");
        safeCheck($check_selected);
        $v["level"] = $level + 1;
        $v["padding_left"] = $level * 20;
        $v["width"] = $width - $level * 20;
        for ($i = 0; $i <= $v["level"]; $i++) {
            $v["nbsps"] .= "&nbsp;&nbsp;&nbsp;";
        }

        if ($check_selected["id"]) {
            if ($v["level"] == 3) {
                echo $v["nbsps"] . '<input type="checkbox" value="' . $v["id"] . '" name="categories[]" checked /> <strong>' . $v["name"] . '</strong><br />';
            } else {
                echo $v["nbsps"] . '<input type="checkbox" value="' . $v["id"] . '" name="categories[]" checked /> ' . $v["name"] . '<br />';
            }
            $_SESSION["selected_" . $main_category_id] += 1;
        } else {
            if ($v["level"] == 2) {
                echo $v["nbsps"] . '<input type="checkbox" value="' . $v["id"] . '" name="categories[]" /> <strong>' . $v["name"] . '</strong><br />';
            } else {
                echo $v["nbsps"] . '<input type="checkbox" value="' . $v["id"] . '" name="categories[]" /> ' . $v["name"] . '<br />';
            }
        }
        $v["submenus"] = getSubmenusCheckboxesSelected($v["id"], $level + 1, $user_profile_id, $main_category_id);

        $v["first"] = 0;
        $v["current"] = $k;
        $v["last"] = sizeof($items) - 1;
        $items[$k] = $v;
    }

    return $items;
}

function getSubmenusById($id, $level = 0) {
    global $db;
    global $categories_table;
    global $lng;

    $items = $db->getAll("SELECT id, category_id
						  FROM " . $categories_table . "
						  WHERE category_id = " . $id . "
						  AND edate = 0
						  ORDER BY pos
						  ");
    safeCheck($items);

    foreach ($items as $k => $v) {
        $categoriesFull[] = $v["id"];
        $result = getSubmenusById($v["id"]);
        foreach ($result as $kk => $vv) {
            if ($vv) {
                $categoriesFull[] = $vv;
            }
        }
    }

    return $categoriesFull;
}

function getSubmenusSelected($id, $level = 0, $selected) {
    global $db;
    global $categories_table;
    global $lng;
    $width = 600;
    $items = $db->getAll("SELECT 
								 name_" . $lng . " AS name,
								 id,
								 category_id
						  FROM " . $categories_table . "
						  WHERE category_id = " . $id . "
						  AND edate = 0
						  ORDER BY pos
						  ");
    safeCheck($items);

    foreach ($items as $k => $v) {
        $v["submenus"] = getSubmenusSelected($v["id"], $level + 1, $selected);
        $v["level"] = $level + 1;
        $v["padding_left"] = $level * 20;
        $v["width"] = $width - $level * 20;
        for ($i = 0; $i <= $v["level"]; $i++) {
            $v["nbsps"] .= "&nbsp;&nbsp;&nbsp;";
        }
        for ($j = 0; $j < sizeof($selected); $j++) {
            if ($v["id"] == $selected[$j]["category_id"]) {
                $v["selected"] = "checked";
                $v["text"] = $selected[$j];
            }
        }
        //$v["widthI"] = 100-$level*20;
        $v["first"] = 0;
        $v["current"] = $k;
        $v["last"] = sizeof($items) - 1;
        $items[$k] = $v;
    }
    return $items;
}

function getXMLData($url) {
    $xml_parser = xml_parser_create();

    if (!($fp = fopen($url, "r"))) {
        die("could not open XML input");
    }
    $handle = fopen($url, "r");

    $completeStr = "";
    if ($handle) {
        while (!feof($handle)) {
            $lines[] = fgets($handle, 4096);
        }
        fclose($handle);
    }
    foreach ($lines as $k => $v) {
        $str .= $v . "\n";
    }

    $data = $str;
    xml_parse_into_struct($xml_parser, $data, $vals, $index);
    xml_parser_free($xml_parser);

    $params = array();
    $level = array();
    foreach ($vals as $xml_elem) {
        if ($xml_elem['type'] == 'open') {
            if (array_key_exists('attributes', $xml_elem)) {
                list($level[$xml_elem['level']], $extra) = array_values($xml_elem['attributes']);
            } else {
                $level[$xml_elem['level']] = $xml_elem['tag'];
            }
        }
        if ($xml_elem['type'] == 'complete') {
            $start_level = 1;
            $php_stmt = '$params';
            while ($start_level < $xml_elem['level']) {
                $php_stmt .= '[$level[' . $start_level . ']]';
                $start_level++;
            }
            $php_stmt .= '[$xml_elem[\'tag\']] = $xml_elem[\'value\'];';
            eval($php_stmt);
        }
    }
    return $params;
}

function getXMLData2($url) {
    $xml_parser = xml_parser_create();

    if (!($fp = fopen($url, "r"))) {
        die("could not open XML input");
    }

    //$data = fread($fp, filesize($url));
    //$data = file_get_contents($url);
    $handle = fopen($url, "r");

    $completeStr = "";
    if ($handle) {
        while (!feof($handle)) {
            $lines[] = fgets($handle, 4096);
        }
        fclose($handle);
    }
    foreach ($lines as $k => $v) {
        $str .= $v . "";
    }

    $data = $str;

    //fclose($fp);
    xml_parse_into_struct($xml_parser, $data, $vals, $index);
    xml_parser_free($xml_parser);

    $params = array();
    $level = array();
    foreach ($vals as $xml_elem) {
        if ($xml_elem['type'] == 'open') {
            if (array_key_exists('attributes', $xml_elem)) {
                list($level[$xml_elem['level']], $extra) = array_values($xml_elem['attributes']);
            } else {
                $level[$xml_elem['level']] = $xml_elem['tag'];
            }
        }
        if ($xml_elem['type'] == 'complete') {
            $start_level = 1;
            $php_stmt = '$params';
            while ($start_level < $xml_elem['level']) {
                $php_stmt .= '[$level[' . $start_level . ']]';
                $start_level++;
            }
            $php_stmt .= '[$xml_elem[\'tag\']] = $xml_elem[\'value\'];';
            eval($php_stmt);
        }

        if ($xml_elem["tag"] == "MERCHANT") {
            $_SESSION["merchants_retrieve"][] = $xml_elem["attributes"];
        }
    }

    return $params;
}

function readDatabase($url) {
    //$xml_parser = xml_parser_create();

    if (!($fp = fopen($url, "r"))) {
        die("could not open XML input");
    }

    //$data = fread($fp, filesize($url));
    //$data = file_get_contents($url);
    $handle = fopen($url, "r");

    // read the XML database 
    if ($handle) {
        while (!feof($handle)) {
            $lines[] = fgets($handle, 4096);
        }
        fclose($handle);
    }
    foreach ($lines as $k => $v) {
        $str .= $v . "";
    }

    $data = $str;
    $parser = xml_parser_create();
    xml_parser_set_option($parser, XML_OPTION_CASE_FOLDING, 0);
    xml_parser_set_option($parser, XML_OPTION_SKIP_WHITE, 1);
    xml_parse_into_struct($parser, $data, $values, $tags);
    xml_parser_free($parser);

    // loop through the structures
    foreach ($tags as $key => $val) {
        if ($key == "Item") {
            $info = $val;
            for ($i = 0; $i < count($info); $i += 2) {
                $offset = $info[$i] + 1;
                $len = $info[$i + 1] - $offset;
                $tdb[] = parseXML(array_slice($values, $offset, $len));
            }
        } else {
            continue;
        }
    }
    return $tdb;
}

function parseXML($mvalues) {
    for ($i = 0; $i < count($mvalues); $i++) {
        $res[$mvalues[$i]["tag"]] = $mvalues[$i]["value"];
    }
    foreach ($res as $k => $v)
        $Result->$k = $res[$k];
    return $Result;
}

function getRootCategory($id) {
    global $db;
    global $categories_table;

    $row = $db->getRow("SELECT category_id FROM " . $categories_table . " WHERE edate = 0 AND id = '{$id}'");
    safeCheck($row);

    if ($row["category_id"] != 0) {
        return getRootCategory($row["category_id"]);
    } else {
        return $id;
    }
}

function checkLogin($params) {
    if (!$_SESSION["user"]["id"]) {
        header("Location: /enter.php");
    }
}

function generateEditor($langs, $fieldName, $contentValue) {
    $tabs = "";
    for ($i = 0; $i < count($langs); $i++) {
        $field = $fieldName . "_" . strtolower($langs[$i]['short']);
        if ($i == 0) {
            $activeTab = " activeTab";
            $activeTabEditor = " activeTabEditor";
        } else {
            $activeTab = "";
            $activeTabEditor = "";
        }

        $tabs .= '<div class="tabLanguage' . $activeTab . '">' . $langs[$i]["long"] . '</div>';
        $editors .= '<div class="tabLanguageEditor' . $activeTabEditor . '"><textarea class="ckeditor inputField" cols="80" id="' . $field . '" name="' . $field . '" rows="10">' . $contentValue[$field] . '</textarea></div>';
    }
    $js = '
				<script language="javascript" type="text/javascript">
				
				$(document).ready(function(){
					$("#editor_' . $fieldName . ' .tabLanguage").bind("click", function(){
						var index = $("#editor_' . $fieldName . ' .tabLanguage").index(this);
						$("#editor_' . $fieldName . ' .tabLanguage").removeClass("activeTab");
						$(this).addClass("activeTab");
						
						$("#editor_' . $fieldName . ' .tabLanguageEditor").removeClass("activeTabEditor");
						$("#editor_' . $fieldName . ' .tabLanguageEditor").eq(index).addClass("activeTabEditor");
						
					});
				});
				</script>
				';

    return $js . '<div class="editorHolder" id="editor_' . $fieldName . '">' . $tabs . '<div class="clear"></div>' . $editors . '</div>' . $js_end;
}

function imagecopymerge_alpha($dst_im, $src_im, $dst_x, $dst_y, $src_x, $src_y, $src_w, $src_h, $pct) {
    if (!isset($pct)) {
        return false;
    }
    $pct /= 100;
    // Get image width and height
    $w = imagesx($src_im);
    $h = imagesy($src_im);
    // Turn alpha blending off
    imagealphablending($src_im, false);
    // Find the most opaque pixel in the image (the one with the smallest alpha value)
    $minalpha = 127;
    for ($x = 0; $x < $w; $x++)
        for ($y = 0; $y < $h; $y++) {
            $alpha = ( imagecolorat($src_im, $x, $y) >> 24 ) & 0xFF;
            if ($alpha < $minalpha) {
                $minalpha = $alpha;
            }
        }
    //loop through image pixels and modify alpha for each
    for ($x = 0; $x < $w; $x++) {
        for ($y = 0; $y < $h; $y++) {
            //get current alpha value (represents the TANSPARENCY!)
            $colorxy = imagecolorat($src_im, $x, $y);
            $alpha = ( $colorxy >> 24 ) & 0xFF;
            //calculate new alpha
            if ($minalpha !== 127) {
                $alpha = 127 + 127 * $pct * ( $alpha - 127 ) / ( 127 - $minalpha );
            } else {
                $alpha += 127 * $pct;
            }
            //get the color index with new alpha
            $alphacolorxy = imagecolorallocatealpha($src_im, ( $colorxy >> 16 ) & 0xFF, ( $colorxy >> 8 ) & 0xFF, $colorxy & 0xFF, $alpha);
            //set pixel with the new color + opacity
            if (!imagesetpixel($src_im, $x, $y, $alphacolorxy)) {
                return false;
            }
        }
    }
    // The image copy

    imagecopy($dst_im, $src_im, $dst_x, $dst_y, $src_x, $src_y, $src_w, $src_h);
}

function mailSender($email, $message_heading, $message_text, $file_attachment_1 = "", $file_attachment_2 = "", $file_attachment_3 = "") {
    global $install_path;
    global $sm;
    global $host;
    global $contacts;
    global $copyrights;
    require_once("phpmailer/class.phpmailer.php");

    $sm->assign("subject", $message_heading);
    $sm->assign("message", $message_text);

    $logo = '<img src="' . $host . 'images/logoEmail.png"  style="padding: 1rem; height: 50px !important; object-fit: contain;">';

    $message_text = '<html>
								<head>
									<title>' . $message_heading . '</title>
								</head>
								<body>
									<table width="100%">
										<tr>
											<td width="100%" style="background-color: #eee; padding: 50px 0px;">
												<table width="650" align="center">
													<tr>
														<td style="background-color: #fff; padding: 20px;">
															<table width="100%" cellpadding="0"  cellspacing="0">
																<tr>
																	<td valign="top" width="100%" style="background-color: white !important; border-bottom: 1px solid #BFD8D0; text-align: center;">
																		<a href="' . $host . '" target="_blank">' . $logo . '</a>
																	</td>
																</tr>
															</table>

															<table width="100%" cellpadding="0" cellspacing="0">
																<tr>
																		<td style="padding:5px;">
																				<br><br><br>
																				' . $message_text . '
																				<br><br><br>
																		</td>
																</tr>
															</table>
															<table width="100%" cellpadding="5" cellspacing="0">
																<tr>
																	<td valign="top" bgcolor="#ccc">
																		<span style="color: #ffffff; font-size: 11px;">' . $copyrights["description"] . '</span>
																	</td>
																</tr>
															</table>
														</td>
													</tr>
												</table>
											</td>
										</tr>
									</table>
								</body>
						</html>';


    $mailObj = new PHPMailer();
    $mailObj->CharSet = 'utf-8';
    $mailObj->From = 'info@arthabeauty.bg';
    $mailObj->isHTML(true);
    $mailObj->FromName = 'arthabeauty.bg';
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

function getSpecialOfferPrice($product_id = 0, $brand_id = 0, $with_text = 0, $option_id = 0) {
    global $db;
    global $lng;
    global $products_table;
    global $discounts_table;
    global $product_to_discount_table;
    global $collection_to_discount_table;
    global $brand_to_discount_table;
    global $category_to_discount_table;
    global $product_to_category_table;
    global $bonus_points_to_sell;
    global $variants_table;

    $date_today = date("Y-m-d");
    $price_specialoffer = 0.0;

    $row = $db->getRow("SELECT * FROM " . $products_table . " WHERE id = '" . $product_id . "'");
    safeCheck($row);
    if ($brand_id == 0 && $row["brand_id"]) {
        $brand_id = $row["brand_id"];
    }

    $collection_id = 0;
    if ($brand_id > 0 && $row["collection_id"]) {
        $collection_id = $row["collection_id"];
    }

    if ($option_id) {
        $variant = $db->getRow("SELECT * FROM " . $variants_table . " WHERE product_id = '" . $product_id . "' AND option_id = '" . $option_id . "'");
        safeCheck($variant);
        $row["price"] = $variant["price"];
    }

    $product_categories = $db->getAll("SELECT * FROM " . $product_to_category_table . " WHERE product_id = '" . $product_id . "'");
    safeCheck($product_categories);
    foreach ($product_categories as $k => $v) {
        if ($k == 0) {
            $sql_cats .= " ctd.category_id = '" . $v["category_id"] . "' ";
        } else {
            $sql_cats .= " OR ctd.category_id = '" . $v["category_id"] . "' ";
        }
    }

    $sql_cats = " AND (" . $sql_cats . ") ";

    $discount_date_from = "";
    $discount_date_to = "";
    
    if (sizeof($product_categories) > 0) {
        $discount_category = $db->getRow("
											SELECT discounts.*,
												   discounts.name_{$lng} AS name
											FROM " . $discounts_table . " AS discounts,
												 " . $category_to_discount_table . " AS ctd
											WHERE discounts.edate = 0
											AND discounts.active = 1
											AND discounts.id = ctd.discount_id
											AND discounts.discount_date_from <= '" . $date_today . "'
											AND discounts.discount_date_to >= '" . $date_today . "'
											{$sql_cats}
										");
        safeCheck($discount_category);
        if ($discount_category["discount_value"]) {
            if ($discount_category["discount_type"] == 1) {
                $price_specialoffer = $row["price"] - $row["price"] * $discount_category["discount_value"] / 100;
            } else if ($discount_category["discount_type"] == 2) {
                $price_specialoffer = $row["price"] - $discount_category["discount_value"] >= 0.00 ? $row["price"] - $discount_products["discount_value"] : 0.00;
            } else {
                throw new Exception("Incorect discount type!");
            }
            $discount_date_from = strtotime($discount_category["discount_date_from"]);
            $discount_date_to = strtotime($discount_category["discount_date_to"]);
            $price_text = $discount_category["name"] . " валидна до " . date("d/m/Y", strtotime($discount_category["discount_date_to"]));
            $pic = $discount_category["pic"];
        }
    }
    $sql = "
					SELECT 
                        discounts.*,
                        discounts.name_{$lng} AS name
					FROM " . $discounts_table . " AS discounts,
						 " . $brand_to_discount_table . " AS btd
					WHERE discounts.edate = 0
					AND discounts.active = 1
					AND discounts.id = btd.discount_id
					AND discounts.discount_date_from <= '" . $date_today . "'
					AND discounts.discount_date_to >= '" . $date_today . "'
					AND btd.brand_id = '" . $brand_id . "'
				";
//		if($_SERVER['REMOTE_ADDR'] == '84.201.192.58'){
//			//dbg($row);
//			//dbg($sql);
//		}

    $discount_brand = $db->getRow($sql);
    safeCheck($discount_brand);

    if ($discount_brand["discount_value"]) {
        if ($discount_brand["discount_type"] == 1) {
            $price_specialoffer = $row["price"] - $row["price"] * $discount_brand["discount_value"] / 100;
        } else if ($discount_brand["discount_type"] == 2) {
            $price_specialoffer = $row["price"] - $discount_brand["discount_value"] >= 0.00 ? $row["price"] - $discount_products["discount_value"] : 0.00;
        } else {
            throw new Exception("Incorect discount type!");
        }
        $discount_date_from = strtotime($discount_brand["discount_date_from"]);
        $discount_date_to = strtotime($discount_brand["discount_date_to"]);
        $price_text = $discount_brand["name"] . " валидна до " . date("d/m/Y", strtotime($discount_brand["discount_date_to"]));
        $pic = $discount_brand["pic"];
    }

// --------------------- Discount by Collection --------------------------------
    $discount_collection = $db->getRow(" SELECT 
                                            discounts.*,
                                            discounts.name_{$lng} AS name
                                        FROM " . $discounts_table . " AS discounts,
                                             " . $collection_to_discount_table . " AS ctd
                                        WHERE discounts.edate = 0
                                        AND discounts.active = 1
                                        AND discounts.id = ctd.discount_id
                                        AND discounts.discount_date_from <= '" . $date_today . "'
                                        AND discounts.discount_date_to >= '" . $date_today . "'
                                        AND ctd.collection_id = '" . $collection_id . "'
                                    ");
    safeCheck($discount_collection);

    if ($discount_collection["discount_value"]) {
        if ($discount_collection["discount_type"] == 1) {
            $price_specialoffer = $row["price"] - $row["price"] * $discount_collection["discount_value"] / 100;
        } else if ($discount_collection["discount_type"] == 2) {
            $price_specialoffer = $row["price"] - $discount_collection["discount_value"] >= 0.00 ? $row["price"] - $discount_products["discount_value"] : 0.00;
        } else {
            throw new Exception("Incorect discount type!");
        }
        $discount_date_from = strtotime($discount_collection["discount_date_from"]);
        $discount_date_to = strtotime($discount_collection["discount_date_to"]);
        $price_text = $discount_collection["name"] . " валидна до " . date("d/m/Y", strtotime($discount_collection["discount_date_to"]));
        $pic = $discount_collection["pic"];
    }

    $discount_products = $db->getRow("
										SELECT discounts.*,
                                            discounts.name_{$lng} AS name
										FROM " . $discounts_table . " AS discounts,
											 " . $product_to_discount_table . " AS ptd
										WHERE discounts.edate = 0
										AND discounts.active = '1'
										AND discounts.id = ptd.discount_id
										AND discounts.discount_date_from <= '" . $date_today . "'
										AND discounts.discount_date_to >= '" . $date_today . "'
										AND ptd.product_id = '" . $product_id . "'
									");
    safeCheck($discount_products);
    if ($discount_products["discount_value"]) {
        if ($discount_products["discount_type"] == 1) {
            $price_specialoffer = $row["price"] - $row["price"] * $discount_products["discount_value"] / 100;
        } else if ($discount_products["discount_type"] == 2) {
            $price_specialoffer = $row["price"] - $discount_products["discount_value"] >= 0.00 ? $row["price"] - $discount_products["discount_value"] : 0.00;
        } else {
            throw new Exception("Incorect discount type!");
        }
        $discount_date_from = strtotime($discount_products["discount_date_from"]);
        $discount_date_to = strtotime($discount_products["discount_date_to"]);
        $price_text = $discount_products["name"] . " валидна до " . date("d/m/Y", strtotime($discount_products["discount_date_to"]));
        $pic = $discount_products["pic"];
    }
    
    $price_specialoffer = number_format($price_specialoffer, 2, ".", "0");

    if ($with_text == 0) {
        return $price_specialoffer;
    } else {
        $price_result["price_specialoffer"] = $price_specialoffer;
        $price_result["price_specialoffer_text"] = $price_text;
        $price_result["bonus_points"] = $price_specialoffer * $bonus_points_to_sell;
        $price_result["discount_date_from"] = $discount_date_from;
        $price_result["discount_date_to"] = $discount_date_to;
        $price_result["pic"] = $pic;

        return $price_result;
    }
}

function prepareURL($url) {

    $url = str_replace(array("http//", "https//", "http://", "https://"), "", $url);

    if (!preg_match("^(?:f|ht)tps?://", $url)) {
        $url = "http://" . $url;
    }

    return $url;
}

function prepareWebsite($url) {

    if (!preg_match("^(?:f|ht)tps?://", $url) && !preg_match("^(?:f|ht)tp?://", $url)) {
        $url = "http://" . $url;
    }

    return $url;
}

function txt2link($text) {
    // force http: on www.
    $text = ereg_replace("www\.", "http://www.", $text);
    // eliminate duplicates after force
    $text = ereg_replace("http://http://www\.", "http://www.", $text);
    $text = ereg_replace("https://http://www\.", "https://www.", $text);

    // The Regular Expression filter
    $reg_exUrl = "/(http|https|ftp|ftps)\:\/\/[a-zA-Z0-9\-\.]+\.[a-zA-Z]{2,3}(\/\S*)?/";
    // Check if there is a url in the text
    if (preg_match($reg_exUrl, $text, $url)) {
        // make the urls hyper links
        $text = preg_replace($reg_exUrl, '<a href="' . $url[0] . '" rel="nofollow" target="_blank">' . str_replace("http://", "", $url[0]) . '</a>', $text);
    }    // if no urls in the text just return the text
    return ($text);
}

function getDiscounts($cart_id) {
    global $db;
    global $products_table;
    global $carts_table;
    global $carts_products_table;
    global $discounts_table;
    global $product_to_discount_table;
    global $brand_to_discount_table;
    global $category_to_discount_table;
    global $discount_to_discount_table;
    global $product_to_category_table;

    $cart = $db->getRow("SELECT * FROM " . $carts_table . " WHERE id = '" . $cart_id . "'");
    safeCheck($cart);
    $cart_products = $db->getAll("SELECT * FROM " . $carts_products_table . " WHERE cart_id = '" . $cart_id . "' AND edate = 0");
    safeCheck($cart_products);

    $date_today = date("Y-m-d");

    $sql = "SELECT * FROM " . $discounts_table . " AS discounts WHERE edate = 0 AND active = '1' AND discount_date_from <= '" . $date_today . "' AND (discounts.voucher = '' OR discounts.voucher IS NULL) AND discount_date_to >= '" . $date_today . "' ";
    $discounts = $db->getAll($sql);
    safeCheck($discounts);

    $discount_results["delivery_amount"] = 0;
    $discount_results["discount_amount"] = 0;
    $discount_results["discount_free_delivery"] = 0;

    $total_amount = $cart["total_amount"];
    $discount_value = 0;
    foreach ($discounts as $k => $v) {
        $discount_discounts = $db->getAll("SELECT * FROM " . $discount_to_discount_table . " WHERE discount_main_id = '" . $v["id"] . "'");
        safeCheck($discount_discounts);
        $discount_products = $db->getAll("SELECT * FROM " . $product_to_discount_table . " WHERE discount_id = '" . $v["id"] . "'");
        safeCheck($discount_products);
        $discount_categories = $db->getAll("SELECT * FROM " . $category_to_discount_table . " WHERE discount_id = '" . $v["id"] . "'");
        safeCheck($discount_categories);
        $discount_brands = $db->getAll("SELECT * FROM " . $brand_to_discount_table . " WHERE discount_id = '" . $v["id"] . "'");
        safeCheck($discount_brands);


        if ($v["discount_type"] == 1) {
            foreach ($discount_products as $kk => $vv) {
                foreach ($cart_products as $kkk => $vvv) {
                    if ($vvv["product_id"] == $vv["product_id"]) {
                        $product_discount = ($vvv["product_price_total"] * $v["discount_value"] / 100);
                        if ($v["items_count_exceeds"] > 0 && $v["items_count_exceeds"] <= $vvv["quantity"]) {
                            $discount_value += $product_discount;
                        }
                        if ($v["items_count_exceeds"] == 0) {
                            $discount_value += $product_discount;
                        }
                        //$vvv["product_price_total"] = $vvv["product_price_total"] - $product_discount;
                        $vvv["product_price_discount"] = (double) $product_discount;
                    }
                    $cart_products[$kkk] = $vvv;
                }
            }

            foreach ($discount_brands as $kk => $vv) {
                foreach ($cart_products as $kkk => $vvv) {
                    $product_tmp = $db->getRow("SELECT id FROM " . $products_table . " WHERE brand_id = '" . $vv["brand_id"] . "' AND id = '" . $vvv["product_id"] . "'");
                    safeCheck($product_tmp);
                    if ($product_tmp["id"]) {
                        if ($vvv["product_price_discount"] > 0.0) {
                            $product_discount = (($vvv["product_price_total"] - $vvv["product_price_discount"]) * $v["discount_value"] / 100);
                        } else {
                            $product_discount = (($vvv["product_price_total"]) * $v["discount_value"] / 100);
                        }

                        if ($v["items_count_exceeds"] > 0 && $v["items_count_exceeds"] <= $vvv["quantity"]) {
                            $discount_value += $product_discount;
                        }
                        if ($v["items_count_exceeds"] == 0) {
                            $discount_value += $product_discount;
                        }
                        //$vvv["product_price_total"] = $vvv["product_price_total"] - $product_discount;
                        $vvv["product_price_discount"] = (double) $product_discount + $vvv["product_price_discount"];
                    }
                    $cart_products[$kkk] = $vvv;
                }
            }

            if ($_SERVER["REMOTE_ADDR"] == "84.201.192.58") {
                //dbg($discount_brands);
            }

            foreach ($discount_categories as $kk => $vv) {
                foreach ($cart_products as $kkk => $vvv) {
                    $product_tmp = $db->getRow("SELECT product_id FROM " . $product_to_category_table . " WHERE category_id = '" . $vv["category_id"] . "' AND product_id = '" . $vvv["product_id"] . "'");
                    safeCheck($product_tmp);
                    if ($product_tmp["product_id"]) {
                        if ($vvv["product_price_discount"] > 0.0) {
                            $product_discount = (($vvv["product_price_total"] - $vvv["product_price_discount"]) * $v["discount_value"] / 100);
                        } else {
                            $product_discount = (($vvv["product_price_total"]) * $v["discount_value"] / 100);
                        }
                        if ($v["items_count_exceeds"] > 0 && $v["items_count_exceeds"] <= $vvv["quantity"]) {
                            $discount_value += $product_discount;
                        }
                        if ($v["items_count_exceeds"] == 0) {
                            $discount_value += $product_discount;
                        }
                        //$vvv["product_price_total"] = $vvv["product_price_total"] - $product_discount;
                        $vvv["product_price_discount"] = (double) $product_discount + $vvv["product_price_discount"];
                    }
                    $cart_products[$kkk] = $vvv;
                }
            }
        }

        $total_amount = $total_amount - $discount_value;

        if ($v["discount_type"] == 2 && sizeof($discount_brands) == 0 && sizeof($discount_categories) == 0 && sizeof($discount_products) == 0) {
            if ($v["order_amount_exceeds"] <= $total_amount) {
                $discount_results["discount_delivery_amount"] = $cart["delivery_amount"];
                $discount_results["discount_free_delivery"] = 1;
            }
        } elseif ($v["discount_type"] == 2 && sizeof($discount_products) > 0) {
            foreach ($cart_products as $k => $v) {
                foreach ($discount_products as $kk => $vv) {
                    if ($vv["product_id"] == $v["product_id"]) {
                        $free_delivery = 1;
                    }
                }
            }
            if ($free_delivery) {
                $discount_results["discount_free_delivery"] = 1;
            }
        }

        /*
          if ( sizeof($discount_products) > 0 ){
          foreach( $discount_products as $kk => $vv){
          foreach($cart_products as $kkk => $vvv){
          if ( $vvv["product_id"] == $vv["product_id"] ){
          $cart_products["use_discount"] = 1;
          }
          $cart_products[$kkk] = $vvv;
          }
          }
          }
          dbg($cart_products);
          dbg($discount_discounts);
          dbg($discount_products);
          dbg($discount_categories);
          dbg($discount_brands);
         */
    }
    $discount_results["discount_amount"] = $discount_value;

    return $discount_results;
}

function copyImageImport($file, $dest, $tn = "", $tntn = "", $size = "") {
    $image = "";

    if (strstr(mime_content_type($file), "image")) {
        $path_info_big = $file;
        $rnd = rand(10000, 99999);
        $unix_time = time();
        $source_big = $file;
        //$fname = strtolower(time()."_".str_replace(" ","_",$file["name"]));
        //$name_big = $fname;
        $name_big = "{$unix_time}{$rnd}.png";

        $name_big = strtolower($name_big);
        $dest_big = "{$dest}/{$name_big}";
        $dest_tn = "{$tn}/{$name_big}";
        $dest_tntn = "{$tntn}/{$name_big}";

        if (@copy($source_big, $dest_big)) {
            $image = $name_big;
            $img = new bimage($dest_big);
            /*
              if($img->width > 700 ) {
              $img->resizeByWidth(700);
              $img->save();
              }
             */
        }
        if ($size != "" || $tn != "") {
            $s = explode("x", $size);
            @copy($source_big, $dest_tn);
            @copy($dest_tn, $dest_tntn);

            $image = $name_big;

            $img = new bimage($dest_tn);
            if ($s[0] == 0)
                $img->resizeByHeight($s[1]);
            elseif ($s[1] == 0)
                $img->resizeByWidth($s[0]);
            else
                $img->autoResize($s[0], $s[1]);
            $img->save();

            $img1 = new bimage($dest_tntn);
            $img1->resizeByHeight(120);
            $img1->save();
        }
    }
    return $image;
}

?>