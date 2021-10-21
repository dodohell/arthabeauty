<?php

/**
 * Description of helpers
 *
 * @author kkalchev
 */
use MatthiasMullie\Minify;
use WebPConvert\WebPConvert;

class Helpers {

    public static function encrypt_decrypt($action, $string) {
        $output = false;
        $encrypt_method = "AES-256-CBC";
        $secret_key = 'SmartWebAndKakDevelopmentSecretKey';
        $secret_iv = 'SmartWebAndKakDevelopmentIV';
        // hash
        $key = hash('sha256', $secret_key);

        // iv - encrypt method AES-256-CBC expects 16 bytes - else you will get a warning
        $iv = substr(hash('sha256', $secret_iv), 0, 16);
        if ($action == 'encrypt') {
            $output = openssl_encrypt($string, $encrypt_method, $key, 0, $iv);
            $output = base64_encode($output);
        } else if ($action == 'decrypt') {
            $output = openssl_decrypt(base64_decode($string), $encrypt_method, $key, 0, $iv);
        }
        return $output;
    }

    public static function validateDate($date, $format = 'Y-m-d') {
        $d = DateTime::createFromFormat($format, $date);

        return $d && $d->format($format) === $date;
    }

    /**
     * 
     * @global type $recaptcha_secret_key
     * @param type $g_recaptcha_response //$g_recaptcha_response = $params->getString("g-recaptcha-response");
     * @return boolean
     */
    public static function checkReCaptcha($g_recaptcha_response, $returnType = 1) {
        global $recaptcha_secret_key;

        // post request to server
        $url = 'https://www.google.com/recaptcha/api/siteverify';
        $data = array('secret' => $recaptcha_secret_key, 'response' => $g_recaptcha_response);

        $options = array(
            'http' => array(
                'header' => "Content-type: application/x-www-form-urlencoded\r\n",
                'method' => 'POST',
                'content' => http_build_query($data)
            )
        );
        $context = stream_context_create($options);
        $response = file_get_contents($url, false, $context);
        $responseKeys = json_decode($response, true);

        if ($returnType === 2) {
            echo $responseKeys["success"] ? 1 : 0;
            die();
        } else {
            return $responseKeys["success"] ? true : false;
        }
    }

    public static function endswith($string, $test) {
        $strlen = strlen($string);
        $testlen = strlen($test);
        if ($testlen > $strlen) return false;
        return substr_compare($string, $test, $strlen - $testlen, $testlen) === 0;
    }
    
    public static function paginate($current_page, $total_pages, $page_url) {
        global $rightLinksCount;
        global $leftLinksCount;
        global $htaccess_file;
        global $sm;

        $sm->configLoad($htaccess_file);
        $htaccessVars = $sm->getConfigVars();
        $request_uri = $_SERVER['REQUEST_URI'];
//        if (strpos($request_uri, "/" . $htaccessVars["htaccess_page"] . "-1")) {
//            header("HTTP/1.1 301 Moved Permanently");
//            header("Location: " . $page_url);
//            die();
//        }
        if (self::endswith($request_uri, "/" . $htaccessVars["htaccess_page"] . "-1")) {
            header("HTTP/1.1 301 Moved Permanently");
            header("Location: " . $page_url);
            die();
        }

        $pagination = '';
        if ($total_pages > 0 && $current_page <= $total_pages) { //verify total pages and current page number
            $pagination .= ''; //justify-content-center

            $right_links = $current_page + $rightLinksCount;
            $left_links = $current_page - $leftLinksCount;
            $previous = $current_page - 1; //previous link 
            $next = $current_page + 1; //next link
            $first_link = true; //boolean var to decide our first link

            if ($current_page > 1) {
                $previous_link = ($previous == 0) ? 1 : $previous;
                $pagination .= '<a href="' . $page_url . '" title="First">«</a>'; //first link
                if ($previous_link == 1) {
                    $pagination .= '<a href="' . $page_url . '" title="Previous"><</a>'; //previous link
                } else {
                    $pagination .= '<a href="' . $page_url . '/page-' . $previous_link . '" title="Previous"><</a>'; //previous link
                }
                for ($i = $left_links; $i < $current_page; $i++) { //Create left-hand side links
                    if ($i > 0) {
                        if ($i == 1) {
                            $pagination .= '<a href="' . $page_url . '">' . $i . '</a>';
                        } else {
                            $pagination .= '<a href="' . $page_url . '/page-' . $i . '">' . $i . '</a>';
                        }
                    }
                }
                $first_link = false; //set first link to false
            }

            if ($first_link) { //if current active page is first link
                $pagination .= '<span class="active">' . $current_page . '</span>';
            } elseif ($current_page == $total_pages) { //if it's the last active link
                $pagination .= '<span class="active">' . $current_page . '</span>';
            } else { //regular current link
                $pagination .= '<span class="active">' . $current_page . '</span>';
            }

            for ($i = $current_page + 1; $i < $right_links; $i++) { //create right-hand side links
                if ($i <= $total_pages) {
                    $pagination .= '<a href="' . $page_url . '/page-' . $i . '">' . $i . '</a>';
                }
            }
//          
            if ($current_page < $total_pages) {
                $next_link = ($next == $total_pages) ? $total_pages : $next;
                $pagination .= '<a href="' . $page_url . '/page-' . $next_link . '" title="Next">></a>'; //next link
                $pagination .= '<a href="' . $page_url . '/page-' . $total_pages . '" title="Last">»</a>'; //last link
            }

            $pagination .= '';
        }

        return $pagination; //return pagination links
    }

