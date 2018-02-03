<?php
/*
后台权限
*/
class CAdminControl extends CControl{
	public function __construct(){
		error_reporting(E_ERROR | E_WARNING | E_PARSE);
		parent::__construct();
		//checklogin
		Ebh::app()->lib('Access')->checkLogin();
	}
}
?>