<?php
/**
 * 数据审核控制器
 */
class DataController extends CAdminControl{
	public function __construct()
	{
		parent::__construct();
		//检查权限
		Ebh::app()->lib('Access')->checkModuleAccess('admindata');//检测权限
	}


	/**
	 * 课件审核
	 * 
	 */
	public function coursewares(){
		$request = $this->input->get();
		$CModel = $this->model('courseware');
		$page = Ebh::app()->getUri()->page;//当前页
		$request['page']=$page;
		$pagesize = 50;
		$astatus = intval($request['admin_status']);
		$crid = intval($request['crid']);
		if($request['cat']!=''){
			$cat = intval($request['cat']);
		}else{
			$request['cat']='';
			$cat = -1;
		}

		$param= array(
			'role'=>'admin',
			'pagesize'=>$pagesize,
			'limit'=>(max(0,($page-1)*$pagesize)).", {$pagesize}",
			'cat'=>$cat,
			'admin_status'=>$astatus,
			'crid'=>$crid,
			'q'=>$request['q'],
			'type'=>1,
			'orderby'=>'c.dateline DESC'
			);
		$access=Ebh::app()->lib('Access')->getAccessCookie('classroom_access');//检测权限

		if($access==""){//网校权限为空
			$this->assign('coursewares',array());
			$this->display('data/coursewares');
			return;
		}else if($access!="ALL"){
			$param['access']=$access;
		}
		// var_dump($param);
		$count = $CModel->getcoursewarecount($param);
		$coursewares = $CModel->getcoursewarelist($param);//生成课件和附件所在服务器地址
		$serverutil = Ebh::app()->lib('ServerUtil');	
		$source = $serverutil->getCourseSource();
		$authkey = $this->_getauthkey();
		foreach($coursewares as &$ware){
			if(!empty($source)){
				$ware['cwsource'] = $source;
			}
			$ware['k'] = $authkey;
            $checkname = $this->model('User')->getOneByUid($ware['admin_uid']);
            $ware['checkname'] = $checkname['username'];
		}
		$pagestr = show_page($count, $pagesize);
		// var_dump($request);
		$this->assign('pagestr', $pagestr);
		$this->assign('cat',$cat);
		// echo '<pre>';
		// var_dump($coursewares);die;
		$this->assign("coursewares", $coursewares);
		$request['pagesize'] =$pagesize; 
		$this->assign("request",$request);
		$this->display('data/coursewares');
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
		$keyStr = "$password\t$uid\t$ip";
		//echo authcode($keyStr,'CODE');
		$encodestr =  authcode($keyStr,'ENCODE');	
		
		return urlencode($encodestr);
		
	}
	/**
	 * 生成云盘加密字符串
	 * @return string
	 */
	protected function _getpankey($fileid){
		$appid = 'kfsystem';
		$ip= $this->input->getip();
		$time = SYSTIME;
		$keyStr = "$appid\t$fileid\t$ip\t$time";
		$encodestr =  authcode($keyStr,'ENCODE');

		return urlencode($encodestr);
	}
		/**
	 * 获取域名学校列表
	 */
		public function getdomainlist()
		{
			$htmlstr = '';
		//分页
			$param['page'] = $this->input->post('page');
			$param['page'] = (empty($param['page']) || intval($param['page']) <= 0)? 1 : intval($param['page']);
			$param['q'] = $this->input->post('keyword');

			$access=Ebh::app()->lib('Access')->getAccessCookie('classroom_access');
			if($access==""){//网校权限为空
				$this->assign('list', array());
				echo '<tr><td colspan="4"><font color="red">您的权限不足，请联系管理员！</font></td></tr>';
				return;
			}else if($access!="ALL"){
				$param['access']=$access;
			}

			$param['page_size'] = 10;
			$param['record_count'] = $this->model('domain')->getdomaincount($param);
			$param = page_and_size($param);
            //var_dump($param['record_count']);die;
			$access=Ebh::app()->lib('Access')->getAccessCookie('classroom_access');
			if($access==""){//网校权限为空
				$this->assign('list', array());
				echo '<tr><td colspan="4"><font color="red">您的权限不足，请联系管理员！</font></td></tr>';
				return;
			}else if($access!="ALL"){
				$param['access']=$access;
			}

		if (!empty($param['record_count']))
		{
		    //print_r($param);die;
			$classroomlist = $this->model('domain')->getDomainList($param);

			$pagestr = show_page_ajax($param['record_count'], $param['page_size']);
			
			foreach($classroomlist as $value)
			{
				$htmlstr .= '<tr style="cursor:pointer" onclick="checkCrItem(\'' . $value['crid'] . '\', \'' . $value['crname'] . '\')"><td><input type="radio" name="crid" id="crid_' . $value['crid'] . '" value="' . $value['crid'] . '" onclick="checkCrItem(\'' . $value['crid'] . '\', \'' . $value['crname'] . '\')" /></td><td>' . $value['fulldomain'] . '</td><td>' . $value['crname'] . '</td><td>' . date("Y-m-d", $value['domain_time']) . '</td></tr>';
			}
			
			$htmlstr .= '<tr><td colspan="4">'. $pagestr . '</td></tr>';
		}
		else
		{
			$htmlstr = '<tr><td colspan="4"><font color="red">未找到符合条件的学校！</font></td></tr>';
		}

		echo $htmlstr;
	}

