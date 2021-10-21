<?php
	include("globals.php");
	
	$cart_id = $params->getInt("id");
    $cartsObj = new Carts();
    $tmp_cart = $cartsObj->getCart($cart_id);
    
    $citiesObj = new Cities();
    $districtsObj = new Districts();
    $delivery_city = $citiesObj->getRecord($tmp_cart["delivery_city_id"])["name"];
    $delivery_region = $districtsObj->getRecord($tmp_cart["delivery_region_id"])["name"];
    $billing_city = $citiesObj->getRecord($tmp_cart["billing_city_id"])["name"];
    $billing_region = $districtsObj->getRecord($tmp_cart["billing_region_id"])["name"];
    
    $voucher_discount = $tmp_cart["voucher_discount"];

    if ( $tmp_cart["delivery_type_id"] == 2 || $tmp_cart["delivery_type_id"] == 4 ){
        try {
            $offices_tmp = Delivery::getOfficesByRegionAndCityNames((int)$tmp_cart["delivery_type_id"], $delivery_region, $delivery_city);
        }catch (Exception $e) {
            $file = __FILE__; $line = __LINE__;
            $courierError = $e->getMessage();
            $res = $db->autoExecute($error_log_delivery_table, array("carts_id" => $cart_id, "file" => $file, "line" => $line, "message" => $courierError, "server_info" => serialize($_SERVER), "error_datetime" => date("Y-m-d H:i")), DB_AUTOQUERY_INSERT); safeCheck($res);
            //header("Location: /message.php?code=403");
            die("Възникна проблем. Проверете валидността на данните в полетата и опитайте отново!");
        }
        if ( $tmp_cart["delivery_type_id"] == 2 ){
            foreach($offices_tmp as $k => $v){
                if ( trim($v["id"]) == trim($tmp_cart["delivery_office_id"]) ){
                    $delivery_office = $v["name"];
                }
            }
        }else if ( $tmp_cart["delivery_type_id"] == 4 ){
            foreach($offices_tmp as $k => $v){
                if ( trim($v["code"]) == trim($tmp_cart["delivery_office_id"]) ){
                    $delivery_office = $v["name"];
                }
            }
        }
    }
    
    if ( $tmp_cart["discount_free_delivery"] ){
        // $total_amount_sagepay = $tmp_cart["total_amount"]-$tmp_cart["bonus_points_amount"]-$tmp_cart["discount_amount"];
        $total_amount_sagepay = $tmp_cart["total_amount"]-$tmp_cart["bonus_points_amount"];
    }else{
        // $total_amount_sagepay = $tmp_cart["delivery_amount"]+$tmp_cart["total_amount"]-$tmp_cart["bonus_points_amount"]-$tmp_cart["discount_amount"];
        $total_amount_sagepay = $tmp_cart["delivery_amount"]+$tmp_cart["total_amount"]-$tmp_cart["bonus_points_amount"];
    }
    
    $total_amount = 0;
    $total_items = $tmp_cart["cart_items"];
    
    $message = "";
    $message = '
    <h3>Номер на поръчка #'.$tmp_cart["id"].'</h3>
    <br /><br ><br />
    <table cellpadding="0" width="100%"><tr><td width="45%" valign="top">
        <h3>Информация за доставка</h3>
        
        '.$tmp_cart["delivery_name"].' '.$tmp_cart["delivery_last_name"].'<br />
        '.$tmp_cart["delivery_address_1"].'<br />
        '.$tmp_cart["delivery_address_2"].'<br />
        '.$delivery_city.', '.$delivery_region.'<br />
        '.$tmp_cart["delivery_postcode"].'<br />
        '.$tmp_cart["delivery_country_name"].'<br />
        </td>
        <td width="10%">
        </td>
        <td valign="top" width="45%">
        <h3>Информация за плащане</h3>
        '.$tmp_cart["billing_first_name"].' '.$tmp_cart["billing_last_name"].'<br />
        '.$tmp_cart["billing_address_1"].'<br />
        '.$tmp_cart["billing_address_2"].'<br />
        '.$tmp_cart["billing_phone"].'<br />
        '.$tmp_cart["email"].'<br />
        '.$billing_city.', '.$billing_region.'<br />
        '.$tmp_cart["billing_postcode"].'<br />
        '.$tmp_cart["billing_country_name"].'<br />
        </td>
        </tr>
        </table>
        
        <hr />
        <br />
        ';
        $message .= '<table border="1" width="100%" cellspacing="0">';
        
        foreach($tmp_cart["products"] as $k=>$v){
            if ( $v["product_price_discount"] > 0.0 ){
                $price_show = $v["product_price_discount"];
            }else{
                $price_show = $v["product_price"];
            }
        $message .='<tr>
        <td width="50%">
                <strong>'.$v["product"]["name_en"].", ".$v["brand_name"].", ".$v["product"]["name"].'</strong><br />
                ';

                $message .= ' <strong>'.$v["option"]["name"].'</strong><br />';

            $message.= '</td><td width="15%" valign="top">'.$price_show.'</td>
            <td width="15%" valign="top">'.$price_show.' лв.</td>
            <td width="5%" valign="top">'.$v["quantity"].'</td>
            <td width="15%" valign="top" align="right">
                '.number_format($price_show*$v["quantity"], 2).' лв.
            </td>
            </tr>';
        }
        if ( $tmp_cart["subtotal_amount"] ) {
            $message .= '<tr>
                <td colspan="4">
                    Сума
                </td>
                <td align="right">
                    '.number_format($tmp_cart["subtotal_amount"], 2).' лв.
                </td>
            </tr>';
        }
        if ( $tmp_cart["promo_code"] && $tmp_cart["discount_promo_code_amount"]) {
            $message .= '<tr>
                <td colspan="4">
                    Промо код: ('.$tmp_cart["promo_code"].')
                </td>
                <td align="right">
                    -'.number_format($tmp_cart["discount_promo_code_amount"], 2).' лв.
                </td>
            </tr>';
        }
        if ( $tmp_cart["bonus_points_amount"] > 0.0 ){
            $message .= '<tr>
                <td colspan="4">
                    Изполват се бонус <strong>'.(int)$tmp_cart["bonus_points_number"].'</strong> точки 
                </td>
                <td align="right">
                    -'.$tmp_cart["bonus_points_amount"].' лв.
                </td>
            </tr>';
        }
        if ( $tmp_cart["discount_amount"] > 0.0 && 0 ){
            $message .= '<tr>
                <td colspan="4">
                    Отстъпка
                </td>
                <td align="right">
                    -'.$tmp_cart["discount_amount"].' лв.
                </td>
            </tr>';
        }
        if ( $tmp_cart["discount_free_delivery"] == 1 ){
            if ( $tmp_cart["discount_amount_delivery"] > 0.0 ){
                $message .= '<tr>
                    <td colspan="4">
                        Отстъпка 2% при поръчка над 100 лева
                    </td>
                    <td align="right">
                        -'.$tmp_cart["discount_amount_delivery"].' лв.
                    </td>
                </tr>';
            }
            if ( $tmp_cart["delivery_type_id"] == 2 ){
            $message .= '<tr>
                <td colspan="4">
                    Доставка до офис на Спиди: '.$delivery_office.'
                </td>
                <td align="right">
                    0.00 лв.
                </td>
            </tr>';
            }elseif ( $tmp_cart["delivery_type_id"] == 4 ){
            $message .= '<tr>
                <td colspan="4">
                    Доставка до офис на Еконт: '.$delivery_office.'
                </td>
                <td align="right">
                    0.00 лв.
                </td>
            </tr>';
            }else{
                $message .= '<tr>
                <td colspan="4">
                    Доставка до адрес
                </td>
                <td align="right">
                    0.00 лв.
                </td>
            </tr>';
            }
        }else{
            if ( $tmp_cart["discount_amount_delivery"] > 0.0 ){
                $message .= '<tr>
                    <td colspan="4">
                        Отстъпка 2% при поръчка над 100 лева
                    </td>
                    <td align="right">
                        -'.$tmp_cart["discount_amount_delivery"].' лв.
                    </td>
                </tr>';
            }
            if ( $tmp_cart["delivery_type_id"] == 2 ){
            $message .= '<tr>
                <td colspan="4">
                    Доставка до офис на Спиди: '.$delivery_office.'
                </td>
                <td align="right">
                    '.$tmp_cart["delivery_amount"].' лв.
                </td>
            </tr>';
            }elseif ( $tmp_cart["delivery_type_id"] == 4 ){
            $message .= '<tr>
                <td colspan="4">
                    Доставка до офис на Еконт: '.$delivery_office.'
                </td>
                <td align="right">
                    '.$tmp_cart["delivery_amount"].' лв.
                </td>
            </tr>';
            }else{
                $message .= '<tr>
                <td colspan="4">
                    Доставка до адрес
                </td>
                <td align="right">
                    '.$tmp_cart["delivery_amount"].' лв.
                </td>
            </tr>';
            }
        }
        if ($tmp_cart["voucher_number"]){ 
        $message .= '
        <tr>
            <td colspan="4">
                Voucher Number: "'.$tmp_cart["voucher_number"].'"
            </td>
            <td align="right">
                - '.$voucher_discount.' лв.
            </td>
        </tr>';
        }
        if ($tmp_cart["order_discount_amount"] > 0){
            if ($tmp_cart["order_discount_percent"] > 0){
                $order_discount_percent_string = "(".$tmp_cart["order_discount_percent"]."%)";
            }
            $message .='
            <tr>
                <td colspan="4">
                    Начислена отстъпка върху поръчката: '.$order_discount_percent_string.'
                </td>
                <td align="right">
                    -'.number_format($tmp_cart["order_discount_amount"],2).' лв.
                </td>
            </tr>';
        }
        $message .='
        <tr>
            <td colspan="4">
                Общо
            </td>
            <td align="right">
                '.number_format($total_amount_sagepay-$tmp_cart["discount_amount_delivery"],2).' лв.
            </td>
        </tr>
        </table>';

    $contacts		= getFromCommon($db,$common_table,$lng,'contacts') ;
    $copyrights		= getFromCommon($db,$common_table,$lng,'copyrights') ;
    $subject = "Финализирана поръчка #".$cart_id;

    $message_text = '<html>
                            <head>
                                <title>'.$message_heading.'</title>
                            </head>
                            <body>
                                <table width="100%" cellpadding="0" cellspacing="0">
                                    <tr>
                                        <td valign="top" width="240">
                                            <a href="'.$host.'" target="_blank"><img src="'.$host.'images/logoMobile.png" border="0" /></a>
                                            <br>
                                        </td>
                                        <td valign="top">
                                            <span style="font-size: 11px;">
                                            '.$contacts["description"].'
                                            <br>
                                            </span>
                                            <br>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td colspan="2" height="10" bgcolor="#212529">
                                        </td>
                                    </tr>
                                </table>

                                <table width="100%" cellpadding="0" cellspacing="0">
                                    <tr>
                                        <td>
                                            <br><br><br>
                                            '.$message.'
                                            <br><br><br>
                                        </td>
                                    </tr>
                                </table>
                                <table width="100%" cellpadding="5" cellspacing="0">
                                    <tr>
                                        <td valign="top" bgcolor="#212529">
                                            <span style="color: #ffffff!important; font-size: 11px;">'.$copyrights["description"].'</span>
                                        </td>
                                    </tr>
                                </table>
                            </body>
                            </html>';

