<?php
	session_start();
	header('Content-type: application/xml; charset="utf-8"');
	header('P3P:CP="IDC DSP COR ADM DEVi TAIi PSA PSD IVAi IVDi CONi HIS OUR IND CNT"');
    
    error_reporting(0);
    
    if ($_SERVER["REMOTE_ADDR"] == "84.201.192.58"){
        ini_set("display_errors", 1);
        error_reporting(E_ALL & ~(E_STRICT|E_NOTICE));
        //error_reporting(E_ALL);
	}
    //error_reporting(E_ALL & ~(E_STRICT|E_NOTICE|E_DEPRECATED|E_WARNING));
    
	/********************** Include Files ************************/
	require ("../config.inc.php");
    
	//ini_set('include_path', ".{$separator}{$install_path}/pear/");
    
	require $install_path . "/libs/smarty/Smarty.class.php";
	require $install_path . "/common.php";
	require $install_path . "/functions.php";
	require $install_path . "/bimage.php";
	//require $install_path . "/classes.php";
	//include $install_path . "/site/assets/MPDF54/mpdf.php";
    include $install_path . "/payments/eBorica.php";
    
    include $install_path . "/site/autoloader.php";

//------- set propert timezone --------------------------------------
		date_default_timezone_set("Europe/Sofia");


//------- register classes dirs ------------------------------------------------
    $request_loader = new autoloader('site/classes/request');
    $classes_loader = new autoloader('site/classes');
//    $minify_loader_1 = new autoloader('site/classes/minify/src');
//    $minify_loader_2 = new autoloader('site/classes/minify/src/Exceptions');
//    $minify_loader_3 = new autoloader('site/classes/path-converter/src');
    
    spl_autoload_register(array($request_loader, 'autoload'));
    spl_autoload_register(array($classes_loader, 'autoload'));
//    spl_autoload_register(array($minify_loader_1, 'autoload'));
//    spl_autoload_register(array($minify_loader_2, 'autoload'));
//    spl_autoload_register(array($minify_loader_3, 'autoload'));
    
	require $install_path . "/vendor/autoload.php";
    
//------- include jmatosp/tumbleweed-cache -------------------------------------
    require_once $install_path . '/libs/cache/vendor/autoload.php';
    use JPinto\TumbleweedCache\CacheFactory;
    $cache = CacheFactory::make(CacheFactory::FILE);
//    $redisClient = new Redis();
//    $redisClient->connect('127.0.0.1', 13042);
//    $cache = CacheFactory::make(CacheFactory::REDIS, $redisClient);
    
	require "../lessc.inc.php";
	$less = new lessc;
	$less->checkedCompile("css/style.less", "css/style.css");
	
//------- Minify CSS -------------------------------------
    $lessFilename = $install_path . 'site/css/style.css';
    $minifiedCssFilename = $install_path . 'site/css/style.min.css';
    if (file_exists($lessFilename) && file_exists($minifiedCssFilename)) {
        $mod_time_less = filemtime($lessFilename);
        $mod_time_minifiedCss = filemtime($minifiedCssFilename);
        if ($mod_time_less >= $mod_time_minifiedCss) {
            $helpers = new Helpers();
            $helpers->minifyCSS();
        }
    }

/********************* Set Db Connection ******************/
	$DSN = "$db_schema://$db_user:$db_pass@$db_host/$db_db";
	$db = DB::connect($DSN);
    
	if (function_exists('safeCheck')) {
		safeCheck($db);
	}
	$db->setFetchMode(DB_FETCHMODE_ASSOC);
	$db->Query("SET NAMES utf8");
	$db->Query("SET time_zone = 'Europe/Sofia'");
	
	
	/******************** Smarty Settings *********************/
	$sm = new Smarty;
	$sm->template_dir = $template_dir;
	$sm->compile_dir = $template_c_dir;

	/************** Assign Host And Files Directories ***********/
	$sm->assign("host", $host);
	$sm->assign("files_host", $files_host);
    
