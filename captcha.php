<?
session_start();
$md5 = md5(microtime() * mktime());

/*
We dont need a 32 character long string so we trim it down to 5
*/
$string = substr($md5,0,5);
			/*
Now for the GD stuff, for ease of use lets create
 the image from a background image.
*/

$captcha = imagecreatefrompng("images/captcha.png");

/*
Lets set the colours, the colour $line is used to generate lines.
 Using a blue misty colours. The colour codes are in RGB
*/

$black = imagecolorallocate($captcha, 218,37,30);
$line = imagecolorallocate($captcha,255,179,175);
$line2 = imagecolorallocate($captcha,0,239,0);

/*
Now to make it a little bit harder for any bots to break, 
assuming they can break it so far. Lets add some lines
in (static lines) to attempt to make the bots life a little harder
*/
imageline($captcha,0,0,39,29,$line);
imageline($captcha,40,0,64,29,$line);
$linesnumber = rand(1,3);
for($i = 1; $i < $linesnumber; $i++){
	$color = rand(90,10);
	$line = imagecolorallocate($captcha,$color,$color,$color );
	imageline($captcha,rand(0,53),rand(0,20), rand(0, 53), rand(0,20),$line);
}
/*
imageline($captcha,rand(0,80),rand(0,30), rand(0, 80), rand(0,30),$line);
imageline($captcha,rand(0,80),rand(0,30), rand(0, 80), rand(0,30),$line2);
imageline($captcha,rand(0,80),rand(0,30), rand(0, 80), rand(0,30),$line);
imageline($captcha,rand(0,80),rand(0,30), rand(0, 80), rand(0,30),$line2);
*/
			imagestring($captcha, 3, 3, 3, $string, $black);


/*
Encrypt and store the key inside of a session
*/

$_SESSION['key'] = $string."Sinister";
/*
Output the image
*/
@header("Content-type: image/png");
imagepng($captcha);


?>	