		/**
	 * 获取学校列表
	 */
		public function getlist()
		{
			$htmlstr = '';
		//分页
			$param['page'] = $this->input->post('page');
			$param['page'] = (empty($param['page']) || intval($param['page']) <= 0)? 1 : intval($param['page']);
			$param['q'] = $this->input->post('keyword');

			$access=Ebh::app()->lib('Access')->getAccessCookie('classroom_access');
			if($access==""){//网校权限为空
				$this->assign('list', array());
				echo '<tr><td colspan="4"><font color="red">您的权限不足，请联系管理员！</font></td></tr>';
				return;
			}else if($access!="ALL"){
				$param['access']=$access;
			}

			$param['page_size'] = 8;
			$param['record_count'] = $this->model('classroom')->getClassroomCount($param);
			$param = page_and_size($param);

			$access=Ebh::app()->lib('Access')->getAccessCookie('classroom_access');
			if($access==""){//网校权限为空
				$this->assign('list', array());
				echo '<tr><td colspan="4"><font color="red">您的权限不足，请联系管理员！</font></td></tr>';
				return;
			}else if($access!="ALL"){
				$param['access']=$access;
			}

		if (!empty($param['record_count']))
		{
			$classroomlist = $this->model('classroom')->getClassroomList($param);
			
			$pagestr = show_page_ajax($param['record_count'], $param['page_size']);
			
			foreach($classroomlist as $value)
			{
				$htmlstr .= '<tr style="cursor:pointer" onclick="checkCrItem(\'' . $value['crid'] . '\', \'' . $value['crname'] . '\')"><td><input type="radio" name="crid" id="crid_' . $value['crid'] . '" value="' . $value['crid'] . '" onclick="checkCrItem(\'' . $value['crid'] . '\', \'' . $value['crname'] . '\')" /></td><td>' . $value['domain'] . '</td><td>' . $value['crname'] . '</td><td>' . date("Y-m-d", $value['dateline']) . '</td></tr>';
			}
			
			$htmlstr .= '<tr><td colspan="4">'. $pagestr . '</td></tr>';
		}
		else
		{
			$htmlstr = '<tr><td colspan="4"><font color="red">未找到符合条件的学校！</font></td></tr>';
		}

		echo $htmlstr;
	}
	/**
	 * 课件查看
	 *
	 */
	public function view(){
		$request = $this->input->get();
		$cwid = intval($request['cwid']);
		$attid= intval($request['attid']);
		$logid = intval($request['logid']);
		$qid = intval($request['qid']);
		$newexam = intval($this->input->get('newexam'));
		$aid = intval($request['aid']);
		$eid = intval($request['eid']);//作业
		$reviewid = intval($request['reviewid']);
		$fileid = intval($request['fileid']);
		$crid=intval($request['crid']);

		if($cwid>0){//课件查看
			$CModel = $this->model('courseware');
			$ware= $CModel->getcoursedetail($cwid);
            $checkname = $this->model('user')->getOneByUid($ware['admin_uid']);
            $ware['checkname'] = $checkname['username'];
            $live = Ebh::app()->getConfig()->load('live');
            $ware['teacher_board_rtmp'] = str_replace('[liveid]', $ware['liveid'], $live['teacher_board_rtmp']);
            $ware['teacher_camera_rtmp'] = str_replace('[liveid]', $ware['liveid'], $live['teacher_camera_rtmp']);
            $ware['teacher_board_http'] = str_replace('[liveid]', $ware['liveid'], $live['teacher_board_http']);
			$this->assign("info",$ware);
		}elseif($attid>0){//附件查看
			$AModel = $this->model('attachment');
			$attach = $AModel->getAttachById($attid);
            $checkname = $this->model('user')->getOneByUid($attach['admin_uid']);
            $attach['checkname'] = $checkname['username'];
			$this->assign("info",$attach);
		}elseif($logid>0){//评论查看
			$RModel = $this->model('review');
			$review = $RModel->getReviewById($logid);
            $review = $this->model('billchecks')->str_change($review);//敏感字处理
            $checkname = $this->model('user')->getOneByUid($review['admin_uid']);
            $review['checkname'] = $checkname['username'];
			$review['subject'] = $this->_exchangeimg($review['subject']);
			$this->assign("info",$review);
		}elseif($qid>0){//答疑查看
			$QModel = $this->model('askquestion');
			$question = $QModel->getaskbyqid($qid);
            $question = $this->model('billchecks')->str_change($question);//敏感字处理
            $checkname = $this->model('user')->getOneByUid($question['admin_uid']);
            $question['checkname'] = $checkname['username'];
			//var_dump($question);
			$this->assign("info",$question);
		}elseif($aid>0){//回答查看
			$QModel = $this->model('askquestion');
			$answer = $QModel->getanswerbyaid($aid);
            $answer = $this->model('billchecks')->str_change($answer);//敏感字处理
            $checkname = $this->model('user')->getOneByUid($answer['admin_uid']);
            $answer['checkname'] = $checkname['username'];
			//var_dump($answer);
			$this->assign("info",$answer);
		}elseif($reviewid>0){//主站评论
			$PModel = $this->model('Previews');
			$UModel = $this->model('User');
			$preview = $PModel->getpreviewbyrid($reviewid);
			$user = $UModel->getuserbyuid($preview['uid']);
			$preview['username'] = $user['username'];
			$preview['realname'] = $user['realname'];
			//var_dump($answer);
			$this->assign("info",$preview);
		}elseif($eid>0){
			if ($newexam) {//新作业
				$HModel=$this->model('homeworkv2');
			} else {
				$HModel=$this->model('homework');
			}
			$info=$HModel->getHomeworkById($eid);
            $checkname = $this->model('user')->getOneByUid($info['admin_uid']);
            $info['checkname'] = $checkname['username'];
			$this->assign('info',$info);
		}elseif($fileid>0){
			$filemodel=$this->model('file');
			$info=$filemodel->getFileById($fileid);
			$user = $this->model('ebhuser')->getuserbyuid($info['uid']);
			$classroom = $this->model('classroom')->getRoomByCrid($info['crid']);
            $checkname = $this->model('user')->getOneByUid($info['admin_uid']);
            $info['checkname'] = $checkname['username'];
			$info['username'] = $user['username'];
			$info['realname'] = $user['realname'];
			$info['crname'] = $classroom['crname'];
			$this->assign('info',$info);
		}elseif ($crid>0) {//域名详情
			//echo 111;die;
			$DModel = $this->model('domain');
			$domain=$DModel->getdomainbycrid($crid);
			 $checkname= $this->model('user')->getOneByUid($domain['admin_uid']);
			 $domain['checkname']=$checkname['username'];
			 //获取网校默认域名
			 $roominfo = $this->model("Classroom")->getRoomByCrid($crid);
			 $domain['domian'] = !empty($roominfo) ? $roominfo['domain'] :  "";
			
			$this->assign('info',$domain);
		}
		else{
			exit;
		}
		$this->assign("request", $request);
		$this->display('data/view');
	}
	/**
	 * 审核处理
	 */
	public function checkprocess(){
		$request = $this->input->post();
    	$stat = intval($request['admin_status']);
		$type= intval($request['type']);
        if($type==13 && (empty($request['icp']))&& $stat==1){
            echo json_encode(array('code'=>1,'msg'=>'请填写备案信息',));
            exit;
        }
		if($type<=0){
			exit(0);
		}
		if($type == 11){
			$ckmodel = $this->model('panbillchecks');
		}
		else {
			$ckmodel = $this->model('billchecks');
		}
        	$user = EBH::app()->user->getloginuser();

		$param = array(
			'role'=>'admin',
			'admin_uid'=>$user['uid'],
			'admin_status'=>$stat,
			'admin_remark'=>$request['admin_remark'],
			'toid'=>	$request['toid'],
			'admin_ip'=>getip(),
			'type'=>$type,
			'icp'=>$request['icp']
			);

		$ret = $ckmodel->check($param);
        if($ret != false){
            if($type==13){
                $param['icp']=$request['icp'];
                $param['crid']=$request['toid'];
                $res=$ckmodel->inserticp($param);

            }
        }
		if(!empty($ret)){
			$temp=$this->getDataInfo($param['toid'],$type);
			$name=$temp['name'];
			$school=$temp['school'];
			$alltype=array(1=>'课件',2=>'附件',3=>'评论',4=>'答疑',5=>'回答',6=>'主站评论',7=>'作业',11=>'云盘',13=>'域名',15=>'兑换码');
			$allresult=array('','通过','不通过');
            if(!empty($stat) && intval($stat) == 2){
                echo json_encode(array('code'=>2,'msg'=>'处理成功','checker'=>$user['username']));
            }else{
                echo json_encode(array('code'=>0,'msg'=>'处理成功','checker'=>$user['username']));
            }

            if(intval($stat) == 1 && $type == 13){
                //当域名审核通过时把域名和备案信息写进classroom表

                $crid=$request['toid'];
                //var_dump($crid);die;
                $result=$this->model('domain')->getdomaininfo($crid);
                if(empty($result)){
                    return  false;
                }else{
                    $dparam=array(
                        'crid'=>$crid,
                        'icp'=>$result['icp'],
                        'fulldomain'=>$result['fulldomain']
                    );
                    $editresult=$this->model('domain')->editclassromm($dparam);

                }
            }
			$remark=' 审核结果为'.$allresult[$stat];
			if($school){
				$remark.=' 所属学校为'.$school;
			}

			admin_log('数据审核',$alltype[$type].'审核',$name,$param['toid'],$remark);//添加日志

			if ($stat == 2 && $type == 4)
			{
				//同步SNS数据(当删除问题时，问题数减1)
				Ebh::app()->lib('Sns')->do_sync($temp['uid'], -1);
			}
			elseif ($stat == 2 && $type == 7)
			{
				//同步SNS数据(当删除问题时，教师作业数减1)
				Ebh::app()->lib('Sns')->do_sync($temp['uid'], -3);
			}

		}else{
			echo json_encode(array('code'=>1,'msg'=>'处理失败','checker'=>$user['username']));
		}

	}
    /**
     * 查看附件上传的图片
     */
    public function viewImage(){
        if($request = $this->input->get()) {
            $attid = isset($request['attid']) ? intval(h($request['attid'])) : '';
            $downurl = '/data/getImage.html?attid='.$attid;
            $this->assign('downurl', $downurl);
            $this->display('data/viewimage');
        }
    }
    /**
     * 获取附件上传图片
     */
    public function getImage(){
        if($request = $this->input->get()){
            $attid= isset($request['attid'])?intval($request['attid']):'';
            if (!empty($attid)){
                $attachmodel = $this->model('Attachment');
                $attach = $attachmodel->getAttachById($attid);
            }
            if(!empty($attach)) {
                $url = isset($attach['url'])?$attach['url']:'';
                $name = isset($attach['filename'])?$attach['filename']:'';
                $suffix = isset($attach['filesuffix'])?$attach['filesuffix']:'';
                getfile('attachment', $url, $name.$suffix);
            }
        }
    }
	/**
	 *新版作业审核
	 */
	public function checkexam(){
		$request = $this->input->post();
    	$stat = intval($request['admin_status']);
		$ckmodel = $this->model('homeworkv2');
        $user = EBH::app()->user->getloginuser();
		$param = array(
			'role'=>'admin',
			'admin_uid'=>$user['uid'],
			'admin_status'=>$stat,
			'admin_remark'=>$request['admin_remark'],
			'toid'=>	$request['toid'],
			'admin_ip'=>getip(),
			'icp'=>$request['icp'],
			'crid'=>$request['crid'],
			'subject'=>$request['subject'],
			'uid'=>$request['uid']
		);

		$ret = $ckmodel->checkexam($param);
		if (is_array($ret)) {//只能向上处理
			echo json_encode(array('code'=>1,'msg'=>'只能处理，最新审核以后的数据，不能跳着审核','checker'=>$user['username']));
			exit();
		}
		if(!empty($ret)){
			$temp=$this->getDataInfo($param['toid'],14);
			$name=$temp['name'];
			$school=$temp['school'];
			$allresult=array('','通过','不通过');
            if(!empty($stat) && intval($stat) == 2){
                echo json_encode(array('code'=>2,'msg'=>'处理成功','checker'=>$user['username']));
            }else{
                echo json_encode(array('code'=>0,'msg'=>'处理成功','checker'=>$user['username']));
            }

			$remark=' 审核结果为'.$allresult[$stat];
			if($school){
				$remark.=' 所属学校为'.$school;
			}

			admin_log('数据审核','新作业审核',$name,$param['toid'],$remark);//添加日志

			if ($stat == 2) {
				//同步SNS数据(当删除问题时，教师作业数减1)
				//Ebh::app()->lib('Sns')->do_sync($temp['uid'], -3);
			}
		}else{
			echo json_encode(array('code'=>1,'msg'=>'处理失败','checker'=>$user['username']));
		}

	}
	/**
	 * 新版本作业批量审核
	 */
	public function multcheckexam(){
		$request = $this->input->post();
		//$user = Ebh::app()->user->getloginuser();
		$stat = intval($request['admin_status']);
		$ckmodel = $this->model('homeworkv2');
        $user = EBH::app()->user->getloginuser();
		$param = array(
				'role'=> 'admin',
				'admin_uid'=>$user['uid'],
				'admin_status'=>$stat,
				'admin_remark'=>h($request['admin_remark']),
				'ids'=>	h($request['ids']),
				'admin_ip'=>getip(),
		);
		if($ckmodel->multcheckexam($param)){
			$id_array = explode(',', $request['ids']);
			foreach ($id_array as $toid)
			{
				$temp=$this->getDataInfo($toid,14);
				$name=$temp['name'];
				$school=$temp['school'];
				$allresult=array('','通过','不通过');
				$remark=' 审核结果为'.$allresult[$stat];
				if($school){
					$remark.=' 所属学校为'.$school;
				}

				admin_log('数据审核','新作业审核',$name,$toid,$remark);//添加日志

				if ($stat == 2)
				{
					//同步SNS数据(当删除问题时，教师作业数减1)
					Ebh::app()->lib('Sns')->do_sync($temp['uid'], -3);
				}
			}
			echo json_encode(array('code'=>0,'msg'=>'处理成功'));
		}else{
			echo json_encode(array('code'=>1,'msg'=>'处理失败'));
		}
	}

