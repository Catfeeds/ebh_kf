<?php
/**
 * 网校管理控制器
 */
class SchoolController extends CAdminControl{
	public function __construct()
	{
		parent::__construct();
		//检查权限
		Ebh::app()->lib('Access')->checkModuleAccess('adminschool');
	}
	/**
	 * 网校管理首页（列表）
	 * @return [type] [description]
	 */
	public function index(){
		$access=Ebh::app()->lib('Access')->getAccessCookie('classroom_access');
		$classroomstr = $this->formatToClassroomStr($access);
		$this->assign('classroomstr',$classroomstr);

		if($access==""){//网校权限为空
			$this->assign('classroomList', array());
			$this->display('school/school_list');
			return;
		}else if($access!="ALL"){
			$param['access']=$access;
		}

		$param['q'] = $this->input->get('q');
		$catid = $this->input->get('catid');
		$catname = '所有分类';
		$hastv = $this->input->get('hastv');
		$ctype = $this->input->get('ctype');


		$categorylist = $this->model('category')->getCategoriesByParam(array('position'=>1));
		if (!empty($catid))
		{
			$param['catids'] = $this->getCatids($categorylist, $catid);
			$catname = $this->model('category')->getCatName($catid);
		}
		if(is_array($categorylist)){
			$categorylist = getTree($categorylist);
		}

		if (isset($hastv) && $hastv >= 0)
		{
			$param['hastv'] = $hastv;
		}
		else
		{
			$hastv = -1;
		}
		if (isset($ctype) && $ctype >= 0)
		{
			$param['ctype'] = $ctype;
		}
		else
		{
			$ctype = -1;
		}
		$param['page_size'] = 10;
		$param['record_count'] = $this->model('school')->getSchoolCount($param);
		$param = page_and_size($param);//分页信息
		$classroomList = $this->model('school')->getSchoolList($param);
		$pagestr = show_page($param['record_count'], $param['page_size']);

		$this->assign('classroomstr',$classroomstr);
		$this->assign('categorylist', $categorylist);
		$this->assign('pagestr', $pagestr);
		$this->assign('q', $param['q']);
		$this->assign('catid', $catid);
		$this->assign('catname', $catname);
		$this->assign('hastv', $hastv);
		$this->assign('ctype', $ctype);
		$this->assign('classroomList', $classroomList);
		$this->display('school/school_list');
	}
	/**
	 * 添加网校
	 */
	public function add(){
		$classroom = $this->model('school');
		if($this->input->post()){
			$param = $this->input->post();
			$param['citycode'] = $this->input->post('address_qu')?$this->input->post('address_qu'):($this->input->post('address_shi')?$this->input->post('address_shi'):$this->input->post('address_sheng'));
			$this->check($param);
			$param['begindate'] = strtotime($param['begindate']);
			$param['enddate'] = strtotime($param['enddate']);
			$param['property'] = intval($param['property']);
			$param['status'] = 1;
			if(!empty($param['modulepower']))
				$param['modulepower'] = implode(',',$param['modulepower']);
			if(!empty($param['stumodulepower']))
				$param['stumodulepower'] = implode(',',$param['stumodulepower']);
			
			if(!empty($param['cface'])){
				$param['cface'] = $param['cface']['upfilepath'];
			}
			if(!empty($param['banner'])){
				$param['banner'] = $param['banner']['upfilepath'];
			}
			if(isset($param['floatadimg'])){
				$param['floatadimg'] = $param['floatadimg']['upfilepath'];
			}
			if(isset($param['profitratio'])){
				$param['profitratio'] = serialize($param['profitratio']);
			}
			unset($param['valuesubmit']);
			$res = $classroom->addclassroom($param);
			//设置教室和老师对应的表
			$ct = array(
				'crid'=>$res,
				'tid'=>$param['uid'],
				'status'=>1,
				'cdateline'=>time(),
				'role'=>2
				);
			$this->model('roomteacher')->insert($ct);
			$farr = array(
				'crid'=>$res,
				'uid'=>$param['uid'],
				'foldername'=>$param['crname'],
				'folderpath'=>'/0/',
				'folderlevel'=>1
				);
			$this->model('folder')->addfolder($farr);
			if($res>0){
				admin_log('网校管理','添加网校',$param['crname'],$res);//添加日志
				//更新SNS学校信息缓存
				EBh::app()->lib('Sns')->updateClassRooomCache(array('crid'=>$res,'domain'=>$param['domain'],'crname'=>$param['crname'],'cface'=>$param['cface']));
				close_dialog();

			}else{
				$this->goback('添加失败!');
			}
		}else{
			$Upcontrol = Ebh::app()->lib('UpcontrolLib');
			$this->assign('Upcontrol',$Upcontrol);
			$crid = $this->input->get('crid');
			$this->assign('crid',$crid);
			$tpowerlist = $classroom->getroompowerlist(712);
			$stpowerlist = $classroom->getroompowerlist(242);
			$sharelist = $classroom->getsharelist();
			$this->assign('tpowerlist',$tpowerlist);
			$this->assign('stpowerlist',$stpowerlist);
			$this->assign('sharelist',$sharelist);
			if($crid){
				$classroomdetail = $classroom->getclassroomdetail($crid);
				$this->assign('classroomdetail',$classroomdetail);
			}
			$this->assign('formhash',formhash('add'));
			$this->assign('token',createToken());
			$this->display('school/school_add');
		}
	}
		/**
	 *安检方法,对新增后台教室和编辑教师页面提交过来的数据进行了非常安全的判断,目前确保绝对安全
	 *@author zkq
	 *@date 2014-04-28
	 */
		public function check($param = array()){
			/*if(checkToken($param['token'])===false){
				$this->goback('请勿重复提交!');
			}*/
			if(!in_array($param['op'],array('add','edit'))){
				$this->goback('操作数被篡改!');
			}
			if($param['op']=='add'){
				$formhash_bt = 'add';
			}
			if($param['op']=='edit'){
				$formhash_bt = 'edit'.$param['_crname'].$param['_domain'].$param['crid'];
			}
			if(formhash($formhash_bt)!=$param['formhash']){
				$this->goback('参数被篡改!');
			}

			$message = array();
			$message['code'] = true;
		//网校老师权限参数合法性判断
			$tpowerlist = $this->model('school')->getroompowerlist(712);
			$tpowerlistCrids = array();
			foreach ($tpowerlist as $sv) {
				$tpowerlistCrids[] = $sv['catid'];
			}
			if(!empty($param['modulepower'])){
				$intersect = array_intersect($param['modulepower'],$tpowerlistCrids);
				$diff = array_diff($param['modulepower'],$intersect);
				if(!empty($diff)){
					$message[] = '网校老师权限参数被篡改!';
					$message['code'] = false;
				}
			}
		//共享平台参数合法性判断
			$sharelist = $this->model('school')->getsharelist();
			$sharelistCrids = array();
			foreach ($sharelist as $sv) {
				$sharelistCrids[] = $sv['crid'];
			}
			if(!empty($param['roompermission'])){
				$intersect = array_intersect($param['roompermission'],$sharelistCrids);
				$diff = array_diff($param['roompermission'],$intersect);
				if(!empty($diff)){
					$message[] = '共享平台参数被篡改!';
					$message['code'] = false;
				}
			}
		//网校学生权限参数合法性判断
			$stpowerlist = $this->model('school')->getroompowerlist(242);
			$stpowerlistCrids = array();
			foreach ($stpowerlist as $sv) {
				$stpowerlistCrids[] = $sv['catid'];
			}
			if(!empty($param['stumodulepower'])){
				$intersect = array_intersect($param['stumodulepower'],$stpowerlistCrids);
				$diff = array_diff($param['stumodulepower'],$intersect);
				if(!empty($diff)){
					$message[] = '网校学生权限参数被篡改!';
					$message['code'] = false;
				}
			}
			if(!in_array($param['isschool'],array(0,1,2,3,4,5,6,7))){
				$message[] = '平台类型被篡改!';
				$message['code'] = false;
			}
			if(empty($param['crname'])){
				$message[] = '网校名为空';
				$message['code'] = false;
			}
			if(empty($param['domain'])){
				$message[] = '域名为空';
				$message['code'] = false;
			}
			if($param['op']=='add'){
				$domain = $this->model('school')->exists_domain($param['domain']);
				if(!empty($domain)){
					$message[] = '域名已存在';
					$message['code'] = false;
				}
				$crname = $this->model('school')->exists_crname($param['crname']);
				if(!empty($crname)){
					$message[] = '网校名称已存在';
					$message['code'] =false;
				}
			}elseif($param['op']=='edit'){
				if($param['_domain']!=$param['domain']){
					$domain = $this->model('school')->exists_domain($param['domain']);
					if(!empty($domain)){
						$message[] = '域名已存在';
						$message['code'] = false;
					}
				}
				if($param['_crname']!=$param['crname']){
					$crname = $this->model('school')->exists_crname($param['crname']);
					if(!empty($crname)){
						$message[] = '网校名称已存在';
						$message['code'] =false;
					}
				}
			}
			if(!in_array($param['maxnum'],array(50,100,150))){
				$message[] = '最大人数被篡改!';
				$message['code'] = false; 
			}
			if($this->model('teacher')->isExists(intval($param['uid']))===false){
				// var_dump($param['uid']);die;
				$message[] = '教师不存在,可能是教师参数被非法篡改!';
				$message['code'] = false;
			}
			if(!empty($param['citycode']) && $this->model('cities')->isExits(intval($param['citycode']))===false){
				$message[] = '城市不存在,可能是城市参数被非法篡改!';
				$message['code'] = false;
			}
			if(preg_match("/^[0-9]+(\.[0-9]{2})?$/",$param['crprice'])==0){
				$message[] = '开通此电子网校所需金额非法';
				$message['code'] = false;
			}
			if(empty($param['profitratio'])){
				$message[] = '分层不能为空!';
				$message[] = false;
			}else{
				foreach($param['profitratio'] as $pv) {
					if(!is_numeric($pv)){
						$message[] = '分层出现非数字参数';
						$message['code'] = false;
						break;
					}
				}
			}

			if(array_sum($param['profitratio'])!=100){
				$message[] = '分层参数不正确!';
				$message['code'] = false;
			}
			if(!empty($param['ispublic'])&&$param['ispublic']!=1&&$param['ispublic']!=2){
				$message[] = '是否公开参数不正确!';
				$message['code'] = false;
			}
			if(!empty($param['isshare'])&&$param['isshare']!=1){
				$message[] = '是否共享平台参数不正确!';
				$message['code'] = false;
			}
			if(preg_match("/^[0-9]{1,}$/",$param['displayorder'])==0){
				$message[] = '排序参数不正确!';
				$message['code'] = false;
			}
			$tplPath = './views/shop/'.$param['template'];
		// if(!is_dir($tplPath)){
		// 	$message[] = '网校模板文件夹不存在或者参数被篡改!';
		// 	$message['code'] = false;
		// }客服系统不需判断模板

		//日期的验证
			if(empty($param['begindate'])||empty($param['enddate'])){
				$message[] = '网校开始时间或者网校结束时间为空';
				$message['code'] = false;
			}else{
				$begindate = strtotime($param['begindate']);
				$enddate = strtotime($param['enddate']);
				if($begindate === FALSE || $enddate === FALSE) {
					$message[] = '网校结束时间格式不对!';
					$message['code'] = false;
				} else if( $begindate > $enddate ) {
					$message[] = '网校开始时间不可以比结束时间晚!';
					$message['code'] = false;
				}
			}
			//金额冻结时间验证
            if (!isset($param['fund_freeze'])) {
                $message[] = '金额冻结时间不能为空!';
                $message['code'] = false;
            }
            if (isset($param['fund_freeze']) && !preg_match('/^\d+$/',$param['fund_freeze'])) {
                $message[] = '金额冻结时间格式不对!';
                $message['code'] = false;
            }

			if($message['code']===false){
				$this->goback(implode('<br />',$message));
			}

		}
		/*
	教室权限列表
	public function getroompowerlist($upid){
		$classroom = $this->model('school');
		$classroom->getroompowerlist($upid);
		////////////////////////////////////////////
	}
	*/
	public function getCrList(){
		$postwhere = (array)json_decode($this->input->post('where'));
		$key = md5(serialize($this->input->post()));
		if($this->cache->get($key)){
			echo $this->cache->get($key);exit;
		}
		if(is_array($postwhere)&&count($postwhere)>0){
			$where = $postwhere;
		}else{
			$where = array('status'=>1,'order'=>'cr.crid desc','limit'=>'0,1000');
		}
		$classroom = $this->model('school')->getroomlist($where);
		$selected = $this->input->post('checked');
		$html='';
		foreach ($classroom as $value) {
			if((int)$value['crid']==$selected){
				$html.= '<option value="'.$value['crid'].'"'.' selected=selected >'.$value['crname'].'</option>';
			}else{
				$html.= '<option value="'.$value['crid'].'">'.$value['crname'].'</option>';
			}
		}
		$this->cache->set($key,$html,60);
		echo $html;
	}
	/**
	 *教师列表（添加网校提交表单中）
	 * @return [type] [description]
	 */
	public function lite(){
		$this->display('school/teacher_lite');
	}
	/**
	 * 获取json格式的教师列表
	 * @return [type] [description]
	 */
	public function getListAjax(){
		$param = $this->input->post();
		$pageNumber = empty($param['pageNumber'])?1:intval($param['pageNumber']);
		$pageSize = empty($param['pageSize'])?20:intval($param['pageSize']);
		$offset = max(0,($pageNumber-1)*$pageSize);
		parse_str($param['query'],$queryArr);
		$queryArr['limit'] = $offset.','.$pageSize;
		$TModel = $this->model('teacher');
		$total = $TModel->getteachercount($queryArr);
		$TList = $TModel->getteacherlist($queryArr);
		array_unshift($TList,array('total'=>$total));
		echo json_encode($TList);
	}
	/**
	 * 返回
	 * @param  string $note      [返回信息]
	 * @param  string $returnurl [返回地址]
	 * @return [type]            [description]
	 */
	public function goback($note='操作成功,跳转中...',$returnurl='/school/index.html'){
		$this->widget('note_widget',array('note'=>$note,'returnurl'=>$returnurl));
		exit;
	}
	/**
	 * 网校信息修改
	 * @return [type] [description]
	 */
	public function edit(){
		$classroom = $this->model('school');
        $apiServer = Ebh::app()->getApiServer('ebh');
		if($this->input->post()){
			$param = $this->input->post();
			$param['citycode'] = $this->input->post('address_qu')?$this->input->post('address_qu'):($this->input->post('address_shi')?$this->input->post('address_shi'):$this->input->post('address_sheng'));
			$this->check($param);
			$param['begindate'] = strtotime($param['begindate']);
			$param['enddate'] = strtotime($param['enddate']);
			if(!empty($param['modulepower'])){
				$param['modulepower'] = implode(',',$param['modulepower']);
			}else{
				$param['modulepower'] = '';
			}
			if(!empty($param['stumodulepower'])){
				$param['stumodulepower'] = implode(',',$param['stumodulepower']);
			}else{
				$param['stumodulepower'] = '';
			}
			
			if(isset($param['cface'])){
				$param['cface'] = $param['cface']['upfilepath'];
			}
			if(isset($param['banner'])){
				$param['banner'] = $param['banner']['upfilepath'];
			}
			if(isset($param['floatadimg'])){
				$param['floatadimg'] = $param['floatadimg']['upfilepath'];
			}
			if(!empty($param['profitratio'])){
				$param['profitratio'] = serialize($param['profitratio']);
			}
			if(isset($param['property'])){
				$param['property'] = $param['property'];
			}
			if(empty($param['ispublic']))
				$param['ispublic'] = 0;
			if(empty($param['isshare']))
				$param['isshare'] = 0;
			if(empty($param['showusername']))
				$param['showusername'] = 0;

			if (!empty($param['totalpansize'])) {
		    	$panModle = $this->model('pan');
		    	$isFromAdmin = 1;
		    	$pan_infos = $panModle->getClassroomPaninfo($param['crid'],$isFromAdmin);
		    	$param['totalpansize'] = intval($param['totalpansize']);
		    	if ($pan_infos) {//存在则更新
		    		$param['defaultpansize'] = $pan_infos['defaultpansize'];
		    		$doUpdate = 1;
		    	} else {//第一次设置则，插入数据库
		    		$doUpdate = 0;
		    	}
		    	$res = $panModle->doPanGive($param,$doUpdate);
		    	if ($res == -1) {
		    		$this->goback('云盘配置有误!');
		    	}
			}

			if($classroom->editclassroom($param)!==false){
                $user = Ebh::app()->user->getloginuser();
			    //设置金额冻结时间
                $ret = $apiServer->reSetting()
                    ->setService('Settleset.FreeznTime.setFreeznTime')
                    ->addParams('day', $param['fund_freeze'])
                    ->addParams('crid', $param['crid'])
                    ->addParams('uid', $user['uid'])
                    ->request();
			//设置教室和老师对应的表
				$ct = array(
					'crid'=>$param['crid'],
					'tid'=>$param['uid'],
					'status'=>1,
					'cdateline'=>time(),
					'role'=>2
					);
			//更换管理员，先将原管理员role置1，再尝试将所选教师置2（已经在此教室的教师），失败则插入一条新数据（不在此教师的教师）
				$this->model('roomteacher')->update(array('crid'=>$param['crid'],'role'=>2,'changerole'=>1));
				$tempu = $this->model('roomteacher')->update(array('crid'=>$param['crid'],'tid'=>$param['uid'],'changerole'=>2));
				if(!$tempu)
					$this->model('roomteacher')->insert($ct);
				
				//修改共享平台分配
				if(isset($param['roompermission']))
				{
					$classroom->editroompermission($param['roompermission'],$param['crid']);
				}else{
					$classroom->editroompermission(array(),$param['crid']);
				}
				
				
				admin_log('网校管理','修改网校信息',$param['crname'],$param['crid'],'修改网校信息：网校名为'.$param['crname'].',网校id为'.$param['crid']);//添加日志
				//更新SNS学校信息缓存
				EBh::app()->lib('Sns')->updateClassRooomCache(array('crid'=>$param['crid'],'domain'=>$param['domain'],'crname'=>$param['crname'],'cface'=>$param['cface']));
				close_dialog();
			}else{
				$this->goback('修改失败!');
			}
		}else{
			$crid = $this->input->get('crid');
			if(!Ebh::app()->lib('Access')->checkClassroomAccess($crid)){//判断是否为非法操作
				show_message("您没有操作权限，请联系管理员！");
			}
			$Upcontrol = Ebh::app()->lib('UpcontrolLib');
			$this->assign('Upcontrol',$Upcontrol);
			$classroomdetail = $classroom->getclassroomdetail($crid);
            $classroomdetail['fund_freeze'] = $apiServer->reSetting()
                ->setService('Settleset.FreeznTime.getFreeznTime')
                ->addParams('crid', $crid)
                ->request();
            $classroomdetail['freeze_editable'] = $apiServer->reSetting()
                ->setService('Settleset.FreeznTime.checkEditable')
                ->addParams('crid', $crid)
                ->request();
			//计算云盘的使用详情
			$panModle = $this->model('pan');
			$isFromAdmin = 1;
		    $pan_infos = $panModle->getClassroomPaninfo($crid,$isFromAdmin);
		    if ($pan_infos) {//配置过了
		    	$classroomdetail['totalpansize'] = ceil($pan_infos['totalpansize']/1048576/1024);
		    	$classroomdetail['usepansize'] = ceil($pan_infos['usepansize']/1048576/1024);
		    	$classroomdetail['defaultpansize'] = ceil($pan_infos['defaultpansize']/1048576/1024);
		    } else {
		    	$classroomdetail['totalpansize'] = 1000;//给网校分配的总容量
		    	$usepansize = $panModle->getCridPanUsesize($crid);
		    	$classroomdetail['usepansize'] = ceil($usepansize['sum']/(1024*1024*1024));
		    	$classroomdetail['defaultpansize'] = 1024*1024*1024;//不存在说明配置为,默认1G
		    }

			$tpowerlist = $classroom->getroompowerlist(712);
			$stpowerlist = $classroom->getroompowerlist(242);
			$sharelist = $classroom->getsharelist();
			$permissionlist = $classroom->getroompermission($crid);
			$this->assign('permissionlist',$permissionlist);
			$this->assign('tpowerlist',$tpowerlist);
			$this->assign('stpowerlist',$stpowerlist);
			$this->assign('sharelist',$sharelist);
			$this->assign('c',$classroomdetail);
			$formhash_bt = 'edit'.$classroomdetail['crname'].$classroomdetail['domain'].$crid;
			$this->assign('formhash',formhash($formhash_bt));
			$this->assign('token',createToken());
			$this->display('school/school_edit');
		}
	}
	public function detail(){
		if(!$this->input->post()){
			exit;
		}
		$classroom = $this->model('school');
		$crid=$this->input->post("crid");
		$classroomdetail = $classroom->getclassroomdetail($crid);//获取当前网校信息
		$folder=$this->model('folder')->getFolderbyCrid($crid);//由当前网校crid获取开设课程
		$class=$this->model('classes')->getroomClassList($crid);//由当前网校crid获取开设班级
		$this->assign('folder',$folder);
		$this->assign('class',$class);
		$this->assign('c',$classroomdetail);
		$this->display('school/school_detail');
	}
	/**
	 * [课程选择教师]
	 * @return [type] [description]
	 */
	public function cchooseTeacher(){
		$teacherModel=$this->model('teacher');
		$folderid=$this->input->get('fid');//获取课程id
		$crid=$this->input->get('crid');//获取网校id
		$teacherList=$teacherModel->getroomteacherlist($crid, array('limit'=>1000));//全部教师
		$folderTeacherList=$teacherModel->getFolderTeacherList($folderid);//某门课程任课老师
		$folder=$this->model('folder')->getFolder($folderid);
		// var_dump($folder);
		// var_dump($foldTeacherList);
		// var_dump($teacherList);
		// var_dump(in_array($folderTeacherList[0],$teacherList));
		$this->assign('teacherList',$teacherList);
		$this->assign('folderTeacherList',$folderTeacherList);
		$this->assign('folder',$folder);
		// var_dump($folder);
		$this->display('school/school_cchoose');
	}
	/**
	 * [课程选择教师表单提交处理]
	 * 
	 */
	public function cchoosePost(){
		$folder = $this->model('folder');
		$choose=$this->input->post('choose')?$this->input->post('choose'):array();//教师选择
		$param['folderid'] = $this->input->post('folderid');//课程id
		$param['teacherids'] =$this->arrayToString($choose);
		$param['crid'] = $this->input->post('crid');//网校id
		$folder->chooseteacher($param);
		$grade = $this->input->post('grade');
		if(isset($grade)){
			$param['grade']=$grade;
			$folder->setGrade($param);
		}
		admin_log('网校管理','课程信息修改',$this->input->post('foldername'),$param['folderid']);//添加日志
		close_dialog();
		
	}
	//讲数组组拼接成字符串
	private function arrayToString(Array $arr){
		$str="";
		foreach($arr as $a){
			if($str!=""){
				$str.=','.$a;
			}else{
				$str.=$a;
			}
		}
		return $str;
	}
	/**
	 * 班级选择教师
	 * @return [type] [description]
	 */
	public function classChooseTeacher(){
		$teacherModel=$this->model('teacher');
		$classid=$this->input->get('classid');//获取课程id
		$crid=$this->input->get('crid');//获取网校id
		$teacherList=$teacherModel->getroomteacherlist($crid, array('limit'=>1000));//全部教师
		$classTeacherList=$teacherModel->getClassTeacherList($classid);//某个班级的教师

		$class=$this->model('classes')->getClass($classid);//班级信息
		// var_dump($class);
		 // var_dump($classTeacherList);die;
		// var_dump($teacherList);
		// var_dump(in_array($folderTeacherList[0],$teacherList));
		$this->assign('teacherList',$teacherList);
		$this->assign('classTeacherList',$classTeacherList);
		$this->assign('class',$class);
		// var_dump($class);
		$this->display('school/school_classchoose');		
	}
	/**
	 * [课程选择教师表单提交处理]
	 * 
	 */
	public  function classchoosePost(){
		$classes = $this->model('classes');
		$choose=$this->input->post('choose')?$this->input->post('choose'):array();//教师选择
		$param['classid'] = $this->input->post('classid');//班级id
		$param['teacherids'] =$this->arrayToString($choose);
		// var_dump($param);die;
		$classes->chooseteacher($param);
		$grade = $this->input->post('grade');
		if(isset($grade)){
			$param['grade']=$grade;
			$classes->setGrade($param);
		}
		admin_log('网校管理','班级信息修改',$this->input->post('classname'),$param['classid']);//添加日志
		close_dialog();
	}
	public function roomselect(){
		$isschool = intval($this->input->get('isschool'));
		$this->assign('isschool',$isschool);
		$this->display('data/classroom_select');
	}
		/*
	判断网校名是否存在 ajax
	*/
	public function exists_crname(){
		$classroom = $this->model('school');
		$crname = $this->input->get('crname');
		if($classroom->exists_crname($crname))
			echo 1;
		else
			echo 0;
	}
	/*
		判断域名是否存在 ajax
	*/
		public function exists_domain(){
			$classroom = $this->model('school');
			$domain = $this->input->get('domain');
			if($classroom->exists_domain($domain))
				echo 1;
			else
				echo 0;
		}
		/**
	 * 学校ID字符串转换成学校名称数组
	 * @param string accessIds
	 * @param array 返回键名为学校ID，键值为学校名称
	 */
	public function formatToClassroomStr($classroomIds)
	{
		$classroom_str = '';
		if ($classroomIds == 'ALL')
		{
			$classroom_str = '所有学校';
		}
		elseif (!empty($classroomIds))
		{
			$classroom_list = $this->model('classroom')->getClassroomListByIds($classroomIds);
			$classroom_list = array_reduce($classroom_list, create_function('$v,$w', '$v[$w["crid"]]=$w["crname"];return $v;'));
			if (is_array($classroom_list))
			{
				$classroom_str = implode('/', $classroom_list);
			}
		}
		
		return $classroom_str;
	}

	/**
	 * 获得该分类和所有子分类字符串
	 * @param  array   $arr  分类
	 * @param  integer $upid 父类id
	 * @return string        该分类和所有子分类字符串
	 */
	public function getCatids($arr = array(), $upid = 0)
	{
		$catid_array = array($upid);
		$catid_array =array_merge($catid_array, getChildCat($arr, $upid));
		return implode (',', $catid_array);
	}
}