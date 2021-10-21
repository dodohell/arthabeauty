<?php
	session_start();
	header("Content-type: text/html; charset=utf-8");
    
    ini_set("display_errors", 1);
	error_reporting(E_ALL & ~E_NOTICE);
    
	require("../config.inc.php");
    
	ini_set('include_path', ".{$separator}{$install_path}/{$separator}{$install_path}/pear/{$separator}admin/spaw/"); 
	
	require("{$install_path}/libs/smarty/Smarty.class.php");
	require("{$install_path}/common.php");
	require("{$install_path}/functions.php");
	require("{$install_path}/bimage.php");

	require("{$install_path}/vendor/autoload.php");
    PEAR::setErrorHandling(PEAR_ERROR_DIE);
    
	$DSN = "$db_schema://$db_user:$db_pass@$db_host/$db_db";

	$db = DB::connect($DSN);

	if (function_exists('safeCheck')) {
	  safeCheck($db);
	}

	$db->setFetchMode(DB_FETCHMODE_ASSOC);
	$db->Query("SET NAMES utf8");

	$sm = new Smarty;    
	$sm->template_dir = $template_dir;
	$sm->compile_dir = $template_c_dir;

	$_SESSION["lg"] = "bg";
	
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
	
	
	
	$leftmenu = array(
		array("name" => $configVars["carts"], 				"url" => "carts.php"),
		array("name" => $configVars["fast_orders"], 				"url" => "fast_orders.php"),
		array("name" => $configVars["daily_income"], 				"url" => "daily_income.php"),
		array("name" => $configVars["products"], 			"url" => "products.php"),
		array("name" => $configVars["categories"], 			"url" => "categories.php"),
		array("name" => $configVars["menus"], 				"url" => "menus.php"),
		array("name" => $configVars["common"], 				"url" => "common.php"),
		array("name" => $configVars["discounts"], 			"url" => "discounts.php"),
		//array("name" => $configVars["fonts"], 				"url" => "fonts.php"),
		// array("name" => $configVars["countries_groups"], 			"url" => "countries_groups.php"),
		array("name" => $configVars["countries"], 			"url" => "countries.php"),
		array("name" => $configVars["delivery_types"], 		"url" => "delivery_types.php"),
		// array("name" => $configVars["postage_points"], 		"url" => "postage_points.php"),
		// array("name" => $configVars["newsletter_emails"], 		"url" => "newsletters_emails.php"),
		array("name" => $configVars["category_types"], 		"url" => "category_types.php"),
		array("name" => $configVars["manifacturers"], 		"url" => "manifacturers.php"),
		array("name" => $configVars["brands"], 		"url" => "brands.php"),
		array("name" => $configVars["aromas"], 		"url" => "aromas.php"),
		array("name" => $configVars["price_limits"], 		"url" => "price_limits.php"),
		array("name" => $configVars["product_types"], 		"url" => "product_types.php"),
		array("name" => $configVars["attributes"], 		"url" => "attributes.php"),
		array("name" => $configVars["availability_labels"], 		"url" => "availability_labels.php"),
		array("name" => $configVars["option_groups"], 		"url" => "option_groups.php"),
		array("name" => $configVars["display_statuses"], 		"url" => "display_statuses.php"),
		array("name" => $configVars["user_groups"], 		"url" => "user_groups.php"),
		array("name" => $configVars["users"], 		"url" => "users.php"),
		array("name" => $configVars["order_statuses"], 		"url" => "order_statuses.php"),
		array("name" => $configVars["news"], 		"url" => "news.php"),
		// array("name" => $configVars["weight_bands"], 		"url" => "weight_bands.php"),
		// array("name" => $configVars["vouchers"], 			"url" => "vouchers.php"),
		// array("name" => $configVars["vat"], 				"url" => "vat.php"),
		// array("name" => $configVars["news"], 				"url" => "news.php"),
		// array("name" => $configVars["users"], 				"url" => "users.php"),
		array("name" => $configVars["users_admin"], 		"url" => "users_admin.php"),
		array("name" => $configVars["words_on_site"], 		"url" => "words.php"),
		array("name" => "<font color=blue>".$configVars["logout"]."</font>", "url" => "logout.php")
		
	);

	$sm->assign("leftmenu",$leftmenu);
	$sm->assign("host", $host);

	$menu_posIDs = array(
		"0_up"				=> $configVars["menupos_top"],
		// "1_up"				=> $configVars["menupos_top_2"],
		"1_left"				=> $configVars["menupos_left"],
		"1_homepage"				=> $configVars["menupos_homepage"],
		"2_homepage"				=> $configVars["menupos_homepage_2"],
		"2_down"				=> $configVars["menupos_down"],
		"3_down"				=> $configVars["menupos_down2"],
		"4_down"				=> $configVars["menupos_down3"],
	);
	
	$sm->assign("menu_posIDs", $menu_posIDs);

	$targetsIDs = array(
		"_self"		=> $configVars["same_window"],
		"_blank"	=> $configVars["new_window"]
	);
	$sm->assign("targetsIDs", $targetsIDs);

	$pic_pos = array(
		"left" => $configVars["left"],
		"right" => $configVars["right"],
	);

	$levels = array(1 => "Broker", 2 => "Administrator" ) ;
	$level = $_SESSION["levelA"];
	$sm->assign("viewL", $_SESSION["levelA"]);
	$sm->assign("levels", $levels);
	
	/************ Set Default Form Action ***********/
	$sm->assign("_self",$_SERVER['PHP_SELF']);

	$sm->display("admin/index.html");
?>