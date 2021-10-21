<?php
	include("globalsXml.php");
	echo 	
	"<?xml version=\"1.0\" encoding=\"UTF-8\"?>
	<urlset xmlns:xsi=\"http://www.w3.org/2001/XMLSchema-instance\"
         xsi:schemaLocation=\"http://www.sitemaps.org/schemas/sitemap/0.9 http://www.sitemaps.org/schemas/sitemap/0.9/sitemap.xsd\"
         xmlns=\"http://www.sitemaps.org/schemas/sitemap/0.9\">";
         
			$sm->configLoad($language_file);
			$configVars = $sm->getConfigVars();
			
//            $code = $params->getString("code");
//            $sm->assign("code", $code);
             
      $sm->configLoad($htaccess_file);
			$htaccessVars = $sm->getConfigVars();

 			$items = $db->getAll("SELECT id,
										  name_{$lng} AS name,
										  excerpt_{$lng} AS excerpt,
										  pic,
										  htaccess_url_{$lng} AS htaccess_url,
										  postdate,
										  publishdate
								FROM ".$news_table."
								WHERE edate =0 
								AND active = '1'
								ORDER BY publishdate DESC
								"); safeCheck($items);
								echo $htaccessVars["htaccess_news"];
								
			foreach($items as $k => $v){
				 echo "<url>	
								<loc>".substr($host,0,-1).($v["htaccess_url"] ? $v["htaccess_url"]: $htaccessVars["htaccess_news"]."/".$v["id"])."</loc>
								<changefreq>weekly</changefreq>
							  <priority>0.6</priority>
						   </url>";
			}

	echo "</urlset>";
?>