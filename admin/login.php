<?php
	session_start();
	
	require("../config.inc.php");
	ini_set('include_path', ".{$separator}{$install_path}/pear/"); 
	require("{$install_path}/libs/smarty/Smarty.class.php");
	require("{$install_path}/common.php");
	require("{$install_path}/functions.php");
	require("{$install_path}/bimage.php");
	require("{$install_path}/classes.php");
	
    require("{$install_path}/vendor/autoload.php");
    PEAR::setErrorHandling(PEAR_ERROR_DIE);
    
	$DSN = "$db_schema://$db_user:$db_pass@$db_host/$db_db";

	$db = DB::connect($DSN);

	if (function_exists('safeCheck')) {
	  safeCheck($db);
	}

	$db->setFetchMode(DB_FETCHMODE_ASSOC);

	$sid = $_REQUEST["sid"];

	$user = $_POST["username"];
	$pass = $_POST["password"];
	$code = $_POST["code"];
	$captcha_valid = 0;
	if ($_SESSION['sess_captchaRequest1'] == $code . "123PropertiesDefensiveText@3") {
		$captcha_valid = 1;
	}
	
	
	$u = $db->getRow("SELECT * FROM " . $users_admin_table . " WHERE uname='" . $user . "' AND edate = 0 AND active = 1"); safeCheck($u);
    
	if($user == $u["uname"] && password_verify($pass, $u["upass"]) /*&& $captcha_valid*/){
		$_SESSION['lg'] = 'bg';
		$_SESSION["time_".md5("realtyinb")] = time();
		$_SESSION["uid"] = $u["id"];
		$_SESSION["levelA"] = $u["level"];
		$_SESSION["uname"] = $u["uname"];
		$_SESSION["sessid"] = md5(time());		
	    $_SESSION["userAdmin"] = $u;
		
		if ( $u["level"] == 1 ){
			header("Location: news.php");
		}else{
			header("Location: menus.php");
		}
		die();
		
	}else{
	   echo "<br>";
	   echo "<center><H3><font color=red>Wrong password <br>or <br>username!</font></H3></center>";
	   echo "<center><a href=\"index.php\">Back!</a></center>";
	   die();
	}
?>