	/**
     * 新作业撤销审核
     */
    public function revokeexam(){
        $request = $this->input->post();
//        var_dump($request);
        $stat = intval($request['status']);
        $user = EBH::app()->user->getloginuser();
        $ckmodel = $this->model('homeworkv2');
        $userinfo = Ebh::app()->user->getloginuser();
        $param = array(
            'admin_status'=>0,
            'admin_uid' => $userinfo['uid'],
            'toid'=>	$request['toid'],
            'status' => $stat,
        );
        //var_dump($param);
        $ret = $ckmodel->revoke($param);
        if($ret>0){
            $temp=$this->getDataInfo($param['toid'],14);
            $name=$temp['name'];
            $school=$temp['school'];
            echo json_encode(array('code'=>0,'msg'=>'处理成功','checker'=>$user['username']));
            $remark='撤销审核';
            if($school){
                $remark.=' 所属学校为'.$school;
            }

            admin_log('数据审核','新作业审核',$name,$param['toid'],$remark);//添加日志

           if ($stat == 2)
            {
                //同步SNS数据(撤销不通过审核，教师作业数加1)
                //Ebh::app()->lib('Sns')->do_sync($temp['uid'], 3);
            }
        }else{
            echo json_encode(array('code'=>1,'msg'=>'处理失败','checker'=>$user['username']));
        }

    }


