<?php


class eBoricaV2 {

    public static function generateBOReq($privateKeyFileName, $privateKeyPassword, $amount, $terminalID, $orderID, $merchantID) {
        date_default_timezone_set('UTC');
        // Основни данни за извършване на електронното плащане: ORDER, AMOUNT, DESC
        $ORDER = str_pad($orderID, 6, "0", STR_PAD_LEFT); // Номер поръчка, 6 знака с водещи нули 
        $AD_CUST_BOR_ORDER_ID = $ORDER."@".$orderID;
        $AMOUNT = number_format($amount, 2, '.', ''); // Сума на плащането, Формат: xx.xx,  Пример: 12.34
        $DESC   = 'Козметика'; // Описание на плащането, Пример: "Тестова поръчка"
        // Допълнителни данни за извършване на електронното плащане
        $TRTYPE = "1"; // Тип на транзацията
        $CURRENCY = "BGN"; // Валута на плащането
        $TERMINAL = $terminalID; // Идентификатор на терминала получен от БОРИКА 
        $MERCHANT = $merchantID; // Идентификатор на търговеца получен от БОРИКА 
        $TIMESTAMP = date("YmdHis"); // a) Формат: YYYYMMDDHHMMSS

        // Формиране на сигнатура за подписване
        $NONCE = strtoupper(bin2hex(openssl_random_pseudo_bytes(16))); 
        // MAC_EXTENDED = TERMINAL, TRTYPE, AMOUNT, CURRENCY, ORDER, MERCHANT, TIMESTAMP, NONCE
        $P_SIGN = 
        strlen($TERMINAL).$TERMINAL.
        strlen($TRTYPE).$TRTYPE.
        strlen($AMOUNT).$AMOUNT.
        strlen($CURRENCY).$CURRENCY.
        strlen($ORDER).$ORDER.
        strlen($MERCHANT).$MERCHANT.
        strlen($TIMESTAMP).$TIMESTAMP.
        strlen($NONCE).$NONCE;
        
        $fp = fopen($privateKeyFileName, "r"); 
        $private_key = fread($fp, filesize($privateKeyFileName)); 
        fclose($fp); 

        // Подписване на съобщението с цифров сертификат
        $private_key_id = openssl_get_privatekey($private_key, $privateKeyPassword); 
        openssl_sign($P_SIGN, $signature, $private_key_id, OPENSSL_ALGO_SHA256);   
        openssl_free_key($private_key_id);   

        // Формиране окончателна подписана сигнатура
        $P_SIGN = strtoupper(bin2hex($signature)); 

        // Отпечатване на формуляр за извършване на плащане
        return '
        <!-- Фиксирани -->
        <input type="hidden" name="TRTYPE" value="1" />
        <input type="hidden" name="COUNTRY" value="BG" />
        <input type="hidden" name="CURRENCY" value="BGN" />
        <input type="hidden" name="ADDENDUM" value="AD,TD" />
        <input type="hidden" name="MERCH_GMT" value="+03" />
        <input type="hidden" name="MERCH_NAME" value="Артха Бюти ЕООД" />
        <input type="hidden" name="MERCH_URL" value="https://arthabeauty.bg/" />
        <input type="hidden" name="EMAIL" value="info@arthabeauty.bg" />
        <!-- Основни -->
        <input type="hidden" name="ORDER"  value="'.$ORDER.'" />
        <input type="hidden" name="AMOUNT" value="'.$AMOUNT.'" />
        <input type="hidden" name="DESC"  value="'.$DESC.'" />
        <input type="hidden" name="TIMESTAMP" value="'.$TIMESTAMP.'" />
        <input type="hidden" name="LANG" value="BG" />
        <!-- Допълнителни -->
        <input type="hidden" name="TERMINAL" value="'.$TERMINAL.'" />
        <input type="hidden" name="MERCHANT" value="'.$MERCHANT.'" />
        <input type="hidden" name="AD.CUST_BOR_ORDER_ID" value="'.$AD_CUST_BOR_ORDER_ID.'" />
        <!-- Сигнатури -->
        <input type="hidden" name="NONCE" value="'.$NONCE.'" />
        <input type="hidden" name="P_SIGN" value="'.$P_SIGN.'" />
        ';
    }

