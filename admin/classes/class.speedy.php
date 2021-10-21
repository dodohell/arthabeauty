<?php

// Utility methods
require_once __DIR__  . DIRECTORY_SEPARATOR . '..' .DIRECTORY_SEPARATOR. '..' .DIRECTORY_SEPARATOR. 'libs' .DIRECTORY_SEPARATOR. 'speedy-eps-php-client-soap' . DIRECTORY_SEPARATOR . 'speedy-eps-lib' . DIRECTORY_SEPARATOR . 'util' . DIRECTORY_SEPARATOR . 'Util.class.php';
// Facade class
require_once __DIR__  . DIRECTORY_SEPARATOR . '..' .DIRECTORY_SEPARATOR. '..' .DIRECTORY_SEPARATOR. 'libs' .DIRECTORY_SEPARATOR. 'speedy-eps-php-client-soap' . DIRECTORY_SEPARATOR . 'speedy-eps-lib' . DIRECTORY_SEPARATOR . 'ver01' . DIRECTORY_SEPARATOR . 'EPSFacade.class.php';
// Implementation class
require_once __DIR__  . DIRECTORY_SEPARATOR . '..' .DIRECTORY_SEPARATOR. '..' .DIRECTORY_SEPARATOR. 'libs' .DIRECTORY_SEPARATOR. 'speedy-eps-php-client-soap' . DIRECTORY_SEPARATOR . 'speedy-eps-lib' . DIRECTORY_SEPARATOR . 'ver01' . DIRECTORY_SEPARATOR . 'soap' . DIRECTORY_SEPARATOR . 'EPSSOAPInterfaceImpl.class.php';

/**
 * Class for easy implementation of Speedy's api
 *
 * @author Kaloyan Kalchev (SmartWeb)
 */
class Speedy {

    private $username = "";
    private $password = "";
    private $sndrSiteId = 87374; // Default siteId of the sender (in this case it is 87374 - Ямбол)
    private $bringToOfficeId = 93; // Default Id of the office that sender should bring the parcel(s). (93 - "ЯМБОЛ - СКЛАД")
    private $sndrContactName = "Тест Тестов"; // Default contact name of the sender
    private $sndrPhoneNumber = "0888000000"; // Default phone number of the sender
    public $timeZone;
    public $eps;
    
    public function __construct() {
        global $credentialsNumber;
        
        //header("Content-Type: application/json; charset=utf-8");
        $this->setCredentials($credentialsNumber);
        if (function_exists("date_default_timezone_set")) {
            date_default_timezone_set(Util::SPEEDY_TIME_ZONE);
            $this->timeZone = date_default_timezone_get();
        } else {
            putenv("TZ=" . Util::SPEEDY_TIME_ZONE);
            $this->timeZone = getenv("TZ");
        }

        $this->eps = new EPSFacade(new EPSSOAPInterfaceImpl("https://www.speedy.bg/eps/main01.wsdl"), $this->username, $this->password);
    }
    
    /**
     * 
     * @global type $speedyUsernameTest
     * @global type $speedyPasswordTest
     * @global type $speedyUsernameProduction
     * @global type $speedyPasswordProduction
     * @param type $credentialsNumber
     */
    public function setCredentials($credentialsNumber) {
        global $speedyUsernameTest;     // #1
        global $speedyPasswordTest;     // #1
        
        global $speedyUsernameProduction; // #2
        global $speedyPasswordProduction; // #2
        
        if($credentialsNumber == 1){
            $this->username = $speedyUsernameTest;
            $this->password = $speedyPasswordTest;
        }else if($credentialsNumber == 2){
            $this->username = $speedyUsernameProduction;
            $this->password = $speedyPasswordProduction;
        }else{
            $this->username = $speedyUsernameTest;
            $this->password = $speedyPasswordTest;
        }
    }

    public function getInfo() {
        $resultLogin = $this->eps->getResultLogin();
        $senderClientData = $this->eps->getClientById($resultLogin->getClientId());
        $senderSiteId = $senderClientData->getAddress()->getSiteId();
        
        // Данни за подател
        $sender = new ParamClientData();
        $sender->setClientId($senderClientData->getClientId());
        $sender->setContactName("Тест ЕООД");
        $senderPhoneNumber = new ParamPhoneNumber();
        $senderPhoneNumber->setNumber("0123456789");
        $sender->setPhones(array(0 => $senderPhoneNumber));
        echo "<pre>";
        var_dump($sender);
        var_dump($senderSiteId);
        var_dump($senderClientData);
        echo "</pre>";
        exit();
        return $senderClientData;
    }
    
