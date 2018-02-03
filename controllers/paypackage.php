<?php
/**
 * 服务包 控制器
*/
class PaypackageController extends CAdminControl{
	public function getlist(){
		$param['crid'] = $this->input->post('crid');
		$list = $this->model('paypackage')->getlist($param);
		echo json_encode($list);
	}
}
?>