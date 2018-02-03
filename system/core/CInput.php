<?php

/**
 * CInput输入类，主要针对$_GET（QUERY_STRING）和$_POST的包装
 */
class CInput {

    var $gets = NULL;
    private $prefix = 'ebh_';
    private $domain = '.ebanhui.com';
    private $path = '/';
	private $alldomain = 0;
    var $user_agent = FALSE;    //客户端浏览器USER_AGENT信息
    var $ip_address = FALSE;    //客户端地址

    public function __construct($config = array()) {
        $this->uri = Ebh::app()->getUri();
        if (isset($config['prefix'])) {
            $this->prefix = $config['prefix'];
        }
        if (isset($config['domain'])) {
            $this->domain = $config['domain'];
        }
        if (isset($config['path'])) {
            $this->path = $config['path'];
        }
		if (isset($config['alldomain'])) {
            $this->alldomain = $config['alldomain'];
        }
    }

    /**
     * 获取get对应值
     * @param string $key get对应的key，为NULL时则获取整个get数组
     * @param boolean $xss 是否进行xss过滤
     * @return string 返回get值
     */
    public function get($key = NULL, $xss = FALSE) {
        if (!isset($this->gets)) {
            $query_string = $this->uri->uri_query_string();
            if (!empty($query_string)) {
                parse_str($query_string, $this->gets);
            }
        }
        if ($key == NULL && $this->gets != NULL)
            return $this->gets;
        if ($this->gets === NULL || !isset($this->gets[$key]))
            return NULL;
        $value = $this->gets[$key];
        if ($xss) {  //过滤处理，预留
        }
        return $value;
    }

    /**
     * 获取post对应值
     * @param string $key post对应的key，为NULL时则获取整个post数组
     * @param boolean $xss 是否进行xss过滤
     * @return string 返回post值
     */
    public function post($key = NULL, $xss = FALSE) {
        if ($key == NULL)
            return $_POST;
        if (!isset($_POST[$key])) {
            return NULL;
        }
        $value = $_POST[$key];
        if ($xss) {  //过滤处理，预留
        }
        return $value;
    }

    /**
     * 获取cookie对应值
     * @param string $key post对应的key，为NULL时则获取整个cookie数组
     * @param boolean $xss 是否进行xss过滤
     * @return string 返回cookie值
     */
    public function cookie($key = NULL, $xss = FALSE) {
        if ($key == NULL)
            return $_COOKIE;
        $key = $this->prefix . $key;
        if (!isset($_COOKIE[$key])) {
            return FALSE;
        }
        $value = $_COOKIE[$key];
        if ($xss) {  //过滤处理，预留
        }
        return $value;
    }

    /**
     * 设置cookie值
     * @param string $key cookie key
     * @param string $value cookie value
     * @param int $life cookie有效期，以秒为单位
     * @return boolean 返回是否设置成功
     */
    public function setcookie($key, $value, $life = 0) {
        if (empty($key))
            return FALSE;
        $key = $this->prefix . $key;
        $expire = 0;
        if(!empty($life)) {
            $expire = SYSTIME + $life;
        }
		$domain = $this->domain;
		if(!empty($this->alldomain)) {	//如果设置alldomain值，则默认去当前主域名左右cookie域
			$uri = Ebh::app()->getUri();
			$domain = $uri->curdomain;
		}
        setcookie($key, $value, $expire, $this->path, $domain, $_SERVER['SERVER_PORT'] == 443 ? 1 : 0);
        return TRUE;
    }

    /**
     * 获取客户端浏览器user_agent信息
     * @return string 返回user_agent信息
     */
    public function user_agent() {
        if ($this->user_agent !== FALSE) {
            return $this->user_agent;
        }
        $this->user_agent = (!isset($_SERVER['HTTP_USER_AGENT'])) ? FALSE : $_SERVER['HTTP_USER_AGENT'];
        return $this->user_agent;
    }
    /**
     * 获取客户端IP地址
     * @return string IP_ADDRESS
     */
    public function getip() {
        if ($this->ip_address !== FALSE)
            return $this->ip_address;
        if (!empty($_SERVER["HTTP_CLIENT_IP"]))
            $this->ip_address = $_SERVER["HTTP_CLIENT_IP"];
        else if (!empty($_SERVER["HTTP_X_FORWARDED_FOR"]))
            $this->ip_address = $_SERVER["HTTP_X_FORWARDED_FOR"];
        else if (!empty($_SERVER["REMOTE_ADDR"]))
            $this->ip_address = $_SERVER["REMOTE_ADDR"];
        else
            $this->ip_address = "127.0.0.1";
        return $this->ip_address;
    }

}