    /**
     * Get sender client id (authenticated client) 
     * 
     * @return int
     */
    public function getClientId() {
        $clientId = $this->eps->getResultLogin()->getClientId();
        
        return $clientId;
    }
    
    /**
     * 
     * Get the siteId of the sender or receiver site. The "sideId" is the identifier of the site.
     * 
     * @param string $siteName (Full site name)
     * @param string $regionName (it can be a part of the name - ex. "ХАСК" as part of the name of "ХАСКОВО" region)
     * @param string $language (optional - default: "BG")
     * @param int $countryId (optional - default: 100 - Bulgaria)
     * @return int $siteId or null (will return only one record for site called ДОБРИЧ in ХАСКОВО region. The combination of name and region is unique.)
     */
    public function getSiteId($siteName, $regionName, $language = "BG", $countryId = 100) {
        $regionName = $regionName == "СОФИЯ (СТОЛИЦА)" ? "София" : $regionName;
        
        $filter = new ParamFilterSite();
        $filter->setCountryId($countryId);
        $filter->setName($siteName);
        $filter->setRegion($regionName);
        
        $resultListSiteEx = $this->eps->listSitesEx($filter, $language);
        
        $siteId = null;
        if($resultListSiteEx){
            //if it returns more then one sites this function returns null
            if($resultListSiteEx[0]->isExactMatch()){
                $siteId = $resultListSiteEx[0]->getSite()->getId();
            }
        }
        
        return $siteId;
    }
    
    public function getOfficesList($siteId, $language = "BG", $countryId = 100){
        $name = NULL;
        
        $resultListOffices = $this->eps->listOfficesEx($name, $siteId, $language, $countryId); // (siteId: 68134; countryId: 100) $resultListOffices will return all offices in Sofia, Bulgaria.
        $resultListOfficesArr = array();
        foreach ($resultListOffices as $k => $v) {
            $id = $resultListOffices[$k]->getId();
            $name = $resultListOffices[$k]->getName();
            $maxParcelDimensionsWidth = $resultListOffices[$k]->getMaxParcelDimensions()->getWidth();
            $maxParcelDimensionsHeight = $resultListOffices[$k]->getMaxParcelDimensions()->getHeight();
            $maxParcelDimensionsDepth = $resultListOffices[$k]->getMaxParcelDimensions()->getDepth();
            $maxParcelWeight = $resultListOffices[$k]->getMaxParcelWeight();
            $resultListOfficesArr[$k]["id"] = $id;
            $resultListOfficesArr[$k]["name"] = $name;
            $resultListOfficesArr[$k]["maxParcelWeight"] = $maxParcelWeight;
            $resultListOfficesArr[$k]["maxParcelDimensions"]["width"] = $maxParcelDimensionsWidth;
            $resultListOfficesArr[$k]["maxParcelDimensions"]["height"] = $maxParcelDimensionsHeight;
            $resultListOfficesArr[$k]["maxParcelDimensions"]["depth"] = $maxParcelDimensionsDepth;
        }
        return $resultListOfficesArr;
    }
    
    /**
     * It returns all valid services with their parameters. ("СТАНДАРТ 24 ЧАСА", ЕКСПРЕСНА, ИКОНОМИЧНА, PALLET ONE BG - PREMIUM, PALLET ONE BG - ECONOMY, ГУМИ ... )
     * 
     * @param int $rcptSiteId
     * @param int $sndrSiteId
     * @param string $language
     * @return type
     */
    public function getValidServicesBetweenTwoSites($rcptSiteId, $sndrSiteId = 0, $language = "BG"){
        $currentDate = date('Y-m-d');
        $senderId = NULL;
        $sndrCountryId = NULL;
        $senderOfficeId = NULL;
        $sndrPostCode = NULL;
        $receiverId = NULL;
        $rcptCountryId = NULL;
        $receiverOfficeId = NULL;
        $rcptPostCode = NULL;
        if (!$sndrSiteId){
            $sndrSiteId = $this->sndrSiteId; // if no $sndrSiteId we get the default one
        }
        $resultListServicesForSites = $this->eps->listServicesForSites($currentDate, $sndrSiteId, $rcptSiteId, $sndrCountryId, $sndrPostCode, $rcptCountryId, $rcptPostCode, $language, $senderId, $receiverId, $senderOfficeId, $receiverOfficeId);
        
        return $resultListServicesForSites;
    }
    
