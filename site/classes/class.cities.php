<?php
	class Cities extends Settings{
		
		
		public static function getRecord(int $id){
			global $db;
			global $cities_table;
			
			$row = $db->getRow("SELECT * FROM ".$cities_table." WHERE id = ".$id); safeCheck($row);
			
			return $row;
		}
		
		function searchCity($name = ""){
			global $db;
			global $lng;
			global $cities_table;
			
			$name = strtolower($name);
			
			$row = $db->getRow("SELECT *, name_{$lng} AS name FROM ".$cities_table." WHERE LOWER(name_{$lng}) = '".$name."' AND edate = 0"); safeCheck($row);
			
			return $row;
		}
		
		
		public static function getCities(FilteredMap $params, int $returnType = 1){
			global $db;
			global $lng;
			global $cities_table;
			
            $sql_region = "";
            if ($params instanceof FilteredMap && $params->has("region") && $params->getInt("region") > 0){
                $district_id = $params->getInt("region");
                $sql_region = " AND district_id = ".$district_id;
            }
			$cities = $db->getAll("SELECT
                                        id,
                                        district_id,
                                        city_type,
                                        postcode,
                                        name_{$lng} AS name,
                                        description_{$lng} AS description
								  FROM ".$cities_table." 
								  WHERE edate = 0 
								  -- AND name_{$lng} <> ''
								  AND active = 1
                                  {$sql_region}
								  ORDER BY pos"); safeCheck($cities);
            if($returnType === 1){
                return $cities;
            }else if($returnType === 3){
                echo json_encode($cities);
            }
		}
        
		public static function getCitiesByDistrictId($district_id, int $returnType = 1){
			global $db;
			global $lng;
			global $cities_table;
            
			$cities = $db->getAll("SELECT
                                        id,
                                        district_id,
                                        city_type,
                                        postcode,
                                        name_{$lng} AS name,
                                        description_{$lng} AS description
                                    FROM 
                                        {$cities_table}
                                    WHERE 
                                        edate = 0 
                                    AND name_{$lng} <> ''
                                    AND active = 1
                                    AND district_id = {$district_id}
                                    ORDER BY pos"); safeCheck($cities);
            if($returnType === 1){
                return $cities;
            }else if($returnType === 3){
                echo json_encode($cities);
            }
		}
        
		public static function getDistrictsAllActive(int $returnType = 1){
			global $db;
			global $lng;
			global $districts_table;
			
			$districts = $db->getAll("SELECT
                                        id,
                                        country_id,
                                        name_{$lng} AS name,
                                        description_{$lng} AS description
								  FROM ".$districts_table." 
								  WHERE edate = 0 
								  AND name_{$lng} <> ''
								  AND active = 1
								  ORDER BY pos"); safeCheck($cities);
            if($returnType === 1){
                return $districts;
            }else if($returnType === 3){
                echo json_encode($districts);
            }
		}

            
		function generateBreadcrumbs($id, $tmp_breadcrumbs = ""){
			global $db;
			global $sm;
			global $lng;
			global $link_find;
			global $link_repl;
			global $host;
			global $language_file;
			global $htaccess_file;
			global $cities_table;
			global $left;
			
			$sm->configLoad($language_file);
			$configVars = $sm->getConfigVars();
			$sm->configLoad($htaccess_file);
			$htaccessVars = $sm->getConfigVars();
			
			$row = $db->getRow("SELECT id, name_{$lng} AS name, url_{$lng} AS url, url_target AS target, htaccess_url_{$lng} AS htaccess_url FROM ".$cities_table." WHERE id = '".$id."'"); safeCheck($row);
			$row['link_title'] = str_replace($link_find, $link_repl, $row['name']);
			
			$htaccess_prefix = $htaccessVars["htaccess_info"];
			if ( $row["url"] ){
				$tmp_breadcrumbs = '<a href="'.$row["url"].'" target="'.$row["target"].'">'.$row["name"].'</a>'.$tmp_breadcrumbs;
			}elseif( $row["htaccess_url"] ){
				$tmp_breadcrumbs = '<a href="'.$row["htaccess_url"].'" target="'.$row["target"].'">'.$row["name"].'</a>'.$tmp_breadcrumbs;
			}else{
				$tmp_breadcrumbs = '<a href="/'.$htaccess_prefix.'/'.$row["id"].'" target="'.$row["target"].'">'.$row["name"].'</a>'.$tmp_breadcrumbs;
			}
			if ($row["category_id"] != 0){
				return self::generateBreadcrumbs($row["category_id"], $tmp_breadcrumbs);
			}else{
				return '<a href="'.$host.'">'.$configVars["home_breadcrumbs"].'</a>'.$tmp_breadcrumbs;
			}
		}
		
		function getPage($id){
			global $cities_table;
			global $static_images_table;
			global $static_files_table;
			global $db;
			global $sm;
			global $lng;
			global $host;
			global $language_file;
			global $htaccess_file;
			global $htaccess_file_bg;
			global $htaccess_file_en;
			global $htaccess_file_de;
			global $htaccess_file_ru;
			global $htaccess_file_ro;
			
			$row = $db->getRow("SELECT *, id,
										  name_{$lng} AS name,
										  description_{$lng} AS description,
										  pic_1,
										  pic_2,
										  pic_1_name_{$lng} AS pic_1_name,
										  pic_2_name_{$lng} AS pic_2_name,
										  contactsform,
										  category_id,
										  restricted,
										  menu_pos, 
										  meta_keywords_{$lng} AS meta_keywords,
										  meta_title_{$lng} AS meta_title,
										  meta_description_{$lng} AS meta_description
								FROM ".$cities_table."
								WHERE id = '{$id}'
								"); safeCheck($row);
			
			$sm->assign("infoKeys", 	$row['meta_keywords']);
			if ( $row["meta_title"] ){
				$sm->assign("infoTitle", $row['meta_title']);
			}else{
				$sm->assign("infoTitle", $row['name']);
			}
			$sm->assign("infoDescr", $row['meta_description']);
			
			
			if ($row["restricted"] == 1 && !isset($_SESSION["uid"])){
				header("Location: restricted.php");
				die();
			}
			
			$row["description"] = $row["description"];	
			
			$sm->assign("row", $row);
			
			$var = $sm->configLoad($language_file);
			$configVars = $sm->getConfigVars();
			
			
			
			$breadcrumbs = self::generateBreadcrumbs($id);
			
			$sm->assign("breadcrumbs", $breadcrumbs);
			
			
			$s = $row["category_id"];
			if ($s){
				$category_id = $s;
			}else{
				$category_id = $row["id"];
			}
			if ($category_id == 0){
				$row = $db->getRow("SELECT id,
											  name_{$lng} AS name,
											  description_{$lng} AS description,
											  pic_1,
											  category_id
									FROM ".$cities_table."
									WHERE id = '{$id}'
									"); safeCheck($row);
				$category_id = $row["id"];
			}
			$subCities = $db->getAll("SELECT id,
											name_{$lng} AS name,
											excerpt_{$lng} AS excerpt,
											description_{$lng} AS description,
											category_id,
											pic_1,
											htaccess_url_{$lng} AS htaccess_url,
											url_{$lng} AS url,
											url_target AS target
									FROM ". $cities_table ."
									WHERE category_id = '".$row["id"]."'
									AND active = 'checked'
									AND name_{$lng} <> ''
									AND edate = 0
									ORDER BY pos
									"); safeCheck($subCities);
													
			//print_r($subCities);
			foreach($subCities as $k=>$v){
				$v['link_title'] = str_replace($link_find, $link_repl, $v['name']);
				$subCities[$k] = $v;
			}
			$sm->assign("subCities", $subCities);
			
			$images =  $db->getAll("SELECT file, name_{$lng} AS name FROM " . $static_images_table . " as st1 WHERE info_id = ".$id." AND edate = 0 ORDER BY pos"); safeCheck($images);
			$sm->assign("images", $images);
			
			$files =  $db->getAll("SELECT file, name_{$lng} AS name FROM " . $static_files_table . " as st1 WHERE info_id = ".$id." AND edate = 0 ORDER BY pos"); safeCheck($files);
			$sm->assign("files", $files);
			
			
			$sm->configLoad($htaccess_file_en);
			$htaccess_en = $sm->getConfigVars();
			if ( $row["htaccess_url_en"] ){
				$link_en = $row["htaccess_url_en"];
			}else{
				if ( $row["url_en"] ){
					$link_en = $row["url_en"];
				}else{
					$link_en = "/".$htaccess_en["htaccess_info"]."/".$row["id"];
				}
			}
			
			$sm->configLoad($htaccess_file_ro);
			$htaccess_ro = $sm->getConfigVars();
			if ( $row["htaccess_url_ro"] ){
				$link_ro = $row["htaccess_url_ro"];
			}else{
				if ( $row["url_ro"] ){
					$link_ro = $row["url_ro"];
				}else{
					$link_ro = "/".$htaccess_ro["htaccess_info"]."/".$row["id"];
				}
			}/**/
			
			$sm->configLoad($htaccess_file_bg);
			$htaccess_bg = $sm->getConfigVars();
			if ( $row["htaccess_url_bg"] ){
				$link_bg = $row["htaccess_url_bg"];
			}else{
				if ( $row["url_bg"] ){
					$link_bg = $row["url_bg"];
				}else{
					$link_bg = "/".$htaccess_bg["htaccess_info"]."/".$row["id"];
				}
			}
			
			
			$sm->configLoad($htaccess_file_de);
			$htaccess_de = $sm->getConfigVars();
			if ( $row["htaccess_url_de"] ){
				$link_de = "/".$row["htaccess_url_de"];
			}else{
				if ( $row["url_de"] ){
					$link_de = $row["url_de"];
				}else{
					$link_de = "/".$htaccess_de["htaccess_info"]."/".$row["id"];
				}
			}
			
			$sm->configLoad($htaccess_file_ru);
			$htaccess_ru = $sm->getConfigVars();
			if ( $row["htaccess_url_ru"] ){
				$link_ru = "/".$row["htaccess_url_ru"];
			}else{
				if ( $row["url_ru"] ){
					$link_ru = $row["url_ru"];
				}else{
					$link_ru = "/".$htaccess_ru["htaccess_info"]."/".$row["id"];
				}
			}

			$sm->assign("link_bg", $link_bg);
			$sm->assign("link_en", $link_en);
			$sm->assign("link_de", $link_de);
			$sm->assign("link_ru", $link_ru);
			$sm->assign("link_ro", $link_ro);
			
			
			$sm->assign("s", $_REQUEST["s"]);
			$sm->assign("breadcrumbs", $breadcrumbs);
			
			$sm->display("info.html");
		}
		
	}
	
?>