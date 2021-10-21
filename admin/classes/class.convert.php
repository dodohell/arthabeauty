<?php
Class Convert extends Settings {
	
	private $cachable = FALSE;
	
	/*
	* Length of cache in seconds
	*
	* Default is 2 hours
	*/
	
	private $cacheTimeout;
	
	/*
	* Set $cacheTimeout for length of caching in seconds
	*/
	
	public function __construct($cache = TRUE, $cacheTimeout = 7200){
		if ($cache == TRUE) {
			$this->cachable     = TRUE;
			$this->cacheTimeout = $cacheTimeout;		
		}
	}
	
	/*
	* Main function for converting
	*
	* Set $round to FALSE to return full amount
	*/
	
	public function convert($amount, $round = false, $to = ""){
        global $cache;
        
        // if(!$cache->hasItem("rate_{$to}")){
		if(1) {
            $rate = (double)Currencies::getRecordByCode($to)["rate"];

            $item = $cache->getItem("rate_{$to}")
                    ->set($rate)
                    ->expiresAfter($this->cacheTimeout);
            $cache->save($item);
        }
        $rate = $cache->getItem("rate_{$to}")->get();
        
		if($rate > 0.0){
			$return = $rate * $amount;			
		}else{
			if (!$this->validateCurrency($to)) {
				throw new Exception('Invalid currency code - must be exactly 3 letters');
			}
			throw new Exception('Invalid rate');
		}
		
		return ($round) ? abs(round($return, 2)) : abs($return);
	}
	
	/*
	* Validates the currency identifier
	*/
	
	protected function validateCurrency()
	{
		foreach (func_get_args() as $val) {		
			if (strlen($val) !== 3 || !ctype_alpha($val)) {
				if (strtoupper($val) != 'BEAC') {			
					return FALSE;				
				}
			}
		}
		
		return TRUE;	
		
	}
	
}