    public static function generateMetaInfo($row, $page = null, $categories = array()) {
        global $sm;

        if ($row["meta_title"]) {
            $infoTitle = $row["meta_title"];

            if ($page) {
                $infoTitle .= ", " . $page;
            }
        } else {
            $infoTitle = $row["name"];
            foreach ($categories as $k => $v) {
                if ($k != 0) {
                    $infoTitle .= ", " . $v["name"];
                } else {
                    $infoTitle .= " - " . $v["name"];
                }
            }
            if ($page) {
                $infoTitle .= ", " . $page;
            }
        }
        if ($row["meta_description"]) {
            $infoDescr = $row["meta_description"];
            if ($page) {
                $infoDescr .= ", " . $page;
            }
        } else {
            $infoDescr = $row["name"];
            foreach ($categories as $k => $v) {
                $infoDescr .= ", " . $v["name"];
            }
            $infoDescr .= ", " . $description["description"];
            if ($page) {
                $infoDescr .= ", " . $page;
            }
        }
        if ($row["meta_keywords"]) {
            $infoKeys = $row["meta_keywords"];
            if ($page) {
                $infoKeys .= ", " . $page;
            }
        } else {
            $infoKeys = $row["name"];
            foreach ($categories as $k => $v) {
                $infoKeys .= ", " . $v["name"];
            }
            $infoKeys .= ", " . $keywords["description"];
            if ($page) {
                $infoKeys .= ", " . $page;
            }
        }

        $sm->assign("infoTitle", $infoTitle);
        $sm->assign("infoDescr", $infoDescr);
        $sm->assign("infoKeys", $infoKeys);
    }

    public static function downloadProductFile(string $fileName, int $product_id) {
        global $install_path;

        $productFileMatch = false;
        $productFiles = Products::getProductFiles($product_id);
        if ($productFiles) {
            foreach ($productFiles as $v) {
                if ($v["doc"] == $fileName) {
                    $productFileMatch = true;
                    break;
                }
            }
        }
        if (!$productFileMatch) {
            header("Location: /messages/410");
            die();
        }

        $filepath = $install_path . "files/" . $fileName;

        // Process download
        if (file_exists($filepath)) {
            header('Content-Description: File Transfer');
            header('Content-Type: application/octet-stream');
            header('Content-Disposition: attachment; filename="' . basename($filepath) . '"');
            header('Expires: 0');
            header('Cache-Control: must-revalidate');
            header('Pragma: public');
            header('Content-Length: ' . filesize($filepath));
            flush(); // Flush system output buffer
            readfile($filepath);
            exit();
        } else {
            header("Location: /messages/410");
            die();
        }
    }

    public static function downloadInfoFile(string $fileName, int $info_id) {
        global $install_path;

        $infoFileMatch = false;
        $infoFiles = Menus::getFiles($info_id);
        if ($infoFiles) {
            foreach ($infoFiles as $v) {
                if ($v["file"] == $fileName) {
                    $infoFileMatch = true;
                    break;
                }
            }
        }
        if (!$infoFileMatch) {
            header("Location: /messages/410");
            die();
        }

        $filepath = $install_path . "files/" . $fileName;

        // Process download
        if (file_exists($filepath)) {
            header('Content-Description: File Transfer');
            header('Content-Type: application/octet-stream');
            header('Content-Disposition: attachment; filename="' . basename($filepath) . '"');
            header('Expires: 0');
            header('Cache-Control: must-revalidate');
            header('Pragma: public');
            header('Content-Length: ' . filesize($filepath));
            flush(); // Flush system output buffer
            readfile($filepath);
            exit();
        } else {
            header("Location: /messages/410");
            die();
        }
    }

