<?php
include("globals.php");	
$product_type_id = (int)$_REQUEST["product_type_id"];
$product_id = (int)$_REQUEST["product_id"];
$sql = "SELECT attributes.id AS id,
attributes.name_{$lng} AS name
FROM ".$attributes_table." AS attributes,
".$product_type_to_attribute_table." AS ptta
WHERE attributes.edate = 0	
AND ptta.product_type_id = '".$product_type_id."'	
AND ptta.attribute_id = attributes.id
ORDER BY attributes.pos";
$attributes = $db->getAll($sql);
safeCheck($attributes);	
$str .= "";
foreach($attributes as $k => $v){
    $attributes_options = $db->getAll("SELECT *, option_text_{$lng} AS option_text FROM ".$attributes_to_attribute_options_table." WHERE edate = 0 AND attribute_id = '".$v["id"]."' ORDER BY pos");
    safeCheck($options);
    $str .= '<br /><strong>'.$v["name"].'</strong><br />';
    //$str .= '<select name="attribute_'.$v["id"].'" class="input-xlarge">';
    //$str .= '<option value="">не е избран</option>';
    foreach($attributes_options as $kk => $vv){
        $check = $db->getRow("SELECT * FROM ".$product_to_attribute_option_table." WHERE product_id = '".$product_id."' AND attribute_id = '".$v["id"]."' AND attribute_option_id = '".$vv["id"]."' ");
        safeCheck($check);
        if ( $check["id"] ){
            //$selected = " selected";
            $selected = " checked";
        }else{
            $selected = "";
        }
        //			$str .= '<div class="checkbox inline"><label for="attribute_'.$v["id"].'"><input type="checkbox" name="attribute_'.$v["id"].'[]" value="'.$vv["id"].'"'.$selected.' id="attribute_'.$v["id"].'" class="">'.$vv["option_text"].'</label></div>';
        $str .= '<div class="form-check form-check-inline"><input class="form-check-input" type="checkbox" name="attribute_'.$v["id"].'[]" value="'.$vv["id"].'"'.$selected.' id="attribute_'.$vv["id"].'"><label class="form-check-label" for="attribute_'.$vv["id"].'">'.$vv["option_text"].'</label></div>';
    }
    //$str .= '</select>';
}
echo $str;
?>
