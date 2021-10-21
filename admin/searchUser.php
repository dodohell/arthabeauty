<?php
	include("globals.php");
	
	$term = $params->getString("term");
	
	$sql = "(SELECT id AS user_id, 
										users.first_name, 
										users.last_name, 
										users.email 
								FROM ".$users_table." AS users
								WHERE MATCH(users.first_name,users.last_name,users.email) AGAINST('".$term."' IN BOOLEAN MODE) AND edate = 0) 
							UNION (SELECT ua.id AS user_id, 
											ua.firstname AS first_name, 
											ua.lastname AS last_name, 
											1 AS email 
								FROM ".$users_addresses_table." AS ua
								WHERE MATCH(ua.firstname,ua.lastname,ua.email,ua.phone,ua.company_name,ua.vat_number) AGAINST('".$term."' IN BOOLEAN MODE) AND edate = 0)
							ORDER BY CONCAT_WS(' ', first_name, last_name), first_name, last_name, email
							";
	$results = $db->getAll($sql); safeCheck($results);
	
	foreach($results as $k => $v){
		if ( $v["email"] == 1 ){
			$user_info = $db->getRow("SELECT * FROM ".$users_table." WHERE id = '".$v["user_id"]."'"); safeCheck($user_info);
			$v["email"] = $user_info["email"];
		}else{
			if ( !$v["first_name"] ){
				$user_info = $db->getRow("SELECT * FROM ".$users_addresses_table." WHERE user_id = '".$v["user_id"]."'"); safeCheck($user_info);
				$v["first_name"] = $user_info["firstname"];
				$v["last_name"] = $user_info["lastname"];
			}
		}
		$results[$k] = $v;
	}
	echo json_encode($results);
?>