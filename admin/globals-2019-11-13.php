<?php
	session_start();
	header("Content-type: text/html; charset=utf-8");
    
	ini_set('register_globals', "off");
    
    ini_set("display_errors", 1);
	error_reporting(E_ALL & ~E_NOTICE);
    
	if( !isset($_SESSION["time_".md5("realtyinb")]) ) {
	  // not set ?!? what the .... buzz off you stupid hacker
		print "Session expired. Please <a href=\"index.php\">login again</a>";
		die();
	}
    
	require("../config.inc.php");
    
	$sess_time = $_SESSION["time_".md5("realtyinb")]; // zadavame go oshte na nachalnata stranica
	$current_time = time();
	$diff = $current_time - $sess_time;
    
	if( $diff < $sessionTime ) {
		$_SESSION["time_".md5("realtyinb")] = $current_time;
	} else {
        session_destroy();
		print "Session expired. Please <a href=\"index.php\">login again</a>";
		die();
	}
    
    
	//ini_set('include_path', ".{$separator}{$install_path}/{$separator}{$install_path}/pear/{$separator}admin/spaw/"); 
	
	require("{$install_path}/libs/smarty/Smarty.class.php");
	require("{$install_path}/common.php");
	require("{$install_path}/functions.php");
	require("{$install_path}/bimage.php");
    
	// INCLUDE CLASSES
	require("{$install_path}/admin/classes/class.settings.php");
	require("{$install_path}/admin/classes/class.common.php");
	require("{$install_path}/admin/classes/class.menus.php");
	require("{$install_path}/admin/classes/class.categories.php");
	require("{$install_path}/admin/classes/class.cities.php");
	require("{$install_path}/admin/classes/class.category_types.php");
	require("{$install_path}/admin/classes/class.manifacturers_origins.php");
	require("{$install_path}/admin/classes/class.news.php");
	require("{$install_path}/admin/classes/class.news_categories.php");
	require("{$install_path}/admin/classes/class.news_authors.php");
	require("{$install_path}/admin/classes/class.users_admin.php");
	require("{$install_path}/admin/classes/class.users.php");
	require("{$install_path}/admin/classes/class.faqs.php");
	require("{$install_path}/admin/classes/class.words.php");
	require("{$install_path}/admin/classes/class.order_statuses.php");
    require("{$install_path}/admin/classes/request/Request.php");
	require("{$install_path}/admin/classes/class.product_types.php");
	require("{$install_path}/admin/classes/class.attributes.php");
	require("{$install_path}/admin/classes/class.products.php");
	require("{$install_path}/admin/classes/class.option_groups.php");
	require("{$install_path}/admin/classes/class.carts.php");
	require("{$install_path}/admin/classes/class.districts.php");
	require("{$install_path}/admin/classes/class.discounts.php");
	require("{$install_path}/admin/classes/class.cart_discounts.php");
	require("{$install_path}/admin/classes/class.fast_orders.php");
	require("{$install_path}/admin/classes/class.carts_products.php");
	require("{$install_path}/admin/classes/class.delivery_types.php");
	require("{$install_path}/admin/classes/class.user_groups.php");
	require("{$install_path}/admin/classes/class.countries.php");
	require("{$install_path}/admin/classes/class.manifacturers.php");
	require("{$install_path}/admin/classes/class.brands.php");
	require("{$install_path}/admin/classes/class.collections.php");
	require("{$install_path}/admin/classes/class.colors.php");
    require("{$install_path}/admin/classes/class.promo_codes.php");
	require("{$install_path}/admin/classes/class.currencies.php");
	require("{$install_path}/admin/classes/class.quiz_questions.php");
	require("{$install_path}/admin/classes/class.quiz_categories.php");
	require("{$install_path}/admin/classes/class.age_groups.php");
	require("{$install_path}/admin/classes/class.speedy.php");
	require("{$install_path}/admin/classes/class.econt.php");
	require("{$install_path}/admin/classes/class.delivery.php");
	require("{$install_path}/admin/classes/class.helpers.php");
	require("{$install_path}/admin/classes/class.convert.php");
	//require("{$install_path}/admin/classes/class.EcontXMLClient.php");
    
	require("{$install_path}/vendor/autoload.php");
	require("{$install_path}/admin/vendor/autoload.php");
    