    /**
     * 撤销审核
     */
    public function revoke(){
        $request = $this->input->post();
//        var_dump($request);
        $stat = intval($request['status']);
        $type= intval($request['type']);
        $user = EBH::app()->user->getloginuser();
        if($type<=0){
            exit(0);
        }
        if($type == 11){
            $ckmodel = $this->model('panbillchecks');
        }
        else {
            $ckmodel = $this->model('billchecks');
        }
        $userinfo = Ebh::app()->user->getloginuser();
        $param = array(
            'admin_status'=>0,
            'admin_uid' => $userinfo['uid'],
            'toid'=>	$request['toid'],
            'type'=>$type,
            'status' => $stat,
        );
        //var_dump($param);
        $ret = $ckmodel->revoke($param);

        if($ret>0){
            $temp=$this->getDataInfo($param['toid'],$type);
            $name=$temp['name'];
            $school=$temp['school'];
            $alltype=array(1=>'课件',2=>'附件',3=>'评论',4=>'答疑',5=>'回答',6=>'主站评论',7=>'作业',11=>'云盘',13=>'域名');
            echo json_encode(array('code'=>0,'msg'=>'处理成功','checker'=>$user['username']));
            $remark='撤销审核';
            if($school){
                $remark.=' 所属学校为'.$school;
            }

            admin_log('数据审核',$alltype[$type].'审核',$name,$param['toid'],$remark);//添加日志

            if ($stat == 2 && $type == 4)
            {
                //同步SNS数据(撤销不通过审核,问题数加1)
                Ebh::app()->lib('Sns')->do_sync($temp['uid'], 1);
            }
            elseif ($stat == 2 && $type == 7)
            {
                //同步SNS数据(撤销不通过审核，教师作业数加1)
                Ebh::app()->lib('Sns')->do_sync($temp['uid'], 3);
            }
        }else{
            echo json_encode(array('code'=>1,'msg'=>'处理失败','checker'=>$user['username']));
        }

    }


	/**
	 * 批量审核处理
	 */
	public function multcheckprocess(){
		$request = $this->input->post();
		//$user = Ebh::app()->user->getloginuser();
		$stat = intval($request['admin_status']);
		$type= intval($request['type']);
		if($type<=0){
			exit(0);
		}
		if($type == 11){
			$ckmodel = $this->model('panbillchecks');
		}
		else {
			$ckmodel = $this->model('billchecks');
		}
        $user = EBH::app()->user->getloginuser();
		$param = array(
				'role'=> 'admin',
				'admin_uid'=>$user['uid'],
				'admin_status'=>$stat,
				'admin_remark'=>h($request['admin_remark']),
				'ids'=>	h($request['ids']),
				'admin_ip'=>getip(),
				'type'=>$type
		);
		if($ckmodel->multcheck($param)){
			$id_array = explode(',', $request['ids']);
			foreach ($id_array as $toid)
			{
				$temp=$this->getDataInfo($toid,$type);
				$name=$temp['name'];
				$school=$temp['school'];
				$alltype=array(1=>'课件',2=>'附件',3=>'评论',4=>'答疑',5=>'回答',6=>'主站评论',7=>'作业',11=>'云盘',15=>'云盘',);
				$allresult=array('','通过','不通过');
				$remark=' 审核结果为'.$allresult[$stat];
				if($school){
					$remark.=' 所属学校为'.$school;
				}

				admin_log('数据审核',$alltype[$type].'审核',$name,$toid,$remark);//添加日志

				if ($stat == 2 && $type == 4)
				{
					//同步SNS数据(当删除问题时，问题数减1)
					Ebh::app()->lib('Sns')->do_sync($temp['uid'], -1);
				}
				elseif ($stat == 2 && $type == 7)
				{
					//同步SNS数据(当删除问题时，教师作业数减1)
					Ebh::app()->lib('Sns')->do_sync($temp['uid'], -3);
				}
			}

			echo json_encode(array('code'=>0,'msg'=>'处理成功'));
		}else{
			echo json_encode(array('code'=>1,'msg'=>'处理失败'));
		}
	}

	private function getDataInfo($id,$type){
		$info=array();
		if($type==1){//课件审核
			$temp=$this->model('courseware')->getcoursedetail($id);
			$info['name']=$temp['title'];
			$info['school']=$this->model('courseware')->getSchoolName($id);
			return $info;
		}elseif($type==2){//附件审核
			$temp=$this->model('attachment')->getAttachById($id);
			$info['name']=$temp['title'];
			$info['school']=$temp['crname'];
			return $info;
		}elseif($type==3){//评论
			$temp=$this->model('review')->getReviewById($id);
			$info['name']=$temp['subject'];
			$info['school']=$this->model('review')->getSchoolName($id);
			return $info;
		}elseif($type==4){//答疑
			$temp=$this->model('askquestion')->getaskbyqid($id);
			$info['name']=$temp['title'];
			$info['school']=$temp['crname'];
			$info['uid']=$temp['uid'];
			return $info;
		}elseif($type==5){//回答
			$temp=$this->model('askquestion')->getanswerbyaid($id);
			$info['name']=strip_tags($temp['amessage']);
			$info['name']=ssubstrch($info['name'],0,40);
			$info['school']=$temp['crname'];
			return $info;
		}elseif($type==6){//主站评论

		}elseif($type==7){//作业审核
			$temp=$this->model('homework')->getHomeworkById($id);
			$info['name']=$temp['title'];
			$info['school']=$temp['crname'];
			$info['uid']=$temp['uid'];
			return $info;
		}elseif($type==11){//云盘审核
			$temp=$this->model('file')->getFileById($id);
			$info['name']=$temp['title'];
			$classroom = $this->model('classroom')->getRoomByCrid($temp['crid']);
			$info['school']=$classroom['crname'];
			$info['uid']=$temp['uid'];
			return $info;
		}elseif($type==14){//新作业
			$temp=$this->model('homeworkv2')->getHomeworkById($id);
			$info['name']=$temp['title'];
			$info['school']=$temp['crname'];
			$info['uid']=$temp['uid'];
			return $info;
		}elseif($type==15){
			$temp=$this->model('redeem')->getRedeemInfo($id);
			$info['name']=$temp['name'];
			$info['school']=$temp['crname'];
			return $info;
		}else{
			return array('name'=>'审核','school'=>'未知');
		}
	}
	/**
	 * 删除处理
	 *
	 */
	public function delprocess(){
		$request = $this->input->post();
		$ckmodel = $this->model('billchecks');
	
		$toid = intval($request['toid']);
		$type = intval($request['type']);
		if($toid<=0||$type<=0){
			exit(0);
		}
		$param = array(
				'toid'=>$toid,
				'type'=>$type
		);
		$ckmodel->del($param);
		//生成备注
		$alltype=array(1=>'课件',2=>'附件',3=>'评论',4=>'答疑',5=>'回答',6=>'主站评论',7=>'作业',11=>'云盘');
		$info=$this->getDataInfo($toid,$type);
		$name=$info['name'];
		$school=$info['school'];
		if($name){
			$remark.=' 名称为'.$name;
		}
		if($school){
			$remark.=' 所属学校为'.$school.' ';
		}

		admin_log('数据审核',$alltype[$type].'删除',$name,$toid,$remark);
		exit(0);
	}

