<?php
	class Favourites extends Settings{
		
		
		function getRecord($id = 0){
			global $db;
			global $favourites_table;
			
			$id = (int)$id;
			
			$row = $db->getRow("SELECT * FROM ".$favourites_table." WHERE id = '".$id."'"); safeCheck($row);
			
			return $row;
		}
		
		function deleteFavourite($params){
			global $db;
			global $user;
			global $favourites_table;
			
			settings::checkLogin();
			$id = (int)$params["id"];
			
			$res = $db->autoExecute($favourites_table, array("edate" => time()), DB_AUTOQUERY_UPDATE, " id = '".$id."' AND user_id = '".$user["id"]."' "); safeCheck($res);
			
			return $row;
		}
		
		
		function getFavourites($options = array()){
			global $db;
			global $lng;
			global $user;
			global $favourites_table;
			global $customers_to_favourites_table;
			
			$sql_where = ""; 
			
			if ( $options["customer_id"] ){
				$sql_where_from = ", ".$customers_to_favourites_table." AS ctct ";
				$sql_where = "AND ctct.customer_id = '".$options["customer_id"]."' AND ctct.category_type_id = favourites.id";
			}
			
			$sql = "SELECT favourites.*, 
						 favourites.name_{$lng} AS name
				    FROM ".$favourites_table." AS favourites {$sql_where_from}
				    WHERE favourites.edate = 0 
				    AND favourites.name_{$lng} <> ''
				    AND favourites.active = 'checked'
					AND favourites.user_id = '".$user["id"]."'
				    {$sql_where}
				    ORDER BY favourites.pos";
			
			$favourites = $db->getAll($sql); safeCheck($favourites);
			
			return $favourites;
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
			global $favourites_table;
			global $left;
			
			$sm->configLoad($language_file);
			$configVars = $sm->getConfigVars();
			$sm->configLoad($htaccess_file);
			$htaccessVars = $sm->getConfigVars();
			
			$row = $db->getRow("SELECT id, name_{$lng} AS name, url_{$lng} AS url, url_target AS target, htaccess_url_{$lng} AS htaccess_url FROM ".$favourites_table." WHERE id = '".$id."'"); safeCheck($row);
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
		
		function getPage($params){
			global $favourites_table;
			global $db;
			global $sm;
			global $lng;
			global $host;
			global $user;
			global $language_file;
			global $htaccess_file;
			global $customers_table;
			global $customers_images_table;
			global $cities_table;
			global $galleries_albums_images_table;
			global $galleries_albums_table;
			
			settings::checkLogin();
			
			if ( $params["param"] == "favourites/organisation" ){
				$sm->assign("show_tab", 1);
			}else{
				$sm->assign("show_tab", 0);
			}
			
			$inspirations = $db->getAll("(
													SELECT favourites.id, 
															favourites.type,
														   (SELECT images.gallery_album_id FROM ".$galleries_albums_images_table." AS images WHERE images.id = favourites.image_id ORDER BY images.pos LIMIT 1) AS album_id,
															favourites.id AS pos,
														   (SELECT images.name_{$lng} FROM ".$galleries_albums_images_table." AS images WHERE images.id = favourites.image_id ORDER BY images.pos LIMIT 1) AS name,
														   (SELECT images2.file FROM ".$galleries_albums_images_table." AS images2 WHERE images2.id = favourites.image_id ORDER BY images2.pos LIMIT 1) AS file
													FROM ".$favourites_table." AS favourites
													WHERE edate = 0 
													AND type = 'image'
													AND user_id = '".$user["id"]."'
												)UNION(
													SELECT favourites.id, 
															favourites.type,
															favourites.album_id AS album_id,
															favourites.id AS pos,
														   (SELECT albums.name_{$lng} FROM ".$galleries_albums_table." AS albums WHERE albums.id = favourites.album_id ORDER BY albums.pos LIMIT 1) AS name,
														   (SELECT images.file FROM ".$galleries_albums_images_table." AS images WHERE images.gallery_album_id = favourites.album_id ORDER BY images.pos LIMIT 1) AS file
													FROM ".$favourites_table." AS favourites
													WHERE edate = 0 
													AND type = 'album'
													AND user_id = '".$user["id"]."'
												)
												ORDER BY pos
											   "); safeCheck($inspirations);
			$sm->assign("inspirations", $inspirations);
			
			$organisation = $db->getAll("SELECT * FROM ".$favourites_table." WHERE edate = 0 AND type = 'customer' AND user_id = '".$user["id"]."' ORDER BY id DESC"); safeCheck($organisation);
			foreach($organisation as $k => $v){
				$sql =  "SELECT customers.*, 
						 customers.name_{$lng} AS name,
						 customers.excerpt_{$lng} AS excerpt,
						 (SELECT images.file FROM ".$customers_images_table." AS images WHERE images.customer_id = customers.id ORDER BY pos LIMIT 1) AS pic,
						 (SELECT cities.name_{$lng} FROM ".$cities_table." AS cities WHERE cities.id = customers.city_id) AS city_name
					FROM ".$customers_table." AS customers
						{$sql_where_from}
					WHERE customers.edate = 0 
					AND customers.name_{$lng} <> ''
					AND customers.id = '".$v["customer_id"]."'";
				$row = $db->getRow($sql); safeCheck($row);
				$v["info"] = $row;
				$organisation[$k] = $v;
			}
			$sm->assign("organisation", $organisation);
			
			$sm->assign("page_profile", 1);
			$sm->display("favourites.html");
		}
		
	}
	
?>