<?php
/**
* Ebh数据库对应的Model类
*/
class CEbhModel extends CModel{
	var $ebhdb;
	public function __construct(){
		parent::__construct();
		$this->ebhdb = Ebh::app()->getOtherDb('ebhdb');
	}
}