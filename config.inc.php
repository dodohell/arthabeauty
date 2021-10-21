<?php
	/******************* Instal Path ****************/

	$install_path	= $_SERVER["DOCUMENT_ROOT"]."/";
// 	$install_path = '/home/dimitar/Code/arthabeauty/';

	date_default_timezone_set("Europe/Sofia");

	/****************** Smarty directories **************/
	$template_dir	= $install_path."/templates/";
	$template_c_dir	= $install_path."/templates_c/";

	/****************** Default Language ***************/
	$lng = 'bg';


	/****************** DB Settings ********************/
	$db_schema	= "mysqli";
	$db_host	= "localhost";
	$db_user	= "arthabea_website";
	$db_pass	= "_887Tlw@ADQ-";
	$db_db		= "arthabea_website";

	$separator	= ":";
	$prefix		= "arthabeauty_";

    /*************** DB tables afya ************************/
    $competition_table							= $prefix . "competition";
	$static_info_table							= $prefix . "static_info";
	$static_images_table 						= $prefix . "static_images";
	$general_images_table 						= $prefix . "general_images";
	$picture_to_static_info_table				= $prefix . "pictures_to_static_info";
	$static_images2_table 						= $prefix . "static_images2";
	$static_files_table 						= $prefix . "static_files";
	$common_table								= $prefix . "common";
	$logos_table								= $prefix . "logos";
	$categories_table							= $prefix . "categories";
	$districts_table							= $prefix . "districts";
	$cities_table								= $prefix . "cities";
	$cities_econt_table							= $prefix . "cities_econt";
	$companies_table							= $prefix . "companies";
	$services_companies_table					= $prefix . "services_companies";
	$advertise_services_companies_table			= $prefix . "advertise_services_companies";
	$users_table								= $prefix . "users";
	$users_addresses_table						= $prefix . "users_addresses";
	$xml_feeds_table							= $prefix . "xml_feeds";
	$services_xml_feeds_table					= $prefix . "services_xml_feeds";
	$services_products_table					= $prefix . "services_products";
	$products_table								= $prefix . "products";
	$product_to_category_table					= $prefix . "product_to_category";
	$online_shop_to_category_table 				= $prefix . "online_shop_to_category";
	$clicks_table								= $prefix . "clicks";
	$clicks_services_table						= $prefix . "clicks_services";
	$insurances_table							= $prefix . "insurances";
	$affiliates_table							= $prefix . "affiliates";
	$ratings_table								= $prefix . "ratings";
	$enquiries_table							= $prefix . "enquiries";
	$specialoffers_table						= $prefix . "specialoffers";
	$banners_table								= $prefix . "banners";
	$zones_table								= $prefix . "zones";
	$countries_table							= $prefix . "countries";
	$manifacturers_table						= $prefix . "manifacturers";
	$orders_table 								= $prefix . "orders";
	$invoices_table 							= $prefix . "invoices";
	$newsletter_emails_table					= $prefix . "newsletter_emails";
	$newsletters_table							= $prefix . "newsletters";
	$products_images_table						= $prefix . "products_images";
	$products_files_table						= $prefix . "products_files";
	$product_to_product_table					= $prefix . "product_to_product";
	$delivery_prices_table						= $prefix . "delivery_prices";
	$fonts_table								= $prefix . "fonts";
	$product_to_font_table						= $prefix . "product_to_font";
	$choices_table								= $prefix . "choices";
	$options_table								= $prefix . "options";
	$carts_table								= $prefix . "carts";
	$carts_products_table						= $prefix . "carts_products";
	$carts_products_options_table				= $prefix . "carts_products_options";
	$products_rating_table						= $prefix . "products_rating";
	$products_comments_table					= $prefix . "products_comments";
	$vouchers_table								= $prefix . "vouchers";
	$product_to_voucher_table					= $prefix . "product_to_voucher";
	$product_to_aroma_table						= $prefix . "product_to_aroma";
	$product_to_product_type_table				= $prefix . "product_to_product_type";
	$product_to_price_limit_table				= $prefix . "product_to_price_limit";
	$carts_user_table							= $prefix . "carts_user";
	$users_admin_table							= $prefix . "users_admin";
	$users_admin_to_menus_table					= $prefix . "users_admin_to_menus";
	$delivery_types_table						= $prefix . "delivery_types";
	$delivery_matrix_table						= $prefix . "delivery_matrix";
	$weight_bands_table							= $prefix . "weight_bands";
	$countries_groups_table						= $prefix . "countries_groups";
	$postage_points_table						= $prefix . "postage_points";
	$vat_table									= $prefix . "vat";
	$brands_table								= $prefix . "brands";
	$aromas_table								= $prefix . "aromas";
	$product_types_table						= $prefix . "product_types";
	$category_types_table						= $prefix . "category_types";
	$categories_info_table						= $prefix . "categories_info";
	$category_to_category_type_table			= $prefix . "category_to_category_type";
	$price_limits_table							= $prefix . "price_limits";
	$product_type_to_attribute_table			= $prefix . "product_type_to_attribute";
	$attributes_table							= $prefix . "attributes";
	$attributes_to_attribute_options_table		= $prefix . "attributes_to_attribute_options";
	$variants_table								= $prefix . "variants";
	$availability_labels_table					= $prefix . "availability_labels";
	$option_groups_table						= $prefix . "option_groups";
	$options_table								= $prefix . "options";
	$display_statuses_table						= $prefix . "display_statuses";
	$product_to_attribute_option_table			= $prefix . "product_to_attribute_option";
	$user_groups_table							= $prefix . "user_groups";
	$order_statuses_table						= $prefix . "order_statuses";
	$static_info_to_category_table				= $prefix . "static_info_to_category";
	$fast_orders_table							= $prefix . "fast_orders";
	$category_to_category_table					= $prefix . "category_to_category";
	$discounts_table							= $prefix . "discounts";
	$cart_discounts_table						= $prefix . "cart_discounts";
	$product_to_discount_table					= $prefix . "product_to_discount";
	$brand_to_discount_table					= $prefix . "brand_to_discount";
	$category_to_discount_table					= $prefix . "category_to_discount";
	$discount_to_discount_table					= $prefix . "discount_to_discount";
	$pharmacies_table							= $prefix . "pharmacies";
	$newsletters_campaigns_table 				= $prefix . "newsletters_campaigns";
	$newsletters_campaigns_to_maillists_table	= $prefix . "newsletters_campaigns_to_maillists";
	$newsletters_campaigns_to_news_table		= $prefix . "newsletters_campaigns_to_news";
	$newsletters_campaigns_to_products_table	= $prefix . "newsletters_campaigns_to_products";
	$newsletters_lists_to_programs_table		= $prefix . "newsletters_lists_to_programs";
	$category_to_category_info_table            = $prefix . "category_to_category_info";
    $diseases_table                             = $prefix . "diseases";
	$category_diseases_table                    = $prefix . "category_diseases";
	$category_disease_to_disease_table          = $prefix . "category_disease_to_disease";
	$category_disease_to_category_disease_table = $prefix . "category_disease_to_category_disease";
	$product_to_disease_table                   = $prefix . "product_to_disease";
	$categories_to_products_top_table           = $prefix . "categories_to_products_top";
	$payment_processing_table           		= $prefix . "payment_processing";
	$psec_users_admin_table           			= $prefix . "psec_users_admin";
	$discount_cards_table           			= $prefix . "discount_cards";
    $users_gdpr_protocol_table           		= $prefix . "users_gdpr_protocol";
    $users_addresses_gdpr_protocol_table        = $prefix . "users_addresses_gdpr_protocol";
    $carts_gdpr_protocol_table                  = $prefix . "carts_gdpr_protocol";
    $carts_user_gdpr_protocol_table             = $prefix . "carts_user_gdpr_protocol";
    $htaccess_table                             = $prefix . "htaccess";
	$news_table 								= $prefix . "news";
	$news_images_table                          = $prefix . "news_images";
    $news_authors_table                			= $prefix . "news_authors";
	$news_categories_table                      = $prefix . "news_categories";
	$news_to_news_categories_table              = $prefix . "news_to_news_categories";
	$requests_table                             = $prefix . "requests";
	$error_log_delivery_table                   = $prefix . "error_log_delivery";
	$mailinglist_table                          = $prefix . "mailinglist";
    $products_rating_links_table				= $prefix . "products_rating_links";
    $collections_table                          = $prefix . "collections";
    $collection_to_discount_table               = $prefix . "collection_to_discount";
    $collection_to_category_table               = $prefix . "collection_to_category";
    $colors_table                               = $prefix . "colors";
    $promo_codes_table                          = $prefix . "promo_codes";
    $promo_codes_usage_log_table                = $prefix . "promo_codes_usage_log";
    $promo_codes_usage_log_table                = $prefix . "promo_codes_usage_log";
    $category_to_promo_code_table               = $prefix . "category_to_promo_code";
    $collection_to_promo_code_table             = $prefix . "collection_to_promo_code";
    $product_to_promo_code_table                = $prefix . "product_to_promo_code";
    $brand_to_promo_code_table                  = $prefix . "brand_to_promo_code";
    $brand_to_category_table					= $prefix . "brand_to_category";
    $currencies_table                           = $prefix . "currencies";
    $favourites_table                           = $prefix . "favourites";
    $quiz_questions_table                       = $prefix . "quiz_questions";
    $quiz_questions_options_table               = $prefix . "quiz_questions_options";
    $quiz_questions_types_table                 = $prefix . "quiz_questions_types";
    $quiz_requests_table                        = $prefix . "quiz_requests";
    $quiz_requests_answers_table                = $prefix . "quiz_requests_answers";
    $product_to_product_similar_table           = $prefix . "product_to_product_similar";
    $quiz_categories_table                      = $prefix . "quiz_categories";
    $age_groups_table                           = $prefix . "age_groups";
    $error_log_mailchimp_table                  = $prefix . "error_log_mailchimp";

	/*************** DB tables weddingday ************************/
