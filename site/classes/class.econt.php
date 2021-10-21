<?php

/**
 * Class for easy implementation of Econt's api
 *
 * @author Kaloyan Kalchev (SmartWeb)
 */
class Econt {

    private $username = "";
    private $password = "";
    private $sndrSiteId = 41; // Default siteId of the sender (in this case it is 41 - София)
    private $sndrSiteName = "София"; // Default siteName of the sender (in this case it is София)
    private $bringToOfficeId = 1001; // Default Id of the office that sender should bring the parcel(s). (93 - "ЯМБОЛ - СКЛАД")
    private $sndrContactName = "Тест Тестов"; // Default contact name of the sender
    private $sndrPhoneNumber = "0888000000"; // Default phone number of the sender
    public $timeZone;
    public $eps;
    
    public function __construct() {
        global $credentialsNumberEcont;

        $this->setCredentials($credentialsNumberEcont);
    }
    
    public function updateCities() {
        global $db;
        global $cities_econt_table;


        $url = "http://ee.econt.com/services/Nomenclatures/NomenclaturesService.getCities.json";
        $cities = $this->send($url, array(), 0);
        $citiesArr = json_decode($cities, true);
        $citiesArr = $citiesArr["cities"];
        
        $query = 'INSERT INTO '.$cities_econt_table.' (`id`, `country_code`, `postcode`, `phone_code`, `name_bg`, `name_en`, `region_bg`, `region_en`, `expressCityDeliveries`, `active`, `edate`) VALUES ';
        $query_parts = array();
        for($i = 0; $i < count($citiesArr); $i++){
            $expressCityDeliveries = $citiesArr[$i]["expressCityDeliveries"] ? 1 : 0;
            if($citiesArr[$i]["country"]["code2"] == "BG"){
                $query_parts[] = "('" . $citiesArr[$i]["id"] . "', '" . $citiesArr[$i]["country"]["code2"] . "', '" . $citiesArr[$i]["postCode"] . "', '" . $citiesArr[$i]["phoneCode"] . "', '" . $citiesArr[$i]["name"] . "', '" . $citiesArr[$i]["nameEn"] . "', '" . $citiesArr[$i]["regionName"] . "', '" . $citiesArr[$i]["regionNameEn"] . "', '" . $expressCityDeliveries . "', '1', '0' )";
            }
        }
        $query .= implode(',', $query_parts);
        
        //$res = $db->query($query); safeCheck($res);
        
        return $res;
        
    }
    
    /**
     * 
     * @global type $econtUsernameTest
     * @global type $econtPasswordTest
     * @global type $econtUsernameProduction
     * @global type $econtPasswordProduction
     * @param type $credentialsNumberEcont
     */
    public function setCredentials($credentialsNumberEcont) {
        global $econtUsernameTest;     // #1
        global $econtPasswordTest;     // #1
        
        global $econtUsernameProduction; // #2
        global $econtPasswordProduction; // #2
        
        if($credentialsNumberEcont == 1){
            $this->username = $econtUsernameTest;
            $this->password = $econtPasswordTest;
        }else if($credentialsNumberEcont == 2){
            $this->username = $econtUsernameProduction;
            $this->password = $econtPasswordProduction;
        }else{
            $this->username = $econtUsernameTest;
            $this->password = $econtPasswordTest;
        }
    }
    
    /**
     * Get sender client id (authenticated client) 
     * 
     * @return int
     */
    public function getClientId() {
        $clientId = 0;//$this->eps->getResultLogin()->getClientId();
        
        return $clientId;
    }
    