    /**
     * $resultListContractClients will return data like 'clientId, 'partnerName', 'objectName', 'address' etc. of all own objects.
     * 
     * @return type
     */
    public function getAllObjectsOfTheContract() {
        $resultListContractClients = $this->eps->listContractClients();
        
        return $resultListContractClients;
    }
    
    /**
     * Calculate the price of a picking (before picking registration)
     * 
     * @param float $amountCODBase
     * @param float $weightDeclared
     * @param int $receiverSiteId
     * @param int $receiverTakeFromOfficeId
     * @param int $senderBringToOfficeId
     * @param int $senderBringToOffice
     * @return array ("codBase" => *, "deliveryAmount" => *)
     */
    public function getCalculatedPrice($amountCODBase, $weightDeclared, $receiverSiteId, $receiverTakeFromOfficeId = null, $senderBringToOfficeId = null, $senderBringToOffice = true) {
        // Init current date
        $todayDate = date('Y-m-d');
        //if $senderBringToOffice is true we use the given or default Id of the office that sender should bring the parcel(s). else if $senderBringToOffice is false Courier will visit sender address
        if($senderBringToOffice){
            $senderBringToOfficeId = $senderBringToOfficeId ? $senderBringToOfficeId : $this->bringToOfficeId;
        }else{
            $senderBringToOfficeId = null;
        }
        $calculationData = new StdClass();
        $calculationData->weightDeclared = $weightDeclared; // Decalred weight (5.25)
        $calculationData->bringToOfficeId = $senderBringToOfficeId; // Id of the office that sender should bring the parcel(s). Courier will visit sender address if this value is null
        $calculationData->takeFromOfficeId = $receiverTakeFromOfficeId; // Id of the office that recipient should take the parcel(s) from. Courier will visit recipient address if this value is null
        $calculationData->parcelsCount = 1; // Parcels count
        $calculationData->documents = false; // Flag for documents (false)
        $calculationData->palletized = false; // Flag for pallets
        $calculationData->fragile = false; // flag for fragile content
        $calculationData->payerType = ParamCalculation::PAYER_TYPE_RECEIVER; // Determine the payer
        $calculationData->amountCODBase = (float)$amountCODBase; // Collecton on Delivery amount (25)
        $calculationData->serviceTypeId = 505; // Service type 505 from Speedy nomenclature (serviceTypeId obtained from "listServicesForSites")
//        $calculationData->takingDate = strtotime(date("Y-m-d", strtotime($todayDate)) . " +1 day"); // We need taking date to be tomorrow
        $calculationData->autoAdjustTakingDate = true; // If set to true, the "takingDate" field is not just to be validated, but the first allowed (following) date will be used instead (in compliance with the pick-up schedule etc.).
        
        $clientId = $this->getClientId();
        
        // In this example receiver address is in city of Sofia
        // Lookup for example receiver city of Sofia in Speedy address nomenclature
        // We are sure we have results therefore array is not verified for null or empty values.
//        $arrSites = $this->eps->listSitesEx('gr.', 'VARNA');
//        $receiverSiteId = $arrSites[0]->getId();
        
        $paramCalculation = new ParamCalculation();
        $paramCalculation->setBroughtToOffice(!is_null($calculationData->bringToOfficeId));
        $paramCalculation->setToBeCalled(!is_null($calculationData->takeFromOfficeId));
        $paramCalculation->setParcelsCount($calculationData->parcelsCount);
        $paramCalculation->setWeightDeclared($calculationData->weightDeclared);
        $paramCalculation->setDocuments($calculationData->documents);
        $paramCalculation->setPalletized($calculationData->palletized);
        $paramCalculation->setFragile($calculationData->fragile);
        $paramCalculation->setSenderId($clientId);
        $paramCalculation->setReceiverSiteId($receiverSiteId);
        $paramCalculation->setPayerType($calculationData->payerType);
        $paramCalculation->setAmountCodBase($calculationData->amountCODBase);
        //$paramCalculation->setRetMoneyTransferReqAmount((float)$calculationData->amountCODBase);
        //$paramCalculation->setTakingDate($calculationData->takingDate);
        $paramCalculation->setAutoAdjustTakingDate($calculationData->autoAdjustTakingDate);
        $paramCalculation->setServiceTypeId($calculationData->serviceTypeId);
        
        //$paramCalculation->setDeferredDeliveryWorkDays(1);
        
        $resultCalculation = $this->eps->calculate($paramCalculation);
        
        $result = array();
        if($resultCalculation){
            $codBase =  $resultCalculation->getAmounts()->getCodBase();
            $deliveryAmount = $resultCalculation->getAmounts()->getTotal();
            $result = array(
                "codBase" => $codBase,
                "deliveryAmount" => $deliveryAmount
            );
        }
        return $result;
    }
    
