<?php

require('eBorica.php');
$transactionCode = '10';
$transactionStatusFlag = '0';
$amount = 3;
$terminalID = '92000279';
$orderID = '001';
$orderDescription = 'order';
$language = 'BG';
$protocolVersion = '1.0';
$privateKeyFileName = './keys/private_key_test.key';
$privateKeyPassword = '97531';
$message = eBorica::generateBOReq($privateKeyFileName, $privateKeyPassword, $transactionCode, $amount, $terminalID, $orderID, $orderDescription, $language, $protocolVersion);

$url = eBorica::testGatewayURL . "registerTransaction?eBorica=" . urlencode(base64_encode($message));

header('Location: ' . $url);
exit;
?>