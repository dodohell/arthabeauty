<?php
include("globals.php");
include("./classes/class.EcontXMLClient.php");

//$params = array(
//    'system' => array(
//        'validate' => 1, //1 - samo validira dannite, 0 - suzdava pratka 
//        'only_calculate' => 1, //1 - samo kalkulaciq na cena, 0 - suzdava pratka 
//        'response_type' => 'XML', 
//        'email_errors_to' => 'kkalchev@development-bg.com', // e-mail na koito shte se izpratqt greshkite 
//    ),
//    'client' => array(
//        'username' => 'afyabg08',
//        'password' => 'slojnaparola',
//    ), 
//    'loadings' => array( 
//        'row' => array(
//            'sender' => array(
//                'name' => "ime na clienta", 
//                'name_person' => "ime na fiz lice ako clienta e firma", 
//                'email' => 'email na clienta', 
//                'phone' => 'telefon na clienta', 
//                'email_on_delivery' => 'email za notifikaciq pri dostavka na pratkata', 
//                'city'=>'ime na naseleno mqsto', 
//                'post_code' => 'pshtenski kod', 
//                'office_code' => 'kod na ofis',//ako e ot ofis 
//                'quarter' => 'ime na kvartal', 
//                'street' => 'ime na ulica', 
//                'street_num' => 'nomer', 
//                'street_bl' => 'blok', 
//                'street_vh' => 'vhod', 
//                'street_et' => 'etaj', 
//                'street_ap' => 'apartament', 
//                'street_other' => 'dopulnitelna informaciq kum adresa' 
//            ), 
//            'receiver' => array(
//                'name' => "ime na clienta", 
//                'name_person' => "ime na fiz lice ako clienta e firma", 
//                'email' => 'email na clienta', 
//                'phone' => 'telefon na clienta', 
//                'sms_no' => 'telefon za sms notifikaciq pri dostavka na pratkata', 
//                'city'=>'ime na naseleno mqsto', 
//                'post_code' => 'pshtenski kod', 
//                'office_code' => 'kod na ofis',//ako e do ofis 
//                'quarter' => 'ime na kvartal', 
//                'street' => 'ime na ulica', 
//                'street_num' => 'nomer', 
//                'street_bl' => 'blok', 
//                'street_vh' => 'vhod', 
//                'street_et' => 'etaj', 
//                'street_ap' => 'apartament', 
//                'street_other' => 'dopulnitelna informaciq kum adresa' 
//            ), 
//            'payment' => array(
//                'side' => 'SENDER', 
//                'method' => 'CASH', 
//                'key_word' => '' 
//            ), 
//            'shipment' => array(
//                'shipment_type' => 'PACK', 
//                'description' => 'opisanie na pratkata', 
//                'pack_count' => 1,//broi paketi 
//                'weight' => 1, 
//                'tariff_sub_code' => 'DOOR_DOOR',// dali pratkata e ot/do vrata/ofis - DOOR_DOOR, DOOR_OFFICE, OFFICE_DOOR, OFFICE_OFFICE
//                'pay_after_accept' => 1,//poluchatelq moje da pregleda pratkata predi da plati nalojeniq platej 
//                'pay_after_test' => 1,//poluchatelq moje da pregleda i testva pratkata predi da plati nalojeniq platej 
//            ), 
//            'services' => array(
//                'oc' => 18.42,//obqvena stoinost 
//                'oc_currency' => 'BGN',//valuta na obqvenata stoinost 
//                'cd' => 18.42,//nalojen platej 
//                'cd_currency' => 'BGN',//valuta na nalojeniq platej 
//                'cd_agreement_num' => '',//nomer na sporazumenie za izplashtane na nalojen platej (pr. ako shte se izplashta po banka)
//            ), 
//        ) 
//    ) 
//);
$params = array(
    
    'client' => array(
        'username' => 'afyabg08',
        'password' => 'slojnaparola',
    ),
    'request_type' => 'offices'
    
);
$econtClient = new EcontXMLClient();
//dbg($econtClient::request('http://demo.econt.com/e-econt/xml_parcel_import2.php',$params));
dbg($econtClient::request('http://demo.econt.com/e-econt/xml_service_tool.php',$params));