    /**
     * Registers Bill Of Lading, but to trigger the process of taking and delivery, the client needs to order a courier.
     * 
     * @param int $payerType //0: PAYER_TYPE_SENDER, 1: PAYER_TYPE_RECEIVER, 3: PAYER_TYPE_THIRD_PARTY
     * @param float $amountCODBase
     * @param string $contentDescription
     * @param float $weightDeclared
     * @param int $receiverSiteId //Must be 0 if receiver will take it from an Office
     * @param string $receiverAddressNote //Must be 0 if receiver will take it from an Office. Site name should not be placed in $receiverAddressNote string, because site Id is passed separately in $receiverSiteId and cannot be omitted
     * @param string $receiverName //Name of the client (if company this is contact person)
     * @param string $receiverCompanyName //Required for companies, forbidden for private persons and must be empty string
     * @param string $receiverPhone
     * @param int $receiverTakeFromOfficeId
     * @param int $senderBringToOfficeId
     * @param bool $senderBringToOffice
     * @return ResultBOL (available methods: getGeneratedParcels(), getAmounts(), getDeadlineDelivery()
     */
    public function createBillOfLading($payerType, $amountCODBase, $contentDescription, $weightDeclared,  $receiverSiteId, $receiverAddressNote, $receiverName, $receiverCompanyName, $receiverPhone, $receiverTakeFromOfficeId = null, $deferredDeliveryWorkDays = 0, $optionsBeforePayment = true, $senderBringToOfficeId = null, $senderBringToOffice = true) {
        
        $contentDescriptionCleaned = htmlspecialchars(trim($contentDescription));
        $receiverAddressNoteCleaned = htmlspecialchars(trim($receiverAddressNote));
        $receiverNameCleaned = htmlspecialchars(trim($receiverName));
        $receiverCompanyNameCleaned = htmlspecialchars(trim($receiverCompanyName));
        $receiverPhoneCleaned = htmlspecialchars(trim($receiverPhone));
        
        //if $senderBringToOffice is true we use the given or default Id of the office that sender should bring the parcel(s). else if $senderBringToOffice is false Courier will visit sender address
        if($senderBringToOffice){
            $senderBringToOfficeId = $senderBringToOfficeId ? $senderBringToOfficeId : $this->bringToOfficeId;
        }else{
            $senderBringToOfficeId = null;
        }
        
        // Init picking structure with data
        $pickingData = new StdClass();
        $pickingData->weightDeclared = $weightDeclared; // Decalred weight
        $pickingData->bringToOfficeId = $senderBringToOfficeId; // Id of the office that sender should bring the parcel(s). Courier will visit sender address if this value is null
        $pickingData->takeFromOfficeId = $receiverTakeFromOfficeId; // Id of the office that recipient should take the parcel(s) from. Courier will visit recipient address if this value is null
        $pickingData->parcelsCount = 1; // Parcels count
        $pickingData->documents = false; // Flag for documents
        $pickingData->palletized = false; // Flag for pallets
        $pickingData->fragile = false; // flag for fragile content
        if($payerType === 0){
            $pickingData->payerType = ParamCalculation::PAYER_TYPE_SENDER; // Determine the payer
        }elseif($payerType === 1){
            $pickingData->payerType = ParamCalculation::PAYER_TYPE_RECEIVER; // Determine the payer
        }elseif($payerType === 2){
            $pickingData->payerType = ParamCalculation::PAYER_TYPE_THIRD_PARTY; // Determine the payer
        }

        if($amountCODBase > 0.0){
           //Наложен платеж
            $pickingData->amountCODBase = (float)$amountCODBase; // Collecton on Delivery amount
            //Пощенски паричен превод
            //$pickingData->retMoneyTransferReqAmount = (float)$amountCODBase; // Return money-transfer request amount
        }
        $pickingData->backDocumentReq = false; // Back document request flag
        $pickingData->backReceiptReq = false; // Back receipt request flag
        $pickingData->contents = $contentDescriptionCleaned; // Content description
        $pickingData->packing = 'PACKAGE'; // Type of packing
        $pickingData->serviceTypeId = 505; // Service type 505 from Speedy nomenclature (serviceTypeId obtained from "listServicesForSites")
        $pickingData->takingDate = time(); // Taking date is today

        // Sender is authenticated client. 
        // Therefore we fill Bill Of Lading sender client data with authenticated client data
        $senderClientId = $this->getClientId();
        $sender = new ParamClientData();
        $sender->setClientId($senderClientId);
        $sender->setContactName($this->sndrContactName);
        $senderPhoneNumber = new ParamPhoneNumber();
        $senderPhoneNumber->setNumber($this->sndrPhoneNumber);
        $sender->setPhones(array(0 => $senderPhoneNumber));

        // In this example receiver address is "VARNA, kv. MLADOST 1, bl. 102, vh. 3, et. 4, ap. 5"
        // Lookup for example receiver city of Sofia in Speedy address nomenclature
        // We are sure we have results therefore array is not verified for null or empty values.
//        $arrSites = $this->eps->listSitesEx('гр.', 'ВАРНА');
//        $receiverSiteId = $arrSites[0]->getId();
//        $receiverSiteId = 68134;
        // Lookup example receiver address quarter "MLADOST 1" in city of Varna in Speedy address nomenclature
        // We are sure we have results therefore array is not verified for null or empty values.
        // EPS supports similar methods for streets also
//        $arrQuarters = $this->eps->listQuarters('MLADOST 1', $receiverSiteId);
//        $receiverResultQuarter = $arrQuarters[0];

        // Set receiver address fields
//        $receiverAddress = new ParamAddress();
//        $receiverAddress->setSiteId($receiverSiteId);
//        $receiverAddress->setQuarterId($receiverResultQuarter->getId());
//        $receiverAddress->setBlockNo('102');
//        $receiverAddress->setEntranceNo('3');
//        $receiverAddress->setFloorNo('4');
//        $receiverAddress->setApartmentNo('5');

        // Because we cannot determine address fields from input address text (we cannot be sure if everything is correct when we structurally parse the input address)
        // So we use method setAddressNote.
        
        // NOTE: Site name should not be placed in addressNote field, because site Id is passed separately and cannot be omitted
        $receiverAddress = new ParamAddress();
        $receiverAddress->setSiteId($receiverSiteId);
        $receiverAddress->setAddressNote($receiverAddressNoteCleaned); //f.e. ('kv. MLADOST 1, bl. 102, vh. 3, et. 4, ap. 5')

        // Set receiver client data
        $receiver = new ParamClientData();
        if(!$receiverTakeFromOfficeId){
            $receiver->setAddress($receiverAddress);
        }
        $receiver->setPartnerName($receiverCompanyNameCleaned ? $receiverCompanyNameCleaned : $receiverNameCleaned);
        $paramPhoneNumber = new ParamPhoneNumber();
        $paramPhoneNumber->setNumber($receiverPhoneCleaned);
        $receiver->setPhones(array(0 => $paramPhoneNumber));
        if($receiverCompanyNameCleaned){
            $receiver->setContactName($receiverNameCleaned);
        }

        $picking = new ParamPicking();
        $picking->setServiceTypeId($pickingData->serviceTypeId);
        $picking->setBackDocumentsRequest($pickingData->backDocumentReq);
        if($deferredDeliveryWorkDays > 0){
            $picking->setDeferredDeliveryWorkDays($deferredDeliveryWorkDays);
        }
        $picking->setBackReceiptRequest($pickingData->backReceiptReq);
        $picking->setWillBringToOffice(!is_null($pickingData->bringToOfficeId));
        if(!is_null($pickingData->bringToOfficeId)){
            $picking->setWillBringToOfficeID($senderBringToOfficeId);
        }
        $picking->setOfficeToBeCalledId($pickingData->takeFromOfficeId);
        $picking->setParcelsCount($pickingData->parcelsCount);
        $picking->setWeightDeclared($pickingData->weightDeclared);
        $picking->setContents($pickingData->contents);
        $picking->setPacking($pickingData->packing);
        $picking->setDocuments($pickingData->documents);
        $picking->setPalletized($pickingData->palletized);
        $picking->setFragile($pickingData->fragile);
        $picking->setSender($sender);
        $picking->setReceiver($receiver);
        $picking->setPayerType($pickingData->payerType);
        $picking->setTakingDate($pickingData->takingDate);
        //Наложен платеж
        $picking->setAmountCodBase($pickingData->amountCODBase);
        //Пощенски паричен превод
        //$picking->setRetMoneyTransferReqAmount($pickingData->retMoneyTransferReqAmount);
        
        /* Optional data */
        // Set parcels size
//        $parcelDetails1 = new ParamParcelInfo();
//        $size = new Size();
//        $size->setWidth(5);
//        $size->setDepth(6);
//        $size->setHeight(7);
//        $parcelDetails1->setSeqNo(1);
//        $parcelDetails1->setParcelId(-1);
//        $parcelDetails1->setSize($size);
//        $parcelDetails1->setWeight(1);
//        $parcelsArray[] = $parcelDetails1;
//
//        $parcelDetails2 = new ParamParcelInfo(); // if there is more than one parcel
//        $size2 = new Size();
//        $size2->setWidth(11);
//        $size2->setDepth(12);
//        $size2->setHeight(13);
//        $parcelDetails2->setSeqNo(2);
//        $parcelDetails2->setParcelId(-1);
//        $parcelDetails2->setSize($size2);
//        $parcelDetails2->setWeight(2);
//        $parcelsArray[] = $parcelDetails2;
//
//        $parcelsCount = count($parcelsArray);
//        $picking->setParcels($parcelsArray);
//        $picking->setParcelsCount($parcelsCount);

        // If you want to set a retutn voucher.
//        $returnVoucher = new ParamReturnVoucher();
//        $returnVoucher->setServiceTypeId(505);
//        $returnVoucher->setPayerType(0); // Note that the sender of the return shipment is the receiver of the primary shipment.
//        $picking->setReturnVoucher($returnVoucher);

        // If you want to set some options before payment.
        if($optionsBeforePayment){
            $paramOptionsBeforePayment = new ParamOptionsBeforePayment(); 
            $paramOptionsBeforePayment->setOpen(true); 
            $paramOptionsBeforePayment->setTest(false);
            $paramOptionsBeforePayment->setReturnServiceTypeId(505);
            $paramOptionsBeforePayment->setReturnPayerType(ParamCalculation::PAYER_TYPE_SENDER); // Note that the sender of the return shipment is the receiver of the primary shipment.
            $picking->setOptionsBeforePayment($paramOptionsBeforePayment);
        }
        
        // Create bill of lading
        $resultBOL = $this->eps->createBillOfLading($picking);
        
        return $resultBOL; // $resultBOL contains response in a ResultBOL class instance
    }
    
