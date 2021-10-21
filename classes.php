<?
class Config {
 var $content;
 var $filename;
 
      function Config() { // constructor
			       $this->content = array();
			}
			function loadFile($fname) {
			$this->filename = $fname;
					 if( file_exists($fname) ) {
						$f = file($fname);
						foreach($f as $line) {
								list($key, $value) = explode("=", $line);
								$this->content[trim($key)] = trim($value);
						}
						$f = array();
					}
			}
			
			function saveFile() {
				$fp = fopen($this->filename, "w");
				foreach($this->content as $key => $value) {
				      $line = trim($key)."=".trim($value)."\r\n";
							fwrite($fp, $line);
				}
				fclose($fp);
			}
			
			function setValue($key, $value) {
			    $this->content[$key] = $value;
			}
}
?>