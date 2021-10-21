<?php
function gen_hidden_fields($arr) {
 $res = "";
        if( count($arr) > 0 ) {
		foreach($arr as	 $key => $value ) {
			$res .= ' <input name="'.$key.'" type="hidden" value="'.$value.'"> ';
		} 
	}		
return $res;
}

function gen_link($params) {
	$res = "?";
	foreach($params as $key => $val) {
	  if($key != "lang") {
		$res .= "{$key}={$val}&";
	  }
	}
	return $res;
}


function extract_lng() {
global $_SESSION;
	$str = $_SESSION["lang"];
	$arr = explode("|",$str);
	foreach($arr as $key => $a) {
		$tmp = explode("=",$a);
		$arr[$key] = array( "name" =>$tmp[0] , "id" => $tmp[1]);
	}
	return $arr;
}

function multiDelete(&$db, $table, $ids) {
    $edate = time();
		$sql = "UPDATE {$table} SET edate='{$edate}' WHERE id=?";
		$sth = $db->prepare($sql); safeCheck($sth);
		 if(count($ids) > 0) {
			 foreach($ids as $id) {
				 $res = $db->execute($sth, $id); safeCheck($res);
			}
		 }
}
?>