    /**
     * 
     * @param int $pickingId
     * @param string $cancelComment
     */
    public function invalidatePicking($pickingId, $cancelComment = null) {
        $result = $this->eps->invalidatePicking($pickingId, $cancelComment);
        
        return $result;
    }
    
    /**
     * Creates Bill of Ladding PDF
     * 
     * @global type $install_path
     * @param type $pickingId // parcelId from ResultBOL of the createBillOfLading method
     * @return int (the number of bytes that were written to the file), or false
     */
    public function printBillOfLadingPdf($pickingId) {
        global $install_path;
        
        $paramPDF = new ParamPDF();
        $paramPDF -> setIds(array(0 => $pickingId));
        $paramPDF -> setType(ParamPDF::PARAM_PDF_TYPE_BOL);
        $paramPDF -> setIncludeAutoPrintJS(true);
        
        // Save pdf in a file
        $fileNameOnly = $this->eps->getUsername().'_picking_'.$pickingId.'_'.time().'.pdf';
        $fileName = $install_path.DIRECTORY_SEPARATOR."files".DIRECTORY_SEPARATOR."speedy".DIRECTORY_SEPARATOR."bill_of_ladings".DIRECTORY_SEPARATOR.$fileNameOnly;
        
        $isFileSaved = file_put_contents($fileName, $this->eps->createPDF($paramPDF), FILE_APPEND | LOCK_EX);
        
        return $isFileSaved ? $fileNameOnly : null;
    }
    
