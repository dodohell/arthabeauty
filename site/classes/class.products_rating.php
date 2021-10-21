<?php
	class ProductsRating extends Settings{
		
		
		public static function getRecord(int $id){
			global $db;
			global $products_comments_table;
			
			$row = $db->getRow("SELECT * FROM ".$products_comments_table." WHERE id = ".$id); safeCheck($row);
			
			return $row;
		}
        
		public static function getProductComments(int $product_id, int $returnType = 1){
			global $db;
			global $products_comments_table;
            
			$products_comments = $db->getAll("SELECT
                                                *
                                            FROM 
                                                ".$products_comments_table." 
                                            WHERE 
                                                edate = 0
                                            AND product_id = {$product_id}
                                            ORDER BY postdate DESC"); safeCheck($products_comments);
            foreach ($products_comments as $k => $v) {
                $words = explode(" ", trim($v["nickname"]));
				$nick_initials = mb_substr($words[0], 0, 1)." ".mb_substr($words[sizeof($words)-1], 0, 1);
                $products_comments[$k]["initials"] = $nick_initials;
            }
            if($returnType === 1){
                return $products_comments;
            }else if($returnType === 3){
                echo json_encode($products_comments);
            }
		}
        
        /**
         * 
         * @global type $user
         * @global Smarty $sm
         * @param FilteredMap $params
         */
        public function leaveRatingAndComment($params){
            global $db;
            global $sm;
            global $user;
            global $language_file;
            global $products_comments_table;
            
            $rating = $params->getNumber("score");
            $title = $params->getString("reviewTitle");
            $comment = $params->getString("comment");
            $product_id = $params->getInt("product_id");
            //$user_id = $user["id"];
            
            if( strlen( $user['first_name'] ) < 1 && strlen( $user['last_name'] < 1 ) ){
                $nickname = $user['email'];
            }else{
                $nickname = $user['first_name'] ." ". $user['last_name'];
            }
            
            $sm->configLoad($language_file);
            $langVs = $sm->getConfigVars();
            
            if ($rating && $product_id){
                if(isset($user['id'])){
                    $pdate = time();
                    $fieldsComment =  array(
                        'product_id' 	=> $product_id,
                        'nickname'		=> $nickname,
                        'rating' 		=> $rating,
                        'title' 		=> $title,
                        'comment' 		=> $comment,
                        'user_id' 		=> $user['id'],
                        'ip_address'	=> $_SERVER["REMOTE_ADDR"],
                        'approved' 		=> 0,
                        'postdate' 		=> $pdate
                    );

                    $check  = $db->getRow("SELECT id FROM ".$products_comments_table." WHERE edate = 0 and product_id = {$product_id} AND user_id = ".$user['id']); 
                    if(!$check){
                        $res = $db->autoExecute($products_comments_table, $fieldsComment, DB_AUTOQUERY_INSERT); safeCheck($res);
                        
                        $message['status'] = '200';
                        $message['description'] = $langVs['comments_left_comment_200'];
                    }else{
                        $message['status'] = '301';
                        $message['description'] = $langVs['comments_left_comment_301'];
                    }
                }else{
                    $message['status'] = '303';
                    $message['description'] = $langVs['comments_left_comment_303'];
                }
            }else{
                $message['status'] = '302';
                $message['description'] = $langVs['comments_left_comment_302'];
            }
            header('Content-Type: application/json');
            echo json_encode($message);
        }
	}