//------- include jmatosp/tumbleweed-cache -------------------------------------
    require_once $install_path . '/libs/cache/vendor/autoload.php';
    use JPinto\TumbleweedCache\CacheFactory;
    $cache = CacheFactory::make(CacheFactory::FILE);
//    $redisClient = new Redis();
//    $redisClient->connect('127.0.0.1', 13042);
//    $cache = CacheFactory::make(CacheFactory::REDIS, $redisClient);
    
    //PEAR::setErrorHandling(PEAR_ERROR_DIE);
	$DSN = "$db_schema://$db_user:$db_pass@$db_host/$db_db";
	$db = DB::connect($DSN);

	if (function_exists('safeCheck')) {
        safeCheck($db);
	}

	$db->setFetchMode(DB_FETCHMODE_ASSOC);
	$db->Query("SET NAMES utf8");
    
    $request = new Request();
    $params = $request->getParams();
    
	$sm = new Smarty;
    //$sm->debugging = true;
	$sm->template_dir = $template_dir;
	$sm->compile_dir = $template_c_dir;

	//$_SESSION["lg"] = "bg";
	
	if( isset($_REQUEST["lg"]) ) {
		$lng = $_REQUEST["lg"];
		$_SESSION["lg"] = $lng;
	}else{
		$lng = $_SESSION["lg"];
	}

	if( isset($_SESSION["lg"]) ) {
		$lng = $_SESSION["lg"];
	}
	
	$language_file = "{$install_path}/admin/lang/".$lng.".txt"; 
	$sm->assign("language_file", $language_file);
	$sm->assign("languages", $languages);
	
	$var = $sm->configLoad($language_file);
	$configVars = $sm->getConfigVars();
	
