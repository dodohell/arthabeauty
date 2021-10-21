<?php

class eBorica {
    const TRANSACTION_CODE = 'TRANSACTION_CODE';
    const TRANSACTION_TIME = 'TRANSACTION_TIME';
    const AMOUNT = 'AMOUNT';
    const TERMINAL_ID = 'TERMINAL_ID';
    const ORDER_ID = 'ORDER_ID';
    const RESPONSE_CODE = 'RESPONSE_CODE';
    const PROTOCOL_VERSION = 'PROTOCOL_VERSION';
    const SIGN = 'SIGN';
    const SIGNATURE_OK = 'SIGNATURE_OK';
    const gatewayURL = 'https://gate.borica.bg/boreps/';
    const testGatewayURL = 'https://gatet.borica.bg/boreps/';

    public static function generateBOReq($privateKeyFileName, $privateKeyPassword, $transactionCode, $amount, $terminalID, $orderID, $orderDescription, $language, $protocolVersion = '1.0', $currency = null, $ott = null) {
        $message = $transactionCode;
        $message .= date("YmdHis");
        $amount *= 100;
        $message .= str_pad($amount, 12, "0", STR_PAD_LEFT);
        $message .= $terminalID;
        $message .= str_pad(substr($orderID, 0, 15), 15);
        $message .= str_pad(substr($orderDescription, 0, 125), 125);
        $message .= ($language == 'BG' || $language == 'EN') ? $language : 'EN';
        $message .= ($protocolVersion == '1.0' || $protocolVersion == '1.1' || $protocolVersion == '2.0') ? $protocolVersion : '1.0';
        if ($protocolVersion != '1.0') {
            $message .= strtoupper($currency);
        }
        if ($protocolVersion == '2.0') {
            $message .= str_pad($ott, 6);
        }
        
        $fp = fopen($privateKeyFileName, "r");
        
        $priv_key = fread($fp, filesize($privateKeyFileName)); //fread($fp, 8192); //fread($fp, filesize($privateKeyFileName))
        fclose($fp);
        
        $pkeyid = openssl_pkey_get_private($priv_key, $privateKeyPassword);

        openssl_sign($message, $signature, $pkeyid);
        
        openssl_free_key($pkeyid);
        $message .= $signature;

        return $message;
    }

    public static function parseBOResp($message, $publicKeyAPGWfileName) {
        $message = base64_decode($message);
        $response[self::TRANSACTION_CODE] = substr($message, 0, 2);
        $response[self::TRANSACTION_TIME] = substr($message, 2, 14);
        $response[self::AMOUNT] = substr($message, 16, 12);
        $response[self::TERMINAL_ID] = substr($message, 28, 8);
        $response[self::ORDER_ID] = substr($message, 36, 15);
        $response[self::RESPONSE_CODE] = substr($message, 51, 2);
        $response[self::PROTOCOL_VERSION] = substr($message, 53, 3);
        $response[self::SIGN] = substr($message, 56, 128);
        $fp = fopen($publicKeyAPGWfileName, "r");
        $cert = fread($fp, 8192);
        fclose($fp);
        $pubkeyid = openssl_get_publickey($cert);
        $response[self::SIGNATURE_OK] = openssl_verify(substr($message, 0, strlen($message) - 128), $response[self::SIGN], $pubkeyid);
        openssl_free_key($pubkeyid);
        return $response;
    }

}