//	$static_info_table                                          			= $prefix . "static_info";
//	$static_images_table                                        			= $prefix . "static_images";
//	$static_files_table                                         			= $prefix . "static_files";
//	$common_table                                               			= $prefix . "common";
//	$users_admin_table                                         			 	= $prefix . "users_admin";
//	$htaccess_table                                             			= $prefix . "htaccess";
//	$news_table                                                 			= $prefix . "news";
//	$news_images_table                                          			= $prefix . "news_images";
//	$news_categories_table                                      			= $prefix . "news_categories";
//	$news_to_news_categories_table                              			= $prefix . "news_to_news_categories";
//	$categories_table                                           			= $prefix . "categories";
//	$categories_images_table                                    			= $prefix . "categories_images";
//	$categories_files_table                                     			= $prefix . "categories_files";
//	$cities_table                                               			= $prefix . "cities";
//	$cities_images_table                                        			= $prefix . "cities_images";
//	$cities_files_table                                         			= $prefix . "cities_files";
//	$category_types_table                                       			= $prefix . "category_types";
//	$manifacturers_origins_table                                			= $prefix . "manifacturers_origins";
//	$faqs_table                                                 			= $prefix . "faqs";
//	$users_table                                                			= $prefix . "users";
//	$contacts_table                                             			= $prefix . "contacts";
//	$users_emails_table                                         			= $prefix . "users_emails";
//	$users_emails_to_users_table                                			= $prefix . "users_emails_to_users";
//	$users_modules_reminder_table                               			= $prefix . 'users_modules_reminder';
//	$users_modules_reminder_email_text_table                    			= $prefix . 'users_modules_reminder_email_text';
//	$delivery_suppliers_table							        			= $prefix . 'delivery_suppliers';
//	$system_settings_table							            			= $prefix . 'system_settings';



	/************** Host direcroties *******************/
	$host = "https://www.arthabeauty.bg/";
	$host_clear = "https://www.arthabeauty.bg";
	$files_host = "https://www.arthabeauty.bg/files/";
	$files_dir = "{$install_path}/files/";

	$languages[0]["short"]	= "BG";
	$languages[0]["long"]	= "Български";
	$languages[1]["short"]	= "EN";
	$languages[1]["long"]	= "English";
	$languages[2]["short"]	= "DE";
	$languages[2]["long"]	= "Deutsch";
	$languages[3]["short"]	= "RO";
	$languages[3]["long"]	= "Romanian";
	$languages[4]["short"]	= "RU";
	$languages[4]["long"]	= "Русский";

	$link_find = array('.', ',', ' ', '_', '"', '\'', '\\', "/");
	$link_repl = array('' , '' , '-', '-', '' , '', "" , "-" );

	$emails_test = array(
        // "d.manolov@larasoft.eu"
    );
    $ordersEmail = "order@arthabeauty.bg";
    $ordersEmail1 = "info@arthabeauty.bg";
    //$quizEmail = "quiz@arthabeuty.com";
    $quizEmail = "arthabeauty.za.teb@arthabeauty.bg";


