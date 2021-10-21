<?php

/**
 * Class for easy implementation of Econt's api
 *
 * @author Kaloyan Kalchev (SmartWeb)
 */
class Econt {

    private $username = "";
    private $password = "";
//    private $sndrSiteId = 41; // Default siteId of the sender (in this case it is 41 - София)
//    private $sndrSiteName = "София"; // Default siteName of the sender (in this case it is София)
    private $bringToOfficeId = 1080; // Default Id of the office that sender should bring the parcel(s). (93 - "ЯМБОЛ - СКЛАД")
    private $sndrContactName = "АРТХА БЮТИ ООД"; // Default contact name of the sender
    private $sndrPhoneNumber = "0885525556"; // Default phone number of the sender
    private $clientNumber = "СФ7447";
    private $ein = "205766567";
    private $agentName = "Виктория Димитрова";
    private $juridicalEntity = true;
//    public $timeZone;
//    public $eps;
    
    public function __construct() {
        global $credentialsNumberEcont;

        $this->setCredentials($credentialsNumberEcont);
    }
    
    public function updateCities() {
//        global $db;
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
        
        //return $res;
        
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
    

    public function createBillOfLading($payerType, $amountCODBase, $contentDescription, $weightDeclared,  $receiverSiteId, $receiverAddressNote, $receiverName, $receiverCompanyName, $receiverPhone, $receiverTakeFromOfficeId = null, $deferredDeliveryWorkDays = 0, $optionsBeforePayment = true, $senderBringToOfficeId = null, $senderBringToOffice = true) {
        global $econtUseProductionURLs;
        global $senderAddresses;
        
        $url_test = "http://demo.econt.com/ee/services/Shipments/LabelService.createLabel.json";
        $url_production = "http://ee.econt.com/services/Shipments/LabelService.createLabel.json";
        $url = $econtUseProductionURLs ? $url_production : $url_test;
        
        $contentDescriptionCleaned = htmlspecialchars(trim($contentDescription));
        $receiverAddressNoteCleaned = htmlspecialchars(trim($receiverAddressNote));
        $receiverNameCleaned = htmlspecialchars(trim($receiverName));
        $receiverCompanyNameCleaned = htmlspecialchars(trim($receiverCompanyName));
        $receiverPhoneCleaned = htmlspecialchars(trim($receiverPhone));
        
        $request = array(
            "label" => array(
                "senderClient" => array(
                    "name" => $this->sndrContactName,
                    "phones" => array($this->sndrPhoneNumber),
                    "clientNumber" => $this->clientNumber,
                    "juridicalEntity" => $this->juridicalEntity,
                    "ein" => $this->ein
                ),
                "senderAgent" => array(
                    "name" => $this->agentName,
                    "phones" => array($this->sndrPhoneNumber)
                ),
                "receiverClient" => array(
                    "name" => $receiverNameCleaned,
                    "phones" => array($receiverPhoneCleaned)
                ),
                "packCount" => 1,
                "shipmentType" => "PACK",
                "weight" => $weightDeclared,
                "shipmentDescription" => $contentDescriptionCleaned,
                "services" => array(
                    //"moneyTransferAmount" => $amountCODBase,
                    "cdAmount" => $amountCODBase,
                    "cdCurrency" => "BGN",
                    "cdPayOptionsTemplate" => "CD113731"
                )
//                ,
//                "paymentSenderMethod" => "credit",
//                "paymentReceiverMethod" => "cash",
//                "paymentReceiverAmountIsPercent" => 0
            ),
            "mode" => "create"
        );
        if($optionsBeforePayment){
            $request["label"]["payAfterAccept"] = "1";
        }
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
                                                    "fullAddress" => $receiverAddressNoteCleaned
                                                );
            $request["label"]["sendDate"] = time();
        }
        
        $resultBOLResponse = $this->send($url, $request);
        $resultBOL = json_decode($resultBOLResponse, true);
        
        return $resultBOL;
    }
    
    public function validateAddress($receiverSiteId, $receiverAddressNote) {
        global $econtUseProductionURLs;
        
        $url_test = "http://demo.econt.com/ee/services/Nomenclatures/AddressService.validateAddress.json";
        $url_production = "http://ee.econt.com/services/Nomenclatures/AddressService.validateAddress.json";
        $url = $econtUseProductionURLs ? $url_production : $url_test;
        
        $receiverAddressNoteCleaned = htmlspecialchars(trim($receiverAddressNote));
        
        $site = $this->getSiteById((int)$receiverSiteId);
        
        $request = array(   
                            "address" => array(
                                "city" => array(
                                    "country" => array(
                                        "code3" => "BGR"
                                    ),
                                    "name" => "{$site["name_bg"]}",
                                    "postCode" => "{$site["postcode"]}"
                                ),
                                "fullAddress" => $receiverAddressNoteCleaned
                            )
                        );
        
        $resultResponse = $this->send($url, $request);
        $result = json_decode($resultResponse, true);
        
        return $result;
    }
    
    /**
     * 
     * @param int $pickingId
     * @param string $cancelComment
     */
    public function invalidatePicking($pickingId, $cancelComment = null) {
        global $econtUseProductionURLs;
        
        $url_test = "http://demo.econt.com/ee/services/Shipments/LabelService.deleteLabels.json";
        $url_production = "http://ee.econt.com/services/Shipments/LabelService.deleteLabels.json";
        $url = $econtUseProductionURLs ? $url_production : $url_test;
        
        $request = array("shipmentNumbers" => array($pickingId));
        
        $result = $this->send($url, $request);
        
        return $result;
    }
    
    /**
     * Creates Bill of Ladding PDF
     * 
     * @global type $install_path
     * @param type $pickingId // parcelId from ResultBOL of the createBillOfLading method
     * @return int (the number of bytes that were written to the file), or false
     */
    public function printBillOfLadingPdf($pickingId, $pdfURL) {
        global $install_path;
        
        // Save pdf in a file
        $fileNameOnly = 'econt_picking_'.$pickingId.'_'.time().'.pdf';
        $fileName = $install_path.DIRECTORY_SEPARATOR."files".DIRECTORY_SEPARATOR."econt".DIRECTORY_SEPARATOR."bill_of_ladings".DIRECTORY_SEPARATOR.$fileNameOnly;
        
        $isFileSaved = file_put_contents($fileName, file_get_contents($pdfURL), FILE_APPEND | LOCK_EX);
        
        return $isFileSaved ? $fileNameOnly : null;
    }
    
    public function trackShipment($billOfLading, $returnOnlyLastOperation = false) {
        global $econtUseProductionURLs;
        
        $url_test = "http://demo.econt.com/ee/services/Shipments/ShipmentService.getShipmentStatuses.json";
        $url_production = "http://ee.econt.com/services/Shipments/ShipmentService.getShipmentStatuses.json";
        $url = $econtUseProductionURLs ? $url_production : $url_test;
        
        $request = array("shipmentNumbers" => array($billOfLading));
        
        $result = $this->send($url, $request);
        $resultArr = json_decode($result, true);
        $error = $resultArr["shipmentStatuses"][0]["error"];
        $tracking = $resultArr["shipmentStatuses"][0]["status"]["trackingEvents"];
        
        return $error ? $error : $tracking;
    }
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