<?php

/**
 * Helpers
 *
 * @author Kaloyan Kalchev (smart web)
 */
class Helpers {
    /**
     * 
     * @param string $path      // $_SERVER['DOCUMENT_ROOT'] . "/demo/documents/";
     * @param string $fileName  // $_GET['download_file'];
     */
    public static function showPdf($path, $fileName) {
         
        $fullPath = $path . $fileName;

        if ($fd = fopen($fullPath, "r")) {
            $fsize = filesize($fullPath);
            $path_parts = pathinfo($fullPath);
            $ext = strtolower($path_parts["extension"]);
            switch ($ext) {
                case "pdf":
                    header("Content-type: application/pdf"); // add here more headers for diff. extensions
                    header("Content-Disposition: inline; filename=\"" . $path_parts["basename"] . "\"");
                    break;
                default;
                    header("Content-type: application/octet-stream");
                    header("Content-Disposition: filename=\"" . $path_parts["basename"] . "\"");
            }
            header("Content-length: $fsize");
            header("Cache-control: private"); //use this to open files directly
            while (!feof($fd)) {
                $buffer = fread($fd, 2048);
                echo $buffer;
            }
        }
        fclose($fd);
        exit;
    }
    
    public static function paginate($current_page, $total_pages, $page_url){
        global $rightLinksCount;
        global $leftLinksCount;
        global $htaccess_file;
        global $sm;
        
        $sm->configLoad($htaccess_file);
        $htaccessVars = $sm->getConfigVars();
        $request_uri = $_SERVER['REQUEST_URI'];
        if ( strpos($request_uri, "/".$htaccessVars["htaccess_page"]."-1") ){
            header("HTTP/1.1 301 Moved Permanently");
            header("Location: ".$page_url);
            die();
        }
        
        $pagination = '';
        if($total_pages > 0 && $total_pages != 1 && $current_page <= $total_pages){ //verify total pages and current page number
            
            $pagination .= '<ul class="pagination">'; //justify-content-center
            
            $right_links    = $current_page + $rightLinksCount; 
            $left_links     = $current_page - $leftLinksCount;
            $previous       = $current_page - 1; //previous link 
            $next           = $current_page + 1; //next link
            $first_link     = true; //boolean var to decide our first link
            
            if($current_page > 1){
                $previous_link = ($previous == 0) ? 1 : $previous;
                $pagination .= '<li class="page-item first"><a class="page-link" href="'.$page_url.'" title="First">«</a></li>'; //first link
                if($previous_link == 1){
                    $pagination .= '<li class="page-item"><a class="page-link" href="'.$page_url.'" title="Previous"><</a></li>'; //previous link
                }else{
                    $pagination .= '<li class="page-item"><a class="page-link" href="'.$page_url.'/page-'.$previous_link.'" title="Previous"><</a></li>'; //previous link
                }
                    for($i = $left_links; $i < $current_page; $i++){ //Create left-hand side links
                        if($i > 0){
                            if($i == 1){
                                $pagination .= '<li><a class="page-link" href="'.$page_url.'">'.$i.'</a></li>';
                            }else{
                                $pagination .= '<li><a class="page-link" href="'.$page_url.'/page-'.$i.'">'.$i.'</a></li>';
                            }
                        }
                    }
                $first_link = false; //set first link to false
            }
            
            if($first_link){ //if current active page is first link
                $pagination .= '<li class="page-item first active"><span class="page-link">'.$current_page.'</span></li>';
            }elseif($current_page == $total_pages){ //if it's the last active link
                $pagination .= '<li class="page-item last active"><span class="page-link">'.$current_page.'</span></li>';
            }else{ //regular current link
                $pagination .= '<li class="page-item active"><span class="page-link">'.$current_page.'</span></li>';
            }
            
            for($i = $current_page+1; $i < $right_links ; $i++){ //create right-hand side links
                if($i<=$total_pages){
                    $pagination .= '<li class="page-item"><a class="page-link" href="'.$page_url.'/page-'.$i.'">'.$i.'</a></li>';
                }
            }
//          
            if($current_page < $total_pages){
                $next_link = ($next == $total_pages) ? $total_pages : $next;
                $pagination .= '<li class="page-item"><a class="page-link" href="'.$page_url.'/page-'.$next_link.'" title="Next">></a></li>'; //next link
                $pagination .= '<li class="page-item last"><a class="page-link" href="'.$page_url.'/page-'.$total_pages.'" title="Last">»</a></li>'; //last link
            }
            
            $pagination .= '</ul>'; 
        }
        return $pagination; //return pagination links
    }
    
//    public function startSession(){
//        global $sessionTime;
//        global $sessionName;
//        session_set_cookie_params($sessionTime);
//        session_name($sessionName);
//        session_start();
//        
//        // Reset the expiration time upon page load
//        if (isset($_COOKIE[$sessionName]))
//          setcookie($sessionName, $_COOKIE[$sessionName], time() + $sessionTime, "/");
//    }
    public function getDiscountedPrice($price, $isSpecial, $userGroupId = 0) {
        global $useUserGroups;
        
        if(is_numeric($price)){
            $price = (float) $price;
        }else{
            $settings = new Settings();
            $settings->getMessagePage(412);
            die();
        }
        
        if($useUserGroups && $userGroupId > 0){
            $userGroup = UserGroups::getRecord($userGroupId);
        }else{
            return $price;
        }
        $discount = isset($userGroup["discount"]) ? (float)$userGroup["discount"] : 0.0;
        $reduce_special_prices = isset($userGroup["reduce_special_prices"]) ? (int)$userGroup["reduce_special_prices"] : 0;
        if($discount > 0.0){
            if($isSpecial){
                if($reduce_special_prices){
                    $price = $price - ($price * ($discount/100));
                }
            }else{
                $price = $price - ($price * ($discount/100));
            }
        }
        
        return $price;
    }
    