//  -------- General settings ----------------------------
    $sessionTime = 60 * 60 * 3; //99999999; // 60 - 1 min; 120 - 2 min; 300 - 5 min; 600 - 10 min; 1800 - 30 min;
    //$sessionName = 'VISTAMARSES';
    $allowZeroQuantityVariantOrder = true;
    $useUserGroups = true;

    $user_cookie_name = "arthabeauty_user";

    $active_payment = 1; //1-activate / 0-deactivate BORICA payments

    /*************** Payment requests settings ************************/
    $paymentRequestLife = 24 * 60 * 60; //The life of the requsts in seconds

    /*************** BORICA settings ************************/
		$publicKeyAPGWfileName = $install_path.'payments/keys/v2/V5400323-arthabeauty.bg-P.cer';
	/*$boricaPublicKey = $install_path.'payments/keys/v2/MPI_OW_APGW.pub';
	$boricaPublicCert = $install_path.'payments/keys/v2/MPI_OW_APGW.cer';*/
	$boricaPublicKey = $install_path.'payments/keys/v2/MPI_OW_APGW.pub';
	$boricaPublicCert = $install_path.'payments/keys/v2/MPI_OW_APGW.cer';
    // $privateKeyFileName = $install_path.'payments/keys/v2/production.key';
    $privateKeyFileName = $install_path.'payments/keys/v2/new/artha_priv.key';
    $privateKeyPassword = '56dkf3kr';
    $terminalID = 'V5400323';
	$merchantID = '6210026798';
	$terminalUrl = 'https://3dsgate.borica.bg/cgi-bin/cgi_link';

