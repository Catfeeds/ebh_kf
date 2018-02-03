<?php
/**
 * 数据审核控制器
 */
class MemController extends CControl{
	public function index() {
		$cache = Ebh::app()->getCache();
		$value = "1234";
		$key = 'memkey1';
		$result = $cache->set($key,$value,20);
		if($result !== FALSE) {
			echo 'set cache success';
		} else {
			echo 'set cache fail';
		}
	}
	public function getm() {
		$cache = Ebh::app()->getCache();
		$key = 'memkey1';
		$value = $cache->get($key);
		echo $value;
	}
}