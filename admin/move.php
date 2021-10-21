<?php
	include "globals.php";
    
	/*** Set Table And Where ***/
	$commonData = explode("|", $_REQUEST['commonData']);
    $table = ${$commonData[0]};
	$where = $commonData[1];
    
	foreach($_REQUEST['items'] as $key => $values) {
		$itemData = explode("@@_@@", $values);
		$sql = "UPDATE " . $table . " SET pos = " . $itemData[0] . " WHERE id = " . $itemData[1] . " " . $where;
		//dbg($sql);
		$res = $db->Query($sql); safeCheck($res);
	}
?>