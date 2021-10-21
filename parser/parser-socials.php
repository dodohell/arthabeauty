<?
	function dbg($str){
		print("<pre>");
		print_r($str);
		print("</pre>");
	}
	set_time_limit(0);
	ini_set("memory_limit", "1024M");
	
	$websites = "http://www.mybespokeroom.com
http://www.chantelelshout.com
http://www.harpersinteriors.co.uk
http://www.empatika.uk
http://leivars.com
http://www.slightlyquirky.com
http://www.honeybeeinteriors.co.uk
http://www.jmdesigninteriors.co.uk
http://www.urbangrain.co.uk
http://www.woodfordarchitecture.com/architecture/portfolio
";
	$websites_links = explode("\n", $websites);
	$instagram = "";
	$facebook = "";
	$twitter = "";
	$pinterest = "";
	foreach($websites_links as $k => $v){
		$url = $v;
		$ch = curl_init();  
		curl_setopt($ch,CURLOPT_URL,$url);
		curl_setopt($ch,CURLOPT_RETURNTRANSFER,1);
		$content = curl_exec($ch);
		curl_close($ch);
		
		preg_match_all('#"https://www.instagram.com/(.*)"#msiU', $content, $matches);
		dbg($content);
		dbg($matches);
	}
	
	
	//file_put_contents("complete_urls_uk.txt", $html);
	
?>