    public static function getCurentUserGroupId() {
        global $user;
        
        return $user["user_group_id"] > 0 ? (int)$user["user_group_id"] : 0;
    }
	
	public static function generateURL($string){
		$alphabet_start[] = 'а';
		$alphabet_replace[] = 'a';
		$alphabet_start[] = 'А';
		$alphabet_replace[] = 'a';
		$alphabet_start[] = 'б';
		$alphabet_replace[] = 'b';
		$alphabet_start[] = 'в';
		$alphabet_replace[] = 'v';
		$alphabet_start[] = 'г';
		$alphabet_replace[] = 'g';
		$alphabet_start[] = 'д';
		$alphabet_replace[] = 'd';
		$alphabet_start[] = 'е';
		$alphabet_replace[] = 'e';
		$alphabet_start[] = 'ж';
		$alphabet_replace[] = 'zh';
		$alphabet_start[] = 'з';
		$alphabet_replace[] = 'z';
		$alphabet_start[] = 'и';
		$alphabet_replace[] = 'i';
		$alphabet_start[] = 'й';
		$alphabet_replace[] = 'j';
		$alphabet_start[] = 'к';
		$alphabet_replace[] = 'k';
		$alphabet_start[] = 'л';
		$alphabet_replace[] = 'l';
		$alphabet_start[] = 'м';
		$alphabet_replace[] = 'm';
		$alphabet_start[] = 'н';
		$alphabet_replace[] = 'n';
		$alphabet_start[] = 'о';
		$alphabet_replace[] = 'o';
		$alphabet_start[] = 'п';
		$alphabet_replace[] = 'p';
		$alphabet_start[] = 'р';
		$alphabet_replace[] = 'r';
		$alphabet_start[] = 'с';
		$alphabet_replace[] = 's';
		$alphabet_start[] = 'т';
		$alphabet_replace[] = 't';
		$alphabet_start[] = 'у';
		$alphabet_replace[] = 'u';
		$alphabet_start[] = 'ф';
		$alphabet_replace[] = 'f';
		$alphabet_start[] = 'х';
		$alphabet_replace[] = 'h';
		$alphabet_start[] = 'ц';
		$alphabet_replace[] = 'c';
		$alphabet_start[] = 'ч';
		$alphabet_replace[] = 'ch';
		$alphabet_start[] = 'ш';
		$alphabet_replace[] = 'sh';
		$alphabet_start[] = 'щ';
		$alphabet_replace[] = 'sht';
		$alphabet_start[] = 'ъ';
		$alphabet_replace[] = 'a';
		$alphabet_start[] = 'ь';
		$alphabet_replace[] = 'y';
		$alphabet_start[] = 'ю';
		$alphabet_replace[] = 'yu';
		$alphabet_start[] = 'я';
		$alphabet_replace[] = 'ya';
		$alphabet_start[] = ' ';
		$alphabet_replace[] = '-';
		$alphabet_start[] = '?';
		$alphabet_replace[] = '';
		$alphabet_start[] = '!';
		$alphabet_replace[] = '';
		$alphabet_start[] = '@';
		$alphabet_replace[] = '';
		$alphabet_start[] = '#';
		$alphabet_replace[] = '';
		$alphabet_start[] = '$';
		$alphabet_replace[] = '';
		$alphabet_start[] = '=';
		$alphabet_replace[] = '';
		$alphabet_start[] = "'";
		$alphabet_replace[] = '';
		$alphabet_start[] = '"';
		$alphabet_replace[] = '';
		$alphabet_start[] = '(';
		$alphabet_replace[] = '';
		$alphabet_start[] = ')';
		$alphabet_replace[] = '';
		$alphabet_start[] = '&';
		$alphabet_replace[] = '';
		$alphabet_start[] = '*';
		$alphabet_replace[] = '';
		$alphabet_start[] = '^';
		$alphabet_replace[] = '';
		$alphabet_start[] = '%';
		$alphabet_replace[] = '';
		$alphabet_start[] = '`';
		$alphabet_replace[] = '';
		$alphabet_start[] = '~';
		$alphabet_replace[] = '';
		$alphabet_start[] = '+';
		$alphabet_replace[] = '';
		
		return '/'.substr(str_replace($alphabet_start, $alphabet_replace, mb_strtolower($string)), 0, 120);
	}
	
    public static function createWebpImages($src, $newFile, $compression) {
        //	convert wizard.png -quality 50 -define webp:lossless=true wizard.webp
        //error_reporting(E_ALL);
        $magicpath = "/usr/bin";
        $mogrify = "convert";
        //exec("{$magicpath}/{$mogrify} {$src} -quality {$compression} -define webp:lossless=true {$newFile}", $result);
        //exec("{$magicpath}/{$mogrify} test-image.jpg -quality 80 -define webp:lossless=true test-image.webp", $result);	
        //print_r("{$magicpath}/{$mogrify} {$src} -quality {$compression} -define webp:lossless=true {$newFile}");
//        echo "<pre>";
//        var_dump("{$magicpath}/{$mogrify} {$src} -quality {$compression} -define webp:lossless=true {$newFile}");
//        echo "</pre>";
//        exit();
    }
}