//  -------- Pagination settings -------------------------
	$limit = 24;
    $rightLinksCount = 3;
    $leftLinksCount = 3;

//  -------- Bonus Points --------------------------------
    $useBonusPoints = false;

//  -------- Shipping Apis -------------------------------
    $useShippingApis = false;
    $useEcontApi = false;
    $useSpeedyApi = false;

    $credentialsNumber = 1;

    $credentialsNumberEcont = 2;
    $econtUseProductionURLs = true;
//  -------- Shipping Apis -------------------------------
//Test --------- credentialsNumber 1 ---------------------------------
    $speedyUsernameTest = ""; // 999309 - Смарт Уеб ООД test UN
    $speedyPasswordTest = ""; // 3371663797 - Смарт Уеб ООД test pass
//Production --- credentialsNumber 2 ---------------------------------
    $speedyUsernameProduction = "";
    $speedyPasswordProduction = "";

//Test --------- credentialsNumberEcont 1 ---------------------------------
    $econtUsernameTest = "iasp-dev";
    $econtPasswordTest = "iasp-dev";
//Production --- credentialsNumberEcont 2 ---------------------------------
    $econtUsernameProduction = "info@arthabeauty.bg";
    $econtPasswordProduction = "Arthabeauty123";

//  -------- Social Logins -------------------------------
    $showSocialLogins = true;
    $useGoogleLogin = true;
    $useFacebookLogin = true;

