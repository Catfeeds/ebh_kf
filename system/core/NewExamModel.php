<?php
/**
* NewExam数据库对应的Model类
*/
class NewexamModel {
	var $ebhdb;
	var $target_server;
	public function __construct() {
		$dataserver = EBH::app()->getConfig('dataserver')->load('dataserver');
        $servers = $dataserver['servers'];
        //随机抽取一台服务器
        $target_server = $servers[array_rand($servers,1)];
        $this->target_server = $target_server;
		$this->ebhdb = Ebh::app()->getOtherDb('ebhdb');
	}
}