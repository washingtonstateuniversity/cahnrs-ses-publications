<?php namespace CAHNRS\Plugin\SES_Pubs;

class Plugin {
    public static function setup_classes(){

    }

    public static function init() {
        self::setup_classes();

        require_once __DIR__ . '/functions.php';    
        require_once __DIR__ . '/scripts.php'; 
    }
}

Plugin::init();