    public function getDiscountedPrice($price, $isSpecial, $userGroupId = 0, $convert = true) {
        global $useUserGroups;

        if (is_numeric($price)) {
            $price = (float) $price;
        } else {
            $settings = new Settings();
            $settings->getMessagePage(412);
            die();
        }

        //Convert the price in the proper currency
        $convertor = new Convert();

        if ($useUserGroups && $userGroupId > 0) {
            $userGroup = UserGroups::getRecord($userGroupId);
        } else {
            return $convert ? $convertor->convert($price) : $price;
        }
        $discount = isset($userGroup["discount"]) ? (float) $userGroup["discount"] : 0.0;
        $reduce_special_prices = isset($userGroup["reduce_special_prices"]) ? (int) $userGroup["reduce_special_prices"] : 0;
        if ($discount > 0.0) {
            if ($isSpecial) {
                if ($reduce_special_prices) {
                    $price = $price - ($price * ($discount / 100));
                }
            } else {
                $price = $price - ($price * ($discount / 100));
            }
        }

        return $convert ? $convertor->convert($price) : $price;
    }

    public static function getCurentUserGroupId() {
        global $user;

        return $user["user_group_id"] > 0 ? (int) $user["user_group_id"] : 0;
    }

    public function minifyCSS() {
        global $install_path;

        require_once $install_path . '/site/classes/minify/src/Minify.php';
        require_once $install_path . '/site/classes/minify/src/CSS.php';
        require_once $install_path . '/site/classes/minify/src/JS.php';
        require_once $install_path . '/site/classes/minify/src/Exception.php';
        require_once $install_path . '/site/classes/minify/src/Exceptions/BasicException.php';
        require_once $install_path . '/site/classes/minify/src/Exceptions/FileImportException.php';
        require_once $install_path . '/site/classes/minify/src/Exceptions/IOException.php';
        require_once $install_path . '/site/classes/path-converter/src/ConverterInterface.php';
        require_once $install_path . '/site/classes/path-converter/src/Converter.php';

        $sourcePath4 = $install_path . 'js/swiper/swiper.min.css';
        $minifier = new Minify\CSS($sourcePath4);

        // we can even add another file, they'll then be
        // joined in 1 output file
//        $sourcePath2 = $install_path.'js/bootstrap_4_3_1/css/bootstrap.min.css';
//        $minifier->add($sourcePath2);
//        $sourcePath3 = $install_path.'js/fontawesome-free-5_7_2-web/css/all.min.css';
//        $minifier->add($sourcePath3);

        $sourcePath3 = $install_path . 'js/fancybox-master/dist/jquery.fancybox.css';
        $minifier->add($sourcePath3);
        $sourcePath5 = $install_path . 'js/jquery-ui/jquery-ui.css';
        $minifier->add($sourcePath5);
        $sourcePath6 = $install_path . 'js/select2/dist/css/select2.min.css';
        $minifier->add($sourcePath6);
        $sourcePath7 = $install_path . 'js/cookieconsent/cookieconsent.min.css';
        $minifier->add($sourcePath7);
        $sourcePath8 = $install_path . 'site/css/flag-icon-css/css/flag-icon.css';
        $minifier->add($sourcePath8);
        $sourcePath9 = $install_path . 'site/css/responsive.css';
        $minifier->add($sourcePath9);
        $sourcePath10 = $install_path . 'js/slick/slick.css';
        $minifier->add($sourcePath10);
        $sourcePath11 = $install_path . 'js/slick/slick-theme.css';
        $minifier->add($sourcePath11);
        $sourcePath12 = $install_path . 'js/sweetalert/sweetalert.css';
        $minifier->add($sourcePath12);

        $sourcePath = $install_path . 'site/css/style.css';
        $minifier->add($sourcePath);

        // or we can just add plain CSS
        //$css = 'body { color: #000000; }';
        //$minifier->add($css);
        // save minified file to disk
        $minifiedPath = $install_path . 'site/css/style.min.css';
//        $minifier->minify($minifiedPath);
        $minified = $minifier->minify();
        file_put_contents($minifiedPath, $minified);
    }