    public function getSiteId($siteName, $regionName, $language = "BG", $countryId = 100) {
        global $db;
        global $cities_econt_table;
        
        $regionName = $regionName == "СОФИЯ (СТОЛИЦА)" ? "София" : $regionName;
        $lang_suff = strtolower($language) === "bg" ? "bg" : "en";
        $row = $db->getRow("SELECT
                                *
                            FROM
                                {$cities_econt_table}
                            WHERE
                                name_{$lang_suff} = '{$siteName}'
                            AND region_{$lang_suff} = '{$regionName}'
                            AND active = 1
                            AND edate = 0");
        
        return isset($row["id"]) ? (int)$row["id"] : null;
    }
    
    public function getSiteById(int $siteId) {
        global $db;
        global $cities_econt_table;
        
        $row = $db->getRow("SELECT
                                *
                            FROM
                                {$cities_econt_table}
                            WHERE
                                id = {$siteId}
                            AND active = 1
                            AND edate = 0");
        
        return $row;
    }
    
    private function send ($url, $data, $useCredentials = 1){
        $username = $this->username;
        $password = $this->password;
        
        $jsonData = json_encode($data);
        
//        $ch = curl_init();
//        curl_setopt($ch, CURLOPT_URL, $url);
//        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
//        curl_setopt($ch, CURLOPT_POSTFIELDS, $jsonData);
//        curl_setopt($ch, CURLOPT_POST, 1);
//        curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/json'));
//        //curl_setopt($ch, CURLOPT_HEADER, 1);
//        //curl_setopt($ch, CURLOPT_HTTPAUTH, CURLAUTH_BASIC);
//        //curl_setopt($ch, CURLOPT_USERPWD, $username . ":" . $password);
//        //curl_setopt($ch, CURLOPT_TIMEOUT, 30);
//        $result = curl_exec($ch);
//        curl_close($ch);
        
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        if($data){
            curl_setopt($ch, CURLOPT_POSTFIELDS, $jsonData);
            curl_setopt($ch, CURLOPT_POST, 1);
        }
        $headers = array();
        $headers[] = 'Content-Type: application/json';
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        if($useCredentials){
            curl_setopt($ch, CURLOPT_USERPWD, $username . ":" . $password);
        }
        
        $result = curl_exec($ch);
        if (curl_errno($ch)) {
            echo 'Error:' . curl_error($ch);
        }
        curl_close($ch);
        
        return $result;
    }
    
    public function getOfficesList($siteId, $countryCode = "BGR"){
        global $lng;
        global $econtUseProductionURLs;

        $url_test = "http://demo.econt.com/ee/services/Nomenclatures/NomenclaturesService.getOffices.json";
        $url_production = "http://ee.econt.com/services/Nomenclatures/NomenclaturesService.getOffices.json";
        
        $url = $econtUseProductionURLs ? $url_production : $url_test;
        
        $data = array(
            "countryCode"   => $countryCode,
            "cityID"        => $siteId
        );
        $resultListOffices = $this->send($url, $data);
        $resultListOffices = json_decode($resultListOffices, true)["offices"];

        $resultListOfficesArr = array();
        foreach ($resultListOffices as $k => $v) {
            $id = $v["id"];
            $code = $v["code"];
            $name = $lng == "bg" ? $v["name"] : $v["nameEn"];
            $name_bg = $v["name"];
            $name_en = $v["nameEn"];
            $isMPS = $v["isMPS"];
            $isAPS = $v["isAPS"];
            $address = $v["address"]["fullAddress"];
            $latitude = $v["address"]["location"]["latitude"];
            $longitude = $v["address"]["location"]["longitude"];
            $confidence = $v["address"]["location"]["confidence"];
            $info = $v["info"];
            $normalBusinessHoursFrom = $v["normalBusinessHoursFrom"];
            $normalBusinessHoursTo = $v["normalBusinessHoursTo"];
            $halfDayBusinessHoursFrom = $v["halfDayBusinessHoursFrom"];
            $halfDayBusinessHoursTo = $v["halfDayBusinessHoursTo"];
            
            $resultListOfficesArr[$k]["id"] = $id;
            $resultListOfficesArr[$k]["code"] = $code;
            $resultListOfficesArr[$k]["name"] = $name;
            $resultListOfficesArr[$k]["name_bg"] = $name_bg;
            $resultListOfficesArr[$k]["name_en"] = $name_en;
            $resultListOfficesArr[$k]["isMPS"] = $isMPS;
            $resultListOfficesArr[$k]["isAPS"] = $isAPS;
            $resultListOfficesArr[$k]["address"] = $address;
            $resultListOfficesArr[$k]["location"]["latitude"] = $latitude;
            $resultListOfficesArr[$k]["location"]["longitude"] = $longitude;
            $resultListOfficesArr[$k]["location"]["confidence"] = $confidence;
            $resultListOfficesArr[$k]["info"] = $info;
            $resultListOfficesArr[$k]["normalBusinessHoursFrom"] = $normalBusinessHoursFrom;
            $resultListOfficesArr[$k]["normalBusinessHoursTo"] = $normalBusinessHoursTo;
            $resultListOfficesArr[$k]["halfDayBusinessHoursFrom"] = $halfDayBusinessHoursFrom;
            $resultListOfficesArr[$k]["halfDayBusinessHoursTo"] = $halfDayBusinessHoursTo;
        }
        return $resultListOfficesArr;
    }
    

    public function getCalculatedPrice($amountCODBase, $weightDeclared, $receiverSiteId, $receiverTakeFromOfficeId = null, $senderBringToOfficeId = null, $senderBringToOffice = true) {
        global $econtUseProductionURLs;
        global $senderAddresses;
        
        $url_test = "http://demo.econt.com/ee/services/Shipments/LabelService.createLabel.json";
        $url_production = "http://ee.econt.com/services/Shipments/LabelService.createLabel.json";
        $url = $econtUseProductionURLs ? $url_production : $url_test;
        
        $request = array(
            "label" => array(
                "senderClient" => array(
                    "name" => $this->sndrContactName,
                    "phones" => array($this->sndrPhoneNumber)
                ),
                "receiverClient" => array(
                    "name" => $this->sndrContactName,
                    "phones" => array($this->sndrPhoneNumber)
                ),
                "packCount" => 1,
                "shipmentType" => "PACK",
                "weight" => $weightDeclared,
                "shipmentDescription" => "Стандартна пратка",
                "services" => array(
                    //"moneyTransferAmount" => $amountCODBase,
                    "cdAmount" => $amountCODBase,
                    "cdCurrency" => "BGN"
                ),
                "payAfterAccept" => "1",
                //"paymentSenderMethod" => "credit",
                "paymentReceiverMethod" => "cash",
                "paymentReceiverAmountIsPercent" => 100
            ),
            "mode" => "calculate"
        );
        
        //$request["label"]["services"]["paymentSide"] = "receiver";
        
        $site = $this->getSiteById((int)$receiverSiteId);
        //if $senderBringToOffice is true we use the given or default Id of the office that sender should bring the parcel(s). else if $senderBringToOffice is false Courier will visit sender address
        if($senderBringToOffice){
            $senderBringToOfficeId = $senderBringToOfficeId ? $senderBringToOfficeId : $this->bringToOfficeId;
            $request["label"]["senderOfficeCode"] = "{$senderBringToOfficeId}";
        }else{
            //$senderBringToOfficeId = null;
            $request["label"]["senderAddress"] = $senderAddresses[1];
        }
        
        if($receiverTakeFromOfficeId){
            $senderBringToOfficeId = $senderBringToOfficeId ? $senderBringToOfficeId : $this->bringToOfficeId;
            $request["label"]["receiverOfficeCode"] = "{$receiverTakeFromOfficeId}";
        }else{
            $request["label"]["receiverAddress"] = array(
                                                    "city" => array(
                                                        "country" => array(
                                                            "code3" => "BGR"
                                                        ),
                                                        "name" => "{$site["name_bg"]}",
                                                        "postCode" => "{$site["postcode"]}"
                                                    ),
                                                    "fullAddress" => "Адрес в града 1"
                                                );
        }
        
        $resultCalculationResponse = $this->send($url, $request);
        $resultCalculation = json_decode($resultCalculationResponse, true)["label"];
        
        $result = array();
        if($resultCalculation){
            $deliveryAmount = $resultCalculation["totalPrice"];
            $result = array(
                "codBase" => $amountCODBase,
                "deliveryAmount" => $deliveryAmount
            );
            $result["services"] = array();
            if($resultCalculation["services"]){
                foreach ($resultCalculation["services"] as $k => $v) {
                    $result["services"][$k] = $v;
                }
            }
        }
        
        return $result;
    }
    

//    public function createBillOfLading($payerType, $amountCODBase, $contentDescription, $weightDeclared,  $receiverSiteId, $receiverAddressNote, $receiverName, $receiverCompanyName, $receiverPhone, $receiverTakeFromOfficeId = null, $deferredDeliveryWorkDays = 0, $optionsBeforePayment = true, $senderBringToOfficeId = null, $senderBringToOffice = true) {
//        
//        $contentDescriptionCleaned = htmlspecialchars(trim($contentDescription));
//        $receiverAddressNoteCleaned = htmlspecialchars(trim($receiverAddressNote));
//        $receiverNameCleaned = htmlspecialchars(trim($receiverName));
//        $receiverCompanyNameCleaned = htmlspecialchars(trim($receiverCompanyName));
//        $receiverPhoneCleaned = htmlspecialchars(trim($receiverPhone));
//        
//        //if $senderBringToOffice is true we use the given or default Id of the office that sender should bring the parcel(s). else if $senderBringToOffice is false Courier will visit sender address
//        if($senderBringToOffice){
//            $senderBringToOfficeId = $senderBringToOfficeId ? $senderBringToOfficeId : $this->bringToOfficeId;
//        }else{
//            $senderBringToOfficeId = null;
//        }
//        
//        // Init picking structure with data
//        $pickingData = new StdClass();
//        $pickingData->weightDeclared = $weightDeclared; // Decalred weight
//        $pickingData->bringToOfficeId = $senderBringToOfficeId; // Id of the office that sender should bring the parcel(s). Courier will visit sender address if this value is null
//        $pickingData->takeFromOfficeId = $receiverTakeFromOfficeId; // Id of the office that recipient should take the parcel(s) from. Courier will visit recipient address if this value is null
//        $pickingData->parcelsCount = 1; // Parcels count
//        $pickingData->documents = false; // Flag for documents
//        $pickingData->palletized = false; // Flag for pallets
//        $pickingData->fragile = false; // flag for fragile content
//        if($payerType === 0){
//            $pickingData->payerType = ParamCalculation::PAYER_TYPE_SENDER; // Determine the payer
//        }elseif($payerType === 1){
//            $pickingData->payerType = ParamCalculation::PAYER_TYPE_RECEIVER; // Determine the payer
//        }elseif($payerType === 2){
//            $pickingData->payerType = ParamCalculation::PAYER_TYPE_THIRD_PARTY; // Determine the payer
//        }
//
//        if($amountCODBase > 0.0){
//           //Наложен платеж
//            $pickingData->amountCODBase = (float)$amountCODBase; // Collecton on Delivery amount
//            //Пощенски паричен превод
//            //$pickingData->retMoneyTransferReqAmount = (float)$amountCODBase; // Return money-transfer request amount
//        }
//        $pickingData->backDocumentReq = false; // Back document request flag
//        $pickingData->backReceiptReq = false; // Back receipt request flag
//        $pickingData->contents = $contentDescriptionCleaned; // Content description
//        $pickingData->packing = 'PACKAGE'; // Type of packing
//        $pickingData->serviceTypeId = 505; // Service type 505 from Econt nomenclature (serviceTypeId obtained from "listServicesForSites")
//        $pickingData->takingDate = time(); // Taking date is today
//
//        // Sender is authenticated client. 
//        // Therefore we fill Bill Of Lading sender client data with authenticated client data
//        $senderClientId = $this->getClientId();
//        $sender = new ParamClientData();
//        $sender->setClientId($senderClientId);
//        $sender->setContactName($this->sndrContactName);
//        $senderPhoneNumber = new ParamPhoneNumber();
//        $senderPhoneNumber->setNumber($this->sndrPhoneNumber);
//        $sender->setPhones(array(0 => $senderPhoneNumber));
//
//        // In this example receiver address is "VARNA, kv. MLADOST 1, bl. 102, vh. 3, et. 4, ap. 5"
//        // Lookup for example receiver city of Sofia in Econt address nomenclature
//        // We are sure we have results therefore array is not verified for null or empty values.
////        $arrSites = $this->eps->listSitesEx('гр.', 'ВАРНА');
////        $receiverSiteId = $arrSites[0]->getId();
////        $receiverSiteId = 68134;
//        // Lookup example receiver address quarter "MLADOST 1" in city of Varna in Econt address nomenclature
//        // We are sure we have results therefore array is not verified for null or empty values.
//        // EPS supports similar methods for streets also
////        $arrQuarters = $this->eps->listQuarters('MLADOST 1', $receiverSiteId);
////        $receiverResultQuarter = $arrQuarters[0];
//
//        // Set receiver address fields
////        $receiverAddress = new ParamAddress();
////        $receiverAddress->setSiteId($receiverSiteId);
////        $receiverAddress->setQuarterId($receiverResultQuarter->getId());
////        $receiverAddress->setBlockNo('102');
////        $receiverAddress->setEntranceNo('3');
////        $receiverAddress->setFloorNo('4');
////        $receiverAddress->setApartmentNo('5');
//
//        // Because we cannot determine address fields from input address text (we cannot be sure if everything is correct when we structurally parse the input address)
//        // So we use method setAddressNote.
//        
//        // NOTE: Site name should not be placed in addressNote field, because site Id is passed separately and cannot be omitted
//        $receiverAddress = new ParamAddress();
//        $receiverAddress->setSiteId($receiverSiteId);
//        $receiverAddress->setAddressNote($receiverAddressNoteCleaned); //f.e. ('kv. MLADOST 1, bl. 102, vh. 3, et. 4, ap. 5')
//
//        // Set receiver client data
//        $receiver = new ParamClientData();
//        if(!$receiverTakeFromOfficeId){
//            $receiver->setAddress($receiverAddress);
//        }
//        $receiver->setPartnerName($receiverCompanyNameCleaned ? $receiverCompanyNameCleaned : $receiverNameCleaned);
//        $paramPhoneNumber = new ParamPhoneNumber();
//        $paramPhoneNumber->setNumber($receiverPhoneCleaned);
//        $receiver->setPhones(array(0 => $paramPhoneNumber));
//        if($receiverCompanyNameCleaned){
//            $receiver->setContactName($receiverNameCleaned);
//        }
//
//        $picking = new ParamPicking();
//        $picking->setServiceTypeId($pickingData->serviceTypeId);
//        $picking->setBackDocumentsRequest($pickingData->backDocumentReq);
//        if($deferredDeliveryWorkDays > 0){
//            $picking->setDeferredDeliveryWorkDays($deferredDeliveryWorkDays);
//        }
//        $picking->setBackReceiptRequest($pickingData->backReceiptReq);
//        $picking->setWillBringToOffice(!is_null($pickingData->bringToOfficeId));
//        if(!is_null($pickingData->bringToOfficeId)){
//            $picking->setWillBringToOfficeID($senderBringToOfficeId);
//        }
//        $picking->setOfficeToBeCalledId($pickingData->takeFromOfficeId);
//        $picking->setParcelsCount($pickingData->parcelsCount);
//        $picking->setWeightDeclared($pickingData->weightDeclared);
//        $picking->setContents($pickingData->contents);
//        $picking->setPacking($pickingData->packing);
//        $picking->setDocuments($pickingData->documents);
//        $picking->setPalletized($pickingData->palletized);
//        $picking->setFragile($pickingData->fragile);
//        $picking->setSender($sender);
//        $picking->setReceiver($receiver);
//        $picking->setPayerType($pickingData->payerType);
//        $picking->setTakingDate($pickingData->takingDate);
//        //Наложен платеж
//        $picking->setAmountCodBase($pickingData->amountCODBase);
//        //Пощенски паричен превод
//        //$picking->setRetMoneyTransferReqAmount($pickingData->retMoneyTransferReqAmount);
//        
//        /* Optional data */
//        // Set parcels size
////        $parcelDetails1 = new ParamParcelInfo();
////        $size = new Size();
////        $size->setWidth(5);
////        $size->setDepth(6);
////        $size->setHeight(7);
////        $parcelDetails1->setSeqNo(1);
////        $parcelDetails1->setParcelId(-1);
////        $parcelDetails1->setSize($size);
////        $parcelDetails1->setWeight(1);
////        $parcelsArray[] = $parcelDetails1;
////
////        $parcelDetails2 = new ParamParcelInfo(); // if there is more than one parcel
////        $size2 = new Size();
////        $size2->setWidth(11);
////        $size2->setDepth(12);
////        $size2->setHeight(13);
////        $parcelDetails2->setSeqNo(2);
////        $parcelDetails2->setParcelId(-1);
////        $parcelDetails2->setSize($size2);
////        $parcelDetails2->setWeight(2);
////        $parcelsArray[] = $parcelDetails2;
////
////        $parcelsCount = count($parcelsArray);
////        $picking->setParcels($parcelsArray);
////        $picking->setParcelsCount($parcelsCount);
//
//        // If you want to set a retutn voucher.
////        $returnVoucher = new ParamReturnVoucher();
////        $returnVoucher->setServiceTypeId(505);
////        $returnVoucher->setPayerType(0); // Note that the sender of the return shipment is the receiver of the primary shipment.
////        $picking->setReturnVoucher($returnVoucher);
//
//        // If you want to set some options before payment.
//        if($optionsBeforePayment){
//            $paramOptionsBeforePayment = new ParamOptionsBeforePayment(); 
//            $paramOptionsBeforePayment->setOpen(true); 
//            $paramOptionsBeforePayment->setTest(false);
//            $paramOptionsBeforePayment->setReturnServiceTypeId(505);
//            $paramOptionsBeforePayment->setReturnPayerType(ParamCalculation::PAYER_TYPE_SENDER); // Note that the sender of the return shipment is the receiver of the primary shipment.
//            $picking->setOptionsBeforePayment($paramOptionsBeforePayment);
//        }
//        
//        // Create bill of lading
//        $resultBOL = $this->eps->createBillOfLading($picking);
//        
//        return $resultBOL; // $resultBOL contains response in a ResultBOL class instance
//    }
//    
//    /**
//     * 
//     * @param int $pickingId
//     * @param string $cancelComment
//     */
//    public function invalidatePicking($pickingId, $cancelComment = null) {
//        $result = $this->eps->invalidatePicking($pickingId, $cancelComment);
//        
//        return $result;
//    }
//    
//    /**
//     * Creates Bill of Ladding PDF
//     * 
//     * @global type $install_path
//     * @param type $pickingId // parcelId from ResultBOL of the createBillOfLading method
//     * @return int (the number of bytes that were written to the file), or false
//     */
//    public function printBillOfLadingPdf($pickingId) {
//        global $install_path;
//        
//        $paramPDF = new ParamPDF();
//        $paramPDF -> setIds(array(0 => $pickingId));
//        $paramPDF -> setType(ParamPDF::PARAM_PDF_TYPE_BOL);
//        $paramPDF -> setIncludeAutoPrintJS(true);
//        
//        // Save pdf in a file
//        $fileNameOnly = $this->eps->getUsername().'_picking_'.$pickingId.'_'.time().'.pdf';
//        $fileName = $install_path.DIRECTORY_SEPARATOR."files".DIRECTORY_SEPARATOR."econt".DIRECTORY_SEPARATOR."bill_of_ladings".DIRECTORY_SEPARATOR.$fileNameOnly;
//        
//        $isFileSaved = file_put_contents($fileName, $this->eps->createPDF($paramPDF), FILE_APPEND | LOCK_EX);
//        
//        return $isFileSaved ? $fileNameOnly : null;
//    }
//    
//    /**
//     * Prints Bill of Ladding Labels in PDF
//     * 
//     * @global type $install_path
//     * @param type $pickingId
//     * @return int (the number of bytes that were written to the file), or false
//     */
//    public function printBillOfLadingLabelsPdf($pickingId) {
//        global $install_path;
//        
//        $paramPDF = new ParamPDF();
//        $paramPDF -> setIds(array(0 => $pickingId));
//        $paramPDF -> setType(ParamPDF::PARAM_PDF_TYPE_LBL);
//        $paramPDF -> setIncludeAutoPrintJS(true);
//        
//        // Save pdf in a file
//        $fileNameOnly = $this->eps->getUsername().'_lbl_'.$pickingId.'_'.time().'.pdf';
//        $fileName = $install_path.DIRECTORY_SEPARATOR."files".DIRECTORY_SEPARATOR."econt".DIRECTORY_SEPARATOR."bill_of_lading_labels".DIRECTORY_SEPARATOR.$fileNameOnly;
//        
//        return file_put_contents($fileName, $this->eps->createPDF($paramPDF), FILE_APPEND | LOCK_EX);
//    }
//    
//    /**
//     * 
//     * @param array $pickingIds //array(0 => 299000129, 1 => 299000130)
//     * @param int $workingEndTime //(f.e. 1800 for 18:00) The sender's working time end
//     * @param int $readinessTime //(f.e. 1730 for 17:30) Specifies when all the shipments/parcels will be ready for pickup. The default value is "now".
//     * @param string $pickupDate //(format: "Y-m-d", f.e. 2019-11-30 ) The date for shipments pick-up (the "time" component is ignored). The default value is "today".
//     */
//    public function createOrder($pickingIds, $workingEndTime, $readinessTime = 0, $pickupDate = "") {
//        $order = new ParamOrder();
//        $order->setBillOfLadingsList($pickingIds);
//        $order->setBillOfLadingsToIncludeType(ParamOrder::ORDER_BOL_INCLUDE_TYPE_EXPLICIT);
//        if($pickupDate){
//            $isValidPickupDate = Helpers::validateDate($pickupDate);
//            if($isValidPickupDate){
//                $order->setPickupDate($pickupDate);
//            }
//        }
//        if($readinessTime){
//            $order->setReadinessTime($readinessTime);
//        }
//        $order->setContactName($this->sndrContactName);
//        $paramPhoneNumber = new ParamPhoneNumber();
//        $paramPhoneNumber -> setNumber($this->sndrPhoneNumber);
//        $order->setPhoneNumber($paramPhoneNumber);
//        $order->setWorkingEndTime($workingEndTime);
//        
//        $arrResultOrderPickingInfo = $this->eps->createOrder($order);
//        
//        return $arrResultOrderPickingInfo;
//    }
//    
//    public function trackShipment($billOfLading, $returnOnlyLastOperation = false) {
//        global $lng;
//        
//        $lang = strtoupper($lng);
//        
//        $resultTrackPickingEx = $this->eps->trackPickingEx($billOfLading, $lang, $returnOnlyLastOperation);
//        
//        return $resultTrackPickingEx;
//    }
//    
//    public function validateAddress($siteId, $address, $validationMode = 2) {
//        $paramAddress = new ParamAddress();
//        $paramAddress->setSiteId($siteId);
//        $paramAddress->setAddressNote($address);
//        $isValidAddress = $this->eps->validateAddress($paramAddress, $validationMode);
//        
//        return $isValidAddress;
//    }
}