<?php


//include (__DIR__ ."/FilteredMap.php");

/**
 * Description of Request
 *
 * @author Kaloyan
 */
class Request {
    
    const GET = 'GET';
    const POST = 'POST';
    
    private $domain;
    private $path;
    private $method;
    private $params;
    private $cookies;
    private $ip;
    
    public function __construct() {
        $this->domain = $_SERVER['HTTP_HOST'];
        $this->path = $_SERVER['REQUEST_URI'];
        $this->method = $_SERVER['REQUEST_METHOD'];
        $this->ip = $_SERVER['REMOTE_ADDR'];
        
        $this->params = new FilteredMap($_REQUEST);       
        
        $this->cookies = new FilteredMap($_COOKIE);
    }
    
    public function getUrl() {
        return $this->domain . $this->path;
    }
    
    public function getDomain() {
        return $this->domain;
    }
    
    public function getPath() {
        return $this->path;
    }
    
    public function getMethod() {
        return $this->method;
    }
    
    public function getIp() {
        if (filter_var($this->ip, FILTER_VALIDATE_IP)){
            return $this->ip;
        }else{
            return NULL;
        }
    }
    
    public function isPost() {
        return $this->method === self::POST;
    }
    
    public function isGet() {
        return $this->method === self::GET;
    }
    
    public function getParams() {
        return $this->params;  
    }
    
    public function getCookies() {
        return $this->cookies;  
    }
}
