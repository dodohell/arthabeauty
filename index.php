<?php
	include "./globals.php";
	
	$items_per_page = 9;
	$page = (int)$_REQUEST["page"];
	$start = ($page)*$items_per_page;
	
	$sql = "SELECT products.id,
										products.name_{$lng} AS name,
										products.excerpt_{$lng} AS excerpt,
										products.description_{$lng} AS description,
										products.picture,
										products.new_product,
										products.price_specialoffer,
										products.pic,
										products.price,
										(SELECT images.pic FROM ".$products_images_table." AS images WHERE images.product_id = products.id ORDER BY pos LIMIT 1) AS pic
							  FROM ".$products_table." AS products
							  WHERE products.edate = 0
							  AND products.hotoffer = '1'
							  ORDER BY products.id DESC
							  LIMIT {$start}, {$items_per_page}
							";
	
	$hotoffers = $db->getAll($sql); safeCheck($hotoffers);
	
	foreach($hotoffers as $k=>$v){			
		$v['link_title'] = str_replace($link_find, $link_repl, $v['product_name']);
		$cat_tmp = $db->getRow("SELECT categories.id, categories.name_{$lng} AS name FROM ".$categories_table." AS categories, ".$product_to_category_table." AS ptc WHERE ptc.category_id = categories.id AND ptc.product_id = '".$v["id"]."'"); safeCheck($cat_tmp);
		$v["category_name"] = $cat_tmp["name"];
		$sizes = getImageSize($install_path."/files/tn/".$v["pic"]);
		$v["image_width"] = $sizes[0];
		$v["image_height"] = $sizes[1];
		$v["price"] = number_format($v["price"], 2, '.', '0');
		$v["price_specialoffer"] = number_format($v["price_specialoffer"], 2, '.', '0');
		$hotoffers[$k] = $v;
	}
	
	$sm->assign("hotoffers", $hotoffers);
	
	$products_count = $db->getRow("
									SELECT count(products.id) AS cntr
									FROM ".$products_table." AS products
									WHERE products.edate = 0
									AND products.firstpage = '1'
									"); safeCheck($products_count);
	
	$pages_count = ceil($products_count["cntr"]/$items_per_page);
	$sm->configLoad($language_file);
	$configVars = $sm->getConfigVars();
	
	$sm->configLoad($htaccess_file);
	$htaccessVars = $sm->getConfigVars();
	
	if ( $page > 1  ){
		$generate_pages .= '<a href="/'.$htaccessVars["htaccess_categories"].'/'.$id.'/'.$htaccessVars["htaccess_page"].'-'.($page-1).'">&lt;</a>';
	}else{
		$generate_pages .= '&lt;';
	}
	
	if ($page > 5 ){
		$starting_page = $page-5;
	}else{
		$starting_page = 0;
	}
	if (($pages_count-$page) < 5 ){
		$ending_page = $pages_count;
	}else{
		$ending_page = $page+5;
	}
	for( $i = $starting_page ; $i < $ending_page ; $i++ ){
		if ( $i == $page){
			if ( $i + 1 == 1 ){
				$generate_pages .= '<a href="/">'.($i+1).'</a>';
			}else{
				$generate_pages .= '<a href="/'.$htaccessVars["htaccess_hotoffers"].'/'.$htaccessVars["htaccess_page"].'-'.($i).'">'.($i+1).'</a>';
			}
		}else{
			if ( $i + 1 == 1 ){
				$generate_pages .= '<a href="/">'.($i+1).'</a>';
			}else{
				$generate_pages .= '<a href="/'.$htaccessVars["htaccess_hotoffers"].'/'.$htaccessVars["htaccess_page"].'-'.($i).'">'.($i+1).'</a>';
			}
		}
		
		
	}
	if ( $pages_count > 1 && $pages_count != $page){
			$generate_pages .= '<a href="/'.$htaccessVars["htaccess_hotoffers"].'/'.$htaccessVars["htaccess_page"].'-'.($page).'">&gt;</a>';
	}else{
		$generate_pages .= '&gt;';
	}
	$sm->assign("generate_pages", $generate_pages);
	
	
	
	
	$promotion = $db->getRow("SELECT products.id,
										products.name_{$lng} AS name,
										products.excerpt_{$lng} AS excerpt,
										products.description_{$lng} AS description,
										products.picture,
										products.new_product,
										products.pic,
										products.price,
										products.price_specialoffer,
										(SELECT images.pic FROM ".$products_images_table." AS images WHERE images.product_id = products.id ORDER BY pos LIMIT 1) AS pic,
										(SELECT images.pic FROM ".$products_images_table." AS images WHERE images.product_id = products.id ORDER BY pos LIMIT 1,1) AS pic_2
							  FROM ".$products_table." AS products
							  WHERE products.edate = 0
							  AND products.promotion_month = '1'
							  ORDER BY products.id
							  LIMIT 1
							"); safeCheck($promotion);
	
	$sm->assign("promotion", $promotion);
	
	
	$counter = 1;
	foreach($menus as $k=>$v){
		if ($v["menu_pos"] == "3_highlights"){
			$scripts[] = '{"id":"slide-img-'.$counter.'","client":"","desc":""}';
			$counter++;
		}
	}
	
	$top_categories = $db->getAll("SELECT id, name_{$lng} AS name, url_{$lng} AS url, pic_2 FROM ".$categories_table." WHERE edate = 0 AND active = 'checked' AND first_page = 1 AND category_id = 0 ORDER BY pos LIMIT 7"); safeCheck($top_categories);
	
	$sm->assign("top_categories", $top_categories);
	
	$sm->assign("script", implode(", ",$scripts));
	$sm->assign("page", $page);
	$sm->assign("index", 1);
	
	$sm->display("./index.html");
?>