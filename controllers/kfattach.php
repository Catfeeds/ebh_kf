<?php

/**
 * 附件课件等下载请求控制器
 */
class KfattachController extends CControl {
	private $user = NULL;	//当前用户
	
	public function __construct() {
		parent::__construct();
		$this->user=$this->getLoginUser();
		if(empty($this->user)) {	//非法用户，则直接退出
			exit();
		}
	}
	
	public function index() {
		$cwid = $this->input->get('cwid');	//课件编号
		$attid = $this->input->get('attid');	//附件编号
		$noticeid = $this->input->get('noticeid'); //通知编号
		if(is_numeric($cwid) && $cwid > 0) {	//处理课件请求
			return $this->_docourse();
			
		} else if(is_numeric($attid) && $attid > 0) {	//处理附件请求
			return $this->_doattach();
		} 
	}
	/**
	*处理附件的请求下载
	*/
	private function _doattach() {
		$attid = $this->input->get('attid');	//附件编号
		$attachmodel = $this->model('Attachment');
		$attach = $attachmodel->getAttachById($attid);
		$this->curatt = $attach;
		$type = $this->input->get('type');

		if(!empty($attach)) {
			//权限处理
			if(!$this->_checkpermission($attid,1,$this->user)) {	//无权限
				return;
			};
			
			$url = $attach['url'];
			$name = $attach['filename'];
			$type = $this->input->get('type');
			if(!empty($type) && $type == 'preview' && $attach['ispreview'] == 1) {
				$suffix = '.swf';
				$name = strstr($name,'.',true).$suffix;
				$url = strstr($url,'.',true).$suffix;
			}
			getfile('attachment', $url, $name);
		}
	}
	/**
	*处理课件文件为附件格式的请求下载
	*/
	private function _docourse() {
		$cwid = $this->input->get('cwid');	//课件编号
		$coursemodel = $this->model('Courseware');
        $course = $coursemodel->getplaycoursedetail($cwid);
		$this->curcourse = $course;
		if(!empty($course)) {
			// 权限处理
			if(!$this->_checkpermission($cwid,2,$this->user)) {	//无权限
					return;
			};
			$url = $course['cwurl'];
            $name = $course['title'];
			$suffix = strstr($url,'.');
			if($suffix == '.ebh' || $suffix == '.ebhp'){
				getfile('course', $url, $name.$suffix);
			}else{
				$type = $this->input->get('type');

				if(!empty($type) && $type == 'preview' && $course['ispreview'] == 1) {
					$suffix = '.swf';
					$url = strstr($url,'.',true).$suffix;
				}
				getfile('attachment', $url, $name.$suffix);

			}
		}
	}
	
	/**
	 * 检查用户关联的权限
	 * @param unknown $toid 课件或者附件id
	 * @param unknown $type 1为课件 2为附件
	 * @param unknown $user 登录用户
	 */
	private function _checkpermission($toid,$type,$user){
		$info=$this->model('user')->getloginbyuid($user['uid'],$user['password'],true);//获取用户信息
		$access=$info['classroom'];
		if($access=="ALL"){//用户拥有全部学校的权限
			return true;
		}else if($access==""){//拥有学校权限为空
			return false;
		}else{//检测用户是否拥有该课件的权限
			if($type==1){//检测课件
				$school=$this->model('courseware')->getClassroomId($toid);
				if(strpos(',' . $access . ',', ',' . $school . ',') !== false){
					return true;
				}else{
					return false;
				}
			}elseif($type==2){
				$attachInfo=$this->model('attachment')->getAttachById($toid);
				$shcool=$attachInfo['crid'];
				if(strpos(',' . $access . ',', ',' . $school . ',') !== false){
					return true;
				}else{
					return false;
				}
			}
		}
		
	}
	
	/**
	 *根据客服系统接口过来的key获取当前用户
	 */
	private function getLoginUser() {
		if (isset($this->user))
			return $this->user;
		$auth = $this->input->get('k');
		$usermodel = $this->model('user');
		//var_dump($auth);
		if (!empty($auth)) {
			@list($password, $uid,$ip) = explode("\t", authcode($auth, 'DECODE'));
			$curip = $this->input->getip();
			if($curip != $ip)
				return FALSE;
			$uid = intval($uid);
			if ($uid <= 0) {
				return FALSE;
			}
			$user = $usermodel->getloginbyuid($uid,$password,TRUE);
			//var_dump($user);
			return $user;
		}
		return FALSE;
	}
}