/************************ Get Request params *************************/
    $request = new Request();
    $params = $request->getParams();
    
/************************ Set Language *************************/
	$site = new Settings();
	$param = htmlspecialchars(trim($_REQUEST["param"]),ENT_QUOTES);
	$result = $site->checkHtaccessByTerm($param, 0, 1);
	
	if( $params->has("lang") ) {
		$lng = $params->getString("lang");
		$_SESSION["lang"] = $lng;
	}

	if ( $result["lang"] ){
		$_SESSION["lang"] = $result["lang"];
		$lng = $result["lang"];
	}

//	if( isset($_SESSION["lang"]) ) {
//		$lng = $_SESSION["lang"];
//	}
	$test = 0;
	if ($_SERVER["REMOTE_ADDR"] == "84.201.192.58" || $_SERVER["REMOTE_ADDR"] == "87.227.188.30"){
        $test = 1;
	}
    $sm->assign("test", $test);

	if($lng != "bg" && $lng != "en" && $lng != "dk" && $lng != "ru" && $lng != "ro") $lng = "bg";
	$language_file = $install_path . "/lang/" . $lng . ".txt"; 
    
	$htaccess_file = $install_path . "/conf/g" . $lng . ".txt"; 
	$htaccess_file_bg = $install_path . "/conf/gbg.txt"; 
	$htaccess_file_en = $install_path . "/conf/gen.txt"; 
	$htaccess_file_de = $install_path . "/conf/gde.txt"; 
	$htaccess_file_ro = $install_path . "/conf/gro.txt"; 
	$htaccess_file_ru = $install_path . "/conf/gru.txt"; 
	$sm->assign("language_file", $language_file);
	$sm->assign("htaccess_file", $htaccess_file);
	$sm->assign("lng", $lng);
    
    $sm->configLoad($language_file);
    $sm->configLoad($htaccess_file);
	
	
	
	$settings = new Settings();
	
	$description = $settings->getFromCommon('description');
	$keywords = $settings->getFromCommon('keywords');
	
	$sm->assign("title", $settings->getFromCommon('title'));
	$sm->assign("keywords", $keywords);
	$sm->assign("description", $description);
	$sm->assign("metatags", $settings->getFromCommon('metatags'));
	$sm->assign("register", $settings->getFromCommon('register'));
	$sm->assign("welcome", $settings->getFromCommon('welcome'));
	$sm->assign("copyrights", $settings->getFromCommon('copyrights'));
	$sm->assign("cart_descr", $settings->getFromCommon('cart'));
	$sm->assign("checkout_descr", $settings->getFromCommon('checkout'));
	$sm->assign("forgot_password", $settings->getFromCommon('forgot_password'));
	
	$categoriesObj = new Categories();
	$categories = $categoriesObj->getCategories();
	$sm->assign("categories", $categories);
	
//	$categoriesHomepage = $categoriesObj->getCategories(array("homepage" => 1));
//	$sm->assign("categoriesHomepage", $categoriesHomepage);
	
