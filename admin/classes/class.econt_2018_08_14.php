<?php
	class econt{
		
		public $username = "";
		public $password = "";
		public $url = 'http://demo.econt.com/e-econt/xml_service_tool.php';
		//public $url = 'http://www.econt.com/e-econt/xml_service_tool.php';
		public $url_import = 'http://demo.econt.com/e-econt/xml_parcel_import2.php';
		//public $url_import = 'www.econt.com/e-econt/xml_parcel_import2.php';
		
		function __construct($username = "", $password = "") {
			if ( $username ){
				$this->username = $username;
			}
			if ( $username ){
				$this->password = $password;
			}
		}
		
		function getLoginInfo(){
			$loginData["username"] = $this->username;
			$loginData["password"] = $this->password;
			
			return $loginData;
		}
		
		function createRequest($requestType = "cities", $filename = "econt_cities.xml", $filename_response = "econt_cities_response.xml", $cms = 0, $delivery_tracking_number = 0){
			// GENERATE INITIAL ECONT REQUEST 
			global $install_path;
            $xml .= '<?xml version="1.0" encoding="UTF-8"?>
						<request>
						 <client>
						  <username>'.$this->username.'</username>
						  <password>'.$this->password.'</password>
						 </client>';
			if ( $requestType == "cities" ){
				$xml .= '<request_type>cities</request_type>';
			}
			if ( $requestType == "regions" ){
				$xml .= '<request_type>cities_regions</request_type>';
			}
			if ( $requestType == "offices" ){
				$xml .= '<request_type>offices</request_type>';
			}
			if ( $requestType == "neighbourhoods" ){
				$xml .= '<request_type>cities_quarters</request_type>';
			}
			if ( $requestType == "streets" ){
				$xml .= '<request_type>cities_streets</request_type>';
			}
			if ( $requestType == "cancel_shipments" ){
				$xml .= '<request_type>cancel_shipments</request_type>';
				$xml .= '<cancel_shipments><num>'.$delivery_tracking_number.'</num></cancel_shipments>';
			}
			$xml .= '</request>';
			
			if ( $cms == 1 ){
				$dir_prefix = "/admin";
			}
			
			
//			if ( $requestType == "shipping_request" ){
//				// $filename = "order_valid.xml";
//				//dbg($this->url_import);
//				//$data = array( 'file' => '@'.$_SERVER["DOCUMENT_ROOT"].$dir_prefix."/".$filename);
//				$data = array( 'xml' => file_get_contents($_SERVER["DOCUMENT_ROOT"].$dir_prefix."/xml/".$filename));
//                
////                $ch = curl_init($this->url_import); 
////                curl_setopt_array($ch,array(
////                    CURLOPT_RETURNTRANSFER => true,
////                    CURLOPT_TIMEOUT => 120,
////                    CURLOPT_POSTFIELDS => $data
////                ));
////               
////                $r = curl_exec($ch);
//                //dbg($r); exit;
//				 
//				$options = array(
//					CURLOPT_URL            => $this->url_import,
//					CURLOPT_RETURNTRANSFER => true,
//					CURLOPT_HEADER         => false,
//					CURLOPT_FOLLOWLOCATION => true,
//					CURLOPT_ENCODING       => "",
//					CURLOPT_AUTOREFERER    => true,
//					CURLOPT_FAILONERROR    => true,
//					CURLOPT_CONNECTTIMEOUT => 120,
//					CURLOPT_TIMEOUT        => 120,
//					CURLOPT_MAXREDIRS      => 10,
//					CURLOPT_POSTFIELDS      => $data
//				);
//				
//				curl_setopt_array( $ch, $options );
//				$response = curl_exec($ch); 
//                
//				//echo $filename_response;
//				file_put_contents($filename_response,$response);        
//				curl_close($ch);
//			}
			if ( $requestType == "shipping_request" ){
				// $filename = "order_valid.xml";
				//dbg($this->url_import);
				$data = array( 'file' => '@'.$_SERVER["DOCUMENT_ROOT"].$dir_prefix."/".$filename);
				$ch = curl_init();
				$options = array(
					CURLOPT_URL            => $this->url_import,
					CURLOPT_RETURNTRANSFER => true,
					CURLOPT_HEADER         => false,
					CURLOPT_FOLLOWLOCATION => true,
					CURLOPT_ENCODING       => "",
					CURLOPT_AUTOREFERER    => true,
					CURLOPT_FAILONERROR    => true,
					CURLOPT_CONNECTTIMEOUT => 120,
					CURLOPT_TIMEOUT        => 120,
					CURLOPT_MAXREDIRS      => 10,
					CURLOPT_POSTFIELDS      => $data
				);
				
				curl_setopt_array( $ch, $options );
				$response = curl_exec($ch); 
                
				//echo $filename_response;
				file_put_contents($filename_response,$response);        
				curl_close($ch);
			}elseif ( $requestType == "cancel_shipments" ){
				// $filename = "order_valid.xml";
				$dir_prefix = $dir_prefix."/xml";
				file_put_contents($_SERVER["DOCUMENT_ROOT"].$dir_prefix."/".$filename, $xml);
				
				$data = array( 'file' => '@'.$_SERVER["DOCUMENT_ROOT"].$dir_prefix."/".$filename);
				$ch = curl_init();
				
				$options = array(
					CURLOPT_URL            => $this->url,
					CURLOPT_RETURNTRANSFER => true,
					CURLOPT_HEADER         => false,
					CURLOPT_FOLLOWLOCATION => true,
					CURLOPT_ENCODING       => "",
					CURLOPT_AUTOREFERER    => true,
					CURLOPT_FAILONERROR    => true,
					CURLOPT_CONNECTTIMEOUT => 120,
					CURLOPT_TIMEOUT        => 120,
					CURLOPT_MAXREDIRS      => 10,
					CURLOPT_POSTFIELDS      => $data
				);
				
				curl_setopt_array( $ch, $options );
				$response = curl_exec($ch); 
				
				
				
				// echo $filename_response;
				file_put_contents($filename_response,$response);        
				curl_close($ch);
			}else{
				// SAVE THE REQUEST XML FILE TO $filename VARIABLE
				if ( filectime($filename) <= time() - 2592000 ) {
					
					file_put_contents($filename, $xml);
					// SEND THE REQUEST XML USING CURL TO ECONT AND GET THE RESPONSE XML FILE IN $filename_response VARIABLE
					$xml = new DomDocument; 
					$data = array( 'file' => '@'.$filename);
					$ch = curl_init();
					curl_setopt($ch, CURLOPT_URL, $this->url);
					curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
					curl_setopt($ch, CURLOPT_POSTFIELDS,$data);
					$response = curl_exec($ch);
					
					$xml->save($filename_response);
					file_put_contents($filename_response,$response);        
					curl_close($ch);
				}
			}
			
			return $filename;
		}
		
		function readXML($xml, $tags = "response") {
			$data = file_get_contents($xml);
			$xmlData = simplexml_load_string($data);
			return $xmlData;  // the single top-level element
		}
		
		function getOffices(){
			$filename = "econt.offices.xml";
			$filename_response = "econt.offices.response.xml";
			
			// RETRIEVE INFORMATION ABOUT CURRENT OFFICES IN AN XML FILE
			$this->createRequest("offices", $filename, $filename_response);
			
			// PARSE XML FILE WITH INFORMATION ABOUT CURRENT OFFICES AND PUT IT INTO AN ARRAY
			$data = $this->readXML($filename_response);
			
			$contentRaw = $data->offices->e;
			foreach($contentRaw as $k => $v){
				$content[] = get_object_vars($v);
			}
			
			return $content;
		}
		
		function getOfficeById($office_id){
			$filename = "econt.offices.xml";
			$filename_response = "econt.offices.response.xml";
			
			// RETRIEVE INFORMATION ABOUT CURRENT OFFICES IN AN XML FILE
			$this->createRequest("offices", $filename, $filename_response);
			
			// PARSE XML FILE WITH INFORMATION ABOUT CURRENT OFFICES AND PUT IT INTO AN ARRAY
			$data = $this->readXML($filename_response);
			
			$contentRaw = $data->offices->e;
			foreach($contentRaw as $k => $v){
				if ( $v->id == $office_id ){
					$result = get_object_vars($v);
				}
			}
			
			return $result;
		}
		
		function getCities(){
			$filename = "econt.cities.xml";
			$filename_response = "econt.cities.response.xml";
			
			// RETRIEVE INFORMATION ABOUT CURRENT CITIES IN AN XML FILE
			$this->createRequest("cities", $filename, $filename_response);
			
			// PARSE XML FILE WITH INFORMATION ABOUT CURRENT CITIES AND PUT IT INTO AN ARRAY
			$data = $this->readXML($filename_response);
			
			$contentRaw = $data->cities->e;
			foreach($contentRaw as $k => $v){
				$content[] = get_object_vars($v);
			}
			
			return $content;
		}
		
		
		function getNeighbourhoods(){
			$filename = "econt.neighbourhoods.xml";
			$filename_response = "econt.neighbourhoods.response.xml";
			
			// RETRIEVE INFORMATION ABOUT CURRENT NEIGHBOURHOODS AND OFFICES IN AN XML FILE
			$this->createRequest("neighbourhoods", $filename, $filename_response);
			
			// PARSE XML FILE WITH INFORMATION ABOUT CURRENT NEIGHBOURHOODS AND PUT IT INTO AN ARRAY
			$data = $this->readXML($filename_response);
			
			$contentRaw = $data->cities_quarters->e;
			foreach($contentRaw as $k => $v){
				$content[] = get_object_vars($v);
			}
			
			return $content;
		}
		
		
		function getRegionsOld(){
			$filename = "econt.regions.xml";
			$filename_response = "econt.regions.response.xml";
			
			// RETRIEVE INFORMATION ABOUT CURRENT NEIGHBOURHOODS AND OFFICES IN AN XML FILE
			$this->createRequest("regions", $filename, $filename_response);
			
			// PARSE XML FILE WITH INFORMATION ABOUT CURRENT CITIES AND OFFICES AND PUT IT INTO AN ARRAY
			$data = $this->readXML($filename_response);
			
			$contentRaw = $data->cities_regions->e;

			foreach($contentRaw as $k => $v){
				$content[] = get_object_vars($v);
			}
			
			return $content;
		}
		
		function getRegions(){
			$cities = $this->getCities();
			
			$regions_tmp = array();
			foreach($cities as $k => $v){
				if ( $v["id_country"] == 1033 ){ // 1033 - country_id of Econt which stands for Bulgaria
					if ( $v["region"] ){
						$regions_tmp[] = $v["region"];
					}
				}
			}
			
			$regions = array_unique($regions_tmp);
			sort($regions);
			return $regions;
		}
        
        function getRegionsMoreInfo(){
			$cities = $this->getCities();
			
			$regions_tmp = [];
            
			foreach($cities as $k => $v){
				if ( $v["id_country"] == 1033 ){ // 1033 - country_id of Econt which stands for Bulgaria
					if ( $v["region"] ){
						$regions_tmp[$v["region"]]["region_bg"] = $v["region"];
                        $regions_tmp[$v["region"]]["region_en"] = $v["region_en"];
					}
				}
			}
			sort($regions_tmp);
			$regions = array_values($regions_tmp);
            
			return $regions;
		}
		
		function getStreets(){
			$filename = "econt.streets.xml";
			$filename_response = "econt.streets.response.xml";
			
			// RETRIEVE INFORMATION ABOUT CURRENT NEIGHBOURHOODS AND OFFICES IN AN XML FILE
			$this->createRequest("streets", $filename, $filename_response);
			
			// PARSE XML FILE WITH INFORMATION ABOUT CURRENT CITIES AND OFFICES AND PUT IT INTO AN ARRAY
			$data = $this->readXML($filename_response);
			
			$contentRaw = $data->cities_street->e;
			foreach($contentRaw as $k => $v){
				$content[] = get_object_vars($v);
			}
			
			return $content;
		}
		
		function createShippingRequest($cart_id, $receiver, $tariffSubCodeSecond, $amount, $weight, $cms = 0, $order_description = "", $free_delivery = 0, $office_code = "8604", $payment_type = 1){
			
			$totalCartSum = $amount;
			
			// ИНФОРМАЦИЯ ЗА ЕКОНТ ПРОФИЛ НА СОБСТВЕНИКА НА МАГАЗИНА
			
			$econtUser["Username"] = $this->username;
			$econtUser["Password"] = $this->password;
			

			// ИНФОРМАЦИЯ ЗА ПОДАТЕЛ (СОБСТВЕНИК НА МАГАЗИНА)
			
			if ( $office_code == "8020" ){
				$sender["City"] = "Бургас";
				$sender["PostCode"] = "8020";
				$sender["NamePerson"] = "Афиа фармаси";
				$sender["Quarter"] = "ул. Оборище";
				$sender["Street"] = "ул. Оборище";
				$sender["Number"] = "57";
			}elseif ( $office_code == "8012" ){
				$sender["City"] = "Бургас";
				$sender["PostCode"] = "8012";
				$sender["NamePerson"] = "Афиа фармаси";
				$sender["Quarter"] = "ж.к. Изгрев";
				$sender["Street"] = "ж.к. Изгрев";
				$sender["Number"] = "бл.186";
			}else{
				$sender["City"] = "Ямбол";
				$sender["PostCode"] = "8600";
				$sender["NamePerson"] = "Румяна Димитрова";
				$sender["Quarter"] = "гара гара Ямбол";
				$sender["Street"] = "ул. Искър";
				$sender["Number"] = "1А";
			}
			$sender["OfficeCode"] = $office_code;
			$sender["Name"] = "АФИА ФАРМАСИ ЕООД";
			/*
			$sender["NamePerson"] = "Румяна Димитрова";
			$sender["Quarter"] = "гара гара Ямбол";
			$sender["Street"] = "ул. Искър";
			$sender["Number"] = "1А";
			*/
			$sender["deliveryBlock"] = "";
			$sender["deliveryEntrance"] = "";
			$sender["Floor"] = "";
			$sender["Apartment"] = "";
			$sender["Other"] = "";
			$sender["Phone"] = "0889717880";
			
			$senderCity = $sender["City"];  //населеното място;
			$senderPostCode = $sender["PostCode"]; //пощенски код на населеното място;
			$senderOfficeCode = $sender["OfficeCode"]; //код на офиса, който ще вземе пратката. Не e задължителен параметър, ако същия не бъде посочен, се взема офиса по подразбиране;
			$senderName = $sender["Name"]; //фирма подател;
			$senderNamePerson = $sender["NamePerson"]; //име на подателя;
			$senderQuarter = $sender["Quarter"]; //квартал;
			$senderStreet = $sender["Street"]; //улица;
			$senderStreetNumber = $sender["Number"]; //номер на улицата;
			$senderStreetBlock = $sender["Block"]; //блок;
			$senderStreetEntrance = $sender["Entrance"]; //вход;
			$senderStreetFloor = $sender["Floor"]; //етаж;
			$senderStreetApartment = $sender["Apartment"]; //апартамент;
			$senderStreetOther = $sender["Other"]; //друга информация;
			$senderPhonePhone = $sender["Phone"]; //телефон на подателя;

			// ИНФОРМАЦИЯ ЗА ПОЛУЧАТЕЛ
			$receiverCity = ($receiver["deliveryCityBG"]); //населеното място;
			$receiverPostCode = ($receiver["deliveryCityPostCode"]); //пощенски код на населеното място
			$receiverOfficeCode = ($receiver["deliveryOfficeSelected"]); // код на офиса, който ще разнесе пратката. Не задължителен параметър, ако същия не бъде посочен, се взема офисът по подразбиране;
			$receiverName = ($receiver["clientCompany"]); //фирма получател;
			$receiverNamePerson = ($receiver["clientName"]); //име на получателя;
			$receiverEmail = ($receiver["clientMail"]); // email на получателя
			$receiverQuarter = ($receiver["deliveryQuarter"]); //квартал;
			$receiverStreet = ($receiver["deliveryStreet"]); //улица;
			$receiverStreetNumber = ($receiver["deliveryStreetNum"]); //номер на улицата;
			$receiverStreetBlock = ($receiver["deliveryBlock"]); //блок;
			$receiverStreetEntrance = ($receiver["deliveryEntrance"]);  //вход;
			$receiverStreetFloor = ($receiver["deliveryFloor"]); //етаж;
			$receiverStreetApartment = ($receiver["deliveryApartment"]); //апартамент;
			$receiverStreetOther = ($receiver["streetOther"]); //друга информация;
			$receiverPhoneNumber = ($receiver["clientPhone"]); // телефон на получателя;
			$receiverSmsNo = ($receiver["clientPhone"]);  //телефонен номер на подателя, при желание от негова страна да бъде уведомен с SMS за доставка на пратката му;
			
			
			// ИНФОРМАЦИЯ ЗА ДОСТАВКА
			// $econtShipment = selectOne("EcontShipment","1"); // връзка на базата данни за начин на доставка
			// $shipmentType = $econtShipment["ShipmentType"]; //тип на пратката. Възможни стойности: PACK - колет, DOCUMENT - документи, DOCUMENTPACK - палет + документи
			$shipmentType = "PACK"; //тип на пратката. Възможни стойности: PACK - колет, DOCUMENT - документи, DOCUMENTPACK - палет + документи
			
			if( $order_description ){
				$shipmentDescription = $order_description;
			}else{
				$shipmentDescription = "стандартна пратка"; // описание на пратката
			}
			
			$shipmentWeight = $weight;
			
			$packCount = "1"; // брой пакети

			$econtShipment["TariffSubCodeFirst"] = "OFFICE";
			$econtShipment["TariffSubCodeSecond"] = $tariffSubCodeSecond;
			$econtShipment["PayAfterAccept"] = "1";
			$econtShipment["InvoiceBeforePay"] = "0";
			$econtShipment["PayAfterTest"] = "0";
			$econtShipment["InstructionReturns"] = "";
			
			$tariffSubCode = $econtShipment["TariffSubCodeFirst"]."_".$econtShipment["TariffSubCodeSecond"]; // образуване на тарифен суб код - от къде до къде да пътува пратката
			
			if($_SESSION["tariffSubCodeSecond"] == "DOOR"){
				$receiverOfficeCode = ""; // Ако сме избрали доставка до врата, зануляваме кода на офиса получател ако преди това има избран такъв
			}
			
			$payAfterAccept = $econtShipment["PayAfterAccept"]; // да се позволи преглед на пратката преди премане и плащане
			$invoiceBeforePay = $econtShipment["InvoiceBeforePay"]; // предай ф-ра преди плащане на наложен платеж
			$payAfterTest = $econtShipment["PayAfterTest"]; // да се позволи тестване на пратката преди плащане
			$instructionReturns = $econtShipment["InstructionReturns"]; // в случай на отказ от пратката, кой да плати транспорта за връщане

			// ИНФОРМАЦИЯ ЗА ПЛАЩАНЕ
			// $econtPayment = selectOne("EcontPayment","1"); 
			
			$econtPayment["Method"] = "CASH";
			$econtPayment["Side"] = "SENDER";
			$econtPayment["ReceiverShare"] = "5.05";
			$econtPayment["KeyWord"] = "ЯМ0022";

			$paymentMethod = $econtPayment["Method"];
			$paymentShareMethod = $econtPayment["ShareMethod"];
			$paymentShareSum = $econtPayment["ReceiverShare"];
			$paymentSharePercent = $econtPayment["SharePercent"];
			$paymentKeyWord = $econtPayment["KeyWord"];
			$paymentCDNumber = "ЯМ5001C1";
			
			// Ако страната на платеца е "Подател" проверяваме за безплатна доставка
			if ( $totalCartSum >= 100 || $free_delivery == 1 ){
				if ( $weight < 1 )
					$paymentSide = "SENDER";
					$paymentShareSum = 0;
					$paymentSharePercent = 0;
					$paymentMethod = "CREDIT";
				
			}else{
				
				$paymentSide = "RECEIVER";
				$paymentMethod = "CASH";
			}
			
			
			
			
			/*
			if($econtPayment["Side"] == "SENDER")
			{
				if($econtPayment["FreeDelivery"] == "Yes")
				{
					// Проверяваме дали сумата на количката отговаря на условието за безплатна доставка
					if($totalCartSum >= $econtPayment["FreeDeliverySum"])
					{
						// Проверяме какво е допълнителното условие за безплатна доставка
						if($_SESSION["tariffSubCodeSecond"] ==  $econtPayment["TermsOfFreeDelivery"])
						{
							// Подателя плаща куриерската услуга
							$paymentSide = "SENDER";
							$paymentShareSum = 0;
							$paymentSharePercent = 0;
						}
						else
						{
							if($paymentShareSum > 0)
								// Подателя плаща разликата над споделената сума
								$paymentSide = "SENDER";
							else
								// Получателя плаща куриерската услуга
								$paymentSide = "RECEIVER";
						}
					}
					else
					{
						if($paymentShareSum > 0)
							// Подателя плаща разликата над споделената сума
							$paymentSide = "SENDER";
						else
							// Получателя плаща куриерската услуга
							$paymentSide = "RECEIVER";
					}
				}
				else
				{
					// Получателя поема разходите по доставката
					$paymentSide = "RECEIVER";
				}
			}
			else
			{
				// Получателя поема разходите по доставката
				$paymentSide = $econtPayment["Side"]; 
			}
			*/
			
			// ДОПЪЛНИТЕЛНА ИНФОРМАЦИЯ ЗА ПРАТКАТА
			if($econtShipment["DeclaratedValue"] == "Yes"){
				$ocValue = $totalCartSum;
			}else{
				$ocValue = 0.00;
			}
			// Проверяваме в POST-a какъв тип плащане сме избрали. Ако е НАЛОЖЕН ПЛАТЕЖ, тогава го начисляваме
			
			if($payment_type == 2){
				$cdValue = 0;
			}else{
				$cdValue = $totalCartSum;
			}
			
			/*
			if($_SESSION["paymentType"] == "CASH")
				$cdValue = $totalCartSum;
			else
				$cdValue = 0.00;
			*/
			$moneyType = $econtPayment["MoneyType"];

			
			$xml = new DomDocument(); 
			$xml->formatOutput = true;
			$xml->preserveWhiteSpace = true;
			$parcels = $xml->appendChild($xml->createElement("parcels"));

			$client_software = $parcels->appendChild($xml->createElement("client_software")); // името на посредника за начисляване на комисионна;
			$client_software->appendChild($xml->createTextNode("Afya-Pharmacy.bg"));

			$validateValue = 0;
			
			
			if ($cms == 0){
				$validateValue = 0; // Валидация
			}else{
				$validateValue = 0;
				
				$request_type = $parcels->appendChild($xml->createElement("request_type"));
				$request_type->appendChild($xml->createTextNode("shipping"));
			}
			
			// Системна част на XML документа
			$system = $parcels->appendChild($xml->createElement("system")); 
			
			$validate = $system->appendChild($xml->createElement("validate"));
			$validate->appendChild($xml->createTextNode($validateValue));

			$response_type = $system->appendChild($xml->createElement("response_type"));
			$response_type->appendChild($xml->createTextNode("XML"));

			$only_calculate = $system->appendChild($xml->createElement("only_calculate"));
			if ( $cms == 1 ){
				$only_calculate_value = 0;
			}else{
				$only_calculate_value = 1;
			}
			$only_calculate->appendChild($xml->createTextNode($only_calculate_value));

			// Добавяне на данни за клиентски профил
			$client = $parcels->appendChild($xml->createElement("client")); 

			$username = $client->appendChild($xml->createElement("username")); // потребителско име за вход в е-еконт
			$username->appendChild($xml->createTextNode($this->username));

			$password = $client->appendChild($xml->createElement("password")); // парола в е-еконт
			$password->appendChild($xml->createTextNode($this->password)); 

			// Добавяне на информация за товарителница
			$loadings = $parcels->appendChild($xml->createElement("loadings")); 

			$row = $loadings->appendChild($xml->createElement("row")); 

			// Добавяне на информация за подател на пратката
			$sender = $row->appendChild($xml->createElement("sender")); // подател

			$city = $sender->appendChild($xml->createElement("city")); // населено место от където пътува пратката.
			$city->appendChild($xml->createTextNode($senderCity));

			$post_code = $sender->appendChild($xml->createElement("post_code")); // пощенски код на населеното място
			$post_code->appendChild($xml->createTextNode($senderPostCode));

			$office_code = $sender->appendChild($xml->createElement("office_code")); // код на офиса, който ще вземе пратката.
			$office_code->appendChild($xml->createTextNode($senderOfficeCode));

			$name = $sender->appendChild($xml->createElement("name")); // фирма подател
			$name->appendChild($xml->createTextNode($senderName));

			$name_person = $sender->appendChild($xml->createElement("name_person")); // име на подател
			$name_person->appendChild($xml->createTextNode($senderNamePerson));

			$quarter = $sender->appendChild($xml->createElement("quarter")); // квартал
			$quarter->appendChild($xml->createTextNode($senderQuarter));

			$street = $sender->appendChild($xml->createElement("street")); // улица
			$street->appendChild($xml->createTextNode($senderStreet));

			$street_num = $sender->appendChild($xml->createElement("street_num")); // номер
			$street_num->appendChild($xml->createTextNode($senderStreetNumber));

			$street_bl = $sender->appendChild($xml->createElement("street_bl")); // блок
			$street_bl->appendChild($xml->createTextNode($senderStreetBlock));

			$street_vh = $sender->appendChild($xml->createElement("street_vh")); // вход
			$street_vh->appendChild($xml->createTextNode($senderStreetEntrance));

			$street_et = $sender->appendChild($xml->createElement("street_et")); // етаж
			$street_et->appendChild($xml->createTextNode($senderStreetFloor));

			$street_ap = $sender->appendChild($xml->createElement("street_ap")); // апартамент
			$street_ap->appendChild($xml->createTextNode($senderStreetApartment));

			$street_other = $sender->appendChild($xml->createElement("street_other")); // друга информация
			$street_other->appendChild($xml->createTextNode($senderStreetOther));

			$phone_num = $sender->appendChild($xml->createElement("phone_num")); // телефон на подателя
			$phone_num->appendChild($xml->createTextNode($senderPhonePhone));

			// Добавяне на информация за получател на пратката
			$receiver = $row->appendChild($xml->createElement("receiver"));

			$city = $receiver->appendChild($xml->createElement("city")); // населено место до където пътува пратката.
			$city->appendChild($xml->createTextNode($receiverCity));

			$post_code = $receiver->appendChild($xml->createElement("post_code")); // пощенски код на населеното място
			$post_code->appendChild($xml->createTextNode($receiverPostCode));

			$office_code = $receiver->appendChild($xml->createElement("office_code")); // код на офиса, който ще разнесе пратката
			$office_code->appendChild($xml->createTextNode($receiverOfficeCode));

			$name = $receiver->appendChild($xml->createElement("name")); // фирма получател
			$name->appendChild($xml->createTextNode($receiverName));

			$name_person = $receiver->appendChild($xml->createElement("name_person")); // име на получателя
			$name_person->appendChild($xml->createTextNode($receiverNamePerson));

			$receiver_email = $receiver->appendChild($xml->createElement("receiver_email")); // e-mail на получателя
			$receiver_email->appendChild($xml->createTextNode($receiverEmail));

			$quarter = $receiver->appendChild($xml->createElement("quarter")); // квартал на адреса на получателя
			$quarter->appendChild($xml->createTextNode($receiverQuarter));

			$street = $receiver->appendChild($xml->createElement("street")); // улица
			$street->appendChild($xml->createTextNode($receiverStreet));

			$street_num = $receiver->appendChild($xml->createElement("street_num")); // номер
			$street_num->appendChild($xml->createTextNode($receiverStreetNumber));

			$street_bl = $receiver->appendChild($xml->createElement("street_bl")); // блок
			$street_bl->appendChild($xml->createTextNode($receiverStreetBlock));

			$street_vh = $receiver->appendChild($xml->createElement("street_vh")); // вход
			$street_vh->appendChild($xml->createTextNode($receiverStreetEntrance));

			$street_et = $receiver->appendChild($xml->createElement("street_et")); // етаж
			$street_et->appendChild($xml->createTextNode($receiverStreetFloor));

			$street_ap = $receiver->appendChild($xml->createElement("street_ap")); // апартамент
			$street_ap->appendChild($xml->createTextNode($receiverStreetApartment));

			$street_other = $receiver->appendChild($xml->createElement("street_other")); // друга информация
			$street_other->appendChild($xml->createTextNode($receiverStreetOther));

			$phone_num = $receiver->appendChild($xml->createElement("phone_num")); // телефон на получателя
			$phone_num->appendChild($xml->createTextNode($receiverPhoneNumber));

			$sms_no = $receiver->appendChild($xml->createElement("sms_no")); // SMS за доставка на пратката
			$sms_no->appendChild($xml->createTextNode($receiverSmsNo));

			// Добавяне на информация за доставка
			$shipment = $row->appendChild($xml->createElement("shipment"));

			$shipment_type = $shipment->appendChild($xml->createElement("shipment_type")); // тип на пратката
			$shipment_type->appendChild($xml->createTextNode($shipmentType));

			$description = $shipment->appendChild($xml->createElement("description")); // описание на пратката
			$description->appendChild($xml->createTextNode($shipmentDescription));

			$pack_count = $shipment->appendChild($xml->createElement("pack_count")); // брой пакети
			$pack_count->appendChild($xml->createTextNode($packCount));

			$weight = $shipment->appendChild($xml->createElement("weight")); // тегло
			$weight->appendChild($xml->createTextNode($shipmentWeight));
			
						
			$tariff_sub_code = $shipment->appendChild($xml->createElement("tariff_sub_code"));
			$tariff_sub_code->appendChild($xml->createTextNode($tariffSubCode));
			
			$pay_after_accept = $shipment->appendChild($xml->createElement("pay_after_accept"));
			$pay_after_accept->appendChild($xml->createTextNode($payAfterAccept));

			$invoice_before_pay_CD = $shipment->appendChild($xml->createElement("invoice_before_pay_CD")); 
			$invoice_before_pay_CD->appendChild($xml->createTextNode($invoiceBeforePay));

			$pay_after_test = $shipment->appendChild($xml->createElement("pay_after_test"));
			$pay_after_test->appendChild($xml->createTextNode($payAfterTest));

			$instruction_returns = $shipment->appendChild($xml->createElement("instruction_returns"));
			$instruction_returns->appendChild($xml->createTextNode($instructionReturns));

			// Добавяне на информация за плащане
			$payment = $row->appendChild($xml->createElement("payment"));

			$side = $payment->appendChild($xml->createElement("side"));
			$side->appendChild($xml->createTextNode($paymentSide));

			$method = $payment->appendChild($xml->createElement("method"));
			$method->appendChild($xml->createTextNode($paymentMethod));

			$receiver_share_sum = $payment->appendChild($xml->createElement("receiver_share_sum")); 
			$receiver_share_sum->appendChild($xml->createTextNode($paymentShareSum));

			$share_percent = $payment->appendChild($xml->createElement("share_percent")); 
			$share_percent->appendChild($xml->createTextNode($paymentSharePercent));

			$key_word = $payment->appendChild($xml->createElement("key_word")); 
			$key_word->appendChild($xml->createTextNode($paymentKeyWord));

			// Допълнителни услуги - services
			$services = $row->appendChild($xml->createElement("services")); 

			$oc = $services->appendChild($xml->createElement("oc"));
			$oc->appendChild($xml->createTextNode($ocValue));

			$cd = $services->appendChild($xml->createElement("cd"));
			$cd->appendChild($xml->createTextNode($cdValue)); 

			$type = $cd->appendChild($xml->createAttribute("type"));
			$type->appendChild($xml->createTextNode($moneyType));

			$cd_agreement_num = $services->appendChild($xml->createElement("cd_agreement_num"));
			$cd_agreement_num->appendChild($xml->createTextNode($paymentCDNumber));
			
			/*
			$cd_pay_options = $services->appendChild($xml->createElement("cd_pay_options"));

			$method = $cd_pay_options->appendChild($xml->createElement("method"));
			$method->appendChild($xml->createTextNode("bank"));
			
			$name = $cd_pay_options->appendChild($xml->createElement("name"));
			$name->appendChild($xml->createTextNode("АФИА ФАРМАСИ ЕООД"));
			
			$phone = $cd_pay_options->appendChild($xml->createElement("phone"));
			$phone->appendChild($xml->createTextNode("0889717880"));
			
			$email = $cd_pay_options->appendChild($xml->createElement("email"));
			$email->appendChild($xml->createTextNode("sales@afya-pharmacy.bg"));
			*/
			
			// Запазване на XML 
			
			$order_filename = "xml/order.".$cart_id.".xml";
			$order_filename_response = "xml/order.".$cart_id.".response.xml";
            if(file_exists($order_filename)){
                unlink($order_filename_response);
            }
			$xml->save($order_filename);
			
			$this->createRequest("shipping_request", $order_filename, $order_filename_response, $cms);
		
			//if ( $_SERVER["REMOTE_ADDR"] == "84.201.192.58" ){
				$content_tmp = $this->readXML($order_filename_response);
				
				if ( $content_tmp->result->e->error ){
					((string)$content_tmp->result->e->error);
				}
			//}
		
			return $order_filename_response;
		}
		
		function deleteShipping($delivery_tracking_number, $cart_id, $cms = 0){
			$filename = "econt.tracking.".$cart_id.".".$delivery_tracking_number.".xml";
			$filename_response = "econt.tracking.".$cart_id.".".$delivery_tracking_number.".response.xml";
			
			// RETRIEVE INFORMATION ABOUT CURRENT CITIES IN AN XML FILE
			$this->createRequest("cancel_shipments", $filename, $filename_response, $cms, $delivery_tracking_number);
			
		}
		
		
	}
	
?>