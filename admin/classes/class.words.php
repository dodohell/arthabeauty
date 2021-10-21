<?php
	class Words extends Settings{
		
		public $pagination = "";
		public $lpath = "lang/";
		
		
		function getLngFiles($dir = "") {
			$result = array();
			if ( !trim($dir) ){
				$dir = $_SERVER["DOCUMENT_ROOT"]."/".$this->lpath;
			}
			if (is_dir($dir)) {
				if ($dh = opendir($dir)) {
					while (($file = readdir($dh)) !== false) {
						if( preg_match( "/^.{2}\.txt$/" ,$file) ) {
							list($lng, $ext) = explode(".",$file);
						  $result[$lng] = $file;
						}
					}
					closedir($dh);
				}
			}
			
			return $result;
		}

		function loadConfig($dir, $file) {
			$content = file("$dir/$file");
			$result = array();
			foreach($content as $line) {
				if( preg_match('/^(.*)=(.*)$/', $line) ) {
					list($param, $value) = explode("=", $line);
					$param = trim($param);
					$value = trim($value);
					$result[$param] = $value;
				}
			}
			return $result;
		}

		function getValue($config, $param) {
			return $config[$param];
		}

		function addLang($arr, $config, $lng) {
			foreach($config as $param => $val) {
				$arr[$param][$lng] = str_replace("\\", "",  $val);
			}
			return $arr;
		}

		function saveConfig($file, $conf) {
			$file = $_SERVER["DOCUMENT_ROOT"]."/".$file;
			$fp = fopen($file, "w");
			foreach($conf as $k => $v) {
				$line = "$k = $v\r\n";
				@fwrite($fp, $line);
			}
			@fclose($fp);
		}

		function backupFiles($langs) {
			$t = time();
			$dir = $_SERVER["DOCUMENT_ROOT"]."/lang/";
			$target = "$dir/$t/";
			mkdir($target);
			foreach($langs as $k => $v) {
				$source = $dir.$v;
				$dest = $target.$v;
				copy($source, $dest);
			}
		}

		function getContent(){
			
			$langs = array();
			$lpath = $_SERVER["DOCUMENT_ROOT"]."/lang/";
			
			$files = $this->getLngFiles($lpath);
			
			$langs = array();
			foreach($files as $k => $v) {
				$config = $this->loadConfig($lpath, $v);
				$langs = $this->addLang($langs, $config, $k);
			}
			ksort($langs);

			return $langs;
		}

		function saveChanges($params){
			$files = $this->getLngFiles();
			
			foreach($files as $k => $v) {
				$this->saveConfig($this->lpath.$v, $params[$k] );
			}
		}



	}
	
?>