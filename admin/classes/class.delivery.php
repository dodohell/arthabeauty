<?php

/**
 * Class for easy implementation of Speedy's api
 *
 * @author Kaloyan Kalchev (SmartWeb)
 */
class Delivery {

    public static function getOfficesSimple($delivery_type, $region_id, $city_id, $returnType = 1) {
        $delivery_type = (int) $delivery_type;
        $region_id = (int) $region_id;
        $city_id = (int) $city_id;

        $districtsObj = new Districts();
        $citiestObj = new Cities();
        $regionName = $districtsObj->getRecord($region_id)["name"];
        $cityName = $citiestObj->getRecord($city_id)["name"];

        $cities = array();
        if($delivery_type === 2){
            $speedy = new Speedy();
            $siteId = $speedy->getSiteId($cityName, $regionName);
            $cities = $speedy->getOfficesList($siteId);
        }
        if($delivery_type === 4){
            $econt = new Econt();
            $siteId = $econt->getSiteId($cityName, $regionName);
            $cities = $econt->getOfficesList($siteId);
        }
        if($returnType != 3){
            return $cities;
        }
        echo json_encode($cities);
    }

    public static function getOfficesByRegionAndCityNames($delivery_type, $region_name, $city_name, $returnType = 1) {
        $cities = array();
        if($delivery_type === 2){
            $speedy = new Speedy();
            $siteId = $speedy->getSiteId($region_name, $city_name);

            $cities = $speedy->getOfficesList($siteId);
        }else if($delivery_type === 4){
            $econt = new Econt();
            $siteId = $econt->getSiteId($region_name, $city_name);

            $cities = $econt->getOfficesList($siteId);
        }
        if($returnType !== 3){
            return $cities;
        }
        echo json_encode($cities);
    }

//    public static function getOffices(FilteredMap $params) {
//        $delivery_type = $params->getInt("delivery_type_id");
//        $region = $params->getString("region");
//        $city = $params->getString("city");
//
//        $cities = array();
//        if($delivery_type === 2){
//            $speedy = new Speedy();
//            $siteId = $speedy->getSiteId($region, $city);
//
//            $cities = $speedy->getOfficesList($siteId);
//        }
//        echo json_encode($cities);
//    }

    public static function getDeliveryAmountAndCodBase($delivery_type, $amountCODBase, $weightDeclared, $receiverSiteId, $receiverTakeFromOfficeId = null){

        //if $weightDeclared units are gr we need to concert it in kg:
        $weightDeclared = $weightDeclared > 0 ? (double)$weightDeclared / 1000 : 0.0;
        if($delivery_type === 1){
            $speedy = new Speedy();
            $result = $speedy->getCalculatedPrice($amountCODBase, $weightDeclared, $receiverSiteId);
        }else if($delivery_type === 2){
            $speedy = new Speedy();
            $result = $speedy->getCalculatedPrice($amountCODBase, $weightDeclared, $receiverSiteId, $receiverTakeFromOfficeId);
        }else if($delivery_type === 3){
            $econt = new Econt();
            $result = $econt->getCalculatedPrice($amountCODBase, $weightDeclared, $receiverSiteId);
        }else if($delivery_type === 4){
            $econt = new Econt();
            $result = $econt->getCalculatedPrice($amountCODBase, $weightDeclared, $receiverSiteId, $receiverTakeFromOfficeId);
        }

        return $result;
    }

    public static function getCourierSiteId($delivery_type, $region, $city) {
//        echo "<pre>";
//        var_dump($delivery_type);
//        var_dump($region);
//        var_dump($city);
//        echo "</pre>";
//        exit();
        if($delivery_type === 1 || $delivery_type === 2){
            $speedy = new Speedy();
            $siteId = $speedy->getSiteId($city, $region);
        }else if($delivery_type === 3 || $delivery_type === 4){
            $econt = new Econt();
            $siteId = $econt->getSiteId($city, $region);
        }

        return $siteId;
    }

    public static function getShipmentInfo($delivery_type, $billOfLading, $returnOnlyLastOperation = false) {
        if($delivery_type === 1 || $delivery_type === 2){
            $speedy = new Speedy();
            $info = $speedy->trackShipment($billOfLading, $returnOnlyLastOperation);
        }else if($delivery_type === 3 || $delivery_type === 4){
            $econt = new Econt();
            $info = $econt->trackShipment($billOfLading, $returnOnlyLastOperation);
        }

        return $info;
    }

