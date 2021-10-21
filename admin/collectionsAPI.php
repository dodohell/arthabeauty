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
$secondaryTable = 'collection_to_' . $secondary . '_table';
$secondaryFieldId = $secondary . '_id';

if( !isset($selected) || $selected==0)	{
	$collection = $db->getAll("SELECT id, name_{$lng} AS name  FROM ".$collections_table." WHERE edate = 0 AND name_{$lng} LIKE '%" . $limitString . "%' ORDER BY  name "); safeCheck($collection);
}else{
	$collection = $db->getAll("SELECT collection.id, collection.name_{$lng} AS name FROM ".$collections_table." as collection, ".$$secondaryTable." as sec
								WHERE edate = 0
								{$strLimiter}
								AND sec.collection_id = collection.id
								AND sec.".$secondaryFieldId." = '".$selected."'
								ORDER BY  name "); safeCheck($collection);
}

header('Content-Type: application/json');
echo json_encode($collection);
?>
