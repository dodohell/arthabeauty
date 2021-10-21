<?php
include("globals.php");
$brand_id = $params->getInt("brand_id");
$collection_id = $params->getInt("collection_id"); 
$str = "";
if($brand_id > 0){
     $collections = Collections::getCollectionsByBrandId($brand_id);
     //echo json_encode($collections);
     $str .= '<label for="collection_id" class="control-label mt-3">Колекция:</label> ';
     $str .= '<select name="collection_id" class="form-control form-control-sm">';
     $str .= '<option value="">Не е избрана колекция</option>';
     foreach($collections as $k => $v){
         if ( $collection_id == $v["id"]){
             $selected = " selected";
         }else{
             $selected = "";
         }
         $str.= '<option value="'.$v["id"].'"'.$selected.'>'.$v["name"].'</option>';
     }
     $str .= '</select>';
 }
echo $str;?>
