<?php
	include("globals.php");

    require_once __DIR__ . '/assets/php-graph-sdk-5_x/src/Facebook/autoload.php';

    $code = $params->getString("code");

	$param = $params->getString("param");
	$id = $params->getInt("id");
	$page = $params->get("page");

	$message_code = $params->getInt("message_code");

	$captcha_code = $params->get("captcha_code");

	$sm->configLoad($htaccess_file);
	$htaccessVars = $sm->getConfigVars();

	$quit_further_check = 0; // SETTING TO QUIT FURTHER CHECKS IF WE HAVE A PAGING PARAMETER SENT

	if ( strpos($param, "/".$htaccessVars["htaccess_page"]."-") ){

		$param_original = $param;
		$param = substr($param, 0, strpos($param, "/".$htaccessVars["htaccess_page"]."-"))."/";
		$page = str_replace(array($param."".$htaccessVars["htaccess_page"]."-", "/"), "", $param_original); // EXTRACT THE NUMBER OF PAGE FROM THE REQUEST STRING
		$use_term = substr($param, 0, strlen($param)-1);

		$result = $site->checkHtaccessByTerm($use_term, (int)$page);
		$quit_further_check = 1;
	}
	
	if($param == 'eborica') {
		$cart = new Cart();
        $cart->processBoricaResult($params);
        die();
	}

    if ($eborica){
        $cart = new Cart();
        $cart->processBoricaResult($params);
        die();
    }

	if ( !$quit_further_check ){
		if ( $param == "search" ){
			$settings->searchPage($params);
		}elseif ( $param == "minify-css" ){
            if ($_SERVER["REMOTE_ADDR"] == "::1"){
                $helpers = new Helpers();
                $helpers->minifyCSS();
            }
		}elseif ( $param == "minify-js" ){
            if ($_SERVER["REMOTE_ADDR"] == "::1"){
                $helpers = new Helpers();
                $minifiedJs = $helpers->minifyJS();
                $sm->assign("minifiedJs", $minifiedJs);
            }
		}elseif ( $param == "test-mailchimp" ){
            if ($_SERVER["REMOTE_ADDR"] == "::1"){
                $helpers = new Helpers();
                $data = [
                            'email'     => 'kkalchev@development-bg',
                            'status'    => 'subscribed',
                            'firstname' => 'Kaloyan',
                            'lastname'  => 'Kalchev'
                        ];
                $result = $helpers->syncMailchimp($listPurchasedId, $data);

                echo "<pre>";
                var_dump($result);
                echo "</pre>";
                exit();
            }
		}elseif ( $param == "test-webp" ){
            require_once($install_path . "libs/composer/vendor/autoload.php");

            $helpers = new Helpers();

            $source = $install_path . 'images/logo-mastercard.png';
            $destination = $install_path . 'images/logo-mastercard.webp';
            $helpers->convertToWebp($source, $destination);
            //$helpers->createWebpImages($install_path."images/article-22.jpg", $install_path."images/new-webp-file.webp", 80);

		}elseif ( $param == "collection" ){
			    error_reporting(1);
            $collectionsObj = new Collections();
			$collectionsObj->getPage($params);
		}elseif ( $param == "brand" ){
            $brandsObj = new Brands();
			$brandsObj->getPage($params);
		}elseif ( $param == "brands" ){
            $brandsObj = new Brands();
			$brandsObj->getBrandsPage();
		}elseif ( $param == "info" && $id ){
			$menus = new Menus();
			$menus->getPage($id);
		}elseif ( $param == "post-contact"){
			$settingsObj = new Settings();
			$settingsObj->postContact($params);
		}elseif ( $param == "check-site-words"){
			$settingsObj = new Settings();
			$settingsObj->checkSiteWords();
		}elseif ( $param == "show-product" && $id ){
            $productsObj = new Products();
			$productsObj->getProductPage($id);
		}elseif ( $param == "download-product-file"){
            $product_id = $params->getInt("product_id");
			$fileName = urldecode($params->getString("doc")); // Decode URL-encoded string
            Helpers::downloadProductFile($fileName, $product_id);
		}elseif ( $param == "download-info-file"){
            $info_id = $params->getInt("info_id");
			$fileName = urldecode($params->getString("file")); // Decode URL-encoded string
            Helpers::downloadInfoFile($fileName, $info_id);
		}elseif ( $param == "categories" && $id ){
			$categories = new Categories();
			$categories->getPage($id, $page, $params);
		}elseif ( $param == "offers"){
			$offers = new Offers();
			$offers->getPage($page, $params);
		}elseif ( $param == "post-product-to-favourites" ){
			$productsObj = new Products();
			$productsObj->postProductToFavourites($params);
		}elseif ( $param == "favourites" ){
			$productsObj = new Products();
			$productsObj->getFavouritesPage($page);
		}elseif ( $param == "process-advanced-search" ){
			$sm->assign("noindex", 1);
			$categories = new Categories();
			$categories->processAdvancedSearch($params);
		}elseif ( $param == "contact-to-category" && $id ){
			if ( $lng == "ro" ){
				$categoriesObj = new Customers();
				$categoriesObj->contactToCategory($id, $params);
			}else{
				$customersObj = new Customers();
				$customersObj->contactToCategory($id, $params);
			}
		}elseif ( $param == "category-types" && $id ){
			$categoryTypes = new CategoryTypes();
			$categoryTypes->getPage($id, $page, $params);
		}elseif ( $param == "blog"){
			$news = new News();
			$news->getBlogPage();
		}elseif ( $param == "news" && $id ){
			$news = new News();
			$news->getPage($id);
		}elseif ( $param == "login-page" ){
			$userObj = new Users();
			$userObj->getLoginPage();
		}elseif ( $param == "register" ){
			$userObj = new Users();
			$userObj->getRegisterPage();
		}elseif ( $param == "forgot-password" ){
			$userObj = new Users();
			$userObj->getForgotPasswordPage();
		}elseif ( $param == "forgot-password-proceed" ){
			$userObj = new Users();
			$userObj->forgotPasswordProceed($params);
		}elseif ( $param == "refresh-session" ){
            session_start();
            // store session data
            if (isset($_SESSION['id'])){
                $_SESSION['id'] = $_SESSION['id'];
            }
            die();
		}elseif ( $param == "order-rating" ){
			$cartObj = new Cart();
			$cartObj->getOrderRatingPage($params);
		}elseif ( $param == "leave-rating-comment" ){
			$ratingObj = new ProductsRating();
			$ratingObj->leaveRatingAndComment($params);
		}elseif ( $param == "myprofile" ){
			$userObj = new Users();
			$userObj->getProfilePage();
		}elseif ( $param == "quiz" ){
			$quizObj = new QuizQuestions();
			$quizObj->getPage();
		}elseif ( $param == "proceed-quiz" ){
			$quizObj = new QuizQuestions();
			$quizObj->quizProceed($params);
		}elseif ( $param == "get-quiz-categories" ){
            $sex = $params->getInt("sex"); // 1- male; 2 - female
			$quizCategoriesObj = new QuizCategories();
			$quizCategoriesObj->getQuizCategoriesBySex($sex, 3);
		}elseif ( $param == "get-quiz-questions" ){
            $quiz_category_id = $params->getInt("quiz_category_id");
			$quizQuestionsObj = new QuizQuestions();
			$quizQuestionsObj->getQuizQuestionsByCategory($quiz_category_id, 3);
		}elseif ( $param == "myorders" ){
			$cartObj = new Cart();
			$cartObj->getMyordersPage($params);
		}elseif ( $param == "add-address-page" ){
			$userObj = new Users();
			$userObj->addEditAddress($params, "add");
		}elseif ( $param == "edit-address-page" ){
			$userObj = new Users();
			$userObj->addEditAddress($params, "edit");
		}elseif ( $param == "add-delivery-address" ){
			$userObj = new Users();
			$userObj->addEditDeliveryAddress($params, "add");
		}elseif ( $param == "edit-delivery-address" ){

			$userObj = new Users();
			$userObj->addEditDeliveryAddress($params, "edit");
		}elseif ( $param == "add-billing-address" ){
			$userObj = new Users();
			$userObj->addEditBillingAddress($params, "add");
		}elseif ( $param == "edit-billing-address" ){
			$userObj = new Users();
			$userObj->addEditBillingAddress($params, "edit");
		}elseif ( $param == "check-promocode" ){
            $promoCode = $params->getString("code");
            $totalAmount = $params->getNumber("total_amount");
			PromoCodes::checkPromoCode($promoCode, $totalAmount, 3);
		}elseif ( $param == "apply-promocode" ){
            $promoCode = $params->getString("code");
            $totalAmount = $params->getNumber("total_amount");
			$userId = (isset($user['id'])) ? $user['id'] : null;
            PromoCodes::applyPromoCode($promoCode, $totalAmount, 3, $userId);
		}elseif ( $param == "get-cities-by-district-id" ){
            $district_id = $params->getInt("district_id");
			Cities::getCitiesByDistrictId((int)$district_id, 3);
		}elseif ( $param == "register-proceed" ){
			$userObj = new Users();
			$userObj->registerUser($params);
		}elseif ( $param == "edit-profile-proceed" ){
			$userObj = new Users();
			$userObj->editUser($params);
		}elseif ( $param == "mailinglist-subscribe-unsubscribe" ){
			$userObj = new Users();
			$userObj->mailinglistSubscribeUnsubscribe();
		}elseif ( $param == "mailinglist-subscribe" ){
            $email = $params->getString("email");
            $agree_terms = $params->getInt("agree_terms");
            $agree_terms_gdpr = $params->getInt("agree_terms_gdpr");
            if($agree_terms && $agree_terms_gdpr){
                $mailinglistObj = new Mailinglist();
                $mailinglistObj->mailinglistSubscribe($email, 3);
            }else{
                $result = array();
                $result["code"] = 0;
                $result["message"] = "You must accept the agree terms!";
                echo json_encode($result);
                die();
            }
		}elseif ( $param == "newsletterHome" ){
            $email = $params->getString("email");
            $mailinglistObj = new Mailinglist();
            $mailinglistObj->mailinglistSubscribeHome($email);
		}elseif ( $param == "check-email-exists" ){
			$userObj = new Users();
			$userObj->checkEmailExists($params, 3);
		}elseif ( $param == "get-logged-user-addresses" ){
			$userObj = new Users();
			$userObj->getLoggedUserAddresses(3);
		}elseif ( $param == "login-proceed" ){
			$userObj = new Users();
			$userObj->loginUser($params);
		}elseif ( $param == "login-facebook"){
            $fb = new Facebook\Facebook([
                'app_id' => $fb_app_id,
                'app_secret' => $fb_app_secret,
                'default_graph_version' => 'v3.3',
            ]);

            $helper = $fb->getRedirectLoginHelper();
//            $permissions = ['email', 'user_likes', 'user_friends']; // Optional permissions
            $permissions = ['email']; // Optional permissions
            $loginUrl = $helper->getLoginUrl($fb_redirect_url, $permissions);

            header("Location: ".$loginUrl);
			die();
        }elseif( $code && !$param ){
            $fb = new Facebook\Facebook([
                'app_id' => $fb_app_id,
                'app_secret' => $fb_app_secret,
                'default_graph_version' => 'v3.3',
            ]);

            $helper = $fb->getRedirectLoginHelper();

            try {
                $accessToken = $helper->getAccessToken();
            } catch (Facebook\Exceptions\FacebookResponseException $e) {
                // When Graph returns an error
                echo 'Graph returned an error: ' . $e->getMessage();
                exit;
            } catch (Facebook\Exceptions\FacebookSDKException $e) {
                // When validation fails or other local issues
                echo 'Facebook SDK returned an error: ' . $e->getMessage();
                exit;
            }

            if (!isset($accessToken)) {
                if ($helper->getError()) {
                    header('HTTP/1.0 401 Unauthorized');
                    echo "Error: " . $helper->getError() . "\n";
                    echo "Error Code: " . $helper->getErrorCode() . "\n";
                    echo "Error Reason: " . $helper->getErrorReason() . "\n";
                    echo "Error Description: " . $helper->getErrorDescription() . "\n";
                } else {
                    header('HTTP/1.0 400 Bad Request');
                    echo 'Bad request';
                }
                exit;
            }

            // Logged in
//            echo '<h3>Access Token</h3>';
//            var_dump($accessToken->getValue());

            // The OAuth 2.0 client handler helps us manage access tokens
            $oAuth2Client = $fb->getOAuth2Client();

            // Get the access token metadata from /debug_token
            $tokenMetadata = $oAuth2Client->debugToken($accessToken);
//            echo '<h3>Metadata</h3>';
//            var_dump($tokenMetadata);

            // Validation (these will throw FacebookSDKException's when they fail)
            $tokenMetadata->validateAppId($fb_app_id); // Replace {app-id} with your app id
            // If you know the user ID this access token belongs to, you can validate it here
            //$tokenMetadata->validateUserId('123');
            $tokenMetadata->validateExpiration();

            if (!$accessToken->isLongLived()) {
                // Exchanges a short-lived access token for a long-lived one
                try {
                    $accessToken = $oAuth2Client->getLongLivedAccessToken($accessToken);
                } catch (Facebook\Exceptions\FacebookSDKException $e) {
                    echo "<p>Error getting long-lived access token: " . $e->getMessage() . "</p>\n\n";
                    exit;
                }
//                echo '<h3>Long-lived</h3>';
//                var_dump($accessToken->getValue());
            }

            $_SESSION['fb_access_token'] = (string) $accessToken;

            try {
			  // Returns a `Facebook\FacebookResponse` object
			  $response = $fb->get('/me?fields=id,name,email,picture', $_SESSION["fb_access_token"]);
			} catch(Facebook\Exceptions\FacebookResponseException $e) {
			  echo 'Graph returned an error: ' . $e->getMessage();
			  exit;
			} catch(Facebook\Exceptions\FacebookSDKException $e) {
			  echo 'Facebook SDK returned an error: ' . $e->getMessage();
			  exit;
			}

			$userData = $response->getGraphUser();

            $usersObj = new Users();
            $usersObj->loginUser($params, $userData);
            //die("---------- End ----------");
            // User is logged in with a long-lived access token.
        }elseif ( $param == "logout" ){
			$userObj = new Users();
			$userObj->logoutUser();
		}elseif ( $param == "change-password" ){
			$userObj = new Users();
			$userObj->changePassword($params);
		}elseif ( $param == "save-user" ){
			$userObj = new Users();
			$userObj->saveUser($params);
		}elseif ( $param == "news_categories" && $id ){
			$news = new News();
			$news->getNewsCategoryPage($id, $page);
		}elseif ( $param == "captcha"){
			$settings = new Settings();
			$settings->generateCaptcha();
		}elseif ( $param == "captcha-check"){
			$settings = new Settings();
			$settings->checkCaptcha($captcha_code);
		}elseif ( $param == "messages" && $message_code ){
			$messages = new Settings();
			$messages->getMessagePage($message_code);
		}elseif ( $param == "get-fast-order-form"){
			Products::getFastOrderPage($params);
		}elseif ( $param == "get-variant-details"){
			Products::getVariantDetails($params);
		}elseif ( $param == "post-fast-order"){
			Products::postFastOrder($params);
		}elseif ( $param == "cart"){
			Cart::getPageCart($params);
		}elseif ( $param == "cart-ajax"){
			$cartObj = new Cart();
			echo json_encode($cartObj->getCart());
		}elseif ( $param == "simple-search-ajax"){
            $search_string = $params->getString("search_string");
			$settingsObj = new Settings();
			$settingsObj->getNumberOfProductsBySearchString($search_string, 4, 3);
		}elseif ( $param == "add-to-cart"){
			$productsObj = new Products();
			$productsObj->addToCart($params);
		}elseif ( $param == "cart-remove-product"){
			$cartObj = new Cart();
			$cartObj->deleteProductFromCart($params);
		}elseif ( $param == "cart-update-product-quantity"){
			$cartObj = new Cart();
			$cartObj->updateQuantity($params);
		}elseif ( $param == "update-cart"){
			Cart::updateQuantity($params, 3);
		}elseif ( $param == "checkout"){
			$cartObj = new Cart();
			$cartObj->getCheckoutPage($params);
		}elseif ( $param == "get-cities"){
			Cities::getCities($params, 3);
		}elseif ( $param == "checkout-final"){
			$cartObj = new Cart();
			$cartObj->getCheckoutFinalPage($params);
		}elseif ( $param == "finalise-order"){
			$cartObj = new Cart();
			$cartObj->finaliseOrder($params);
		}elseif ( $param == "get-delivery-offices"){
            $delivery_type = $params->getInt("delivery_type_id");
            $region = $params->getString("region");
            $city = $params->getString("city");

			Delivery::getOfficesByRegionAndCityNames($delivery_type, $region, $city, 3);
		}

        elseif ( $param == "shop"){
			$shopObj = new Shop();
			$shopObj->getPageIndex();
		}elseif ( $param == "shop-category"){
			$shopObj = new Shop();
			$shopObj->getPageCategory($params);
		}elseif ( $param == "shop-product"){
			$shopObj = new Shop();
			$shopObj->getPageProduct($params);
		}elseif ( $param == "product-show-images"){
			$shopObj = new UsersShop();
			$shopObj->getProductImages($params);
		}elseif ( $param == "product-delete-images"){
			$shopObj = new UsersShop();
			$shopObj->deletePic($params);
		}elseif ( $param == "product-add-images"){
			$shopObj = new UsersShop();
			$shopObj->addPic($params);
		}elseif ( $param == "move-product-images"){
			$shopObj = new UsersShop();
			$shopObj->moveImage($params);
		}elseif ( $param == "shop-update-delivery-settings"){
			$shopObj = new Shop();
			$shopObj->updateDeliverySettings($params);
		}elseif ( $param == "shop-checkout"){
			$shopObj = new Shop();
			$shopObj->getPageCheckout($params);
		}elseif ( $param == "shop-thank-you-message"){
			$shopObj = new Shop();
			$sm->assign("shop_categories",$shopObj->getShopCategories());
			$sm->display("shop_thank_you_message.html");
		}elseif ( $param == "shop-checkout-proceed"){
			$shopObj = new Shop();
			$shopObj->proceedCheckout($params);
		}elseif ( $param == "prod-feed"){
			$prodObj = new Products();
			$prodObj->genFeed();
		}elseif ( preg_match("#[0-9]{4}/[0-1][0-9]/#",$param) ){
			$news = new News();
			$news->getListByMonth($param);
		}elseif ( $param ){
			$result = $site->checkHtaccessByTerm($param, 0, 0);

			$_SESSION["lang"] = $result["lang"];
			if($result["id"]){
			}
			else{
				header("Location: /messages/100");
				die();
			}
		}else{
			$site->getIndexPage();
		}
	}


?>