    /**
     *
     * @param int $delivery_type            //(1: speedy address, 2: speedy office ...)
     * @param int $payerType //0: PAYER_TYPE_SENDER, 1: PAYER_TYPE_RECEIVER, 3: PAYER_TYPE_THIRD_PARTY
     * @param float $amountCODBase
     * @param string $contentDescription
     * @param float $weightDeclared
     * @param int $receiverSiteId
     * @param string $receiverAddressNote   //Speedy: Site name should not be placed in $receiverAddressNote string, because site Id is passed separately in $receiverSiteId and cannot be omitted
     * @param string $receiverName          //Speedy: Name of the client (company or private person)
     * @param string $receiverCompanyName   //Speedy: Required for companies, forbidden for private persons and must be empty string
     * @param string $receiverPhone
     * @param int $receiverTakeFromOfficeId
     * @param int $senderBringToOfficeId
     * @param bool $senderBringToOffice
     * @return type
     */
    public static function createBillOfLading($delivery_type, $payerType, $amountCODBase, $contentDescription, $weightDeclared,  $receiverSiteId, $receiverAddressNote, $receiverName, $receiverCompanyName, $receiverPhone, $receiverTakeFromOfficeId = null, $deferredDeliveryWorkDays = 0, $optionsBeforePayment = true, $senderBringToOfficeId = null, $senderBringToOffice = false) {
//        echo "<pre>";
//        var_dump($delivery_type);
//        var_dump($payerType);
//        var_dump($amountCODBase);
//        var_dump($contentDescription);
//        var_dump($weightDeclared);
//        var_dump($receiverSiteId);
//        var_dump($receiverAddressNote);
//        var_dump($receiverName);
//        var_dump($receiverCompanyName);
//        var_dump($receiverPhone);
//        var_dump($receiverTakeFromOfficeId);
//        var_dump($deferredDeliveryWorkDays);
//        var_dump($optionsBeforePayment);
//        var_dump($senderBringToOfficeId);
//        var_dump($senderBringToOffice);
//        echo "</pre>";
//        exit();

        //if $weightDeclared units are gr we need to concert it in kg:
        $weightDeclared = $weightDeclared > 0 ? (double)$weightDeclared / 1000 : 0.0;

        if($delivery_type === 1 || $delivery_type === 2){
            $speedy = new Speedy();
            $resultBOL = $speedy->createBillOfLading($payerType, $amountCODBase, $contentDescription, $weightDeclared, $receiverSiteId, $receiverAddressNote, $receiverName, $receiverCompanyName, $receiverPhone, $receiverTakeFromOfficeId, $deferredDeliveryWorkDays, $optionsBeforePayment, $senderBringToOfficeId, $senderBringToOffice);

            return $resultBOL; // $resultBOL contains response in a ResultBOL class instance
        }else if($delivery_type === 3 || $delivery_type === 4){
            $econt = new Econt();
            $isValidAddress = true;
            if($delivery_type === 3){
                $isValidAddress = $econt->validateAddress($receiverSiteId, $receiverAddressNote);
            }
            if(isset($isValidAddress["type"]) && $isValidAddress["type"] == "ExInvalidParam"){
                $resultBOL["errorMessage"] = "invalidAddress";
            }else{
                $resultBOL = $econt->createBillOfLading($payerType, $amountCODBase, $contentDescription, $weightDeclared, $receiverSiteId, $receiverAddressNote, $receiverName, $receiverCompanyName, $receiverPhone, $receiverTakeFromOfficeId, $deferredDeliveryWorkDays, $optionsBeforePayment, $senderBringToOfficeId, $senderBringToOffice);
            }

            if(isset($resultBOL["type"]) && $resultBOL["type"] == "ExInvalidParam"){

                if(isset($resultBOL["message"]) && strlen($resultBOL["message"]) > 3){
                    $resultBOL["errorMessage"] = $resultBOL["message"];
                }else if(isset($resultBOL["innerErrors"][0]["message"]) && strlen($resultBOL["innerErrors"][0]["message"]) > 3){
                    $resultBOL["errorMessage"] = $resultBOL["innerErrors"][0]["message"];
                }else{
                    $resultBOL["errorMessage"] = serialize($resultBOL);
                }

            }

            return $resultBOL; // $resultBOL contains response in a ResultBOL class instance
        }

        return null;
    }

    public static function invalidateShipping($delivery_type, $pickingId, $cancelComment = null) {
        if($delivery_type === 1 || $delivery_type === 2){
            $speedy = new Speedy();
            $result = $speedy->invalidatePicking($pickingId, $cancelComment);

            return $result;
        }else if($delivery_type === 3 || $delivery_type === 4){
            $econt = new Econt();
            $result = $econt->invalidatePicking($pickingId, $cancelComment);

            return $result;
        }

        return null;
    }

    public static function printBillOfLadingPDF($delivery_type, $pickingId, $pdfURL = "") {
        if($delivery_type === 1 || $delivery_type === 2){
            $speedy = new Speedy();
            $fileName = $speedy->printBillOfLadingPdf($pickingId);

            return $fileName; // $resultBOL contains response in a ResultBOL class instance
        }else if($delivery_type === 3 || $delivery_type === 4){
            $econt = new Econt();
            $fileName = $econt->printBillOfLadingPdf($pickingId, $pdfURL);

            return $fileName; // $resultBOL contains response in a ResultBOL class instance
        }

        return null;
    }

    public static function validateAddress($delivery_type, $siteId, $address, $validationMode = 2) {
        if($delivery_type === 1){
            $speedy = new Speedy();
            $isValidAddress = $speedy->validateAddress($siteId, $address, $validationMode);

            return $isValidAddress;
        }

        return null;
    }
}
