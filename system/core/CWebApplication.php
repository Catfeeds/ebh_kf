<?php

/**
 * Web应用类，从Web请求进来主要有此类负责调用相关程序
 */
class CWebApplication extends CApplication {

    /**
     * 处理应用请求
     */
    public function processRequest() {
        $router = $this->getRouter();
        $uri = $this->getUri();
        $router->setUri($uri);
        $router->parse();
        $controller = $router->createController();
        $method = $uri->uri_method();
        if (method_exists($controller, $method)) {
            $controller->$method();
        } else {
            echo '';
        }
    }

    /**
     * 注册核心组件类
     */
    protected function registerCoreComponents() {
        parent::registerCoreComponents();

        $components = array(
            'user' => 'CUser',
            //'room' => 'CRoom'
        );

        $this->setComponents($components);
    }

}