<?php

/**
 * 附件下载请求控制器
 */
class AttachController extends CControl {
	private $curcourse = NULL;	//当前的课件对象
	private $curatt = NULL;	//当前的附件对象
	private $flag = 0;	//当前处理对象，1为课件 2为附件 3为通知附件
	private $user=null;
	public function __construct(){
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
		$examcwid = $this->input->get('examcwid'); //通知编号
		if(is_numeric($cwid) && $cwid > 0) {	//处理课件请求
			$this->flag = 1;
			return $this->_docourse();
		} else if(is_numeric($attid) && $attid > 0) {	//处理附件请求
			$this->flag = 2;
			return $this->_doattach();
		} else if(is_numeric($noticeid) && $noticeid > 0) {
			$this->flag = 3;
			return $this->_donotice();
		} else if(is_numeric($examcwid)){
			$this->flag = 4;
			return $this->_doexamattach();
		}
	}
	/**
	*处理附件的请求下载
	*/
	private function _doattach() {
		$attid = $this->input->get('attid');	//附件编号
		if(!$this->_checkpermission($attid,1,$this->user)) {	//无权限
				return;
		};		
		$attachmodel = $this->model('Attachment');
		$attach = $attachmodel->getAttachById($attid);
		$this->curatt = $attach;
		if(!empty($attach)) {
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
		$inajax = $this->input->get('inajax');	//是否ajax调用权限
		if($inajax == 1) {
			return $this->_initajax();
		}
		$cwid = $this->input->get('cwid');
		// var_dump($cwid);	//课件编号
		if(!$this->_checkpermission($cwid,2,$this->user)) {	//无权限
				return;
		};
		$coursemodel = $this->model('Courseware');
        $course = $coursemodel->getplaycoursedetail($cwid);
		$this->curcourse = $course;

		if(!empty($course)) {
			$url = $course['cwurl'];
            $name = $course['title'];
			$suffix = strstr($url,'.');

			if($this->input->get('m3u8')){
				getfile('attachment',$course['m3u8url'],$name.'.m3u8');
			}else if($suffix == '.ebh' || $suffix == '.ebhp'){
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
	private function _initajax() {
		$cwid = $this->input->get('cwid');	//课件编号
		$fromid = $this->input->get('fromid');	//来源crid，如在小学平台看全科复习的内容，则此id为小学平台的id
		$coursemodel = $this->model('Courseware');
        $course = $coursemodel->getplaycoursedetail($cwid);
		$this->curcourse = $course;
		$errorcode = 2;
		$cwsource = '';
		if(!empty($course)) {
			$user = Ebh::app()->user->getloginuser();
			if($course['isfree'] != 1) {	//不是免费课件的文件需要判断权限
				$user = Ebh::app()->user->getloginuser();
				if(empty($user))
					$errorcode = 1;
				if($errorcode != 1) {
					$crid = $course['crid'];
					if(!$this->checkpermission($crid,$fromid)) {	//无权限
						$errorcode = 2;
					} else {
						$errorcode = 0;
						$cwsource = $course['cwsource'];
					}
				}
			} else {
				$errorcode = 0;
			}
		}
		$result = array();
		if($errorcode == 0) {
			$result['status'] = 1;
			$serverlib = Ebh::app()->lib('Serverlib');
			$source = $serverlib->getCourseSource();	//默认从配置文件中读取课件所在服务器地址
			if(!empty($source)) {
				$cwsource = $source;
			}
			$result['source'] = $cwsource;
		} else if($errorcode == 1) {
			$result['status'] = -1;
		} else if($errorcode == 2) {
			$result['status'] = 0;
		}
		echo json_encode($result);
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
		if($access="ALL"){//用户拥有全部学校的权限
			return true;
		}else if($access=""){//拥有学校权限为空
			return false;
		}else{//检测用户是否拥有该课件的权限
			if($type==1){//检测课件
				$school=$this->model('courseware')->getClassroomId($toid);
				if(strpos($access,$school)){
					return true;
				}else{
					return false;
				}
			}elseif($type==2){
				$attachInfo=$this->model('attachment')->getAttachById($toid);
				$shcool=$attachInfo['crname'];
				if(strpos($access,$school)){
					return true;
				}else{
					return false;
				}
			}
		}
		
	}
	
	private function _donotice(){
		$noticeid = $this->input->get('noticeid');
		$noticemodel = $this->model('notice');
		$notice = $noticemodel->getNoticeByNoticeid($noticeid);
		$user = Ebh::app()->user->getloginuser();
		$crmodel = $this->model('classroom');
		if($user['groupid']==6)
			$roomuser = $crmodel->checkstudent($user['uid'],$notice['crid']);
		else
			$roomteacher = $crmodel->checkteacher($user['uid'],$notice['crid']);
		if((!empty($roomuser)&&$roomuser==1) || (!empty($roomteacher)&&$roomteacher==1)){
			$attmodel = $this->model('attachment');
			$attachment = $attmodel->getAttachByIdForNotice($notice['attid']);
			$url = $attachment['url'];
			$filename = $attachment['filename'];
			getfile('attachment', $url, $filename);
		}
	}

	private function _doexamattach(){
		$examcwid = $this->input->get('examcwid'); //通知编号
		$ucoursemodel = $this->model('Usercourseware');
		$course = $ucoursemodel->getUserCourse($examcwid);
		if(!empty($course)){
			$url = $course['cwurl'];
			$filename = basename($course['cwurl']);
			getfile('attachment', $url, $filename);
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
