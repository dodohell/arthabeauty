<?php
	class Settings{
		function generateEditor($langs, $fieldName, $contentValue){
			$tabs = "";
			for($i = 0; $i < count($langs); $i++) {
				$field = $fieldName . "_" . strtolower($langs[$i]['short']);
				if ($i == 0){
					$activeTab = " activeTab";
					$activeTabEditor = " activeTabEditor";
				}else{
					$activeTab = "";
					$activeTabEditor = "";
				}
				
				$tabs .= '<div class="tabLanguage'.$activeTab.'">'.$langs[$i]["long"].'</div>';
				$editors .= '<div class="tabLanguageEditor'.$activeTabEditor.'"><textarea class="ckeditor inputField" cols="80" id="'.$field.'" name="'.$field.'" rows="10">'.$contentValue[$field].'</textarea></div>';
				
			}
			$js = '
					<script language="javascript" type="text/javascript">
					
					$(document).ready(function(){
						$("#editor_'.$fieldName.' .tabLanguage").bind("click", function(){
							var index = $("#editor_'.$fieldName.' .tabLanguage").index(this);
							$("#editor_'.$fieldName.' .tabLanguage").removeClass("activeTab");
							$(this).addClass("activeTab");
							
							$("#editor_'.$fieldName.' .tabLanguageEditor").removeClass("activeTabEditor");
							$("#editor_'.$fieldName.' .tabLanguageEditor").eq(index).addClass("activeTabEditor");
							
						});
					});
					</script>
					';
			
			return $js.'<div class="editorHolder" id="editor_'.$fieldName.'">'.$tabs.'<div class="clear"></div>'.$editors.'</div>'.$js_end;
		}
		
		function updateHtaccess( $lang, $htaccess_url, $type, $record_id = 0 ){
			global $db;
			global $htaccess_table;
			
			$fields = array(
								"lang" => $lang,
								"htaccess_url" => $htaccess_url,
								"type" => $type,
								"record_id" => $record_id
							);
			$result = $this->checkHtaccess($htaccess_url, $type, $record_id);
			
			if ( !$result )	{
				$res = $db->autoExecute($htaccess_table, $fields, DB_AUTOQUERY_INSERT); safeCheck($res);
			}else{
				$update_id = $this->checkHtaccessByID($htaccess_url, $type, $record_id);
				if ( $update_id ){
					$res = $db->autoExecute($htaccess_table, $fields, DB_AUTOQUERY_UPDATE, " id = '".$update_id."' "); safeCheck($res);
				}
			}
		}
		
		function checkHtaccess($htaccess_url, $type = "", $record_id = 0, $lang = "") {
            global $db;
            global $htaccess_table;
            
            $sql = "SELECT * FROM " . $htaccess_table . " WHERE edate = 0 AND htaccess_url = '" . $htaccess_url . "'";
            $check = $db->getRow($sql); safeCheck($check);
            
            $result = 0;
            if ($check["id"]) {
                $result = 1;
//                if ($record_id != $check["record_id"]) {
//                    $result = 0;
//                }
                if ($record_id == $check["record_id"] && $type == $check["type"] && $lang == $check["lang"]) {
                    $result = 0;
                }
            }
            
            return $result;
        }
		
		function checkHtaccessByID( $htaccess_url, $type = "" , $record_id = 0 ){
			global $db;
			global $htaccess_table;
			
			$check = $db->getRow("SELECT * FROM ".$htaccess_table." WHERE edate = 0 AND htaccess_url = '".$htaccess_url."' AND record_id = '".$record_id."'"); safeCheck($check);
			
			
			return $check["id"];
		}
		
		function checkLogin(){
			
			if ( $_SESSION["userAdmin"]["level"] == 1 ){
				header("Location: unauthorized.php");
				die();
			}
		}
		
		
		function mailSender($email, $message_heading, $message_text, $file_attachment_1 = "", $file_attachment_2 = "", $file_attachment_3 = "",$email_content = array(), $reply_tos = array() ){
			global $domains_cyrillic;
			global $install_path;
			global $sm;
			global $db;
			global $lng;
			global $host;
			global $contacts;
			global $copyrights;
			require_once($install_path."phpmailer/class.phpmailer.php");
			
			$sm->assign("subject", $message_heading);
			$sm->assign("message", $message_text);
			
			if ( $lng == 'ro' ){
				$logo = '<img src="'.$host.'images/logo-ro.png" border="0" />';
			}else{
				$logo = '<img src="'.$host.'images/logo.png" border="0" />';
			}
			
                        if(!empty($email_content)){

                            $message_text = '<html>
                                                <head>
                                                    <meta charset="utf-8">
                                                    <title>'.$message_heading.'</title>
                                                </head>
                                                <body style="background-color: #F7F6F5;" >
                                            
                                                    <table cellpadding="0" align="center" cellspacing="0" style="background-color: #fff;width: 580px;">
                                                        <tr>
                                                            <td valign="top" align="center">
                                                                <a href="'.$host.'" target="_blank">'.$logo.'</a>
                                                                <br>
                                                            </td>
                                                        </tr>
                                                        <tr>
                                                            <td colspan="2" height="10" bgcolor="#B48F6C">
                                                            </td>
                                                        </tr>
                                                        <tr>
                                                            <td  height="10">
                                                            </td>
                                                        </tr>
                                                        <tr>
                                                            <td style="text-transform: uppercase; color: #b69371; font-size: 18px; text-align: center; padding:20px 0;">
                                                                <a href="'.$email_content["url"].'" style="text-decoration:none;color:inherit">'.$email_content["title"].'</a>
                                                            </td>
                                                        </tr>
                                                        <tr> 
                                                            <td valign="top">
                                                                 <div width="580">
                                                                    <a href="'.$email_content["url"].'" style="text-decoration:none;color:inherit"> <img src="'. $host.'images/'. $email_content["pic_1"].'" style="width: 580px;" align="center"></a>  
                                                                 </div>
                                                                 <div  style="clear:both;" ></div>
                                                                 <div  style="padding:20px 30px;  line-height: 22px;" width="580">
                                                                     <a href="'.$email_content["url"].'" style="text-decoration:none;color:inherit"><br>'.$message_text.$email_content["description"].'</a>
                                                                 </div>
                                                            </td>
                                                        </tr>
                                                         <tr>
                                                            <td valign="top" >
                                                            	<a href="'.$email_content["url"].'" style="text-decoration:none;color:inherit;color:#dabd95; padding: 0px 30px;font-size:18px;">
                                                                    Oтзиви от щастливи младоженци:
                                                                </a>
                                                                    <br/><br/>
                                                             </td>
                                                        </tr>
                                                    </table>
                                                    <table cellpadding="0" align="center"  width="580" cellspacing="0">
                                                       <tr>
                                                            <td valign="top" >
                                                                <a href="'.$email_content["url"].'" style="text-decoration:none;color:inherit">
                                                                    <div style="color:#fff;font-size:18px;">
                                                                        <div style="background-color:#dabd95;padding: 10px 30px 30px 30px;;">
                                                                           <a href="'.$email_content["url"].'" style="text-decoration:none;color:inherit"> '.$email_content["testimonials"].'</a> 
                                                                        </div>
                                                                    </div>
                                                                </a>    
                                                            </td>
                                                        </tr>
                                                    </table>
                                                </body>
                                            </html>';echo $message_text;//exit;
                        }else{
                            $message_text = '<html>
                                                <head>
                                                        <title>'.$message_heading.'</title>
                                                </head>
                                                <body>
                                                        <table width="100%" cellpadding="0"  cellspacing="0">
                                                                <tr>
                                                                        <td valign="top" width="240">
                                                                                <a href="'.$host.'" target="_blank">'.$logo.'</a>
                                                                                <br>
                                                                        </td>
                                                                        <td valign="top">
                                                                                <span style="font-size: 11px;">
                                                                                '.$contacts["description"].'
                                                                                <br>
                                                                                </span>
                                                                                <br>
                                                                        </td>
                                                                </tr>
                                                                <tr>
                                                                        <td colspan="2" height="10" bgcolor="#B48F6C">
                                                                        </td>
                                                                </tr>
                                                        </table>

                                                        <table width="100%" cellpadding="0" cellspacing="0">
                                                                <tr>
                                                                        <td style="padding:5px;">
                                                                                <br><br><br>
                                                                                '.$message_text.'
                                                                                <br><br><br>
                                                                        </td>
                                                                </tr>
                                                        </table>
                                                        <table width="100%" cellpadding="5" cellspacing="0">
                                                                <tr>
                                                                        <td valign="top" bgcolor="#B48F6C">
                                                                                <span style="color: #ffffff; font-size: 11px;">'.$copyrights["description"].'</span>
                                                                        </td>
                                                                </tr>
                                                        </table>
                                                </body>
                                        </html>';
                        }

			$mailObj = new PHPMailer();
			$mailObj->CharSet = 'utf-8';
			if ( $lng == "ro" ){
				$mailObj->From      = 'support@miresesinunti.ro';
				$mailObj->FromName  = 'MireseŞiNunţi.ro';
			}else{
				$mailObj->From      = 'support@weddingday.bg';
				$mailObj->FromName  = 'WeddingDay.bg';
			}
			$mailObj->isHTML(true);
			$mailObj->Subject   = $message_heading;
			$mailObj->Body      = $message_text;
			$mailObj->AddAddress( $email );

			if ( sizeof($reply_tos) > 0 ){
				foreach( $reply_tos as $k => $v ){
					$mailObj->addReplyTo( $v );
				}
			}
			
			if ( $file_attachment_1 ){
				$mailObj->AddAttachment( $file_attachment_1 );
			}

			if ( $file_attachment_2 ){
				$file_to_attach = $install_path.'files/emails/'.$file_attachment_2;
				$mailObj->AddAttachment( $file_to_attach , $file_attachment_2 );
			}

			if ( $file_attachment_3 ){
				$file_to_attach = $install_path.'files/emails/'.$file_attachment_3;
				$mailObj->AddAttachment( $file_to_attach , $file_attachment_3 );
			}

			return $mailObj->Send();
		}
		
	}

?>