    public static function parseBOResp($params, $boricaPublicKey) {
        date_default_timezone_set('UTC');
        $ACTION = strlen($params->get('ACTION')) > 0 ? strlen($params->get('ACTION')).$params->get('ACTION') : "-";
        $RC = strlen($params->get('RC')) > 0 ? strlen($params->get('RC')).$params->get('RC') : "-";
        $APPROVAL = strlen($params->get('APPROVAL')) > 0 ? strlen($params->get('APPROVAL')).$params->get('APPROVAL') : "-";
        $TERMINAL = strlen($params->get('TERMINAL')) > 0 ? strlen($params->get('TERMINAL')).$params->get('TERMINAL') : "-";
        $TRTYPE = strlen($params->get('TRTYPE')) > 0 ? strlen($params->get('TRTYPE')).$params->get('TRTYPE') : "-";
        $AMOUNT = strlen($params->get('AMOUNT')) > 0 ? strlen($params->get('AMOUNT')).$params->get('AMOUNT') : "-";
        $CURRENCY = strlen($params->get('CURRENCY')) > 0 ? strlen($params->get('CURRENCY')).$params->get('CURRENCY') : "-";
        $ORDER = strlen($params->get('ORDER')) > 0 ? strlen($params->get('ORDER')).$params->get('ORDER') : "-";
        $RRN = strlen($params->get('RRN')) > 0 ? strlen($params->get('RRN')).$params->get('RRN') : "-";
        $INT_REF = strlen($params->get('INT_REF')) > 0 ? strlen($params->get('INT_REF')).$params->get('INT_REF') : "-";
        $PARES_STATUS = strlen($params->get('PARES_STATUS')) > 0 ? strlen($params->get('PARES_STATUS')).$params->get('PARES_STATUS') : "-";
        $ECI = strlen($params->get('ECI')) > 0 ? strlen($params->get('ECI')).$params->get('ECI') : "-";
        $TIMESTAMP = strlen($params->get('TIMESTAMP')) > 0 ? strlen($params->get('TIMESTAMP')).$params->get('TIMESTAMP') : "-";
        $NONCE = strlen($params->get('NONCE')) > 0 ? strlen($params->get('NONCE')).$params->get('NONCE') : "-";

        // data & p_sign
        $DATA = $ACTION.$RC.$APPROVAL.$TERMINAL.$TRTYPE.$AMOUNT.$CURRENCY.$ORDER.$RRN.$INT_REF.$PARES_STATUS.$ECI.$TIMESTAMP.$NONCE;
        $DATA = rtrim($DATA,"-"); // Fix FW: update Borica EMV 3DS ver 2.2 from 22.10.2020
        // Сигнатура
        $P_SIGN = hex2bin($params->get('P_SIGN'));
        
        // Отваряне на файла съдържащ цифровия публичен ключ 
        $fp = fopen($boricaPublicKey, "r"); 
        $public_key = fread($fp, filesize($boricaPublicKey)); 
        fclose($fp); 

        // Подписване на съобщението с цифров сертификат
        $public_key_id = openssl_get_publickey($public_key); 
        $ssl_verification = openssl_verify($DATA, $P_SIGN, $public_key_id, OPENSSL_ALGO_SHA256);  
        openssl_free_key($public_key_id);   

        // Проверка за валидност?
        if($ssl_verification != 1) die("<h1>SSL Verification Error!</h1><p>".openssl_error_string()."</p>");

        // Записване на хронология в JavaScript Object Notation (JSON) файл
        // file_put_contents("$ORDER.json", json_encode($_REQUEST)); 
        
        return [
            'ACTION' => $params->get('ACTION'),
            'RC' => $params->get('RC'),
            'APPROVAL' => $params->get('APPROVAL'),
            'TERMINAL' => $params->get('TERMINAL'),
            'TRTYPE' => $params->get('TRTYPE'),
            'AMOUNT' => $params->get('AMOUNT'),
            'CURRENCY' => $params->get('CURRENCY'),
            'ORDER' => $params->get('ORDER'),
            'RRN' => $params->get('RRN'),
            'INT_REF' => $params->get('INT_REF'),
            'PARES_STATUS' => $params->get('PARES_STATUS'),
            'TIMESTAMP' => $params->get('TIMESTAMP')
        ];
    }

}
