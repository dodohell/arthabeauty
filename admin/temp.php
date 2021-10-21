<?php


public function generateBOL($row) {
    global $db;
    global $lng;
    global $host;
    global $carts_table;
    global $carts_products_table;
    global $products_table;
    global $variants_table;
    global $useBonusPoints;
    global $error_log_delivery_table;

    $bonusPointsAmount = $useBonusPoints ? $row["bonus_points_amount"] : 0.0;
    if ( $row["discount_free_delivery"] ){
        $total_amount = $row["total_amount"] - $bonusPointsAmount - $row["discount_amount_delivery"] ;
    }else{
        $total_amount = $row["total_amount"] - $bonusPointsAmount;
    }

    if($row["discount_free_delivery"] == 1){
        $free_delivery = 1;
    }

    if ( trim($_REQUEST["delivery_tracking_info"]) ){
        $fields["delivery_tracking_info"] = $_REQUEST["delivery_tracking_info"];
    }

    $tmp_row = $db->getRow("SELECT * FROM ".$carts_table." WHERE id = '".$row["id"]."'"); safeCheck($tmp_row);

    $cart_tmp2 = $db->getAll("SELECT * FROM ".$carts_products_table." WHERE edate = 0 AND cart_id = '".$row["id"]."'"); safeCheck($cart_tmp2);
    $cart_weight = 0;
    foreach($cart_tmp2 as $k => $v){
        $product = $db->getRow("SELECT *, name_{$lng} AS name FROM ".$products_table." WHERE edate = 0 AND id = '".$v["product_id"]."'"); safeCheck($product);
        if ($v["variant_id"]){
            $option_id = $v["variant_id"];
            $variant = $db->getRow("SELECT * FROM ".$variants_table." WHERE id = '".$option_id."'"); safeCheck($variant);
            $cart_weight += $v["quantity"] * $variant["weight"];
        }else{
            $cart_weight += $v["quantity"] * $product["weight"];
        }
        $cart_tmp2[$k] = $v;
    }

    $total_amount_package = $total_amount;
    if ( !$free_delivery ){
        if ( $row["payment_type_id"] != 2 ){
            $total_amount_package = $total_amount_package + $row["delivery_amount"];
        }
    }
    if($row["payment_type_id"] == 2){
        $total_amount_package = 0;
    }

    $deliveryCompanyName = "";
    if ( $row["delivery_company_name"] ){
        $receiverCompanyName = $row["delivery_company_name"];
    }
    $receiverName = $row["delivery_first_name"]." ".$row["delivery_last_name"];

    $districtsObj = new Districts();
    $citiesObj = new Cities();
    $districtName = $districtsObj->getRecord($row["delivery_region_id"])["name"];
    $citiesName = $citiesObj->getRecord($row["delivery_city_id"])["name"];

    try {
        $courierReceiverSiteId = Delivery::getCourierSiteId((int)$row["delivery_type_id"], $districtName, $citiesName);
        $delivery_tracking_office = (int)$_REQUEST["delivery_tracking_office"];

        $senderBringToOffice = false;
//                $senderBringToOffice = true;
        if($delivery_tracking_office > 0){
            $senderBringToOffice = true;
        }
        $deferredDeliveryWorkDays = (int)$_REQUEST["deferred_delivery_work_days"] > 0 ? (int)$_REQUEST["deferred_delivery_work_days"] : 0;
        $optionsBeforePayment = isset($_REQUEST["options_before_payment"]) && (int)$_REQUEST["options_before_payment"] > 0 ? true : false;

        if ( $row["delivery_office_id"] && ($row["delivery_type_id"] == 2 || $row["delivery_type_id"] == 4) ){
            $resultBOL = Delivery::createBillOfLading((int)$row["delivery_type_id"], 0, $total_amount_package, $fields["delivery_tracking_info"], $row["weight"], null, null, $receiverName, $receiverCompanyName, $row["billing_phone"], $row["delivery_office_id"], $deferredDeliveryWorkDays, $optionsBeforePayment, $delivery_tracking_office, $senderBringToOffice);
        }elseif ( $row["delivery_type_id"] == 1 || $row["delivery_type_id"] == 3){
            $resultBOL = Delivery::createBillOfLading((int)$row["delivery_type_id"], 0, $total_amount_package, $fields["delivery_tracking_info"], $row["weight"], $courierReceiverSiteId, $row["delivery_address_1"], $receiverName, $receiverCompanyName, $row["billing_phone"], null, $deferredDeliveryWorkDays, $optionsBeforePayment, $delivery_tracking_office, $senderBringToOffice);
        }

        if(!isset($resultBOL["errorMessage"])){
            $pdfURL = '';
            if ( $row["delivery_type_id"] == 1 || $row["delivery_type_id"] == 2 ){
                $delivery_tracking_number = $resultBOL->getGeneratedParcels()[0]->getParcelId();
                $fileName = Delivery::printBillOfLadingPDF((int)$row["delivery_type_id"], $delivery_tracking_number);
            }else if( $row["delivery_type_id"] == 3 || $row["delivery_type_id"] == 4 ){
                $delivery_tracking_number = $resultBOL["label"]["shipmentNumber"];
                $pdfURL = $resultBOL["label"]["pdfURL"];
                // $fileName = Delivery::printBillOfLadingPDF((int)$row["delivery_type_id"], $delivery_tracking_number, $pdfURL);
            }

            $delivery_tracking_pdf_url = $pdfURL;
            // if($fileName){
            //     if ( $row["delivery_type_id"] == 1 || $row["delivery_type_id"] == 2 ){
            //         $delivery_tracking_pdf_url = $host."admin/showSpeedyBolPdf.php?file_name=$fileName";
            //     }else if( $row["delivery_type_id"] == 3 || $row["delivery_type_id"] == 4 ){
            //         $delivery_tracking_pdf_url = $host."admin/showEcontBolPdf.php?file_name=$fileName";
            //     }
            // }

            $fields["deferred_delivery_work_days"] = $deferredDeliveryWorkDays;
            $fields["options_before_payment"] = $optionsBeforePayment;

            $fields["delivery_tracking_number"] = $delivery_tracking_number;
            $fields["delivery_tracking_pdf"] = $delivery_tracking_pdf_url;

            //$fields["delivery_tracking_number_test"] = $delivery_tracking_number;
            //$fields["delivery_tracking_pdf_test"] = $delivery_tracking_pdf_url;
            $fields["delivery_tracking_office"] = $delivery_tracking_office;

            $res = $db->autoExecute($carts_table, $fields, DB_AUTOQUERY_UPDATE, " id = '".$row["id"]."' "); safeCheck($res);
        }
    }catch (Exception $e) {
        $file = __FILE__; $line = __LINE__;
        $speedyErrorCode = $e->getCode();
        $speedyError = $e->getMessage();
        $res = $db->autoExecute($error_log_delivery_table, array("carts_id" => $row["id"], "file" => $file, "line" => $line, "message" => $speedyError, "server_info" => serialize($_SERVER), "error_datetime" => date("Y-m-d H:i")), DB_AUTOQUERY_INSERT); safeCheck($res);
        if( strpos( $speedyError, "OPTIONS_BEFORE_PAYMENT_NOT_ALLOWED_FOR_APT" ) ) {
            die("Опциите преди плащане не са позволени при вземане от Автомат. Моля премахнете Преглед преди плащане или сменете офиса.");
        }
        //header("Location: /message.php?code=403");
        die("Възникна проблем. Проверете валидността на данните в полетата и опитайте отново!4<br>Message: ".$speedyError."<br>Code: ".$speedyErrorCode);
    }

    return $resultBOL;
}