    public function minifyJS() {
        global $install_path;

        require_once $install_path . '/site/classes/minify/src/Minify.php';
        require_once $install_path . '/site/classes/minify/src/CSS.php';
        require_once $install_path . '/site/classes/minify/src/JS.php';
        require_once $install_path . '/site/classes/minify/src/Exception.php';
        require_once $install_path . '/site/classes/minify/src/Exceptions/BasicException.php';
        require_once $install_path . '/site/classes/minify/src/Exceptions/FileImportException.php';
        require_once $install_path . '/site/classes/minify/src/Exceptions/IOException.php';
        require_once $install_path . '/site/classes/path-converter/src/ConverterInterface.php';
        require_once $install_path . '/site/classes/path-converter/src/Converter.php';

        $sourcePath = $install_path . 'js/bootstrap_4_3_1/js/bootstrap.bundle.min.js';
        $minifier = new Minify\JS($sourcePath);

        // we can even add another file, they'll then be
        // joined in 1 output file

        $sourcePath4 = $install_path . 'js/swiper/swiper.min.js';
        $minifier->add($sourcePath4);
        $sourcePath3 = $install_path . 'js/fancybox-master/dist/jquery.fancybox.min.js';
        $minifier->add($sourcePath3);
        $sourcePath5 = $install_path . 'js/jquery-ui/jquery-ui.min.js';
        $minifier->add($sourcePath5);
        $sourcePath6 = $install_path . 'js/jquery-ui/jquery.ui.touch-punch.min.js';
        $minifier->add($sourcePath6);
        $sourcePath7 = $install_path . 'js/select2/dist/js/select2.min.js';
        $minifier->add($sourcePath7);
        $sourcePath8 = $install_path . 'js/slick/slick.min.js';
        $minifier->add($sourcePath8);
        $sourcePath9 = $install_path . 'js/raty/js/jquery.raty.js';
        $minifier->add($sourcePath9);
        $sourcePath10 = $install_path . 'js/cookieconsent/scriptCookies.js';
        $minifier->add($sourcePath10);
        $sourcePath11 = $install_path . 'js/cookieconsent/cookieconsent.min.js';
        $minifier->add($sourcePath11);
        $sourcePath12 = $install_path . 'js/lazysizes.min.js';
        $minifier->add($sourcePath12);
        $sourcePath13 = $install_path . 'js/popperJS_1_12_5/popper.min.js';
        $minifier->add($sourcePath13);
        $sourcePath14 = $install_path . 'js/sweetalert/sweetalert-dev.js';
        $minifier->add($sourcePath14);
        $sourcePath15 = $install_path . 'js/jquery.cookie.js';
        $minifier->add($sourcePath15);

        // or we can just add plain CSS
        //$css = 'body { color: #000000; }';
        //$minifier->add($css);
        // save minified file to disk
        $minifiedPath = $install_path . 'js/all.min.js';
        $minifier->minify($minifiedPath);
//        $minified = $minifier->minify();
//        file_put_contents($minifiedPath, $minified);
    }

    public function convertToWebp($source, $destination) {
        //ini_set("display_errors", 1);
//        error_reporting(E_ALL & ~(E_STRICT|E_NOTICE));
        //error_reporting(E_ALL);
        //global $install_path;
        //$source = $install_path . 'images/main-article-2.jpg';
        //$destination = $source . '_test_.webp';
//        $options = [
//            "stack-converters" => ['imagemagick']
//        ];
        $res = WebPConvert::convert($source, $destination/* , $options */);
        var_dump($res);
    }

    public function createWebpImages($src, $newFile, $compression) {
        //	convert wizard.png -quality 50 -define webp:lossless=true wizard.webp
        //error_reporting(E_ALL);
        $magicpath = "/usr/bin";
        $mogrify = "convert";
        exec("{$magicpath}/{$mogrify} {$src} -quality {$compression} -define webp:lossless=true {$newFile}", $result);
        //exec("{$magicpath}/{$mogrify} test-image.jpg -quality 80 -define webp:lossless=true test-image.webp", $result);
        print_r($result);
        //print_r("{$magicpath}/{$mogrify} {$src} -quality {$compression} -define webp:lossless=true {$newFile}");
//        echo "<pre>";
//        var_dump("{$magicpath}/{$mogrify} {$src} -quality {$compression} -define webp:lossless=true {$newFile}");
//        echo "</pre>";
//        exit();
    }

    /**
     * example: $data = [
      'email'     => 'johndoe@example.com',
      'status'    => 'subscribed',
      'firstname' => 'john',
      'lastname'  => 'doe'
      ];
     * @global string $mailchimpApiKey
     * @param int $mailchimpListId
     * @param array $data
     * @return int http code
     */
    public static function syncMailchimp($mailchimpListId, $data) {
        global $mailchimpApiKey;

        $memberId = md5(strtolower($data['email']));
        $dataCenter = substr($mailchimpApiKey, strpos($mailchimpApiKey, '-') + 1);
        $url = 'https://' . $dataCenter . '.api.mailchimp.com/3.0/lists/' . $mailchimpListId . '/members/' . $memberId;

        $json = json_encode([
            'email_address' => $data['email'],
            'status' => $data['status'], // "subscribed","unsubscribed","cleaned","pending"
            'merge_fields' => [
                'FNAME' => $data['firstname'],
                'LNAME' => $data['lastname']
            ]
        ]);
        $ch = curl_init($url);

        curl_setopt($ch, CURLOPT_USERPWD, 'user:' . $mailchimpApiKey);
        curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/json']);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_TIMEOUT, 10);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'PUT');
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $json);

        $result = curl_exec($ch);

        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);

        $res["httpCode"] = $httpCode;
        $res["result"] = json_decode($result);

        return $res;
    }

}
