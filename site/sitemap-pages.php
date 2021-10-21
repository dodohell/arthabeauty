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

			$items = $db->getAll("SELECT *, 
										 name_{$lng} AS name,
										 h1_{$lng} AS h1,
										 url_{$lng} AS url,
										 excerpt_{$lng} AS excerpt,
										 url_target AS target,
										 htaccess_url_{$lng} AS htaccess_url
								  FROM ".$categories_table." 
								  WHERE edate = 0
                                  AND active = 1
								  AND name_{$lng} <> '' AND htaccess_url_{$lng} <> ''
								  ORDER BY pos"); safeCheck($categories);

			foreach($items as $k => $v){
				
				 echo "<url>	
								<loc>".substr($host,0,-1).($v["htaccess_url"] ? $v["htaccess_url"]: $htaccessVars["htaccess_categories"]."/".$v["id"])."</loc>
								<changefreq>weekly</changefreq>
							  <priority>0.6</priority>
						   </url>";
			}

	echo "</urlset>";
?>