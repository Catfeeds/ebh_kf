<?php 
/**
*播放控制器
**/
class PlayController extends CAdminControl{
	/**
	 * 课件flv,MP3播放
	 * 
	 */
	public function  index(){
		$request = $this->input->get();
		$cwid= $request['cwid'];
		$attid= $request['attid'];
		// var_dump($request);
		if(is_numeric($cwid)&&$cwid>0){
			//课件	
			$coursemodel = $this->model('Courseware');
			$course = $coursemodel->getcoursedetail($cwid);
			$serverutil = Ebh::app()->lib('ServerUtil');	//生成课件和附件所在服务器地址
			$source = $serverutil->getCourseSource();
			if(!empty($source))
				$course['cwsource'] = $source;
			// var_dump($course);
			$user = Ebh::app()->user->getloginuser();
			$type = $this->input->get('type');	//如果type为1则表示普通播放，即不采用m3u8方式播放
			if($course['ism3u8'] == 1 && $type != 1) {	//rtmp特殊处理 
				$m3u8source = $serverutil->getM3u8CourseSource();
				if(!empty($m3u8source)) {
					$key = $this->getKey($cwid);
					$key = urlencode($key);
					$m3u8url = "$m3u8source?k=$key&id=$cwid&.m3u8";
					$course['m3u8url'] = $m3u8url;
				}
			} else if($course['isrtmp'] == 1 && $type != 1) {	//rtmp特殊处理
				$rtmpsource = $serverutil->getRtmpCourseSource();
				if(!empty($rtmpsource)) {
					$key = $this->getKey($cwid);
					$cwurl = $course['cwurl'];
					$key = urlencode($key);
					$rtmpurl = "$rtmpsource?k=$key&id=$cwid/flv:$cwurl";
					$course['rtmpurl'] = $rtmpurl;
				}
			}
			$arr = explode('.',$course['cwurl']);
			$types = $arr[count($arr)-1];
			if($types != 'flv' && $course['ism3u8'] == 1) {
				$types = 'flv';
			}
			$this->assign('types',$types);
			// var_dump($types);
			$this->assign('course',$course);
			$this->assign('source',$source);
			// var_dump($source);
			$k=$this->_getauthkey();
			$this->assign('k',$k);
			//var_dump($course);
			//$url= $source.'jsattach.html?cwid='.$cwid.'&k='.$this->_getauthkey();

		}elseif(is_numeric($attid)&&$attid>0){
			//附件
			$attachmodel = $this->model('Attachment');
			$attach = $attachmodel->getAttachById($attid);
			$arr = explode('.',$attach['url']);
			$types = $arr[count($arr)-1];			
			$serverutil = Ebh::app()->lib('ServerUtil');	//生成课件和附件所在服务器地址
			$source = $serverutil->getCourseSource();
			if(!empty($source)){
				$attach['source'] = $source;			
			}
			
			$this->assign('types',$types);
			$this->assign('attach',$attach);
			$k=$this->_getauthkey();
			$url= $source."/attach.html?attid={$attach['attid']}&k={$k}";
			$this->assign('k',$k);
		}
		
		$this->assign('url',$url);
		$this->display("data/play");
	}
	
	/**
	 * 生成加密字符串
	 * @return string
	 */
	protected function _getauthkey(){
		$user = Ebh::app()->user->getloginuser();
		$password = $user['password'];
		$ip= $this->input->getip();
		$uid = $user['uid'];
		$from = 'kf'; //客服系统专属，验证时通行
		$keyStr = "$password\t$uid\t$ip\t$from";

		$encodestr =  authcode($keyStr,'ENCODE');
		return urlencode($encodestr);
	}
	/**
	*生成包含用户信息的key，目前主要
	*/
	private function getKey($cwid) {
		$crid = $this->model('courseware')->getClassroomId($cwid);
		if (Ebh::app()->lib('Access')->checkClassroomAccess($crid))
		{
			$user = $this->model('ebhuser')->getKeyUser($crid);//获得用改学校权限的教师信息
			$uid = $user['uid'];
			$pwd = $user['password'];
			$ip = $this->input->getip();
			$time = SYSTIME;
			$skey = "$pwd\t$uid\t$ip\t$time";
			$auth = authcode($skey, 'ENCODE');
			return $auth;
		}

	}
}