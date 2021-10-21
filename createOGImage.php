<?
	session_start();
	
	//header('Content-Type: image/jpeg');
	
	function ImageTTFCenter($image, $text, $font, $size, $angle = 45){
		$xi = imagesx($image);
		$yi = imagesy($image);

		$box = imagettfbbox($size, $angle, $font, $text);
		$bbox = imagettfbbox(85, 0, $font, $string);
		$center1 = (imagesx($image) / 2) - (($bbox[2] - $bbox[0]) / 2);
		
		
		$xr = abs(max($box[2], $box[4]));
		$yr = abs(max($box[5], $box[7]));

		$x = intval(($xi - $xr) / 2) - 70;
		$y = intval(($yi + $yr) / 2) + 40;

		return array($x, $y);
	}
	
	
	$string = $_REQUEST["name"];
	
	$image = imagecreatefromjpeg("images/invitation-background.jpg");
	
	$font = $_SERVER["DOCUMENT_ROOT"]."/site/css/fonts/PTS55F.ttf";
	list($x, $y) = ImageTTFCenter($image, $string, $font, 40);
	
	$white = imagecolorallocate($image, 255, 255, 255);
	$grey = imagecolorallocate($image, 128, 128, 128);
	
	//imagettftext($image, 30, 0, $x+2, $y+2, $grey, $font, $string);

	// The name
	imagettftext($image, 30, 0, $x, $y, $white, $font, $string);
	
	//imagettftext($image, 20, 0, $x, 580, $white, $font, "http://www.weddingday.bg/us/kokona-i-gazar");
	
	
	$user_id = (int)$_REQUEST["user_id"];
	
	//@header("Content-type: image/jpeg");
	$path = $_SERVER["DOCUMENT_ROOT"]."/subsite-images/".$user_id."/";
	$filename = htmlspecialchars(trim($_REQUEST["hash"]), ENT_QUOTES).".jpg";
	
	if ( file_exists($path) == false ){
		mkdir($path);
		chmod($path, 0777);
	}
	if ( file_exists($path.$filename) == true ){
		unlink($path.$filename);
	}
	imagejpeg($image, $path.$filename );
	//imagejpeg($image);


?>	