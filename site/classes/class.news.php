<?php

class News extends Settings {

    function getRecord($id = 0) {
        global $db;
        global $lng;
        global $news_table;

        $id = (int) $id;

        $row = $db->getRow("SELECT *, name_{$lng} AS name FROM " . $news_table . " WHERE id = '" . $id . "'");
        safeCheck($row);

        return $row;
    }

    function updateRow($test = "") {
        global $db;
        global $news_table;

        $row = $db->getRow("SELECT * FROM " . $news_table . "");
        safeCheck($row);

        return $row;
    }

    function addEditRow($params) {
        global $db;
        global $news_table;

        $act = $params["act"];
        $id = (int) $params["id"];
        $fields = array(
            'active' => $params['active'],
            'name_bg' => htmlspecialchars(trim($params['name_bg'])),
            'name_en' => htmlspecialchars(trim($params['name_en'])),
            'name_de' => htmlspecialchars(trim($params['name_de'])),
            'name_ru' => htmlspecialchars(trim($params['name_ru'])),
            'name_it' => htmlspecialchars(trim($params['name_it'])),
            'name_gr' => htmlspecialchars(trim($params['name_gr'])),
            'name_tr' => htmlspecialchars(trim($params['name_tr'])),
            'name_is' => htmlspecialchars(trim($params['name_is'])),
            'h1_bg' => htmlspecialchars(trim($params['h1_bg'])),
            'h1_en' => htmlspecialchars(trim($params['h1_en'])),
            'h1_de' => htmlspecialchars(trim($params['h1_de'])),
            'h1_ru' => htmlspecialchars(trim($params['h1_ru'])),
            'h1_it' => htmlspecialchars(trim($params['h1_it'])),
            'h1_gr' => htmlspecialchars(trim($params['h1_gr'])),
            'h1_tr' => htmlspecialchars(trim($params['h1_tr'])),
            'h1_is' => htmlspecialchars(trim($params['h1_is'])),
            'url_bg' => htmlspecialchars(trim($params['url_bg'])),
            'url_en' => htmlspecialchars(trim($params['url_en'])),
            'url_de' => htmlspecialchars(trim($params['url_de'])),
            'url_ru' => htmlspecialchars(trim($params['url_ru'])),
            'url_it' => htmlspecialchars(trim($params['url_it'])),
            'url_gr' => htmlspecialchars(trim($params['url_gr'])),
            'url_tr' => htmlspecialchars(trim($params['url_tr'])),
            'url_is' => htmlspecialchars(trim($params['url_is'])),
            'labels_bg' => $params['labels_bg'],
            'labels_en' => $params['labels_en'],
            'labels_de' => $params['labels_de'],
            'labels_ru' => $params['labels_ru'],
            'labels_it' => $params['labels_it'],
            'labels_gr' => $params['labels_gr'],
            'labels_tr' => $params['labels_tr'],
            'labels_is' => $params['labels_is'],
            'author_bg' => htmlspecialchars(trim($params['author_bg'])),
            'author_en' => htmlspecialchars(trim($params['author_en'])),
            'author_de' => htmlspecialchars(trim($params['author_de'])),
            'author_ru' => htmlspecialchars(trim($params['author_ru'])),
            'author_it' => htmlspecialchars(trim($params['author_it'])),
            'author_gr' => htmlspecialchars(trim($params['author_gr'])),
            'author_tr' => htmlspecialchars(trim($params['author_tr'])),
            'author_is' => htmlspecialchars(trim($params['author_is'])),
            'excerpt_bg' => (trim($params['excerpt_bg']) == '<br />' ? '' : trim($params['excerpt_bg'])),
            'excerpt_en' => (trim($params['excerpt_en']) == '<br />' ? '' : trim($params['excerpt_en'])),
            'excerpt_de' => (trim($params['excerpt_de']) == '<br />' ? '' : trim($params['excerpt_de'])),
            'excerpt_ru' => (trim($params['excerpt_ru']) == '<br />' ? '' : trim($params['excerpt_ru'])),
            'excerpt_it' => (trim($params['excerpt_it']) == '<br />' ? '' : trim($params['excerpt_it'])),
            'excerpt_gr' => (trim($params['excerpt_gr']) == '<br />' ? '' : trim($params['excerpt_gr'])),
            'excerpt_tr' => (trim($params['excerpt_tr']) == '<br />' ? '' : trim($params['excerpt_tr'])),
            'excerpt_is' => (trim($params['excerpt_is']) == '<br />' ? '' : trim($params['excerpt_is'])),
            'meta_title_bg' => $params['meta_title_bg'],
            'meta_title_en' => $params['meta_title_en'],
            'meta_title_de' => $params['meta_title_de'],
            'meta_title_ru' => $params['meta_title_ru'],
            'meta_title_it' => $params['meta_title_it'],
            'meta_title_gr' => $params['meta_title_gr'],
            'meta_title_tr' => $params['meta_title_tr'],
            'meta_title_is' => $params['meta_title_is'],
            'pic_alt_bg' => $params['pic_alt_bg'],
            'pic_alt_en' => $params['pic_alt_en'],
            'pic_alt_de' => $params['pic_alt_de'],
            'pic_alt_ru' => $params['pic_alt_ru'],
            'pic_alt_it' => $params['pic_alt_it'],
            'pic_alt_gr' => $params['pic_alt_gr'],
            'pic_alt_tr' => $params['pic_alt_tr'],
            'pic_alt_is' => $params['pic_alt_is'],
            'file_name_bg' => $params['file_name_bg'],
            'file_name_en' => $params['file_name_en'],
            'file_name_de' => $params['file_name_de'],
            'file_name_ru' => $params['file_name_ru'],
            'file_name_it' => $params['file_name_it'],
            'file_name_gr' => $params['file_name_gr'],
            'file_name_tr' => $params['file_name_tr'],
            'file_name_is' => $params['file_name_is'],
            'meta_description_bg' => $params['meta_description_bg'],
            'meta_description_en' => $params['meta_description_en'],
            'meta_description_de' => $params['meta_description_de'],
            'meta_description_ru' => $params['meta_description_ru'],
            'meta_description_it' => $params['meta_description_it'],
            'meta_description_gr' => $params['meta_description_gr'],
            'meta_description_tr' => $params['meta_description_tr'],
            'meta_description_is' => $params['meta_description_is'],
            'meta_keywords_bg' => $params['meta_keywords_bg'],
            'meta_keywords_en' => $params['meta_keywords_en'],
            'meta_keywords_de' => $params['meta_keywords_de'],
            'meta_keywords_ru' => $params['meta_keywords_ru'],
            'meta_keywords_it' => $params['meta_keywords_it'],
            'meta_keywords_gr' => $params['meta_keywords_gr'],
            'meta_keywords_tr' => $params['meta_keywords_tr'],
            'meta_keywords_is' => $params['meta_keywords_is'],
            'meta_metatags_bg' => $params['meta_metatags_bg'],
            'meta_metatags_en' => $params['meta_metatags_en'],
            'meta_metatags_de' => $params['meta_metatags_de'],
            'meta_metatags_ru' => $params['meta_metatags_ru'],
            'meta_metatags_it' => $params['meta_metatags_it'],
            'meta_metatags_gr' => $params['meta_metatags_gr'],
            'meta_metatags_tr' => $params['meta_metatags_tr'],
            'meta_metatags_is' => $params['meta_metatags_is'],
            'description_bg' => (trim($params['description_bg']) == '<br />' ? '' : trim($params['description_bg'])),
            'description_en' => (trim($params['description_en']) == '<br />' ? '' : trim($params['description_en'])),
            'description_de' => (trim($params['description_de']) == '<br />' ? '' : trim($params['description_de'])),
            'description_ru' => (trim($params['description_ru']) == '<br />' ? '' : trim($params['description_ru'])),
            'description_it' => (trim($params['description_it']) == '<br />' ? '' : trim($params['description_it'])),
            'description_gr' => (trim($params['description_gr']) == '<br />' ? '' : trim($params['description_gr'])),
            'description_tr' => (trim($params['description_tr']) == '<br />' ? '' : trim($params['description_tr'])),
            'description_is' => (trim($params['description_is']) == '<br />' ? '' : trim($params['description_is'])),
            'postdate' => mktime(0, 0, 0, $params["postdate_Month"], $params["postdate_Day"], $params["postdate_Year"]),
            'publishdate' => strtotime($params["publishdate"]),
            'publishdate_2' => date("Y-m-d", strtotime($params["publishdate"])),
            'firstpage' => $params["firstpage"],
            'firstpage_right' => $params["firstpage_right"],
            'is_event' => $params["is_event"],
            'cms_user_id' => $_SESSION["uid"],
            'leading_news' => (isset($params["leading_news"]) ? $params["leading_news"] : ''),
            'publication' => $params["publication"],
            'align' => $params["align"],
        );

        $pic_left = copyImage($_FILES['pic_left'], "../files/", "../files/tn/", "../files/tntn/", "250x");
        if (!empty($pic_left))
            $fields['pic_left'] = $pic_left;

        $pic_1 = copyImage($_FILES['pic_1'], "../files/", "../files/tn/", "../files/tntn/", "250x");
        if (!empty($pic_1))
            $fields['pic_1'] = $pic_1;

        $pic_2 = copyImage($_FILES['pic_2'], "../files/", "../files/tn/", "../files/tntn/", "250x");
        if (!empty($pic_2))
            $fields['pic_2'] = $pic_2;

        $doc = copyFile($_FILES['doc'], "../files/");
        if (!empty($doc))
            $fields['doc'] = $doc;


        if ($act == "add") {
            shiftPos($db, $news_table);
            $res = $db->autoExecute($news_table, $fields, DB_AUTOQUERY_INSERT);
            safeCheck($res);
            $id = mysqli_insert_id($db->connection);
        }

        if ($act == "edit") {
            $id = (int) $params["id"];
            $res = $db->autoExecute($news_table, $fields, DB_AUTOQUERY_UPDATE, "id = " . $id);
            safeCheck($res);
        }
    }