    /**
	 * 附件审核
	 * 
	 */
	public function attachment(){
		$request = $this->input->get();
		$AModel = $this->model('attachment');
		$page = Ebh::app()->getUri()->page;//当前页
		$request['page']=$page;
		$pagesize = 50;
		$astatus = intval($request['admin_status']);

		$crid = intval($request['crid']);
		if($request['cat']!=''){
			$cat = intval($request['cat']);
		}else{
			$cat = -1;
		}

		$param= array(
			'role'=>'admin',
			'pagesize'=>$pagesize,
			'limit'=>(max(0,($page-1)*$pagesize)).", {$pagesize}",
			'cat'=>$cat,
			'admin_status'=>$astatus,
			'crid'=>$crid,
			'q'=>$request['q'],
			'type'=>2
			);
		$access=Ebh::app()->lib('Access')->getAccessCookie('classroom_access');//检测权限
		// $access="10022";
		if($access==""){//网校权限为空
			$this->assign('attachments',array());
			$this->display('data/attachment');
			return;
		}else if($access!="ALL"){
			$param['access']=$access;
		}
		$count = $AModel->getattachmentcount($param);
		$attachments = $AModel->getattachmentlist($param);
		//生成课件和附件所在服务器地址
		$serverutil = Ebh::app()->lib('ServerUtil');
		$source = $serverutil->getCourseSource();
		$authkey = $this->_getauthkey();
		foreach($attachments as &$attach){
			if(!empty($source)){
				$attach['source'] = $source;
			}
			$attach['k'] = $authkey;
		}
		//分页
		$pagestr = show_page($count, $pagesize);
		$this->assign('pagestr', $pagestr);
		$this->assign('cat',$cat);
		$this->assign("attachments", $attachments);
		$request['pagesize'] =$pagesize;
		$this->assign("request",$request);
		$this->display('data/attachment');
	}
	/**
	 * 答疑审核
	 * 
	 */
	public function question(){
		$request = $this->input->get();
		$QModel = $this->model('askquestion');
		$page = Ebh::app()->getUri()->page;//当前页
		$request['page']=$page;
		$pagesize = 50;
		$astatus = intval($request['admin_status']);
		$crid = intval($request['crid']);
		if($request['cat']!=''){
			$cat = intval($request['cat']);
		}else{
			$cat = -1;
		}



		$param= array(
				'role'=>'admin',
				'pagesize'=>$pagesize,
				'limit'=>(max(0,($page-1)*$pagesize)).", {$pagesize}",
				'cat'=>$cat,
		
				'admin_status'=>$astatus,
				'crid'=>$crid,
				'q'=>$request['q'],
				'type'=>4
		);
        
		$access=Ebh::app()->lib('Access')->getAccessCookie('classroom_access');//检测权限
		// $access="10022";
		//var_dump($access);die;
		if($access==""){//网校权限为空
			$this->assign('questions',array());
			$this->display('data/question');
			return;
		}else if($access!="ALL"){
			$param['access']=$access;
		}
		
		$questions = $QModel->getaskquestionlist($param);
		
//        $questions = $this->model('Billchecks')->str_change($questions,true);//特殊字加粗红色显示
		$count = $QModel->getaskquestioncount($param);
        foreach($questions as &$v){
            $checkname = $this->model('User')->getOneByUid($v['admin_uid']);
            $v['checkname'] = $checkname['username'];
            $v['message'] = strip_tags($v['message']);
            $v['message'] = ssubstrch($v['message'],0,40);
            $v['message'] = $this->model('Billchecks')->str_change($v['message']);//特殊字加粗红色显示
            $v['title'] = ssubstrch($v['title'],0,40);
            $v['title'] = $this->model('Billchecks')->str_change($v['title']);//特殊字加粗红色显示
        }
		$pagestr = show_page($count, $pagesize);
		$this->assign('pagestr', $pagestr);	
		$this->assign("questions", $questions);
		$this->assign('cat',$cat);
		$request['pagesize'] =$pagesize;
		$this->assign("request",$request);
		$this->display('data/question');
	}

	/**
	 * 回答审核
	 */
	public function answer(){
		$request = $this->input->get();
		$QModel = $this->model('askquestion');
		$page = Ebh::app()->getUri()->page;//当前页
		$request['page']=$page;
		$pagesize = 50;
		$astatus = intval($request['admin_status']);
		$crid = intval($request['crid']);
		if($request['cat']!=''){
			$cat = intval($request['cat']);
		}else{
			$cat = -1;
		}

 //print_r($request);die;
		$param= array(
				'role'=>'admin',
				'pagesize'=>$pagesize,
				'limit'=>(max(0,($page-1)*$pagesize)).", {$pagesize}",
				'cat'=>$cat,
				'admin_status'=>$astatus,
				'crid'=>$crid,
				'q'=>$request['q'],
				'type'=>5
		);
		$access=Ebh::app()->lib('Access')->getAccessCookie('classroom_access');//检测权限
		if($access==""){//网校权限为空
			$this->assign('answers',array());
			$this->display('data/answer');
			return;
		}else if($access!="ALL"){
			$param['access']=$access;
		}
		$answers = $QModel->getanswerlist($param);
//        $answers = $this->model('Billchecks')->str_change($answers,true);//特殊字加粗红色显示
		$count = $QModel->getanswercount($param);
        foreach($answers as &$v){
            $checkname = $this->model('User')->getOneByUid($v['admin_uid']);
            $v['checkname'] = $checkname['username'];
            $v['message'] = strip_tags($v['message']);
            $v['message'] = ssubstrch($v['message'],0,40);
            $v['message'] = $this->model('Billchecks')->str_change($v['message']);//特殊字加粗红色显示
            $v['title'] = ssubstrch($v['title'],0,40);
            $v['title'] = $this->model('Billchecks')->str_change($v['title']);//特殊字加粗红色显示
        }
		//分页
		$pagestr = show_page($count, $pagesize);
		$this->assign('pagestr', $pagestr);
//        p($answers);die;
		$this->assign('cat',$cat);
		$this->assign("answers", $answers);
		$request['pagesize'] =$pagesize;
		$this->assign("request",$request);
		$this->display('data/answer');
	}