//	$citiesObj = new Cities();
//	$cities = $citiesObj->getCities();
//	$sm->assign("cities", $cities);
	
	$menusObj = new Menus();
	
	$header_menus = $menusObj->getMenuPosition("0_up");
	$sm->assign("header_menus", $header_menus);
	
    $user_menu = $menusObj->getMenuPosition("1_user");
	$sm->assign("user_menu", $user_menu);

	$homepage = $menusObj->getMenuPosition("1_homepage");
	$sm->assign("homepage", $homepage);
    
	$homepage2 = $menusObj->getMenuPosition("2_homepage");
	$sm->assign("homepage2", $homepage2);
	
	$homepage3 = $menusObj->getMenuPosition("3_homepage");
	$sm->assign("homepage3", $homepage3);
	
	$footer_menus = $menusObj->getMenuPosition("2_down");
	$sm->assign("footer_menus", $footer_menus);
	
	$footer_menus2 = $menusObj->getMenuPosition("2_down_2");
	$sm->assign("footer_menus2", $footer_menus2);
	
	$right = $menusObj->getMenuPosition("3_right");
	$sm->assign("right", $right);
    
	if ( $lng == "bg" ){
		$sm->assign("link_home", "/");
	}elseif ( $lng == "ro" ){
		$sm->assign("link_home", "/");
	}else{
		$sm->assign("link_home", "/en");
	}
	$sm->assign("link_bg", "/");
	$sm->assign("link_en", "/en");
	$sm->assign("link_ro", "/");
	
    if($lng == "bg"){
        $currency = Currencies::getRecordByCode("BGN");
    }else{
        $currency = Currencies::getRecordByCode("EUR");
    }
    
    $user = array();
	if ( $_SESSION["user"] ){
		$user = $_SESSION["user"];
        if($user["currency_id"] > 0){
            $currency = Currencies::getRecord($user["currency_id"]);
        }
        $name_initials = mb_substr($user["first_name"], 0, 1)." ".mb_substr($user["last_name"], 0, 1);
        $user["name_initials"] = $name_initials;
        //$user["currency"] = $currency;
        $coockieInfo = array(
            "userID" => $_SESSION["userID"]
        );
        setcookie($user_cookie_name, serialize($coockieInfo), time() + (86400 * 355), "/"); // 86400 = 1 day
    }else if(isset($_COOKIE[$user_cookie_name])){
        $coockieInfo = unserialize($_COOKIE[$user_cookie_name], ["allowed_classes" => false]);
        $userID = $coockieInfo["userID"];
        $userObj = new Users();
        if($userID){
            $row = $userObj->getRecord($userID);

            if (isset($row["id"]) && $row["id"] > 0) {
                $_SESSION["userID"] = $row["id"];
                $_SESSION["user"] = $row;
                //Update last login info
                $res = $db->autoExecute($users_table, array("last_login_time" => time(), "last_login_ip" => $_SERVER["REMOTE_ADDR"]), DB_AUTOQUERY_UPDATE, " id = " . $row["id"]); safeCheck($res);
                
                $user = $row;
                if($user["currency_id"] > 0){
                    $currency = Currencies::getRecord($user["currency_id"]);
                }
                $name_initials = mb_substr($user["first_name"], 0, 1)." ".mb_substr($user["last_name"], 0, 1);
                $user["name_initials"] = $name_initials;
                
//                header("Location: /myprofile");
//                die();
            }
        }
//        echo "User Cookie '" . $user_cookie_name . "' is set!<br>";
//        echo "Value is: " . $coockieInfo["userID"];
    }
    
    $_SESSION["currency"] = $currency;
    $sm->assign("user", $user);
    
    $abb_before_amount = $currency["abb_before_amount"] == 1 ? 1 : 0;
    $sm->assign("abb_before_amount", $abb_before_amount);
    $currency_abb = $currency["abbreviation"];
    $sm->assign("currency_abb", $currency_abb);
    $global_currency_code = $currency["code"];
    $sm->assign("global_currency_code", $global_currency_code);
    
	$cart_items_count = 0;
    $cart_amount = 0.0;
    if ($_SESSION["cart_id"]){
		$cart_id = (int)$_SESSION["cart_id"];
        $cartProducts = $db->getAll("SELECT * FROM ".$carts_products_table." WHERE edate = 0 AND cart_id = ".$cart_id); safeCheck($cartProducts);
        if(count($cartProducts) > 0){
            foreach($cartProducts as $k => $v){
                $cart_items_count += $v["quantity"];
                $cart_amount += (float)$v["product_price_total"];
            }
        }
	}
    $sm->assign("cart_items_count", $cart_items_count);
    $sm->assign("cart_amount", $cart_amount);

//  -------- Bonus Points -------------------------------
    $sm->assign("useBonusPoints", $useBonusPoints);
    
