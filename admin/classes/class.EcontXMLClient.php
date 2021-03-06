<?php

class EcontXMLClient {
    
    public static function request($url, $params = array(),$timeout = 10) {
        $request = new \SimpleXMLElement('<?xml version="1.0" encoding="UTF-8"?><request></request>');
        self::array2XMLNode($params,$request);
        $ch = curl_init($url);
        
        curl_setopt_array($ch,array(
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_TIMEOUT => $timeout,
            CURLOPT_POSTFIELDS => array(
                'xml' => $request->asXML()
            )
        ));
        $r = curl_exec($ch);
        
        if($r === false) throw new Exception("Connection error."); 
        //ako potrebitelq ili parolata ne sa verni, rezultata e HTML!! 
        return json_decode(json_encode(new \SimpleXMLElement($r)),true); //poor man's XML2Array 
    } 
    
    public static function array2XMLNode($array, \SimpleXMLElement $parentNode) {
        if(!is_array($array)) return; 
        
        foreach($array as $k => $v) {
            if(is_array($v)) {
                if(!array_key_exists(0,$v)) $vv = array($v);
                else $vv = $v;
                
                foreach ($vv as $vvv) {
                    self::array2XMLNode($vvv,$parentNode->addChild($k));
                }
            } else {
                $parentNode->addChild($k,$v);
            }
        }
    }
}