	/**
	 * 回答审核
	 */
	public function redeem(){
		$request = $this->input->get();
		$QModel = $this->model('Redeem');
		$page = Ebh::app()->getUri()->page;//当前页
		$request['page']=$page;
		$pagesize = 50;
		$astatus = intval($request['admin_status']);
		$crid = intval($request['crid']);
		if($request['cat']!=''){
			$cat = intval($request['cat']);
		}else{
			$cat = -1;
		}

		$param = array(
			'role'=>'admin',
			'pagesize'=>$pagesize,
			'limit'=>(max(0,($page-1)*$pagesize)).", {$pagesize}",
			'cat'=>$cat,
			'admin_status'=>$astatus,
			'crid'=>$crid,
			'q'=>$request['q'],
			'type'=>15
		);
		$access=Ebh::app()->lib('Access')->getAccessCookie('classroom_access');//检测权限
		if($access==""){//网校权限为空
			$this->assign('redeems',array());
			$this->display('data/redeem');
			return;
		}else if($access!="ALL"){
			$param['access']=$access;
		}
		$redeems = $QModel->getlist($param);
		if (!empty($redeems)) {
			foreach($redeems as &$ware){
	            $checkname = $this->model('User')->getOneByUid($ware['admin_uid']);
	            $ware['checkname'] = $checkname['username'];
			}
		}
		$count = $QModel->getcount($param);
		//分页
		$pagestr = show_page($count, $pagesize);
		$this->assign('pagestr', $pagestr);
		$this->assign('cat',$cat);
		$this->assign("redeems", $redeems);
		$request['pagesize'] =$pagesize;
		$this->assign("request",$request);
		$this->display('data/redeem');
	}

	/**
	 * 评论审核
	 * 
	 */
	public function review(){
		$request = $this->input->get();
		$RModel = $this->model('review');
		$page = Ebh::app()->getUri()->page;//当前页
		$request['page']=$page;
		$pagesize = 50;
		$astatus = intval($request['admin_status']);
		$crid = intval($request['crid']);
		if($request['cat']!=''){
			$cat = intval($request['cat']);
		}else{
			$cat = -1;
		}

		//var_dump($param);
		$param= array(
				'role'=>'admin',
				'pagesize'=>$pagesize,
				'limit'=>(max(0,($page-1)*$pagesize)).", {$pagesize}",
				'cat'=>$cat,
				'admin_status'=>$astatus,
				'crid'=>$crid,
				'q'=>$request['q'],
				'type'=>3
		);
		$access=Ebh::app()->lib('Access')->getAccessCookie('classroom_access');//检测权限
		// $access="10022";
		if($access==""){//网校权限为空
			$this->assign('reviews',array());
			$this->display('data/review');
			return;
		}else if($access!="ALL"){
			$param['access']=$access;
		}
		
		$count = $RModel->getreviewcount($param);
		$reviews = $RModel->getreviewlist($param);
//        $reviews = $this->model('Billchecks')->str_change($reviews,true);//特殊字加粗红色显示
        foreach($reviews as &$v){
            $checkname = $this->model('User')->getOneByUid($v['admin_uid']);
            $v['checkname'] = $checkname['username'];
            $v['subject'] = strip_tags($v['subject']);
            $v['subject'] = ssubstrch($v['subject'],0,40);
            $v['subject'] = $this->model('Billchecks')->str_change($v['subject']);//特殊字加粗红色显示
        }
		//var_dump($reviews);
		//var_dump($param);
		//分页
		$pagestr = show_page($count, $pagesize);
		$this->assign('pagestr', $pagestr);	
		$this->assign("reviews", $reviews);
		$this->assign('cat',$cat);
//        p($reviews);die;
		$request['pagesize'] =$pagesize;
		$this->assign("request",$request);
		$this->display('data/review');
	}
	/**
	 * 评论内容动态图片替换
	 */
	protected function _exchangeimg($subject){
		$emotionarr = array('微笑','害羞','调皮','偷笑','送花','大笑','跳舞','飞吻','安慰','抱抱','加油','胜利','强','亲亲','花痴','露齿笑','查找','呼叫','算账','财迷','好主意','鬼脸','天使','再见','流口水','享受');
		$matstr = '/\[emo(\S{1,2})\]/is';
		$emotioncount = count($emotionarr);
		preg_match_all($matstr,$subject,$mat);
		if(!empty($mat[0])){
			foreach($mat[0] as $l=>$m){
				$imgnumber = intval($mat[1][$l]);
				if($imgnumber<$emotioncount){
					$subject=str_replace($m,'<img src="http://static.ebanhui.com/ebh/tpl/default/images/'.$imgnumber.'.gif">',$subject);
				}			
			}
		}

		return $subject;
	}
	/**
	 * 作业审核
	 * 
	 */
	public function homework(){
		$request = $this->input->get();
        //print_r($request); die;
		$HModel = $this->model('homework');
		$page = Ebh::app()->getUri()->page;//当前页
		$request['page']=$page;
		$pagesize = 50;
		$astatus = intval($request['admin_status']);
		$crid = intval($request['crid']);
		if($request['cat']!=''){
			$cat = intval($request['cat']);
		}else{
			$cat = -1;
		}

		//var_dump($param);
		$param= array(
				'role'=>'admin',
				'pagesize'=>$pagesize,
				'limit'=>(max(0,($page-1)*$pagesize)).", {$pagesize}",
				'cat'=>$cat,
				'admin_status'=>$astatus,
				'crid'=>$crid,
				'q'=>$request['q'],
				'type'=>7
		);
		$access=Ebh::app()->lib('Access')->getAccessCookie('classroom_access');//检测权限
		// $access="10022";
		if($access==""){//网校权限为空
			$this->assign('homeworks',array());
			$this->display('data/homework');
			return;
		}else if($access!="ALL"){
			$param['access']=$access;
		}
		
		$count = $HModel->getHomeworkCount($param);
		$homeworks = $HModel->getHomeworkList($param);
        foreach($homeworks as $k=>$v){
            $checkname = $this->model('User')->getOneByUid($v['admin_uid']);
            $homeworks[$k]['checkname'] = $checkname['username'];
        }
		// var_dump($homeworks);
		//var_dump($reviews);
		//var_dump($param);
		//var_dump($request);
		//分页
		$pagestr = show_page($count, $pagesize);
		$this->assign('pagestr', $pagestr);	
		$this->assign("homeworks", $homeworks);
		$this->assign('cat',$cat);		
		$request['pagesize'] =$pagesize;
		$this->assign("request",$request);
		$this->display('data/homework');
	}

