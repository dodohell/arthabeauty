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
        
        $rate = 0;
        if(!$to){
            global $currency;
            $to = $currency["code"];
            $rate = (double)$currency["rate"];
        }
        
        if($rate === 0){
            if(!$cache->hasItem("rate_{$to}")){
                $rate = Currencies::getRecordByCode($to)["rate"];
                
                $item = $cache->getItem("rate_{$to}")
                        ->set($rate)
                        ->expiresAfter($this->cacheTimeout);
                $cache->save($item);
            }
            $rate = $cache->getItem("rate_{$to}")->get();
        }
        
		
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
    
	public function convertToBaseCurrency($amount, $round = false, $from = ""){
        
        $rate = 0;
        if(!$from){
            global $currency;
            $from = $currency["code"];
            $rate = (double)$currency["rate"];
        }
        
        if($rate === 0){
            $rate = Currencies::getRecordByCode($from)["rate"];
        }
        
		if($rate > 0.0){
			$return = $amount / $rate;		
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