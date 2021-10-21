<?
	class Faqs extends Settings{
		
		
		function getRecord($id = 0){
			global $db;
			global $lng;
			global $faqs_table;
			
			$id = (int)$id;
			
			$row = $db->getRow("SELECT *, name_{$lng} AS name, description_{$lng} AS description FROM ".$faqs_table." WHERE id = '".$id."'"); safeCheck($row);
			
			return $row;
		}
		
		
		function getFaqs($options = array()){
			global $db;
			global $lng;
			global $faqs_table;
			
			$sql_where = ""; 
			
			if ( $options["customer_id"] ){
				$sql_where = " AND customer_id = '".$options["customer_id"]."' ";
			}
			
			$faqs = $db->getAll("SELECT *, 
										 name_{$lng} AS name,
										 description_{$lng} AS description
								  FROM ".$faqs_table." 
								  WHERE edate = 0 
								  AND name_{$lng} <> ''
								  {$sql_where}
								  AND active = 'checked'
								  ORDER BY pos"); safeCheck($faqs);
			
			return $faqs;
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
			global $faqs_table;
			global $left;
			
			$sm->configLoad($language_file);
			$configVars = $sm->getConfigVars();
			$sm->configLoad($htaccess_file);
			$htaccessVars = $sm->getConfigVars();
			
			$row = $db->getRow("SELECT id, name_{$lng} AS name, url_{$lng} AS url, url_target AS target, htaccess_url_{$lng} AS htaccess_url FROM ".$faqs_table." WHERE id = '".$id."'"); safeCheck($row);
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
		
	}
	
?>