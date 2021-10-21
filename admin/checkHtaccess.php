<?php
	include("globals.php");
	
	$htaccess_url = $params->getString("htaccess_url");
	$type = $params->getString("type");
	$record_id = $params->getInt("record_id");
	$lang_check = $params->getString("lang_check");;
	
	$settings = new Settings();
	$result = $settings->checkHtaccess($htaccess_url, $type, $record_id, $lang_check);
	
	echo $result;
	
?>