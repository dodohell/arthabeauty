<?php
include "globals.php";
$id = $_REQUEST['id'];
$selected_product = (int)$_REQUEST['selected_product'];
$selected = $_REQUEST['selected'];
$limitString = $_REQUEST['search'];

if($limitString!=''){
//	$strLimiter = "AND name_{$lng} LIKE '%" . $limitString . "%' ";
    $strLimiter = "AND (name_en LIKE '%".$limitString."%' OR name_{$lng} LIKE '%".$limitString."%' OR id = '".$limitString."' OR code LIKE '%".$limitString."%') ";
}

$secondary = $_REQUEST['secondary'];
if( !isset($secondary) || $secondary==''){
	$error = 'ERROR : No secondary target provided';
	die($error);
}
$secondaryTable = 'product_to_' . $secondary . '_table';

$secondaryFieldId = $secondary != "product_similar" ? $secondary . '_id' : "product_id";

if($secondary == 'product' || $secondary == 'product_similar'){
	$secondaryFieldId = 'main_'.$secondaryFieldId;
}
/*
function searchForName($str, $array) {
   foreach ($array as $key => $val) {
	   if ( false !== stripos ( $val['name'] , $str) ) {
	  
		   $out[]=$val;
	   }
   }
   return $out;
}*/

if( !isset($selected) || $selected==0){
	$products = $db->getAll("SELECT
                                id, name_{$lng} AS name,
                                code 
                            FROM
                                ".$products_table."
                            WHERE 
                                edate = 0 
                            -- AND name_{$lng} LIKE '%".$limitString."%'
                            {$strLimiter}
                            ORDER BY code, name "); safeCheck($products);
                            
}else{
	if ( $selected_product ){
		$products = $db->getAll("SELECT
							products.id, 
							products.name_{$lng} AS name,
							products.code 
						FROM ".$products_table." as products
						WHERE 
							edate = 0
							{$strLimiter}
						AND products.id = '{$selected_product}'
						ORDER BY code, name "); safeCheck($products);

	}else{
		$products = $db->getAll("SELECT
									products.id, 
									products.name_{$lng} AS name,
									products.code 
								FROM ".$products_table." as products,
								".${$secondaryTable}." as sec
								WHERE 
									edate = 0
								{$strLimiter}
								AND sec.product_id = products.id
								AND sec.".$secondaryFieldId." = '".$selected."'
								ORDER BY code, name "); safeCheck($products);
	}
}

//dbg( sizeof($products) );
/*	 OLD PHP APPROACH , changed to LIKE
if($limitString!=''){
	$products = searchForName($limitString , $products); 
}*/
header('Content-Type: application/json');
echo json_encode($products);
?>