    function postImage($file = "", $news_id = 0) {
        global $db;
        global $news_images_table;

        $fields = array(
            "file" => $file,
            "news_id" => $news_id,
        );
        shiftPos($db, $news_images_table);
        $res = $db->autoExecute($news_images_table, $fields, DB_AUTOQUERY_INSERT);
        safeCheck($res);

        return $res;
    }

    function deleteImage($id) {
        global $db;
        global $news_images_table;

        $fields = array(
            "edate" => time()
        );
        $res = $db->autoExecute($news_images_table, $fields, DB_AUTOQUERY_UPDATE, " id = '" . $id . "' ");
        safeCheck($res);

        return $res;
    }

    public static function getImages($news_id) {
        global $db;
        global $lng;
        global $news_images_table;

        $images = $db->getAll("SELECT * FROM " . $news_images_table . " WHERE edate = 0 AND news_id = '" . $news_id . "' ORDER BY pos");
        safeCheck($images);
        return $images;
    }

    function getNews($page = 0) {
        global $db;
        global $lng;
        global $news_table;

        $news = $db->getAll("SELECT *, name_{$lng} AS name FROM " . $news_table . " WHERE edate = 0 AND active = 'checked' ORDER BY pos");
        safeCheck($news);
        foreach ($news as $k => $v) {

            $news[$k] = $v;
        }
        return $news;
    }

