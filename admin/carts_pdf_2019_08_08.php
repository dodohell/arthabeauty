<?php
	include("globals.php");
	include("./classes/class.econt.php");
	
	$id = $params->getInt("id");
		$cart_id = $id;
		$tmp_cart = $db->getRow("SELECT * FROM ".$carts_table." WHERE id = '".$cart_id."'");
        
		if ($tmp_cart["postdate"] < 1351704368){
			$sql_cart_use = " session_id = '".$tmp_cart["session_id"]."' ";
		}else{
			$sql_cart_use = " cart_id = '".$tmp_cart["id"]."' ";
		}
		
		$user_info = $db->getRow("SELECT * 
								FROM ".$carts_user_table." AS users
								WHERE ".$sql_cart_use." ORDER BY id DESC"); safeCheck($user_info);
        
		$sm->assign("user_info", $user_info);
		
		
		$country = $db->getRow("SELECT * FROM ".$countries_table." WHERE id = '".$user_info["billing_country_id"]."'"); safeCheck($country);
		$group_id = $country["group_id"];
		
		$delivery_country = $db->getRow("SELECT * FROM ".$countries_table." WHERE id = '".$user_info["country_id"]."'"); safeCheck($delivery_country);
		
		$group_id = $delivery_country["group_id"];
		
		$products_voucher = $db->getAll("SELECT DISTINCT vouchers.*, ptv.product_id
								FROM ".$vouchers_table." AS vouchers, 
									 ".$product_to_voucher_table." AS ptv
								WHERE vouchers.edate = 0
								AND vouchers.id = ptv.voucher_id
								AND vouchers.number = '".$tmp_cart["voucher_number"]."'
								"); safeCheck($products_voucher);
		
		$cart = $db->getAll("SELECT * FROM ".$carts_products_table." WHERE edate = 0 AND cart_id = '".$cart_id."'"); safeCheck($cart);
		$total_weight = 0;
		$total_postage_points = 0;
		foreach($cart as $k => $v){
			$option = $db->getRow("SELECT *, option_text AS name, (SELECT og.name_{$lng} FROM ".$option_groups_table." AS og WHERE og.id = options.option_group_id) AS option_group_name FROM ".$options_table." AS options	WHERE options.id = '".$v["variant_id"]."'"); safeCheck($option);
			$v["option"] = $option;
			
			$v["cart_price"] = $v["product_price"];
			$v["cart_price_clear"] = number_format($v["cart_price"], 2);
			
			
			$v["cart_price"] = number_format($v["cart_price"],2);
			$v["cart_price_clear"] = number_format($v["cart_price_clear"]*$v["quantity"],2);
			$v["cart_total_product"] = number_format($v["cart_price_w_vat"]*$v["quantity"],2);
			
			
			
			$v["options_personalised"] = $options_personalised;
			$v["options"] = $options;
			$v["option"] = $option;
			$v["options_string"] = unserialize($v["options_string"]);
			$product = $db->getRow("SELECT *, name_{$lng} AS name FROM ".$products_table." AS products WHERE edate = 0 AND id = '".$v["product_id"]."'"); safeCheck($product);
			$v["product"] = $product;
			$total_weight += $product["product_weight"]*$v["quantity"];
			$total_postage_points += $product["postage_points"]*$v["quantity"];
			$v["cart_price"] = number_format($v["product_price"]+$v["choices_price"],2);
			$vat_norounding = $v["cart_price"]*5/6;
			$v["cart_price_clear"] = number_format((($v["cart_price"]*5)/6), 2);
			$v["cart_price_w_vat"] = number_format($vat_norounding+($vat_norounding*$vat["vat_percent"])/100, 2);
			$v["cart_price_w_vat_total"] = number_format($v["cart_price_w_vat"]*$v["quantity"],2);
			
			foreach($products_voucher as $kk => $vv){
				$cart_price_discount = 0;
				$cart_price_clear_discount = 0;
				$cart_price_w_vat_discount = 0;
				if ($v["product_id"] == $vv["product_id"]){
					if ($vv["delivery_international"] == "0" && $vv["delivery_uk"] == "0"){
						if ($vv["percent"]){
							$cart_price_discount = $v["cart_price"]*$v["quantity"]*$vv["percent"]/100;
							// $cart_price_clear_discount = $v["cart_price_clear"]*$vv["percent"]/100;
							// $cart_price_w_vat_discount = $v["cart_price_w_vat"]*$vv["percent"]/100;
						}
						if ($vv["price"]){
							$cart_price_discount = $v["quantity"]*$vv["price"];
						}
						$voucher_discount += $cart_price_discount;
					}
				}
			}
			
			$cart[$k] = $v;
		}
		
	
	
		$sm->assign("delivery_type", $delivery_type);
		$sm->assign("delivery_price", number_format($delivery_price_use,2));
		
		$sm->assign("voucher_discount", number_format($voucher_discount,2));
		$voucher_discount = $tmp_cart["voucher_discount"];
		
		
		if ( $user_info["delivery_type_id"] == 2 ){
			$econtDelivery = new econt($econt_user, $econt_pass);
		
			$offices_tmp = $econtDelivery->getOffices();
			// dbg($cities_tmp);
			foreach($offices_tmp as $k => $v){
				if ( trim($v["id"]) == trim($tmp_cart["delivery_office_econt"]) ){
					$delivery_office = $v["name"];
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
		$total_items = sizeof($cart);
		
		$message = "";
		$message = '
		<h3>Номер на поръчка #'.$tmp_cart["id"].'</h3>
		<br /><br ><br />
		<table cellpadding="0" width="100%"><tr><td width="45%" valign="top">
			<h3>Информация за доставка</h3>
			
			'.$tmp_cart["delivery_name"].' '.$tmp_cart["delivery_last_name"].'<br />
			'.$tmp_cart["delivery_address_1"].'<br />
			'.$tmp_cart["delivery_address_2"].'<br />
			'.$tmp_cart["delivery_city"].', '.$tmp_cart["delivery_region"].'<br />
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
			'.$tmp_cart["billing_city"].', '.$tmp_cart["billing_region"].'<br />
			'.$tmp_cart["billing_postcode"].'<br />
			'.$tmp_cart["billing_country_name"].'<br />
			</td>
			</tr>
			</table>
			
			<hr />
			<br />
			';
			$message .= '<table border="1" width="100%" cellspacing="0">';
			foreach($cart as $k=>$v){
				if ( $v["product_price_discount"] > 0.0 ){
					$price_show = $v["product_price_discount"];
				}else{
					$price_show = $v["product_price"];
				}
			$message .='<tr>
			<td width="50%">
					<strong>'.$v["product"]["name"].'</strong><br />
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
						<td>
							Отстъпка 2% при поръчка над 100 лева
						</td>
						<td>		
							&nbsp;
						</td>
						<td>			
							 &nbsp;
						</td>
						<td>
							&nbsp;
						</td>
						<td align="right">
							-'.$tmp_cart["discount_amount_delivery"].' лв.
						</td>
					</tr>';
				}
				if ( $user_info["delivery_type_id"] == 2 ){
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
					<td>
						Доставка до адрес
					</td>
					<td>		
						&nbsp;
					</td>
					<td>			
						 &nbsp;
					</td>
					<td>
						&nbsp;
					</td>
					<td align="right">
						0.00 лв.
					</td>
				</tr>';
				}
			}else{
                
//                echo "<pre>";
//                var_dump($user_info);
//                echo "</pre>";
//                exit();
                
				if ( $user_info["delivery_type_id"] == 2 ){
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
					<td>
						Доставка до адрес
					</td>
					<td>		
						&nbsp;
					</td>
					<td>			
						 &nbsp;
					</td>
					<td>
						&nbsp;
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
				<td>
					Voucher Number: "'.$tmp_cart["voucher_number"].'"
				</td>
				<td>		
					&nbsp;
				</td>
				<td>			
					&nbsp;
				</td>
				<td>
					&nbsp;
				</td>
				<td align="right">
					- '.$voucher_discount.' лв.
				</td>
			</tr>';
			}
			$message .='
			<tr>
				<td colspan="4">
					Общо
				</td>
				<td align="right">
					<!--'.number_format($total_amount_sagepay-$tmp_cart["discount_amount_delivery"],2).' лв.-->
					'.number_format($total_amount_sagepay,2).' лв.
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
												<a href="'.$host.'" target="_blank"><img src="'.$host.'images/logo.png" border="0" /></a>
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
											<td colspan="2" height="10" bgcolor="#3A5795">
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
											<td valign="top" bgcolor="#3A5795">
												<span style="color: #ffffff!important; font-size: 11px;">'.$copyrights["description"].'</span>
											</td>
										</tr>
									</table>
								</body>
								</html>';
	
	
	//include("MPDF54/mpdf.php");
	//ini_set("memory_limit","128M");
	//ob_start();
	//echo $message_text;
	
	//$html = ob_get_clean();
    
	$mpdf = new \Mpdf\Mpdf();
	$mpdf->useAdobeCJK = true;		// Default setting in config.php
							// You can set this to false if you have defined other CJK fonts
	//$mpdf->SetAutoFont(AUTOFONT_ALL);	//	AUTOFONT_CJK | AUTOFONT_THAIVIET | AUTOFONT_RTL | AUTOFONT_INDIC	// AUTOFONT_ALL
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