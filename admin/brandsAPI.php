<?php
include "./globals.php";
$id = $params->getInt("id");
$selected = $params->get("selected");
$limitString = $params->get("search");
if($limitString!=''){
	$strLimiter = "AND name_{$lng} LIKE '%" . $limitString . "%' ";
}

$secondary = $params->get("secondary");
if( !isset($secondary) || $secondary==''){
	$error = 'ERROR : No secondary target provided';
	die($error);
}
$secondaryTable = 'brand_to_' . $secondary . '_table';
$secondaryFieldId = $secondary . '_id';

if( !isset($selected) || $selected==0)	{
	$brand = $db->getAll("SELECT id, name_{$lng} AS name  FROM ".$brands_table." WHERE edate = 0 AND name_{$lng} LIKE '%" . $limitString . "%' ORDER BY  name "); safeCheck($brand);
}else{
	$brand = $db->getAll("SELECT brand.id, brand.name_{$lng} AS name FROM ".$brands_table." as brand, ".$$secondaryTable." as sec
								WHERE edate = 0
								{$strLimiter}
								AND sec.brand_id = brand.id
								AND sec.".$secondaryFieldId." = '".$selected."'
								ORDER BY  name "); safeCheck($brand);
}

header('Content-Type: application/json');
echo json_encode($brand);
?>