//  -------- Mailchimp -------------------------------
    $mailchimpApiKey = "30d46767ed0f446881e17121e04d2117-us20";
    $listPurchasedId = "e7e098b768";

//  -------- Facebook Login -------------------------------
    $fb_app_id = "725664821261461";
	$fb_app_secret = "cc7e0150b55a63d697fd0848e3fbf979";
    $fb_redirect_url = "https://www.arthabeauty.bg/site/index.php"; //"https://www.os.nastop-bg.com/facebook-login-confirm";

//  -------- Google Login ---------------------------------
    $google_client_id = "670842906415-m3s5lr4tu2cs47a2mmt5k542d167hqp5.apps.googleusercontent.com";
	$google_client_secret = "V6X-hQZ0v36qlwcsR6eJvZKR";

//  -------- Google reCAPTCHA ---------------------------------
    $recaptcha_site_key = "6LfFTL8UAAAAANF2MctL4s6opO405Nfzjv0qk6dl";
	$recaptcha_secret_key = "6LfFTL8UAAAAAHPwtofTKXDD7QFVtU80znAlNCLd";

//  ---------------- Econt configuration ------------------
	// ИНФОРМАЦИЯ ЗА ПОДАТЕЛ (СОБСТВЕНИК НА МАГАЗИНА)
    $default_office_code = "8012"; //default office code

    $senderAddresses = array(
        array(
            "city" => array(
                "country" => array(
                    "code3" => "BGR"
                ),
                "name" => "София",
                "postCode" => "1000"
            ),
            "street" => "Самоковско шосе",
            "num" => "2л",
            "other" => "Сграда Бойла, Ет. 2 офис 8"
        )
//        ,
//        array(
//            "city" => array(
//                "country" => array(
//                    "code3" => "BGR"
//                ),
//                "name" => "София",
//                "postCode" => "1000"
//            ),
//            "street" => "Алея Младост",
//            "num" => "7"
//        )
    );


    // ИНФОРМАЦИЯ ЗА ДОСТАВКА
    $econtShipmentType = "PACK"; //тип на пратката. Възможни стойности: PACK - колет, DOCUMENT - документи, DOCUMENTPACK - палет + документи
    $econtPackCount = "1";       // брой пакети

    $econtShipmentInfo = [];
    $econtShipmentInfo["PayAfterAccept"] = "1";     // да се позволи преглед на пратката преди премане и плащане
    $econtShipmentInfo["InvoiceBeforePay"] = "0";   // предай ф-ра преди плащане на наложен платеж
    $econtShipmentInfo["PayAfterTest"] = "0";       // да се позволи тестване на пратката преди плащане
    $econtShipmentInfo["InstructionReturns"] = "";  // в случай на отказ от пратката, кой да плати транспорта за връщане
	$econtShipmentInfo["DeclaratedValue"] = "No";

    // ИНФОРМАЦИЯ ЗА ПЛАЩАНЕ
    $econtPaymentInfo = [];
    $econtPaymentInfo["Method"] = "CASH";
    $econtPaymentInfo["Side"] = "SENDER";
    $econtPaymentInfo["ReceiverShare"] = "5.05";
    $econtPaymentInfo["KeyWord"] = "ЯМ0022";
    $econtPaymentInfo["paymentCDNumber"] = ""; //"ЯМ5001C1";


?>