	/**
	 * 作业2.0审核
	 * 
	 */
	public function homeworkv2(){
		$request = $this->input->get();
		$HModel = $this->model('homeworkv2');
		$page = Ebh::app()->getUri()->page;//当前页
		$request['page']=$page;
		$pagesize = 50;
		$astatus = intval($request['admin_status']);
		$crid = intval($request['crid']);
		if($request['cat']!=''){
			$cat = intval($request['cat']);
		}else{
			$cat = -1;
		}

		//var_dump($param);
		$param= array(
				'role'=>'admin',
				'pagesize'=>$pagesize,
				'limit'=>(max(0,($page-1)*$pagesize)).", {$pagesize}",
				'cat'=>$cat,
				'admin_status'=>$astatus,
				'crid'=>$crid,
				'q'=>$request['q'],
				'type'=>14
		);
		$access=Ebh::app()->lib('Access')->getAccessCookie('classroom_access');//检测权限
		// $access="10022";
		if($access==""){//网校权限为空
			$this->assign('homeworks',array());
			$this->display('data/homeworkv2');
			return;
		}else if($access!="ALL"){
			$param['access']=$access;
		}
		$examInfo = $HModel->getHomeworkList($param);
		$homeworks = $examInfo['examList'];
		$count = $examInfo['count'];
		if ($homeworks) {
			 foreach($homeworks as $k=>$v){
	            $checkname = $this->model('User')->getOneByUid($v['admin_uid']);
	            $homeworks[$k]['checkname'] = $checkname['username'];
	        }
		} else {
			$homeworks = array();
		}
       
		//分页
		$pagestr = show_page($count, $pagesize);
		$this->assign('pagestr', $pagestr);	
		$this->assign("homeworks", $homeworks);
		$this->assign('cat',$cat);		
		$request['pagesize'] =$pagesize;
		$this->assign("request",$request);
		$this->display('data/homeworkv2');
	}

			/**
	 * 获取教师列表
	 */
		public function getTeacherList()
		{
			$htmlstr = '';
		//分页
			$param['page'] = $this->input->post('page');
			$param['page'] = (empty($param['page']) || intval($param['page']) <= 0)? 1 : intval($param['page']);
			$param['q'] = $this->input->post('keyword');
			$param['page_size'] = 8;
			$param['record_count'] = $this->model('roomteacher')->getroomteachercount($param);
			$param = page_and_size($param);
			$access=Ebh::app()->lib('Access')->getAccessCookie('classroom_access');
		// $access="10022";
		if($access==""){//网校权限为空
			$this->assign('list', array());
			echo '<tr><td colspan="4"><font color="red">您的权限不足，请联系管理员！</font></td></tr>';
			return;
		}else if($access!="ALL"){
			$param['access']=$access;
		}		
		
		if (!empty($param['record_count']))
		{
			$list = $this->model('roomteacher')->getroomteacherlist($param);
			$pagestr = show_page_ajax($param['record_count'], $param['page_size']);
			
			foreach($list as $value)
			{
				$htmlstr .= '<tr style="cursor:pointer" onclick="checkCrItem(\'' . $value['uid'] . '\', \'' . $value['username'] . '\')"><td><input type="radio" name="crid" id="crid_' . $value['uid'] . '" value="' . $value['uid'] . '" onclick="checkCrItem(\'' . $value['uid'] . '\', \'' . $value['username'] . '\')" /></td><td>' . $value['username'] . '</td><td>' . $value['realname'] . '</td><td>' .$value['mobile'] . '</td></tr>';
			}
			
			$htmlstr .= '<tr><td colspan="4">'. $pagestr . '</td></tr>';
		}
		else
		{
			$htmlstr = '<tr><td colspan="4"><font color="red">未找到符合条件的学校！</font></td></tr>';
		}

		echo $htmlstr;
	}

	/**
	 * 云盘审核
	 */
	public function panfile(){
		$request = $this->input->get();
		$filemodel = $this->model('file');
		$page = Ebh::app()->getUri()->page;//当前页
		$request['page']=$page;
		$pagesize = 50;
		$astatus = intval($request['admin_status']);
		$crid = intval($request['crid']);
		if($request['cat']!=''){
			$cat = intval($request['cat']);
		}else{
			$request['cat']='';
			$cat = -1;
		}

		$param= array(
			'role'=>'admin',
			'pagesize'=>$pagesize,
			'limit'=>(max(0,($page-1)*$pagesize)).", {$pagesize}",
			'cat'=>$cat,
			'admin_status'=>$astatus,
			'crid'=>$crid,
			'q'=>$request['q'],
			'type'=>11//11为云盘审核
			);
		$access=Ebh::app()->lib('Access')->getAccessCookie('classroom_access');//检测权限
		if($access==""){//网校权限为空
			$this->assign('panfiles',array());
			$this->display('data/panfile');
			return;
		}else if($access!="ALL"){
			$param['access']=$access;
		}

		$count = $filemodel->getFileCount($param);
		$panfiles = $filemodel->getFileList($param);
		if(!empty($panfiles)){
		    $uid_array = array();
		    $users = array();
		    $crid_array = array();
		    $classrooms = array();
		    foreach($panfiles as $value)
		    {
		        $uid_array[] = $value['uid'];
		        $crid_array[] = $value['crid'];
		    }
		    $users = $this->model('Ebhuser')->getuserarray($uid_array);
		    $classrooms = $this->model('Classroom')->getClassRoomArray($crid_array);
		    foreach($panfiles as $key => $value)
		    {
		        $panfiles[$key]['username'] = $users[$value['uid']]['username'];
		        $panfiles[$key]['realname'] = $users[$value['uid']]['realname'];
		        $panfiles[$key]['crname'] = $classrooms[$value['crid']];
		        $panfiles[$key]['size'] = $this->_format_bytes($value['size']);
		        $panfiles[$key]['k'] = $this->_getpankey($value['fileid']);
		        $checkname = $this->model('User')->getOneByUid($value['admin_uid']);
		        $panfiles[$key]['checkname'] = $checkname['username'];
		    }  
		}else{
		    $panfiles = array();
		}
		//分页
		$pagestr = show_page($count, $pagesize);
		$this->assign('pagestr', $pagestr);		
		$this->assign("panfiles", $panfiles);
		$this->assign('cat',$cat);
		$request['pagesize'] =$pagesize; 
		$this->assign("request",$request);
		$this->display('data/panfile');
	}

	//计算文件大小，转换成B,KB,MB,GB,TB格式
	function _format_bytes($size) {
		if ($size == 0) return 0;
		$units = array('B', 'KB', 'MB', 'GB', 'TB');
		for ($i = 0; $size >= 1024 && $i < 4; $i++) $size /= 1024;
		return round($size, 2) . $units[$i];
	}

