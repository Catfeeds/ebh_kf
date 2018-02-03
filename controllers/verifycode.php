<?php
/**
*验证码控制器
*/
class VerifycodeController extends CControl{
	public function __construct(){
		parent::__construct();
	}
	/**
	 *生成验证码,页面请求verifycode/getCode.html即可获取验证码
	 *
	*/
	public function getCode(){
		Ebh::app()->lib('VerifyCode')->buildImageVerify(4,1,'gif',30,20);
	}
	/**
	 *检查验证码,
	 *@param $cookie 
	 *@return bool;
	 *注:页面请求verifycode/checkCode.html即可检查验证码
	*/
	public function checkCode($cookie=0){
		if(empty($cookie)){
			$cookie = $this->input->get('code');
			echo EBH::app()->getInput()->cookie('verify')===$cookie;exit();
		}
		return EBH::app()->getInput()->cookie('verify')===$cookie;
	}
}
?>