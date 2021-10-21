<?php
	class SpecialOffers extends Settings{
		
		
		function getRecord($id = 0){
			global $db;
			global $lng;
			global $special_offers_table;
			
			$id = (int)$id;
			
			$row = $db->getRow("SELECT *, name_{$lng} AS name, excerpt_{$lng} AS excerpt, offer_conditions_{$lng} AS offer_conditions, description_{$lng} AS description, discount_description_{$lng} AS discount_description, meta_title_{$lng} AS meta_title, meta_description_{$lng} AS meta_description, meta_keywords_{$lng} AS meta_keywords, meta_metatags_{$lng} AS meta_metatags FROM ".$special_offers_table." WHERE id = '".$id."'"); safeCheck($row);
			
			return $row;
		}
		
		
		function getSpecialOffers($options = array()){
			global $db;
			global $lng;
			global $special_offers_table;
			global $customers_to_special_offers_table;
			global $customers_table;
			
			$sql_where = ""; 
			
			if ( $options["customer_id"] ){
				$sql_where_from = ", ".$customers_to_special_offers_table." AS ctct ";
				$sql_where = "AND ctct.customer_id = '".$options["customer_id"]."' AND ctct.category_type_id = special_offers.id";
			}
			if ( $options["limit"] ){
				$sql_limit = " LIMIT ".$options["limit"]." ";
			}
			
			$sql = "SELECT special_offers.*, 
						 special_offers.name_{$lng} AS name,
						 special_offers.excerpt_{$lng} AS excerpt,
						 special_offers.discount_description_{$lng} AS discount_description,
                                                     (SELECT name_{$lng} FROM $customers_table WHERE $customers_table.id=special_offers.customer_id) AS customerName
				    FROM ".$special_offers_table." AS special_offers {$sql_where_from}
				    WHERE special_offers.edate = 0 
				    AND special_offers.name_{$lng} <> ''
				    AND special_offers.active = 'checked'
					AND date_to >= '".time()."'
				    {$sql_where}
				    ORDER BY special_offers.date_to, special_offers.pos 
					{$sql_limit}
					";
			
			$special_offers = $db->getAll($sql); safeCheck($special_offers);
			$row["downloadedVouchers"] = $this->downloadedVouchers($id);
			foreach($special_offers as $key => $offer){
				$special_offers[$key]["downloadedVouchers"] = $this->downloadedVouchers($offer['id']);
			}
			
			
			$sql = "SELECT special_offers.*, 
						 special_offers.name_{$lng} AS name,
						 special_offers.excerpt_{$lng} AS excerpt,
						 special_offers.discount_description_{$lng} AS discount_description,
                                                     (SELECT name_{$lng} FROM $customers_table WHERE $customers_table.id=special_offers.customer_id) AS customerName
				    FROM ".$special_offers_table." AS special_offers {$sql_where_from}
				    WHERE special_offers.edate = 0 
				    AND special_offers.name_{$lng} <> ''
				    AND special_offers.active = 'checked'
					AND date_to < '".time()."'
				    {$sql_where}
				    ORDER BY special_offers.date_to, special_offers.pos 
					{$sql_limit}
					";
			
			$special_offers_expired = $db->getAll($sql); safeCheck($special_offers_expired);
			$row["downloadedVouchers"] = $this->downloadedVouchers($id);
			foreach($special_offers_expired as $key => $offer){
				$special_offers_expired[$key]["downloadedVouchers"] = $this->downloadedVouchers($offer['id']);
				
				$special_offers[] = $special_offers_expired[$key];
			}
			return $special_offers;
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
			global $special_offers_table;
			global $left;
			
			$sm->configLoad($language_file);
			$configVars = $sm->getConfigVars();
			$sm->configLoad($htaccess_file);
			$htaccessVars = $sm->getConfigVars();
			
			$row = $db->getRow("SELECT id, name_{$lng} AS name FROM ".$special_offers_table." WHERE id = '".$id."'"); safeCheck($row);
			$row['link_title'] = str_replace($link_find, $link_repl, $row['name']);
			
			$htaccess_prefix = $htaccessVars["htaccess_specialoffer"];
			
                        $tmp_breadcrumbs .= '<a href="/'.$htaccessVars["htaccess_specialoffers"].'">'.$configVars['special_offers'].'</a>';
                        if($id){
                            $tmp_breadcrumbs .= ' >> <a href="/'.$htaccess_prefix.'/'.$row["id"].'" target="'.$row["target"].'">'.$row["name"].'</a>';
                        }
			
			
			return '<a href="'.$host.'">'.$configVars["home_breadcrumbs"].'</a> >> '.$tmp_breadcrumbs;
			
		}
		
		function getPage($id){
			global $special_offers_table;
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
			
			$settings = new Settings();
			
			$sm->assign("infoTitle", $settings->getFromCommon('special_offers_meta_title')["description"]);
			$sm->assign("infoDescr", $settings->getFromCommon('special_offers_meta_description')["description"]);
			$sm->assign("infoKeys", $settings->getFromCommon('special_offers_meta_keywords')["description"]);
			$sm->assign("special_offers_description", $settings->getFromCommon('special_offers'));
			
			$var = $sm->configLoad($language_file);
			$configVars = $sm->getConfigVars();
			
			$special_offers = $this->getSpecialOffers();
			$sm->assign("special_offers", $special_offers);
			
			$breadcrumbs = self::generateBreadcrumbs();
			$sm->assign("breadcrumbs", $breadcrumbs);
			$sm->assign("page_categories", 1);
                        
			$sm->display("special-offers.html");
		}
		
		function getPageSpecialOffer($params){
			global $special_offers_table;
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
			
			
			$id = (int)$params["id"];
			
			$row = $this->getRecord($id);
			$row["downloadedVouchers"] = $this->downloadedVouchers($id);
			
			$customer = Customers::getRecord($row["customer_id"]);
			$sm->assign("customer", $customer);
			
			$sm->assign("infoKeys", 	$row['meta_keywords']);
			if ( $row["meta_title"] ){
				$sm->assign("infoTitle", $row['meta_title']);
			}else{
				$sm->assign("infoTitle", $row['name'].", ".$customer["name"]);
			}
			$sm->assign("infoDescr", $row['meta_description']);
			
			$sm->assign("row", $row);
			
			if ( $id ){
				$settingsObj = new Settings();
				$settingsObj->setLoginRedirect("get_special_offer_voucher", $id);
			}
			
			// $sm->assign("page_categories", 1);
			$breadcrumbs = self::generateBreadcrumbs($id);
			$sm->assign("breadcrumbs", $breadcrumbs);
			
			$sm->display("show_special_offer.html");
		}
		
		function checkVoucher($id, $user){
			global $db;
			global $special_offers_vouchers_table;
			
			$row = $db->getRow("SELECT * FROM ".$special_offers_vouchers_table." WHERE special_offer_id = '".$id."' AND user_id = '".$user["id"]."'"); safeCheck($row);
			return $row;
		}
		
		function downloadedVouchers($id){
			global $db;
			global $special_offers_vouchers_table;
			
			$row = $db->getRow("SELECT count(id) AS cntr FROM ".$special_offers_vouchers_table." WHERE special_offer_id = '".$id."' "); safeCheck($row);
			
			return $row["cntr"];
		}
		
		function generateVoucher($id, $user=array(), $params=array()){
			global $db;
			global $lng;
			global $special_offers_vouchers_table;
			global $emails_test;
			
			$row = $this->getRecord($id);
			
			$secret = rand(100000,999999);
			
			$fields = array(
								"user_id" => $user["id"],
								"special_offer_id" => $id,
								"customer_id" => $row["customer_id"],
								"secret" => $secret,
								"postdate" => time(),
								"user_name" => $user["name"],
								"wedding_date" => strtotime($params["wedding_date"]),
								"user_name_2" =>  htmlspecialchars(trim($params["name"]),ENT_QUOTES),
								"phone" => htmlspecialchars(trim($params["phone"]),ENT_QUOTES),
								"email" => htmlspecialchars(trim($params["email"]),ENT_QUOTES),
								"date_to" => $row["date_to"],
								"ip" => $_SERVER["REMOTE_ADDR"]
							);
			$res = $db->autoExecute($special_offers_vouchers_table, $fields, DB_AUTOQUERY_INSERT); safeCheck($res);
			$id = mysqli_insert_id($db->connection);
			
			$customer = Customers::getRecord($row["customer_id"]);
			
			$settingsObj = new Settings();
			/*
			
			if ( $lng == "ro" ){
				$message .= "Utilizatorul „Mirese şi Nunţi” a cerut voucherul pentru reducerea oferită de dumneavoastră.<br/>";
				$message .= "Detalii despre voucher:<br/>Denumire : ".$params["name"]."<br/>Număr de ordin: ".$id."<br>Cod secret:".$secret;

				$subject = "Un utilizator a cerut voucherul pentru reducerea oferită de dumneavoastră";
			}else{
				$message .= "Потребител на WeddingDay.bg изтегли ваучер за Ваша промоционална оферта.<br/>";
				$message .= "Данните на ваучера са.<br/>Име: ".$params["name"]."<br/>Номер на ваучер: ".$id."<br>Секретен код:".$secret;

				$subject = "Потребител изтегли ваучер за Ваша промоция";
			}
			
			foreach($emails_test as $v){
				$settingsObj->mailSender($v, $subject, $message);
			}
			
			$settingsObj->mailSender($customer["contact_email"], $subject, $message);
			*/
			
			return $id;
		}
		
		function getPagePrintPdf($params){
			global $db;
			global $lng;
			global $sm;
			global $user;
			global $install_path;
			global $emails_test;
			global $special_offers_vouchers_table;
			
			$settingsObj = new Settings();
			
			$id = (int)$params["id"];
			
			$row = $this->getRecord($id);
			$sm->assign("row", $row);
			
			$customer = Customers::getRecord($row["customer_id"]);
			$sm->assign("customer", $customer);
			
			if ( trim($params["name"]) && trim($params["phone"]) && trim($params["email"]) ){
				if($user['id']){
					
					$voucher = $this->checkVoucher($id, $user, $params);

					if ( !$voucher["id"] ){
						$this->generateVoucher($id, $user, $params);
						$voucher = $this->checkVoucher($id, $user);
					
					}
					
				}else{
					$voucher_id = $this->generateVoucher($id, array(), $params);
			
					$voucher = $db->getRow("SELECT * FROM ".$special_offers_vouchers_table." WHERE id = '".$voucher_id."'"); 
					safeCheck($voucher);
				
				}

				$sm->assign("voucher", $voucher);
				//$download_file = $install_path."files-export/".$user["id"]."/offer-voucher-".$id.".pdf";
				
				$sm->assign("page_categories", 1);
				
				$settingsObj = new Settings();
				$site_title = $settingsObj->getFromCommon('title');
				
				ob_start();
				$sm->display("special-offer-voucher.html");	
				$html = ob_get_clean();
				
				$mpdf = new mPDF(); 
				$mpdf->useAdobeCJK = true;		
				$mpdf->SetAutoFont(AUTOFONT_ALL);	
				$mpdf->SetHTMLHeader($site_title["description"]);
				$mpdf->WriteHTML($html);
				
				
				
				if($user){
					if ( file_exists($install_path."files-export/".$user["id"]."/") === false ){
						mkdir($install_path."files-export/".$user["id"]."/");
						chmod($install_path."files-export/".$user["id"]."/", 0777);
					}
					$download_file = $install_path."files-export/".$user["id"]."/offer-voucher-".$id.".pdf";
					$mpdf->Output($download_file, 'F'); 
				}else{
					$download_file = $install_path."files-export/not_registered/offer-voucher-".$id.".pdf";
					$mpdf->Output($download_file, 'F'); 
				}
				
				$download_file_clear = "offer-voucher-".$id.".pdf";
				$mpdf->Output($download_file_clear, 'D'); 
				//$mpdf->Output(); 
				
				
				
				if ( $lng == "ro" ){
					$message .= "Bună ziua, ".$params["name"]."!.<br/>";
					$message .= "A-ţi solicitat voucherul de cumpărături \"<b>".$row["name"]."\" de pe contul ".$customer["name"]."</b>!<br><br> Detalii despre voucher:<br/>Denumire: ".$params["name"]."<br/>Telefon: ".$params["phone"]."<br/>Număr de ordin: ".$voucher["id"]."<br>Cod secret:".$voucher["secret"]."<br><br>De asemenea v-am trimis aceste detalii pe adresa dumneavoastră de e-mail!<br><br>Vă mulţumim,<br> Echipa Mirese şi Nunţi";

					$subject = "Reducerea specială pentru dumneavoastră";
				}else{
					$message .= "Здравейте, ".$params["name"]."!.<br/>";
					$message .= "Вие изтеглихте ваучер за \"<b>".$row["name"]."\" от ".$customer["name"]."</b>!<br><br> Данните на Вашият ваучер са:<br/>Име: ".$params["name"]."<br/>Телефон: ".$params["phone"]."<br/>Номер на ваучер: ".$voucher["id"]."<br>Секретен код:".$voucher["secret"]."<br><br>Междувременно изпращаме Вашият ваучер и като прикачен файл към този е-мейл!<br><br>Благодарим Ви, че използвате WeddingDay.bg!<br>Екипът";

					$subject = "Изтеглен ваучер за промоция";
					
				}
				foreach($emails_test as $v){
					$settingsObj->mailSender($v, $subject, $message, $download_file);
				}
				
				$settingsObj->mailSender($params["email"], $subject, $message, $download_file);
				
				if(!$user){
					unlink($download_file);
				}
				
				if ( $voucher["id"] ){
				
					$message = "";
					
					if ( $lng == "ro" ){
						$message .= "Utilizatorul „Mirese şi Nunţi” a cerut voucherul pentru reducerea oferită de dumneavoastră.<br/>";
						$message .= "Detalii despre voucher:<br/>Denumire : ".$params["name"]."<br />Telefon: ".$params["phone"]."<br />e-mail: ".$params["email"]."<br/>Data căsătoriei: ".$params["wedding_date"]."<br/>Număr de ordin: ".$voucher["id"]."<br>Cod secret:".$voucher["secret"];

						$subject = "Un utilizator a cerut voucherul pentru reducerea oferită de dumneavoastră";
					}else{
						$message .= "Потребител на WeddingDay.bg изтегли ваучер за Ваша промоционална оферта.<br/>";
						$message .= "Данните на ваучера са.<br/>Име: ".$params["name"]."<br />Телефон: ".$params["phone"]."<br />e-mail: ".$params["email"]."<br/>Дата на сватбата: ".$params["wedding_date"]."<br/>Номер на ваучер: ".$voucher["id"]."<br>Секретен код:".$voucher["secret"];

						$subject = "Потребител изтегли ваучер за Ваша промоция";
					}
					
					
					foreach($emails_test as $v){
						$settingsObj->mailSender($v, $subject, $message);
					}
					
					$settingsObj->mailSender($customer["contact_email"], $subject, $message);
				}
			}
		}
		
		
	}
	
?>