//	include("MPDF54/mpdf.php");
//	ini_set("memory_limit","128M");
//	ob_start();
//	echo $message_text;
//	
//	$html = ob_get_clean();
	$mpdf = new \Mpdf\Mpdf(); 
	$mpdf->useAdobeCJK = true;		// Default setting in config.php
							// You can set this to false if you have defined other CJK fonts
//	$mpdf->SetAutoFont(AUTOFONT_ALL);	//	AUTOFONT_CJK | AUTOFONT_THAIVIET | AUTOFONT_RTL | AUTOFONT_INDIC	// AUTOFONT_ALL
							// () = default ALL, 0 turns OFF (default initially)
	$mpdf->WriteHTML($message_text);
	$mpdf->Output('carts_orders/order-'.$tmp_cart["id"].'.pdf', 'F'); 
	//$mpdf->Output(); 
	$file = 'carts_orders/order-'.$tmp_cart["id"].'.pdf';
	
	header('Content-Description: File Transfer');
	header('Content-Type: application/vnd.pdf');
	header('Content-Disposition: attachment; filename='.basename($file));
	header('Content-Transfer-Encoding: binary');
	header('Expires: 0');
	header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
	header('Pragma: public');
	header('Content-Length: ' . filesize($file));
	ob_clean();
	flush();
	readfile($file);
	exit;
	
?>