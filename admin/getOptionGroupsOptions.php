<?php	include("globals.php");		$option_group_id = (int)$_REQUEST["option_group_id"];		$options = $db->getAll("SELECT *, option_text AS name FROM ".$options_table." WHERE edate = 0 AND option_group_id = '".$option_group_id."' ORDER BY pos"); safeCheck($options);	echo json_encode($options);	?>