     /**
     * 域名审核
     *
     */
    public  function  domain(){
        $request = $this->input->get();
        $DModel = $this->model('domain');
        $page = Ebh::app()->getUri()->page;//当前页
        $request['page']=$page;
        $pagesize = 20;
        $astatus = intval($request['admin_status']);
        $crid = intval($request['crid']);
        if($request['cat']!=''){
            $cat = intval($request['cat']);
        }else{
            $cat = -1;
        }
        $param= array(
            'role'=>'admin',
            'pagesize'=>$pagesize,
            'limit'=>(max(0,($page-1)*$pagesize)).", {$pagesize}",
            'cat'=>$cat,
            'admin_status'=>$astatus,
            'crid'=>$crid,
            'q'=>$request['q'],
            'type'=>13//域名审核
        );
        $access=Ebh::app()->lib('Access')->getAccessCookie('classroom_access');//检测权限

        if($access==""){//网校权限为空
            $this->assign('domain',array());
            $this->display('data/domain');
            return;
        }else if($access!="ALL"){
            $param['access']=$access;
        }

        $domainList = $DModel->getDomainList($param);
        $count = $DModel->getdomaincount($param);
        // print_r($count);
        // die;
        if(!empty($domainList)){
            foreach($domainList as &$v){
                if(!empty($v['admin_uid'])){
                    $uid_array[]=$v['admin_uid'];
                }
            }
            $checkname = $this->model('User')->getUsernameByUidarr($uid_array);
            foreach ($domainList as &$v){
                if(!empty($checkname)){
                    foreach ($checkname as $val){
                        if($v['admin_uid']==$val['uid']){
                            $v['checkname']=$val['username'];
                        }
                    }
                }
            }

        }
     //print_r($domainList);die;
        //分页
        $pagestr = show_page($count, $pagesize);
        $this->assign('pagestr', $pagestr);
        $request['pagesize'] =$pagesize;
        $this->assign('domainList',$domainList);
        $this->assign('request',$request);
        $this->display('data/domain');

    }

    /**
     * 身份申请
     */
    public function identity() {
        $access = Ebh::app()->lib('Access')->getAccessCookie('classroom_access');//检测权限
        $param = array();
        if($access == '') {//网校权限为空
            echo '您没有操作权限，请联系管理员！';
            //show_message("您没有操作权限，请联系管理员！");
            return;
        }else if($access != 'ALL') {
            $param['access'] = $access;
            $access = explode(',', $access);
        }
        $request = $this->input->get();
        $page = Ebh::app()->getUri()->page;//当前页
        $request['page'] = max(1, $page);
        $pagesize = $request['pagesize'] = 20;
        $crid = intval($request['crid']);
        if ($crid > 0 && ($access == 'ALL' || in_array($crid, $access))) {
            $param['crid'] = $crid;
        }
        if ($this->input->get('status') === null) {
            $request['status'] = -1;
        }
        if (in_array($request['status'], array(0, 1, 2))) {
            $param['status'] = $request['status'];
        }
        if ($request['start'] != '') {
            $param['start'] = strtotime($request['start']);
        }
        if ($request['end'] != '') {
            $param['end'] = strtotime($request['end']);
        }
        $param['page'] = $request['page'];
        $param['pagesize'] = $pagesize;
        $apiServer = Ebh::app()->getApiServer('ebh');
        $ret = $apiServer->reSetting()
            ->setService('Kf.Identity.index')
            ->addParams($param)
            ->request();
        $count = $ret['count'];
        $list = $ret['list'];
        //分页
        $pagestr = show_page($count, $pagesize);
        $this->assign('pagestr', $pagestr);
        $this->assign('list', $list);
        $this->assign('request', $request);
        $this->display('data/identity');
    }

    public function jsauth() {
        $aid = intval($this->input->get('aid'));
        $apiServer = Ebh::app()->getApiServer('ebh');
        $jsauth = null;
        if ($aid > 0) {
            $jsauth = $apiServer->reSetting()
                ->setService('Kf.Identity.info')
                ->addParams('aid', $aid)
                ->request();
        }
        if (empty($jsauth)) {
            $jsauth = array(

            );
        }
        $this->assign('jsauth', $jsauth);
        $this->display('data/jsauth');
    }

    /**
     * 身份申请审核
     */
    public function ajax_identity_audit() {
        @ob_end_clean();
        header('Content-Type: application/json; charset=utf-8');
        $status = intval($this->input->post('status'));
        $aids = $this->input->post('aids');
        $aids = array_filter($aids, function($id) {
            return is_numeric($id) && $id > 0;
        });

        if ($status != 1 && $status != 2 || empty($aids)) {
            echo json_encode(array(
                'errno' => 1,
                'msg' => '参数审核失败'
            ));
            exit();
        }

        $aids = array_map('intval', $aids);
        $user = Ebh::app()->user->getloginuser();
        $logModel = $this->model('Log');
        $apiServer = Ebh::app()->getApiServer('ebh');
        $pagesize = count($aids);
        $jsauths = $apiServer->reSetting()
            ->setService('Kf.Identity.index')
            ->addParams(array(
                'aids' => $aids,
                'pagesize' => $pagesize,
                'pagesize' => 1
            ))
            ->request();
        if ($jsauths['count'] == 0) {
            echo json_encode(array(
                'errno' => 0,
                'msg' => '未做审核'
            ));
            exit();
        }
        $showName = $user['username'];
        if ($user['realname'] != '') {
            $showName = $showName.'['.$user['realname'].']';
        }
        foreach ($jsauths['list'] as $jsauth) {
            if (!empty($jsauth['status'])) {
                continue;
            }
            $ret = $apiServer->reSetting()
                ->setService('Kf.Identity.audit')
                ->addParams(array(
                    'aid' => $jsauth['aid'],
                    'uid' => $user['uid'],
                    'status' => $status,
                    'remark' => trim(strip_tags($this->input->post('remark'))),
                    'ip' => $this->input->getip()
                ))
                ->request();
            if (!$ret) {
                continue;
            }
            $jsauthName = $jsauth['username'];
            if ($jsauth['realname'] != '') {
                $jsauthName .= '['.$jsauth['realname'].']';
            }
            $logModel->writeLog(array(
                'uid' => $user['uid'],
                'username' => $user['username'],
                'realname' => $user['realname'],
                'module' => '数据审核',
                'operation' => '身份审核',
                'objectname' => '用户',
                'objectid' => $jsauth['aid'],
                'info' => $showName.($status ? '审核通过' : '审核通不过').'网校'.$jsauth['crname'].'['.$jsauth['crid'].']'.'用户'.$jsauthName,
                'ip' => $this->input->getip(),
                'dateline' => SYSTIME
            ));
        }
        echo json_encode(array(
            'errno' => 0,
            'msg' => '审核成功'
        ));
        exit();
    }
}