    /**
     * Prints Bill of Ladding Labels in PDF
     * 
     * @global type $install_path
     * @param type $pickingId
     * @return int (the number of bytes that were written to the file), or false
     */
    public function printBillOfLadingLabelsPdf($pickingId) {
        global $install_path;
        
        $paramPDF = new ParamPDF();
        $paramPDF -> setIds(array(0 => $pickingId));
        $paramPDF -> setType(ParamPDF::PARAM_PDF_TYPE_LBL);
        $paramPDF -> setIncludeAutoPrintJS(true);
        
        // Save pdf in a file
        $fileNameOnly = $this->eps->getUsername().'_lbl_'.$pickingId.'_'.time().'.pdf';
        $fileName = $install_path.DIRECTORY_SEPARATOR."files".DIRECTORY_SEPARATOR."speedy".DIRECTORY_SEPARATOR."bill_of_lading_labels".DIRECTORY_SEPARATOR.$fileNameOnly;
        
        return file_put_contents($fileName, $this->eps->createPDF($paramPDF), FILE_APPEND | LOCK_EX);
    }
    
    /**
     * 
     * @param array $pickingIds //array(0 => 299000129, 1 => 299000130)
     * @param int $workingEndTime //(f.e. 1800 for 18:00) The sender's working time end
     * @param int $readinessTime //(f.e. 1730 for 17:30) Specifies when all the shipments/parcels will be ready for pickup. The default value is "now".
     * @param string $pickupDate //(format: "Y-m-d", f.e. 2019-11-30 ) The date for shipments pick-up (the "time" component is ignored). The default value is "today".
     */
    public function createOrder($pickingIds, $workingEndTime, $readinessTime = 0, $pickupDate = "") {
        $order = new ParamOrder();
        $order->setBillOfLadingsList($pickingIds);
        $order->setBillOfLadingsToIncludeType(ParamOrder::ORDER_BOL_INCLUDE_TYPE_EXPLICIT);
        if($pickupDate){
            $isValidPickupDate = Helpers::validateDate($pickupDate);
            if($isValidPickupDate){
                $order->setPickupDate($pickupDate);
            }
        }
        if($readinessTime){
            $order->setReadinessTime($readinessTime);
        }
        $order->setContactName($this->sndrContactName);
        $paramPhoneNumber = new ParamPhoneNumber();
        $paramPhoneNumber -> setNumber($this->sndrPhoneNumber);
        $order->setPhoneNumber($paramPhoneNumber);
        $order->setWorkingEndTime($workingEndTime);
        
        $arrResultOrderPickingInfo = $this->eps->createOrder($order);
        
        return $arrResultOrderPickingInfo;
    }
    
    public function trackShipment($billOfLading, $returnOnlyLastOperation = false) {
        global $lng;
        
        $lang = strtoupper($lng);
        
        $resultTrackPickingEx = $this->eps->trackPickingEx($billOfLading, $lang, $returnOnlyLastOperation);
        
        return $resultTrackPickingEx;
    }
    
    public function validateAddress($siteId, $address, $validationMode = 2) {
        $paramAddress = new ParamAddress();
        $paramAddress->setSiteId($siteId);
        $paramAddress->setAddressNote($address);
        $isValidAddress = $this->eps->validateAddress($paramAddress, $validationMode);
        
        return $isValidAddress;
    }
}