    function getPage($id) {
        global $sm;
        global $db;
        global $lng;

        global $language_file;
        global $htaccess_file;
        global $htaccess_file_bg;
        global $htaccess_file_en;
        global $htaccess_file_de;
        global $htaccess_file_ru;
        global $htaccess_file_ro;

        global $news_table;
        global $news_images_table;
        global $news_categories_table;
        global $news_to_news_categories_table;

        $sm->configLoad($language_file);
        $configVars = $sm->getConfigVars();
        $sm->configLoad($htaccess_file);
        $htaccessVars = $sm->getConfigVars();

        $row = $db->getRow("SELECT 
                                *,
                                name_{$lng} AS name,
                                  name_bg AS name_bg, 
                                  name_en AS name_en, 
                                description_{$lng} AS description,
                                excerpt_{$lng} AS excerpt,
                                htaccess_url_{$lng} AS htaccess_url,
                                /*author_{$lng} AS author,
                                resource_{$lng} AS resource,*/
                                meta_title_{$lng} AS meta_title,
                                meta_keywords_{$lng} AS meta_keywords,
                                meta_description_{$lng} AS meta_description,
                                meta_metatags_{$lng} AS meta_metatags,
                                pic,
                                postdate,
                                publishdate
                            FROM " . $news_table . "
                            WHERE id = '{$id}'
                            ");
        safeCheck($row);
        $row['author'] = self::getNewsAuthor($row['news_author_id']);
        $row['news_date'] = date("d", $row["publishdate"]) . " " . self::getMonthName($row["publishdate"]) . " " . date("Y", $row["publishdate"]);
        $row['news_categories'] = self::getNewsCategoriesByNews($row['id']);

        $images = self::getImages($id);
        $sm->assign("images", $images);

        if ($_SERVER["REQUEST_URI"] == "/" . $htaccessVars["htaccess_news_article"] . "/" . $row["id"] && trim($row["htaccess_url"])) {
            header('HTTP/1.1 301 Moved Permanently');
            header('Location: ' . $row["htaccess_url"]);
            die();
        }

        if ($row["edate"] > 0 || (int) $row["active"] != 1) {
            header("HTTP/1.0 404 Not Found");
            header("Location: /messages/100");
            die();
        }

        $sm->assign("infoKeys", $row['meta_keywords']);
        if ($row["meta_title"]) {
            $sm->assign("infoTitle", $row['meta_title']);
        } else {
            $sm->assign("infoTitle", $row['name']);
        }
        $sm->assign("infoDescr", $row['meta_description']);

        $sm->assign("row", $row);

        $latest_news = self::getLatestNews(2);
        $sm->assign("latest_news", $latest_news);


        $category = $db->getRow("SELECT categories.id, 
											categories.name_{$lng} AS name, 
											categories.htaccess_url_{$lng} AS htaccess_url 
									 FROM " . $news_categories_table . " AS categories, 
										  " . $news_to_news_categories_table . " AS ntnc
									 WHERE categories.edate = 0
									 AND ntnc.news_id = '" . $id . "'
									 AND categories.name_{$lng} <> ''
									 AND ntnc.news_category_id = categories.id
									 ");
        safeCheck($category);

        $crumbs = '<a href="' . $host . '" class="single">' . $configVars["home_breadcrumbs"] . '</a>';
        if ($category["id"]) {
            if ($category["htaccess_url"]) {
                $crumbs .= '<a href="' . $category["htaccess_url"] . '" class="single">' . $category["name"] . '</a>';
            } else {
                $crumbs .= '<a href="/' . $htaccessVars["htaccess_news_categories"] . '/' . $category["id"] . '" class="single">' . $category["name"] . '</a>';
            }
        }
        $crumbs .= '<a href="/' . $htaccessVars["htaccess_news_article"] . '/' . $row['id'] . '" class="single">' . $row["name"] . '</a>';

        $sm->assign("breadcrumbs", $crumbs);


        global $htaccess_file_bg;
        global $htaccess_file_en;

        $sm->configLoad($htaccess_file_en);
        $htaccess_en = $sm->getConfigVars();
        if ($row["htaccess_url_en"]) {
            $link_en = $row["htaccess_url_en"];
        } else {
            if ($row["url_en"]) {
                $link_en = $row["url_en"];
            } else {
                $link_en = "/" . $htaccess_en["htaccess_news_article"] . "/" . $row["id"];
            }
        }

        $sm->configLoad($htaccess_file_ro);
        $htaccess_ro = $sm->getConfigVars();
        if ($row["htaccess_url_ro"]) {
            $link_ro = $row["htaccess_url_ro"];
        } else {
            if ($row["url_ro"]) {
                $link_ro = $row["url_ro"];
            } else {
                $link_ro = "/" . $htaccess_ro["htaccess_news_article"] . "/" . $row["id"];
            }
        }/**/

        $sm->configLoad($htaccess_file_bg);
        $htaccess_bg = $sm->getConfigVars();
        if ($row["htaccess_url_bg"]) {
            $link_bg = $row["htaccess_url_bg"];
        } else {
            if ($row["url_bg"]) {
                $link_bg = $row["url_bg"];
            } else {
                $link_bg = "/" . $htaccess_bg["htaccess_news_article"] . "/" . $row["id"];
            }
        }
        if (strlen($row['name_en']) == 0) {
            unset($link_en);
        }
        if (strlen($row['name_bg']) == 0) {
            unset($name_bg);
        }


        $sm->assign("link_bg", $link_bg);
        $sm->assign("link_en", $link_en);
        $sm->assign("link_ro", $link_ro);
        $sm->assign("page_news", 1);

        $sm->display("show_news.html");

        // Increment views count
        $res = $db->query("UPDATE {$news_table} SET views = views + 1 WHERE id = {$id} AND edate = 0");
        safeCheck($res);
        die();
    }

    function getListByMonth($date) {
        global $sm;
        global $db;
        global $lng;
        global $htaccess_file;
        global $language_file;
        global $news_table;
        global $news_images_table;


        $news = $db->getAll("SELECT id,
										  name_{$lng} AS name,
										  excerpt_{$lng} AS excerpt,
										  pic,
										  htaccess_url_{$lng} AS htaccess_url,
										  postdate,
										  publishdate
								FROM " . $news_table . "
								WHERE edate =0 
								AND FROM_UNIXTIME(publishdate, '%Y/%m/') = '" . $date . "'
								AND active = 'checked'
								AND name_{$lng} <> ''
								ORDER BY publishdate DESC
								");
        safeCheck($news);

        $sm->assign("news", $news);

        $sm->configLoad($language_file);
        $configVars = $sm->getConfigVars();
        $sm->configLoad($htaccess_file);
        $htaccessVars = $sm->getConfigVars();

        $crumbs = '<a href="' . $host . '" class="linkBreadcrumbs">' . $configVars["home_breadcrumbs"] . '</a>';
        $crumbs .= '<a href="/' . $date . '" class="linkBreadcrumbs">' . $configVars["news"] . '</a>';

        $sm->assign("breadcrumbs", $crumbs);

        $sm->assign("page_news", 1);
        $sm->display("news.html");
    }

    function getNewsMonthCategories() {
        global $sm;
        global $db;
        global $lng;
        global $htaccess_file;
        global $language_file;
        global $news_table;
        global $news_images_table;

        $news_months = $db->getAll("SELECT DISTINCT FROM_UNIXTIME(publishdate, '%Y-%m') AS month FROM " . $news_table . " WHERE edate = 0 AND active = 'checked' ORDER BY publishdate DESC");
        safeCheck($news_months);

        foreach ($news_months as $k => $v) {
            $tmp = explode("-", $v["month"]);
            if ($lng == "bg") {
                switch ($tmp[1]) {
                    case "01": $v["month_text"] = "Януари";
                        break;
                    case "02": $v["month_text"] = "Февруари";
                        break;
                    case "03": $v["month_text"] = "Март";
                        break;
                    case "04": $v["month_text"] = "Април";
                        break;
                    case "05": $v["month_text"] = "Май";
                        break;
                    case "06": $v["month_text"] = "Юни";
                        break;
                    case "07": $v["month_text"] = "Юли";
                        break;
                    case "08": $v["month_text"] = "Август";
                        break;
                    case "09": $v["month_text"] = "Септември";
                        break;
                    case "10": $v["month_text"] = "Октомври";
                        break;
                    case "11": $v["month_text"] = "Ноември";
                        break;
                    case "12": $v["month_text"] = "Декември";
                        break;
                }
            } else {
                switch ($tmp[1]) {
                    case "01": $v["month_text"] = "January";
                        break;
                    case "02": $v["month_text"] = "February";
                        break;
                    case "03": $v["month_text"] = "March";
                        break;
                    case "04": $v["month_text"] = "April";
                        break;
                    case "05": $v["month_text"] = "May";
                        break;
                    case "06": $v["month_text"] = "June";
                        break;
                    case "07": $v["month_text"] = "July";
                        break;
                    case "08": $v["month_text"] = "August";
                        break;
                    case "09": $v["month_text"] = "September";
                        break;
                    case "10": $v["month_text"] = "October";
                        break;
                    case "11": $v["month_text"] = "November";
                        break;
                    case "12": $v["month_text"] = "December";
                        break;
                }
            }
            $v["month"] = $tmp[1];
            $v["year"] = $tmp[0];
            $news_months[$k] = $v;
        }

        return $news_months;
    }

    function getNewsCategories() {
        global $sm;
        global $db;
        global $lng;
        global $htaccess_file;
        global $language_file;
        global $news_table;
        global $news_images_table;
        global $news_categories_table;

        $news_categories = $db->getAll("SELECT id, name_{$lng} AS name, htaccess_url_{$lng} AS htaccess_url FROM " . $news_categories_table . " WHERE edate = 0 AND name_{$lng} <> '' ORDER BY pos");
        safeCheck($news_categories);

        return $news_categories;
    }

    function getBlogPage() {
        global $sm;
        global $db;
        global $lng;

        $accent_news = self::getAccentNews(3);
        $sm->assign("accent_news", $accent_news);

        $latest_news = self::getLatestNews(6);
        $sm->assign("latest_news", $latest_news);


        $sm->display("news.html");
    }

    function getNewsCategoryPage($id, $page = 0) {
        global $sm;
        global $db;
        global $lng;
        global $host;
        global $htaccess_file;
        global $language_file;
        global $news_table;
        global $news_images_table;
        global $news_categories_table;
        global $news_to_news_categories_table;
        global $htaccess_file_bg;
        global $htaccess_file_en;

        $row = $db->getRow("SELECT id, name_{$lng} AS name, htaccess_url_{$lng} AS htaccess_url, meta_title_{$lng} AS meta_title, meta_keywords_{$lng} AS meta_keywords, meta_metatags_{$lng} AS meta_metatags, meta_description_{$lng} AS meta_description FROM " . $news_categories_table . " WHERE id = '" . $id . "'");
        safeCheck($row);
        $sm->assign("row", $row);

        if ($page && $lng == "bg") {
            $meta_addon = " - стр. " . $page;
        }

        if ($row["meta_title"]) {
            $sm->assign("infoTitle", $row['meta_title'] . $meta_addon);
        } else {
            $sm->assign("infoTitle", $row['name'] . $meta_addon);
        }
        $sm->assign("infoDescr", $row['meta_description'] . $meta_addon);

        $sm->configLoad($htaccess_file);
        $htaccessVars = $sm->getConfigVars();

        if ($row["htaccess_url"]) {
            $url_prefix = $row["htaccess_url"];
            $url_suffix = "/";
        } else {
            $url_prefix = $host . $htaccessVars["htaccess_news_categories"] . "/" . $row["id"];
            $url_suffix = "";
        }


        $limit = 10;
        if ($page) {
            $page = $page - 1;
        }
        $start = $page * $limit;

        $counter = $db->getRow("SELECT count(news.id) AS cntr
								FROM " . $news_table . " AS news,
									 " . $news_to_news_categories_table . " AS ntnc
								WHERE news.edate = 0 
								AND ntnc.news_category_id = '" . $id . "'
								AND news.name_{$lng} <> ''
								AND active = 'checked'
								AND news.publishdate_2 <= '" . date("Y-m-d") . "'
								AND news.id = ntnc.news_id");
        safeCheck($counter);
        $pages = ceil($counter["cntr"] / $limit);
        for ($i = 0; $i < $pages; $i++) {
            if ($i == 0) {
                if ($i == $page) {
                    $generate_pages .= '<a href="' . $url_prefix . '" class="thispage">' . ($i + 1) . '</a>';
                } else {
                    $generate_pages .= '<a href="' . $url_prefix . '">' . ($i + 1) . '</a>';
                }
            } else {
                if ($i == $page) {
                    $generate_pages .= '<a href="' . $url_prefix . "/" . $htaccessVars["htaccess_page"] . "/" . ($i + 1) . $url_suffix . '" class="thispage">' . ($i + 1) . '</a>';
                } else {
                    $generate_pages .= '<a href="' . $url_prefix . "/" . $htaccessVars["htaccess_page"] . "/" . ($i + 1) . $url_suffix . '">' . ($i + 1) . '</a>';
                }
            }
        }
        if ($page < $pages - 1) {
            $generate_pages .= '<a href="' . $url_prefix . "/" . $htaccessVars["htaccess_page"] . "/" . ($page + 2) . $url_suffix . '" class="next">&gt;</a>';
        }

        if ($page > 0) {
            if ($page == 1) {
                $generate_pages = '<a href="' . $url_prefix . '" class="prev">&lt;</a>' . $generate_pages;
            } else {
                $generate_pages = '<a href="' . $url_prefix . "/" . $htaccessVars["htaccess_page"] . "/" . ($page) . $url_suffix . '" class="prev">&lt;</a>' . $generate_pages;
            }
        }

        $generate_pages = $generate_pages;
        $sm->assign("generate_pages", $generate_pages);

        $news = $db->getAll("SELECT news.id,
										  news.name_{$lng} AS name,
										  news.excerpt_{$lng} AS excerpt,
										  news.pic,
										  news.htaccess_url_{$lng} AS htaccess_url,
										  news.postdate,
										  news.publishdate
								FROM " . $news_table . " AS news,
									 " . $news_to_news_categories_table . " AS ntnc
								WHERE news.edate = 0 
								AND ntnc.news_category_id = '" . $id . "'
								AND news.name_{$lng} <> ''
								AND news.publishdate_2 <= '" . date("Y-m-d") . "'
								AND active = 'checked'
								AND news.id = ntnc.news_id
								ORDER BY news.publishdate DESC
								LIMIT {$start}, {$limit}
								");
        safeCheck($news);

        $sm->assign("news", $news);

        $sm->configLoad($language_file);
        $configVars = $sm->getConfigVars();
        $sm->configLoad($htaccess_file);
        $htaccessVars = $sm->getConfigVars();

        $crumbs = '<a href="' . $host . '" class="linkBreadcrumbs">' . $configVars["home_breadcrumbs"] . '</a>';
        $crumbs .= '<a href="' . $url_prefix . '" class="linkBreadcrumbs">' . $configVars["news"] . '</a>';

        $sm->assign("breadcrumbs", $crumbs);





        $sm->configLoad($htaccess_file_en);
        $htaccess_en = $sm->getConfigVars();
        if ($row["htaccess_url_en"]) {
            $link_en = $row["htaccess_url_en"];
        } else {
            if ($row["url_en"]) {
                $link_en = $row["url_en"];
            } else {
                $link_en = "/" . $htaccess_en["htaccess_news_categories"] . "/" . $row["id"];
            }
        }

        $sm->configLoad($htaccess_file_bg);
        $htaccess_bg = $sm->getConfigVars();
        if ($row["htaccess_url_bg"]) {
            $link_bg = $row["htaccess_url_bg"];
        } else {
            if ($row["url_bg"]) {
                $link_bg = $row["url_bg"];
            } else {
                $link_bg = "/" . $htaccess_bg["htaccess_news_categories"] . "/" . $row["id"];
            }
        }

        $sm->assign("link_bg", $link_bg);
        $sm->assign("link_en", $link_en);



        $sm->assign("page_news", 1);
        $sm->display("news.html");
    }

    public static function getLatestNews($limit = 4) {
        global $sm;
        global $db;
        global $lng;
        global $host;
        global $htaccess_file;
        global $language_file;
        global $news_table;
        global $news_images_table;
        global $news_categories_table;
        global $news_to_news_categories_table;


        $latest_news = $db->getAll("SELECT news.id,
										  news.name_{$lng} AS name,
										  news.excerpt_{$lng} AS excerpt,
										  news.pic,
										  news.news_author_id,
										  news.htaccess_url_{$lng} AS htaccess_url,
										  news.postdate,
										  news.publishdate
								FROM " . $news_table . " AS news
								WHERE news.edate = 0 
								AND news.name_{$lng} <> ''
								AND active = 1
								AND news.publishdate_2 <= '" . date("Y-m-d") . "'
								ORDER BY publishdate DESC
								LIMIT {$limit}
								");
        safeCheck($latest_news);
        foreach ($latest_news as $k => $v) {
            $v['author'] = self::getNewsAuthor($v['news_author_id']);
            $v['news_date'] = date("d", $v["publishdate"]) . " " . self::getMonthName($v["publishdate"]) . " " . date("Y", $v["publishdate"]);
            $v['news_categories'] = self::getNewsCategoriesByNews($v['id']);

            $latest_news[$k] = $v;
        }
        return $latest_news;
    }

    public static function getAccentNews($limit = 4) {
        global $sm;
        global $db;
        global $lng;
        global $host;
        global $htaccess_file;
        global $language_file;
        global $news_table;
        global $news_images_table;
        global $news_categories_table;
        global $news_to_news_categories_table;


        $results = $db->getAll("SELECT news.id,
										  news.name_{$lng} AS name,
										  news.excerpt_{$lng} AS excerpt,
										  news.pic,
										  news.pic_header,
										  news.news_author_id,
										  news.htaccess_url_{$lng} AS htaccess_url,
										  news.postdate,
										  news.publishdate
								FROM " . $news_table . " AS news
								WHERE news.edate = 0 
								AND news.name_{$lng} <> ''
								AND active = 1
								AND accent = 1
								AND news.publishdate_2 <= '" . date("Y-m-d") . "'
								ORDER BY publishdate DESC
								LIMIT {$limit}
								");
        safeCheck($results);
        foreach ($results as $k => $v) {
            $v['author'] = self::getNewsAuthor($v['news_author_id']);
            $v['news_date'] = date("d", $v["publishdate"]) . " " . self::getMonthName($v["publishdate"]) . " " . date("Y", $v["publishdate"]);
            $v['news_categories'] = self::getNewsCategoriesByNews($v['id']);

            $results[$k] = $v;
        }

        return $results;
    }

    public static function getNewsAuthor($id = 0) {
        global $sm;
        global $db;
        global $lng;
        global $news_authors_table;

        if ($id) {
            $result = $db->getRow("SELECT *, name_{$lng} AS name FROM " . $news_authors_table . " WHERE id = '" . $id . "'");
            safeCheck($result);
        }

        return $result;
    }

    public static function getNewsCategoriesByNews($id = 0) {
        global $sm;
        global $db;
        global $lng;
        global $news_categories_table;
        global $news_to_news_categories_table;

        if ($id) {
            $results = $db->getAll("SELECT news_categories.id,
											  news_categories.name_{$lng} AS name
									   FROM " . $news_categories_table . " AS news_categories,
											" . $news_to_news_categories_table . " AS ntnc
									   WHERE news_categories.edate = 0
									   AND ntnc.news_category_id = news_categories.id 
									   AND ntnc.news_id = '" . $id . "' 
									   ORDER BY news_categories.pos");
            safeCheck($results);
        }

        return $results;
    }

    public static function getMonthName($date) {
        switch (date("m", $date)) {
            case 1: $date = "Януари";
                break;
            case 2: $date = "Февруари";
                break;
            case 3: $date = "Март";
                break;
            case 4: $date = "Април";
                break;
            case 5: $date = "Май";
                break;
            case 6: $date = "Юни";
                break;
            case 7: $date = "Юли";
                break;
            case 8: $date = "Август";
                break;
            case 9: $date = "Септември";
                break;
            case 10: $date = "Октомври";
                break;
            case 11: $date = "Ноември";
                break;
            case 12: $date = "Декември";
                break;
        }
        return $date;
    }

}

?>