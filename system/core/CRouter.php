<?php

/**
 * Web路由类
 */
class CRouter {
    private $controllers;
    private $action;
    private $module;
    private $uri;
    public function __construct() {
        
    }
    public function parse() {    
        if(empty($this->uri)) {
            $this->uri = Ebh::app()->getUri();
        }
        $this->uri->parse_uri();
    }
    public function setUri($uri) {
        $this->uri = $uri;
    }
    /**
     * 创建控制器
     * @return object 控制器对象
     */
    public  function createController() {
        $control = $this->uri->uri_control();
        if(empty($control)) {
            show_404();
        }
        $directory = $this->uri->uri_directory();
        if(!empty($directory)) {
            $controlpath = CONTROL_PATH.$directory.'/'.$control.'.php';
        } else {
            $controlpath = CONTROL_PATH.$control.'.php';
        }
        if(!file_exists($controlpath)) {
            show_404();
            return false;
        }
        $controlname = ucfirst($control).'Controller';
        require $controlpath;
        if(class_exists($controlname)) {
            $controller = new $controlname;
        }
        return $controller;
    }
    
}