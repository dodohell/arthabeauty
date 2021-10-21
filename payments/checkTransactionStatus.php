<?php

require('eBorica.php');
$transactionCode = '10';
$amount = 10;
$terminalID = '99916492';
$orderID = '123456';
$orderDescription = 'order';
$language = 'BG';
$protocolVersion = '1.0';
$privateKeyFileName = './keys/spermaxEshopTest.key';
$privateKeyPassword = '';
$message = eBorica::generateBOReq($privateKeyFileName, $privateKeyPassword, $transactionCode, $amount, $terminalID, $orderID, $orderDescription, $language, $protocolVersion);
$url = eBorica::testGatewayURL . "transactionStatusReport?eBorica=" . urlencode(base64_encode($message));
$curl = curl_init();
curl_setopt($curl, CURLOPT_URL, $url);
curl_setopt($curl, CURLOPT_VERBOSE, TRUE);
curl_setopt($curl, CURLOPT_RETURNTRANSFER, TRUE);
curl_setopt($curl, CURLOPT_SSLVERSION, 3);
curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, false);
curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
curl_setopt($curl, CURLOPT_HEADER, false);
curl_setopt($curl, CURLOPT_SSLCERT, realpath('../../keys/eTlogcertifikate.pem'));
curl_setopt($curl, CURLOPT_SSLCERTPASSWD, '');
curl_setopt($curl, CURLOPT_SSLKEY, realpath('../../keys/eTlogcertifikate.pem'));
curl_setopt($curl, CURLOPT_SSLKEYPASSWD, '');
$respData = curl_exec($curl);
$curl_error = curl_error($curl);
$curl_info = curl_getinfo($curl);
curl_close($curl);
$resp = eBorica::parseBOResp($respData, '../../keys/ publicKeyAPGWfileName');
print_r($resp);
?>