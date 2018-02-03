<?php
/**
* Sns数据库对应的Model类
*/
class CSnsModel{
	var $ebhdb;
	public function __construct(){
		//parent::__construct();
		$this->snsdb = Ebh::app()->getOtherDb('snsdb');
	}
}