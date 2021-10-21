<?php
	class Faqs extends Settings{

		public $pagination = "";

		function getRecord($id = 0){
			global $db;
			global $faqs_table;

			$id = (int)$id;

			$row = $db->getRow("SELECT * FROM ".$faqs_table." WHERE id = '".$id."'"); safeCheck($row);

			return $row;
		}

		function updateRow($test = ""){
			global $db;
			global $faqs_table;

			$row = $db->getRow("SELECT * FROM ".$faqs_table.""); safeCheck($row);

			return $row;
		}

		function addEditRow($params){
			global $db;
			global $faqs_table;

			$act = $params["act"];
			$id = (int)$params["id"];
			$customer_id = (int)$params["customer_id"];
			$fields = array(
				'name_bg'	=> htmlspecialchars(trim($params['name_bg'])),
				'name_en'	=> htmlspecialchars(trim($params['name_en'])),
				'name_de'	=> htmlspecialchars(trim($params['name_de'])),
				'name_ru'	=> htmlspecialchars(trim($params['name_ru'])),
				'name_ro'	=> htmlspecialchars(trim($params['name_ro'])),
				'description_bg'			=> $params['description_bg'],
				'description_en'			=> $params['description_en'],
				'description_de'			=> $params['description_de'],
				'description_ro'			=> $params['description_ro'],
				'description_ru'			=> $params['description_ru'],
				'customer_id'			=> $customer_id,
				'active'			=> $params['active'],
				'cms_user_id'		=> $_SESSION["uid"],
			);

			if($act == "add") {
				shiftPos($db, $faqs_table);
				$res = $db->autoExecute($faqs_table,$fields,DB_AUTOQUERY_INSERT); safeCheck($res);
				$id = mysqli_insert_id($db->connection);
			}

			if($act == "edit") {
				$id = (int)$params["id"];
				$res = $db->autoExecute($faqs_table,$fields,DB_AUTOQUERY_UPDATE,"id = " . $id);safeCheck($res);
			}

			$htaccessUpdate = new Settings();
			$htaccess_type = "faqs";

			if ( $params["htaccess_url_bg"] ){
				$htaccessUpdate->updateHtaccess("bg", $params["htaccess_url_bg"], $htaccess_type, $id);
			}
			if ( $params["htaccess_url_en"] ){
				$htaccessUpdate->updateHtaccess("en", $params["htaccess_url_en"], $htaccess_type, $id);
			}
			if ( $params["htaccess_url_de"] ){
				$htaccessUpdate->updateHtaccess("de", $params["htaccess_url_de"], $htaccess_type, $id);
			}
			if ( $params["htaccess_url_ru"] ){
				$htaccessUpdate->updateHtaccess("ru", $params["htaccess_url_ru"], $htaccess_type, $id);
			}
			if ( $params["htaccess_url_ro"] ){
				$htaccessUpdate->updateHtaccess("ro", $params["htaccess_url_ro"], $htaccess_type, $id);
			}

		}

		function postImage($file = "", $city_id = 0){
			global $db;
			global $faqs_images_table;

			$fields = array(
								"file" => $file,
								"city_id" => $city_id,
							);
			shiftPos($db, $faqs_images_table);
			$res = $db->autoExecute($faqs_images_table, $fields, DB_AUTOQUERY_INSERT); safeCheck($res);

			return $res;
		}

		function deleteImage($id){
			global $db;
			global $faqs_images_table;

			$fields = array(
								"edate" => time()
							);
			$res = $db->autoExecute($faqs_images_table, $fields, DB_AUTOQUERY_UPDATE, " id = '".$id."' "); safeCheck($res);

			return $res;
		}

		function postFile($file = "", $city_id = 0){
			global $db;
			global $faqs_files_table;

			$fields = array(
								"file" => $file,
								"city_id" => $city_id,
							);
			shiftPos($db, $faqs_files_table);
			$res = $db->autoExecute($faqs_files_table, $fields, DB_AUTOQUERY_INSERT); safeCheck($res);

			return $res;
		}

		function deleteFile($id){
			global $db;
			global $faqs_files_table;

			$fields = array(
								"edate" => time()
							);
			$res = $db->autoExecute($faqs_files_table, $fields, DB_AUTOQUERY_UPDATE, " id = '".$id."' "); safeCheck($res);

			return $res;
		}

		function deleteField($id, $field){
			global $db;
			global $lng;
			global $faqs_table;

			$res = $db->autoExecute($faqs_table, array( $field => "" ), DB_AUTOQUERY_UPDATE, " id = '".$id."' "); safeCheck($res);

			return $res;
		}

		function getFaqs($options = array()){
			global $db;
			global $lng;
			global $faqs_table;
			global $customers_to_faqs_table;

			if ( $options["customer_id"] ){
				$sql_customer = " AND customer_id = '".$options["customer_id"]."' ";
			}

			$faqs = $db->getAll("SELECT *, name_{$lng} AS name FROM ".$faqs_table." WHERE edate = 0 {$sql_customer} ORDER BY pos"); safeCheck($faqs);

			if ( $options["selected"] && $options["customer_id"] ){
				$faqsSelected = $db->getAll("SELECT * FROM ".$customers_to_faqs_table." WHERE customer_id = '".$options["customer_id"]."'"); safeCheck($faqsSelected);
				foreach($faqs as $k => $v){
					foreach($faqsSelected as $kk => $vv){
						if ( $vv["education_type_id"] == $v["id"] ){
							$v["selected"] = "checked";
						}
					}
					$faqs[$k] = $v;
				}
			}
			return $faqs;
		}

		function deleteRecord($id){
			global $db;
			global $faqs_table;

			$fields = array(
								"edate" => time()
							);
			$res = $db->autoExecute($faqs_table, $fields, DB_AUTOQUERY_UPDATE, " id = '".$id."' "); safeCheck($res);

			return $res;
		}

	}

?>
