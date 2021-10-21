<?php

/**
 * Description of FilteredMap
 *
 * @author Kaloyan
 */
class FilteredMap {
    private $map;
    
    public function __construct(array $baseMap) {
        $this->map = $baseMap;
    }
    
    /**
     * 
     * @param type $name
     * @return type
     */
    public function has($name) {
        return isset($this->map[$name]);
    }
    /**
     * 
     * @param type $name
     */
    public function unsetElement($name) {
        unset($this->map[$name]);
    }
    /**
     * 
     * @param type $name
     * @return type
     */
    public function get($name) {
        return isset($this->map[$name]) ? $this->map[$name] : NULL;
    }
    /**
     * 
     * @param type $name
     * @return int
     */
    public function getInt($name) {
        return is_numeric($this->get($name)) ? (int) $this->get($name) : NULL;
    }
    /**
     * 
     * @param type $name
     * @return float
     */
    public function getNumber($name) {
        return is_numeric($this->get($name)) ? (float) $this->get($name) : NULL;
    }
    /**
     * 
     * @param type $name
     * @param type $filter
     * @return type
     */
    public function getString($name, $filter = true) {
        $value = (string) $this->get($name);
        return $filter ? htmlspecialchars(trim($value), ENT_QUOTES) : $value;
    }
    
    /**
     * 
     * @param type $name
     * @return valid Email or NULL
     */
    public function getEmail($name) {
        $value = $this->get($name);
        if (filter_var($value, FILTER_VALIDATE_EMAIL)) {
            return $value;
        }
        return NULL;
    }
    
    /**
     * 
     * @param type $name
     * @return valid Email or NULL
     */
    public function getEmailStrict($name) {
        $value = $this->get($name);
        if (preg_match("/^[a-zA-Z0-9_.-]+@[a-zA-Z0-9-]+.[a-zA-Z0-9-.]+$/", $value)) {
            list($username, $domain) = explode('@', $value);
            if (!checkdnsrr($domain, 'MX')) {
                return NULL;
            }
            return $value;
        }
        return NULL;
    }

}