//  -------- Shipping Apis -------------------------------
    $sm->assign("useShippingApis", $useShippingApis);
    $sm->assign("useEcontApi", $useEcontApi);
    $sm->assign("useSpeedyApi", $useSpeedyApi);
    
//  -------- Social Logins -------------------------------
    $sm->assign("showSocialLogins", $showSocialLogins);
    $sm->assign("useGoogleLogin", $useGoogleLogin);
    $sm->assign("useFacebookLogin", $useFacebookLogin);
    
//  -------- Facebook Login -------------------------------
    $sm->assign("fb_app_id", $fb_app_id);
    $sm->assign("fb_app_secret", $fb_app_secret);
    $sm->assign("fb_redirect_url", $fb_redirect_url);
    
//  -------- Google Login -------------------------------
    $sm->assign("google_client_id", $google_client_id);
    $sm->assign("google_client_secret", $google_client_secret);
    
//  -------- Google reCAPTCHA ---------------------------------
    $sm->assign("recaptcha_site_key", $recaptcha_site_key);
    $sm->assign("recaptcha_secret_key", $recaptcha_secret_key);
    
	$firstpage_brands = Brands::getBrands(1);
	$sm->assign("firstpage_brands", $firstpage_brands);
	
    $favouritesCount = 0;
    if($user["id"]){
        $favouritesCountReq = $db->getRow("SELECT
                                            COUNT(id) AS cntr
                                        FROM 
                                            ".$favourites_table."
                                        WHERE 
                                            edate = 0 
                                        AND user_id = ".$user["id"]); safeCheck($favouritesCountReq);
        $favouritesCount = $favouritesCountReq["cntr"];
    }
    $favouritesCount = $favouritesCount > 0 ? (int)$favouritesCount : 0;
    $sm->assign("favouritesCount", $favouritesCount);
    
    $sm->assign("active_payment", $active_payment);
    $eborica = $params->has("eBorica") ? $params->getString("eBorica") : "";
	
//    $speedy = new Speedy();
//    $resultTest1 = $speedy->getSiteId("София", "София");
    
//    $resultTest = $speedy->listAllSites();
//    $resultTest2 = $speedy->getOfficesList(66264); // 67338 - Сливен; 66264 - Септември, Пазарджик
//    $resultTest3 = $speedy->getValidServicesBetweenTwoSites(7079);
//    $resultTest4 = $speedy->getAllObjectsOfTheContract();
//    $resultTest5 = $speedy->getClientId();
//    $resultTest6 = $speedy->getCalculatedPrice(37.9, 0.5, 66264, 761);
//    $resultTest7 = $speedy->createBillOfLading(26.9, "разни работи", 0.9, 67338, "кв. Дружба, бл. 2, вх. Г ет. 2, ап. 5", "Димитър Димитров2", "Митака2 ЕООД", "0123456789");
//    $resultTest8 = $speedy->printBillOfLadingPdf(60699298222);
//    $resultTest9 = $speedy->printBillOfLadingLabelsPdf(60699298222);
//    $pickingIds = array(60699298222, 60699545322);
//    $resultTest10 = $speedy->createOrder($pickingIds, 1900, 1800, "2019-06-15");
//    $resultTest11 = $speedy->trackShipment(299999992);

//echo "<pre>";
//var_dump($resultTest6);
//echo "</pre>";
//exit();
    
//    get available methods
//    $class_name = get_class($resultTest[0]);
//    $methods = get_class_methods($class_name);
//    foreach($methods as $method) { var_dump($method); }

//    $econt = new Econt();
    
    //$resultTest = $econt->updateCities();
//    $resultTestId = $econt->getSiteId("Сливен", "Сливен", "BG");
//    $resultTest = $econt->getOfficesList($resultTestId);
//
//    echo "<pre>";
//    var_dump($resultTest);
//    echo "</pre>";
//    exit();
?>