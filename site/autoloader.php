<?php

class autoloader {

    private $directory_name;

    public function __construct($directory_name) {
        $this->directory_name = $directory_name;
    }

    public function getInstallPath() {
        return $_SERVER["DOCUMENT_ROOT"]."/";
        // return '/home/dimitar/Code/arthabeauty/';
    }

    public function autoload($class_name) {
        $pieces = preg_split('/(?=[A-Z])/',$class_name, -1, PREG_SPLIT_NO_EMPTY);
        if($pieces){
            $file_name = 'class.' . strtolower(implode("_", $pieces)) . '.php';
        }else{
            $file_name = 'class.' . strtolower($class_name) . '.php';
        }

        $file = $this->getInstallPath() . $this->directory_name . '/' . $file_name;

        if (file_exists($file) == false) {
            return false;
        }
        include ($file);
    }
}
