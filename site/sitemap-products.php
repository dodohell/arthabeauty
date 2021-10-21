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

            $sqlItems = "SELECT DISTINCT
                            products.*,
                            products.name_{$lng} AS name,
                            products.htaccess_url_{$lng} AS htaccess_url,
                            products.excerpt_{$lng} AS excerpt,
                            products.htaccess_url_{$lng} AS htaccess_url
                        FROM 
                            ".$products_table." AS products
                        WHERE
                            products.edate = 0
                        AND products.active = 1
                        AND products.quantity > 0
                        {$sql_order_by}
                        ";


            $items = $db->getAll($sqlItems); safeCheck($items);

			foreach($items as $k => $v){
				 echo "<url>	
								<loc>".substr($host,0,-1).($v["htaccess_url"] ? $v["htaccess_url"]: $htaccessVars["htaccess_product"]."/".$v["id"])."</loc>
								<changefreq>weekly</changefreq>
							  <priority>0.6</priority>
						   </url>";
			}

	echo "</urlset>";
?>