//	$leftmenu = array(
//		array("name" => $configVars["carts"], 				"url" => "carts.php"),
//		array("name" => $configVars["fast_orders"], 				"url" => "fast_orders.php"),
//		array("name" => $configVars["daily_income"], 				"url" => "daily_income.php"),
//		array("name" => $configVars["products"], 			"url" => "products.php"),
//		array("name" => $configVars["categories"], 			"url" => "categories.php"),
//		array("name" => $configVars["menus"], 				"url" => "menus.php"),
//		array("name" => $configVars["common"], 				"url" => "common.php"),
//		array("name" => $configVars["discounts"], 			"url" => "discounts.php"),
//		array("name" => $configVars["countries"], 			"url" => "countries.php"),
//		array("name" => $configVars["delivery_types"], 		"url" => "delivery_types.php"),
//		array("name" => $configVars["category_types"], 		"url" => "category_types.php"),
//		array("name" => $configVars["manifacturers"], 		"url" => "manifacturers.php"),
//		array("name" => $configVars["brands"], 		"url" => "brands.php"),
//		array("name" => $configVars["aromas"], 		"url" => "aromas.php"),
//		array("name" => $configVars["price_limits"], 		"url" => "price_limits.php"),
//		array("name" => $configVars["product_types"], 		"url" => "product_types.php"),
//		array("name" => $configVars["attributes"], 		"url" => "attributes.php"),
//		array("name" => $configVars["availability_labels"], 		"url" => "availability_labels.php"),
//		array("name" => $configVars["option_groups"], 		"url" => "option_groups.php"),
//		array("name" => $configVars["display_statuses"], 		"url" => "display_statuses.php"),
//		array("name" => $configVars["user_groups"], 		"url" => "user_groups.php"),
//		array("name" => $configVars["users"], 		"url" => "users.php"),
//		array("name" => $configVars["order_statuses"], 		"url" => "order_statuses.php"),
//		array("name" => $configVars["news"], 		"url" => "news.php"),
//		array("name" => $configVars["users_admin"], 		"url" => "users_admin.php"),
//		array("name" => $configVars["words_on_site"], 		"url" => "words.php"),
//		array("name" => "<font color=blue>".$configVars["logout"]."</font>", "url" => "logout.php")
//		
//	);
//
//	$sm->assign("leftmenu",$leftmenu);
	$sm->assign("host", $host);

	$menu_posIDS = array(
		//array( "menu_pos" => "0_up", "name" => $configVars["menu_header_1"]),
		//array( "menu_pos" => "1_user", "name" => $configVars["menu_user_1"]),
		array( "menu_pos" => "1_homepage", "name" => $configVars["menu_homepage"]),
		array( "menu_pos" => "2_homepage", "name" => $configVars["menu_homepage_2"]),
		array( "menu_pos" => "3_homepage", "name" => $configVars["menu_homepage_3"]),
		array( "menu_pos" => "2_down", "name" => $configVars["menu_footer"]),
		array( "menu_pos" => "2_down_2", "name" => $configVars["menu_footer_2"]),
		//array( "menu_pos" => "3_right", "name" => $configVars["menu_right"]),
		//array( "menu_pos" => "4_categories", "name" => $configVars["menu_categories"]),
		array( "menu_pos" => "4_partners", "name" => $configVars["menu_partners"]),
		//array( "menu_pos" => "4_partners_right", "name" => $configVars["menu_partners_right"]),
	);
	
	$sm->assign("menu_posIDS", $menu_posIDS);

	$targetsIDs = array(
		"_self"		=> $configVars["same_window"],
		"_blank"	=> $configVars["new_window"]
	);
	$sm->assign("targetsIDs", $targetsIDs);

	$pic_pos = array(
		"left" => $configVars["left"],
		"right" => $configVars["right"],
	);
    
    $fullmenu = array(
        array("name" => $configVars["news"], "open" => 2,            "menu_id" => "100", "url" => "#", "icon_class" => "mdi-newspaper", "submenu" => array(
            array("name" => $configVars["news_articles"],            "menu_id" => "101", "url" => "news.php"),
            array("name" => $configVars["news_categories"], "menu_id" => "102", "url" => "news_categories.php"),
			array("name" => $configVars["news_authors"],         "menu_id" => "103", "url" => "news_authors.php"),
            )
        ),
        array("name" => $configVars["orders"],"open" => 3,               "menu_id" => "200", "url" => "#", "icon_class" => "mdi-book-open", "submenu" => array(
                array("name" => $configVars["carts_page"],          "menu_id" => "201", "url" => "carts.php"),
                array("name" => $configVars["fast_orders"],         "menu_id" => "202", "url" => "fast_orders.php"),
                array("name" => $configVars["users"],       "menu_id" => "203", "url" => "users.php"),                
            )
        ),
        array("name" => $configVars["catalogue"], "open" => 4,               "menu_id" => "300", "url" => "#", "icon_class" => "mdi-book-open", "submenu" => array(
                //array("name" => $configVars["carts_page"],          "menu_id" => "301", "url" => "carts.php"),
                //array("name" => $configVars["fast_orders"],         "menu_id" => "312", "url" => "fast_orders.php"),
                array("name" => $configVars["categories"],          "menu_id" => "302", "url" => "categories.php"),
                array("name" => $configVars["manifacturers"],       "menu_id" => "311", "url" => "manifacturers.php"),
                array("name" => $configVars["brands"],              "menu_id" => "312", "url" => "brands.php"),
                array("name" => $configVars["collections"],         "menu_id" => "313", "url" => "collections.php"),
                array("name" => $configVars["product_types"],       "menu_id" => "307", "url" => "product_types.php"),
                array("name" => $configVars["attributes"],          "menu_id" => "308", "url" => "attributes.php"),
                array("name" => $configVars["option_groups"],       "menu_id" => "309", "url" => "option_groups.php"),                
                array("name" => $configVars["products"],            "menu_id" => "306", "url" => "products.php"),
                array("name" => $configVars["discounts"],           "menu_id" => "303", "url" => "discounts.php"),
                array("name" => $configVars["cart_discounts_page"], "menu_id" => "304", "url" => "cart_discounts.php"),
                array("name" => $configVars["promo_codes"],         "menu_id" => "314", "url" => "promo_codes.php"),
                //array("name" => $configVars["category_types"],      "menu_id" => "305", "url" => "category_types.php"),
                array("name" => $configVars["order_statuses"],      "menu_id" => "310", "url" => "order_statuses.php"),
                array("name" => $configVars["quiz_categories"],     "menu_id" => "315", "url" => "quiz_categories.php"),
                array("name" => $configVars["quiz_questions"],      "menu_id" => "316", "url" => "quiz_questions.php"),
            )
        ),
		array("name" => $configVars["administration"],"open" => 5,  "menu_id" => "400", "url" => "#", "icon_class" => "mdi-settings", "submenu" => array(
                array("name" => $configVars["common"],              "menu_id" => "401", "url" => "common.php"),
                array("name" => $configVars["words"],               "menu_id" => "402", "url" => "words.php"),
                array("name" => $configVars["products_comments"],   "menu_id" => "403", "url" => "products_comments.php"),
                array("name" => $configVars["users_admin"],         "menu_id" => "404", "url" => "users_admin.php"),
                array("name" => $configVars["user_groups"],         "menu_id" => "405", "url" => "user_groups.php"),
                array("name" => $configVars["cities"],              "menu_id" => "406", "url" => "cities.php"),
                array("name" => $configVars["countries"],           "menu_id" => "407", "url" => "countries.php"),
                array("name" => $configVars["colors"],              "menu_id" => "408", "url" => "colors.php"),
                array("name" => $configVars["currencies"],          "menu_id" => "409", "url" => "currencies.php"),
                array("name" => $configVars["age_groups"],          "menu_id" => "410", "url" => "age_groups.php"),
            )
        ),

		array("name" => $configVars["logout"], "menu_id" => "400", "url" => "logout.php", "icon_class" => "mdi-logout", "submenu" => array())
	);
	
		$script_name = end(explode('/', $_SERVER['SCRIPT_NAME'] ) );
		
		if(in_array($script_name, array("news.php", "news_ae.php", "news_categories.php", "news_categories_ae.php", "news_authors.php", "news_authors_ae.php"))) {
				$sm->assign("open_menu", 2);
		}

		if(in_array($script_name, array("carts.php", "carts_ae.php", "fast_orders.php", "fast_orders_ae.php", "users.php", "users_ae.php"))) {
				$sm->assign("open_menu", 3);
		}
		
		if(in_array($script_name, array("categories.php",     
"manifacturers.php", "brands.php", "collections.php", "product_types.php", "attributes.php", "option_groups.php", "products.php", "discounts.php", "cart_discounts.php", "promo_codes.php", "order_statuses.php", "categories_ae.php","manifacturers_ae.php", "brands_ae.php", "collections_ae.php", "product_types_ae.php", "attributes_ae.php", "option_groups_ae.php", "products_ae.php", "discounts_ae.php", "cart_discounts_ae.php", "promo_codes_ae.php", "order_statuses_ae.php"))) {
				$sm->assign("open_menu", 4);
		}

		if(in_array($script_name, array("words.php", "products_comments.php", "users_admin.php", "user_groups.php", "cities.php", "countries.php", "colors.php", "currencies.php", "words_ae.php", "products_comments_ae.php", "users_admin_ae.php", "user_groups_ae.php", "cities_ae.php", "countries_ae.php", "colors_ae.php", "currencies_ae.php"))) {
				$sm->assign("open_menu", 5);
		}
    
    $settingsObj = new Settings();
    
    /* USER access restriction */
	if(  $_SESSION['levelA'] == 1 ){
        
		$userLevelsGlobal = $db->getAll("SELECT * FROM " . $users_admin_to_menus_table . " WHERE user_id = " . $_SESSION['uid']  . " ORDER BY menu_id ASC ");
		
		foreach( $userLevelsGlobal as $k =>$v ){
			$accessLevelsGlobal[$v['menu_id']] = $v['permission_level'];
		}
		
		$sm->assign("userLimit",1);
		$script_name = end(explode('/', $_SERVER['SCRIPT_NAME'] ) );
		$searchP = str_replace('_ae', '' , $script_name ) ;
		$searchP = str_replace('_e', '' , $searchP ) ;
		
		foreach($fullmenu as $k => $v){
			if( $accessLevelsGlobal[$v['menu_id']] == 0 ){
				unset( $fullmenu[$k] );
				if (strpos($v['url'] ,$searchP) !== false) {
                    $settingsObj->checkLogin();
//					header("Location: /");
//					die();
				}
			}elseif( $accessLevelsGlobal[$v['menu_id']] == 1 ){
				$viewOnly = 1; //Bool for use in script
				
				if (strpos($v['url'] ,$searchP) !== false) {
					if(strpos($script_name ,'_ae') !== false  || strpos($script_name ,'_e') !== false ) {
						$sm->assign("formLock", 1); // front end input lock; script in footer.html or before body end
						unset( $_REQUEST['Submit'] );
                        unset( $_REQUEST['SaveAndStay'] );
                        $params->unsetElement("Submit");
                        $params->unsetElement("SaveAndStay");
					}
					$sm->assign("disableSort", 1); // front end input lock; script in footer.html or before body end
					unset( $_REQUEST["pos1"] );		// remove all actions
					$params->unsetElement("pos1");
                    unset( $_REQUEST['pos2'] );		// remove all actions
                    $params->unsetElement("pos2");
					unset( $_REQUEST['act'] );		// remove all actions
                    $params->unsetElement("act");
				}
			}elseif( $accessLevelsGlobal[$v['menu_id']] == 2 ){
				$limitedEdit = 1; //Bool for use in script
				
				if (strpos($v['url'] ,$searchP) !== false) {
					//dbg('Limited Edit PLaceholder');
				}
				if($params->getString("act") == 'delete' || $params->getString("act") == 'mDelete' || $params->has("mDelete") || $params->getString("act") == 'add'){//remove delete and/or add parameter
					unset( $_REQUEST['act'] );
                    $params->unsetElement("act");
				}
			}else{
				if (strpos($v['url'] ,$searchP) !== false) {
					//dbg('Full Access ');
				}
			}
            if($v["submenu"]){
                foreach ($v["submenu"] as $kk => $vv) {
                    if( $accessLevelsGlobal[$vv['menu_id']] == 0 ){
                        unset( $fullmenu[$k]["submenu"][$kk] );
                        if (strpos($vv['url'] ,$searchP) !== false) {
                            $settingsObj->checkLogin();                            
//                            header("Location: /admin/");
//                            die();
                        }
                    }elseif( $accessLevelsGlobal[$vv['menu_id']] == 1 ){
                        $viewOnly = 1; //Bool for use in script

                        if (strpos($vv['url'] ,$searchP) !== false) {
                            if(strpos($script_name ,'_ae') !== false  || strpos($script_name ,'_e') !== false ) {
                                $sm->assign("formLock", 1); // front end input lock; script in footer.html or before body end
                                unset( $_REQUEST['Submit'] );
                                unset( $_REQUEST['SaveAndStay'] );
                                $params->unsetElement("Submit");
                                $params->unsetElement("SaveAndStay");
                            }
                            $sm->assign("disableSort", 1); // front end input lock; script in footer.html or before body end
                            unset( $_REQUEST["pos1"] );		// remove all actions
                            $params->unsetElement("pos1");
                            unset( $_REQUEST['pos2'] );		// remove all actions
                            $params->unsetElement("pos2");
                            unset( $_REQUEST['act'] );		// remove all actions
                            $params->unsetElement("act");
                        }
                    }elseif( $accessLevelsGlobal[$vv['menu_id']] == 2 ){
                        $limitedEdit = 1; //Bool for use in script
                        
                        if (strpos($vv['url'] ,$searchP) !== false) {
                            //dbg('Limited Edit PLaceholder');
                        }
                        
                        if($params->getString("act") == 'delete' || $params->getString("act") == 'mDelete' || $params->has("mDelete") || $params->getString("act") == 'add'){//remove delete and/or add parameter
                            unset( $_REQUEST['act'] );
                            $params->unsetElement("act");
                            unset( $_REQUEST['mDelete'] );
                            $params->unsetElement("mDelete");
                        }
                    }else{
                        if (strpos($vv['url'] ,$searchP) !== false) {
                            //dbg('Full Access ');
                        }
                    }
                }
            }
		}
		//$fullmenu = array_values($fullmenu);
	}

    $sm->assign("fullmenu", $fullmenu);
	
	$levels = array(1 => "Broker", 2 => "Administrator" );
	$level = $_SESSION["levelA"];
	$sm->assign("viewL", $_SESSION["levelA"]);
	$sm->assign("levels", $levels);
	$userAdmin = $_SESSION["userAdmin"];
	
	$sm->assign("userAdmin", $userAdmin);
    $sm->assign("useUserGroups", $useUserGroups);
	
	/************ Set Default Form Action ***********/
	$sm->assign("_self",$_SERVER['PHP_SELF']);
    
?>