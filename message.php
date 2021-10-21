<?
	include("globals.php");
	
	$code = (int)$_REQUEST["code"];
	
	$sm->configLoad($language_file);
	$configVars = $sm->getConfigVars();
	
	$sm->assign("message_title", $configVars["message_title_".$code]);
	$sm->assign("message_description", $configVars["message_description_".$code]);
	$sm->display("message.html");
?>